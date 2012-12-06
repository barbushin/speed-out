<?php

class Test_SpeedOut_DataHandler_Combiner_JS extends Test_SpeedOut_DataHandler_Combiner {

	protected function initCombiner(SpeedOut_CacheStorage $cacheStorage, $combinedLinkPlaceholder, $insertBeforePlaceholder) {
		return new SpeedOut_DataHandler_Combiner_JS($cacheStorage, $combinedLinkPlaceholder, $insertBeforePlaceholder);
	}

	protected function getFileType() {
		return 'js';
	}
}
