<?php

class Test_SpeedOut_OutputHandler extends Test_SpeedOut_Abstract {

	/** @var SpeedOut_OutputHandler */
	protected $outputHandler;

	public function setUp() {
		$this->outputHandler = clone SpeedOut_OutputHandler::getInstance(); // why not?)
	}

	/**
	 * @expectedException SpeedOut_Exception
	 */
	public function testGetHandledOutputWithoutStartExceptions() {
		$this->outputHandler->getHandledOutput();
	}

	/**
	 * @expectedException SpeedOut_Exception
	 */
	public function testStartTwiceException() {
		$this->outputHandler->start();
		$this->outputHandler->start();
	}

	public function testOutputDataIsHandled() {
		$expectedData = md5(mt_rand());
		$this->outputHandler->start();
		echo $expectedData;
		$this->assertEquals($expectedData, $this->outputHandler->getHandledOutput());
	}

	public function testDataHandlersCalling() {
		$data = mt_rand();
		$expectedData = $data;
		for($i = 0; $i < 3; $i++) {
			$expectedData++;
			$this->outputHandler->addHandler(new SpeedOut_DataHandler_Callback(create_function('$data', 'return $data + 1;')));
		}
		$this->outputHandler->start();
		echo $data;
		$this->assertEquals($expectedData, $this->outputHandler->getHandledOutput());
	}

	public function testDataHandlingInAutoFlushMode() {
		$expectedData = mt_rand();
		$this->outputHandler->addHandler(new SpeedOut_DataHandler_Callback(create_function('$data, $string', 'return $string;'), array($expectedData)));
		$this->outputHandler->start(null, true);
		ob_start();
		echo $expectedData;
		unset($this->outputHandler);
		$actualOutput = ob_get_contents();
		ob_end_clean();
		$this->assertEquals($expectedData, $actualOutput);
	}

	public function testDestructionExceptionsHandler() {
		$this->outputHandler->addHandler(new SpeedOut_DataHandler_Callback(create_function('$data', 'throw new Exception("expectedMessage");')));
		$this->outputHandler->start(array($this, 'handleDestructionException'), true);
		unset($this->outputHandler);
	}

	public function handleDestructionException(Exception $exception) {
		$this->assertInstanceOf('Exception', $exception);
		$this->assertEquals('expectedMessage', $exception->getMessage());
	}
}

