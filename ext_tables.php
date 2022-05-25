<?php

declare(strict_types=1);

defined('TYPO3') or die();

call_user_func(
    function () {
        /**
         * Backend Module
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'publications',
            'web',
            'import',
            '',
            [
                In2code\Publications\Controller\ImportController::class => 'overview, import'
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:publications/Resources/Public/Icons/ModuleImport.svg',
                'labels' => 'LLL:EXT:publications/Resources/Private/Language/locallang_mod_import.xlf',
            ]
        );

        /**
         * Register default importer
         */
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['publications']['importer']['BIB'] =
            \In2code\Publications\Import\Importer\BibImporter::class;
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['publications']['importer']['XML'] =
            \In2code\Publications\Import\Importer\XmlImporter::class;
    }
);
