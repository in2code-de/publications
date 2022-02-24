<?php

declare(strict_types=1);

namespace In2code\Publications\Controller;

use In2code\Publications\Domain\Model\Dto\Filter;
use In2code\Publications\Domain\Repository\PublicationRepository;
use In2code\Publications\Pagination\NumberedPagination;
use In2code\Publications\Utility\SessionUtility;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class PublicationController
 */
class PublicationController extends ActionController
{
    /**
     * @var PublicationRepository
     */
    protected ?PublicationRepository $publicationRepository = null;

    /**
     * @return void
     * @throws NoSuchArgumentException
     */
    public function initializeListAction()
    {
        $this->setFilterArguments();
    }

    /**
     * @param Filter $filter
     * @return void
     * @throws InvalidQueryException
     * @throws NoSuchArgumentException
     */
    public function listAction(Filter $filter)
    {
        $publications = $this->publicationRepository->findByFilter($filter);

        $itemsPerPage =  $filter->getRecordsPerPage();
        $maximumLinks = 10;

        $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
        $paginator = new ArrayPaginator($publications, $currentPage, $itemsPerPage);
        $pagination = new NumberedPagination($paginator, $maximumLinks);

        $this->view->assign('pagination', [
            'paginator' => $paginator,
            'pagination' => $pagination,
        ]);

        $this->view->assignMultiple([
            'filter' => $filter,
            'publications' => $publications,
            'data' => $this->getContentObject()->data,
            'maxItems' => count($publications),
        ]);
    }

    /**
     * @return void
     * @throws StopActionException
     */
    public function resetListAction()
    {
        SessionUtility::saveValueToSession('filter_' . $this->getContentObject()->data['uid'], []);
        $this->redirect('list');
    }

    /**
     * @return void
     * @throws NoSuchArgumentException
     */
    public function initializeDownloadBibtexAction()
    {
        $this->setFilterArguments();
    }

    /**
     * @param Filter $filter
     * @return void
     * @throws InvalidQueryException
     */
    public function downloadBibtexAction(Filter $filter)
    {
        $publications = $this->publicationRepository->findByFilter($filter);
        $this->view->assignMultiple([
            'filter' => $filter,
            'publications' => $publications
        ]);

        $this->response->setHeader('Content-Type', 'application/x-bibtex');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="download.bib"');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->sendHeaders();
        echo $this->view->render();
        exit;
    }

    /**
     * @return void
     * @throws NoSuchArgumentException
     */
    public function initializeDownloadXmlAction()
    {
        $this->setFilterArguments();
    }

    /**
     * @param Filter $filter
     * @return void
     * @throws InvalidQueryException
     */
    public function downloadXmlAction(Filter $filter)
    {
        $publications = $this->publicationRepository->findByFilter($filter);
        $this->view->assignMultiple([
            'filter' => $filter,
            'publications' => $publications
        ]);

        $this->response->setHeader('Content-Type', 'text/xml');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="download.xml"');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->sendHeaders();
        echo $this->view->render();
        exit;
    }

    /**
     * @return void
     * @throws NoSuchArgumentException
     */
    protected function setFilterArguments()
    {
        if ($this->request->hasArgument('filter') === false) {
            $filterArguments = SessionUtility::getSessionValue('filter_' . $this->getContentObject()->data['uid']);
        } else {
            /** @var array $filterArguments */
            $filterArguments = $this->request->getArgument('filter');
            SessionUtility::saveValueToSession('filter_' . $this->getContentObject()->data['uid'], $filterArguments);
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
     * @param PublicationRepository $publicationRepository
     * @return void
     */
    public function injectPublicationRepository(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * @return ContentObjectRenderer
     */
    protected function getContentObject(): ContentObjectRenderer
    {
        return $this->configurationManager->getContentObject();
    }
}
