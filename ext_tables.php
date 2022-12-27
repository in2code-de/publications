<?php

declare(strict_types=1);

defined('TYPO3') or die();

call_user_func(
    function () {
        /**
         * Register default importer
         */
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['publications']['importer']['BIB'] =
            \In2code\Publications\Import\Importer\BibImporter::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['publications']['importer']['XML'] =
            \In2code\Publications\Import\Importer\XmlImporter::class;
    }
);
