<?php

    use App\Helpers\Check;

    $FooterPages = $MenuPages ?? [];
    $FooterServices = $MenuServices ?? [];
    $FooterBlogLink = $BlogLink ?? null;
    $LegalSlugs = ['termos-de-uso', 'termos-e-condicoes', 'politica-de-privacidade'];
    $LegalPages = array_values(
        array_filter(
            $FooterPages,
            static fn(array $Page): bool => in_array($Page['page_name'], $LegalSlugs, true)
        )
    );

    // Redes sociais do Client.inc.php: só exibe as que estiverem preenchidas
    // [constante => [url, nome, ícone do rodapé (flaticon), ícone do menu mobile (font awesome)]]
    $SocialNetworks = [
        'SITE_SOCIAL_INSTAGRAM' => ['https://instagram.com/%s', 'Instagram', 'icon-instagram', 'fab fa-instagram'],
        'SITE_SOCIAL_FB_PAGE' => ['https://fb.com/%s', 'Facebook', 'icon-facebook-app-symbol', 'fab fa-facebook-square'],
        'SITE_SOCIAL_YOUTUBE' => [
            'https://www.youtube.com/@%s?sub_confirmation=1',
            'YouTube',
            'fab fa-youtube',
            'fab fa-youtube'
        ],
        'SITE_SOCIAL_TWITTER' => ['https://x.com/%s', 'X (Twitter)', 'icon-twitter', 'fab fa-twitter'],
        'SITE_SOCIAL_LINKEDIN' => [
            'https://www.linkedin.com/company/%s',
            'LinkedIn',
            'icon-linkedin',
            'fab fa-linkedin-in'
        ],
    ];
    $SocialLinks = [];
    foreach ($SocialNetworks as $Const => [$Url, $Name, $FooterIcon, $MobileIcon]) {
        $Handle = defined($Const) ? trim((string)constant($Const), " @/") : '';
        if ('' !== $Handle) {
            $SocialLinks[] = [
                'url' => sprintf($Url, rawurlencode($Handle)),
                'name' => $Name,
                'footer_icon' => $FooterIcon,
                'mobile_icon' => $MobileIcon,
            ];
        }
    }
?>
<!--CTA One Start-->
<section class='cta-one'>
	<div class='container'>
		<div class='cta-one__inner wow fadeInUp' data-wow-duration='.9s' data-wow-delay='300ms'>
			<div class='cta-one__inner-bg'
			     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/cta-one-inner-bg.jpg);'></div>
			<h3 class='cta-one__title'>Inscreva-se para receber atualizações e informações.</h3>
			<div class='cta-one__from-box'>
				<form class='cta-one__form'>
					<div class='cta-one__input-box'>
						<input type='email' placeholder='Digite o endereço de e-mail' name='email'>
						<button type='submit' class='thm-btn'>Inscreva-se<span class='icon-next'></span></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<!--CTA One End-->

