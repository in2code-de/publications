<?php

declare(strict_types = 1);

namespace In2code\Publications\Service;

use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractService
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
