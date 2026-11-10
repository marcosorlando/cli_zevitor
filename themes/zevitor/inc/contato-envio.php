<?php

use App\Conn\Create;
use App\Helpers\Check;
use App\Helpers\Recaptcha;
use App\Models\Email;
use App\View\Template;

/**
 * Processa um envio de contato: valida, confere o reCAPTCHA, grava o lead em DB_LEADS e envia os e-mails.
 * Usado pela página de contato (o agendamento da home também posta nela) e pela landing page (lp/ajax/Lp.ajax.php).
 *
 * Entrada: $Contato (POST), $ContatoOrigem (gravado em lead_conversion) e $ContatoAcao (action do reCAPTCHA).
 * Saída: $ContatoErro e $ContatoCampo quando o envio é recusado, $ContatoSucesso quando é aceito.
 */
$ContatoErro = null;
$ContatoCampo = null;
$ContatoSucesso = null;

$Contato = array_map(static fn (mixed $value): string => is_string($value) ? trim($value) : '', $Contato);
$recaptchaToken = $Contato['recaptcha_token'] ?? '';
$Contato = [
    'nome' => $Contato['nome'] ?? $Contato['name'] ?? '',
    'email' => $Contato['email'] ?? '',
    'telefone' => $Contato['telefone'] ?? $Contato['phone'] ?? '',
    'assunto' => $Contato['assunto'] ?? $Contato['subject'] ?? '',
    'servico' => $Contato['servico'] ?? $Contato['service'] ?? '',
    'veiculo' => $Contato['veiculo'] ?? $Contato['brand_model_year'] ?? '',
    'mensagem' => $Contato['mensagem'] ?? $Contato['message'] ?? '',
];

$camposPendentes = array_keys(array_filter($Contato, static fn (string $valor): bool => $valor === ''));
$Recaptcha ??= Recaptcha::fromEnv();

if ($camposPendentes) {
    $ContatoErro = 'Preencha todos os campos obrigatórios para enviar seu contato!';
    $ContatoCampo = $camposPendentes[0];
} elseif (!filter_var($Contato['email'], FILTER_VALIDATE_EMAIL)) {
    $ContatoErro = 'O e-mail informado não tem um formato válido!';
    $ContatoCampo = 'email';
} elseif (!$Recaptcha->verify($recaptchaToken, $ContatoAcao)) {
    $ContatoErro = 'Não conseguimos confirmar o envio. Recarregue a página e tente novamente ou fale com a gente pelo WhatsApp.';
} else {
    $Create = new Create();
    $Create->exeCreate(DB_LEADS, [
        'lead_name' => mb_substr($Contato['nome'], 0, 100),
        'lead_email' => mb_substr($Contato['email'], 0, 100),
        'lead_phone' => mb_substr($Contato['telefone'], 0, 45),
        'lead_subject' => mb_substr($Contato['assunto'], 0, 255),
        'lead_service' => mb_substr($Contato['servico'], 0, 255),
        'lead_vehicle' => mb_substr($Contato['veiculo'], 0, 255),
        'lead_message' => $Contato['mensagem'],
        'lead_conversion' => mb_substr($ContatoOrigem, 0, 255),
        'lead_status' => 1,
    ]);
    $leadGravado = (bool)$Create->getResult();

    $contatoEmail = defined('SITE_ADDR_EMAIL') ? SITE_ADDR_EMAIL : 'contato@zevitor.com.br';
    $contatoNome = defined('SITE_ADDR_NAME') ? SITE_ADDR_NAME : SITE_NAME;
    $clienteWhatsappUrl = Check::whatsMessage(
        $Contato['telefone'],
        'Olá, ' . $Contato['nome'] . '! Recebemos sua mensagem pelo site da Mecânica Zé Vitor.'
    );

    $templatesFolder = REQUIRE_PATH . '/assets/html/';
    $clienteTemplate = Template::getTemplate('contato-cliente.html', $templatesFolder);
    $adminTemplate = Template::getTemplate('contato-admin.html', $templatesFolder);
    $emailsEnviados = false;

    if ($clienteTemplate === '' || $adminTemplate === '') {
        error_log('Contato: templates de e-mail não encontrados em ' . $templatesFolder);
    } else {
        $templateData = (require REQUIRE_PATH . '/inc/email-empresa.php') + [
            'cliente_nome' => Check::safeHtmlChars($Contato['nome']),
            'cliente_email' => Check::safeHtmlChars($Contato['email']),
            'cliente_telefone' => Check::safeHtmlChars($Contato['telefone']),
            'assunto' => Check::safeHtmlChars($Contato['assunto']),
            'servico' => Check::safeHtmlChars($Contato['servico']),
            'veiculo' => Check::safeHtmlChars($Contato['veiculo']),
            'mensagem' => nl2br(Check::safeHtmlChars($Contato['mensagem'])),
            'data_envio' => date('d/m/Y H:i'),
            'cliente_whatsapp_url' => Check::safeHtmlChars($clienteWhatsappUrl),
        ];

        $EmailAdmin = new Email();
        $EmailAdmin->enviarMontando(
            'Novo contato pelo site — ' . SITE_NAME . ' (' . $ContatoOrigem . ')',
            Template::setTemplate($adminTemplate, $templateData),
            $Contato['nome'],
            $Contato['email'],
            $contatoNome,
            $contatoEmail
        );

        $EmailCliente = new Email();
        $EmailCliente->enviarMontando(
            'Recebemos sua mensagem — ' . SITE_NAME,
            Template::setTemplate($clienteTemplate, $templateData),
            $contatoNome,
            $contatoEmail,
            $Contato['nome'],
            $Contato['email']
        );

        $emailsEnviados = !$EmailAdmin->getError() && !$EmailCliente->getError();
        if (!$emailsEnviados) {
            error_log('Contato: falha ao enviar os e-mails do contato de ' . $Contato['email'] . '.');
        }
    }

    // Com o lead gravado o contato não se perde, mesmo que o e-mail falhe: ele fica no painel em Leads.
    if ($leadGravado || $emailsEnviados) {
        $ContatoSucesso = "Obrigado, {$Contato['nome']}! Sua mensagem foi enviada.";
    } else {
        $ContatoErro = 'Não foi possível enviar agora. Tente novamente ou escreva para ' . $contatoEmail . '.';
    }
}
