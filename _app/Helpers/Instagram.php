<?php

declare(strict_types=1);

namespace App\Helpers;

final class Instagram
{
    private const CACHE_TTL = 3600;
    private const STALE_TTL = 86400;

    public function __construct(
        private readonly string $accessToken,
        private readonly string $apiVersion = 'v25.0',
        private readonly string $cachePath = __DIR__ . '/../../cache/instagram-feed.json'
    ) {
    }

    public function getRecent(int $limit = 9): array
    {
        if ($this->accessToken === '') {
            return [];
        }

        $limit = max(1, min($limit, 20));
        $cached = $this->readCache();
        if ($cached && time() - $cached['cached_at'] < self::CACHE_TTL) {
            return array_slice($cached['posts'], 0, $limit);
        }

        $posts = $this->fetchRecent();
        if ($posts !== null) {
            $this->writeCache($posts);

            return array_slice($posts, 0, $limit);
        }

        if ($cached && time() - $cached['cached_at'] < self::STALE_TTL) {
            return array_slice($cached['posts'], 0, $limit);
        }

        return [];
    }

    private function fetchRecent(): ?array
    {
        if (!function_exists('curl_init')) {
            return null;
        }

        $version = preg_match('/^v\d+\.\d+$/', $this->apiVersion) ? $this->apiVersion : 'v25.0';
        $query = http_build_query(
            ['fields' => 'id,media_type,media_url,thumbnail_url,permalink', 'limit' => 25],
            '',
            '&',
            PHP_QUERY_RFC3986
        );
        $request = curl_init('https://graph.instagram.com/' . $version . '/me/media?' . $query);
        if ($request === false) {
            return null;
        }

        curl_setopt_array($request, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Authorization: Bearer ' . $this->accessToken,
            ],
        ]);
        $response = curl_exec($request);
        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
        curl_close($request);

        if (!is_string($response) || $status !== 200) {
            error_log('Instagram feed: falha ao consultar a API da Meta (HTTP ' . $status . ').');

            return null;
        }

        $payload = json_decode($response, true);
        if (!is_array($payload) || !isset($payload['data']) || !is_array($payload['data'])) {
            return null;
        }

        $posts = [];
        foreach ($payload['data'] as $media) {
            if (!is_array($media)) {
                continue;
            }

            $type = $media['media_type'] ?? '';
            $image = $type === 'VIDEO'
                ? ($media['thumbnail_url'] ?? '')
                : ($media['media_url'] ?? $media['thumbnail_url'] ?? '');
            $link = $media['permalink'] ?? '';
            $linkHost = is_string($link) ? parse_url($link, PHP_URL_HOST) : null;

            if (!is_string($image) || !filter_var($image, FILTER_VALIDATE_URL)
                || parse_url($image, PHP_URL_SCHEME) !== 'https'
                || !is_string($link) || !filter_var($link, FILTER_VALIDATE_URL)
                || parse_url($link, PHP_URL_SCHEME) !== 'https'
                || !in_array($linkHost, ['instagram.com', 'www.instagram.com'], true)) {
                continue;
            }

            $posts[] = ['image' => $image, 'link' => $link];
        }

        return $posts;
    }

    private function readCache(): ?array
    {
        if (!is_file($this->cachePath) || !is_readable($this->cachePath)) {
            return null;
        }

        $contents = file_get_contents($this->cachePath);
        $cached = is_string($contents) ? json_decode($contents, true) : null;

        if (!is_array($cached)
            || ($cached['token_hash'] ?? null) !== hash('sha256', $this->accessToken)
            || ($cached['version'] ?? null) !== $this->apiVersion
            || !isset($cached['cached_at'], $cached['posts'])
            || !is_int($cached['cached_at'])
            || !is_array($cached['posts'])) {
            return null;
        }

        return $cached;
    }

    private function writeCache(array $posts): void
    {
        $directory = dirname($this->cachePath);
        if (!is_dir($directory) || !is_writable($directory)) {
            return;
        }

        $json = json_encode([
            'token_hash' => hash('sha256', $this->accessToken),
            'version' => $this->apiVersion,
            'cached_at' => time(),
            'posts' => $posts,
        ], JSON_UNESCAPED_SLASHES);
        if (!is_string($json)) {
            return;
        }

        $temporaryFile = tempnam($directory, 'instagram-');
        if ($temporaryFile === false) {
            return;
        }

        if (file_put_contents($temporaryFile, $json) === false || !rename($temporaryFile, $this->cachePath)) {
            unlink($temporaryFile);
        }
    }
}
