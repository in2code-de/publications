<?php
defined('TYPO3_MODE') || die();

/**
 * Register Plugins
 */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin('publications', 'Pi1', 'Publication List');

/**
 * Disable not needed fields in tt_content
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['publications_pi1']
    = 'select_key,pages,recursive';

/**
 * Include Flexform
 */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['publications_pi1'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'publications_pi1',
    'FILE:EXT:publications/Configuration/FlexForms/FlexFormPi1.xml'
);
