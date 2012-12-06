<?php

require_once(dirname(__FILE__) . '/../SpeedOut/__autoload.php');

$speedOutCacheDir = 'cache';

$cssCombiner = new SpeedOut_DataHandler_Combiner_CSS(new SpeedOut_CacheStorage_Directory($speedOutCacheDir, null, '.css'), '<!--COMMON_CSS_PLACEHOLDER-->', false);
$cssCombiner->setDebugMode(true);
$cssCombiner->addCombinedDataHandler(new SpeedOut_DataHandler_YUICompressor_CSS());

$jsCombiner = new SpeedOut_DataHandler_Combiner_JS(new SpeedOut_CacheStorage_Directory($speedOutCacheDir, null, '.js'), '<!--COMMON_JS_PLACEHOLDER-->', false);
$jsCombiner->setDebugMode(true);
$jsCombiner->addCombinedDataHandler(new SpeedOut_DataHandler_YUICompressor_JS());

$speedOut = SpeedOut_OutputHandler::getInstance();
$speedOut->addHandler($cssCombiner);
$speedOut->addHandler($jsCombiner);
$speedOut->start();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
<head>
	<!--COMMON_CSS_PLACEHOLDER-->
	<link rel="stylesheet" href="css1.css" media="screen" />
	<link rel="stylesheet" href="css2.css" media="screen" />

	<!--COMMON_JS_PLACEHOLDER-->
	<script src="js1.js" type='text/javascript'></script>
	<script src="js2.js" type='text/javascript'></script>
</head>
<body>
</body>
</html>
<?

$handledOutput = $speedOut->getHandledOutput();

var_dump($handledOutput);
