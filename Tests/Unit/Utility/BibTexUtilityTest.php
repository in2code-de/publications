<?php

declare(strict_types=1);

namespace In2code\Publications\Tests\Unit\Utility;

use In2code\Publications\Utility\BibTexUtility;
use PHPUnit\Framework\TestCase;

final class BibTexUtilityTest extends TestCase
{
    public function testDecodesQuoteArgumentsWithNestedGroupsAndAccents(): void
    {
        self::assertSame('"Intentionalität"', BibTexUtility::decode('\\mkbibquote{Intentionalit{\\"a}t}'));
        self::assertSame('Before "some grouped text" after', BibTexUtility::decode('Before \\mkbibquote{some {grouped} text} after'));
        self::assertSame('"literal {braces}"', BibTexUtility::decode('\\mkbibquote{literal \\{braces\\}}'));
        self::assertSame('""', BibTexUtility::decode('\\mkbibquote{}'));
    }
}
