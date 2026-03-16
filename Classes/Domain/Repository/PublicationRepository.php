<?php

declare(strict_types=1);

namespace In2code\Publications\Domain\Repository;

use In2code\Publications\Domain\Model\Dto\Filter;
use In2code\Publications\Domain\Model\Publication;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class PublicationRepository extends AbstractRepository
{
    /**
     * @throws InvalidQueryException
     */
    public function findByFilter(Filter $filter): array
    {
        $query = $this->createQuery();
        $this->filterQuery($query, $filter);
        $this->setOrderingsByFilterSettings($query, $filter);
        $results = $query->execute();
        return $this->convertToArray($results);
    }

    /**
     * @throws InvalidQueryException
     */
    protected function filterQuery(QueryInterface $query, Filter $filter): void
    {
        $concat = [];
        if ($filter->isFilterFlexFormSet()) {
            $concat = $this->filterQueryByKeywords($query, $filter, $concat);
            $concat = $this->filterQueryByTags($query, $filter, $concat);
            $concat = $this->filterQueryByTimeframe($query, $filter, $concat);
            $concat = $this->filterQueryByBibtypes($query, $filter, $concat);
            $concat = $this->filterQueryByStatus($query, $filter, $concat);
            $concat = $this->filterQueryByAuthor($query, $filter, $concat);
            $concat = $this->filterQueryByExternFilter($query, $filter, $concat);
            $concat = $this->filterQueryByReviewFilter($query, $filter, $concat);
            $concat = $this->filterQueryByRecords($query, $filter, $concat);
        }
        if ($filter->isFilterFrontendSet()) {
            $concat = $this->filterQueryBySearchterms($query, $filter, $concat);
            $concat = $this->filterQueryByYear($query, $filter, $concat);
            $concat = $this->filterQueryByAuthorstring($query, $filter, $concat);
            $concat = $this->filterQueryByDocumenttype($query, $filter, $concat);
            $concat = $this->filterQueryByTitle($query, $filter, $concat);
        }
        if ($concat !== [] && $filter->getConcatination() === 'or') {
            $query->matching($query->logicalOr(...$concat));
        } elseif ($concat !== []) {
            $query->matching($query->logicalAnd(...$concat));
        }
    }

    /**
     * @throws InvalidQueryException
     */
    protected function filterQueryByKeywords(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isKeywordsSet()) {
            $or = [];
            foreach ($filter->getKeywords() as $keyword) {
                $or[] = $query->like('keywords', '%' . $keyword . '%');
            }
            $and[] = $query->logicalOr(...$or);
        }
        return $and;
    }

    /**
     * @throws InvalidQueryException
     */
    protected function filterQueryByTags(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isTagsSet()) {
            $or = [];
            foreach ($filter->getTags() as $tag) {
                $or[] = $query->like('tags', '%' . $tag . '%');
            }
            $and[] = $query->logicalOr(...$or);
        }
        return $and;
    }

    /**
     * @throws InvalidQueryException
     */
    protected function filterQueryByTimeframe(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isTimeFrameSet()) {
            $and[] = $query->greaterThan('year', $filter->getDateFromTimeFrame()->format('Y'));
        }
        return $and;
    }

    protected function filterQueryByBibtypes(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isBibtypesSet()) {
            $or = [];
            foreach ($filter->getBibtypes() as $bibtype) {
                $or[] = $query->equals('bibtype', $bibtype);
            }
            $and[] = $query->logicalOr(...$or);
        }
        return $and;
    }

    protected function filterQueryByStatus(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isStatusSet()) {
            $or = [];
            foreach ($filter->getStatus() as $status) {
                $or[] = $query->equals('status', $status);
            }
            $and[] = $query->logicalOr(...$or);
        }
        return $and;
    }

    /**
     * @throws InvalidQueryException
     */
    protected function filterQueryByAuthor(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isAuthorSet()) {
            $or = [];
            foreach ($filter->getAuthors() as $author) {
                $or[] = $query->contains('authors', $author);
            }
            $and[] = $query->logicalOr(...$or);
        }
        return $and;
    }

    protected function filterQueryByExternFilter(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isExternOrIntern()) {
            $and[] = $query->equals('extern', ($filter->isIntern() ? 0 : 1));
        }
        return $and;
    }

    protected function filterQueryByReviewFilter(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isReviewed()) {
            $and[] = $query->equals('reviewed', ($filter->isReview() ? 0 : 1));
        }
        return $and;
    }

    /**
     * @throws InvalidQueryException
     */
    protected function filterQueryByRecords(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isRecordsSet()) {
            $and[] = $query->in('pid', $filter->getRecords());
        }
        return $and;
    }

    /**
     * @throws InvalidQueryException
     */
    protected function filterQueryBySearchterms(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isSearchtermSet()) {
            $or = [];
            foreach ($filter->getSearchterms() as $searchterm) {
                $or[] = $query->like('title', '%' . $searchterm . '%');
                $or[] = $query->like('abstract', '%' . $searchterm . '%');
                $or[] = $query->like('booktitle', '%' . $searchterm . '%');
                $or[] = $query->like('journal', '%' . $searchterm . '%');
                $or[] = $query->like('edition', '%' . $searchterm . '%');
                $or[] = $query->like('volume', '%' . $searchterm . '%');
                $or[] = $query->like('publisher', '%' . $searchterm . '%');
                $or[] = $query->like('chapter', '%' . $searchterm . '%');
                $or[] = $query->like('series', '%' . $searchterm . '%');
                $or[] = $query->like('edition', '%' . $searchterm . '%');
                $or[] = $query->like('keywords', '%' . $searchterm . '%');
                $or[] = $query->like('tags', '%' . $searchterm . '%');
                $or[] = $query->like('authors.firstName', '%' . $searchterm . '%');
                $or[] = $query->like('authors.lastName', '%' . $searchterm . '%');
            }
            $and[] = $query->logicalOr(...$or);
        }
        return $and;
    }

    protected function filterQueryByYear(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isYearSet()) {
            $and[] = $query->equals('year', $filter->getYear());
        }
        return $and;
    }

    protected function filterQueryByDocumenttype(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isDocumenttypeSet()) {
            $and[] = $query->equals('bibtype', $filter->getDocumenttype());
        }
        return $and;
    }

    protected function filterQueryByTitle(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isTitleSet()) {
            $sql_delimiter = '%';
            if ($filter->getTitleExact() === 'exact') {
                $sql_delimiter = '';
            }
            $and[] = $query->like('title', $sql_delimiter . $filter->getTitle() . $sql_delimiter);
        }
        return $and;
    }

    /**
     * @throws InvalidQueryException
     */
    protected function filterQueryByAuthorstring(QueryInterface $query, Filter $filter, array $and): array
    {
        if ($filter->isAuthorstringSet()) {
            $sql_delimiter = '%';
            if ($filter->getAuthorstringExact() === 'exact') {
                $sql_delimiter = '';
            }
            $or = [];
            foreach ($filter->getAuthorstrings() as $authorstring) {
                $or[] = $query->like('authors.firstName', $sql_delimiter . $authorstring . $sql_delimiter);
                $or[] = $query->like('authors.lastName', $sql_delimiter . $authorstring . $sql_delimiter);
            }
            $and[] = $query->logicalOr(...$or);
        }
        return $and;
    }

    protected function setOrderingsByFilterSettings(QueryInterface $query, Filter $filter): void
    {
        $query->setOrderings($filter->getGroupByArrayForQuery());
    }

    protected function convertToArray(QueryResultInterface $results): array
    {
        return $results->toArray();
    }

    /**
     * Callback function to sort by a date
     * @return int 0 or -1 or 1
     */
    public function compareCallbackByDate(Publication $p1, Publication $p2): int
    {
        return $p2->getDate() <=> $p1->getDate();
    }
}
