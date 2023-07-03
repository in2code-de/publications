<?php

declare(strict_types=1);

namespace In2code\Publications\Utility;

use TYPO3\CMS\Core\Localization\LanguageService;

class ObjectUtility
{
    /**
     * @return LanguageService
     */
    public static function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
