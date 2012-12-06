<?php

class Test_SpeedOut_DataHandler_YUICompressor_JS extends Test_SpeedOut_DataHandler_YUICompressor {

	protected function initDataHandler() {
		return new SpeedOut_DataHandler_YUICompressor_JS($this->initAdapter());
	}

	public function testMinimize() {
		$originalData = file_get_contents($this->getTestsHtmlDir() . '/base1.js');
		$minimizedData = $this->getHandledData($originalData);
		$this->assertNotEmpty($minimizedData);
		$this->assertLessThan(strlen($originalData), strlen($minimizedData));
	}
}
