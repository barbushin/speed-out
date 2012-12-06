<?php

/**
 * Handle data by some callback
 *
 * @see https://github.com/barbushin/speed-out
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 */
class SpeedOut_DataHandler_Callback extends SpeedOut_DataHandler {

	protected $callback;
	protected $arguments;

	public function __construct($callback, array $arguments = array()) {
		if(!is_callable($callback)) {
			throw new SpeedOut_Exception('Argument $callback is not callable');
		}
		$this->callback = $callback;
		$this->arguments = $arguments;
	}

	public function handleData(&$data) {
		$data = call_user_func_array($this->callback, array_merge(array($data), $this->arguments));
	}
}
