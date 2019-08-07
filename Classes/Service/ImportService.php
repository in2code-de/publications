<?php

namespace In2code\Publications\Service;

use Doctrine\DBAL\Statement;
use In2code\Publications\Domain\Model\Author;
use In2code\Publications\Domain\Model\Publication;
use In2code\Publications\Import\Importer\ImporterInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

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
        foreach ($this->publicationsToImport as $rawPublication) {
            $publication = $this->addOrUpdatePublication(
                $this->cleanupRawPublicationArray($rawPublication)
            );

            if (!empty($rawPublication['authors'])) {
                $authors = $this->addAuthors($rawPublication['authors']);
                $this->addAuthorRelations($publication, $authors);
            }
        }
    }

    /**
     * @param $publication
     * @param $authors
     */
    protected function addAuthorRelations($publication, $authors)
    {
        $relationTable = 'tx_publications_publication_author_mm';
        $sorting = 1;
        $relations = [];

        foreach ($authors as $author) {
            $relations[] = [
                'uid_local' => $publication['uid'],
                'uid_foreign' => $author['uid'],
                'sorting' => $sorting,
                'sorting_foreign' => $sorting
            ];

            $sorting++;
        }

        $this->connectionPool->getConnectionForTable($relationTable)->bulkInsert($relationTable, $relations);
    }

    /**
     * @param array $rawAuthors
     * @return array an array with the associated authors
     */
    protected function addAuthors(array $rawAuthors): array
    {
        $authors = [];

        foreach ($rawAuthors as $author) {
            $authors[] = $this->addAuthorIfNotExist($author['first_name'], $author['last_name']);
        }

        return $authors;
    }

    /**
     * @param array $publication
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function cleanupRawPublicationArray(array $publication)
    {
        $publicationTableFields = $this->getDatabaseFieldsByTable(Publication::TABLE_NAME);

        foreach ($publication as $publicationField => $value) {
            if (!in_array($publicationField, $publicationTableFields)) {
                // @todo log removed fields
                unset($publication[$publicationField]);
            }
        }

        return $publication;
    }

    protected function addOrUpdatePublication($record)
    {
        // set the author count
        if (!empty($record['authors'])) {
            $record['authors'] = count($record['authors']);
        }

        $record = array_merge_recursive($record, $this->getAdditionalTypo3Fields());
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(Publication::TABLE_NAME);
        $queryBuilder
            ->insert(Publication::TABLE_NAME)
            ->values($record)
            ->execute();

        $publicationUid =
            (int)$this->connectionPool->getConnectionForTable(Publication::TABLE_NAME)->lastInsertId(
                Publication::TABLE_NAME
            );

        return $publicationUid;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @return array the affected author
     */
    protected function addAuthorIfNotExist(string $firstName, string $lastName): array
    {
        $author = $this->getAuthorByName($firstName, $lastName);

        if (empty($author)) {
            $record = [
                'first_name' => $firstName,
                'last_name' => $lastName,
            ];

            // add additional fields for typo3 e.g. pid, tstamp etc.
            $record = array_merge_recursive($record, $this->getAdditionalTypo3Fields());

            $queryBuilder = $this->connectionPool->getQueryBuilderForTable(Author::TABLE_NAME);
            // insert author
            $queryBuilder
                ->insert(Author::TABLE_NAME)
                ->values($record)
                ->execute();

            $author = $this->getAuthorByName($firstName, $lastName);
        }

        return $author;
    }

    /**
     * @param $firstName
     * @param $lastName
     * @return mixed
     */
    protected function getAuthorByName($firstName, $lastName)
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(Author::TABLE_NAME);

        return $queryBuilder->select('*')->from(Author::TABLE_NAME)->where(
            $queryBuilder->expr()->eq('first_name', $queryBuilder->createNamedParameter($firstName, \PDO::PARAM_STR)),
            $queryBuilder->expr()->eq('last_name', $queryBuilder->createNamedParameter($lastName, \PDO::PARAM_STR))
        )->execute()->fetch();
    }

    protected function getAdditionalTypo3Fields()
    {
        return [
            'tstamp' => time(),
            'crdate' => time(),
            'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
            'pid' => $this->getStoragePid()
        ];
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

    /**
     * @return int
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getStoragePid(): int
    {
        $settings = $this->getExtensionSettings();
        $pid = (int)$settings['storagePid'];

        if (!empty(GeneralUtility::_GET('id'))) {
            $pid = (int)GeneralUtility::_GET('id');
        }

        return $pid;
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    protected function getExtensionSettings(): array
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(ConfigurationManager::class);

        return $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
    }
}
