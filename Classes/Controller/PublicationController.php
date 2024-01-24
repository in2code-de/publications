<?php

declare(strict_types=1);

namespace In2code\Publications\Controller;

use In2code\Publications\Domain\Model\Dto\Filter;
use In2code\Publications\Domain\Repository\PublicationRepository;
use In2code\Publications\Domain\Service\PublicationService;
use In2code\Publications\Pagination\NumberedPagination;
use In2code\Publications\Utility\SessionUtility;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\PropagateResponseException;
use TYPO3\CMS\Core\Pagination\ArrayPaginator;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Persistence\Exception\InvalidQueryException;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class PublicationController
 */
class PublicationController extends ActionController
{
    protected ?PublicationRepository $publicationRepository = null;
    protected ?PublicationService $publicationService = null;

    public function __construct(PublicationService $publicationService, PublicationRepository $publicationRepository)
    {
        $this->publicationService = $publicationService;
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * @return void
     */
    public function initializeListAction(): void
    {
        $this->setFilterArguments();
    }

    /**
     * @throws InvalidQueryException
     */
    public function listAction(Filter $filter): ResponseInterface
    {
        $publications = $this->publicationRepository->findByFilter($filter);

        $itemsPerPage = $filter->getRecordsPerPage();
        $maximumLinks = 10;

        $currentPage = $this->request->hasArgument('currentPage') ? (int)$this->request->getArgument('currentPage') : 1;
        $paginator = new ArrayPaginator($publications, $currentPage, $itemsPerPage);
        $pagination = new NumberedPagination($paginator, $maximumLinks);
        $contentObjectUid = $this->getContentObject()->data['uid'] ?? 0;

        if (array_key_exists('showGroupLinks', $this->settings) && (bool)$this->settings['showGroupLinks'] === true) {
            $this->view->assign(
                'groupLinks',
                $this->publicationService->getGroupedPublicationLinks(
                    $publications,
                    (int)$this->settings['groupby'],
                    $contentObjectUid,
                    $itemsPerPage
                )
            );
        }

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
        return $this->htmlResponse();
    }

    public function resetListAction(): ?ResponseInterface
    {
        SessionUtility::saveValueToSession('filter_' . $this->getContentObject()->data['uid'], []);
        return $this->redirect('list');
    }

    public function initializeDownloadBibtexAction(): void
    {
        $this->setFilterArguments();
        $this->request = $this->request->withFormat('xml');
    }

    /**
     * @throws InvalidQueryException|PropagateResponseException
     */
    public function downloadBibtexAction(Filter $filter): ResponseInterface
    {
        $publications = $this->publicationRepository->findByFilter($filter);

        $this->view->assignMultiple(
            [
                'filter' => $filter,
                'publications' => $publications
            ]
        );

        $response = $this->responseFactory
            ->createResponse()
            ->withHeader('Content-Type', 'application/x-bibtex')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Content-Disposition', 'attachment; filename="download.bib"')
            ->withBody($this->streamFactory->createStream($this->view->render()));

        throw new PropagateResponseException($response, 200);
    }

    public function initializeDownloadXmlAction(): void
    {
        $this->setFilterArguments();
        $this->request = $this->request->withFormat('xml');
    }

    /**
     * @throws InvalidQueryException|PropagateResponseException
     */
    public function downloadXmlAction(Filter $filter): ResponseInterface
    {
        $publications = $this->publicationRepository->findByFilter($filter);
        $this->view->assignMultiple(
            [
                'filter' => $filter,
                'publications' => $publications
            ]
        );

        $response = $this->responseFactory
            ->createResponse()
            ->withHeader('Content-Type', 'text/xml')
            ->withHeader('Pragma', 'no-cache')
            ->withHeader('Content-Disposition', 'attachment; filename="download.xml"')
            ->withBody($this->streamFactory->createStream($this->view->render()));

        throw new PropagateResponseException($response, 200);
    }

    protected function setFilterArguments(): void
    {
        if ($this->request->hasArgument('filter') === false) {
            $filterArguments = SessionUtility::getSessionValue('filter_' . $this->getContentObject()->data['uid']);
        } else {
            /** @var array $filterArguments */
            $filterArguments = $this->request->getArgument('filter');
            SessionUtility::saveValueToSession('filter_' . $this->getContentObject()->data['uid'], $filterArguments);
        }
        $filter = GeneralUtility::makeInstance(Filter::class, $this->settings);
        if (!empty($filterArguments['searchterm'])) {
            $filter->setSearchterm($filterArguments['searchterm']);
        }
        if (!empty($filterArguments['year'])) {
            $filter->setYear((int)$filterArguments['year']);
        }
        if (!empty($filterArguments['authorstring'])) {
            $filter->setAuthorstring($filterArguments['authorstring']);
        }
        if (!empty($filterArguments['documenttype'])) {
            $filter->setDocumenttype($filterArguments['documenttype']);
        }

        $this->request = $this->request->withArguments([
            'filter' => $filter,
            'currentContentObject' => $this->getContentObject(),
        ]);
    }

    protected function getContentObject(): ?ContentObjectRenderer
    {
        return $this->request->getAttribute('currentContentObject') ?? null;
    }
}
