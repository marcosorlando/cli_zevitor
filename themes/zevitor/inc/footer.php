<?php

    use App\Helpers\Check;

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
							<div class='site-footer__social'>
								<a href='#'><i class='icon-facebook-app-symbol'></i></a>
								<a href='#'><i class='icon-twitter'></i></a>
								<a href='#'><i class='icon-linkedin'></i></a>
								<a href='#'><i class='icon-pinterest'></i></a>
							</div>
						</div>
					</div>
					<div class='col-xl-3 col-lg-6 col-md-6 wow fadeInUp' data-wow-delay='200ms'>
						<div class='footer-widget__column footer-widget__quick-link'>
							<div class='footer-widget__title-box'>
								<h3 class='footer-widget__title'>Links rápidos</h3>
							</div>
							<ul class='footer-widget__quick-link-list list-unstyled'>
								<li>
									<a href='about.html'><span class='fas fa-angle-right'></span>Sobre nós</a>
								</li>
								<li>
									<a href='team-v1.html'><span class='fas fa-angle-right'></span>Nossa equipe</a>
								</li>
								<li>
									<a href='pricing.html'><span class='fas fa-angle-right'></span>Nosso
										Preços</a>
								</li>
								<li>
									<a href='contact.html'><span class='fas fa-angle-right'></span>Agendamento</a>
								</li>
								<li>
									<a href='contact.html'><span class='fas fa-angle-right'></span>Contate-nos</a>
								</li>
							</ul>
						</div>
					</div>
					<div class='col-xl-3 col-lg-6 col-md-6 wow fadeInUp' data-wow-delay='300ms'>
						<div class='footer-widget__column footer-widget__services'>
							<div class='footer-widget__title-box'>
								<h3 class='footer-widget__title'>Nossos serviços</h3>
							</div>
							<ul class='footer-widget__quick-link-list list-unstyled'>
								<li>
									<a href='oil-change-filters.html'><span class='fas fa-angle-right'></span>Troca de
										óleo e filtros</a>
								</li>
								<li>
									<a href='engine-repair.html'><span class='fas fa-angle-right'></span>Reparo do motor</a>
								</li>
								<li>
									<a href='brake-service.html'><span class='fas fa-angle-right'></span>Serviço de
										freios</a>
								</li>
								<li>
									<a href='tire-wheel-services.html'><span class='fas fa-angle-right'></span>Serviços
										de pneus e rodas</a>
								</li>
								<li>
									<a href='battery-electrical.html'><span class='fas fa-angle-right'></span>Bateria e
										elétrica</a>
								</li>
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
										<p>Sábados: 08h00 - 12h00</p>
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
                                              ?>'><?= SITE_ADDR_PHONE_A ?></a><span> ou </span><a
													title='Chamar no Whats: <?= SITE_ADDR_PHONE_A ?> ' target='_blank'
													href='<?= Check::whatsMessage(
                                                        SITE_ADDR_WHATS,
                                                        'Escreva sua mensagem para Mecânica Zé Vitor: '
                                                    )
                                                    ?>'><?= SITE_ADDR_WHATS ?></a></p>
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
					<li><a href='#'>Termos e condições</a></li>
					<li><a href='#'>Política de privacidade</a></li>
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
				<a
						title='Chamar no Whats: <?= SITE_ADDR_PHONE_A ?> ' target='_blank'
						href='<?= Check::whatsMessage(
                            SITE_ADDR_WHATS,
                            'Escreva sua mensagem para Mecânica Zé Vitor: '
                        )
                        ?>'><?= SITE_ADDR_WHATS ?></a>
			</li>
		</ul><!-- /.mobile-nav__contact -->
		<div class='mobile-nav__top'>
			<div class='mobile-nav__social'>
				<a href='#' class='fab fa-twitter'></a>
				<a href='#' class='fab fa-facebook-square'></a>
				<a href='#' class='fab fa-pinterest-p'></a>
				<a href='#' class='fab fa-instagram'></a>
			</div><!-- /.mobile-nav__social -->
		</div><!-- /.mobile-nav__top -->

	</div>
	<!-- /.mobile-nav__content -->
</div>
<!-- /.mobile-nav__wrapper -->


<!-- Search Popup -->
<div class='search-popup'>
	<div class='color-layer'></div>
	<button class='close-search'><span class='far fa-times fa-fw'></span></button>
	<form method='post' action='blog.html'>
		<div class='form-group'>
			<input type='search' name='search-field' value='' placeholder='Pesquisar aqui' required=''>
			<button type='submit'><i class='fas fa-search'></i></button>
		</div>
	</form>
</div>
<!-- End Search Popup -->

<a href='#' data-target='html' class='scroll-to-target scroll-to-top'>
	<span class='scroll-to-top__wrapper'><span class='scroll-to-top__inner'></span></span>
	<span class='scroll-to-top__text'>Voltar ao topo</span>
</a>
