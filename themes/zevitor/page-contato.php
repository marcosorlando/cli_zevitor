<?php

    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Email;

    /**
     * Página "Contato" — tema Zevitor.
     * Encanamento: doripel/page-contato.php (DB_PAGES + handler de e-mail via App\Models\Email).
     * Visual: contact.html do template Servixa (page-header + contact-page).
     */
    $Read ??= new Read();

    $Read->exeRead(DB_PAGES, 'WHERE page_name = :nm AND page_status = 1', "nm={$URL[0]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';

        return;
    }
    extract($Read->getResult()[0]);

    $contatoEmail = defined('SITE_ADDR_EMAIL') ? SITE_ADDR_EMAIL : 'contato@zevitor.com.br';
    $contatoNome = defined('SITE_ADDR_NAME') ? SITE_ADDR_NAME : SITE_NAME;

    $Contato = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?: [];
    if ($Contato && isset($Contato['action']) && $Contato['action'] === 'contact') {
        unset($Contato['action']);

        if (empty($Contato['nome']) || empty($Contato['email']) || empty($Contato['mensagem'])) {
            echo Check::erro('Para enviar seu contato, preencha nome, e-mail e mensagem!', E_USER_WARNING);
        } elseif (!filter_var($Contato['email'], FILTER_VALIDATE_EMAIL)) {
            echo Check::erro('O e-mail informado não tem um formato válido!', E_USER_WARNING);
        } else {
            $Contato = array_map('strip_tags', $Contato);
            $MailContent = '<p>Novo contato de <b>' . $Contato['nome'] . '</b> pelo site.</p>'
                . '<p>E-mail: ' . $Contato['email'] . '</p>'
                . '<p>Telefone: ' . ($Contato['telefone'] ?? '') . '</p>'
                . '<p>Mensagem:<br>' . nl2br($Contato['mensagem']) . '</p>';

            $Email = new Email();
            $Email->EnviarMontando(
                'Contato pelo site — ' . SITE_NAME,
                $MailContent,
                $Contato['nome'],
                $Contato['email'],
                $contatoNome,
                $contatoEmail
            );

            if (!$Email->getError()) {
                $_SESSION['sucesso'] = "Obrigado, {$Contato['nome']}! Sua mensagem foi enviada.";
                header('Location: ' . BASE . '/contato#form');

                return;
            }
            echo Check::erro(
                'Não foi possível enviar agora. Tente novamente ou escreva para ' . $contatoEmail . '.',
                E_USER_WARNING
            );
        }
    }

    if (!empty($_SESSION['sucesso']) && empty($Contato)) {
        echo Check::erro($_SESSION['sucesso']);
        unset($_SESSION['sucesso']);
    }
?>
<!--Page Header Start-->
<section class='page-header'>
	<div class='page-header__bg'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/page-header-bg.jpg);'>
	</div>
	<div class='container'>
		<div class='page-header__inner'>
			<div class='page-header__img-1'>
				<img src='<?= INCLUDE_PATH ?>/assets/images/resources/page-header-img-1.png' alt=''>
			</div>
			<h3>Contate-nos</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='<?= BASE ?>'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Contate-nos</li>
				</ul>
			</div>
		</div>
	</div>
</section>
<!--Page Header End-->

