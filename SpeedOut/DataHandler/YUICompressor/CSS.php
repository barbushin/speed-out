<?php

/**
 * CSS compression data handler based on SpeedOut_DataHandler_YUICompressor_Adapter
 *
 * @see https://github.com/barbushin/speed-out
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 */
class SpeedOut_DataHandler_YUICompressor_CSS extends SpeedOut_DataHandler_YUICompressor {

	public function handleData(&$data) {
		$data = $this->adapter->minifyCss($data, $this->charset, $this->lineBreak);
	}
}
