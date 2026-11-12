<?php

declare(strict_types=1);

namespace In2code\Publications\Tests\Functional\Frontend;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class RecursiveRecordsTest extends FunctionalTestCase
{
    private const BASE_URL = 'https://website.local/';
    private const PAGE_RECURSIVE_WITHOUT_SUBPAGES = 10;
    private const PAGE_RECURSIVE_ONE_LEVEL = 11;

    protected array $testExtensionsToLoad = [
        'in2code/publications',
    ];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/publications/Tests/Functional/Frontend/Fixtures/Sites' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/RecursiveRecords.csv');
        $this->setUpFrontendRootPage(1, [
            'constants' => ['EXT:publications/Configuration/TypoScript/constants.typoscript'],
            'setup' => [
                'EXT:publications/Configuration/TypoScript/setup.typoscript',
                'EXT:publications/Tests/Functional/Frontend/Fixtures/Setup.typoscript',
            ],
        ]);
    }

    public static function recursiveRecordsDataProvider(): array
    {
        return [
            'storage page without subpages' => [
                self::PAGE_RECURSIVE_WITHOUT_SUBPAGES,
                ['Leaf storage publication'],
                ['Parent storage publication', 'First level publication', 'Second level publication'],
            ],
            'storage page with two levels of subpages' => [
                self::PAGE_RECURSIVE_ONE_LEVEL,
                ['Parent storage publication', 'First level publication'],
                ['Leaf storage publication', 'Second level publication'],
            ],
        ];
    }

    /**
     * @param list<string> $expectedTitles
     * @param list<string> $unexpectedTitles
     */
    #[Test]
    #[DataProvider('recursiveRecordsDataProvider')]
    public function listShowsPublicationsOfRecordsUpToRecursionDepth(
        int $pageUid,
        array $expectedTitles,
        array $unexpectedTitles
    ): void {
        $body = $this->renderPage($pageUid);

        foreach ($expectedTitles as $title) {
            self::assertStringContainsString($title, $body);
        }
        foreach ($unexpectedTitles as $title) {
            self::assertStringNotContainsString($title, $body);
        }
    }

    private function renderPage(int $pageUid): string
    {
        $response = $this->executeFrontendSubRequest((new InternalRequest(self::BASE_URL))->withPageId($pageUid));
        $body = (string)$response->getBody();
        self::assertSame(200, $response->getStatusCode(), substr(strip_tags($body), 0, 1000));
        return $body;
    }
}
