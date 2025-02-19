<?php

declare(strict_types=1);

namespace In2code\Publications\Service;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Statement;
use In2code\Publications\Domain\Model\Author;
use In2code\Publications\Domain\Model\Publication;
use In2code\Publications\Import\Importer\ImporterInterface;
use In2code\Publications\Import\ImportOptions;
use In2code\Publications\Utility\DatabaseUtility;
use Psr\Log\LogLevel;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;

/**
 * Class ImportService
 */
class ImportService extends AbstractService
{
    /**
     * @var ImporterInterface
     */
    protected ImporterInterface $importer;

    /**
     * @var array
     */
    protected array $publicationsToImport = [];

    /**
     * @var int
     */
    protected int $storagePid;

    /**
     * contains information how much records where affected / created / updated etc.
     */
    protected array $importInformation = [
        'updatedPublications' => 0,
        'createdPublications' => 0,
        'publicationsWithNoUpdate' => 0,
        'createdAuthors' => 0
    ];

    protected array $importOptions = [];

    /**
     * ImportService constructor.
     *
     * @param string $data
     * @param ImporterInterface $importer
     * @throws InvalidConfigurationTypeException
     */
    public function __construct(string $data, ImporterInterface $importer, array $importOptions)
    {
        parent::__construct();

        $this->importer = $importer;
        $this->publicationsToImport = $this->importer->convert($data);
        $this->importOptions = $importOptions;
        $this->storagePid = $this->getStoragePid();
    }

    /**
     * @throws DBALException
     */
    public function import()
    {
        foreach ($this->publicationsToImport as $rawPublication) {
            $publicationUid = $this->addOrUpdatePublication(
                $this->cleanupRawPublicationArray($rawPublication)
            );

            if (!empty($rawPublication['authors'])) {
                $authors = $this->addAuthors($rawPublication['authors']);
                $this->addAuthorRelations($publicationUid, $authors);
            }
        }
    }

    /**
     * @return array
     */
    public function getImportInformation(): array
    {
        return $this->importInformation;
    }

    /**
     * @return bool
     */
    public function isImported(): bool
    {
        return $this->importInformation['updatedPublications'] > 0
            || $this->importInformation['createdPublications'] > 0
            || $this->importInformation['publicationsWithNoUpdate'] > 0
            || $this->importInformation['createdAuthors'] > 0;
    }

    /**
     * create the author relations between the given publication and authors
     * NOTE: existing relations will be deleted before new relations will be created
     *
     * @param int $publicationUid
     * @param array $authors
     */
    protected function addAuthorRelations(int $publicationUid, array $authors)
    {
        $relationTable = 'tx_publications_publication_author_mm';
        $sorting = 1;
        $relations = [];

        foreach ($authors as $author) {
            $relations[] = [
                'uid_local' => $publicationUid,
                'uid_foreign' => $author['uid'],
                'sorting' => $sorting,
                'sorting_foreign' => $sorting
            ];

            $sorting++;
        }

        $this->removeAuthorRelations($publicationUid);

        $affectedRows = DatabaseUtility::getConnectionForTable($relationTable)->bulkInsert($relationTable, $relations);

        $this->logger->log(
            LogLevel::DEBUG,
            $affectedRows . ' Author relations have bin created for publication #' . $publicationUid
        );
    }

    /**
     * deletes all existing author relations for an given publication
     *
     * @param int $publicationUid
     */
    protected function removeAuthorRelations(int $publicationUid)
    {
        $relationTable = 'tx_publications_publication_author_mm';

        $queryBuilder = DatabaseUtility::getQueryBuilderForTable($relationTable);
        $affectedRows = $queryBuilder->delete($relationTable)->where(
            $queryBuilder->expr()->eq(
                'uid_local',
                $queryBuilder->createNamedParameter($publicationUid, \PDO::PARAM_INT)
            )
        )->execute();

        $this->logger->log(
            LogLevel::DEBUG,
            $affectedRows . ' Author relations deleted from publication #' . $publicationUid
        );
    }

