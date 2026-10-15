<?php

declare(strict_types=1);

namespace In2code\Publications\Tests\Functional\Frontend;

use DOMDocument;
use DOMXPath;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Renders the list plugin with unpublished publications in every citation style and compares the citations as the
 * browser shows them: tags are removed, line breaks are rendered as " | " and whitespace is collapsed.
 */
final class CitestyleTest extends FunctionalTestCase
{
    private const LANGUAGE_GERMAN = 'de';
    private const LANGUAGE_ENGLISH = 'en';

    /**
     * Page uid with the list plugin configured for the citation style
     */
    private const PAGE_BY_CITESTYLE = [
        0 => 10,
        1 => 11,
        4 => 12,
        6 => 13,
        9 => 14,
    ];

    /**
     * Base URL of the site language, see Fixtures/Sites/main/config.yaml
     */
    private const LANGUAGE_BASES = [
        self::LANGUAGE_GERMAN => 'https://website.local/',
        self::LANGUAGE_ENGLISH => 'https://website.local/en/',
    ];

    protected array $testExtensionsToLoad = [
        'in2code/publications',
    ];

    protected array $pathsToLinkInTestInstance = [
        'typo3conf/ext/publications/Tests/Functional/Frontend/Fixtures/Sites' => 'typo3conf/sites',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->importCSVDataSet(__DIR__ . '/Fixtures/UnpublishedPublications.csv');
        $this->setUpFrontendRootPage(1, [
            'constants' => ['EXT:publications/Configuration/TypoScript/constants.typoscript'],
            'setup' => [
                'EXT:publications/Configuration/TypoScript/setup.typoscript',
                'EXT:publications/Tests/Functional/Frontend/Fixtures/Setup.typoscript',
            ],
        ]);
    }

    public static function unpublishedPublicationsDataProvider(): array
    {
        $ieeeGerman = [
            '[English] U. Example, "A synthetic unpublished work", Submitted for review, 2025.',
            'S. Sample, "A synthetic unpublished work with a dated status", Manuscript in preparation, Mrz. 2024.',
            'U. Example and S. Sample, "A synthetic unpublished work without status", nicht veröffentlicht.',
        ];
        $ieeeEnglish = [
            '[English] U. Example, "A synthetic unpublished work", Submitted for review, 2025.',
            'S. Sample, "A synthetic unpublished work with a dated status", Manuscript in preparation, Mar. 2024.',
            'U. Example and S. Sample, "A synthetic unpublished work without status", unpublished.',
        ];
        $abstract = ' | | Abstract: | A synthetic abstract.';

        return [
            'default style, German' => [0, self::LANGUAGE_GERMAN, [
                '[English] Example, Uma | A synthetic unpublished work | Submitted for review | 2025',
                'Sample, Sam | A synthetic unpublished work with a dated status | Manuscript in preparation. | März 2024',
                'Example, Uma; Sample, Sam | A synthetic unpublished work without status',
            ]],
            'default style, English' => [0, self::LANGUAGE_ENGLISH, [
                '[English] Example, Uma | A synthetic unpublished work | Submitted for review | 2025',
                'Sample, Sam | A synthetic unpublished work with a dated status | Manuscript in preparation. | March 2024',
                'Example, Uma; Sample, Sam | A synthetic unpublished work without status',
            ]],
            'IEEE, German' => [1, self::LANGUAGE_GERMAN, $ieeeGerman],
            'IEEE, English' => [1, self::LANGUAGE_ENGLISH, $ieeeEnglish],
            'APA, German' => [4, self::LANGUAGE_GERMAN, [
                '[English] Example, U. (2025). A synthetic unpublished work [Submitted for review]. Example University.',
                'Sample, S. (2024). A synthetic unpublished work with a dated status [Manuscript in preparation].',
                'Example, U., & Sample, S. (o. J.). A synthetic unpublished work without status [Nicht veröffentlicht].',
            ]],
            'APA, English' => [4, self::LANGUAGE_ENGLISH, [
                '[English] Example, U. (2025). A synthetic unpublished work [Submitted for review]. Example University.',
                'Sample, S. (2024). A synthetic unpublished work with a dated status [Manuscript in preparation].',
                'Example, U., & Sample, S. (n.d.). A synthetic unpublished work without status [Unpublished].',
            ]],
            'IEEE with abstract, German' => [6, self::LANGUAGE_GERMAN, [$ieeeGerman[0] . $abstract] + $ieeeGerman],
            'IEEE with abstract, English' => [6, self::LANGUAGE_ENGLISH, [$ieeeEnglish[0] . $abstract] + $ieeeEnglish],
            'Vancouver-based, German' => [9, self::LANGUAGE_GERMAN, [
                'Example U. A synthetic unpublished work. 2025. Submitted for review.',
                'Sample S. A synthetic unpublished work with a dated status. 2024 Mrz. Manuscript in preparation.',
                'Example U, Sample S. A synthetic unpublished work without status.',
            ]],
            'Vancouver-based, English' => [9, self::LANGUAGE_ENGLISH, [
                'Example U. A synthetic unpublished work. 2025. Submitted for review.',
                'Sample S. A synthetic unpublished work with a dated status. 2024 Mar. Manuscript in preparation.',
                'Example U, Sample S. A synthetic unpublished work without status.',
            ]],
        ];
    }

    #[Test]
    #[DataProvider('unpublishedPublicationsDataProvider')]
    public function rendersUnpublishedPublications(int $citestyle, string $language, array $expectedCitations): void
    {
        self::assertSame($expectedCitations, $this->renderCitations($citestyle, $language));
    }

    /**
     * @return string[]
     */
    private function renderCitations(int $citestyle, string $language): array
    {
        $request = (new InternalRequest(self::LANGUAGE_BASES[$language]))
            ->withPageId(self::PAGE_BY_CITESTYLE[$citestyle]);
        $response = $this->executeFrontendSubRequest($request);
        self::assertSame(200, $response->getStatusCode(), substr(strip_tags((string)$response->getBody()), 0, 1000));

        $document = new DOMDocument();
        $document->loadHTML(
            '<?xml encoding="utf-8"?>' . (string)$response->getBody(),
            LIBXML_NOERROR | LIBXML_NOWARNING
        );
        $xpath = new DOMXPath($document);
        foreach (iterator_to_array($document->getElementsByTagName('br')) as $lineBreak) {
            $lineBreak->parentNode->replaceChild($document->createTextNode(' | '), $lineBreak);
        }

        $citations = [];
        foreach ($xpath->query('//div[@class="tx-publications"]//div[@style="display: flex;"]/div[last()]') as $node) {
            $citations[] = trim(preg_replace('/\s+/u', ' ', $node->textContent));
        }
        return $citations;
    }
}
