<?php

declare(strict_types=1);

namespace In2code\Publications\Domain\Model\Dto;

use In2code\Publications\Domain\Model\Author;
use In2code\Publications\Domain\Repository\AuthorRepository;
use In2code\Publications\Utility\ObjectUtility;
use In2code\Publications\Utility\PageUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Class Filter
 */
class Filter
{
    public const GROUP_BY_NONE = -1;
    public const GROUP_BY_YEAR = 0;
    public const GROUP_BY_TYPE = 1;
    public const GROUP_BY_YEAR_AND_TYPE = 2;

    /**
     * @var int
     */
    protected int $citestyle = 0;

    /**
     * @var int
     */
    protected int $groupby = self::GROUP_BY_YEAR;

    /**
     * @var int
     */
    protected int $recordsPerPage = 25;

    /**
     * @var int
     */
    protected int $timeframe = 0;

    /**
     * @var array
     */
    protected array $bibtypes = [];

    /**
     * @var int[]
     */
    protected array $status = [];

    /**
     * @var array
     */
    protected array $keywords = [];

    /**
     * @var array
     */
    protected array $tags = [];

    /**
     * Commaseparated identifiers of author objects
     *
     * @var string "123,345,678"
     */
    protected string $author = '';

    /**
     * @var int 0=off, 1=intern, 2=extern
     */
    protected int $externFilter = 0;

    /**
     * @var int 0=off, 1=intern, 2=extern
     */
    protected int $reviewFilter = 0;

    /**
     * @var array
     */
    protected array $records = [];

    /**
     * @var string
     */
    protected string $searchterm = '';

    /**
     * @var string
     */
    protected string $documenttype = 'all';

    /**
     * @var int
     */
    protected int $year = 0;

    /**
     * @var string
     */
    protected string $authorstring = '';

    /**
     * @var array
     */
    protected array $export = [];

    /**
     * @var int recursive level
     */
    protected int $recursive = 0;

    /**
     * Filter constructor.
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->setCitestyle((int)$settings['citestyle']);
        $this->setGroupby((int)$settings['groupby']);
        $this->setRecordsPerPage((int)$settings['recordsPerPage']);
        $this->setTimeframe((int)$settings['timeframe']);
        $this->setBibtypes(GeneralUtility::trimExplode(',', $settings['bibtypes'], true));
        $this->setStatus(GeneralUtility::intExplode(',', $settings['status'], true));
        $this->setKeywords(GeneralUtility::trimExplode(PHP_EOL, $settings['keywords'], true));
        $this->setTags(GeneralUtility::trimExplode(PHP_EOL, $settings['tags'], true));
        $this->setAuthor($settings['author'] ?? '');
        $this->setExternFilter((int)$settings['extern']);
        $this->setReviewFilter((int)$settings['review']);
        $this->setRecursive((int)$settings['recursive']);
        $this->setRecords(GeneralUtility::intExplode(',', $settings['records'], true));
        $this->setExport(GeneralUtility::intExplode(',', $settings['export'], true));
    }

    /**
     * @return int
     */
    public function getCitestyle(): int
    {
        return $this->citestyle;
    }

    /**
     * @return bool
     */
    public function isCitestyleSet(): bool
    {
        return $this->getCitestyle() !== 0;
    }

    /**
     * @param int $citestyle
     * @return Filter
     */
    public function setCitestyle(int $citestyle): self
    {
        $this->citestyle = $citestyle;
        return $this;
    }

    /**
     * @return int
     */
    public function getGroupby(): int
    {
        return $this->groupby;
    }

    /**
     * @return array
     */
    public function getGroupByArrayForQuery(): array
    {
        switch ($this->getGroupby()) {
            case 0:
                $orderings = [
                    'year' => QueryInterface::ORDER_DESCENDING,
                    'title' => QueryInterface::ORDER_ASCENDING
                ];
                break;
            case 1:
                $orderings = [
                    'bibtype' => QueryInterface::ORDER_ASCENDING,
                    'title' => QueryInterface::ORDER_ASCENDING
                ];
                break;
            default:
            case 2:
                $orderings = [
                    'year' => QueryInterface::ORDER_DESCENDING,
                    'bibtype' => QueryInterface::ORDER_ASCENDING,
                    'title' => QueryInterface::ORDER_ASCENDING
                ];
        }
        return $orderings;
    }

