<?php

declare(strict_types=1);

namespace In2code\Publications\Tests\Unit\Import\Importer;

use In2code\Publications\Import\Importer\AbstractImporter;
use PHPUnit\Framework\TestCase;

final class AbstractImporterTest extends TestCase
{
    public function testSelfMappingsPreserveValuesWhileOtherFieldsAreRenamed(): void
    {
        $importer = new class extends AbstractImporter {
            protected array $additionalPublicationMapping = [
                'web_url' => 'web_url',
                'pmid' => 'pmid',
                'title' => 'title',
                'note' => 'note',
                'DOI' => 'doi'
            ];

            public function convert(string $filePath): array
            {
                throw new \LogicException('This test double only exposes field mapping.');
            }

            public function map(array $publications): array
            {
                return $this->fieldMapping($publications);
            }
        };

        self::assertSame([[
            'web_url' => 'https://example.org/article',
            'pmid' => '0',
            'title' => '',
            'note' => null,
            'doi' => '10.1234/example'
        ]], $importer->map([[
            'web_url' => 'https://example.org/article',
            'pmid' => '0',
            'title' => '',
            'note' => null,
            'DOI' => '10.1234/example'
        ]]));
        self::assertSame([[]], $importer->map([[]]));
    }
}
