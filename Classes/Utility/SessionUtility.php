<?php

declare(strict_types=1);

namespace In2code\Publications\Utility;

class SessionUtility
{
    /**
     * Save a value in a session cookie
     */
    public static function saveValueToSession(string $key, array $data): void
    {
        setcookie('publications_' . $key, json_encode($data), 0, '/');
    }

    /**
     * Read value from a session cookie
     */
    public static function getSessionValue(string $key): array
    {
        $_COOKIE['publications_' . $key] = $_COOKIE['publications_' . $key] ?? '';
        return (array)json_decode((string)$_COOKIE['publications_' . $key], true);
    }
}