    /**
     * create multiple authors
     *
     * @param array $rawAuthors
     * @return array an array with the associated authors
     */
    protected function addAuthors(array $rawAuthors): array
    {
        $authors = [];

        foreach ($rawAuthors as $author) {
            $authors[] = $this->addAuthorIfNotExist(trim($author['first_name']), trim($author['last_name']));
        }

        return $authors;
    }

    /**
     * @param array $publication
     * @return array
     * @throws DBALException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    protected function cleanupRawPublicationArray(array $publication)
    {
        $publicationTableFields = $this->getDatabaseFieldsByTable(Publication::TABLE_NAME);
        foreach ($publication as $publicationField => $value) {
            if (!in_array($publicationField, $publicationTableFields)) {
                $this->logger->log(
                    LogLevel::INFO,
                    'The field "' . $publicationField . '" from the raw publication record has been ignored because there is no suitable counterpart in the database.'
                );
                unset($publication[$publicationField]);
            }
        }

        return $publication;
    }

    /**
     * @param $record
     * @return int
     */
    protected function addOrUpdatePublication($record): int
    {
        // set the author count
        if (!empty($record['authors'])) {
            $record['authors'] = count($record['authors']);
        }

        $record = array_merge_recursive($record, $this->getAdditionalTypo3Fields());

        $currentPublication = $this->getPublicationByIdentifier(
            $this->storagePid,
            $record['title'],
            $record['year'],
            $record['citeid'],
            $record['bibtype']
        );

        if (!empty($currentPublication)) {
            $publicationUid = $this->updatePublication($record);
        } else {
            $publicationUid = $this->insertPublication($record);
        }

        return $publicationUid;
    }

    /**
     * @param array $updatedPublication
     * @return int
     */
    protected function updatePublication(array $updatedPublication): int
    {
        $currentPublication = $this->getPublicationByIdentifier(
            $this->storagePid,
            $updatedPublication['title'],
            $updatedPublication['year'],
            $updatedPublication['citeid'],
            $updatedPublication['bibtype']
        );

        $fieldsToUpdate = $this->getFieldsToUpdate($currentPublication, $updatedPublication);

        $affectedRows = DatabaseUtility::getConnectionForTable(Publication::TABLE_NAME)->update(
            Publication::TABLE_NAME,
            $fieldsToUpdate,
            ['uid' => $currentPublication['uid']]
        );

        if ($affectedRows > 0) {
            $this->importInformation['updatedPublications']++;
            $this->logger->log(
                LogLevel::DEBUG,
                'The following fields of the publication #' . $currentPublication['uid'] . ' where updated',
                $fieldsToUpdate
            );
        } else {
            $this->importInformation['publicationsWithNoUpdate']++;
            $this->logger->log(
                LogLevel::DEBUG,
                'There was no updates for the publication #' . $currentPublication['uid']
            );
        }

        return $currentPublication['uid'];
    }

    /**
     * @param array $currentPublication
     * @param array $updatedPublication
     * @return array
     */
    protected function getFieldsToUpdate(array $currentPublication, array $updatedPublication): array
    {
        // fields which should not be updated.
        $fieldsToIgnore = [
            'uid',
            'crdate'
        ];

        // remove fields to ignore
        foreach ($fieldsToIgnore as $fieldToIgnore) {
            unset($updatedPublication[$fieldToIgnore]);
        }

        return array_diff_assoc($updatedPublication, $currentPublication);
    }

    /**
     * @param array $publicationRecord
     * @return int
     */
    protected function insertPublication(array $publicationRecord)
    {
        DatabaseUtility::getQueryBuilderForTable(Publication::TABLE_NAME)
            ->insert(Publication::TABLE_NAME)
            ->values($publicationRecord)
            ->executeStatement();

        $publicationUid =
            (int)DatabaseUtility::getConnectionForTable(Publication::TABLE_NAME)->lastInsertId(Publication::TABLE_NAME);

        $this->importInformation['createdPublications']++;
        $this->logger->log(
            LogLevel::DEBUG,
            'The publication #' . $publicationUid . ' was created',
            $publicationRecord
        );

        return $publicationUid;
    }

