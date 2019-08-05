<?php
declare(strict_types=1);
namespace In2code\Publications\Domain\Repository;

use In2code\Publications\Domain\Model\Dto\Filter;
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
     * @return array|QueryResultInterface
     * @throws InvalidQueryException
     */
    public function findByFilter(Filter $filter)
    {
        $query = $this->createQuery();
        $this->filterQuery($query, $filter);
        return $query->execute();
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
}