<!--Site Footer Start-->
<footer class='site-footer'>
	<div class='site-footer__bg-shape'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/shapes/site-footer-bg-shape.png);'></div>
	<div class='site-footer__top'>
		<div class='container'>
			<div class='site-footer__top-inner'>
				<div class='row'>
					<div class='col-xl-3 col-lg-6 col-md-6 wow fadeInUp' data-wow-delay='100ms'>
						<div class='footer-widget__column footer-widget__about'>
							<div class='footer-widget__logo'>
								<a href='<?= BASE ?>'><img src='<?= INCLUDE_PATH
                                    ?>/assets/images/resources/logo-white-red.svg'
								                           alt='Logotipo - <?= SITE_NAME ?>'></a>
							</div>
							<p class='footer-widget__about-text'><?= SITE_DESC ?></p>
                            <?php
                                if ($SocialLinks) {
                                    ?>
									<div class='site-footer__social'>
                                        <?php
                                            foreach ($SocialLinks as $Social) {
                                                ?>
												<a href='<?= $Social['url'] ?>' target='_blank' rel='noopener'
												   title='Siga-nos no <?= $Social['name'] ?>'
												   aria-label='<?= $Social['name'] ?>'><i
															class='<?= $Social['footer_icon'] ?>'></i></a>
                                                <?php
                                            } ?>
									</div>
                                    <?php
                                } ?>
						</div>
					</div>
					<div class='col-xl-3 col-lg-6 col-md-6 wow fadeInUp' data-wow-delay='200ms'>
						<div class='footer-widget__column footer-widget__quick-link'>
							<div class='footer-widget__title-box'>
								<h3 class='footer-widget__title'>Links rápidos</h3>
							</div>
							<ul class='footer-widget__quick-link-list list-unstyled'>
								<li><a href='<?= BASE ?>'><span class='fas fa-angle-right'></span>Início</a></li>
                                <?php
                                    foreach ($FooterPages as $Page):
                                        if (in_array($Page['page_name'], $LegalSlugs, true)) {
                                            continue;
                                        }
                                        ?>
										<li><a href='<?= BASE ?>/<?= rawurlencode($Page['page_name']) ?>'><span
														class='fas fa-angle-right'></span><?= Check::safeHtmlChars(
                                                    $Page['page_title'] ?: $Page['page_name']
                                                ) ?></a></li>
                                    <?php
                                    endforeach; ?>
                                <?php
                                    if ($FooterBlogLink): ?>
										<li><a href='<?= Check::safeHtmlChars($FooterBlogLink) ?>'><span
														class='fas fa-angle-right'></span>Blog</a></li>
                                    <?php
                                    endif; ?>
							</ul>
						</div>
					</div>
					<div class='col-xl-3 col-lg-6 col-md-6 wow fadeInUp' data-wow-delay='300ms'>
						<div class='footer-widget__column footer-widget__services'>
							<div class='footer-widget__title-box'>
								<h3 class='footer-widget__title'>Nossos serviços</h3>
							</div>
							<ul class='footer-widget__quick-link-list list-unstyled'>
                                <?php
                                    foreach ($FooterServices as $Service): ?>
										<li><a href='<?= BASE ?>/servico/<?= rawurlencode(
                                                $Service['svc_name']
                                            ) ?>'><span class='fas fa-angle-right'></span><?= Check::safeHtmlChars(
                                                    $Service['svc_title']
                                                ) ?></a></li>
                                    <?php
                                    endforeach; ?>
							</ul>
						</div>
					</div>
					<div class='col-xl-3 col-lg-6 col-md-6 wow fadeInUp' data-wow-delay='400ms'>
						<div class='footer-widget__column footer-widget__contact'>
							<div class='footer-widget__title-box'>
								<h3 class='footer-widget__title'>Informações de contato</h3>
							</div>
							<ul class='footer-widget__contact-list list-unstyled'>
								<li>
									<div class='icon'>
										<span class='icon-location'></span>
									</div>
									<div class='content'>
										<span>Localização:</span>
										<p><a target='_blank' title='Ver rotas'
										      href='https://maps.app.goo.gl/AiMXbGrVdaji2qLV9'><?=
                                                    SITE_ADDR_ADDR . ' - ' . SITE_ADDR_DISTRICT . '<br>' .
                                                    SITE_ADDR_CITY . ' - ' . SITE_ADDR_UF

                                                ?></a></p>
									</div>
								</li>
								<li>
									<div class='icon'>
										<span class='icon-clock'></span>
									</div>
									<div class='content'>
										<span>Horário de funcionamento:</span>
										<p>Seg - Sex: 08h00 - 18h00</p>
									</div>
								</li>
								<li>
									<div class='icon'>
										<span class='icon-phone-call'></span>
									</div>
									<div class='content'>
										<span>Telefone:</span>
										<p><a title='Fazer Ligação para: <?= SITE_ADDR_PHONE_A ?> ' target='_blank'
										      href='tel:<?= Check::clearNumber
                                              (
                                                  SITE_ADDR_PHONE_A
                                              )
                                              ?>'><?= SITE_ADDR_PHONE_A ?></a><span> ou </span>
										</p>
										<p>
											<a title='Chamar no Whats: <?= SITE_ADDR_PHONE_A ?> '
											   target='_blank'
											   href='<?= Check::whatsMessage(
                                                   SITE_ADDR_WHATS,
                                                   'Escreva sua mensagem para Mecânica Zé Vitor: '
                                               )
                                               ?>'><i class='icon-whatsapp'></i><?= SITE_ADDR_WHATS ?></a>
										</p>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class='site-footer__bottom'>
		<div class='container'>
			<div class='site-footer__bottom-inner'>
				<p class='site-footer__bottom-text'>© Direitos autorais <?= date('Y') ?> por <a
							href='https://zen.ppg.br'>Zen Agência Web</a>. Todos os direitos
					Reservado.</p>
				<ul class='list-unstyled site-footer__bottom-menu'>
                    <?php
                        foreach ($LegalPages as $Page): ?>
							<li><a href='<?= BASE ?>/<?= rawurlencode($Page['page_name']) ?>'><?= Check::safeHtmlChars(
                                        $Page['page_title'] ?: $Page['page_name']
                                    ) ?></a></li>
                        <?php
                        endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</footer>
