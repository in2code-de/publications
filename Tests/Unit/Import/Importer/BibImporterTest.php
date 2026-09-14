<?php

declare(strict_types=1);

namespace In2code\Publications\Tests\Unit\Import\Importer;

use In2code\Publications\Import\Importer\BibImporter;
use PHPUnit\Framework\TestCase;

final class BibImporterTest extends TestCase
{
    public function testMapsLinksAndAccessDateWithoutSplittingUrls(): void
    {
        [$publication] = (new BibImporter())->convert(__DIR__ . '/Fixtures/links.bib');

        self::assertSame('https://example.org/article?ids=1,2', $publication['web_url'] ?? null);
        self::assertSame('2026-09-14', $publication['web_url_date'] ?? null);
        self::assertSame('fileadmin/publications/article.pdf', $publication['file_url'] ?? null);
        self::assertSame('12345', $publication['pmid'] ?? null);
        self::assertArrayNotHasKey('web_url2', $publication);
        foreach (['url', 'urldate', 'file'] as $source) {
            self::assertArrayNotHasKey($source, $publication);
        }
    }

    public function testPreservesNativeLinkFieldsAndPmid(): void
    {
        [, $publication] = (new BibImporter())->convert(__DIR__ . '/Fixtures/links.bib');

        self::assertSame('https://example.org/native', $publication['web_url'] ?? null);
        self::assertSame('https://example.org/alternative', $publication['web_url2'] ?? null);
        self::assertSame('2026-09', $publication['web_url_date'] ?? null);
        self::assertSame('fileadmin/publications/native.pdf', $publication['file_url'] ?? null);
        self::assertSame('67890', $publication['pmid'] ?? null);
    }

    public function testConvertsCompleteExampleCollection(): void
    {
        $publications = (new BibImporter())->convert(__DIR__ . '/Fixtures/biblatex-examples.bib');

        // The parser also returns @string definitions, which are not citations.
        $publications = array_values(array_filter($publications, static fn (array $entry): bool => $entry['bibtype'] !== 'string'));
        self::assertCount(92, $publications);
        $byCitation = array_column($publications, null, 'citeid');
        self::assertCount(92, $byCitation);
        foreach ($publications as $publication) {
            self::assertNotEmpty($publication['citeid']);
            self::assertNotEmpty($publication['bibtype']);
            self::assertArrayNotHasKey('citation-key', $publication);
            self::assertArrayNotHasKey('author', $publication);
        }

        $article = $byCitation['bertram'];
        self::assertSame('article', $article['bibtype']);
        self::assertSame('Gromov invariants for holomorphic maps on Riemann surfaces', $article['title']);
        self::assertSame('J.~Amer. Math. Soc.', $article['journaltitle']);
        self::assertSame('529-571', $article['pages']);
        self::assertSame(['Aaron', 'Richard'], array_column($article['authors'], 'first_name'));
        self::assertSame(['Bertram', 'Wentworth'], array_column($article['authors'], 'last_name'));

        $chapter = $byCitation['westfahl:space'];
        self::assertSame('incollection', $chapter['bibtype']);
        self::assertSame('The True Frontier', $chapter['title']);
        self::assertSame('westfahl:frontier', $chapter['crossref']);
        self::assertCount(1, $chapter['authors']);
        self::assertSame('Gary', $chapter['authors'][0]['first_name']);
        self::assertSame('Westfahl', $chapter['authors'][0]['last_name']);

        self::assertCount(7, $byCitation['aksin']['authors']);
        self::assertSame('Özge', $byCitation['aksin']['authors'][0]['first_name']);
        self::assertSame('Türkmen', $byCitation['aksin']['authors'][1]['last_name']);
    }

    public function testMapsPublicationFieldsAndPreservesExplicitType(): void
    {
        [$publication] = (new BibImporter())->convert(__DIR__ . '/Fixtures/publication.bib');

        self::assertSame('example-report', $publication['citeid']);
        self::assertSame('techreport', $publication['bibtype']);
        self::assertSame('Research report', $publication['type']);
        self::assertSame('An example publication', $publication['title']);
        self::assertSame('10.1234/example', $publication['doi']);
        self::assertSame('1234-5678', $publication['issn']);
        self::assertSame('978-3-16-148410-0', $publication['isbn']);
        self::assertSame('First note', $publication['miscellaneous']);
        self::assertSame('Second note', $publication['miscellaneous2']);
        foreach (['DOI', 'ISSN', 'ISBN', 'misc', 'misc2', 'Title'] as $source) {
            self::assertArrayNotHasKey($source, $publication);
        }
    }
}
