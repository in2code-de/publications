<?php
declare(strict_types=1);
namespace In2code\Publications\Domain\Repository;

use In2code\Publications\Domain\Model\Dto\Filter;
use In2code\Publications\Domain\Model\Publication;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class PublicationRepository
 */
class PublicationRepository extends Repository
{
    /**
     * @param Filter $filter
     * @return array
     * @throws InvalidQueryException
     */
    public function findByFilter(Filter $filter): array
    {
        $query = $this->createQuery();
        $this->filterQuery($query, $filter);
        $this->setOrderingsByFilterSettings($query, $filter);
        $results = $query->execute();
        $results = $this->convertToAscendingArray($results, $filter);
        return $results;
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @return void
     * @throws InvalidQueryException
     */
    protected function filterQuery(QueryInterface $query, Filter $filter)
    {
        $and = [];
        if ($filter->isFilterFlexFormSet()) {
            $and = $this->filterQueryByKeywords($query, $filter, $and);
            $and = $this->filterQueryByTags($query, $filter, $and);
            $and = $this->filterQueryByTimeframe($query, $filter, $and);
            $and = $this->filterQueryByBibtypes($query, $filter, $and);
            $and = $this->filterQueryByStatus($query, $filter, $and);
            $and = $this->filterQueryByAuthor($query, $filter, $and);
            $and = $this->filterQueryByRecords($query, $filter, $and);
        }
        if ($filter->isFilterFrontendSet()) {
            $and = $this->filterQueryBySearchterms($query, $filter, $and);
            $and = $this->filterQueryByYear($query, $filter, $and);
            $and = $this->filterQueryByAuthorstring($query, $filter, $and);
        }
        if ($and !== []) {
            $query->matching($query->logicalAnd($and));
        }
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @param array $and
     * @return array
     * @throws InvalidQueryException
     */
    protected function filterQueryByKeywords(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isKeywordsSet()) {
            $or = [];
            foreach ($filter->getKeywords() as $keyword) {
                $or[] = $query->like('keywords', '%' . $keyword . '%');
            }
            $and[] = $query->logicalOr($or);
        }
        return $and;
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @param array $and
     * @return array
     * @throws InvalidQueryException
     */
    protected function filterQueryByTags(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isTagsSet()) {
            $or = [];
            foreach ($filter->getTags() as $tag) {
                $or[] = $query->like('tags', '%' . $tag . '%');
            }
            $and[] = $query->logicalOr($or);
        }
        return $and;
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @param array $and
     * @return array
     * @throws InvalidQueryException
     */
    protected function filterQueryByTimeframe(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isTimeFrameSet()) {
            $and[] = $query->greaterThan('date', $filter->getDateFromTimeFrame());
        }
        return $and;
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @param array $and
     * @return array
     */
    protected function filterQueryByBibtypes(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isBibtypesSet()) {
            $or = [];
            foreach ($filter->getBibtypes() as $bibtype) {
                $or[] = $query->equals('bibtype', $bibtype);
            }
            $and[] = $query->logicalOr($or);
        }
        return $and;
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @param array $and
     * @return array
     */
    protected function filterQueryByStatus(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isStatusSet()) {
            $or = [];
            foreach ($filter->getStatus() as $status) {
                $or[] = $query->equals('status', $status);
            }
            $and[] = $query->logicalOr($or);
        }
        return $and;
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @param array $and
     * @return array
     * @throws InvalidQueryException
     */
    protected function filterQueryByAuthor(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isAuthorSet()) {
            $and[] = $query->contains('authors', $filter->getAuthorObject());
        }
        return $and;
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @param array $and
     * @return array
     */
    protected function filterQueryByRecords(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isRecordsSet()) {
            $and[] = $query->equals('pid', $filter->getRecords());
        }
        return $and;
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @param array $and
     * @return array
     * @throws InvalidQueryException
     */
    protected function filterQueryBySearchterms(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isSearchtermSet()) {
            $or = [];
            foreach ($filter->getSearchterms() as $searchterm) {
                $or[] = $query->like('title', '%' . $searchterm . '%');
                $or[] = $query->like('abstract', '%' . $searchterm . '%');
                $or[] = $query->like('bibtype', '%' . $searchterm . '%');
                $or[] = $query->like('type', '%' . $searchterm . '%');
                $or[] = $query->like('language', '%' . $searchterm . '%');
                $or[] = $query->like('citeid', '%' . $searchterm . '%');
                $or[] = $query->like('isbn', '%' . $searchterm . '%');
                $or[] = $query->like('issn', '%' . $searchterm . '%');
                $or[] = $query->like('doi', '%' . $searchterm . '%');
                $or[] = $query->like('organization', '%' . $searchterm . '%');
                $or[] = $query->like('school', '%' . $searchterm . '%');
                $or[] = $query->like('institution', '%' . $searchterm . '%');
                $or[] = $query->like('institute', '%' . $searchterm . '%');
                $or[] = $query->like('booktitle', '%' . $searchterm . '%');
                $or[] = $query->like('journal', '%' . $searchterm . '%');
                $or[] = $query->like('edition', '%' . $searchterm . '%');
                $or[] = $query->like('volume', '%' . $searchterm . '%');
                $or[] = $query->like('publisher', '%' . $searchterm . '%');
                $or[] = $query->like('address', '%' . $searchterm . '%');
                $or[] = $query->like('chapter', '%' . $searchterm . '%');
                $or[] = $query->like('series', '%' . $searchterm . '%');
                $or[] = $query->like('edition', '%' . $searchterm . '%');
                $or[] = $query->like('howpublished', '%' . $searchterm . '%');
                $or[] = $query->like('editor', '%' . $searchterm . '%');
                $or[] = $query->like('affiliation', '%' . $searchterm . '%');
                $or[] = $query->like('eventName', '%' . $searchterm . '%');
                $or[] = $query->like('eventPlace', '%' . $searchterm . '%');
                $or[] = $query->like('number', '%' . $searchterm . '%');
                $or[] = $query->like('number2', '%' . $searchterm . '%');
                $or[] = $query->like('keywords', '%' . $searchterm . '%');
                $or[] = $query->like('tags', '%' . $searchterm . '%');
                $or[] = $query->like('note', '%' . $searchterm . '%');
                $or[] = $query->like('annotation', '%' . $searchterm . '%');
                $or[] = $query->like('miscellaneous', '%' . $searchterm . '%');
                $or[] = $query->like('miscellaneous2', '%' . $searchterm . '%');
                $or[] = $query->like('borrowed_by', '%' . $searchterm . '%');
                $or[] = $query->like('authors.firstName', '%' . $searchterm . '%');
                $or[] = $query->like('authors.lastName', '%' . $searchterm . '%');
            }
            $and[] = $query->logicalOr($or);
        }
        return $and;
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @param array $and
     * @return array
     * @throws InvalidQueryException
     */
    protected function filterQueryByYear(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isYearSet()) {
            $and[] = $query->greaterThan('date', $filter->getYearFrom());
            $and[] = $query->lessThan('date', $filter->getYearTo());
        }
        return $and;
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @param array $and
     * @return array
     * @throws InvalidQueryException
     */
    protected function filterQueryByAuthorstring(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isAuthorstringSet()) {
            $or = [];
            foreach ($filter->getAuthorstrings() as $authorstring) {
                $or[] = $query->like('authors.firstName', '%' . $authorstring . '%');
                $or[] = $query->like('authors.lastName', '%' . $authorstring . '%');
            }
            $and[] = $query->logicalOr($or);
        }
        return $and;
    }

    /**
     * @param QueryInterface $query
     * @param Filter $filter
     * @return void
     */
    protected function setOrderingsByFilterSettings(QueryInterface $query, Filter $filter)
    {
        $query->setOrderings($filter->getGroupByArrayForQuery());
    }

    /**
     * Convert results to array and
     * - add a number to the records
     * - sort by filter settings
     *
     * @param QueryResultInterface $results
     * @param Filter $filter
     * @return array
     */
    protected function convertToAscendingArray(QueryResultInterface $results, Filter $filter): array
    {
        $i = 0;
        $resultsRaw = $results->toArray();
        usort($resultsRaw, [$this, 'compareCallback' . $filter->getGroupby()]);
        /** @var Publication $result */
        foreach ($resultsRaw as $result) {
            $result->setNumber(count($resultsRaw) - $i);
            $i++;
        }
        return $resultsRaw;
    }

    /**
     * Callback strcmp function for a simulated "group by year"
     * Order by "Year DESC" and if equal by "Title ASC"
     *
     * @param Publication $p1
     * @param Publication $p2
     * @return int 0 or -1 or 1
     */
    public function compareCallback0(Publication $p1, Publication $p2): int
    {
        $comparedByYear = $this->compareCallbackYear($p1, $p2);
        if ($comparedByYear === 0) {
            return $this->compareCallbackTitle($p1, $p2);
        }
        return $comparedByYear;
    }

    /**
     * Callback strcmp function for a simulated "group by bibtype"
     * Order by "Bibtype ASC" and if equal by "Title ASC"
     *
     * @param Publication $p1
     * @param Publication $p2
     * @return int 0 or -1 or 1
     */
    public function compareCallback1(Publication $p1, Publication $p2): int
    {
        $comparedByBibtype = $this->compareCallbackBibtype($p1, $p2);
        if ($comparedByBibtype === 0) {
            return $this->compareCallbackTitle($p1, $p2);
        }
        return $comparedByBibtype;
    }

    /**
     * Callback strcmp function for a simulated "group by year and within group by bibtype"
     * Order by "Year DESC" and if equal by "Bibtype ASC" and if equal by "Title ASC"
     *
     * @param Publication $p1
     * @param Publication $p2
     * @return int 0 or -1 or 1
     */
    public function compareCallback2(Publication $p1, Publication $p2): int
    {
        $comparedByYear = $this->compareCallbackYear($p1, $p2);
        if ($comparedByYear === 0) {
            $comparedByBibtype = $this->compareCallbackBibtype($p1, $p2);
            if ($comparedByBibtype === 0) {
                return $this->compareCallbackTitle($p1, $p2);
            }
            return $comparedByBibtype;
        }
        return $comparedByYear;
    }

    /**
     * Order by Year DESC
     *
     * @param Publication $p1
     * @param Publication $p2
     * @return int 0 or -1 or 1
     */
    protected function compareCallbackYear(Publication $p1, Publication $p2): int
    {
        return strcmp((string)$p2->getYearFromDate(), (string)$p1->getYearFromDate());
    }

    /**
     * Order by Bibtype ASC
     *
     * @param Publication $p1
     * @param Publication $p2
     * @return int 0 or -1 or 1
     */
    protected function compareCallbackBibtype(Publication $p1, Publication $p2): int
    {
        return strcmp($p1->getBibtype(), $p2->getBibtype());
    }

    /**
     * Order by Title ASC
     *
     * @param Publication $p1
     * @param Publication $p2
     * @return int 0 or -1 or 1
     */
    protected function compareCallbackTitle(Publication $p1, Publication $p2): int
    {
        return strcmp($p1->getTitle(), $p2->getTitle());
    }
}
