<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * reCAPTCHA (Google Cloud) com chave baseada em pontuação.
 * Valida o token criando uma avaliação (CreateAssessment) na reCAPTCHA Enterprise API,
 * que é o método indicado pelo Google no lugar do siteverify legado.
 */
final class Recaptcha
{
    private const ENDPOINT = 'https://recaptchaenterprise.googleapis.com/v1/projects/%s/assessments?key=%s';

    public function __construct(
        private readonly string $siteKey,
        private readonly string $projectId,
        private readonly string $apiKey,
        private readonly float $minScore = 0.5
    ) {
    }

    public static function fromEnv(): self
    {
        $minScore = self::env('RECAPTCHA_MIN_SCORE');

        return new self(
            self::env('RECAPTCHA_SITE_KEY'),
            self::env('RECAPTCHA_PROJECT_ID'),
            self::env('RECAPTCHA_API_KEY'),
            is_numeric($minScore) ? (float)$minScore : 0.5
        );
    }

    /**
     * Sem as três chaves no .env o reCAPTCHA fica desligado (front e back), útil no localhost.
     */
    public function isEnabled(): bool
    {
        return $this->siteKey !== '' && $this->projectId !== '' && $this->apiKey !== '';
    }

    public function getSiteKey(): string
    {
        return $this->siteKey;
    }

    /**
     * Retorna false para token ausente, inválido, de outra ação ou com pontuação abaixo do mínimo.
     * Falhas de infraestrutura (rede, cota estourada, chave mal configurada) liberam o envio
     * e ficam no error_log, para o site não perder contatos por indisponibilidade da API.
     */
    public function verify(string $token, string $expectedAction): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        if ($token === '') {
            return false;
        }

        $assessment = $this->createAssessment($token, $expectedAction);
        if ($assessment === null) {
            return true;
        }

        $tokenProperties = $assessment['tokenProperties'] ?? [];
        if (($tokenProperties['valid'] ?? false) !== true) {
            return false;
        }

        if (($tokenProperties['action'] ?? '') !== $expectedAction) {
            return false;
        }

        $score = $assessment['riskAnalysis']['score'] ?? null;

        return is_numeric($score) && (float)$score >= $this->minScore;
    }

    private function createAssessment(string $token, string $expectedAction): ?array
    {
        if (!function_exists('curl_init')) {
            error_log('reCAPTCHA: extensão cURL indisponível, avaliação ignorada.');

            return null;
        }

        $body = json_encode([
            'event' => [
                'token' => $token,
                'siteKey' => $this->siteKey,
                'expectedAction' => $expectedAction,
                'userAgent' => (string)($_SERVER['HTTP_USER_AGENT'] ?? ''),
                'userIpAddress' => (string)($_SERVER['REMOTE_ADDR'] ?? ''),
            ],
        ]);

        $request = curl_init(sprintf(self::ENDPOINT, rawurlencode($this->projectId), rawurlencode($this->apiKey)));
        if ($request === false || $body === false) {
            return null;
        }

        curl_setopt_array($request, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
            ],
        ]);
        $response = curl_exec($request);
        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);

        if (!is_string($response) || $status !== 200) {
            error_log('reCAPTCHA: falha ao criar a avaliação (HTTP ' . $status . ').');

            return null;
        }

        $payload = json_decode($response, true);

        return is_array($payload) ? $payload : null;
    }

    private static function env(string $key): string
    {
        $value = $_ENV[$key] ?? getenv($key);

        return is_string($value) ? trim($value) : '';
    }
}
