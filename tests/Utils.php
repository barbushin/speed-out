<?php

class Test_SpeedOut_Utils extends Test_SpeedOut_Abstract {

	/**
	 * @dataProvider getPathUriProvider
	 */
	public function testGetPathUri($path, $expectedUri) {
		$this->assertEquals($expectedUri, SpeedOut_Utils::getPathUri($path));
	}

	public static function getPathUriProvider() {
		return array(
			array(SpeedOut_Utils::getDocRoot(), '/'),
			array(SpeedOut_Utils::getDocRoot() . '/x', '/x'),
			array(SpeedOut_Utils::getDocRoot() . '/x/y/../z', '/x/z'),
			array(SpeedOut_Utils::getDocRoot() . '/x/y/../././z', '/x/z'),
			array(SpeedOut_Utils::getDocRoot() . '/x/y/../..', '/'),
		);
	}

	/**
	 * @expectedException SpeedOut_Exception
	 */
	public function testGetPathUriValidatesDocumentRoot() {
		SpeedOut_Utils::getPathUri(dirname(SpeedOut_Utils::getDocRoot()));
	}

	/**
	 * @dataProvider isExternalLinkProvider
	 */
	public function testIsExternalLink($link, $expectedIsExternal) {
		$this->assertEquals($expectedIsExternal, SpeedOut_Utils::isExternalLink($link));
	}

	public static function isExternalLinkProvider() {
		return array(
			array('http://goo.gl', true),
			array('http://goo', true),
			array('https://goo.gl', true),
			array('https://goo', true),
			array('http:goo', false),
			array('https:goo', false),
			array('httpgoo', false),
			array('httpsgoo', false),
			array('', false),
		);
	}

	/**
	 * @dataProvider isRelativeLinkProvider
	 */
	public function testIsRelativeLink($link, $expectedIsRelative) {
		$this->assertEquals($expectedIsRelative, SpeedOut_Utils::isRelativeLink($link));
	}

	public static function isRelativeLinkProvider() {
		return array(
			array('http://goo.gl', false),
			array('http://goo', false),
			array('https://goo.gl', false),
			array('https://goo', false),
			array('/', false),
			array('/a', false),
			array('', true),
			array('.', true),
			array('..', true),
			array('../..', true),
			array('../../a', true),
			array('a', true),
		);
	}

	/**
	 * @dataProvider getLinkPathProvider
	 */
	public function testGetLinkPath($baseDir, $link, $expectedPath) {
		$this->assertEquals($expectedPath, SpeedOut_Utils::getLinkPath($link, $baseDir));
	}

	public static function getLinkPathProvider() {
		return array(
			array(null, 'http://goo.gl', 'http://goo.gl'),
			array(null, 'https://goo.gl', 'https://goo.gl'),
			array('/a/b/c', '..', '/a/b'),
			array('/a/b/c', '..', '/a/b'),
			array('/a/b/c', '..////', '/a/b'),
			array('/a/b/c', '.././././.', '/a/b'),
			array('/a/b/c', '../../..', '/'),
			array('/a/b/c', '../../../', '/'),
		);
	}

	/**
	 * @dataProvider getRealLinkProvider
	 */
	public function testGetRealLink($baseDir, $path, $expectedLink) {
		$this->assertEquals($expectedLink, SpeedOut_Utils::getRealLink($path, $baseDir));
	}

	public static function getRealLinkProvider() {
		return array(
			array(null, 'http://goo.gl', 'http://goo.gl'),
			array(null, 'http://goo', 'http://goo'),
			array(null, 'https://goo.gl', 'https://goo.gl'),
			array(null, 'https://goo', 'https://goo'),
			array(null, '/a', '/a'),
			array(null, 'x/y/', '/x/y'),
			array(SpeedOut_Utils::getDocRoot() . '/x/y/z', '../../q', '/x/q'),
		);
	}

	/**
	 * @dataProvider getRealPathProvider
	 */
	public function testGetRealPath($path, $expectedRealPath) {
		$this->assertEquals($expectedRealPath, SpeedOut_Utils::getRealPath($path, false, false));
	}

	public static function getRealPathProvider() {
		return array(
			array('/a/b', DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR . 'b'),
			array('\a\b', DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR . 'b'),
			array('/a/b/..////../././', DIRECTORY_SEPARATOR),
		);
	}

	public function testGetRealPathForcesSlash() {
		$this->assertEquals('/a/b', SpeedOut_Utils::getRealPath('\a\b', false, true));
	}

	public function testGetRealPathChecksFileExists() {
		$this->assertEmpty(SpeedOut_Utils::getRealPath('/blahamuha', true));
	}

	/**
	 * @expectedException SpeedOut_Exception
	 */
	public function testWrongRealPathException() {
		SpeedOut_Utils::getRealPath('/a/b/../../../..', false);
	}

	public function testSafePregMatch() {
		SpeedOut_Utils::safePregMatch('~(x)~', 'x', $m);
		$this->assertCount(2, $m);
		$this->assertEquals('x', $m[1]);
	}

	/**
	 * @expectedException SpeedOut_Exception
	 */
	public function testSafePregMatchThrowsException() {
		SpeedOut_Utils::safePregMatch('~(x)[~', 'x');
	}

	public function testSafePregMatchAll() {
		SpeedOut_Utils::safePregMatchAll('~(x)~', 'xxx', $m);
		$this->assertCount(2, $m);
		$this->assertCount(3, $m[1]);
		$this->assertEquals('x', $m[1][0]);
	}

	/**
	 * @expectedException SpeedOut_Exception
	 */
	public function testSafePregMatchAllThrowsException() {
		SpeedOut_Utils::safePregMatchAll('~(x)[~', 'x');
	}

	public function testSafePregReplace() {
		$this->assertEquals('xxx', SpeedOut_Utils::safePregReplace('~[yz]~', 'x', 'xyz'));
	}

	/**
	 * @expectedException SpeedOut_Exception
	 */
	public function testSafePregReplaceThrowsException() {
		SpeedOut_Utils::safePregReplace('~[yz]', 'x', 'xyz');
	}
}
