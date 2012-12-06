<?php

/**
 * @see http://code.google.com/p/speed-out
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 */
class SpeedOut_DataHandler_Combiner_Exception_PathNotFound extends SpeedOut_Exception {

	protected $path;
	protected $source;

	public function __construct($path, $source) {
		$this->path = $path;
		$this->source = $source;
		parent::__construct('Path "' . $path . '" not found in ' . $source);
	}

	public function getPath() {
		return $this->path;
	}

	public function getSource() {
		return $this->source;
	}
}
