<?php
defined('TYPO3') or die();

/**
 * Override icon for storage folder
 */
$ext = 'publications';
$iconRef = 'extension-' . $ext;

$addToModuleSelection = true;
foreach ($GLOBALS['TCA']['pages']['columns']['module']['config']['items'] as $item) {
    if ($item['value'] === $ext) {
        $addToModuleSelection = false;
        break;
    }
}

if ($addToModuleSelection) {
    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-' . $ext] = $iconRef;
    $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
        0 => 'LLL:EXT:' . $ext . '/Resources/Private/Language/locallang_db.xlf:wizardTitlePi1',
        1 => $ext,
        2 => $iconRef
    ];
}
