<?php

declare(strict_types=1);

namespace In2code\Publications\Tests\Functional\Frontend;

use PHPUnit\Framework\Attributes\Test;
use SimpleXMLElement;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class DownloadLinkTest extends FunctionalTestCase
{
    private const BASE_URL = 'https://website.local/';
    private const PAGE_PLUGIN = 10;
    private const TARGET_URL = 'https://website.local/target';
    private const TARGET_URL_WITH_PARAMETERS = 'https://website.local/target?tx_example%5Bid%5D=3&cHash=';
    private const EXTERNAL_URL = 'https://example.org/paper.pdf';

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
        $this->importCSVDataSet(__DIR__ . '/Fixtures/DownloadLinks.csv');
        $this->setUpFrontendRootPage(1, [
            'constants' => ['EXT:publications/Configuration/TypoScript/constants.typoscript'],
            'setup' => [
                'EXT:publications/Configuration/TypoScript/setup.typoscript',
                'EXT:publications/Tests/Functional/Frontend/Fixtures/Setup.typoscript',
            ],
        ]);
    }

    #[Test]
    public function bibtexExportContainsAbsoluteUrlsAndOmitsUnresolvableLinks(): void
    {
        $bibtex = $this->download('downloadBibtex');

        self::assertStringContainsString('web_url = {' . self::TARGET_URL . '}', $bibtex);
        self::assertStringContainsString('web_url2 = {' . self::TARGET_URL . '?', $bibtex);
        self::assertStringContainsString('file_url = {' . self::EXTERNAL_URL . '}', $bibtex);
        self::assertSame(1, substr_count($bibtex, 'web_url = '));
        self::assertStringNotContainsString('t3://', $bibtex);
    }

    #[Test]
    public function xmlExportIsWellFormedAndContainsAbsoluteUrls(): void
    {
        $references = [];
        foreach ((new SimpleXMLElement($this->download('downloadXml')))->reference as $reference) {
            $references[(string)$reference->citeid] = $reference;
        }

        $resolvable = $references['resolvable-links'];
        self::assertSame('Research & development', (string)$resolvable->title);
        self::assertSame(self::TARGET_URL, (string)$resolvable->web_url);
        self::assertStringStartsWith(self::TARGET_URL_WITH_PARAMETERS, (string)$resolvable->web_url2);
        self::assertSame(self::EXTERNAL_URL, (string)$resolvable->file_url);
        self::assertCount(0, $references['unresolvable-link']->web_url);
    }

    private function download(string $action): string
    {
        $request = (new InternalRequest(self::BASE_URL))
            ->withPageId(self::PAGE_PLUGIN)
            ->withQueryParameters([
                'tx_publications_pi1[controller]' => 'Publication',
                'tx_publications_pi1[action]' => $action,
            ]);
        $response = $this->executeFrontendSubRequest($request);
        $body = (string)$response->getBody();
        self::assertSame(200, $response->getStatusCode(), substr(strip_tags($body), 0, 1000));
        return $body;
    }
}
