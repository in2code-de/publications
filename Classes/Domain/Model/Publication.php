<?php

declare(strict_types=1);

namespace In2code\Publications\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Class Publication
 */
class Publication extends AbstractEntity
{
    public const TABLE_NAME = 'tx_publications_domain_model_publication';

    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    protected string $abstract = '';

    /**
     * @var string
     */
    protected string $bibtype = '';

    /**
     * @var string
     */
    protected string $type = '';

    /**
     * @var int
     */
    protected int $status = 0;

    /**
     * @var string
     */
    protected string $year = '';

    /**
     * @var string
     */
    protected string $month = '';

    /**
     * @var string
     */
    protected string $day = '';

    /**
     * @var bool
     */
    protected bool $reviewed = false;

    /**
     * @var string
     */
    protected string $language = '';

    /**
     * @var string
     */
    protected string $citeid = '';

    /**
     * @var string
     */
    protected string $isbn = '';

    /**
     * @var string
     */
    protected string $issn = '';

    /**
     * @var string
     */
    protected string $doi = '';

    /**
     * @var string
     */
    protected string $pmid = '';

    /**
     * @var string
     */
    protected string $organization = '';

    /**
     * @var string
     */
    protected string $school = '';

    /**
     * @var string
     */
    protected string $institution = '';

    /**
     * @var string
     */
    protected string $institute = '';

    /**
     * @var string
     */
    protected string $booktitle = '';

    /**
     * @var string
     */
    protected string $journal = '';

    /**
     * @var string
     */
    protected string $journalAbbr = '';

    /**
     * @var string
     */
    protected string $edition = '';

    /**
     * @var string
     */
    protected string $volume = '';

    /**
     * @var string
     */
    protected string $publisher = '';

    /**
     * @var string
     */
    protected string $address = '';

    /**
     * @var string
     */
    protected string $chapter = '';

    /**
     * @var string
     */
    protected string $series = '';

    /**
     * @var string
     */
    protected string $howpublished = '';

    /**
     * @var string
     */
    protected string $editor = '';

    /**
     * @var string
     */
    protected string $pages = '';

    /**
     * @var string
     */
    protected string $affiliation = '';

    /**
     * @var bool
     */
    protected bool $extern = false;

    /**
     * @var string
     */
    protected string $eventName = '';

    /**
     * @var string
     */
    protected string $eventPlace = '';

    /**
     * @var string
     */
    protected string $eventDate = '';

    /**
     * @var string
     */
    protected string $number = '';

    /**
     * @var string
     */
    protected string $number2 = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\In2code\Publications\Domain\Model\Author>
     */
    protected $authors = null;

    /**
     * @var string
     */
    protected string $keywords = '';

    /**
     * @var string
     */
    protected string $tags = '';

    /**
     * @var string
     */
    protected string $webUrl = '';

    /**
     * @var string
     */
    protected string $webUrl2 = '';

    /**
     * @var string
     */
    protected string $webUrlDate = '';

    /**
     * @var string
     */
    protected string $fileUrl = '';

    /**
     * @var string
     */
    protected string $note = '';

    /**
     * @var string
     */
    protected string $annotation = '';

    /**
     * @var string
     */
    protected string $miscellaneous = '';

    /**
     * @var string
     */
    protected string $miscellaneous2 = '';

    /**
     * @var bool
     */
    protected bool $inLibrary = false;

    /**
     * @var string
     */
    protected string $borrowedBy = '';

    /**
     * Basic mapping for any export. Which fields should be available in export files with which key.
     * ModelProperty => KeyNameInExport
     *
     * @var array
     */
    protected array $propertiesMapping = [
        'title' => 'title',
        'abstract' => 'abstract',
        'type' => 'type',
        'status' => 'status',
        'year' => 'year',
        'month' => 'month',
        'day' => 'day',
        'reviewed' => 'reviewed',
        'language' => 'language',
        'isbn' => 'isbn',
        'issn' => 'issn',
        'doi' => 'DOI',
        'pmid' => 'pmid',
        'organization' => 'organization',
        'school' => 'school',
        'institution' => 'institution',
        'institute' => 'institute',
        'booktitle' => 'booktitle',
        'journal' => 'journal',
        'journalAbbr' => 'journal_abbr',
        'edition' => 'edition',
        'volume' => 'volume',
        'publisher' => 'publisher',
        'address' => 'address',
        'chapter' => 'chapter',
        'series' => 'series',
        'howpublished' => 'howpublished',
        'editor' => 'editor',
        'pages' => 'pages',
        'affiliation' => 'affiliation',
        'extern' => 'extern',
        'eventName' => 'event_name',
        'eventPlace' => 'event_place',
        'number' => 'number',
        'number2' => 'number2',
        'keywords' => 'keywords',
        'tags' => 'tags',
        'webUrl' => 'web_url',
        'webUrl2' => 'web_url2',
        'webUrlDate' => 'web_url_date',
        'fileUrl' => 'file_url',
        'note' => 'note',
        'annotation' => 'annotation',
        'miscellaneous' => 'misc',
        'miscellaneous2' => 'misc2',
        'inLibrary' => 'in_library',
        'borrowedBy' => 'borrowed_by'
    ];