    /**
     * creates an author if no matching author exists
     *
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

            // insert author
            DatabaseUtility::getQueryBuilderForTable(Author::TABLE_NAME)
                ->insert(Author::TABLE_NAME)
                ->values($record)
                ->executeStatement();

            $author = $this->getAuthorByName($firstName, $lastName);

            $this->importInformation['createdAuthors']++;
            $this->logger->log(LogLevel::DEBUG, 'The author #' . $author['uid'] . ' was created.', $author);
        }

        return $author;
    }

    /**
     * @param $firstName
     * @param $lastName
     * @return array
     */
    protected function getAuthorByName($firstName, $lastName): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Author::TABLE_NAME);

        $statement = $queryBuilder->select('*')->from(Author::TABLE_NAME)->where(
            $queryBuilder->expr()->eq('first_name', $queryBuilder->createNamedParameter($firstName, \PDO::PARAM_STR)),
            $queryBuilder->expr()->eq('last_name', $queryBuilder->createNamedParameter($lastName, \PDO::PARAM_STR))
        );

        if ($this->importOptions['duplicateAuthorBehaviour'] === ImportOptions::DUPLICATE_AUTHOR_BEHAVIOUR_PID) {
            $statement->andWhere(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($this->storagePid, \PDO::PARAM_INT)
                )
            );
        }

        $author = $statement->executeQuery()->fetchAssociative();

        if (!empty($author)) {
            return $author;
        }

        return [];
    }

    /**
     * @param int $pid
     * @param string $title
     * @param string $year
     * @param string $citeid
     * @param string $bibtype
     * @return array
     */
    protected function getPublicationByIdentifier(int $pid, string $title, string $year, string $citeid, string $bibtype): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Publication::TABLE_NAME);
        $publication = $queryBuilder->select('*')->from(Publication::TABLE_NAME)->where(
            $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)),
            $queryBuilder->expr()->eq('title', $queryBuilder->createNamedParameter($title, \PDO::PARAM_STR)),
            $queryBuilder->expr()->eq('year', $queryBuilder->createNamedParameter($year, \PDO::PARAM_STR)),
            $queryBuilder->expr()->eq('citeid', $queryBuilder->createNamedParameter($citeid, \PDO::PARAM_STR)),
            $queryBuilder->expr()->eq('bibtype', $queryBuilder->createNamedParameter($bibtype, \PDO::PARAM_STR))
        )->executeQuery()->fetchAssociative();

        if (!empty($publication)) {
            return $publication;
        }

        return [];
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    protected function getAdditionalTypo3Fields()
    {
        return [
            'tstamp' => time(),
            'crdate' => time(),
            'pid' => $this->storagePid,
            'sys_language_uid' => (int)$this->importOptions['languageBehavior'] ?? 0
        ];
    }

    /**
     * returns an array with all existing fields of a database table
     *
     * @throws DBALException
     * @throws \Doctrine\DBAL\Exception
     */
    protected function getDatabaseFieldsByTable(string $table): array
    {
        $fields = [];

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable($table);

        $query = 'SHOW COLUMNS FROM `' . $table . '`';
        /** @var Statement $statement */
        $statement = $connection->prepare($query);
        $result = $statement->executeQuery()->fetchAllAssociative();

        foreach ($result as $column) {
            if (isset($column['Field'])) {
                $fields[] = $column['Field'];
            }
        }
        return $fields;
    }

    /**
     * get the current selected pid with fallback auf an defined storagePid in the typoscript
     *
     * @return int
     * @throws InvalidConfigurationTypeException
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
     * @throws InvalidConfigurationTypeException
     */
    protected function getExtensionSettings(): array
    {
        /** @var ConfigurationManager $configurationManager */
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        return $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);
    }
}
