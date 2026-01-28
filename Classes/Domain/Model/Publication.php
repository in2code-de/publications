<?php

declare(strict_types=1);

namespace In2code\Publications\Domain\Model;

use DateTime;
use Exception;
use LogicException;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

class Publication extends AbstractEntity
{
    public const TABLE_NAME = 'tx_publications_domain_model_publication';

    protected string $title = '';
    protected string $abstract = '';
    protected string $bibtype = '';
    protected string $type = '';
    protected int $status = 0;
    protected string $year = '';
    protected string $month = '';
    protected string $day = '';
    protected bool $reviewed = false;
    protected string $language = '';
    protected string $citeid = '';
    protected string $isbn = '';
    protected string $issn = '';
    protected string $doi = '';
    protected string $pmid = '';
    protected string $organization = '';
    protected string $school = '';
    protected string $institution = '';
    protected string $institute = '';
    protected string $booktitle = '';
    protected string $journal = '';
    protected string $journalAbbr = '';
    protected string $edition = '';
    protected string $volume = '';
    protected string $publisher = '';
    protected string $address = '';
    protected string $chapter = '';
    protected string $series = '';
    protected string $howpublished = '';
    protected string $editor = '';
    protected string $pages = '';
    protected string $affiliation = '';
    protected bool $extern = false;
    protected string $eventName = '';
    protected string $eventPlace = '';
    protected string $eventDate = '';
    protected string $number = '';
    protected string $number2 = '';

    /**
     * @var ObjectStorage<Author>
     */
    protected ObjectStorage $authors;
    protected string $keywords = '';
    protected string $tags = '';
    protected string $webUrl = '';
    protected string $webUrl2 = '';
    protected string $webUrlDate = '';
    protected string $fileUrl = '';
    protected string $note = '';
    protected string $annotation = '';
    protected string $miscellaneous = '';
    protected string $miscellaneous2 = '';
    protected bool $inLibrary = false;
    /**
     * @TYPO3\CMS\Extbase\Annotation\ORM\Transient
     */
    protected int $_numeration = 0;
    protected string $borrowedBy = '';

