<?php

namespace In2code\Publications\Service;

use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractService
 *
 * @package In2code\Publications\Service
 */
class AbstractService
{
    /**
     * @var Logger
     */
    protected $logger = null;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(static::class);
    }
}