    /**
     * @var array
     */
    protected array $propertiesMappingBib = [
        'authorsForBibExport' => 'author'
    ];

    /**
     * @var array
     */
    protected array $propertiesMappingXml = [
        'bibtype' => 'bibtype',
        'citeid' => 'citeid'
    ];

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Publication
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getAbstract(): string
    {
        return $this->abstract;
    }

    /**
     * @param string $abstract
     * @return Publication
     */
    public function setAbstract(string $abstract): self
    {
        $this->abstract = $abstract;
        return $this;
    }

    /**
     * @return string
     */
    public function getBibtype(): string
    {
        return $this->bibtype;
    }

    /**
     * @param string $bibtype
     * @return Publication
     */
    public function setBibtype(string $bibtype): self
    {
        $this->bibtype = $bibtype;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Publication
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     * @return Publication
     */
    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getYear(): string
    {
        return $this->year;
    }

    /**
     * @param string $year
     * @return Publication
     */
    public function setYear(string $year): self
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return string
     */
    public function getMonth(): string
    {
        return $this->month;
    }

    /**
     * @param string $month
     * @return Publication
     */
    public function setMonth(string $month): self
    {
        $this->month = $month;
        return $this;
    }

    /**
     * @return string
     */
    public function getDay(): string
    {
        return $this->day;
    }

    /**
     * @param string $day
     * @return Publication
     */
    public function setDay(string $day): self
    {
        $this->day = $day;
        return $this;
    }

    /**
     * Always return a (virtual) publication date calculated by year, month and day (even if some of them are empty)
     *
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        $day = (int)$this->getDay();
        if ($day === 0) {
            $day = 1;
        }
        $month = (int)$this->getMonth();
        if ($month === 0) {
            $month = 1;
        }
        $year = (int)$this->getYear();
        if ($year === 0) {
            $year = date('Y');
        }
        $date = \DateTime::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $day);
        if (is_a($date, \DateTime::class) === false) {
            throw new \LogicException(
                'DateTime could not be calculated for publication with uid ' . $this->getUid(),
                1570545740
            );
        }
        $date->modify('midnight');
        return $date;
    }

    /**
     * @return bool
     */
    public function isReviewed(): bool
    {
        return $this->reviewed;
    }

    /**
     * @param bool $reviewed
     * @return Publication
     */
    public function setReviewed(bool $reviewed): self
    {
        $this->reviewed = $reviewed;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     * @return Publication
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return string
     */
    public function getCiteid(): string
    {
        return $this->citeid;
    }

    /**
     * Always get a citeid for export (to not break the bibtex files)
     *
     * @return string
     */
    public function getCiteidForExport(): string
    {
        $citeid = $this->getCiteid();
        if (empty($citeid)) {
            $citeid = (string)rand(100000000000, 999999999999) . '_' . $this->getYear();
        }
        return $citeid;
    }

    /**
     * @param string $citeid
     * @return Publication
     */
    public function setCiteid(string $citeid): self
    {
        $this->citeid = $citeid;
        return $this;
    }

    /**
     * @return string
     */
    public function getIsbn(): string
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     * @return Publication
     */
    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;
        return $this;
    }

    /**
     * @return string
     */
    public function getIssn(): string
    {
        return $this->issn;
    }

    /**
     * @param string $issn
     * @return Publication
     */
    public function setIssn(string $issn): self
    {
        $this->issn = $issn;
        return $this;
    }

    /**
     * @return string
     */
    public function getDoi(): string
    {
        return $this->doi;
    }

    /**
     * @param string $doi
     * @return Publication
     */
    public function setDoi(string $doi): self
    {
        $this->doi = $doi;
        return $this;
    }

    /**
     * @return string
     */
    public function getPmid(): string
    {
        return $this->pmid;
    }

    /**
     * @param string $pmid
     * @return Publication
     */
    public function setPmid(string $pmid): self
    {
        $this->pmid = $pmid;
        return $this;
    }

    /**
     * @return string
     */
    public function getOrganization(): string
    {
        return $this->organization;
    }

    /**
     * @param string $organization
     * @return Publication
     */
    public function setOrganization(string $organization): self
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * @return string
     */
    public function getSchool(): string
    {
        return $this->school;
    }

    /**
     * @param string $school
     * @return Publication
     */
    public function setSchool(string $school): self
    {
        $this->school = $school;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstitution(): string
    {
        return $this->institution;
    }

    /**
     * @param string $institution
     * @return Publication
     */
    public function setInstitution(string $institution): self
    {
        $this->institution = $institution;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstitute(): string
    {
        return $this->institute;
    }

    /**
     * @param string $institute
     * @return Publication
     */
    public function setInstitute(string $institute): self
    {
        $this->institute = $institute;
        return $this;
    }

    /**
     * @return string
     */
    public function getJournal(): string
    {
        return $this->journal;
    }

    /**
     * @return string
     */
    public function getJournalAbbr(): string
    {
        return $this->journalAbbr;
    }

    /**
     * @return string
     */
    public function getBooktitle(): string
    {
        return $this->booktitle;
    }

    /**
     * @param string $booktitle
     * @return Publication
     */
    public function setBooktitle(string $booktitle): self
    {
        $this->booktitle = $booktitle;
        return $this;
    }

    /**
     * @param string $journal
     * @return Publication
     */
    public function setJournal(string $journal): self
    {
        $this->journal = $journal;
        return $this;
    }

    /**
     * @param string $journalAbbr
     * @return Publication
     */
    public function setJournalAbbr(string $journalAbbr): self
    {
        $this->journalAbbr = $journalAbbr;
        return $this;
    }

    /**
     * @return string
     */
    public function getEdition(): string
    {
        return $this->edition;
    }

    /**
     * @param string $edition
     * @return Publication
     */
    public function setEdition(string $edition): self
    {
        $this->edition = $edition;
        return $this;
    }

    /**
     * @return string
     */
    public function getVolume(): string
    {
        return $this->volume;
    }

    /**
     * @param string $volume
     * @return Publication
     */
    public function setVolume(string $volume): self
    {
        $this->volume = $volume;
        return $this;
    }

    /**
     * @return string
     */
    public function getPublisher(): string
    {
        return $this->publisher;
    }

    /**
     * @param string $publisher
     * @return Publication
     */
    public function setPublisher(string $publisher): self
    {
        $this->publisher = $publisher;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     * @return Publication
     */
    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string
     */
    public function getChapter(): string
    {
        return $this->chapter;
    }

    /**
     * @param string $chapter
     * @return Publication
     */
    public function setChapter(string $chapter): self
    {
        $this->chapter = $chapter;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeries(): string
    {
        return $this->series;
    }

    /**
     * @param string $series
     * @return Publication
     */
    public function setSeries(string $series): self
    {
        $this->series = $series;
        return $this;
    }

    /**
     * @return string
     */
    public function getHowpublished(): string
    {
        return $this->howpublished;
    }

    /**
     * @param string $howpublished
     * @return Publication
     */
    public function setHowpublished(string $howpublished): self
    {
        $this->howpublished = $howpublished;
        return $this;
    }

    /**
     * @return string
     */
    public function getEditor(): string
    {
        return $this->editor;
    }

    /**
     * @param string $editor
     * @return Publication
     */
    public function setEditor(string $editor): self
    {
        $this->editor = $editor;
        return $this;
    }

    /**
     * @return string
     */
    public function getPages(): string
    {
        return $this->pages;
    }

    /**
     * @param string $pages
     * @return Publication
     */
    public function setPages(string $pages): self
    {
        $this->pages = $pages;
        return $this;
    }

    /**
     * @return string
     */
    public function getAffiliation(): string
    {
        return $this->affiliation;
    }

    /**
     * @param string $affiliation
     * @return Publication
     */
    public function setAffiliation(string $affiliation): self
    {
        $this->affiliation = $affiliation;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExtern(): bool
    {
        return $this->extern;
    }

    /**
     * @param bool $extern
     * @return Publication
     */
    public function setExtern(bool $extern): self
    {
        $this->extern = $extern;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventName(): string
    {
        return $this->eventName;
    }

    /**
     * @param string $eventName
     * @return Publication
     */
    public function setEventName(string $eventName): self
    {
        $this->eventName = $eventName;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventPlace(): string
    {
        return $this->eventPlace;
    }

    /**
     * @param string $eventPlace
     * @return Publication
     */
    public function setEventPlace(string $eventPlace): self
    {
        $this->eventPlace = $eventPlace;
        return $this;
    }

    /**
     * @return string
     */
    public function getEventDate(): string
    {
        return $this->eventDate;
    }

    /**
     * @param string $eventDate
     * @return Publication
     */
    public function setEventDate(string $eventDate): self
    {
        $this->eventDate = $eventDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        // @extensionScannerIgnoreLine
        return $this->number;
    }

    /**
     * @param string $number
     * @return Publication
     */
    public function setNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return string
     */
    public function getNumber2(): string
    {
        return $this->number2;
    }

    /**
     * @param string $number2
     * @return Publication
     */
    public function setNumber2(string $number2): self
    {
        $this->number2 = $number2;
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getAuthors(): ObjectStorage
    {
        return $this->authors;
    }

    /**
     * Return author export string like "Kellner, Alexander and Pohl, Sandra"
     *
     * @return string
     */
    public function getAuthorsForBibExport(): string
    {
        $authors = $this->getAuthors();
        $names = [];
        foreach ($authors as $author) {
            $name = $author->getLastName();
            if ($author->getLastName() !== '' && $author->getFirstName() !== '') {
                $name .= ', ';
            }
            $name .= $author->getFirstName();
            $names[] = $name;
        }
        return implode(' and ', $names);
    }

    /**
     * @param ObjectStorage $authors
     * @return Publication
     */
    public function setAuthors(ObjectStorage $authors): self
    {
        $this->authors = $authors;
        return $this;
    }

    /**
     * @return string
     */
    public function getKeywords(): string
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     * @return Publication
     */
    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * @return string
     */
    public function getTags(): string
    {
        return $this->tags;
    }

    /**
     * @param string $tags
     * @return Publication
     */
    public function setTags(string $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebUrl(): string
    {
        return $this->webUrl;
    }

    /**
     * @param string $webUrl
     * @return Publication
     */
    public function setWebUrl(string $webUrl): self
    {
        $this->webUrl = $webUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebUrl2(): string
    {
        return $this->webUrl2;
    }

    /**
     * @param string $webUrl2
     * @return Publication
     */
    public function setWebUrl2(string $webUrl2): self
    {
        $this->webUrl2 = $webUrl2;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebUrlDate(): string
    {
        return $this->webUrlDate;
    }

    /**
     * @param string $webUrlDate
     * @return Publication
     */
    public function setWebUrlDate(string $webUrlDate): self
    {
        $this->webUrlDate = $webUrlDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getFileUrl(): string
    {
        return $this->fileUrl;
    }

    /**
     * @param string $fileUrl
     * @return Publication
     */
    public function setFileUrl(string $fileUrl): self
    {
        $this->fileUrl = $fileUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @param string $note
     * @return Publication
     */
    public function setNote(string $note): self
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @return string
     */
    public function getAnnotation(): string
    {
        return $this->annotation;
    }

    /**
     * @param string $annotation
     * @return Publication
     */
    public function setAnnotation(string $annotation): self
    {
        $this->annotation = $annotation;
        return $this;
    }

    /**
     * @return string
     */
    public function getMiscellaneous(): string
    {
        return $this->miscellaneous;
    }

    /**
     * @param string $miscellaneous
     * @return Publication
     */
    public function setMiscellaneous(string $miscellaneous): self
    {
        $this->miscellaneous = $miscellaneous;
        return $this;
    }

    /**
     * @return string
     */
    public function getMiscellaneous2(): string
    {
        return $this->miscellaneous2;
    }

    /**
     * @param string $miscellaneous2
     * @return Publication
     */
    public function setMiscellaneous2(string $miscellaneous2): self
    {
        $this->miscellaneous2 = $miscellaneous2;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInLibrary(): bool
    {
        return $this->inLibrary;
    }

    /**
     * @param bool $inLibrary
     * @return Publication
     */
    public function setInLibrary(bool $inLibrary): self
    {
        $this->inLibrary = $inLibrary;
        return $this;
    }

    /**
     * @return string
     */
    public function getBorrowedBy(): string
    {
        return $this->borrowedBy;
    }

    /**
     * @param string $borrowedBy
     * @return Publication
     */
    public function setBorrowedBy(string $borrowedBy): self
    {
        $this->borrowedBy = $borrowedBy;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumeration(): int
    {
        return $this->_numeration;
    }

    /**
     * @param int $numeration
     * @return Publication
     */
    public function setNumeration(int $numeration): self
    {
        $this->_numeration = $numeration;
        return $this;
    }

    /**
     * @return array
     */
    public function getPropertiesForBibExport(): array
    {
        $mapping = $this->propertiesMappingBib + $this->propertiesMapping;
        return $this->getPropertiesForExport($mapping);
    }

    /**
     * @return array
     */
    public function getPropertiesForXmlExport(): array
    {
        $mapping = $this->propertiesMappingXml + $this->propertiesMapping;
        return $this->getPropertiesForExport($mapping);
    }

    /**
     * @param array $mapping
     * @return array
     */
    protected function getPropertiesForExport(array $mapping): array
    {
        $properties = [];
        foreach ($mapping as $property => $field) {
            try {
                $value = ObjectAccess::getProperty($this, $property);
            } catch (\Exception $exception) {
                throw new \LogicException($exception->getMessage(), 1565809183);
            }
            if (!empty($value)) {
                $properties[$field] = (string)$value;
            }
        }
        return $properties;
    }
}
