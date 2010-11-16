<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<title>Error!</title>
	<style>
body		{
			font: 11px/1.5 "Lucida Grande", Arial, Geneva, sans-serif;
			color: #676767;
			text-align: left;
			padding: 0;
			background: #fff;
			margin: 30px;
			}

h1, h2, h3, h4, h5, h6 {
			font-weight: normal;
			color: #003366;
			line-height: 1.2;
			margin-bottom: 9px;
			font-family: Helvetica, Arial, Geneva, sans-serif;
			}

h1 			{
			font-size: 28px;
			color: #ff5533;
			}

h2 			{
			font-size: 19px;
			}

h3 			{
			font-size: 14px;
			color: #006080;
			}

h4 			{ font-size: 16px; }
h5 			{ margin-bottom: 9px; }

ol			{ list-style: decimal; }
ul 			{ list-style: circle; }
li			{ margin-left: 0px }

a, a:hover	{ color: #0099cc; text-decoration: none; border-bottom: 1px solid #0099cc; }
a:focus		{ outline: 1px dotted invert; }

strong		{ font-weight: bold; }
em			{ font-style: normal; }
sup 		{ font-size: 10px; vertical-align: top; }

.quiet		{ color: #ddd; }
del 		{  }

p, dl, hr, ol, ul, pre, table, address, fieldset { margin-bottom: 18px; }

hr 			{
			border-color: #eeebea;
			border-style: solid;
			border-width: 1px 0 0;
			clear: both;
			height: 0;
			margin-bottom: 36px;
			margin-top: 36px;
			}

pre 		{
			font-family: monospace;
			font-family: "andale mono";
			padding: 10px;
			background: #000;
			-webkit-border-radius: 5px;
			color: #fff;
			overflow: auto;
			margin: 0;
			background: #e3dddc;
			background: #eeebea;
			color: #111;
			}
	</style>
</head>

<body>
	<h1>Exception: <?= $e->getMessage(); ?></h1>

	<h2>File</h2>
	<pre><?= $e->getFile()?></pre>

	<h2>Line</h2>
	<pre><? $line = $e->getTrace(); echo $line[0]['line']; ?></pre>

			

	<h2>Trace</h2>
	<pre><? print_r ($e->getTraceAsString()); ?></pre>

	<h2>Exception Object</h2>
	<pre><? print_r ($e); ?></pre>

	<h2>Var Dump</h2>
	<pre><? debug_print_backtrace(); ?></pre>
</body>
</html>