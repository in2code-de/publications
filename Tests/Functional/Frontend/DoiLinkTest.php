<?php

declare(strict_types=1);

namespace In2code\Publications\Tests\Functional\Frontend;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class DoiLinkTest extends FunctionalTestCase
{
    private const BASE_URL = 'https://website.local/en/';
    private const PAGE_LINKBLOCK_TABLE = 10;
    private const PAGE_LINKBLOCK_INLINE = 14;
    private const EXPECTED_DOI_LINKS = [
        'https://doi.org/10.1234/example',
        'https://doi.org/10.1234/other',
    ];
    private const INVALID_DOI = '10. Auflage';

    protected array $testExtensionsToLoad = [
        'in2code/publications',
    ];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/publications/Tests/Functional/Frontend/Fixtures/Sites' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DoiLinks.csv');
        $this->setUpFrontendRootPage(1, [
            'constants' => ['EXT:publications/Configuration/TypoScript/constants.typoscript'],
            'setup' => [
                'EXT:publications/Configuration/TypoScript/setup.typoscript',
                'EXT:publications/Tests/Functional/Frontend/Fixtures/Setup.typoscript',
            ],
        ]);
    }

    #[Test]
    public function linkBlockTableLinksNormalizedDoisAndShowsInvalidDoiAsText(): void
    {
        $xpath = $this->renderPage(self::PAGE_LINKBLOCK_TABLE);

        $doiCells = [];
        foreach ($xpath->query('//tr[td[1][normalize-space()="DOI:"]]/td[2]') as $cell) {
            $doiCells[] = trim($cell->textContent);
        }
        sort($doiCells);

        self::assertSame(self::EXPECTED_DOI_LINKS, $this->getDoiLinks($xpath));
        self::assertSame([self::INVALID_DOI, '10.1234/example', '10.1234/other'], $doiCells);
    }

    #[Test]
    public function inlineLinkBlockLinksNormalizedDoisAndShowsInvalidDoiAsText(): void
    {
        $xpath = $this->renderPage(self::PAGE_LINKBLOCK_INLINE);

        self::assertSame(self::EXPECTED_DOI_LINKS, $this->getDoiLinks($xpath));
        self::assertStringContainsString(
            self::INVALID_DOI,
            $xpath->query('//div[@class="tx-publications"]')->item(0)->textContent
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
    private function getDoiLinks(DOMXPath $xpath): array
    {
        $links = [];
        foreach ($xpath->query('//div[@class="tx-publications"]//a[starts-with(@href, "https://doi.org/")]') as $link) {
            $links[] = $link->getAttribute('href');
        }
        sort($links);
        return $links;
    }
}
