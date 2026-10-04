<?php

    use App\Helpers\Check;

?>
<header class='main-header'>
	<div class='main-menu__top'>
		<div class='main-menu__top-inner'>
			<ul class='list-unstyled main-menu__contact-list'>
				<li>
					<div class='text'>
						<p>
							<a title='Chamar no Whats: <?= SITE_ADDR_WHATS ?>' target='_blank'
							   href='<?= Check::whatsMessage(
                                   SITE_ADDR_WHATS,
                                   'Olá, estou no site da Mecânica Zé Vitor. Quero agendar uma visita.'
                               )
                               ?>'><i class='fab fa-whatsapp'></i> <?= SITE_ADDR_WHATS ?></a>
						</p>
					</div>
				</li>
				<li>
					<div class='text'>
						<p><a target='_blank' title='Enviar e-mail'
						      href='mailto:<?= SITE_ADDR_EMAIL ?>'><i class='icon-email'></i> <?= SITE_ADDR_EMAIL
                                ?></a></p>
					</div>
				</li>
				<li>
					<div class='text'>
						<p>
							<a target='_blank' title='Ver rotas no Google Maps'
							   href='https://maps.app.goo.gl/AiMXbGrVdaji2qLV9'><i class='icon-location'></i> <?=
                                    SITE_ADDR_ADDR . ' - ' . SITE_ADDR_DISTRICT ?></a>
						</p>
					</div>
				</li>
			</ul>
			<p class='main-menu__top-welcome-text'>Bem-vindo ao nosso escritório <?= SITE_NAME ?></p>
			<div class='main-menu__top-right'>
				<div class='main-menu__top-time'>
					<div class='main-menu__top-time-icon'>
						<span class='fas fa-clock'></span>
					</div>
					<p class='main-menu__top-text'><b>Seg - Sex:</b> 08:00 - 11:45 | 14:00 - 18:00</p>
				</div>
				<div class='main-menu__social'>

                    <?php
                        if (defined('SITE_SOCIAL_INSTAGRAM') && SITE_SOCIAL_INSTAGRAM): ?>
							<a href="https://instagram.com/<?= SITE_SOCIAL_INSTAGRAM ?>" target="_blank"
							   title="Siga-nos no Instagram">
								<i class="fab fa-instagram"></i>
							</a>
                        <?php
                        endif; ?>

                    <?php
                        if (defined('SITE_SOCIAL_YOUTUBE') && SITE_SOCIAL_YOUTUBE): ?>
							<a href="https://www.youtube.com/@<?= SITE_SOCIAL_YOUTUBE ?>?sub_confirmation=1"
							   target="_blank" title="Siga-nos no Youtube">
								<i class="fab fa-youtube"></i>
							</a>
                        <?php
                        endif; ?>

                    <?php
                        if (defined('SITE_SOCIAL_FB_PAGE') && SITE_SOCIAL_FB_PAGE): ?>
							<a href="https://fb.com/<?= SITE_SOCIAL_FB_PAGE ?>" target="_blank"
							   title="Siga-nos no Facebook">
								<i class="fab fa-facebook"></i>
							</a>
                        <?php
                        endif; ?>
				</div>
			</div>
		</div>
	</div>
	<nav class='main-menu'>
		<div class='main-menu__wrapper'>
			<div class='main-menu__wrapper-inner'>
				<div class='main-menu__left'>
					<div class='main-menu__logo'>
						<a href='<?= BASE ?>'><img src='<?= INCLUDE_PATH ?>/assets/images/resources/logo-white-red.svg'
						                           alt='Ir para Home'></a>
					</div>
				</div>
				<div class='main-menu__main-menu-box'>
					<a href='#' class='mobile-nav__toggler'><i class='fa fa-bars'></i></a>
                    <?php
                        require __DIR__ . '/menu.php'; ?>
				</div>
				<div class='main-menu__right'>
					<div class='main-menu__call'>
						<div class='main-menu__call-icon'>
							<i class='icon-phone-call'></i>
						</div>
						<div class='main-menu__call-content'>
							<p class='main-menu__call-sub-title'>Ligue a qualquer hora</p>
							<h5 class='main-menu__call-number'>
								<a href='tel:<?= Check::clearNumber(SITE_ADDR_PHONE_A); ?>'><?= SITE_ADDR_PHONE_A ?></a>
							</h5>
						</div>
					</div>
					<div class='main-menu__search-cart-box'>
						<div class='main-menu__search-cart-box'>
							<div class='main-menu__search-box'>
								<a href='#' class='main-menu__search searcher-toggler-box fal fa-search'></a>
							</div>
						</div>
					</div>

					<div class='main-menu__btn-box'>
						<a title='Chamar no Whats: <?= SITE_ADDR_WHATS ?>' target='_blank'
						   href='<?= Check::whatsMessage(
                               SITE_ADDR_WHATS,
                               'Olá, estou no site da Mecânica Zé Vitor. Quero agendar uma visita.'
                           )
                           ?>' class='thm-btn'>Agendar Visita<span><i
										class='fab fa-whatsapp'></i></span></a>
					</div>
				</div>
			</div>
		</div>
	</nav>
</header>

<div class='stricky-header stricked-menu main-menu'>
	<div class='sticky-header__content'></div>
</div>
