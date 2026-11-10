<?php

use App\Helpers\Recaptcha;

/**
 * Aviso exigido pelo Google quando o selo flutuante do reCAPTCHA fica oculto (.grecaptcha-badge no style.css).
 */
$Recaptcha ??= Recaptcha::fromEnv();
if (!$Recaptcha->isEnabled()) {
    return;
}
?>
<p class='recaptcha-notice'>
	Este site é protegido pelo reCAPTCHA. Valem a
	<a href='https://policies.google.com/privacy' target='_blank' rel='noopener'>Política de Privacidade</a> e os
	<a href='https://policies.google.com/terms' target='_blank' rel='noopener'>Termos de Serviço</a> do Google.
</p>
