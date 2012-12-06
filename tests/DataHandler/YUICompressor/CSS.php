<?php

class Test_SpeedOut_DataHandler_YUICompressor_CSS extends Test_SpeedOut_DataHandler_YUICompressor {

	protected function initDataHandler() {
		return new SpeedOut_DataHandler_YUICompressor_CSS($this->initAdapter());
	}

	public function testMinimize() {
		$originalData = file_get_contents($this->getTestsHtmlDir() . '/base1.css');
		$minimizedData = $this->getHandledData($originalData);
		$this->assertNotEmpty($minimizedData);
		$this->assertLessThan(strlen($originalData), strlen($minimizedData));
	}
}
