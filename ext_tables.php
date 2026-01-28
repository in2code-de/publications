<?php

declare(strict_types=1);

use In2code\Publications\Import\Importer\BibImporter;
use In2code\Publications\Import\Importer\XmlImporter;

defined('TYPO3') or die();

/**
 * Register default importer
 */
$GLOBALS['TYPO3_CONF_VARS']['EXT']['publications']['importer']['BIB'] =
    BibImporter::class;
$GLOBALS['TYPO3_CONF_VARS']['EXT']['publications']['importer']['XML'] =
    XmlImporter::class;
