<?php
declare(strict_types=1);
namespace In2code\Publications\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Core\Localization\LanguageService;

/**
 * Class ObjectUtility
 */
class ObjectUtility
{

    /**
     * @return ObjectManager
     */
    public static function getObjectManager(): ObjectManager
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return TypoScriptFrontendController
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getTyposcriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * @return LanguageService
     */
    public static function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
