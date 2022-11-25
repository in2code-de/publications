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
        '\\#%#','\\%#%','','','"','"','"',
        'ä','á','à','ã','â','å','ą','Ä','Á','À','Â','Ã','Å',
        'ř','ß','ß','ś','š','ş','Š','Ś','§',
        'ç','ć','č',
        'é','è','ê','ë','ę','ě','É','È','Ê','æ','Æ','œ','Œ',
        'í','ï','î','ı','ł','Ł','ń','ñ','ň',
        'ö','ó','ô','ø','ő','ŏ','Ö','Ó',
        'ü','ú','ų','ū','ů','û','Ü',
        'ý','ž','Ž','—','—','&','&',
        '$','_','†','†','«','»','„','“','„','“','‚','‘','´',
        '½','¼','…','…','®','™',
        '{','}','%','#'];

    /**
     * @var array
     * @comment protect first latex curly brackets, then remove all other curly brackets, replace patterns, restore latex curly brackets
     */
    protected static $decoding = [
        '\\{','\\}','{','}','\\dq','\'\'','``',
        '\\"a','\\\'a','\\`a','\\~a','\\^a','\\aa','\\ka','\\"A','\\\'A','\\`A','\\^A','\\~A','\\AA',
        '\\vr','\\"s','\\ss','\\\'s','\\vs','\\cs','\\vS','\\\'S','\\S',
        '\\cc','\\c','\\vc',
        '\\\'e','\\`e','\\^e','\\"e','\\ke','\\ve','\\\'E','\\`E','\\^E','\\ae','\\AE','\\oe','\\OE',
        '\\\'i','\\"i','\\^i','\\i','\\l','\\L','\\\'n','\\~n','\\vn',
        '\\"o','\\\'o','\\^o','\\o','\\Ho','\\uo','\\"O','\\\'O',
        '\\"u','\\\'u','\\ku','\\=u','\\ru','\\^u','\\"U',
        '\\\'y','\\vz','\\vZ','--','&ndash;','\\&','\&',
        '\$','\_','$\\dagger$','\ddag','\\flqq','\\frqq','&bdquo;','&ldquo;','\\glqq','\\grqq','\\glq','\\grq','\\textasciiacute',
        '\\textonehalf','\\textonequarter','[$\ldots$]','$\ldots$','\\texttrademark','\\textregistered',
        '\\#%#','\\%#%','\\%','\\#'];


    /**
     * @var array
     */
    protected static $encoded = [
        '','','','','\\dq','\'\'','``',
        '{\\"{a}','{\\\'{a}}','{\\`{a}}','{\\~{a}}','{\\^{a}}','{\\a{a}}','{\\k{a}}','{\\"{A}}','{\\\'{A}}','{\\`{A}}','{\\^{A}}','{\\~{A}}','{\\A{A}}',
        '{\\v{r}}','{\\"{s}}','{\\ss}','{\\\'s}','{\\v{s}}','{\\c{s}}','{\\v{S}}','{\\\'{S}}','{\\{S}}',
        '{\\c{c}}','{\\c}','{\\v{c}}',
        '{\\\'{e}}','{\\`{e}}','{\\^{e}}','{\\"{e}}','{\\k{e}}','{\\v{e}}','{\\\'{E}}','{\\`{E}}','{\\^{E}}','{\\a{e}}','{\\A{E}}','{\\o{e}}','{\\O{E}}',
        '{\\\'{i}}','{\\"{i}}','{\\^{i}}','{\\i}','{\\l}','{\\L}','{\\\'{n}}','{\\~{n}}','{\\v{n}}',
        '{\\"{o}}','{\\\'{o}}','{\\^{o}}','{\\o}','{\\H{o}}','{\\u{o}}','{\\"{O}}','{\\\'{O}}',
        '{\\"{u}}','{\\\'{u}}','{\\k{u}}','{\\={u}}','{\\r{u}}','{\\^{u}}','{\\"{U}}',
        '{\\\'{y}}','{\\v{z}}','{\\v{Z}}','{--}','&ndash;','{\\&}','\&',
        '\$','\_','$\\dagger$','\ddag','\\flqq','\\frqq','&bdquo;','&ldquo;','\\glqq','\\grqq','\\glq','\\grq','\\textasciiacute',
        '\\textonehalf','\\textonequarter','[$\ldots$]','$\ldots$','\\texttrademark','\\textregistered',
        '\\{','\\}','{\\%}','{\\#}'];

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
        return str_replace(self::$decoding, self::$decoded, $string);
    }
}
