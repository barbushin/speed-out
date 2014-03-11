# Features

 * Automatically handle output HTML(see [OutputHandler.php](https://github.com/barbushin/speed-out/blob/master/SpeedOut/OutputHandler.php))
 * Minimize output HTML using Minify_HTML(thanks to Stephen Clay)
 * Parse output HTML and combine JavaScript/CSS files content
   * Support external files downloading
   * Include/exclude links from combining by custom [SpeedOut_Filter](https://github.com/barbushin/speed-out/blob/master/SpeedOut/Filter.php) based on regexp
   * Merge CSS with handling @import operator and URI paths converting
   * Insert combined file link to custom `<!-- placeholder -->`
   * Cache combined files by links hash
 * Minimize JavaScript & CSS using [YUI Compressor](http://developer.yahoo.com/yui/compressor/)(requires [JVM](http://en.wikipedia.org/wiki/Java_virtual_machine))
 * Minimize JavaScript using [Closure Compiler Service](http://closure-compiler.appspot.com Google)
 * Auto-compile LESS files to CSS (thanks to Leaf Corcoran for [lessphp](http://leafo.net/lessphp))
 * Optional activation controller(see [SpeedOut_Activator](https://github.com/barbushin/speed-out/blob/master/SpeedOut/Activator.php))

# Example
## Output HTML before SpeedOut

	<html>
	<head>
		<!--COMMON_CSS_PLACEHOLDER-->
		<link rel="stylesheet" href="css1.css" media="screen" />
		<link rel="stylesheet" href="css2.css" media="screen" />
		<link rel="stylesheet" href="css3.css" media="screen" />
		<link rel="stylesheet" href="css4.css" media="screen" />

		<!--COMMON_JS_PLACEHOLDER-->
		<script src="js1.js" type='text/javascript'></script>
		<script src="js2.js" type='text/javascript'></script>
		<script src="js3.js" type='text/javascript'></script>
		<script src="js4.js" type='text/javascript'></script>
	</head>
	<body>
	</body>
	</html>

## Output HTML after SpeedOut

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
	<head>
		<!--COMMON_CSS_PLACEHOLDER-->
		<link rel="stylesheet" href="http://open/speed-out/example/cache/121114_f6a5b24149175f8f73f6259e2491ba3c.css" />
		
		<!--COMMON_JS_PLACEHOLDER-->
		<script type="text/javascript" src="http://open/speed-out/example/cache/121114_7f20b2e5234a124444a901c74ee0d267.js"></script>
		
	</head>
	<body>
	</body>
	</html>
 
# Recommended
  * Google Chrome extension <a href="http://goo.gl/b10YF">PHP Console</a>.
  * Google Chrome extension <a href="http://goo.gl/kNix9">JavaScript Errors Notifier</a>.