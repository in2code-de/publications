<?php

declare(strict_types=1);

namespace In2code\Publications\Utility;

class DoiUtility
{
    public const RESOLVER_URL = 'https://doi.org/';

    protected const PREFIX_PATTERN = '~^(?:\s+|(?:https?://)?(?:dx\.|www\.)?doi\.org/|doi\s*[:=]?|\\\\url\b)+~i';
    protected const BRACES_PATTERN = '~^\{(.*)\}$~s';
    protected const VALID_PATTERN = '~^10\.\d{4,9}(?:\.\d+)*/\S+$~';

    public static function normalize(string $doi): string
    {
        $normalized = self::removePrefixes($doi);
        if (preg_match(self::BRACES_PATTERN, $normalized, $matches) === 1) {
            $normalized = self::removePrefixes($matches[1]);
        }
        return $normalized;
    }

    public static function isValid(string $doi): bool
    {
        return preg_match(self::VALID_PATTERN, $doi) === 1;
    }

    public static function getUrl(string $doi): string
    {
        $url = '';
        $normalized = self::normalize($doi);
        if (self::isValid($normalized)) {
            $url = self::RESOLVER_URL . implode('/', array_map('rawurlencode', explode('/', $normalized)));
        }
        return $url;
    }

    protected static function removePrefixes(string $doi): string
    {
        return trim(preg_replace(self::PREFIX_PATTERN, '', $doi));
    }
}
