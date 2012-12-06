<?php

/**
 * Optimize JavaScript code by Google Closure service http://closure-compiler.appspot.com
 * @see http://code.google.com/p/speed-out
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 */
class SpeedOut_DataHandler_GoogleClosure_JsCompressor extends SpeedOut_DataHandler {

	const CLOSURE_SERVICE_URL = 'http://closure-compiler.appspot.com/compile';
	const SERVICE_REQUEST_TIMEOUT = 60;

	const OPTIMIZE_SIMPLE = 'SIMPLE_OPTIMIZATIONS';
	const OPTIMIZE_WHITESPACE = 'WHITESPACE_ONLY';
	const OPTIMIZE_ADVANCED = 'ADVANCED_OPTIMIZATIONS';

	protected $compileSettings = array(
		'compilation_level' => 'ADVANCED_OPTIMIZATIONS',
		'output_format' => 'text',
		'output_info' => 'compiled_code',
	);
	protected $useLocalCompiler = false;

	public function __construct($optimizationLevel = self::OPTIMIZE_SIMPLE, $externalJsUrl = null) {
		if($externalJsUrl) {
			$this->compileSettings['externs_url'] = $externalJsUrl;
		}
		if(!in_array($optimizationLevel, array(static::OPTIMIZE_SIMPLE, static::OPTIMIZE_WHITESPACE, static::OPTIMIZE_ADVANCED))) {
			throw new Exception('Unknown $optimizationLevel type');
		}
		$this->compileSettings['compilation_level'] = $optimizationLevel;
	}

	public function handleData(&$data) {
		if($this->useLocalCompiler) {
			$data = $this->optimizeByLocalService($data);
		}
		else {
			$data = $this->optimizeByGoogleService($data);
		}
	}

	protected function optimizeByGoogleService($js) {
		$request = $this->compileSettings;
		$request['js_code'] = $js;
		$response = trim($this->sendPostRequest(static::CLOSURE_SERVICE_URL, $request));
		if(preg_match('!^Error!i', $response)) {
			throw new Exception('Minimizing failed with response: ' . $response);
		}
		return $response;
	}

	protected function optimizeByLocalService($js) {
		throw new Exception('Not implemented yet');
	}

	protected function sendPostRequest($url, array $data) {
		$postRawData = http_build_query($data);
		$response = @file_get_contents($url, false, stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => "Connection: close\r\nContent-Length: " . strlen($postRawData) . "\r\nContent-type: application/x-www-form-urlencoded\r\n",
				'content' => $postRawData,
				'timeout' => static::SERVICE_REQUEST_TIMEOUT,
			))));
		if($response === false) {
			throw new Exception('Request to "' . $url . '" failed');
		}
		return $response;
	}
}
