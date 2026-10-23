<?php

declare(strict_types=1);

namespace App\Helpers;

final class InstagramToken
{
    private const REFRESH_BEFORE_EXPIRY = 14 * 86400;
    private const RETRY_AFTER_FAILURE = 86400;

    public function __construct(
        private readonly string $initialToken,
        private readonly string $statePath = __DIR__ . '/../Config/.instagram-token.json'
    ) {
    }

    public function get(): string
    {
        if ($this->initialToken === '') {
            return '';
        }

        $state = $this->readState();
        if (!$this->needsRefresh($state)) {
            return $state['access_token'];
        }

        $lockPath = $this->statePath . '.lock';
        if (!is_writable(dirname($lockPath)) || (is_file($lockPath) && !is_writable($lockPath))) {
            error_log('Instagram: diretório do token sem permissão de escrita.');

            return $state['access_token'] ?? $this->initialToken;
        }

        $lock = fopen($lockPath, 'c');
        if ($lock === false) {
            error_log('Instagram: não foi possível abrir o arquivo de bloqueio do token.');

            return $state['access_token'] ?? $this->initialToken;
        }

        if (!flock($lock, LOCK_EX | LOCK_NB)) {
            fclose($lock);

            return $state['access_token'] ?? $this->initialToken;
        }

        try {
            $state = $this->readState();
            if (!$this->needsRefresh($state)) {
                return $state['access_token'];
            }

            $currentToken = $state['access_token'] ?? $this->initialToken;
            $refreshed = $this->refresh($currentToken);
            if ($refreshed !== null) {
                $newState = [
                    'source_hash' => hash('sha256', $this->initialToken),
                    'access_token' => $refreshed['access_token'],
                    'expires_at' => time() + $refreshed['expires_in'],
                    'retry_at' => 0,
                ];
                if ($this->writeState($newState)) {
                    return $newState['access_token'];
                }

                error_log('Instagram: token renovado, mas não foi possível salvá-lo.');

                return $currentToken;
            }

            $this->writeState([
                'source_hash' => hash('sha256', $this->initialToken),
                'access_token' => $currentToken,
                'expires_at' => $state['expires_at'] ?? 0,
                'retry_at' => time() + self::RETRY_AFTER_FAILURE,
            ]);

            return $currentToken;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    private function needsRefresh(?array $state): bool
    {
        if ($state === null) {
            return true;
        }

        $now = time();

        return $now >= $state['retry_at']
            && ($state['expires_at'] === 0 || $state['expires_at'] - $now <= self::REFRESH_BEFORE_EXPIRY);
    }

    private function readState(): ?array
    {
        if (!is_file($this->statePath) || !is_readable($this->statePath)) {
            return null;
        }

        $contents = file_get_contents($this->statePath);
        $state = is_string($contents) ? json_decode($contents, true) : null;
        if (!is_array($state)
            || ($state['source_hash'] ?? null) !== hash('sha256', $this->initialToken)
            || !is_string($state['access_token'] ?? null)
            || $state['access_token'] === ''
            || !is_int($state['expires_at'] ?? null)
            || !is_int($state['retry_at'] ?? null)) {
            return null;
        }

        return $state;
    }

    private function refresh(string $token): ?array
    {
        if (!function_exists('curl_init')) {
            error_log('Instagram: extensão cURL indisponível para renovar o token.');

            return null;
        }

        $url = 'https://graph.instagram.com/refresh_access_token?' . http_build_query([
            'grant_type' => 'ig_refresh_token',
            'access_token' => $token,
        ], '', '&', PHP_QUERY_RFC3986);
        $request = curl_init($url);
        if ($request === false) {
            return null;
        }

        curl_setopt_array($request, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
        ]);
        $response = curl_exec($request);
        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);
        curl_close($request);

        if (!is_string($response) || $status !== 200) {
            error_log('Instagram: falha na renovação do token (HTTP ' . $status . ').');

            return null;
        }

        $payload = json_decode($response, true);
        if (!is_array($payload)
            || !is_string($payload['access_token'] ?? null)
            || $payload['access_token'] === ''
            || !is_int($payload['expires_in'] ?? null)
            || $payload['expires_in'] < 86400) {
            error_log('Instagram: resposta inválida ao renovar o token.');

            return null;
        }

        return [
            'access_token' => $payload['access_token'],
            'expires_in' => $payload['expires_in'],
        ];
    }

    private function writeState(array $state): bool
    {
        $directory = dirname($this->statePath);
        if (!is_dir($directory) || !is_writable($directory)) {
            return false;
        }

        $contents = json_encode($state, JSON_UNESCAPED_SLASHES);
        if (!is_string($contents)) {
            return false;
        }

        $temporaryFile = tempnam($directory, '.instagram-token-');
        if ($temporaryFile === false) {
            return false;
        }

        chmod($temporaryFile, 0600);
        if (file_put_contents($temporaryFile, $contents) === false || !rename($temporaryFile, $this->statePath)) {
            unlink($temporaryFile);

            return false;
        }

        return true;
    }
}
