<?php

abstract class Test_SpeedOut_DataHandler_Combiner extends Test_SpeedOut_DataHandler {

	/**
	 * @abstract
	 * @param SpeedOut_CacheStorage $cacheStorage
	 * @param $combinedLinkPlaceholder
	 * @param $insertBeforePlaceholder
	 * @return SpeedOut_DataHandler_Combiner
	 */
	abstract protected function initCombiner(SpeedOut_CacheStorage $cacheStorage, $combinedLinkPlaceholder, $insertBeforePlaceholder);

	abstract protected function getFileType();

	protected function setUp() {
		parent::setUp();
		$this->clearTestsTmpDir();
	}

	/**
	 * @param bool $insertBeforePlaceholder
	 * @internal param null|\SpeedOut_Filter $customLinksFilter
	 * @internal param null|\SpeedOut_Filter $internalLinksValidationFilter
	 * @internal param string $combinedLinkPlaceholder
	 * @return \SpeedOut_DataHandler|\SpeedOut_DataHandler_Combiner
	 */
	final protected function initDataHandler($insertBeforePlaceholder = false) {
		$cacheStorage = new SpeedOut_CacheStorage_Directory($this->getTestsTmpDir(), get_class($this) . '.' . mt_rand() . '.', '.generated', 0666);
		$combiner = $this->initCombiner($cacheStorage, $this->getPlaceholder(), $insertBeforePlaceholder);
		$combiner->setLinksBaseDir($this->getTestsHtmlDir());
		return $combiner;
	}

	protected function getHandledHtmlData(SpeedOut_DataHandler $handler = null) {
		return $this->getHandledData($this->getTestHtmlData(), $handler);
	}

	protected function getTestDataFile($fileUri) {
		return file_get_contents($this->getTestsHtmlDir() . '/' . $fileUri);
	}

	protected function getTestHtmlData() {
		return $this->getTestDataFile('index.html');
	}

	protected function getPlaceholder() {
		return '<!--' . $this->getFileType() . '-placeholder-->';
	}

	protected function getCombinedLinkRegexp() {
		return '~="(' . SpeedOut_Utils::getPathUrl($this->getTestsTmpDir()) . '[^"]+\.generated)".*?>~';
	}

	public function testPlaceholderInsertBefore() {
		$actualData = $this->getHandledHtmlData($this->initDataHandler(true));
		$this->assertRegExp('~' . trim($this->getCombinedLinkRegexp(), '~') . '\s*' . $this->getPlaceholder() . '~', $actualData);
		$this->assertNotRegExp('~\.' . $this->getFileType() . '~', $actualData);
	}

	public function testPlaceholderInsertAfter() {
		$actualData = $this->getHandledHtmlData($this->initDataHandler(false));
		$this->assertRegExp('~' . $this->getPlaceholder() . '\s*?<[^>]+?' . trim($this->getCombinedLinkRegexp(), '~') . '~', $actualData);
		$this->assertNotRegExp('~\.' . $this->getFileType() . '~', $actualData);
	}

	/**
	 * @expectedException SpeedOut_Exception
	 */
	public function testEmptyPlaceholderThrowsException() {
		$this->getHandledData(str_replace($this->getPlaceholder(), '', $this->getTestHtmlData()));
	}

	/**
	 * @expectedException SpeedOut_Exception
	 */
	public function testManyPlaceholdersThrowsException() {
		$this->getHandledData($this->getTestHtmlData() . $this->getPlaceholder());
	}

	public function testCombinedDataIsCorrect() {
		$this->assertEquals(1, SpeedOut_Utils::safePregMatch($this->getCombinedLinkRegexp(), $this->getHandledHtmlData(), $m));
		$combinedFileLink = $m[1];
		$combinedFilePath = str_replace(SpeedOut_Utils::getPathUrl(SpeedOut_Utils::getDocRoot()), SpeedOut_Utils::getDocRoot(), $combinedFileLink);
		$this->assertTrue(is_file($combinedFilePath));
		$this->assertEquals($this->getTestDataFile('_expected_combined.' . $this->getFileType()), file_get_contents($combinedFilePath));
	}
}
