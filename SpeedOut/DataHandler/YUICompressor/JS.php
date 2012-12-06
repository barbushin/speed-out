<?php

/**
 * JavaScript compression data handler based on SpeedOut_DataHandler_YUICompressor_Adapter
 *
 * @see https://github.com/barbushin/speed-out
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 */
class SpeedOut_DataHandler_YUICompressor_JS extends SpeedOut_DataHandler_YUICompressor {

	protected $obfuscate;
	protected $preserveSemi;
	protected $optimize;

	/**
	 * @param null|SpeedOut_DataHandler_YUICompressor_Adapter $adapter
	 * @param bool $obfuscate Obfuscate
	 * @param bool $preserveSemi Preserve all semicolons
	 * @param bool $optimize Disable all micro optimizations
	 * @param string $charset Input data charset
	 * @param int $lineBreak Insert a line break after the specified column number
	 */
	public function __construct(SpeedOut_DataHandler_YUICompressor_Adapter $adapter = null, $obfuscate = false, $preserveSemi = false, $optimize = false, $charset = 'utf-8', $lineBreak = 5000) {
		$this->obfuscate = $obfuscate;
		$this->preserveSemi = $preserveSemi;
		$this->optimize = $optimize;
		parent::__construct($adapter, $charset, $lineBreak);
	}

	public function handleData(&$data) {
		$data = $this->adapter->minifyJs($data, !$this->obfuscate, $this->preserveSemi, !$this->optimize, $this->charset, $this->lineBreak);
	}
}
