<?php
declare(strict_types=1);
namespace In2code\Publications\Utility;

/**
 * Class SessionUtility
 */
class SessionUtility
{
    /**
     * Save a value in a session cookie
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public static function saveValueToSession(string $key, array $data)
    {
        setcookie('publications_' . $key, json_encode($data), 0, '/');
    }

    /**
     * Read value from a session cookie
     *
     * @param string $key
     * @return array
     */
    public static function getSessionValue(string $key): array
    {
        return (array)json_decode((string)$_COOKIE['publications_' . $key], true);
    }
}
