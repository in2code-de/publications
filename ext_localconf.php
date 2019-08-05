<?php
defined('TYPO3_MODE') || die();

call_user_func(
    function () {

        /**
         * PageTSConfig
         */
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:publications/Configuration/TsConfig/Page/ContentElements.tsconfig">'
        );
    }
);
