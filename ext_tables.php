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

        /**
         * Register icons
         */
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        $iconRegistry->registerIcon(
            'extension-publications',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:publications/Resources/Public/Icons/Extension.svg']
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
