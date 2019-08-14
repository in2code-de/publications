<?php
declare(strict_types=1);

namespace In2code\Publications\Utility;

/**
 * Class BibTexUtility
 */
class BibTexUtility
{

    /**
     * @var array
     */
    protected static $decoded = [
        'Ä',
        'Ö',
        'Ü',
        'ä',
        'ö',
        'ü',
        'ß',
        'ß',
        'ć',
        'í',
        'é',
        '—'
    ];

    /**
     * @var array
     */
    protected static $encoded = [
        '{\\"A}',
        '{\\"O}',
        '{\\"U}',
        '{\\"a}',
        '{\\"o}',
        '{\\"u}',
        '{\\"s}',
        '{\\ss}',
        '{\\c}',
        '{\\\'i}',
        '{\\\'e}',
        '{--}'
    ];

    /**
     * @param string $string
     * @return string
     */
    public static function encode(string $string): string
    {
        return str_replace(self::$decoded, self::$encoded, $string);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function decode(string $string): string
    {
        return str_replace(self::$encoded, self::$decoded, $string);
    }
}
