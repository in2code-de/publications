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
    protected static $decoded = array (
        '\\#%#','\\%#%','','','"','"','"',
        'ä','á','à','ã','â','å','ą','Ä','Á','À','Â','Ã','Å',
        'ç','ć','č',
        'é','è','ê','ë','ę','ě','É','æ','Æ','œ','Œ',
        'í','ï','î','ı','ł','Ł','ń','ñ','ň',
        'ö','ó','ô','ø','ő','ŏ','Ö','‪Ó',
        'ř','ß','ß','ś','š','Š','Ś','§',
        'ü','ú','ų','ū','ů','û','Ü',
        'ý','ž','Ž','—','—','&','&',
        '$','_','†','†','«','»','„','“','„','“','‚','‘','´',
        '½','¼','…','…','®','™',
        '{','}','%','#');

    /**
     * @var array
     * @comment protect first latex curly brackets, then remove all other curly brackets, replace patterns, restore latex curly brackets
     */
    protected static $decoding = array(
      '\\{','\\}','{','}','\\dq','\'\'','``',
      '\\"a','\\\'a','\\`a','\\~a','\\^a','\\aa','\\ka','\\"A','\\\'A','\\`A','\\^A','\\~A','\\AA',
      '\\cc','\\c','\\vc',
      '\\\'e','\\`e','\\^e','\\"e','\\ke','\\ve','\\\'E','\\ae','\\AE','\\oe','\\OE',
      '\\\'i','\\"i','\\^i','\\i','\\l','\\L','\\\'n','\\~n','\\vn',
      '\\"o','\\\'o','\\^o','\\o','\\Ho','\\uo','\\"O','\\\'O',
      '\\vr','\\"s','\\ss','\\\'s','\\vs','\\vS','\\\'S','\\S',
      '\\"u','\\\'u','\\ku','\\=u','\\ru','\\^u','\\"U',
      '\\\'y','\\vz','\\vZ','--','&ndash;','\\&','\&',
      '\$','\_','$\\dagger$','\ddag','\\flqq','\\frqq','&bdquo;','&ldquo;','\\glqq','\\grqq','\\glq','\\grq','\\textasciiacute',
      '\\textonehalf','\\textonequarter','[$\ldots$]','$\ldots$','\\texttrademark','\\textregistered',
      '\\#%#','\\%#%','\\%','\\#');


  /**
   * @var array
   */
  protected static $encoded = array(
    '','','','','\\dq','\'\'','``',
    '{\\"{a}','{\\\'{a}}','{\\`{a}}','{\\~{a}}','{\\^{a}}','{\\a{a}}','{\\k{a}}','{\\"{A}}','{\\\'{A}}','{\\`{A}}','{\\^{A}}','{\\~{A}}','{\\A{A}}',
    '{\\c{c}}',{'\\c}','{\\v{c}}',
    '{\\\'{e}}','{\\`{e}}','{\\^{e}}','{\\"{e}}','{\\k{e}}','{\\v{e}}','{\\\'{E}}','{\\a{e}}','{\\A{E}}','{\\o{e}}','{\\O{E}}',
    '{\\\'{i}}','{\\"{i}}','{\\^{i}}','{\\i}','{\\l}','{\\L}','{\\\'{n}}','{\\~{n}}','{\\v{n}}',
    '{\\"{o}}','{\\\'{o}}','{\\^{o}}','{\\o}','{\\H{o}}','{\\u{o}}','{\\"{O}}','{\\\'{O}}',
    '{\\v{r}}','{\\"{s}}','{\\ss}','{\\\'s}','{\\v{s}}','{\\v{S}}','{\\\'{S}}','{\\{S}}',
    '{\\"{u}}','{\\\'{u}}','{\\k{u}}','{\\={u}}','{\\r{u}}','{\\^{u}}','{\\"{U}}',
    '{\\\'{y}}','{\\v{z}}','{\\v{Z}}','{--}','&ndash;','{\\&}','\&',
    '\$','\_','$\\dagger$','\ddag','\\flqq','\\frqq','&bdquo;','&ldquo;','\\glqq','\\grqq','\\glq','\\grq','\\textasciiacute',
    '\\textonehalf','\\textonequarter','[$\ldots$]','$\ldots$','\\texttrademark','\\textregistered',
    '\\{','\\}','{\\%}','{\\#}');

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
        return str_replace(self::$encoded, self::$decoding, $string);
    }
}
