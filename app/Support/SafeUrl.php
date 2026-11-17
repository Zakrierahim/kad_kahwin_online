<?php

namespace App\Support;

final class SafeUrl
{
    public static function https(?string $url): ?string
    {
        if (! is_string($url)) {
            return null;
        }

        $url = trim($url);

        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        if ($scheme !== 'https') {
            return null;
        }

        return $url;
    }
}
