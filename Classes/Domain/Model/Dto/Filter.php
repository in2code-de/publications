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
 * Class Filter.
 */
class Filter
{
    /**
     * Commaseparated identifiers of author objects.
     *
     * @var string "123,345,678"
     */
    protected $author = '';

    /**
     * @var string
     */
    protected $authorstring = '';

    /**
     * @var array
     */
    protected $bibtypes = [];
    /**
     * @var int
     */
    protected $citestyle = 0;

    /**
     * @var array
     */
    protected $export = [];

    /**
     * @var int 0=off, 1=intern, 2=extern
     */
    protected $externFilter = 0;

    /**
     * @var int
     */
    protected $groupby = 0;

    /**
     * @var array
     */
    protected $keywords = [];

    protected int $maximumNumberOfLinks = 10;

    protected int $page = 0;

    /**
     * @var array
     */
    protected $records = [];

    /**
     * @var int
     */
    protected $recordsPerPage = 25;

    /**
     * @var int recursive level
     */
    protected $recursive = 0;

    /**
     * @var string
     */
    protected $searchterm = '';

    /**
     * @var int[]
     */
    protected $status = [];

    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @var int
     */
    protected $timeframe = 0;

    /**
     * @var int
     */
    protected $year = 0;

    /**
     * Filter constructor.
     */
    public function __construct(array $settings)
    {
        $this->setCitestyle((int) $settings['citestyle']);
        $this->setGroupby((int) $settings['groupby']);
        $this->setRecordsPerPage((int) $settings['recordsPerPage']);
        $this->setMaximumNumberOfLinks((int) $settings['maximumNumberOfLinks']);
        $this->setTimeframe((int) $settings['timeframe']);
        $this->setBibtypes(GeneralUtility::trimExplode(',', $settings['bibtypes'], true));
        $this->setStatus(GeneralUtility::intExplode(',', $settings['status'], true));
        $this->setKeywords(GeneralUtility::trimExplode(PHP_EOL, $settings['keywords'], true));
        $this->setTags(GeneralUtility::trimExplode(PHP_EOL, $settings['tags'], true));
        $this->setAuthor($settings['author']);
        $this->setExternFilter((int) $settings['extern']);
        $this->setRecursive((int) $settings['recursive']);
        $this->setRecords(GeneralUtility::intExplode(',', $settings['records'], true));
        $this->setExport(GeneralUtility::intExplode(',', $settings['export'], true));
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return Author[]
     */
    public function getAuthors()
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

    public function getAuthorstring(): string
    {
        return $this->authorstring;
    }

    /**
     * Split on space and remove comma to allow a search for e.g. "Bernd Aumann, Klaus Fumy".
     */
    public function getAuthorstrings(): array
    {
        $authorstring = str_replace(',', '', $this->getAuthorstring());

        return GeneralUtility::trimExplode(' ', $authorstring, true);
    }

    public function getBibtypes(): array
    {
        return $this->bibtypes;
    }

    public function getCitestyle(): int
    {
        return $this->citestyle;
    }

    /**
     * @return \DateTime
     */
    public function getDateFromTimeFrame()
    {
        $date = null;
        try {
            $date = new \DateTime();
            if ($this->getTimeframe() > 0) {
                $date->modify('- '.$this->getTimeframe().' years');
            }
        } catch (\Exception $exception) {
        }

        return $date;
    }

    public function getExport(): array
    {
        return $this->export;
    }

    public function getExternFilter(): int
    {
        return $this->externFilter;
    }

    public function getGroupby(): int
    {
        return $this->groupby;
    }

    public function getGroupByArrayForQuery(): array
    {
        switch ($this->getGroupby()) {
            case 0:
                $orderings = [
                    'year' => QueryInterface::ORDER_DESCENDING,
                    'title' => QueryInterface::ORDER_ASCENDING,
                ];
                break;
            case 1:
                $orderings = [
                    'bibtype' => QueryInterface::ORDER_ASCENDING,
                    'title' => QueryInterface::ORDER_ASCENDING,
                ];
                break;
            case 2:
                $orderings = [
                    'year' => QueryInterface::ORDER_DESCENDING,
                    'bibtype' => QueryInterface::ORDER_ASCENDING,
                    'title' => QueryInterface::ORDER_ASCENDING,
                ];
                break;
            default:
            case 3:
                $orderings = [
                    'year' => QueryInterface::ORDER_DESCENDING,
                    'authors.lastName' => QueryInterface::ORDER_ASCENDING,
                    'authors.firstName' => QueryInterface::ORDER_ASCENDING,
                    'title' => QueryInterface::ORDER_ASCENDING,
                ];
        }

        return $orderings;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function getMaximumNumberOfLinks(): int
    {
        return $this->maximumNumberOfLinks;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getRecords(): array
    {
        return $this->records;
    }

    public function getRecordsPerPage(): int
    {
        return $this->recordsPerPage;
    }

    public function getRecursive(): int
    {
        return $this->recursive;
    }

    public function getSearchterm(): string
    {
        return $this->searchterm;
    }

    public function getSearchterms(): array
    {
        return GeneralUtility::trimExplode(' ', $this->getSearchterm(), true);
    }

    public function getStatus(): array
    {
        return $this->status;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function getTimeframe(): int
    {
        return $this->timeframe;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @return \DateTime
     */
    public function getYearFrom()
    {
        try {
            return new \DateTime('first day of January '.$this->getYear());
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @return \DateTime|null
     */
    public function getYearTo()
    {
        try {
            $date = new \DateTime('first day of January '.($this->getYear() + 1));
            $date->modify('- 1 minute');

            return $date;
        } catch (\Exception $exception) {
            return null;
        }
    }

    public function isAuthorSet(): bool
    {
        return $this->getAuthor() !== '';
    }

    public function isAuthorstringSet(): bool
    {
        return $this->getAuthorstring() !== '';
    }

    public function isBibtypesSet(): bool
    {
        return $this->getBibtypes() !== [];
    }

    public function isCitestyleSet(): bool
    {
        return $this->getCitestyle() !== 0;
    }

    public function isExtern(): bool
    {
        return $this->getExternFilter() === 2;
    }

    public function isExternOrIntern(): bool
    {
        return $this->isIntern() || $this->isExtern();
    }

    public function isFilterFlexFormSet(): bool
    {
        return $this->isCitestyleSet()
            || $this->isGroupbySet()
            || $this->isMaximumNumberOfLinksSet()
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
            || $this->isAuthorstringSet();
    }

    public function isGroupbySet(): bool
    {
        return $this->getGroupby() !== 0;
    }

    public function isIntern(): bool
    {
        return $this->getExternFilter() === 1;
    }

    public function isKeywordsSet(): bool
    {
        return $this->getKeywords() !== [];
    }

    public function isMaximumNumberOfLinksSet(): bool
    {
        return $this->getMaximumNumberOfLinks() !== 10;
    }

    public function isRecordsPerPageSet(): bool
    {
        return $this->getRecordsPerPage() !== 25;
    }

    public function isRecordsSet(): bool
    {
        return $this->getRecords() !== [];
    }

    public function isSearchtermSet(): bool
    {
        return $this->getSearchterm() !== '';
    }

    public function isStatusSet(): bool
    {
        return $this->getStatus() !== [];
    }

    public function isTagsSet(): bool
    {
        return $this->getTags() !== [];
    }

    public function isTimeFrameSet(): bool
    {
        return $this->getTimeframe() !== 0;
    }

    public function isYearSet(): bool
    {
        return $this->getYear() !== 0;
    }

    /**
     * @return Filter
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setAuthorstring(string $authorstring): self
    {
        $this->authorstring = $authorstring;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setBibtypes(array $bibtypes): self
    {
        $this->bibtypes = $bibtypes;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setCitestyle(int $citestyle): self
    {
        $this->citestyle = $citestyle;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setExport(array $export): self
    {
        $this->export = $export;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setExternFilter(int $externFilter): self
    {
        $this->externFilter = $externFilter;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setGroupby(int $groupby): self
    {
        $this->groupby = $groupby;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setKeywords(array $keywords): self
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function setMaximumNumberOfLinks(int $maximumNumberOfLinks): void
    {
        $this->maximumNumberOfLinks = $maximumNumberOfLinks;
    }

    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return Filter
     */
    public function setRecords(array $records): self
    {
        $this->records = PageUtility::extendPidListByChildren($records, $this->getRecursive());

        return $this;
    }

    /**
     * @return Filter
     */
    public function setRecordsPerPage(int $recordsPerPage): self
    {
        $this->recordsPerPage = $recordsPerPage;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setRecursive(int $recursive): self
    {
        $this->recursive = $recursive;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setSearchterm(string $searchterm): self
    {
        $this->searchterm = $searchterm;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setStatus(array $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setTimeframe(int $timeframe): self
    {
        $this->timeframe = $timeframe;

        return $this;
    }

    /**
     * @return Filter
     */
    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }
}
