<?php

/**
 * Store data in directory using file name as ID with sharing by URL
 *
 * @see http://code.google.com/p/speed-out
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 */
class SpeedOut_CacheStorage_Directory extends SpeedOut_CacheStorage {

	protected $dir;
	protected $dirUri;
	protected $fileNamePrefix;
	protected $fileNamePostfix;
	protected $directoryPermissions;

	/**
	 * @param $dir
	 * @param null $fileNamePrefix
	 * @param null $fileNamePostfix
	 * @param integer|null $directoryPermissions Cache directory permissions in octal format, e.x.: 0666
	 * @throws SpeedOut_Exception
	 */
	public function __construct($dir, $fileNamePrefix = null, $fileNamePostfix = null, $directoryPermissions = null) {
		$this->dir = SpeedOut_Utils::getRealPath($dir);
		if(!$this->dir) {
			throw new SpeedOut_Exception('Directory "' . $dir . '" not found');
		}
		if(!SpeedOut_Utils::isPathUnderDocumentRoot($this->dir)) {
			throw new SpeedOut_Exception('Directory "' . $this->dir . '" is not under DOCUMENT_ROOT "' . SpeedOut_Utils::getDocRoot() . '"');
		}

		$this->fileNamePrefix = $fileNamePrefix;
		$this->fileNamePostfix = $fileNamePostfix;
		$this->directoryPermissions = $directoryPermissions;
	}

	protected function getFilePathById($id) {
		return $this->dir . DIRECTORY_SEPARATOR . $this->fileNamePrefix . $id . $this->fileNamePostfix;
	}

	public function store($id, $data) {
		$filePath = $this->getFilePathById($id);
		if($this->directoryPermissions && strpos(PHP_OS, 'WIN') === false && !chmod($this->dir, $this->directoryPermissions)) {
			throw new SpeedOut_Exception('Chmod(' . decoct($this->directoryPermissions) . ') for directory "' . $this->dir . '" failed');
		}
		if(file_put_contents($this->getFilePathById($id), $data) === false) {
			throw new SpeedOut_Exception('Writing to file "' . $filePath . '" failed');
		}
	}

	public function getUrl($id) {
		if(is_file($this->getFilePathById($id))) {
			return SpeedOut_Utils::getPathUrl(SpeedOut_Utils::getDocRoot()) . SpeedOut_Utils::getPathUri($this->getFilePathById($id));
		}
	}

	public function isStored($id) {
		return is_file($this->getFilePathById($id));
	}
}
