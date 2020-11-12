<?php

declare(strict_types=1);

namespace In2code\Publications\Adapter;

use In2code\Publications\Domain\Model\Dto\Filter;
use In2code\Publications\Domain\Repository\PublicationRepository;
use Pagerfanta\Adapter\AdapterInterface;


class PaginationAdapter implements AdapterInterface
{

    private Filter $filter;
    private PublicationRepository $repository;

    public function __construct(Filter $filter, PublicationRepository $repository)
    {
        $this->filter = $filter;
        $this->repository = $repository;
    }

    public function getNbResults()
    {
        return $this->repository->countPublications($this->filter);
    }

    public function getSlice($offset, $length)
    {
        $this->filter->setRecordsPerPage($length);
        $this->filter->setPage($this->filter->getPage());

        return $this->repository->findByFilter($this->filter);
    }
}