<!--Contact Page Start-->
<section class='contact-page'>
	<div class='container'>

		<div class='contact-page__middle'>
			<div class='row'>
				<div class='col-xl-6 col-lg-6'>
					<div class='contact-page__middle-left'>
						<h3 class='contact-page__middle-title'>Entre em contato</h3>
						<p class='contact-page__middle-text'>A grande maioria dos profissionais de marketing de
							aplicativos concentra-se principalmente
							pós-lançamento<br> técnicas e medidas de marketing de aplicativos, embora completamente
							ausentes
							<br>campanha de pré-lançamento. Isto impede o</p>
						<div class='contact-page__contact-info'>
							<h3 class='contact-page__contact-info-title'>Informações de contato</h3>
							<ul class='contact-page__contact-list list-unstyled'>
								<li>
									<h4 class='contact-page__contact-list-title'>Endereço</h4>
									<p><a target='_blank' title='Ver rotas'
									      href='https://maps.app.goo.gl/AiMXbGrVdaji2qLV9'><?=
                                                SITE_ADDR_ADDR . ' - ' . SITE_ADDR_DISTRICT ?></a></p>
								</li>
								<li>
									<h4 class='contact-page__contact-list-title'>Telefone</h4>
									<p><a title='Fazer Ligação para: <?= SITE_ADDR_PHONE_A ?> ' target='_blank'
									      href='tel:<?= Check::clearNumber
                                          (
                                              SITE_ADDR_PHONE_A
                                          )
                                          ?>'><?= SITE_ADDR_PHONE_A ?></a><span>ou</span><a
												title='Chamar no Whats: <?= SITE_ADDR_PHONE_A ?> ' target='_blank'
												href='<?= Check::whatsMessage(
                                                    SITE_ADDR_WHATS,
                                                    'Escreva sua mensagem para Mecânica Zé Vitor: '
                                                )
                                                ?>'><?= SITE_ADDR_WHATS ?></a></p>
								</li>
								<li>
									<h4 class='contact-page__contact-list-title'>E-mail</h4>
									<p><a target='_blank' title='Enviar e-mail' href='mailto:<?= SITE_ADDR_EMAIL ?>'><?=
                                                SITE_ADDR_EMAIL ?></a></p>
								</li>
							</ul>
						</div>
						<a href='https://maps.app.goo.gl/AiMXbGrVdaji2qLV9' target="_blank"
						   class='contact-page__contact-link'>Como
							chegar? Ver rotas.</a>
					</div>
				</div>
				<div class='col-xl-6 col-lg-6'>
					<div class='contact-page__middle-right'>

						<iframe src='https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2998.1720001839813!2d-51.19866928946638!3d-29.17928729159515!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x951ea33c6419cbdd%3A0x5f0eb029bf4519f!2zTWVjw6JuaWNhIFrDqSBWaXRvcg!5e1!3m2!1spt-BR!2sbr!4v1782071557969!5m2!1spt-BR!2sbr'
						        class='contact-page__google-map' allowfullscreen='' loading='lazy'
						        referrerpolicy='no-referrer-when-downgrade'></iframe>

					</div>
				</div>
			</div>
		</div>
		<div class='contact-page__bottom'>
			<div class='contact-page__form-box'>
				<h3 class='comment-one__title'>Vamos entrar em contato</h3>
				<p class='comment-one__text'>
					Seu endereço de e-mail não será publicado. Os campos obrigatórios são
					marcado *
				</p>
				<form action='<?= INCLUDE_PATH ?>/assets/inc/sendemail.php' method='POST'
				      class='contact-page__form contact-form-validated'>
					<div class='row'>
						<div class='col-xl-6 col-lg-6'>
							<div class='contact-page__input-box'>
								<input type='text' placeholder='Seu nome*' name='name' required>
							</div>
						</div>
						<div class='col-xl-6 col-lg-6'>
							<div class='contact-page__input-box'>
								<input type='email' placeholder='Seu e-mail*' name='email' required>
							</div>
						</div>
						<div class='col-xl-6 col-lg-6'>
							<div class='contact-page__input-box'>
								<input type='text' placeholder='Telefone*' name='phone' required>
							</div>
						</div>
						<div class='col-xl-6 col-lg-6'>
							<div class='contact-page__input-box'>
								<input type='text' placeholder='Assunto*' name='subject' required>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-xl-12 col-lg-12'>
							<div class='contact-page__input-box text-message-box'>
								<textarea required name='message' placeholder='Sua mensagem*'></textarea>
							</div>
							<div class='contact-page__btn-box'>
								<button type='submit' class='thm-btn contact-page__btn'
								        data-loading-text='Por favor, aguarde...'>
									<i class="fa fa-envelope"></i>Enviar mensagem<span><i
												class='icon-next'></i></span>
								</button>
							</div>
						</div>
					</div>
					<div class='result'></div>
				</form>
			</div>
		</div>
	</div>
</section>
<!--Contact Page End-->
