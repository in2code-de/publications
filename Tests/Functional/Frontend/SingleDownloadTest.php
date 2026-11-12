<?php

declare(strict_types=1);

namespace In2code\Publications\Tests\Functional\Frontend;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class SingleDownloadTest extends FunctionalTestCase
{
    private const BASE_URL = 'https://website.local/';
    private const PAGE_LINKBLOCK_TABLE = 10;
    private const PAGE_WITHOUT_SINGLE_DOWNLOAD = 11;
    private const PAGE_LINKBLOCK_INLINE = 14;
    private const PUBLICATION_MISC = 1;
    private const PUBLICATION_ARTICLE = 2;
    private const PUBLICATION_OUTSIDE_RECORDS = 3;
    private const PUBLICATION_MISSING = 999;

    protected array $testExtensionsToLoad = [
        'in2code/publications',
    ];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/publications/Tests/Functional/Frontend/Fixtures/Sites' => 'typo3conf/sites',
    ];

    protected array $configurationToUseInTestInstance = [
        'FE' => [
            'cacheHash' => [
                'enforceValidation' => false,
            ],
            'pageNotFoundOnCHashError' => false,
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/SingleDownload.csv');
        $this->setUpFrontendRootPage(1, [
            'constants' => ['EXT:publications/Configuration/TypoScript/constants.typoscript'],
            'setup' => [
                'EXT:publications/Configuration/TypoScript/setup.typoscript',
                'EXT:publications/Tests/Functional/Frontend/Fixtures/Setup.typoscript',
            ],
        ]);
    }

    /**
     * @return array<string, array{int, list<int>}>
     */
    public static function downloadLinksDataProvider(): array
    {
        return [
            'link block table' => [self::PAGE_LINKBLOCK_TABLE, [self::PUBLICATION_MISC, self::PUBLICATION_ARTICLE]],
            'inline link block' => [self::PAGE_LINKBLOCK_INLINE, [self::PUBLICATION_ARTICLE]],
        ];
    }

    /**
     * @param list<int> $publicationUids
     */
    #[Test]
    #[DataProvider('downloadLinksDataProvider')]
    public function listShowsDownloadLinksForEveryEnabledFormat(int $pageId, array $publicationUids): void
    {
        $xpath = $this->renderPage($pageId);

        foreach ($publicationUids as $publicationUid) {
            self::assertSame(['BibTeX', 'XML'], $this->getDownloadLinkTexts($xpath, $publicationUid));
        }
    }

    #[Test]
    public function listShowsNoDownloadLinksWithoutSingleDownloadOption(): void
    {
        $xpath = $this->renderPage(self::PAGE_WITHOUT_SINGLE_DOWNLOAD);

        self::assertSame([], $this->getDownloadLinkTexts($xpath, self::PUBLICATION_MISC));
        self::assertSame([], $this->getDownloadLinkTexts($xpath, self::PUBLICATION_ARTICLE));
        self::assertSame(1, $xpath->query('//a[normalize-space()="BibTeX download"]')->length);
    }

    #[Test]
    public function bibtexDownloadLinkOfListExportsOnlyThisPublication(): void
    {
        $href = $this->getDownloadLinkHref($this->renderPage(self::PAGE_LINKBLOCK_TABLE), self::PUBLICATION_MISC, 'BibTeX');

        $response = $this->executeFrontendSubRequest(new InternalRequest($href));
        $bibtex = (string)$response->getBody();

        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('@Misc { single-misc,', $bibtex);
        self::assertSame(1, substr_count($bibtex, '@'));
    }

    #[Test]
    public function xmlDownloadLinkOfListExportsOnlyThisPublication(): void
    {
        $href = $this->getDownloadLinkHref($this->renderPage(self::PAGE_LINKBLOCK_TABLE), self::PUBLICATION_ARTICLE, 'XML');

        $response = $this->executeFrontendSubRequest(new InternalRequest($href));
        $references = (new SimpleXMLElement((string)$response->getBody()))->reference;

        self::assertSame(200, $response->getStatusCode());
        self::assertCount(1, $references);
        self::assertSame('single-article', (string)$references[0]->citeid);
    }

    /**
     * @return array<string, array{string, int}>
     */
    public static function unavailablePublicationDataProvider(): array
    {
        return [
            'BibTeX, outside plugin records' => ['downloadBibtex', self::PUBLICATION_OUTSIDE_RECORDS],
            'XML, outside plugin records' => ['downloadXml', self::PUBLICATION_OUTSIDE_RECORDS],
            'BibTeX, missing publication' => ['downloadBibtex', self::PUBLICATION_MISSING],
        ];
    }

    #[Test]
    #[DataProvider('unavailablePublicationDataProvider')]
    public function downloadOfUnavailablePublicationReturnsNotFound(string $action, int $publicationUid): void
    {
        self::assertSame(404, $this->download($action, $publicationUid)->getStatusCode());
    }

    #[Test]
    public function downloadWithoutPublicationExportsAllPublicationsOfThePlugin(): void
    {
        $bibtex = (string)$this->download('downloadBibtex', 0)->getBody();

        self::assertStringContainsString('single-misc', $bibtex);
        self::assertStringContainsString('single-article', $bibtex);
        self::assertStringNotContainsString('outside-records', $bibtex);
    }

    private function download(string $action, int $publicationUid): ResponseInterface
    {
        $parameters = [
            'tx_publications_pi1[controller]' => 'Publication',
            'tx_publications_pi1[action]' => $action,
        ];
        if ($publicationUid > 0) {
            $parameters['tx_publications_pi1[publicationUid]'] = (string)$publicationUid;
        }
        return $this->executeFrontendSubRequest(
            (new InternalRequest(self::BASE_URL))->withPageId(self::PAGE_LINKBLOCK_TABLE)->withQueryParameters($parameters)
        );
    }

    private function renderPage(int $pageId): DOMXPath
    {
        $response = $this->executeFrontendSubRequest((new InternalRequest(self::BASE_URL))->withPageId($pageId));
        self::assertSame(200, $response->getStatusCode(), substr(strip_tags((string)$response->getBody()), 0, 1000));

        $document = new DOMDocument();
        $document->loadHTML(
            '<?xml encoding="utf-8"?>' . (string)$response->getBody(),
            LIBXML_NOERROR | LIBXML_NOWARNING
        );
        return new DOMXPath($document);
    }

    /**
     * @return list<string>
     */
    private function getDownloadLinkTexts(DOMXPath $xpath, int $publicationUid): array
    {
        $texts = [];
        foreach ($this->queryDownloadLinks($xpath, $publicationUid) as $link) {
            $texts[] = trim($link->textContent);
        }
        return $texts;
    }

    private function getDownloadLinkHref(DOMXPath $xpath, int $publicationUid, string $format): string
    {
        $href = '';
        foreach ($this->queryDownloadLinks($xpath, $publicationUid) as $link) {
            if (trim($link->textContent) === $format) {
                $href = $link->getAttribute('href');
            }
        }
        self::assertStringContainsString('publicationUid%5D=' . $publicationUid, $href);
        return str_starts_with($href, 'http') ? $href : rtrim(self::BASE_URL, '/') . $href;
    }

    /**
     * @return iterable<\DOMElement>
     */
    private function queryDownloadLinks(DOMXPath $xpath, int $publicationUid): iterable
    {
        return $xpath->query(
            '//div[@data-publications-record="' . $publicationUid . '"]//a[contains(@class, "publications-download")]'
        );
    }
}
