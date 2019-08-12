<?php
declare(strict_types=1);
namespace In2code\Publications\Controller;

use In2code\Publications\Domain\Model\Dto\Filter;
use In2code\Publications\Domain\Repository\PublicationRepository;
use In2code\Publications\Utility\FrontendUserUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
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
        if ($this->request->hasArgument('filter') === false) {
            $filterArguments = FrontendUserUtility::getSessionValue('filter');
        } else {
            /** @var array $filterArguments */
            $filterArguments = $this->request->getArgument('filter');
            FrontendUserUtility::saveValueToSession('filter', $filterArguments);
        }
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $filter = $this->objectManager->get(Filter::class, $this->settings);
        if (!empty($filterArguments['searchterm'])) {
            $filter->setSearchterm($filterArguments['searchterm']);
        }
        if (!empty($filterArguments['year'])) {
            $filter->setYear((int)$filterArguments['year']);
        }
        if (!empty($filterArguments['authorstring'])) {
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
            'publications' => $publications,
            'showPagination' => count($publications) > $filter->getRecordsPerPage()
        ]);
    }

    /**
     * @return void
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     */
    public function resetListAction()
    {
        FrontendUserUtility::saveValueToSession('filter', []);
        $this->redirect('list');
    }
}
