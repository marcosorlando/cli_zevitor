<?php

    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Email;
    use App\View\Template;

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
    $mapsUrl = 'https://maps.app.goo.gl/AiMXbGrVdaji2qLV9';
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

    $Contato = filter_input_array(INPUT_POST) ?: [];

    if ($Contato && isset($Contato['action']) && $Contato['action'] === 'contact') {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        unset($Contato['action']);
        $Contato = array_map(static fn(mixed $value): string => is_string($value) ? trim($value) : '', $Contato);

        $Contato['nome'] = $Contato['nome'] ?? $Contato['name'] ?? '';
        $Contato['telefone'] = $Contato['telefone'] ?? $Contato['phone'] ?? '';
        $Contato['assunto'] = $Contato['assunto'] ?? $Contato['subject'] ?? '';
        $Contato['servico'] = $Contato['servico'] ?? $Contato['service'] ?? '';
        $Contato['veiculo'] = $Contato['veiculo'] ?? $Contato['brand_model_year'] ?? '';
        $Contato['mensagem'] = $Contato['mensagem'] ?? $Contato['message'] ?? '';

        $camposObrigatorios = ['nome', 'email', 'telefone', 'assunto', 'servico', 'veiculo', 'mensagem'];
        $camposPendentes = array_filter(
            $camposObrigatorios,
            static fn(string $campo): bool => ($Contato[$campo] ?? '') === ''
        );

        if ($camposPendentes) {
            echo Check::erro('Preencha todos os campos obrigatórios para enviar seu contato!', E_USER_WARNING);
            if ($isAjax) {
                return;
            }
        } elseif (!filter_var($Contato['email'], FILTER_VALIDATE_EMAIL)) {
            echo Check::erro('O e-mail informado não tem um formato válido!', E_USER_WARNING);
            if ($isAjax) {
                return;
            }
        } else {
            $templatesFolder = REQUIRE_PATH . '/assets/html/';
            $clienteTemplate = Template::getTemplate('contato-cliente.html', $templatesFolder);
            $adminTemplate = Template::getTemplate('contato-admin.html', $templatesFolder);

            if ($clienteTemplate === '' || $adminTemplate === '') {
                echo Check::erro('Templates de e-mail não encontrados. Verifique a pasta assets/html.', E_USER_WARNING);
                if ($isAjax) {
                    return;
                }
            } else {
                $assuntoContato = $Contato['assunto'] !== '' ? $Contato['assunto'] : 'Contato pelo site';
                $clienteWhatsappUrl = $Contato['telefone'] !== ''
                    ? Check::whatsMessage(
                        $Contato['telefone'],
                        'Olá, ' . $Contato['nome'] . '! Recebemos sua mensagem pelo site da Mecânica Zé Vitor.'
                    )
                    : 'mailto:' . $Contato['email'];
                $templateData = [
                    'site_name' => Check::safeHtmlChars(SITE_NAME),
                    'cliente_nome' => Check::safeHtmlChars($Contato['nome']),
                    'cliente_email' => Check::safeHtmlChars($Contato['email']),
                    'cliente_telefone' => Check::safeHtmlChars($Contato['telefone'] ?: 'Não informado'),
                    'assunto' => Check::safeHtmlChars($assuntoContato),
                    'servico' => Check::safeHtmlChars($Contato['servico'] ?: 'Não informado'),
                    'veiculo' => Check::safeHtmlChars($Contato['veiculo'] ?: 'Não informado'),
                    'mensagem' => nl2br(Check::safeHtmlChars($Contato['mensagem'])),
                    'data_envio' => date('d/m/Y H:i'),
                    'empresa_nome' => Check::safeHtmlChars($contatoNome),
                    'empresa_razao' => Check::safeHtmlChars(SITE_ADDR_RS),
                    'empresa_cnpj' => Check::safeHtmlChars(SITE_ADDR_CNPJ),
                    'empresa_email' => Check::safeHtmlChars($contatoEmail),
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
                    'cliente_whatsapp_url' => Check::safeHtmlChars($clienteWhatsappUrl),
                    'facebook_url' => Check::safeHtmlChars($facebookUrl),
                    'instagram_url' => Check::safeHtmlChars($instagramUrl),
                    'maps_url' => Check::safeHtmlChars($mapsUrl),
                ];

                $MailContentAdmin = Template::setTemplate($adminTemplate, $templateData);
                $MailContentCliente = Template::setTemplate($clienteTemplate, $templateData);

                $EmailAdmin = new Email();
                $EmailAdmin->enviarMontando(
                    'Novo contato pelo site — ' . SITE_NAME,
                    $MailContentAdmin,
                    $Contato['nome'],
                    $Contato['email'],
                    $contatoNome,
                    $contatoEmail
                );

                $EmailCliente = new Email();
                $EmailCliente->enviarMontando(
                    'Recebemos sua mensagem — ' . SITE_NAME,
                    $MailContentCliente,
                    $contatoNome,
                    $contatoEmail,
                    $Contato['nome'],
                    $Contato['email']
                );

                if (!$EmailAdmin->getError() && !$EmailCliente->getError()) {
                    $sucesso = "Obrigado, {$Contato['nome']}! Sua mensagem foi enviada.";
                    if ($isAjax) {
                        echo Check::erro($sucesso);

                        return;
                    }

                    $_SESSION['sucesso'] = $sucesso;
                    header('Location: ' . BASE . '/contato#form');

                    return;
                }

                echo Check::erro(
                    'Não foi possível enviar agora. Tente novamente ou escreva para ' . $contatoEmail . '.',
                    E_USER_WARNING
                );
                if ($isAjax) {
                    return;
                }
            }
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
						<h3 class='contact-page__middle-title'>Fale com quem entende</h3>
						<p class='contact-page__middle-text'>Seu carro apresentou barulho, falha, luz no painel ou está
							na hora da revisão? A Mecânica Zé Vitor une mais de 50 anos de experiência, diagnóstico
							técnico e atendimento transparente para cuidar de nacionais e importados com a atenção que
							você merece.</p>
						<div class='contact-page__contact-info'>
							<h3 class='contact-page__contact-info-title'>Informações de contato</h3>
							<ul class='contact-page__contact-list list-unstyled'>
								<li>
									<h4 class='contact-page__contact-list-title'>Endereço</h4>
									<p><a target='_blank' title='Ver rotas'
									      href='<?= $mapsUrl ?>'><?=
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
						<a href='<?= $mapsUrl ?>' target="_blank"
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
		<div class='contact-page__bottom' id='form'>
			<div class='contact-page__form-box'>
				<h3 class='comment-one__title'>Conte para a gente o que está acontecendo</h3>
				<p class='comment-one__text'>
					Envie sua dúvida, solicite um orçamento ou agende uma avaliação. Nossa equipe retorna com
					orientação clara e sem enrolação. Os campos obrigatórios estão marcados com *.
				</p>
				<form action='<?= BASE ?>/contato' method='POST'
				      class='contact-page__form contact-form-validated form_capitalize'>
					<input type='hidden' name='action' value='contact'>
					<div class='row'>
						<div class='col-xl-6 col-lg-6'>
							<div class='contact-page__input-box'>
								<input type='text' placeholder='Seu nome*' name='nome' required>
							</div>
						</div>
						<div class='col-xl-6 col-lg-6'>
							<div class='contact-page__input-box'>
								<input type='email' placeholder='Seu e-mail*' name='email' required>
							</div>
						</div>
						<div class='col-xl-6 col-lg-6'>
							<div class='contact-page__input-box'>
								<input type='text' class="formPhone" placeholder='Whatsapp*' name='telefone' required>
							</div>
						</div>
						<div class='col-xl-6 col-lg-6'>
							<div class='contact-page__input-box'>
								<input type='text' placeholder='Assunto*' name='assunto' required>
							</div>
						</div>
					</div>
					<div class="row">
						<div class='col-xl-6 col-lg-6'>
							<div class='contact-page__input-box'>
								<select name="servico" id="service" required>
									<option value="" selected disabled>Selecione um serviço*</option>
                                    <?php
                                        $Read ??= new Read();
                                        $Read->exeRead(DB_SERVICES);
                                        if ($Read->getResult()) {
                                            foreach ($Read->getResult() as $opt) {
                                                $serviceTitle = Check::safeHtmlChars($opt['svc_title'] ?? '');
                                                ?>
												<option value="<?= $serviceTitle ?>"><?= $serviceTitle ?></option>
                                                <?php
                                            }
                                        } else {
                                            ?>
											<option value="">Não existem serviços cadastrados</option>
                                            <?php
                                        }
                                    ?>
									<option value='Outro'>Outro</option>

								</select>
							</div>
						</div>
						<div class='col-xl-6 col-lg-6'>
							<div class='contact-page__input-box'>
								<input type='text' placeholder='Marca / Modelo / Ano do carro*' name='veiculo'
								       required>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class='col-xl-12 col-lg-12'>
							<div class='contact-page__input-box text-message-box'>
								<textarea required name='mensagem'
								          placeholder='Sua mensagem, descreva o que esta ocorrendo com seu veículo*'></textarea>
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

<script src="<?= BASE ?>/assets/js/text.control.min.js"></script>
