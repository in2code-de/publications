<?php

declare(strict_types=1);

namespace In2code\Publications\Tests\Unit\Utility;

use In2code\Publications\Utility\DoiUtility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DoiUtilityTest extends TestCase
{
    private const DOI = '10.1234/example';
    private const SICI_DOI = '10.1002/(SICI)1097-4636(199706)35:4<419::AID-JBM1>3.0.CO;2-K';

    /**
     * @return array<string, array{string, string}>
     */
    public static function normalizeDataProvider(): array
    {
        return [
            'bare DOI' => [self::DOI, self::DOI],
            'surrounding whitespace' => [' ' . self::DOI . PHP_EOL, self::DOI],
            'resolver URL' => ['https://doi.org/' . self::DOI, self::DOI],
            'legacy resolver URL with http' => ['http://dx.doi.org/' . self::DOI, self::DOI],
            'resolver URL with www' => ['https://www.doi.org/' . self::DOI, self::DOI],
            'resolver host without scheme' => ['doi.org/' . self::DOI, self::DOI],
            'colon prefix' => ['DOI:' . self::DOI, self::DOI],
            'colon prefix with space' => ['doi: ' . self::DOI, self::DOI],
            'equals sign prefix' => ['doi=' . self::DOI, self::DOI],
            'space prefix' => ['DOI ' . self::DOI, self::DOI],
            'LaTeX url command' => ['\\url{' . self::DOI . '}', self::DOI],
            'LaTeX url command with resolver URL' => ['\\url{https://doi.org/' . self::DOI . '}', self::DOI],
            'combined prefixes' => ['DOI: https://doi.org/' . self::DOI, self::DOI],
            'foreign URL' => ['https://example.org/' . self::DOI, 'https://example.org/' . self::DOI],
            'empty value' => ['', ''],
        ];
    }

    #[DataProvider('normalizeDataProvider')]
    public function testNormalizeRemovesPrefixes(string $doi, string $expected): void
    {
        self::assertSame($expected, DoiUtility::normalize($doi));
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function isValidDataProvider(): array
    {
        return [
            'bare DOI' => [self::DOI, true],
            'registrant code with subdivision' => ['10.1000.10/123', true],
            'SICI DOI with special characters' => [self::SICI_DOI, true],
            'empty value' => ['', false],
            'registrant code too short' => ['10.1/x', false],
            'text starting with 10.' => ['10. Auflage', false],
            'whitespace in suffix' => ['10.1234/an example', false],
            'several lines' => [self::DOI . PHP_EOL . '10.1234/other', false],
            'resolver URL' => ['https://doi.org/' . self::DOI, false],
        ];
    }

    #[DataProvider('isValidDataProvider')]
    public function testIsValidChecksDoiSyntax(string $doi, bool $expected): void
    {
        self::assertSame($expected, DoiUtility::isValid($doi));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function getUrlDataProvider(): array
    {
        return [
            'bare DOI' => [self::DOI, 'https://doi.org/' . self::DOI],
            'legacy resolver URL' => ['https://dx.doi.org/' . self::DOI, 'https://doi.org/' . self::DOI],
            'SICI DOI' => [
                self::SICI_DOI,
                'https://doi.org/10.1002/%28SICI%291097-4636%28199706%2935%3A4%3C419%3A%3AAID-JBM1%3E3.0.CO%3B2-K',
            ],
            'fragment and query characters' => ['10.1234/a#b?c', 'https://doi.org/10.1234/a%23b%3Fc'],
            'invalid value' => ['10. Auflage', ''],
            'several lines' => ['https://dx.doi.org/' . self::DOI . PHP_EOL . 'doi:10.1234/other', ''],
            'empty value' => ['', ''],
        ];
    }

    #[DataProvider('getUrlDataProvider')]
    public function testGetUrlBuildsEncodedResolverUrl(string $doi, string $expected): void
    {
        self::assertSame($expected, DoiUtility::getUrl($doi));
    }
}
