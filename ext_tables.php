<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function () {
        /**
         * Backend Module
         */
        if (TYPO3_MODE === 'BE') {
            ExtensionUtility::registerModule(
                'In2code.publications',
                'web',
                'import',
                '',
                [
                    'Import' => 'overview, import'
                ]
            );
        }

        /**
         * TypoScript
         */
        ExtensionManagementUtility::addStaticFile(
            'publications',
            'Configuration/TypoScript',
            'Publications'
        );
    }
);
