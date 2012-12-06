<?php

class Test_SpeedOut_CacheStorage_Directory extends Test_SpeedOut_CacheStorage {

	protected function setUp() {
		parent::setUp();
		$this->clearTestsTmpDir();
	}

	/**
	 * @return SpeedOut_CacheStorage
	 */
	public function initCacheStorage() {
		return new SpeedOut_CacheStorage_Directory($this->getTestsTmpDir(), get_class($this) . '.', null, 0666);
	}

	/**
	 * @expectedException SpeedOut_Exception
	 */
	public function testInitWithWrongDirException() {
		new SpeedOut_CacheStorage_Directory($this->getTestsDataDir() . '/blahamuha');
	}

	/**
	 * @expectedException SpeedOut_Exception
	 */
	public function testInitWithNotInDocumentRootDirException() {
		new SpeedOut_CacheStorage_Directory(dirname(SpeedOut_Utils::getDocRoot()));
	}
}
