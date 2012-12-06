<?php

/**
 * Check if some string matches to require and exclude regexp patterns
 *
 * @see https://github.com/barbushin/speed-out
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 */
class SpeedOut_Filter {

	protected $requiredRegexps;
	protected $excludedRegexps;

	/**
	 * @param array $requiredRegexps
	 * @param array $excludedRegexps
	 */
	public function __construct(array $requiredRegexps = array(), array $excludedRegexps = array()) {
		$this->requiredRegexps = $requiredRegexps;
		$this->excludedRegexps = $excludedRegexps;
	}

	public function isMatch($data) {
		$isMatched = !$this->requiredRegexps;
		foreach($this->requiredRegexps as $regexp) {
			if(SpeedOut_Utils::safePregMatch($regexp, $data)) {
				$isMatched = true;
				break;
			}
		}
		foreach($this->excludedRegexps as $regexp) {
			if(SpeedOut_Utils::safePregMatch($regexp, $data)) {
				$isMatched = false;
				break;
			}
		}
		return $isMatched;
	}

	public function addRequiredRegexp($linkRegexp) {
		$this->requiredRegexps[] = $linkRegexp;
	}

	public function addIgnoreRegexp($linkRegexp) {
		$this->excludedRegexps[] = $linkRegexp;
	}

	public function getExcludedRegexps() {
		return $this->excludedRegexps;
	}

	public function getRequiredRegexps() {
		return $this->requiredRegexps;
	}
}