    /**
     * Basic mapping for any export. Which fields should be available in export files with which key.
     * ModelProperty => KeyNameInExport
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
    protected array $propertiesMappingBib = [
        'authorsForBibExport' => 'author'
    ];
    protected array $propertiesMappingXml = [
        'bibtype' => 'bibtype',
        'citeid' => 'citeid'
    ];

    public function __construct()
    {
        $this->initializeObject();
    }

    public function initializeObject(): void
    {
        $this->authors = new ObjectStorage();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getAbstract(): string
    {
        return $this->abstract;
    }

    public function setAbstract(string $abstract): self
    {
        $this->abstract = $abstract;
        return $this;
    }

    public function getBibtype(): string
    {
        return $this->bibtype;
    }

    public function setBibtype(string $bibtype): self
    {
        $this->bibtype = $bibtype;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getYear(): string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getMonth(): string
    {
        if (!is_numeric($this->month)) {
            return (string)(date_parse($this->month)['month'] ?: '');
        }
        return (string)((int)$this->month);
    }

    public function setMonth(string $month): self
    {
        $this->month = $month;
        return $this;
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public function setDay(string $day): self
    {
        $this->day = $day;
        return $this;
    }

    /**
     * Always return a (virtual) publication date calculated by year, month and day (even if some of them are empty)
     */
    public function getDate(): DateTime
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
        $date = DateTime::createFromFormat('Y-m-d', $year . '-' . $month . '-' . $day);
        if (is_a($date, DateTime::class) === false) {
            throw new LogicException(
                'DateTime could not be calculated for publication with uid ' . $this->getUid(),
                1570545740
            );
        }
        $date->modify('midnight');
        return $date;
    }

    public function isReviewed(): bool
    {
        return $this->reviewed;
    }

    public function setReviewed(bool $reviewed): self
    {
        $this->reviewed = $reviewed;
        return $this;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function setLanguage(string $language): self
    {
        $this->language = $language;
        return $this;
    }

    public function getCiteid(): string
    {
        return $this->citeid;
    }

    /**
     * Always get a citeid for export (to not break the bibtex files)
     */
    public function getCiteidForExport(): string
    {
        $citeid = $this->getCiteid();
        if (empty($citeid)) {
            $citeid = (string)rand(100000000000, 999999999999) . '_' . $this->getYear();
        }
        return $citeid;
    }

    public function setCiteid(string $citeid): self
    {
        $this->citeid = $citeid;
        return $this;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getIssn(): string
    {
        return $this->issn;
    }

    public function setIssn(string $issn): self
    {
        $this->issn = $issn;
        return $this;
    }

    public function getDoi(): string
    {
        return $this->doi;
    }

    public function setDoi(string $doi): self
    {
        $this->doi = $doi;
        return $this;
    }

    public function getPmid(): string
    {
        return $this->pmid;
    }

    public function setPmid(string $pmid): self
    {
        $this->pmid = $pmid;
        return $this;
    }

    public function getOrganization(): string
    {
        return $this->organization;
    }

    public function setOrganization(string $organization): self
    {
        $this->organization = $organization;
        return $this;
    }

    public function getSchool(): string
    {
        return $this->school;
    }

    public function setSchool(string $school): self
    {
        $this->school = $school;
        return $this;
    }

    public function getInstitution(): string
    {
        return $this->institution;
    }

    public function setInstitution(string $institution): self
    {
        $this->institution = $institution;
        return $this;
    }

    public function getInstitute(): string
    {
        return $this->institute;
    }

    public function setInstitute(string $institute): self
    {
        $this->institute = $institute;
        return $this;
    }

    public function getJournal(): string
    {
        return $this->journal;
    }

    public function getJournalAbbr(): string
    {
        return $this->journalAbbr;
    }

    public function getBooktitle(): string
    {
        return $this->booktitle;
    }

    public function setBooktitle(string $booktitle): self
    {
        $this->booktitle = $booktitle;
        return $this;
    }

    public function setJournal(string $journal): self
    {
        $this->journal = $journal;
        return $this;
    }

    public function setJournalAbbr(string $journalAbbr): self
    {
        $this->journalAbbr = $journalAbbr;
        return $this;
    }

    public function getEdition(): string
    {
        return $this->edition;
    }

    public function setEdition(string $edition): self
    {
        $this->edition = $edition;
        return $this;
    }

    public function getVolume(): string
    {
        return $this->volume;
    }

    public function setVolume(string $volume): self
    {
        $this->volume = $volume;
        return $this;
    }

    public function getPublisher(): string
    {
        return $this->publisher;
    }

    public function setPublisher(string $publisher): self
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getChapter(): string
    {
        return $this->chapter;
    }

    public function setChapter(string $chapter): self
    {
        $this->chapter = $chapter;
        return $this;
    }

    public function getSeries(): string
    {
        return $this->series;
    }

    public function setSeries(string $series): self
    {
        $this->series = $series;
        return $this;
    }

    public function getHowpublished(): string
    {
        return $this->howpublished;
    }

    public function setHowpublished(string $howpublished): self
    {
        $this->howpublished = $howpublished;
        return $this;
    }

    public function getEditor(): string
    {
        return $this->editor;
    }

    public function setEditor(string $editor): self
    {
        $this->editor = $editor;
        return $this;
    }

    public function getPages(): string
    {
        return $this->pages;
    }

    public function setPages(string $pages): self
    {
        $this->pages = $pages;
        return $this;
    }

    public function getAffiliation(): string
    {
        return $this->affiliation;
    }

    public function setAffiliation(string $affiliation): self
    {
        $this->affiliation = $affiliation;
        return $this;
    }

    public function isExtern(): bool
    {
        return $this->extern;
    }

    public function setExtern(bool $extern): self
    {
        $this->extern = $extern;
        return $this;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): self
    {
        $this->eventName = $eventName;
        return $this;
    }

    public function getEventPlace(): string
    {
        return $this->eventPlace;
    }

    public function setEventPlace(string $eventPlace): self
    {
        $this->eventPlace = $eventPlace;
        return $this;
    }

    public function getEventDate(): string
    {
        return $this->eventDate;
    }

    public function setEventDate(string $eventDate): self
    {
        $this->eventDate = $eventDate;
        return $this;
    }

    public function getNumber(): string
    {
        // @extensionScannerIgnoreLine
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getNumber2(): string
    {
        return $this->number2;
    }

    public function setNumber2(string $number2): self
    {
        $this->number2 = $number2;
        return $this;
    }

    public function getAuthors(): ObjectStorage
    {
        return $this->authors;
    }

    /**
     * Return author export string like "Kellner, Alexander and Pohl, Sandra"
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

    public function setAuthors(ObjectStorage $authors): self
    {
        $this->authors = $authors;
        return $this;
    }

    public function getKeywords(): string
    {
        return $this->keywords;
    }

    public function setKeywords(string $keywords): self
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function getTags(): string
    {
        return $this->tags;
    }

    public function setTags(string $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getWebUrl(): string
    {
        return $this->webUrl;
    }

    public function setWebUrl(string $webUrl): self
    {
        $this->webUrl = $webUrl;
        return $this;
    }

    public function getWebUrl2(): string
    {
        return $this->webUrl2;
    }

    public function setWebUrl2(string $webUrl2): self
    {
        $this->webUrl2 = $webUrl2;
        return $this;
    }

    public function getWebUrlDate(): string
    {
        return $this->webUrlDate;
    }

    public function setWebUrlDate(string $webUrlDate): self
    {
        $this->webUrlDate = $webUrlDate;
        return $this;
    }

    public function getFileUrl(): string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(string $fileUrl): self
    {
        $this->fileUrl = $fileUrl;
        return $this;
    }

    public function getNote(): string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getAnnotation(): string
    {
        return $this->annotation;
    }

    public function setAnnotation(string $annotation): self
    {
        $this->annotation = $annotation;
        return $this;
    }

    public function getMiscellaneous(): string
    {
        return $this->miscellaneous;
    }

    public function setMiscellaneous(string $miscellaneous): self
    {
        $this->miscellaneous = $miscellaneous;
        return $this;
    }

    public function getMiscellaneous2(): string
    {
        return $this->miscellaneous2;
    }

    public function setMiscellaneous2(string $miscellaneous2): self
    {
        $this->miscellaneous2 = $miscellaneous2;
        return $this;
    }

    public function isInLibrary(): bool
    {
        return $this->inLibrary;
    }

    public function setInLibrary(bool $inLibrary): self
    {
        $this->inLibrary = $inLibrary;
        return $this;
    }

    public function getBorrowedBy(): string
    {
        return $this->borrowedBy;
    }

    public function setBorrowedBy(string $borrowedBy): self
    {
        $this->borrowedBy = $borrowedBy;
        return $this;
    }

    public function getNumeration(): int
    {
        return $this->_numeration;
    }

    public function setNumeration(int $numeration): self
    {
        $this->_numeration = $numeration;
        return $this;
    }

    public function getPropertiesForBibExport(): array
    {
        $mapping = $this->propertiesMappingBib + $this->propertiesMapping;
        return $this->getPropertiesForExport($mapping);
    }

    public function getPropertiesForXmlExport(): array
    {
        $mapping = $this->propertiesMappingXml + $this->propertiesMapping;
        return $this->getPropertiesForExport($mapping);
    }

    protected function getPropertiesForExport(array $mapping): array
    {
        $properties = [];
        foreach ($mapping as $property => $field) {
            try {
                $value = ObjectAccess::getProperty($this, $property);
            } catch (Exception $exception) {
                throw new LogicException($exception->getMessage(), 1565809183);
            }
            if (!empty($value)) {
                $properties[$field] = (string)$value;
            }
        }
        return $properties;
    }
}
