<?php

/**
 * Output handler based on PHP output buffering(ob_start)
 *
 * @see http://code.google.com/p/speed-out
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 */
class SpeedOut_OutputHandler {

	// TODO: Add ETag headers handling

	/** @var SpeedOut_OutputHandler */
	protected static $instance;

	/** @var SpeedOut_DataHandler[] */
	protected $handlers = array();
	/** @var callback */
	protected $destructionExceptionHandler;
	protected $isStarted;
	/** @var SpeedOut_Filter|null */
	protected $autoFlushContentTypeFilter;
	protected $autoFlushHandledData = false;

	protected function __construct() {
	}

	/**
	 * @static
	 * @return SpeedOut_OutputHandler
	 */
	public static function getInstance() {
		if(!self::$instance) {
			self::$instance = new SpeedOut_OutputHandler();
		}
		return self::$instance;
	}

	/**
	 * Add handler that will proceed some changes on output data
	 * @param SpeedOut_DataHandler $handler
	 */
	public function addHandler(SpeedOut_DataHandler $handler) {
		$this->handlers[] = $handler;
	}

	/**
	 * @important Be careful using $autoFlushHandledData = true, because exceptions handling does not work in __destruct()
	 * @param callback $destructionExceptionsHandler If $autoFlushHandledData == true, so there should be some exceptions handler
	 * @param bool $autoFlushHandledData Flush handled data in output when SpeedOut_OutputHandler object __destruct()
	 * @param null|SpeedOut_Filter $autoFlushContentTypeFilter
	 * @throws SpeedOut_Exception
	 */
	public function start($destructionExceptionsHandler = null, $autoFlushHandledData = false, SpeedOut_Filter $autoFlushContentTypeFilter = null) {
		if($this->isStarted) {
			throw new SpeedOut_Exception('Already started');
		}
		if($destructionExceptionsHandler && !is_callable($destructionExceptionsHandler)) {
			throw new SpeedOut_Exception('Argument $destructionExceptionsHandler is not callable');
		}
		$this->isStarted = true;
		$this->autoFlushHandledData = $autoFlushHandledData;
		$this->autoFlushContentTypeFilter = $autoFlushContentTypeFilter;
		$this->destructionExceptionHandler = $destructionExceptionsHandler;
		ob_start();
	}

	protected function isMatchToAutoFlushFilter() {
		if($this->autoFlushContentTypeFilter) {
			$contentTypeHeaderPrefix = 'Content-type:';
			foreach(headers_list() as $header) {
				if(stripos($header, $contentTypeHeaderPrefix) === 0) {
					return $this->autoFlushContentTypeFilter->isMatch(substr($header, strlen($contentTypeHeaderPrefix)));
				}
			}
			return false;
		}
		return true;
	}

	/**
	 * Returns handled output data and turns off output buffering
	 * @throws SpeedOut_Exception
	 * @return string
	 */
	public function getHandledOutput() {
		if(!$this->isStarted) {
			throw new SpeedOut_Exception('Output handler was not started');
		}
		$this->isStarted = false;
		$data = ob_get_contents();
		ob_end_clean();
		$this->handleOutputData($data);
		return $data;
	}

	protected function handleOutputData(&$data) {
		foreach($this->handlers as $handler) {
			$handler->handleData($data);
		}
	}

	public function __destruct() {
		if($this->isStarted && $this->autoFlushHandledData && $this->isMatchToAutoFlushFilter()) {
			try {
				echo $this->getHandledOutput();
			}
			catch(Exception $exception) {
				if($this->destructionExceptionHandler) {
					call_user_func($this->destructionExceptionHandler, $exception);
				}
				else {
					throw $exception;
				}
			}
		}
	}
}
