<?php

abstract class Test_SpeedOut_CacheStorage extends Test_SpeedOut_Abstract {

	/** @var SpeedOut_CacheStorage */
	protected $storage;

	/**
	 * @abstract
	 * @return SpeedOut_CacheStorage
	 */
	abstract public function initCacheStorage();

	protected function generateStoreId() {
		return mt_rand();
	}

	protected function setUp() {
		$this->storage = $this->initCacheStorage();
	}

	public function testStoreAndGetUrl() {
		$storeId = $this->generateStoreId();
		$expectedData = mt_rand();
		$this->storage->store($storeId, $expectedData);
		$actualUrl = $this->storage->getUrl($storeId);
		$this->assertTrue(SpeedOut_Utils::isExternalLink($actualUrl));

		// because of tests are running from CLI
		$actualPath = SpeedOut_Utils::getDocRoot() . substr($actualUrl, strlen(SpeedOut_Utils::getPathUrl(SpeedOut_Utils::getDocRoot())));
		$this->assertEquals($expectedData, file_get_contents($actualPath));
	}

	public function testIsStored() {
		$storeId = $this->generateStoreId();
		$this->assertFalse($this->storage->isStored($storeId));
		$this->storage->store($storeId, 123);
		$this->assertTrue($this->storage->isStored($storeId));
	}
}
