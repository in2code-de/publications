<?php
defined('TYPO3') || die();

/**
* TypoScript as static file
*/
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'publications',
    'Configuration/TypoScript',
    'Publications'
);
