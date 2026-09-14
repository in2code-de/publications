<?php

declare(strict_types=1);

namespace In2code\Publications\Tests\Unit\Import\Importer;

use In2code\Publications\Import\Importer\XmlImporter;
use PHPUnit\Framework\TestCase;

final class XmlImporterTest extends TestCase
{
    public function testMapsFieldsAndPreservesPublicationLinks(): void
    {
        $publications = (new XmlImporter())->convert(__DIR__ . '/Fixtures/publications.xml');

        self::assertCount(3, $publications);
        $publication = $publications[0];
        foreach ([
            'citeid' => 'example-article',
            'bibtype' => 'article',
            'title' => 'Research & publications',
            'abstract' => 'Text with <em>markup</em> & special characters.',
            'doi' => '10.1234/example',
            'isbn' => '978-3-16-148410-0',
            'issn' => '1234-5678',
            'miscellaneous' => 'First note',
            'miscellaneous2' => 'Second note',
            'web_url' => 'https://example.org/article?ids=1,2',
            'web_url2' => 'https://example.org/alternative',
            'web_url_date' => '2026-09-14',
            'file_url' => 'https://example.org/article.pdf',
            'pmid' => '12345'
        ] as $field => $expected) {
            self::assertSame($expected, $publication[$field], $field);
        }
        foreach (['DOI', 'ISBN', 'ISSN', 'misc', 'misc2'] as $source) {
            self::assertArrayNotHasKey($source, $publication);
        }
    }

    public function testMapsSingleAndMultipleAuthorsAndAllowsMissingAuthors(): void
    {
        $publications = (new XmlImporter())->convert(__DIR__ . '/Fixtures/publications.xml');

        self::assertSame([['first_name' => 'Jane', 'last_name' => 'Doe']], $publications[0]['authors']);
        self::assertSame([
            ['first_name' => 'Jörg', 'last_name' => 'Müller'],
            ['first_name' => 'John', 'last_name' => 'Smith']
        ], $publications[1]['authors']);
        self::assertSame('example-anonymous', $publications[2]['citeid']);
        self::assertArrayNotHasKey('authors', $publications[2]);
    }
}
