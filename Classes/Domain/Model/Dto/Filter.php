<?php

declare(strict_types=1);

namespace In2code\Publications\Domain\Model\Dto;

use DateTime;
use Doctrine\DBAL\Driver\Exception;
use In2code\Publications\Domain\Repository\AuthorRepository;
use In2code\Publications\Utility\PageUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class Filter
{
    public const GROUP_BY_NONE = -1;
    public const GROUP_BY_YEAR = 0;
    public const GROUP_BY_TYPE = 1;
    public const GROUP_BY_YEAR_AND_TYPE = 2;

    protected int $citestyle = 0;
    protected int $groupby = self::GROUP_BY_YEAR;
    protected int $recordsPerPage = 25;
    protected int $timeframe = 0;
    protected array $bibtypes = [];
    protected array $status = [];
    protected array $keywords = [];
    protected array $tags = [];

    /**
     * Comma separated identifiers of author objects
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
    protected array $records = [];
    protected string $searchterm = '';
    protected string $documenttype = 'all';
    protected string $concatination = 'and';
    protected int $year = 0;
    protected string $authorstring = '';
    protected string $authorstring_exact = '';
    protected array $export = [];
    protected int $recursive = 0;
    protected string $filterType = 'classic';
    protected string $title = '';
    protected string $title_exact = '';

    public function __construct(array $settings)
    {
        $this->setCitestyle((int)($settings['citestyle'] ?? 0));
        $this->setGroupby((int)($settings['groupby'] ?? 0));
        $this->setRecordsPerPage((int)($settings['recordsPerPage'] ?? 25));
        $this->setTimeframe((int)($settings['timeframe'] ?? 0));
        $this->setBibtypes(GeneralUtility::trimExplode(',', $settings['bibtypes'] ?? '', true));
        $this->setStatus(GeneralUtility::intExplode(',', $settings['status'] ?? '', true));
        $this->setKeywords(GeneralUtility::trimExplode(PHP_EOL, $settings['keywords'] ?? '', true));
        $this->setTags(GeneralUtility::trimExplode(PHP_EOL, $settings['tags'] ?? '', true));
        $this->setAuthor($settings['author'] ?? '');
        $this->setExternFilter((int)($settings['extern'] ?? 0));
        $this->setReviewFilter((int)($settings['review'] ?? 0));
        $this->setRecursive((int)($settings['recursive'] ?? 0));
        $this->setRecords(GeneralUtility::intExplode(',', $settings['records'] ?? '', true));
        $this->setExport(GeneralUtility::intExplode(',', $settings['export'] ?? '', true));
        $this->setFilterType($settings['filterType'] ?? 'classic');
    }

    public function getCitestyle(): int
    {
        return $this->citestyle;
    }

    public function isCitestyleSet(): bool
    {
        return $this->getCitestyle() !== 0;
    }

    public function setCitestyle(int $citestyle): self
    {
        $this->citestyle = $citestyle;
        return $this;
    }

    public function getGroupby(): int
    {
        return $this->groupby;
    }

    public function getGroupByArrayForQuery(): array
    {
        switch ($this->getGroupby()) {
            case self::GROUP_BY_NONE:
            case self::GROUP_BY_YEAR:
                $orderings = [
                    'year' => QueryInterface::ORDER_DESCENDING,
                    'title' => QueryInterface::ORDER_ASCENDING
                ];
                break;
            case self::GROUP_BY_TYPE:
                $orderings = [
                    'bibtype' => QueryInterface::ORDER_ASCENDING,
                    'title' => QueryInterface::ORDER_ASCENDING
                ];
                break;
            default:
            case self::GROUP_BY_YEAR_AND_TYPE:
                $orderings = [
                    'year' => QueryInterface::ORDER_DESCENDING,
                    'bibtype' => QueryInterface::ORDER_ASCENDING,
                    'title' => QueryInterface::ORDER_ASCENDING
                ];
        }
        return $orderings;
    }

    public function isGroupbySet(): bool
    {
        return $this->getGroupby() !== self::GROUP_BY_NONE;
    }

    public function setGroupby(int $groupby): self
    {
        $this->groupby = $groupby;
        return $this;
    }

    public function getRecordsPerPage(): int
    {
        return $this->recordsPerPage;
    }

    public function isRecordsPerPageSet(): bool
    {
        return $this->getRecordsPerPage() !== 25;
    }

    public function setRecordsPerPage(int $recordsPerPage): self
    {
        $this->recordsPerPage = $recordsPerPage;
        return $this;
    }

    public function getTimeframe(): int
    {
        return $this->timeframe;
    }

    public function isTimeFrameSet(): bool
    {
        return $this->getTimeframe() !== 0;
    }

    public function getDateFromTimeFrame(): ?DateTime
    {
        $date = null;
        try {
            $date = new DateTime();
            if ($this->getTimeframe() > 0) {
                $date->modify('- ' . $this->getTimeframe() . ' years');
            }
        } catch (\Exception $exception) {
        }
        return $date;
    }

    public function setTimeframe(int $timeframe): self
    {
        $this->timeframe = $timeframe;
        return $this;
    }

    public function getBibtypes(): array
    {
        return $this->bibtypes;
    }

    public function isBibtypesSet(): bool
    {
        return $this->getBibtypes() !== [];
    }

    public function setBibtypes(array $bibtypes): self
    {
        $this->bibtypes = $bibtypes;
        return $this;
    }

    public function getStatus(): array
    {
        return $this->status;
    }

    public function isStatusSet(): bool
    {
        return $this->getStatus() !== [];
    }

    public function setStatus(array $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function isKeywordsSet(): bool
    {
        return $this->getKeywords() !== [];
    }

    public function setKeywords(array $keywords): self
    {
        $this->keywords = $keywords;
        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function isTagsSet(): bool
    {
        return $this->getTags() !== [];
    }

    public function setTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getAuthors(): array
    {
        $authors = [];
        if ($this->isAuthorSet()) {
            $authorRepository = GeneralUtility::makeInstance(AuthorRepository::class);
            foreach (GeneralUtility::intExplode(',', $this->getAuthor(), true) as $identifier) {
                $authors[] = $authorRepository->findByUid($identifier);
            }
        }
        return $authors;
    }

    public function isAuthorSet(): bool
    {
        return $this->getAuthor() !== '';
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getExternFilter(): int
    {
        return $this->externFilter;
    }

    public function isExternOrIntern(): bool
    {
        return $this->isIntern() || $this->isExtern();
    }

    public function isIntern(): bool
    {
        return $this->getExternFilter() === 1;
    }

    public function isExtern(): bool
    {
        return $this->getExternFilter() === 2;
    }

    public function setExternFilter(int $externFilter): self
    {
        $this->externFilter = $externFilter;
        return $this;
    }

    public function getReviewFilter(): int
    {
        return $this->reviewFilter;
    }

    public function isReviewed(): bool
    {
        return $this->isReview() || $this->isNotReview();
    }

    public function isReview(): bool
    {
        return $this->getReviewFilter() === 1;
    }

    public function isNotReview(): bool
    {
        return $this->getReviewFilter() === 2;
    }

    public function setReviewFilter(int $reviewFilter): self
    {
        $this->reviewFilter = $reviewFilter;
        return $this;
    }

    public function getRecords(): array
    {
        return $this->records;
    }

    public function isRecordsSet(): bool
    {
        return $this->getRecords() !== [];
    }

    /**
     * @throws Exception
     */
    public function setRecords(array $records): self
    {
        $this->records = PageUtility::extendPidListByChildren($records, $this->getRecursive());
        return $this;
    }

    public function getSearchterm(): string
    {
        return $this->searchterm;
    }

    public function isSearchtermSet(): bool
    {
        return $this->getSearchterm() !== '';
    }

    public function getSearchterms(): array
    {
        return GeneralUtility::trimExplode(' ', $this->getSearchterm(), true);
    }

    public function setSearchterm(string $searchterm): self
    {
        $this->searchterm = $searchterm;
        return $this;
    }

    public function getDocumenttype(): string
    {
        return $this->documenttype;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function isYearSet(): bool
    {
        return $this->getYear() !== 0;
    }

    public function getYearFrom(): ?DateTime
    {
        try {
            return new DateTime('first day of January ' . $this->getYear());
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function getYearTo(): ?DateTime
    {
        try {
            $date = new DateTime('first day of January ' . ($this->getYear() + 1));
            $date->modify('- 1 minute');
            return $date;
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function setDocumenttype(string $documenttype): self
    {
        $this->documenttype = $documenttype;
        return $this;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getAuthorstring(): string
    {
        return $this->authorstring;
    }

    public function isDocumenttypeSet(): bool
    {
        return $this->getDocumenttype() !== 'all';
    }

    public function isAuthorstringSet(): bool
    {
        return $this->getAuthorstring() !== '';
    }

    /**
     * Split on space and remove comma to allow a search for e.g. "Bernd Aumann, Klaus Fumy"
     */
    public function getAuthorstrings(): array
    {
        $authorstring = str_replace(',', ' ', $this->getAuthorstring());
        return GeneralUtility::trimExplode(' ', $authorstring, true);
    }

    public function setAuthorstring(string $authorstring): self
    {
        $this->authorstring = $authorstring;
        return $this;
    }

    public function getExport(): array
    {
        return $this->export;
    }

    public function setExport(array $export): self
    {
        $this->export = $export;
        return $this;
    }

    public function setFilterType(string $filterType): self
    {
        $this->filterType = $filterType;
        return $this;
    }

    public function getFilterType(): string
    {
        return $this->filterType;
    }

    public function getRecursive(): int
    {
        return $this->recursive;
    }

    public function setRecursive(int $recursive): self
    {
        $this->recursive = $recursive;
        return $this;
    }

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

    public function isFilterFrontendSet(): bool
    {
        return $this->isSearchtermSet()
            || $this->isYearSet()
            || $this->isAuthorstringSet()
            || $this->isDocumenttypeSet()
            || $this->isTitleSet();
    }

    public function getAuthorstringExact(): string
    {
        return $this->authorstring_exact;
    }

    public function setAuthorstringExact(string $authorstring_exact): self
    {
        $this->authorstring_exact = $authorstring_exact;
        return $this;
    }

    public function getConcatination(): string
    {
        return $this->concatination;
    }

    public function setConcatination(string $concatination): self
    {
        $this->concatination = $concatination;
        return $this;
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

    public function isTitleSet(): bool
    {
        return $this->getTitle() !== '';
    }

    public function getTitleExact(): string
    {
        return $this->title_exact;
    }

    public function setTitleExact(string $title_exact): self
    {
        $this->title_exact = $title_exact;
        return $this;
    }
}
