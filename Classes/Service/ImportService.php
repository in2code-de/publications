<?php

namespace In2code\Publications\Service;

use In2code\Publications\Domain\Model\Publication;
use In2code\Publications\Import\Importer\ImporterInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImportService
{
    /**
     * @var ImporterInterface
     */
    protected $importer;

    /**
     * @var array
     */
    protected $dataArray = [];

    /**
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * ImportService constructor.
     *
     * @param string $data
     * @param ImporterInterface $importer
     */
    public function __construct(string $data, ImporterInterface $importer)
    {
        $this->importer = $importer;
        $this->dataArray = $this->importer->convert($data);
        $this->connectionPool =
            GeneralUtility::makeInstance(ConnectionPool::class);
    }

    /**
     *
     */
    public function import()
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(Publication::TABLE_NAME);
        // @todo import the array to the database
    }
}
