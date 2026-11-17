<?php

namespace App\Support;

final class YouTube
{
    public static function videoId(?string $url): ?string
    {
        $url = SafeUrl::https($url);

        if ($url === null) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $host = preg_replace('/^www\./', '', $host) ?? $host;

        if (! in_array($host, ['youtube.com', 'm.youtube.com', 'music.youtube.com', 'youtu.be', 'youtube-nocookie.com'], true)) {
            return null;
        }

        $path = (string) parse_url($url, PHP_URL_PATH);

        if ($host === 'youtu.be') {
            return self::id(trim($path, '/'));
        }

        parse_str((string) parse_url($url, PHP_URL_QUERY), $params);

        if (is_string($params['v'] ?? null)) {
            return self::id($params['v']);
        }

        if (preg_match('#/(?:embed|shorts|live)/([A-Za-z0-9_-]{11})(?:/|$)#', $path, $matches) === 1) {
            return $matches[1];
        }

        return null;
    }

    private static function id(string $value): ?string
    {
        $value = explode('/', $value)[0];

        return preg_match('/^[A-Za-z0-9_-]{11}$/', $value) === 1 ? $value : null;
    }
}
