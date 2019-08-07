<?php

namespace In2code\Publications\Service;

use Doctrine\DBAL\Statement;
use In2code\Publications\Domain\Model\Author;
use In2code\Publications\Domain\Model\Publication;
use In2code\Publications\Import\Importer\ImporterInterface;
use In2code\Publications\Utility\DatabaseUtility;
use Psr\Log\LogLevel;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class ImportService extends AbstractService
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
     * @var int
     */
    protected $storagePid;

    /**
     * ImportService constructor.
     *
     * @param string $data
     * @param ImporterInterface $importer
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function __construct(string $data, ImporterInterface $importer)
    {
        parent::__construct();

        $this->importer = $importer;
        $this->publicationsToImport = $this->importer->convert($data);
        $this->storagePid = $this->getStoragePid();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
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
                $this->logger->log(
                    LogLevel::INFO,
                    'The field "' . $publicationField . '" from the raw publication record has bin ignored because there is no suitable counterpart in the database.'
                );
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

        if ($this->existPublicationOnPid($record)) {
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
            $updatedPublication['citeid'],
            $updatedPublication['date']
        );

        $fieldsToUpdate = $this->getFieldsToUpdate($currentPublication, $updatedPublication);

        $affectedRows = DatabaseUtility::getConnectionForTable(Publication::TABLE_NAME)->update(
            Publication::TABLE_NAME,
            $fieldsToUpdate,
            ['uid' => $currentPublication['uid']]
        );

        if ($affectedRows > 0) {
            $this->logger->log(
                LogLevel::DEBUG,
                'The following fields of the publication #' . $currentPublication['uid'] . ' where updated',
                $fieldsToUpdate
            );
        } else {
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
            ->execute();

        $publicationUid =
            (int)DatabaseUtility::getConnectionForTable(Publication::TABLE_NAME)->lastInsertId(Publication::TABLE_NAME);

        $this->logger->log(
            LogLevel::DEBUG,
            'The publication #' . $publicationUid . ' was created',
            $publicationRecord
        );

        return $publicationUid;
    }

    /**
     * @param array $publication
     * @return bool
     */
    protected function existPublicationOnPid(array $publication): bool
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Publication::TABLE_NAME);

        $publicationCount = $queryBuilder
            ->count('*')
            ->from(Publication::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($this->storagePid, \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'citeid',
                    $queryBuilder->createNamedParameter($publication['citeid'], \PDO::PARAM_INT)
                ),
                $queryBuilder->expr()->eq(
                    'date',
                    $queryBuilder->createNamedParameter($publication['date'], \PDO::PARAM_INT)
                )
            )
            ->execute()->fetchColumn(0);

        if ($publicationCount > 0) {
            return true;
        }

        return false;
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

            // insert author
            DatabaseUtility::getQueryBuilderForTable(Author::TABLE_NAME)
                ->insert(Author::TABLE_NAME)
                ->values($record)
                ->execute();

            $author = $this->getAuthorByName($firstName, $lastName);

            $this->logger->log(LogLevel::DEBUG, 'The author #' . $author['uid'] . ' was created.', $author);
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
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Author::TABLE_NAME);

        return $queryBuilder->select('*')->from(Author::TABLE_NAME)->where(
            $queryBuilder->expr()->eq('first_name', $queryBuilder->createNamedParameter($firstName, \PDO::PARAM_STR)),
            $queryBuilder->expr()->eq('last_name', $queryBuilder->createNamedParameter($lastName, \PDO::PARAM_STR))
        )->execute()->fetch();
    }

    /**
     * @param int $pid
     * @param string $citeid
     * @param int $date
     * @return array
     */
    protected function getPublicationByIdentifier(int $pid, string $citeid, int $date): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(Publication::TABLE_NAME);
        $publication = $queryBuilder->select('*')->from(Publication::TABLE_NAME)->where(
            $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid, \PDO::PARAM_INT)),
            $queryBuilder->expr()->eq('citeid', $queryBuilder->createNamedParameter($citeid, \PDO::PARAM_STR)),
            $queryBuilder->expr()->eq('date', $queryBuilder->createNamedParameter($date, \PDO::PARAM_INT))
        )->execute()->fetch();

        if (!empty($publication)) {
            return $publication;
        }

        return [];
    }

    /**
     * @return array
     */
    protected function getAdditionalTypo3Fields()
    {
        return [
            'tstamp' => time(),
            'crdate' => time(),
            'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
            'pid' => $this->storagePid
        ];
    }

    /**
     * @param string $table
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function getDatabaseFieldsByTable(string $table): array
    {
        $fields = [];
        /** @var Statement $statement */
        $statement = GeneralUtility::makeInstance(
            \Doctrine\DBAL\Statement::class,
            'SHOW COLUMNS FROM ' . $table,
            DatabaseUtility::getConnectionForTable($table)
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
