<?php

abstract class Test_SpeedOut_DataHandler extends Test_SpeedOut_Abstract {

	/** @var SpeedOut_DataHandler */
	protected $dataHandler;

	/**
	 * @abstract
	 * @return SpeedOut_DataHandler
	 */
	abstract protected function initDataHandler();

	protected function setUp() {
		parent::setUp();
		$this->dataHandler = $this->initDataHandler();
	}

	protected function tearDown() {
		parent::tearDown();
	}

	protected function getHandledData($actualData, SpeedOut_DataHandler $dataHandler = null) {
		if(!$dataHandler) {
			$dataHandler = $this->dataHandler;
		}
		$dataHandler->handleData($actualData);
		return $actualData;
	}
}