    /**
     * @return bool
     */
    public function isGroupbySet(): bool
    {
        return $this->getGroupby() !== 0;
    }

    /**
     * @param int $groupby
     * @return Filter
     */
    public function setGroupby(int $groupby): self
    {
        $this->groupby = $groupby;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecordsPerPage(): int
    {
        return $this->recordsPerPage;
    }

    /**
     * @return bool
     */
    public function isRecordsPerPageSet(): bool
    {
        return $this->getRecordsPerPage() !== 25;
    }

    /**
     * @param int $recordsPerPage
     * @return Filter
     */
    public function setRecordsPerPage(int $recordsPerPage): self
    {
        $this->recordsPerPage = $recordsPerPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeframe(): int
    {
        return $this->timeframe;
    }

    /**
     * @return bool
     */
    public function isTimeFrameSet(): bool
    {
        return $this->getTimeframe() !== 0;
    }

    /**
     * @return \DateTime
     */
    public function getDateFromTimeFrame(): ?\DateTime
    {
        $date = null;
        try {
            $date = new \DateTime();
            if ($this->getTimeframe() > 0) {
                $date->modify('- ' . $this->getTimeframe() . ' years');
            }
        } catch (\Exception $exception) {
        }
        return $date;
    }

    /**
     * @param int $timeframe
     * @return Filter
     */
    public function setTimeframe(int $timeframe): self
    {
        $this->timeframe = $timeframe;
        return $this;
    }

    /**
     * @return array
     */
    public function getBibtypes(): array
    {
        return $this->bibtypes;
    }

    /**
     * @return bool
     */
    public function isBibtypesSet(): bool
    {
        return $this->getBibtypes() !== [];
    }

    /**
     * @param array $bibtypes
     * @return Filter
     */
    public function setBibtypes(array $bibtypes): self
    {
        $this->bibtypes = $bibtypes;
        return $this;
    }

    /**
     * @return array
     */
    public function getStatus(): array
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isStatusSet(): bool
    {
        return $this->getStatus() !== [];
    }

    /**
     * @param array $status
     * @return Filter
     */
    public function setStatus(array $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return array
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * @return bool
     */
    public function isKeywordsSet(): bool
    {
        return $this->getKeywords() !== [];
    }

    /**
     * @param array $keywords
     * @return Filter
     */
    public function setKeywords(array $keywords): self
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @return bool
     */
    public function isTagsSet(): bool
    {
        return $this->getTags() !== [];
    }

    /**
     * @param array $tags
     * @return Filter
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return Author[]
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public function getAuthors(): array
    {
        $authors = [];
        if ($this->isAuthorSet()) {
            $authorRepository = ObjectUtility::getObjectManager()->get(AuthorRepository::class);
            foreach (GeneralUtility::intExplode(',', $this->getAuthor(), true) as $identifier) {
                $authors[] = $authorRepository->findByUid($identifier);
            }
        }
        return $authors;
    }

    /**
     * @return bool
     */
    public function isAuthorSet(): bool
    {
        return $this->getAuthor() !== '';
    }

    /**
     * @param string $author
     * @return Filter
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return int
     */
    public function getExternFilter(): int
    {
        return $this->externFilter;
    }

    /**
     * @return bool
     */
    public function isExternOrIntern(): bool
    {
        return $this->isIntern() || $this->isExtern();
    }

    /**
     * @return bool
     */
    public function isIntern(): bool
    {
        return $this->getExternFilter() === 1;
    }

    /**
     * @return bool
     */
    public function isExtern(): bool
    {
        return $this->getExternFilter() === 2;
    }

    /**
     * @param int $externFilter
     * @return Filter
     */
    public function setExternFilter(int $externFilter): self
    {
        $this->externFilter = $externFilter;
        return $this;
    }

    /**
     * @return int
     */
    public function getReviewFilter(): int
    {
        return $this->reviewFilter;
    }

    /**
     * @return bool
     */
    public function isReviewed(): bool
    {
        return $this->isReview() || $this->isNotReview();
    }

    /**
     * @return bool
     */
    public function isReview(): bool
    {
        return $this->getReviewFilter() === 1;
    }

    /**
     * @return bool
     */
    public function isNotReview(): bool
    {
        return $this->getReviewFilter() === 2;
    }

    /**
     * @param int $reviewFilter
     * @return Filter
     */
    public function setReviewFilter(int $reviewFilter): self
    {
        $this->reviewFilter = $reviewFilter;
        return $this;
    }

    /**
     * @return array
     */
    public function getRecords(): array
    {
        return $this->records;
    }

    /**
     * @return bool
     */
    public function isRecordsSet(): bool
    {
        return $this->getRecords() !== [];
    }

    /**
     * @param array $records
     * @return Filter
     */
    public function setRecords(array $records): self
    {
        $this->records = PageUtility::extendPidListByChildren($records, $this->getRecursive());
        return $this;
    }

    /**
     * @return string
     */
    public function getSearchterm(): string
    {
        return $this->searchterm;
    }

    /**
     * @return bool
     */
    public function isSearchtermSet(): bool
    {
        return $this->getSearchterm() !== '';
    }

    /**
     * @return array
     */
    public function getSearchterms(): array
    {
        return GeneralUtility::trimExplode(' ', $this->getSearchterm(), true);
    }

    /**
     * @param string $searchterm
     * @return Filter
     */
    public function setSearchterm(string $searchterm): self
    {
        $this->searchterm = $searchterm;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocumenttype(): string
    {
        return $this->documenttype;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @return bool
     */
    public function isYearSet(): bool
    {
        return $this->getYear() !== 0;
    }

    /**
     * @return \DateTime|null
     */
    public function getYearFrom(): ?\DateTime
    {
        try {
            return new \DateTime('first day of January ' . $this->getYear());
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getYearTo(): ?\DateTime
    {
        try {
            $date = new \DateTime('first day of January ' . ($this->getYear() + 1));
            $date->modify('- 1 minute');
            return $date;
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param string $documenttype
     * @return Filter
     */
    public function setDocumenttype(string $documenttype): self
    {
        $this->documenttype = $documenttype;
        return $this;
    }

    /**
     * @param int $year
     * @return Filter
     */
    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    /**
     * @return string
     */
    public function getAuthorstring(): string
    {
        return $this->authorstring;
    }

    /**
     * @return bool
     */
    public function isDocumenttypeSet(): bool
    {
        return $this->getDocumenttype() !== 'all';
    }

    /**
     * @return bool
     */
    public function isAuthorstringSet(): bool
    {
        return $this->getAuthorstring() !== '';
    }

    /**
     * Split on space and remove comma to allow a search for e.g. "Bernd Aumann, Klaus Fumy"
     *
     * @return array
     */
    public function getAuthorstrings(): array
    {
        $authorstring = str_replace(',', ' ', $this->getAuthorstring());
        return GeneralUtility::trimExplode(' ', $authorstring, true);
    }

    /**
     * @param string $authorstring
     * @return Filter
     */
    public function setAuthorstring(string $authorstring): self
    {
        $this->authorstring = $authorstring;
        return $this;
    }

    /**
     * @return array
     */
    public function getExport(): array
    {
        return $this->export;
    }

    /**
     * @param array $export
     * @return Filter
     */
    public function setExport(array $export): self
    {
        $this->export = $export;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecursive(): int
    {
        return $this->recursive;
    }

    /**
     * @param int $recursive
     * @return Filter
     */
    public function setRecursive(int $recursive): self
    {
        $this->recursive = $recursive;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFilterFlexFormSet(): bool
    {
        return $this->isCitestyleSet()
            || $this->isGroupbySet()
            || $this->isRecordsPerPageSet()
            || $this->isTimeFrameSet()
            || $this->isBibtypesSet()
            || $this->isStatusSet()
            || $this->isKeywordsSet()
            || $this->isTagsSet()
            || $this->isAuthorSet()
            || $this->isRecordsSet();
    }

    /**
     * @return bool
     */
    public function isFilterFrontendSet(): bool
    {
        return $this->isSearchtermSet()
            || $this->isYearSet()
            || $this->isAuthorstringSet()
            || $this->isDocumenttypeSet();
    }
}
