<?php

declare(strict_types=1);

require_once __DIR__.'/../../../vendor/autoload.php';

use App\Conn\Read;
use App\Helpers\Check;

/**
 * Exporta a base de leads em CSV (botão "Download" de leads/home.php).
 * Um contato por e-mail (o mais recente); newsletter sem double opt-in confirmado fica de fora.
 */
$read = new Read();

if (\session_status() !== PHP_SESSION_ACTIVE) {
    \session_start();
}

// O nível vem da sessão do painel; o antigo ?a=<nível> da URL não é confiável.
$adminLevel = (int) ($_SESSION['userLogin']['user_level'] ?? 0);

if ($adminLevel < (int) LEVEL_WC_LEADS) {
    \http_response_code(403);
    exit(
        '<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;">'
        .'<b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!'
        .'</div>'
    );
}

$read->exeRead(DB_LEADS, 'ORDER BY lead_email ASC, lead_date DESC, lead_id DESC');

$leads = [];
$emails = [];

foreach ($read->getResult() ?? [] as $lead) {
    if (($lead['lead_conversion'] ?? '') === 'Newsletter' && empty($lead['lead_confirmed'])) {
        continue;
    }

    $email = \strtolower(\trim((string) ($lead['lead_email'] ?? '')));
    if ('' === $email || isset($emails[$email])) {
        continue;
    }

    $emails[$email] = true;
    $leads[] = $lead;
}

if (!$leads) {
    echo Check::erro('<span>Ainda não existem conversões para esse material!</span>', E_USER_NOTICE);

    exit;
}

\usort($leads, static fn (array $a, array $b): int => \strcasecmp(
    (string) ($a['lead_name'] ?? ''),
    (string) ($b['lead_name'] ?? '')
));

\header('Content-Type: text/csv; charset=UTF-8');
\header('Content-Disposition: attachment; filename="base-de-leads.csv"');
\header('Cache-Control: max-age=0');
\header('Pragma: public');
\header('Expires: 0');

$output = \fopen('php://output', 'wb');

if (false === $output) {
    throw new RuntimeException('Não foi possível iniciar o processo de exportação.');
}

// Adiciona BOM para compatibilidade com o Excel.
\fwrite($output, "\xEF\xBB\xBF");

$columns = ['Nome', 'E-mail', 'Telefone', 'Assunto', 'Serviço', 'Veículo', 'Mensagem', 'Conversão', 'Data'];
\fputcsv($output, $columns, ';', '"', '');

foreach ($leads as $lead) {
    $leadDate = '';
    if (!empty($lead['lead_date'])) {
        try {
            $leadDate = (new DateTimeImmutable((string) $lead['lead_date']))->format('d/m/Y');
        } catch (Exception) {
            // Mantém a data vazia caso não seja possível convertê-la.
        }
    }

    \fputcsv(
        $output,
        [
            Check::getCapilalize((string) ($lead['lead_name'] ?? '')),
            \strtolower((string) ($lead['lead_email'] ?? '')),
            (string) ($lead['lead_phone'] ?? ''),
            (string) ($lead['lead_subject'] ?? ''),
            (string) ($lead['lead_service'] ?? ''),
            (string) ($lead['lead_vehicle'] ?? ''),
            (string) ($lead['lead_message'] ?? ''),
            // Sem capitalizar: "Landing page: <título>" sai como foi gravado.
            (string) ($lead['lead_conversion'] ?? ''),
            $leadDate,
        ],
        ';',
        '"',
        ''
    );
}

\fclose($output);

exit;
