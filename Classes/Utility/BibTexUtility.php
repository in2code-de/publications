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
        'ä',
        'á',
        'à',
        'ã',
        'â',
        'å',
        'ą',
        'æ',
        'Ä',
        'Á',
        'À',
        'Â',
        'Ã',
        'Æ',
        'ć',
        'ç',
        'ç',
        'č',
        'é',
        'è',
        'ê',
        'ë',
        'ę',
        'ě',
        'É',
        'í',
        'ï',
        'î',
        'ł',
        'Ł',
        'ń',
        'ñ',
        'ň',
        'ö',
        'ó',
        'ô',
        'ø',
        'Ö',
        '‪Ó',
        'ř',
        'ß',
        'ß',
        'ś',
        'š',
        'Š',
        'Ś',
        '§',
        'ü',
        'ú',
        'ų',
        'ū',
        'ů',
        'û',
        'Ü',
        'ý',
        'ž',
        'Ž',
        '—',
        '–',
        '&',
        '&',
        '†',
        '"',
        '"',
        '"',
        '«',
        '»',
        '„',
        '“',
        '„',
        '“',
        '‚',
        '‘',
        '½',
        '¼',
        '…',
        '…'
    ];

    /**
     * @var array
     */
    protected static $encoded = [
        '{\\"a}',
        '{\\\'a}',
        '{\\`a}',
        '{\\~a}',
        '{\\^a}',
        '{\\aa}',
        '{\\k{a}}',
        '{\\ae}',
        '{\\"A}',
        '{\\\'A}',
        '{\\`A}',
        '{\\^A}',
        '{\\~A}',
        '{\\AE}',
        '{\\c}',
        '{\\c{c}}',
        '\\c{c}',
        '{\\v{c}}',
        '{\\\'e}',
        '{\\`e}',
        '{\\^e}',
        '{\\"e}',
        '{\\k{e}}',
        '{\\v{e}}',
        '{\\\'E}',
        '{\\\'i}',
        '{\\"i}',
        '{\\^i}',
        '{\\l}',
        '{\\L}',
        '{\\\'n}',
        '{\\~n}',
        '{\\v{n}}',
        '{\\"o}',
        '{\\\'o}',
        '{\\^o}',
        '{\\o}',
        '{\\"O}',
        '{\\\'O}',
        '{\\v{r}}',
        '{\\"s}',
        '{\\ss}',
        '{\\\'s}',
        '{\\v{s}}',
        '{\\v{S}}',
        '{\\\'S}',
        '{\\S}',
        '{\\"u}',
        '{\\\'u}',
        '{\\k{u}}',
        '{\\={u}}',
        '{\\r{u}}',
        '{\\^u}',
        '{\\"U}',
        '{\\\'y}',
        '{\\v{z}}',
        '{\\v{Z}}',
        '{--}',
        '&ndash;',
        '{\\&}',
        '\&',
        '$\\dagger$',
        '{\\dq}',
        '\'\'',
        '``',
        '{\\flqq}',
        '{\\frqq}',
        '&bdquo;',
        '&ldquo;',
        '{\\glqq}',
        '{\\grqq}',
        '{\\glq}',
        '{\\grq}',
        '{\\textonehalf}',
        '{\\textonequarter}',
        '[$\ldots$]',
        '$\ldots$'
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
