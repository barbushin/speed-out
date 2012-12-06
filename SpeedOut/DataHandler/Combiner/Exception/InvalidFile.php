<?php

/**
 * @see https://github.com/barbushin/speed-out
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 */
class SpeedOut_DataHandler_Combiner_Exception_InvalidFile extends SpeedOut_Exception {

	protected $filePath;

	public function __construct($filePath, $message = null) {
		$this->filePath = $filePath;
		parent::__construct($message);
	}

	public function getFilePath() {
		return $this->filePath;
	}
}


