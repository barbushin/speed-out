<?php

/**
 * @see https://github.com/barbushin/speed-out
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 */
class SpeedOut_DataHandler_YUICompressor_Adapter {

	/**
	 * File path of the YUI Compressor jar file
	 *
	 * @var string
	 */
	protected $jarFile;

	/**
	 * Writable temp directory
	 *
	 * @var string
	 */
	protected $tempDir;

	/**
	 * File path of "java" executable (may be needed if not in shell's PATH)
	 *
	 * @var string
	 */
	protected $javaExecutable;

	protected static $defaultOptions = array(
		'charset' => 'utf-8',
		'line-break' => 5000,
	);

	protected static $booleanOptions = array(
		'nomunge',
		'preserve-semi',
		'disable-optimizations',
	);

	public function __construct($tmpDir = null, $javaExecutable = 'java', $jarFile = null) {
		$this->tempDir = realpath($tmpDir ? $tmpDir : sys_get_temp_dir());
		$this->javaExecutable = $javaExecutable;
		$this->jarFile = $jarFile ? $jarFile : dirname(__FILE__) . '/yuicompressor.jar';

		if(!is_file($this->jarFile)) {
			throw new SpeedOut_Exception('$jarFile(' . $this->jarFile . ') is not a valid file.');
		}
		if(!is_dir($this->tempDir)) {
			throw new SpeedOut_Exception('$tempDir(' . $this->tempDir . ') is not a valid direcotry.');
		}
		if(!is_writable($this->tempDir)) {
			throw new SpeedOut_Exception('$tempDir(' . $this->tempDir . ') is not writable.');
		}
	}

	/**
	 * Minify a Javascript string
	 *
	 * @param string $js
	 * @param bool $noMunge
	 * @param bool $preserveSemi
	 * @param bool $disableOptimizations
	 * @param string $charset
	 * @param int $lineBreak
	 * @internal param array $options (verbose is ignored)
	 * @see http://www.julienlecomte.net/yuicompressor/README
	 * @return string
	 */
	public function minifyJs($js, $noMunge = true, $preserveSemi = true, $disableOptimizations = true, $charset = 'utf-8', $lineBreak = 5000) {
		$options = array(
			'charset' => $charset,
			'line-break' => $lineBreak,
			'nomunge' => $noMunge,
			'preserve-semi' => $preserveSemi,
			'disable-optimizations' => $disableOptimizations,
		);
		$js = $this->deprecateJavaScriptConditionalCompilationCode($js);
		return $this->minify('js', $js, $options);
	}

	protected function deprecateJavaScriptConditionalCompilationCode($js) {
		return SpeedOut_Utils::safePregReplace('~/\*\s*?(@.*?\*/)~s', '/* DEPRECATED: \1', $js);
	}

	/**
	 * Minify a CSS string
	 *
	 * @param string $css
	 * @param string $charset
	 * @param int $lineBreak
	 * @see http://www.julienlecomte.net/yuicompressor/README
	 * @return string
	 */
	public function minifyCss($css, $charset = 'utf-8', $lineBreak = 5000) {
		$options = array(
			'charset' => $charset,
			'line-break' => $lineBreak,
		);
		return $this->minify('css', $css, $options);
	}

	protected function minify($type, $data, array $options = array()) {
		$tmpFile = tempnam($this->tempDir, 'yuic_');
		if(!$tmpFile) {
			throw new SpeedOut_Exception('Could not create temp file in "' . $this->tempDir . '"');
		}
		$data = str_replace('/*!', '/*', $data); // fixes YUICompressor bug with compressing /*! ... */ strings
		file_put_contents($tmpFile, $data);
		$command = $this->getMinifyCommand($type, $tmpFile, $options);
		exec($command . ' 2>&1', $output, $resultCode);
		$output = implode("\n", $output);
		if($resultCode != 0) {
			throw new SpeedOut_Exception('YUICompressor command "' . $command . '" failed with result: ' . $output);
		}
		unlink($tmpFile);
		return $output;
	}

	private function getMinifyCommand($type, $tmpFile, array $userOptions = array()) {
		$options = array_merge(self::$defaultOptions, $userOptions, array('type' => $type));
		$cmd = $this->javaExecutable . ' -jar ' . escapeshellarg($this->jarFile);
		foreach($options as $option => $value) {
			if(in_array($option, self::$booleanOptions)) {
				if($value) {
					$cmd .= ' --' . $option;
				}
			}
			else {
				$cmd .= ' --' . $option . ' ' . escapeshellarg($value);
			}
		}
		$cmd .= ' ' . escapeshellarg($tmpFile);
		return $cmd;
	}
}

