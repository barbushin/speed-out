<?php

class Test_SpeedOut_DataHandler_Callback extends Test_SpeedOut_DataHandler {

	protected function initDataHandler() {
		return new SpeedOut_DataHandler_Callback(create_function('$data', 'return $data + 1;'));
	}

	public function testHandleDataCallbackCalling() {
		$actualData = mt_rand();
		$expectedData = $actualData + 1;
		$this->assertEquals($expectedData, $this->getHandledData($actualData));
	}
}
