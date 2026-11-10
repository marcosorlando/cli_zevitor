<?php

declare(strict_types=1);

namespace App\Models;

use App\Conn\Create;
use App\Conn\Read;
use App\Conn\Update;

/**
 * Newsletter com double opt-in sobre DB_LEADS (lead_conversion = 'Newsletter').
 * O token do link de confirmação só existe no e-mail; o banco guarda o SHA-256 dele em lead_token.
 */
final class Newsletter
{
    public const CONVERSION = 'Newsletter';

    /** Validade do link de confirmação. */
    public const TOKEN_DAYS = 7;

    /** Intervalo mínimo entre dois e-mails de confirmação para o mesmo endereço. */
    public const RESEND_SECONDS = 600;

    public static function find(string $email): ?array
    {
        $read = new Read();
        $read->fullRead(
            'SELECT lead_id, lead_token_date, lead_confirmed FROM ' . DB_LEADS
            . ' WHERE lead_email = :email AND lead_conversion = :conversion ORDER BY lead_id ASC LIMIT 1',
            'email=' . rawurlencode($email) . '&conversion=' . rawurlencode(self::CONVERSION)
        );

        return $read->getResult()[0] ?? null;
    }

    public static function canResend(array $lead): bool
    {
        $sentAt = strtotime((string)($lead['lead_token_date'] ?? ''));

        return $sentAt === false || time() - $sentAt >= self::RESEND_SECONDS;
    }

    /**
     * Grava um token novo para o e-mail (criando o lead pendente se preciso) e devolve o token em claro.
     */
    public static function issueToken(string $email, ?array $lead): ?string
    {
        $token = bin2hex(random_bytes(32));
        $data = [
            'lead_token' => hash('sha256', $token),
            'lead_token_date' => date('Y-m-d H:i:s'),
        ];

        if ($lead === null) {
            $create = new Create();
            $create->exeCreate(DB_LEADS, $data + [
                'lead_email' => $email,
                'lead_conversion' => self::CONVERSION,
                'lead_status' => 1,
            ]);

            return $create->getResult() ? $token : null;
        }

        $update = new Update();
        $update->exeUpdate(DB_LEADS, $data, 'WHERE lead_id = :id', 'id=' . (int)$lead['lead_id']);

        return $update->getResult() ? $token : null;
    }

    /**
     * Libera o reenvio imediato quando o e-mail de confirmação não pôde ser enviado.
     */
    public static function releaseToken(string $email): void
    {
        $update = new Update();
        $update->exeUpdate(
            DB_LEADS,
            ['lead_token_date' => null],
            'WHERE lead_email = :email AND lead_conversion = :conversion AND lead_confirmed IS NULL',
            'email=' . rawurlencode($email) . '&conversion=' . rawurlencode(self::CONVERSION)
        );
    }

    public static function confirmUrl(string $token): string
    {
        return BASE . '/newsletter/confirmar/' . $token;
    }

    /**
     * Inscrição pendente dona do token, ou null se o token não existe, já foi usado ou expirou.
     */
    public static function findPendingByToken(string $token): ?array
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }

        $read = new Read();
        $read->fullRead(
            'SELECT lead_id, lead_email, lead_token_date FROM ' . DB_LEADS
            . ' WHERE lead_token = :token AND lead_conversion = :conversion AND lead_confirmed IS NULL LIMIT 1',
            'token=' . hash('sha256', $token) . '&conversion=' . rawurlencode(self::CONVERSION)
        );
        $lead = $read->getResult()[0] ?? null;
        if ($lead === null) {
            return null;
        }

        $sentAt = strtotime((string)($lead['lead_token_date'] ?? ''));
        if ($sentAt === false || time() - $sentAt > self::TOKEN_DAYS * 86400) {
            return null;
        }

        return $lead;
    }

    public static function confirm(int $leadId): bool
    {
        $update = new Update();
        $update->exeUpdate(
            DB_LEADS,
            ['lead_confirmed' => date('Y-m-d H:i:s'), 'lead_token' => null, 'lead_token_date' => null],
            'WHERE lead_id = :id AND lead_confirmed IS NULL',
            'id=' . $leadId
        );

        return $update->getRowCount() > 0;
    }
}
