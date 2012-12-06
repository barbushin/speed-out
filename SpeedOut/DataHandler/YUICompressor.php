<?php

/**
 * Compress static data using SpeedOut_DataHandler_YUICompressor_Adapter
 *
 * @see http://code.google.com/p/speed-out
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 */
abstract class SpeedOut_DataHandler_YUICompressor extends SpeedOut_DataHandler {

	/** @var  SpeedOut_DataHandler_YUICompressor_Adapter */
	protected $adapter;
	protected $charset;
	protected $lineBreak;

	/**
	 * @param null|SpeedOut_DataHandler_YUICompressor_Adapter $adapter
	 * @param string $charset Input data charset
	 * @param int $lineBreak Insert a line break after the specified column number
	 */
	public function __construct(SpeedOut_DataHandler_YUICompressor_Adapter $adapter = null, $charset = 'utf-8', $lineBreak = 5000) {
		$this->charset = $charset;
		$this->lineBreak = $lineBreak;
		$this->adapter = $adapter ? $adapter : new SpeedOut_DataHandler_YUICompressor_Adapter();
	}
}
