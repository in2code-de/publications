<?php
declare(strict_types=1);
namespace In2code\Publications\Utility;

use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/**
 * Class ConfigurationUtility
 */
class ConfigurationUtility
{
    /**
     * @param string $path
     * @return array|string
     */
    public static function getSetting(string $path)
    {
        $configurationManager = ObjectUtility::getObjectManager()->get(ConfigurationManagerInterface::class);
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
