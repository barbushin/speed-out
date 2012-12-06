<?php

/**
 * Data handler for combining many JS resources links to one
 *
 * @see http://code.google.com/p/speed-out
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 */
class SpeedOut_DataHandler_Combiner_CSS extends SpeedOut_DataHandler_Combiner {

	/** @var null|SpeedOut_Filter */
	protected $internalLinksValidationFilter;

	public function __construct(SpeedOut_CacheStorage $storage, $combinedLinkPlaceholder = '<head>', $insertBeforePlaceholder = false, SpeedOut_Filter $customLinksFilter = null, SpeedOut_Filter $internalLinksValidationFilter = null) {
		$this->internalLinksValidationFilter = $internalLinksValidationFilter;
		parent::__construct($storage, $combinedLinkPlaceholder, $insertBeforePlaceholder, $customLinksFilter);
	}

	protected function initLinksFilter(SpeedOut_Filter $linksFilter = null) {
		$linksFilter = parent::initLinksFilter($linksFilter);
		$linksFilter->addIgnoreRegexp('~(^|/)print.css(\?|$)~');
		return $linksFilter;
	}

	/**
	 * @return lessc
	 */
	protected function getPhpLessCompiler() {
		// TODO: move to separate handler
		static $phpLessCompiler;
		if(!$phpLessCompiler) {
			require_once(dirname(__FILE__) . '/PhpLess/lessc.inc.php');
			$phpLessCompiler = new lessc();
		}
		return $phpLessCompiler;
	}

	protected function getLinkData($linkPath) {
		if(preg_match('~\.less$~', $linkPath)) {
			return $this->getPhpLessCompiler()->compileFile($linkPath);
		}
		return parent::getLinkData($linkPath);
	}

	protected function getCombinedDataPart($linkData, $linkPath) {
		return '/* FILE: ' . SpeedOut_Utils::getPathUri($linkPath) . " */ \n\n$linkData\n\n";
	}

	protected function getLinksNodesRegexps() {
		return array('~<link[^>]+rel=[\'"][^\'"]*stylesheet[^\'"]*[\'"][^>]+?href=[\'"](.*?)[\'"].*?/>~');
	}

	public function getCommonLinkHtml($link) {
		return '<link rel="stylesheet" href="' . htmlentities($link) . '" />';
	}

	protected function stripInternalComments(&$data) {
		$commentsByIds = array();
		if(SpeedOut_Utils::safePregMatchAll('~/\*.*?\*/~s', $data, $m)) {
			foreach($m[0] as $i => $comment) {
				$commentsByIds[';^&' . $i . '&^;'] = $comment;
			}
		}
		if($commentsByIds) {
			$data = str_replace($commentsByIds, array_keys($commentsByIds), $data);
		}
		return $commentsByIds;
	}

	protected function restoreInternalComments($commentsByIds, &$data) {
		$data = str_replace(array_keys($commentsByIds), $commentsByIds, $data);
	}

	protected function handleLinkedFileData(&$linkedFileData, $linkPath) {
		$comments = $this->stripInternalComments($linkedFileData);

		$this->replaceRelationalLinksInLinkData($linkedFileData, $linkPath);
		$this->replaceImportsInLinkedData($linkedFileData, $linkPath);
		parent::handleLinkedFileData($linkedFileData, $linkPath);

		$this->restoreInternalComments($comments, $linkedFileData);
	}

	protected function replaceRelationalLinksInLinkData(&$linkedFileData, $linkPath) {
		$internalLinksReplaces = array();
		$linksBaseDir = dirname($linkPath);
		if(SpeedOut_Utils::safePregMatchAll('~[\'"\(]\s*([\:\w-_\./\\\\]+?\.[\w]{3,4})\s*[\'"\)]~', $linkedFileData, $m)) {
			foreach($m[1] as $internalLink) {
				if(SpeedOut_Utils::isRelativeLink($internalLink)) {
					$internalLinkPath = SpeedOut_Utils::getLinkPath($internalLink, $linksBaseDir);
					$internalLinkUri = SpeedOut_Utils::getPathUri($internalLinkPath);
					if($this->isValidInternalLink($internalLinkPath, $linkPath)) {
						$internalLinksReplaces[$internalLink] = $internalLinkUri;
					}
				}
			}
		}
		foreach($this->getInternalLinksPaths($linkedFileData) as $internalLink => $internalLinkPath) {
			if(SpeedOut_Utils::isRelativeLink($internalLink)) {
			}
		}
		if($internalLinksReplaces) {
			$linkedFileData = str_replace(array_keys($internalLinksReplaces), $internalLinksReplaces, $linkedFileData);
		}
	}

	protected function isValidInternalLink($linkPath, $source) {
		if(!SpeedOut_Utils::isExternalLink($linkPath) && !is_file($linkPath)) {
			if($this->internalLinksValidationFilter && $this->internalLinksValidationFilter->isMatch($linkPath)) {
				throw new SpeedOut_DataHandler_Combiner_Exception_PathNotFound($linkPath, $source);
			}
			return false;
		}
		return true;
	}

	protected function replaceImportsInLinkedData(&$linkedFileData, $linkPath) {
		$linkBaseDir = dirname($linkPath);
		$importsRegexp = '~@import\s*?["\'](.*?)["\'].*?;~';
		$replaceLinksDataRegexps = array();

		$importedLinksPaths =& $this->handlingProcessData[__METHOD__ . 'importsLinksPaths'];
		if(!$importedLinksPaths) {
			$importedLinksPaths = array();
		}

		if(SpeedOut_Utils::safePregMatchAll($importsRegexp, $linkedFileData, $m)) {
			foreach($m[1] as $i => $importLink) {
				$importLinkPath = SpeedOut_Utils::getLinkPath($importLink, $linkBaseDir);
				if(!SpeedOut_Utils::isExternalLink($importLink) && !in_array($importLinkPath, $importedLinksPaths) && $this->isValidInternalLink($importLinkPath, 'combined CSS data')) {
					$importedLinksPaths[] = $importLinkPath;
					$replaceLinksDataRegexps['~' . preg_quote($m[0][$i], '~') . '~'] = $this->getLinkData($importLinkPath);
				}
			}
		}

		foreach($replaceLinksDataRegexps as $regexp => $replace) {
			$linkedFileData = SpeedOut_Utils::safePregReplace($regexp, $replace, $linkedFileData, 1);
		}

		$linkedFileData = SpeedOut_Utils::safePregReplace($importsRegexp, '', $linkedFileData);
	}
}
