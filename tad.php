<?php
/*
@package framework
@author Shane Thacker <shane@steadymade.com>
@version 0.1
@license MIT License
@copyright Shane Thacker, 2010
@todo keep cleaning up, security, unit tests...
*/

if (!defined('DEBUG'))				define('DEBUG', false);
if (!defined('BASE_PATH'))			define('BASE_PATH', realpath(dirname(__FILE__).'/..'));
if (!defined('VIEW_PATH'))			define('VIEW_PATH', '/views/');
if (!defined('CONTROLLER_PATH'))	define('CONTROLLER_PATH', '/controllers/');
if (!defined('LAYOUT_PATH'))		define('LAYOUT_PATH', '/layouts/');


class Tad {
	private static
		$instance;

	protected
		$path,
		$request,
		$controller = 'main',
		$action = 'index',
		$args = array(),
		$routes = array();

	public static function getInstance(){
		if (!self::$instance) self::$instance = new Tad();
		set_error_handler(array(self::$instance, 'handleError'), E_ALL | E_STRICT);
		return self::$instance;
	}

	public function start(){
		try {
			$this->dispatch();
		} catch (Exception $e) {
			DEBUG ? include_once('error.php') : $this->pathNotFound();
		}
	}

	public function handleError($number, $message, $file, $line, $context){
		if (0 === error_reporting()) return false;
		throw new ErrorException($message, 0, $number, $file, $line);
	}

	public function addRoute($rule, $controller, $action, $http_method = 'GET'){
		$this->routes[] = array('/^' . str_replace('/', '\/', $rule) . '$/', $controller, $action, $http_method);
	}

	public function setController(){
		$this->controller = $this->args[0];
		unset($this->args[0]);
	}

	public function setAction(){
		$this->action = $this->args[1];
		unset($this->args[1]);
	}

	public function getMethod($action){
		$action = preg_replace(array('/_(.?)/e', '/-(.?)/e', '/ (.?)/e'), 'strtoupper("$1")', $action);
		if ($action == '') return 'index';
		else if (function_exists($action) || $action == 'list' || $action == 'new' || $action == 'sort') return '_'.$action;
		else return $action;
	}

	private function getArgs($matches){
		$new_matches = array();
		foreach ($matches as $k => $match) if ($match!=$matches[0] && is_string($k)) $new_matches[$k] = $match;
		return $new_matches;
	}

	public function pathNotFound(){
		$path = $this->path;
		echo include BASE_PATH.LAYOUT_PATH.'404.php';
	}

	public function isAjax(){
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}

	public function dispatch(){
		$this->path = $path = (isset($_GET['url'])) ? trim($_GET['url'], '/') : '';
		$this->request = $_SERVER['REQUEST_METHOD'];
		$found = false;
		
		if (count($this->routes) > 0) {
			foreach ($this->routes as $rule => $part) {
				if (preg_match($part[0], $path, $matches) && $this->request == $part[3]) {
					$this->controller = $part[1];
					$this->action = $part[2];
					$this->args = $this->getArgs($matches);
					$found = true;
					break;
				}
			}
		}
		
		if (!$found && !empty($path)) {
			$this->args = explode('/', $path);
			if (!empty($this->args[0])) $this->setController();
			if (!empty($this->args[1])) $this->setAction();
		}

		$method = $this->getMethod($this->action);
		include_once(BASE_PATH.CONTROLLER_PATH."$this->controller.php");
		$controller = new $this->controller(VIEW_PATH."$this->controller/$this->action.php", LAYOUT_PATH.'layout.php');
		if (method_exists($controller, $method)) $result = call_user_func_array(array($controller, $method), $this->args);
		$files = array('view' => $controller->view);
		if (!$this->isAjax() && $controller->layout !== false) $files['layout'] = $controller->layout;
		echo ($controller->text_only || $this->request == 'POST' && isset($result)) ? $result : $controller->render($files);
	}
}


class View {
	private
		$files = array(),
		$vars = array();

	public function __construct($files, $vars){
		$this->files = $files;
		$this->vars = $vars;
	}

	public function parseParts($fileContent){
		preg_match_all('/{part:(.*?)}(.*?){\/part}/s', $fileContent, $matches);
		foreach ($matches[1] as $p => $part) {
			$setVar = $part;
			$$setVar = $matches[2][$p];
		}
	}

	public function render(){
		extract(get_object_vars($this->vars), EXTR_SKIP);
		foreach ($this->files as $file => $value) {
			ob_start();
			include BASE_PATH.$value;
			$fileContent = ob_get_clean();
			if (!next($this->files)) return $fileContent;
			else if ($this->vars->has_parts) $this->parseParts($fileContent);
			else $$file = $fileContent;
		}
	}
}


class Controller {
	public 
		$view,
		$layout,
		$text_only = false,
		$has_parts = false;

	public function __construct($view=null, $layout=null){
		$this->view = $view;
		$this->layout = $layout;
	}

	public function render($file = null){
		$files = (is_array($file)) ? $file : array('view' => $file);
		$viewClass = new View($files, $this);
		return $viewClass->render();
	}

	public function redirect($url){
		header("Location: {$url}");
		exit();
	}
}