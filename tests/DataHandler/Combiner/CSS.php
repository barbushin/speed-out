<?php

class Test_SpeedOut_DataHandler_Combiner_CSS extends Test_SpeedOut_DataHandler_Combiner {

	protected function initCombiner(SpeedOut_CacheStorage $cacheStorage, $combinedLinkPlaceholder, $insertBeforePlaceholder) {
		return new SpeedOut_DataHandler_Combiner_CSS($cacheStorage, $combinedLinkPlaceholder, $insertBeforePlaceholder);
	}

	protected function getFileType() {
		return 'css';
	}
}
