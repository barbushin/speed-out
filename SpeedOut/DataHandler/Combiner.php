<?php

/**
 * Data handler for combining many resources links in HTML to one resource link
 *
 * @see https://github.com/barbushin/speed-out
 * @author Barbushin Sergey http://linkedin.com/in/barbushin
 */
abstract class SpeedOut_DataHandler_Combiner extends SpeedOut_DataHandler {

	/** @var SpeedOut_CacheStorage */
	protected $storage;
	/** @var SpeedOut_Filter */
	protected $linksFilter;
	/** @var SpeedOut_DataHandler[] */
	protected $combinedDataHandlers = array();

	protected $combinedLinkPlaceholder;
	protected $insertBeforePlaceholder;
	protected $linksBaseDir;
	/** @var array This property is flushed to array on each new call of handleData() method */
	protected $handlingProcessData = array();

	abstract protected function getLinksNodesRegexps();

	abstract protected function getCommonLinkHtml($link);

	abstract protected function getCombinedDataPart($linkData, $linkPath);

	/**
	 * @param SpeedOut_CacheStorage $storage
	 * @param string $combinedLinkPlaceholder Placeholder that will be used to insert combined data link
	 * @param bool $insertBeforePlaceholder
	 * @param null|SpeedOut_Filter $internalLinksValidationFilter Filter what links must be validated with exception throwing
	 */
	public function __construct(SpeedOut_CacheStorage $storage, $combinedLinkPlaceholder = '<head>', $insertBeforePlaceholder = false, SpeedOut_Filter $internalLinksValidationFilter = null) {
		$this->storage = $storage;
		$this->combinedLinkPlaceholder = $combinedLinkPlaceholder;
		$this->insertBeforePlaceholder = $insertBeforePlaceholder;
		$this->linksFilter = $this->initLinksFilter($internalLinksValidationFilter);
		$linksBaseUri = !empty($_SERVER['PHP_SELF']) ? dirname($_SERVER['PHP_SELF']) : !empty($_SERVER['SCRIPT_NAME']) ? dirname($_SERVER['SCRIPT_NAME']) : null;
		$this->linksBaseDir = SpeedOut_Utils::getLinkPath($linksBaseUri);
	}

	protected function getLinksBaseDir() {
		if(!$this->linksBaseDir) {
			throw new SpeedOut_Exception('Links base dir is not defined');
		}
		return $this->linksBaseDir;
	}

	public function setLinksBaseDir($linksBaseDir) {
		if(!is_dir($linksBaseDir)) {
			throw new SpeedOut_Exception('Directory "' . $linksBaseDir . '" not found');
		}
		$this->linksBaseDir = $linksBaseDir;
	}

	/**
	 * @param null|SpeedOut_Filter $linksFilter
	 * @return SpeedOut_Filter
	 */
	protected function initLinksFilter(SpeedOut_Filter $linksFilter = null) {
		return $linksFilter ? $linksFilter : new SpeedOut_Filter();
	}

	/**
	 * Add data handler that will be applied to finally combined data
	 * @param SpeedOut_DataHandler $handler
	 */
	public function addCombinedDataHandler(SpeedOut_DataHandler $handler) {
		$this->combinedDataHandlers[] = $handler;
	}

	public function getFilter() {
		return $this->linksFilter;
	}

	/**
	 * Replace resources links by one link on file, that contains resources links data
	 * @param $html
	 * @throws SpeedOut_DataHandler_Combiner_Exception_PathNotFound
	 * @return null
	 */
	public function handleData(&$html) {
		$this->handlingProcessData = array();
		$linksNodes = $this->getLinksNodes($html);
		if($linksNodes) {
			$linksHash = $this->getLinksHash(array_keys($linksNodes));
			// combine and store links data
			if(!$this->storage->isStored($linksHash)) {
				$combinedData = '';
                $baseDir = $this->getLinksBaseDir();
                foreach($linksNodes as $link => $data) {
                    $linkPath = SpeedOut_Utils::getLinkPath(SpeedOut_Utils::getRealLink(html_entity_decode($link), $baseDir), $baseDir);
					$linkData = $this->getLinkData($linkPath);
					$combinedData .= $this->getCombinedDataPart($linkData, $linkPath);
				}
				$this->handleCombinedData($combinedData);
				$this->storage->store($linksHash, $combinedData);
			}
			$html = str_replace($linksNodes, '', $html);
			$this->addStringInCommonPlaceholder($html, $this->getCommonLinkHtml($this->storage->getUrl($linksHash)));
		}
	}

