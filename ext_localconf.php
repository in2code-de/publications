<?php

use In2code\Publications\Controller\PublicationController;
use Psr\Log\LogLevel;
use TYPO3\CMS\Core\Log\Writer\FileWriter;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die();

ExtensionUtility::configurePlugin(
    'publications',
    'Pi1',
    [
        PublicationController::class => 'list,resetList,downloadBibtex,downloadXml'
    ],
    [
        PublicationController::class => 'list,resetList,downloadBibtex,downloadXml'
    ]
);

$GLOBALS['TYPO3_CONF_VARS']['LOG']['In2code']['Publications'] = [
    'writerConfiguration' => [
        LogLevel::DEBUG => [
            FileWriter::class => [
                'logFile' => 'typo3temp/logs/tx_publications.log'
            ]
        ]
    ]
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['publications'][] = 'In2code\Publications\ViewHelpers';
