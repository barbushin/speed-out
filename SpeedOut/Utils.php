<?php

/**
 * Different kind of public static methods for common usage
 *
 * @see https://github.com/barbushin/speed-out
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 */
class SpeedOut_Utils {

	public static function getDocRoot() {
		if(empty($_SERVER['DOCUMENT_ROOT'])) {
			throw new SpeedOut_Exception('$_SERVER["DOCUMENT_ROOT"] is empty');
		}
		return self::getRealPath($_SERVER['DOCUMENT_ROOT'], false);
	}

	public static function getServerHost() {
		if(!empty($_SERVER['HTTP_HOST'])) {
			return $_SERVER['HTTP_HOST'];
		}
		elseif(!empty($_SERVER['SERVER_NAME'])) {
			return $_SERVER['SERVER_NAME'];
		}
		else {
			throw new SpeedOut_Exception('Cannot detect server host by $_SERVER["HTTP_HOST"] & $_SERVER["SERVER_NAME"]');
		}
	}

	public static function getServerProtocol() {
		return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
	}

	public static function getPathUrl($path) {
		$url = self::getServerProtocol() . '://' . self::getServerHost();
		if(isset($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443)) {
			$url .= ':' . $_SERVER['SERVER_PORT'];
		}
		$url .= self::getPathUri($path);
		return rtrim($url, '/');
	}

	/**
	 * Get URI of some file/directory path for current DOCUMENT_ROOT
	 * @static
	 * @param $path
	 * @throws SpeedOut_Exception
	 * @return string
	 */
	public static function getPathUri($path) {
		$path = self::getRealPath($path, false);
		if(!self::isPathUnderDocumentRoot($path)) {
			throw new SpeedOut_Exception('Path "' . $path . '" is not under DOCUMENT_ROOT "' . self::getDocRoot() . '"');
		}
		$uri = rtrim(substr($path, strlen(self::getDocRoot())), '/');
		return $uri ? $uri : '/';
	}

	public static function isPathUnderDocumentRoot($path) {
		return strpos($path, self::getDocRoot()) === 0;
	}

	public static function isExternalLink($link) {
		SpeedOut_Utils::safePregMatch('~^https?\://~i', $link);

		return (bool)SpeedOut_Utils::safePregMatch('~^https?\://~i', $link);
	}

	public static function isRelativeLink($link) {
		return $link == '' || (!self::isExternalLink($link) && $link[0] != '/' && $link[0] != '\\');
	}

	/**
	 * Get URL for external or local path of some link
	 * @static
	 * @param string $link
	 * @param string $baseDir
	 * @return string|null
	 */
	public static function getLinkPath($link, $baseDir = '.') {
		if(self::isExternalLink($link)) {
			return $link;
		}
		else {
			return self::getRealPath((self::isRelativeLink($link) ? $baseDir : self::getDocRoot()) . '/' . SpeedOut_Utils::safePregReplace('~\?.*~', '', $link), false);
		}
	}

	/**
	 * Get URL for external or URI for local path of some link
	 * @static
	 * @param $link
	 * @param string $baseDir
	 * @return string|null
	 */
	public static function getRealLink($link, $baseDir = null) {
		if(self::isExternalLink($link)) {
			return $link;
		}
		else {
			$path = ($baseDir && self::isRelativeLink($link) ? $baseDir : self::getDocRoot()) . '/' . SpeedOut_Utils::safePregReplace('~\?.*~', '', $link);
			return self::getPathUri($path, false);
		}
	}

	/**
	 * Get local real path of some file/directory path
	 * @static
	 * @param $path
	 * @param bool $checkPathExists
	 * @param bool $forceFrontSlash
	 * @throws SpeedOut_Exception
	 * @return string|null
	 */
	public static function getRealPath($path, $checkPathExists = true, $forceFrontSlash = true) {
		$realPath = realpath($path);
		if(!$realPath && !$checkPathExists) {
			$realParts = array();
			foreach(explode(DIRECTORY_SEPARATOR, str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path)) as $i => $part) {
				if($part == '.' || ($i && $part == '')) {
					continue;
				}
				if($part == '..') {
					if(!$realParts) {
						throw new SpeedOut_Exception('Path "' . $path . '" is wrong');
					}
					array_pop($realParts);
				}
				else {
					$realParts[] = $part;
				}
			}
			$realPath = implode(DIRECTORY_SEPARATOR, $realParts);
		}
		if($realPath === '') {
			$realPath = DIRECTORY_SEPARATOR;
		}
		if($forceFrontSlash && DIRECTORY_SEPARATOR == '\\') {
			$realPath = str_replace(DIRECTORY_SEPARATOR, '/', $realPath);
		}
		return $realPath;
	}

	protected static function pregHandleResult($result, $pattern, $errorResult = false) {
		if($result === $errorResult) {
			throw new SpeedOut_Exception('RegExp "' . $pattern . '" failed with error: ' . self::pregLastError());
		}
		return $result;
	}

	protected static function pregLastError() {
		$pregErrors = array(
			PREG_NO_ERROR => 'Syntax error',
			PREG_INTERNAL_ERROR => 'Internal error',
			PREG_BACKTRACK_LIMIT_ERROR => 'Backtrack limit error',
			PREG_RECURSION_LIMIT_ERROR => 'Recursion limit error',
			PREG_BAD_UTF8_ERROR => 'Bad UTF8',
		);
		return isset($pregErrors[preg_last_error()]) ? $pregErrors[preg_last_error()] : preg_last_error();
	}

	public static function safePregMatch($pattern, $subject, array &$matches = null, $flags = 0, $offset = 0) {
		self::pcreIncreaseBacktrackLimit($subject);
		return self::pregHandleResult(@preg_match($pattern, $subject, $matches, $flags, $offset), $pattern);
	}

	public static function safePregMatchAll($pattern, $subject, array &$matches = null, $flags = 0, $offset = 0) {
		self::pcreIncreaseBacktrackLimit($subject);
		return self::pregHandleResult(@preg_match_all($pattern, $subject, $matches, $flags, $offset), $pattern);
	}

	public static function safePregReplace($pattern, $replacement, $subject, $limit = -1, &$count = null) {
		self::pcreIncreaseBacktrackLimit($subject);
		return self::pregHandleResult(@preg_replace($pattern, $replacement, $subject, $limit, $count), $pattern, null);
	}

	public static function pcreIncreaseBacktrackLimit($string) {
		static $currentLimit;
		if(!$currentLimit) {
			$currentLimit = ini_get('pcre.backtrack_limit');
		}
		if(strlen($string) > $currentLimit) {
			$currentLimit = strlen($string) * 10;
			ini_set('pcre.backtrack_limit', $currentLimit);
		}
	}
}
