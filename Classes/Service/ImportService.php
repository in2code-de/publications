<?php

namespace In2code\Publications\Service;

use Doctrine\DBAL\Statement;
use In2code\Publications\Domain\Model\Author;
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
    protected $publicationsToImport = [];

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
        $this->publicationsToImport = $this->importer->convert($data);

        $this->connectionPool =
            GeneralUtility::makeInstance(ConnectionPool::class);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function import()
    {
        $publicationTableFields = $this->getDatabaseFieldsByTable(Publication::TABLE_NAME);
        $authorTableFields = $this->getDatabaseFieldsByTable(Author::TABLE_NAME);

        foreach ($this->publicationsToImport as $publication) {
            if (array_key_exists('authors', $publication)) {
                // @todo create authors + relations if not exist.
            }

            foreach ($publicationTableFields as $tableField) {
                if (array_key_exists($tableField, $publication)) {
                    // $record contains the actual data which can be imported to the database
                    $record[$tableField] = $publication[$tableField];
                } else {
                    // @todo log not matching fields!
                }
            }
        }

        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(
            $record,
            __CLASS__ . ' in der Zeile ' . __LINE__
        );
        die();

        // @todo import the array to the database
    }

    /**
     * @param string $table
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getDatabaseFieldsByTable(string $table): array
    {
        $connection = $this->connectionPool->getConnectionForTable($table);
        $fields = [];
        /** @var Statement $statement */
        $statement = GeneralUtility::makeInstance(
            \Doctrine\DBAL\Statement::class,
            'SHOW COLUMNS FROM ' . $table,
            $connection
        );

        $statement->execute();

        foreach ($statement->fetchAll() as $column) {
            $fields[] = $column['Field'];
        }

        return $fields;
    }
}
