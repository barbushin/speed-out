<?php

abstract class Test_SpeedOut_DataHandler_YUICompressor extends Test_SpeedOut_DataHandler {

	protected function initAdapter() {
		return new SpeedOut_DataHandler_YUICompressor_Adapter($this->getTestsTmpDir());
	}
}
