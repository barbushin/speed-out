<?php

/**
 * Data handler for combining many JS resources links to one
 *
 * @see http://code.google.com/p/speed-out
 * @author Barbushin Sergey http://www.linkedin.com/in/barbushin
 */
class SpeedOut_DataHandler_Combiner_JS extends SpeedOut_DataHandler_Combiner {

	protected function getCombinedDataPart($linkData, $linkPath) {
		return '// FILE: ' . SpeedOut_Utils::getPathUri($linkPath) . "\n\n$linkData\n\n";
	}

	protected function getLinksNodesRegexps() {
		return array('~<script[^>]*?src=[\'"](.*?)[\'"].*?>.*?</script>~');
	}

	protected function getCommonLinkHtml($link) {
		return '<script type="text/javascript" src="' . htmlentities($link) . '"></script>';
	}
}
