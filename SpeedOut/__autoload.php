<?php

define('SPEED_OUT_BASE_DIR', dirname(__FILE__));

function speedOutAutoload($class) {
	if(strpos($class, 'SpeedOut_') === 0) {
		require_once(SPEED_OUT_BASE_DIR . '/' . substr(str_replace('_', '/', $class), strlen('SpeedOut_')) . '.php');
	}
}

spl_autoload_register('speedOutAutoload');
