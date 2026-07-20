<?php

use App\Helpers\Check;

?>
<header class='main-header'>
	<div class='main-menu__top'>
		<div class='main-menu__top-inner'>
			<ul class='list-unstyled main-menu__contact-list'>
				<li>
					<div class='icon'>
						<i class='icon-phone-call'></i>
					</div>
					<div class='text'>
						<p>
							<a title='Fazer Ligação para: <?= SITE_ADDR_PHONE_A ?>' target='_blank'
							   href='tel:<?= Check::clearNumber(SITE_ADDR_PHONE_A); ?>'><?= SITE_ADDR_PHONE_A ?></a>
						</p>
					</div>
				</li>
				<li>
					<div class='icon'>
						<i class='icon-email'></i>
					</div>
					<div class='text'>
						<p><a target='_blank' title='Enviar e-mail' href='mailto:<?= SITE_ADDR_EMAIL ?>'><?= SITE_ADDR_EMAIL ?></a></p>
					</div>
				</li>
				<li>
					<div class='icon'>
						<i class='icon-location1'></i>
					</div>
					<div class='text'>
						<p>
							<a target='_blank' title='Ver rotas'
							   href='https://maps.app.goo.gl/AiMXbGrVdaji2qLV9'><?= SITE_ADDR_ADDR . ' - ' . SITE_ADDR_DISTRICT ?></a>
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
					<p class='main-menu__top-text'>Seg - Sex: 09:00 - 17:00</p>
				</div>
				<div class='main-menu__social'>
					<a href='#'><i class='fab fa-facebook'></i></a>
					<a href='#'><i class='fab fa-youtube'></i></a>
					<a href='#'><i class='fab fa-instagram'></i></a>
				</div>
			</div>
		</div>
	</div>
	<nav class='main-menu'>
		<div class='main-menu__wrapper'>
			<div class='main-menu__wrapper-inner'>
				<div class='main-menu__left'>
					<div class='main-menu__logo'>
						<a href='<?= BASE ?>'><img src='<?= INCLUDE_PATH ?>/assets/images/resources/logo-white-red.svg' alt='Ir para Home'></a>
					</div>
				</div>
				<div class='main-menu__main-menu-box'>
					<a href='#' class='mobile-nav__toggler'><i class='fa fa-bars'></i></a>
                    <?php require __DIR__ . '/menu.php'; ?>
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
							<div class='main-menu__cart-box'>
								<a href='#' class='main-menu__cart'>
									<span class='far fa-shopping-cart'></span>
									<span class='main-menu__cart-count'>02</span>
								</a>
							</div>
						</div>
					</div>
					<div class='main-menu__nav-sidebar-icon'>
						<a class='navSidebar-button' href='#'>
							<span class='icon-dots-menu-one'></span>
							<span class='icon-dots-menu-two'></span>
							<span class='icon-dots-menu-three'></span>
						</a>
					</div>
					<div class='main-menu__btn-box'>
						<a href='<?= BASE ?>/contato' class='thm-btn'>Obtenha uma cotação<span><i class='icon-next'></i></span></a>
					</div>
				</div>
			</div>
		</div>
	</nav>
</header>

<div class='stricky-header stricked-menu main-menu'>
	<div class='sticky-header__content'></div>
</div>