	protected function handleCombinedData(&$combinedData) {
		foreach($this->combinedDataHandlers as $combinedDataHandler) {
			$combinedDataHandler->handleData($combinedData);
		}
	}

	protected function getLinkData($linkPath) {
		if(!SpeedOut_Utils::isExternalLink($linkPath) && !is_file($linkPath)) {
			throw new SpeedOut_DataHandler_Combiner_Exception_PathNotFound($linkPath, '[link "' . SpeedOut_Utils::getPathUri($linkPath) . '" in HTML]');
		}
		$linkData = file_get_contents($linkPath);
		$this->handleLinkedFileData($linkData, $linkPath);
		return $linkData;
	}

	protected function handleLinkedFileData(&$linkData, $linkPath) {
		$this->validateNotEmpty($linkData, $linkPath);
		$this->validateNoPhpTags($linkData, $linkPath);
	}

	protected function validateNoPhpTags($linkData, $linkPath) {
		if(SpeedOut_Utils::safePregMatch('~^\s*<\?~s', $linkData)) {
			throw new SpeedOut_DataHandler_Combiner_Exception_InvalidFile($linkPath, 'File format "' . $linkPath . '" contains PHP tags');
		}
	}

	protected function validateNotEmpty($linkData, $linkPath) {
		if(!$linkData) {
			throw new SpeedOut_DataHandler_Combiner_Exception_InvalidFile($linkPath, 'File "' . $linkPath . '" is empty');
		}
	}

	/**
	 * Insert some string after/before some placeholder
	 * @param $data
	 * @param $string
	 * @throws SpeedOut_Exception
	 * @return void
	 */
	protected function addStringInCommonPlaceholder(&$data, $string) {
		$placeholdersInData = SpeedOut_Utils::safePregMatchAll('~' . preg_quote($this->combinedLinkPlaceholder, '~') . '~', $data);
		if($placeholdersInData === 0) {
			throw new SpeedOut_Exception('Placeholder "' . $this->combinedLinkPlaceholder . '" not found in source data');
		}
		elseif($placeholdersInData > 1) {
			throw new SpeedOut_Exception('There are more than one placeholder "' . $this->combinedLinkPlaceholder . '" in source data');
		}
		$data = str_replace($this->combinedLinkPlaceholder, $this->insertBeforePlaceholder
				? $string . "\n" . $this->combinedLinkPlaceholder
				: $this->combinedLinkPlaceholder . "\n" . $string,
			$data);
	}

	protected function removeHtmlComments(&$html) {
		return SpeedOut_Utils::safePregReplace('~<!--.*?-->~s', '', $html);
	}

	protected function getLinksHash($linksPaths) {
		$hashString = implode('@', $linksPaths);
		return substr(md5($hashString), -15);
	}

	protected function getInternalLinksPaths($string) {
		SpeedOut_Utils::safePregMatchAll('~[\'"\(]\s*([\:\w-_\./\\\\]+?\.[\w]{3,4})\s*[\'"\)]~', $string, $m);
		return array_unique($m[1]);
	}



	protected function getLinksNodes($html) {
		$html = $this->removeHtmlComments($html);
		$linksData = array();
		foreach($this->getLinksNodesRegexps() as $regexp) {
			if(SpeedOut_Utils::safePregMatchAll($regexp, $html, $m)) {
				foreach($m[1] as $i => $link) {
                    if(!SpeedOut_Utils::isExternalLink($link) && $this->linksFilter->isMatch($link)) {
                        $linksData[$link] = $m[0][$i];
					}
				}
			}
		}
        return $linksData;
	}
}
