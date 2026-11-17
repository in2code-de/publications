<?php

declare(strict_types=1);

namespace In2code\Publications\Tests\Unit\Utility;

use In2code\Publications\Utility\BibTexUtility;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BibTexUtilityTest extends TestCase
{
    public function testEncodesAmpersandOnce(): void
    {
        self::assertSame('Research \\& development', BibTexUtility::encode('Research & development'));
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function specialCharactersDataProvider(): array
    {
        return [
            'double quote' => ['"', '{\\dq}'],
            'a with ring' => ['å', '{\\aa}'],
            'A with ring' => ['Å', '{\\AA}'],
            'sharp s' => ['ß', '{\\ss}'],
            'section sign' => ['§', '{\\S}'],
            'ae ligature' => ['æ', '{\\ae}'],
            'AE ligature' => ['Æ', '{\\AE}'],
            'oe ligature' => ['œ', '{\\oe}'],
            'OE ligature' => ['Œ', '{\\OE}'],
            'dotless i' => ['ı', '{\\i}'],
            'o with stroke' => ['ø', '{\\o}'],
            'u with ogonek' => ['ų', '\\k{u}'],
            'em dash' => ['—', '---'],
            'en dash' => ['–', '--'],
            'dagger' => ['†', '$\\dagger$'],
            'double dagger' => ['‡', '{\\ddag}'],
            'left guillemet' => ['«', '{\\flqq}'],
            'right guillemet' => ['»', '{\\frqq}'],
            'German opening double quote' => ['„', '{\\glqq}'],
            'German closing double quote' => ['“', '{\\grqq}'],
            'German opening single quote' => ['‚', '{\\glq}'],
            'German closing single quote' => ['‘', '{\\grq}'],
            'acute accent' => ['´', '{\\textasciiacute}'],
            'one half' => ['½', '{\\textonehalf}'],
            'one quarter' => ['¼', '{\\textonequarter}'],
            'ellipsis' => ['…', '$\\ldots$'],
            'registered sign' => ['®', '{\\textregistered}'],
            'trade mark sign' => ['™', '{\\texttrademark}'],
        ];
    }

    #[DataProvider('specialCharactersDataProvider')]
    public function testEncodesAndDecodesSpecialCharacters(string $character, string $latex): void
    {
        self::assertSame($latex, BibTexUtility::encode($character));
        self::assertSame($character, BibTexUtility::decode($latex));
    }

    public function testEncodesCommandsWithoutMergingThemWithFollowingLetters(): void
    {
        self::assertSame(
            '{\\flqq}M{\\o}ller{\\frqq}, C{\\ae}sar, {\\glqq}Zitat{\\grqq}',
            BibTexUtility::encode('«Møller», Cæsar, „Zitat“')
        );
    }

    public function testEncodesUrlWithoutLatexEscapingAndKeepsFieldDelimitersBalanced(): void
    {
        self::assertSame(
            'https://example.org/page?tx_example%5Bid%5D=3&cHash=a1#top',
            BibTexUtility::encodeUrl('https://example.org/page?tx_example%5Bid%5D=3&cHash=a1#top')
        );
        self::assertSame('https://example.org/%7Bid%7D.pdf', BibTexUtility::encodeUrl('https://example.org/{id}.pdf'));
    }

    public function testDecodesTexCommandWithoutReplacingLongerCommandNames(): void
    {
        self::assertSame('TeX: The Program', BibTexUtility::decode('{\\TeX}: The Program'));
        self::assertSame('The TeXbook', BibTexUtility::decode('The {\\TeX}book'));
        self::assertSame('TeX', BibTexUtility::decode('\\TeX'));
        self::assertSame('\\TeXcustom', BibTexUtility::decode('\\TeXcustom'));
    }

    public function testDecodesQuoteArgumentsWithNestedGroupsAndAccents(): void
    {
        self::assertSame('"Intentionalität"', BibTexUtility::decode('\\mkbibquote{Intentionalit{\\"a}t}'));
        self::assertSame('Before "some grouped text" after', BibTexUtility::decode('Before \\mkbibquote{some {grouped} text} after'));
        self::assertSame('"literal {braces}"', BibTexUtility::decode('\\mkbibquote{literal \\{braces\\}}'));
        self::assertSame('""', BibTexUtility::decode('\\mkbibquote{}'));
    }
}
