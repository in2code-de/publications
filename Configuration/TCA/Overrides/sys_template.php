<?php
defined('TYPO3_MODE') || die();

/**
* TypoScript as static file
*/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'publications',
    'Configuration/TypoScript',
    'Publications'
);