<!--Site Footer End-->


</div><!-- /.page-wrapper -->


<div class='mobile-nav__wrapper'>
	<div class='mobile-nav__overlay mobile-nav__toggler'></div>
	<!-- /.mobile-nav__overlay -->
	<div class='mobile-nav__content'>
		<span class='mobile-nav__close mobile-nav__toggler'><i class='fa fa-times'></i></span>
		<div class='logo-box'>
			<a href='<?= BASE ?>' aria-label='imagem do logotipo'><img
						src='<?= INCLUDE_PATH ?>/assets/images/resources/logo-white-red.svg'
						width='140' alt=''></a>
		</div>
		<!-- /.logo-box -->
		<div class='mobile-nav__container'></div>
		<!-- /.mobile-nav__container -->

		<ul class='mobile-nav__contact list-unstyled'>
			<li>
				<i class='fa fa-envelope'></i>
				<a target='_blank' title='Enviar e-mail' href='mailto:<?= SITE_ADDR_EMAIL ?>'><?=
                        SITE_ADDR_EMAIL ?></a>
			</li>
			<li>
				<i class='fas fa-phone'></i>

				<a title='Fazer Ligação para: <?= SITE_ADDR_PHONE_A ?> ' target='_blank'
				   href='tel:<?= Check::clearNumber
                   (
                       SITE_ADDR_PHONE_A
                   )
                   ?>'><?= SITE_ADDR_PHONE_A ?></a><span> ou </span>
				<a title='Chamar no Whats: <?= SITE_ADDR_PHONE_A ?> ' target='_blank'
				   href='<?= Check::whatsMessage(
                       SITE_ADDR_WHATS,
                       'Escreva sua mensagem para Mecânica Zé Vitor: '
                   )
                   ?>'><?= SITE_ADDR_WHATS ?></a>
			</li>
		</ul><!-- /.mobile-nav__contact -->
        <?php
            if ($SocialLinks) {
                ?>
				<div class='mobile-nav__top'>
					<div class='mobile-nav__social'>
                        <?php
                            foreach ($SocialLinks as $Social) {
                                ?>
								<a href='<?= $Social['url'] ?>' class='<?= $Social['mobile_icon'] ?>' target='_blank'
								   rel='noopener' title='Siga-nos no <?= $Social['name'] ?>'
								   aria-label='<?= $Social['name'] ?>'></a>
                                <?php
                            } ?>
					</div><!-- /.mobile-nav__social -->
				</div><!-- /.mobile-nav__top -->
                <?php
            } ?>

	</div>
	<!-- /.mobile-nav__content -->
</div>
<!-- /.mobile-nav__wrapper -->


<!-- Search Popup -->
<div class='search-popup'>
	<div class='color-layer'></div>
	<button class='close-search'><span class='far fa-times fa-fw'></span></button>
	<form name='search' method='post' action=''>
		<div class='form-group'>
			<input type='search' name='p' value='' placeholder='Pesquisar serviços e artigos' autocomplete='off'
			       required>
			<button type='submit'><i class='fas fa-search'></i></button>
		</div>
	</form>
</div>
<!-- End Search Popup -->

<a href='#' data-target='html' class='scroll-to-target scroll-to-top'>
	<span class='scroll-to-top__wrapper'><span class='scroll-to-top__inner'></span></span>
	<span class='scroll-to-top__text'>Voltar ao topo</span>
</a>
