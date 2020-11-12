<?php

declare(strict_types=1);

namespace In2code\Publications\Controller;

use In2code\Publications\Domain\Model\Dto\Filter;
use In2code\Publications\Domain\Repository\PublicationRepository;
use In2code\Publications\Utility\SessionUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class PublicationController.
 */
class PublicationController extends ActionController
{
    /**
     * @var PublicationRepository
     */
    protected $publicationRepository = null;

    /**
     * @return void
     *
     * @throws InvalidQueryException
     */
    public function downloadBibtexAction(Filter $filter)
    {
        $publications = $this->publicationRepository->findByFilter($filter);
        $this->view->assignMultiple([
            'filter' => $filter,
            'publications' => $publications,
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
     *
     * @throws InvalidQueryException
     */
    public function downloadXmlAction(Filter $filter)
    {
        $publications = $this->publicationRepository->findByFilter($filter);
        $this->view->assignMultiple([
            'filter' => $filter,
            'publications' => $publications,
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
     *
     * @throws InvalidArgumentNameException
     * @throws NoSuchArgumentException
     */
    public function initializeDownloadBibtexAction()
    {
        $this->setFilterArguments();
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentNameException
     * @throws NoSuchArgumentException
     */
    public function initializeDownloadXmlAction()
    {
        $this->setFilterArguments();
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentNameException
     * @throws NoSuchArgumentException
     */
    public function initializeListAction()
    {
        $this->setFilterArguments();
    }

    /**
     * @return void
     */
    public function injectPublicationRepository(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * @return void
     *
     * @throws InvalidQueryException
     */
    public function listAction(Filter $filter)
    {
        if ($this->request->hasArgument('page')) {
            $page = $this->request->getArgument('page');
        } else {
            $page = 1;
        }

        $filter->setPage((int) $page);

        $publications = $this->publicationRepository->findByFilter($filter);
        $counter = $this->publicationRepository->countPublications($filter);
        $lastPage = ceil($counter / $filter->getRecordsPerPage());
        $this->view->assignMultiple([
            'total' => $counter,
            'filter' => $filter,
            'publications' => $publications,
            'publicationsPage' => $page,
            'previousPage' => $page > 1 ? $page - 1 : 1,
            'nextPage' => ($page + 1 < $lastPage) ? $page + 1 : $lastPage,
            'data' => $this->getContentObject()->data,
        ]);
    }

    /**
     * @return void
     *
     * @throws StopActionException
     * @throws UnsupportedRequestTypeException
     */
    public function resetListAction()
    {
        SessionUtility::saveValueToSession('filter_'.$this->getContentObject()->data['uid'], []);
        $this->redirect('list');
    }

    protected function getContentObject(): ContentObjectRenderer
    {
        return $this->configurationManager->getContentObject();
    }

    /**
     * @return void
     *
     * @throws InvalidArgumentNameException
     * @throws NoSuchArgumentException
     */
    protected function setFilterArguments()
    {
        if ($this->request->hasArgument('filter') === false) {
            $filterArguments = SessionUtility::getSessionValue('filter_'.$this->getContentObject()->data['uid']);
        } else {
            /** @var array $filterArguments */
            $filterArguments = $this->request->getArgument('filter');
            SessionUtility::saveValueToSession('filter_'.$this->getContentObject()->data['uid'], $filterArguments);
        }
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $filter = $this->objectManager->get(Filter::class, $this->settings);
        if (!empty($filterArguments['searchterm'])) {
            $filter->setSearchterm($filterArguments['searchterm']);
        }
        if (!empty($filterArguments['year'])) {
            $filter->setYear((int) $filterArguments['year']);
        }
        if (!empty($filterArguments['authorstring'])) {
            $filter->setAuthorstring($filterArguments['authorstring']);
        }
        $this->request->setArgument('filter', $filter);
    }
}
