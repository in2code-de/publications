<?php
declare(strict_types=1);
namespace In2code\Publications\Controller;

use In2code\Publications\Domain\Model\Dto\Filter;
use In2code\Publications\Domain\Repository\PublicationRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;

/**
 * Class PublicationController
 */
class PublicationController extends ActionController
{
    /**
     * @return void
     * @throws InvalidArgumentNameException
     * @throws NoSuchArgumentException
     */
    public function initializeListAction()
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $filter = $this->objectManager->get(Filter::class, $this->settings);
        if ($this->request->hasArgument('filter') === true) {
            /** @var array $filterArguments */
            $filterArguments = $this->request->getArgument('filter');
            $filter->setSearchterm($filterArguments['searchterm']);
            $filter->setYear((int)$filterArguments['year']);
            $filter->setAuthorstring($filterArguments['authorstring']);
        }
        $this->request->setArgument('filter', $filter);
    }

    /**
     * @param Filter $filter
     * @return void
     * @throws InvalidQueryException
     */
    public function listAction(Filter $filter)
    {
        $publicationRepository = $this->objectManager->get(PublicationRepository::class);
        $publications = $publicationRepository->findByFilter($filter);
        $this->view->assignMultiple([
            'filter' => $filter,
            'publications' => $publications
        ]);
    }
}
