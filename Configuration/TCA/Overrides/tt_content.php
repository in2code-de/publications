<?php

use TYPO3\CMS\Extbase\Utility\ExtensionUtility as ExtensionUtilityAlias;

defined('TYPO3') || die();

$llPrefix = 'LLL:EXT:publications/Resources/Private/Language/locallang_db.xlf:';

ExtensionUtilityAlias::registerPlugin(
    'publications',
    'Pi1',
    $llPrefix . 'wizardTitlePi1',
    'extension-publications-wizard-icon',
    'Publications',
    $llPrefix . 'wizardDescriptionsPi1',
    'FILE:EXT:publications/Configuration/FlexForms/FlexFormPi1.xml'
);
