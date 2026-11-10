<?php

declare(strict_types=1);

use App\Helpers\Check;
use App\Helpers\Recaptcha;
use App\Models\Email;
use App\Models\Newsletter;
use App\View\Template;

/**
 * Newsletter do rodapé (inc/footer.php) com double opt-in: grava o e-mail como lead pendente e envia o link
 * de confirmação (newsletter.php). Devolve JSON para o script.js.
 */
require_once __DIR__ . '/../../../vendor/autoload.php';
require_once __DIR__ . '/../../../_app/Config.inc.php';

// REQUIRE_PATH é relativo à raiz do site.
chdir(dirname(__DIR__, 3));

header('Content-Type: application/json; charset=UTF-8');

/**
 * @param array<string, mixed> $extra
 */
function newsletterResponse(string $message, ?int $type = null, array $extra = []): never
{
    echo json_encode(['trigger' => Check::ajaxErro($message, $type)] + $extra);

    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    newsletterResponse('<b>Acesso restrito!</b>', E_USER_WARNING);
}

$Email = mb_strtolower(trim((string)filter_input(INPUT_POST, 'email', FILTER_DEFAULT)));
$Token = trim((string)filter_input(INPUT_POST, 'recaptcha_token', FILTER_DEFAULT));

if (!filter_var($Email, FILTER_VALIDATE_EMAIL) || mb_strlen($Email) > 100) {
    newsletterResponse('Informe um e-mail válido para se inscrever.', E_USER_NOTICE);
}

if (!Recaptcha::fromEnv()->verify($Token, 'newsletter')) {
    newsletterResponse(
        'Não conseguimos confirmar a inscrição. Recarregue a página e tente novamente.',
        E_USER_WARNING
    );
}

$Lead = Newsletter::find($Email);
if ($Lead !== null && !empty($Lead['lead_confirmed'])) {
    newsletterResponse('Este e-mail já está inscrito na nossa newsletter.', null, ['success' => true]);
}

$Pendente = 'Quase lá! Enviamos um link de confirmação para ' . Check::safeHtmlChars($Email)
    . '. Confira sua caixa de entrada e o spam.';

// Pedido repetido em pouco tempo: não reenvia, para o formulário não servir para lotar a caixa de alguém.
if ($Lead !== null && !Newsletter::canResend($Lead)) {
    newsletterResponse($Pendente, null, ['success' => true]);
}

$ConfirmToken = Newsletter::issueToken($Email, $Lead);
$Template = Template::getTemplate('newsletter-confirmacao.html', REQUIRE_PATH . '/assets/html/');
if ($ConfirmToken === null || $Template === '') {
    error_log('Newsletter: não foi possível gerar o token ou ler o modelo de e-mail.');
    newsletterResponse('Não foi possível concluir a inscrição agora. Tente novamente.', E_USER_WARNING);
}

$EmpresaData = require REQUIRE_PATH . '/inc/email-empresa.php';
$MailContent = Template::setTemplate($Template, $EmpresaData + [
    'cliente_email' => Check::safeHtmlChars($Email),
    'confirmar_url' => Check::safeHtmlChars(Newsletter::confirmUrl($ConfirmToken)),
    'validade_dias' => (string)Newsletter::TOKEN_DAYS,
]);

$EmailConfirmacao = new Email();
$EmailConfirmacao->enviarMontando(
    'Confirme sua inscrição — ' . SITE_NAME,
    $MailContent,
    defined('SITE_ADDR_NAME') ? SITE_ADDR_NAME : SITE_NAME,
    defined('SITE_ADDR_EMAIL') ? SITE_ADDR_EMAIL : MAIL_USER,
    $Email,
    $Email
);

if ($EmailConfirmacao->getError()) {
    error_log('Newsletter: falha ao enviar a confirmação para ' . $Email . '.');
    Newsletter::releaseToken($Email);
    newsletterResponse('Não conseguimos enviar o e-mail de confirmação agora. Tente novamente.', E_USER_WARNING);
}

newsletterResponse($Pendente, null, ['success' => true]);
