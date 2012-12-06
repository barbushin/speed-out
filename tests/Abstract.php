<?php

abstract class Test_SpeedOut_Abstract extends PHPUnit_Framework_TestCase {

	protected function getTestsHtmlDir() {
		return $this->getTestsDataDir() . '/html';
	}

	protected function getTestsDataDir() {
		return dirname(__FILE__) . '/_data';
	}

	protected function getTestsTmpDir() {
		return $this->getTestsDataDir() . '/tmp';
	}

	protected function clearTestsTmpDir() {
		$dir = $this->getTestsTmpDir();
		foreach(scandir($dir) as $name) {
			$path = $dir . '/' . $name;
			if(is_file($path)) {
				unlink($path);
			}
		}
	}
}
