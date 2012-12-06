<?php

/**
 * Controls SpeedOut activity state by argument in GET request and store it in SESSION
 * $_GET[$getVarName] == 1 // isActive === true
 * $_GET[$getVarName] == 0 // isActive === false
 * $_GET[$getVarName] === '' // isActive === $isActiveByDefault, for example index.php?_speedout=
 *
 * @see http://code.google.com/p/speed-out
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 */
class SpeedOut_Activator {

	protected $getVarName;
	protected $sessionVarName;
	protected $isActiveByDefault;
	protected $isActive;

	/**
	 * @param bool $isActiveByDefault If it will be active before first passing GET argument
	 * @param string $getVarName
	 * @param string $sessionVarNamePrefix
	 */
	public function __construct($isActiveByDefault = false, $getVarName = '_speedout', $sessionVarNamePrefix = '_speedOutIsActive?') {
		$this->getVarName = $getVarName;
		$this->sessionVarName = $sessionVarNamePrefix . $getVarName;
		$this->isActiveByDefault = (bool)$isActiveByDefault;
		$this->isActive = $this->initIsActive();
	}

	protected function initIsActive() {
		if(!session_id()) {
			session_start();
		}
		if(isset($_GET[$this->getVarName])) {
			if($_GET[$this->getVarName] === '') {
				if(isset($_SESSION[$this->sessionVarName])) {
					unset($_SESSION[$this->sessionVarName]);
				}
			}
			else {
				$_SESSION[$this->sessionVarName] = (bool)$_GET[$this->getVarName];
			}
		}
		if(isset($_SESSION[$this->sessionVarName])) {
			return $_SESSION[$this->sessionVarName];
		}
		return $this->isActiveByDefault;
	}

	public function isActive() {
		return $this->isActive;
	}
}
