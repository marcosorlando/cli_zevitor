<?php

declare(strict_types=1);

use App\Conn\Read;
use App\Helpers\Check;

/**
 * Formulário da landing page: grava o lead em DB_LEADS com os mesmos campos do formulário de contato
 * (inc/contato-envio.php) e devolve JSON para o script de landing-page.php.
 */
require_once __DIR__ . '/../../../../vendor/autoload.php';
require_once __DIR__ . '/../../../../_app/Config.inc.php';

// REQUIRE_PATH é relativo à raiz do site.
chdir(dirname(__DIR__, 4));

header('Content-Type: application/json; charset=UTF-8');

$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?: [];
if (($PostData['callback'] ?? '') !== 'Lp' || ($PostData['callback_action'] ?? '') !== 'manage') {
    http_response_code(403);
    echo json_encode(['trigger' => Check::ajaxErro('<b>Acesso restrito!</b>', E_USER_WARNING)]);

    exit;
}

$jSON = [];
$PageId = filter_var($PostData['lp_id'] ?? null, FILTER_VALIDATE_INT);
$Read = new Read();
if ($PageId) {
    $Read->exeRead(DB_LANDING_PAGES, 'WHERE page_id = :id', "id={$PageId}");
}
$LandingPage = $PageId ? ($Read->getResult()[0] ?? null) : null;

if (!$LandingPage) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Não encontramos esta página. Recarregue e tente novamente.',
        E_USER_WARNING
    );
} else {
    $Contato = $PostData;
    $ContatoOrigem = 'Landing page: ' . (string)$LandingPage['page_title'];
    $ContatoAcao = 'landing_page';
    require REQUIRE_PATH . '/inc/contato-envio.php';

    if ($ContatoSucesso !== null) {
        $jSON['trigger'] = Check::ajaxErro(Check::safeHtmlChars($ContatoSucesso));
        $Destino = (string)($LandingPage['page_destino'] ?? '');
        if (filter_var($Destino, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $Destino)) {
            $jSON['redirect'] = $Destino;
        }
    } else {
        $jSON['trigger'] = Check::ajaxErro('<b>OPPSSS:</b> ' . Check::safeHtmlChars((string)$ContatoErro), E_USER_NOTICE);
        if ($ContatoCampo !== null) {
            $jSON['field'] = $ContatoCampo;
        }
    }
}

echo json_encode($jSON);
