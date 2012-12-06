<?php

/**
 * CSS compression data handler based on SpeedOut_DataHandler_YUICompressor_Adapter
 *
 * @see http://code.google.com/p/speed-out
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 */
class SpeedOut_DataHandler_YUICompressor_CSS extends SpeedOut_DataHandler_YUICompressor {

	public function handleData(&$data) {
		$data = $this->adapter->minifyCss($data, $this->charset, $this->lineBreak);
	}
}
