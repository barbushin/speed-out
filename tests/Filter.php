<?php

class Test_SpeedOut_Filter extends Test_SpeedOut_Abstract {

	const FILTER_DATA = 'ABCDEFG';

	/**
	 * @dataProvider filterRegexpsProvider
	 */
	public function testFilter(array $requiredRegexps, array $excludedRegexps, $expectedIsMatch) {
		$filter = new SpeedOut_Filter($requiredRegexps, $excludedRegexps, $expectedIsMatch);
		$this->assertEquals($expectedIsMatch, $filter->isMatch(self::FILTER_DATA));
	}

	public static function filterRegexpsProvider() {
		return array(
			array(array(), array(), true),
			array(array(), array('~x~'), true),
			array(array('~A~', '~B~'), array(), true),
			array(array('~x~'), array(), false),
			array(array('~x~', '~A~'), array('~A~'), false),
			array(array('~x~', '~A~'), array(), true),
			array(array('~A~', '~x~'), array(), true),
			array(array('~A~', '~x~', '~B~'), array(), true),
			array(array(), array('~A~'), false),
			array(array(), array('~x~', '~A~'), false),
			array(array(), array('~A~', '~x~'), false),
			array(array(), array('~A~', '~x~', '~A~'), false),
			array(array('~A~'), array('~A~'), false),
		);
	}
}
