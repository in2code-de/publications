<?php
declare(strict_types=1);
namespace In2code\Publications\Utility;

/**
 * Class FrontendUserUtility
 */
class FrontendUserUtility
{

    /**
     * @param string $key
     * @param array $data
     * @return void
     */
    public static function saveValueToSession(string $key, array $data): void
    {
        ObjectUtility::getTyposcriptFrontendController()->fe_user->setKey('ses', $key . '_publications', $data);
    }

    /**
     * @param string $key
     * @return array
     */
    public static function getSessionValue(string $key): array
    {
        return (array)ObjectUtility::getTyposcriptFrontendController()->fe_user->getKey('ses', $key . '_publications');
    }
}
