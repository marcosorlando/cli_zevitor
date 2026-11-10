<?php

use App\Helpers\Check;

/**
 * Dados da empresa usados nos modelos de e-mail (assets/html/*.html), já escapados para HTML.
 * Uso: $templateData = (require REQUIRE_PATH . '/inc/email-empresa.php') + [...dados do e-mail];
 */
$whatsappUrl = Check::whatsMessage(
    SITE_ADDR_WHATS,
    'Olá, Mecânica Zé Vitor! Vim pelo site e gostaria de atendimento.'
);
$facebookUrl = defined('SITE_SOCIAL_FB_PAGE') && SITE_SOCIAL_FB_PAGE !== ''
    ? 'https://www.facebook.com/' . SITE_SOCIAL_FB_PAGE
    : BASE;
$instagramUrl = defined('SITE_SOCIAL_INSTAGRAM') && SITE_SOCIAL_INSTAGRAM !== ''
    ? 'https://www.instagram.com/' . SITE_SOCIAL_INSTAGRAM . '/'
    : BASE;

return [
    'site_name' => Check::safeHtmlChars(SITE_NAME),
    'empresa_nome' => Check::safeHtmlChars(defined('SITE_ADDR_NAME') ? SITE_ADDR_NAME : SITE_NAME),
    'empresa_razao' => Check::safeHtmlChars(SITE_ADDR_RS),
    'empresa_cnpj' => Check::safeHtmlChars(SITE_ADDR_CNPJ),
    'empresa_email' => Check::safeHtmlChars(defined('SITE_ADDR_EMAIL') ? SITE_ADDR_EMAIL : 'contato@zevitor.com.br'),
    'empresa_site' => Check::safeHtmlChars(SITE_ADDR_SITE),
    'empresa_telefone' => Check::safeHtmlChars(SITE_ADDR_PHONE_A),
    'empresa_telefone_url' => Check::safeHtmlChars(Check::clearNumber(SITE_ADDR_PHONE_A)),
    'empresa_whatsapp' => Check::safeHtmlChars(SITE_ADDR_WHATS),
    'empresa_endereco' => Check::safeHtmlChars(
        SITE_ADDR_ADDR . ' - ' . SITE_ADDR_DISTRICT . ', ' . SITE_ADDR_CITY . '/' . SITE_ADDR_UF
    ),
    'logo_url' => Check::safeHtmlChars(INCLUDE_PATH . '/assets/images/resources/logo-white-red.svg'),
    'site_url' => Check::safeHtmlChars(BASE),
    'whatsapp_url' => Check::safeHtmlChars($whatsappUrl),
    'facebook_url' => Check::safeHtmlChars($facebookUrl),
    'instagram_url' => Check::safeHtmlChars($instagramUrl),
    'maps_url' => Check::safeHtmlChars('https://maps.app.goo.gl/AiMXbGrVdaji2qLV9'),
];
