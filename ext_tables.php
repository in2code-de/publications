<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function () {
        /**
         * Backend Module
         */
        if (TYPO3_MODE === 'BE') {
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'In2code.publications',
                'web',
                'import',
                '',
                [
                    'Import' => 'overview, import'
                ]
            );
        }
    }
);
