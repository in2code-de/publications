<?php

declare(strict_types=1);

namespace In2code\Publications\Migration;

use Doctrine\DBAL\Exception;
use In2code\Publications\Utility\DatabaseUtility;

class MigrateFromBib
{
    protected array $tableMapping = [
        'tx_bib_domain_model_reference' => 'tx_publications_domain_model_publication',
        'tx_bib_domain_model_author' => 'tx_publications_domain_model_author',
        'tx_bib_domain_model_authorships' => 'tx_publications_publication_author_mm'
    ];

    protected array $mappingPublicationFields = [
        'uid' => 'uid',
        'pid' => 'pid',
        'crdate' => 'crdate',
        'tstamp' => 'tstamp',
        'cruser_id' => 'cruser_id',
        'hidden' => 'hidden',
        'deleted' => 'deleted',
        'bibtype' => 'bibtype',
        'citeid' => 'citeid',
        'title' => 'title',
        'journal' => 'journal',
        'year' => 'year',
        'month' => 'month',
        'day' => 'day',
        'volume' => 'volume',
        'number' => 'number',
        'number2' => 'number2',
        'pages' => 'pages',
        'abstract' => 'abstract',
        'affiliation' => 'affiliation',
        'note' => 'note',
        'annotation' => 'annotation',
        'keywords' => 'keywords',
        'tags' => 'tags',
        'file_url' => 'file_url',
        'web_url' => 'web_url',
        'web_url_date' => 'web_url_date',
        'web_url2' => 'web_url2',
        'misc' => 'miscellaneous',
        'misc2' => 'miscellaneous2',
        'editor' => 'editor',
        'publisher' => 'publisher',
        'howpublished' => 'howpublished',
        'address' => 'address',
        'series' => 'series',
        'edition' => 'edition',
        'chapter' => 'chapter',
        'booktitle' => 'booktitle',
        'school' => 'school',
        'institute' => 'institute',
        'organization' => 'organization',
        'institution' => 'institution',
        'event_name' => 'event_name',
        'event_place' => 'event_place',
        'event_date' => 'event_date',
        'state' => 'status',
        'type' => 'type',
        'language' => 'language',
        'ISBN' => 'isbn',
        'ISSN' => 'issn',
        'DOI' => 'doi',
        'pmid' => 'pmid',
        'extern' => 'extern',
        'reviewed' => 'reviewed',
        'in_library' => 'in_library',
        'borrowed_by' => 'borrowed_by'
    ];

    protected array $mappingAuthorFields = [
        'uid' => 'uid',
        'pid' => 'pid',
        'crdate' => 'crdate',
        'tstamp' => 'tstamp',
        'cruser_id' => 'cruser_id',
        'hidden' => 'hidden',
        'deleted' => 'deleted',
        'forename' => 'first_name',
        'surname' => 'last_name',
        'url' => 'url'
    ];

    protected array $mappingMm = [
        'pub_id' => 'uid_local',
        'author_id' => 'uid_foreign',
        'sorting' => 'sorting'
    ];

    protected array $mappingBibTypes = [
        0 => '',
        1  => 'article',
        2  => 'book',
        3  => 'inbook',
        4  => 'booklet',
        5  => 'conference',
        6  => 'incollection',
        7  => 'proceedings',
        8  => 'inproceedings',
        9  => 'manual',
        10 => 'mastersthesis',
        11 => 'phdthesis',
        12 => 'techreport',
        13 => 'unpublished',
        14 => 'misc',
        15 => 'string',
        16 => 'poster',
        17 => 'thesis',
        18 => 'manuscript',
        19 => 'report',
        20 => 'misc',
        21 => 'url',
    ];

    /**
     * @throws Exception
     */
    public function migrate(): int
    {
        $numberOfPublications = $this->migratePublications();
        $this->migrateAuthors();
        $this->migrateMmRecords();
        return $numberOfPublications;
    }

    /**
     * @throws Exception
     */
    protected function migratePublications(): int
    {
        $publications = $this->getOldPublications();
        foreach ($publications as $publication) {
            $properties = [];
            foreach ($publication as $field => $value) {
                if (array_key_exists($field, $this->mappingPublicationFields)) {
                    if ($this->mappingPublicationFields[$field] === 'bibtype') {
                        $value = $this->mappingBibTypes[(int)$value];
                    }
                    $fieldName = $this->mappingPublicationFields[$field];
                    if (!empty($fieldName) && !empty($value)) {
                        $properties[$fieldName] = $value;
                    }
                }
            }
            /** @var string $tableName */
            $tableName = array_values($this->tableMapping)[0];
            $connection = DatabaseUtility::getConnectionForTable($tableName);
            $connection->insert($tableName, $properties);
        }
        return count($publications);
    }

    /**
     * @throws Exception
     */
    protected function migrateAuthors(): void
    {
        $authors = $this->getOldAuthors();
        foreach ($authors as $author) {
            $properties = [];
            foreach ($author as $field => $value) {
                if (array_key_exists($field, $this->mappingAuthorFields)) {
                    $fieldName = $this->mappingAuthorFields[$field];
                    if (!empty($fieldName) && !empty($value)) {
                        $properties[$fieldName] = $value;
                    }
                }
            }
            /** @var string $tableName */
            $tableName = array_values($this->tableMapping)[1];
            $connection = DatabaseUtility::getConnectionForTable($tableName);
            $connection->insert($tableName, $properties);
        }
    }

    /**
     * @throws Exception
     */
    protected function migrateMmRecords(): void
    {
        $mmRecords = $this->getOldMmRecords();
        foreach ($mmRecords as $mmRecord) {
            $properties = [];
            foreach ($mmRecord as $field => $value) {
                if (array_key_exists($field, $this->mappingMm)) {
                    $fieldName = $this->mappingMm[$field];
                    if (!empty($fieldName) && !empty($value)) {
                        $properties[$fieldName] = $value;
                    }
                }
            }
            /** @var string $tableName */
            $tableName = array_values($this->tableMapping)[2];
            $connection = DatabaseUtility::getConnectionForTable($tableName);
            $connection->insert($tableName, $properties);
        }
    }

    /**
     * @throws Exception
     */
    protected function getOldPublications(): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(array_keys($this->tableMapping)[0], true);
        return $queryBuilder
            ->select('*')
            ->from(array_keys($this->tableMapping)[0])
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    protected function getOldAuthors(): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(array_keys($this->tableMapping)[1], true);
        return $queryBuilder
            ->select('*')
            ->from(array_keys($this->tableMapping)[1])
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    protected function getOldMmRecords(): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(array_keys($this->tableMapping)[2], true);
        return $queryBuilder
            ->select('*')
            ->from(array_keys($this->tableMapping)[2])
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
