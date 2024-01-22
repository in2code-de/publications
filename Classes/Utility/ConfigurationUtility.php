<?php

declare(strict_types=1);

namespace In2code\Publications\Utility;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class ConfigurationUtility
 */
class ConfigurationUtility
{
    /**
     * @param string $path
     * @return array|mixed|string
     * @throws \TYPO3\CMS\Extbase\Object\Exception
     */
    public static function getSetting(string $path)
    {
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);
        $settings = (array)$configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'publications'
        );
        try {
            return ArrayUtility::getValueByPath($settings, $path, '.');
        } catch (\Exception $exception) {
            unset($exception);
        }
        return '';
    }
}
