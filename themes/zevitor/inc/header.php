<?php

use App\Conn\Read;

/**
 * Header — tema Zevitor.
 * Visual: template Servixa (.main-header / .main-menu). Encanamento: WorkControl.
 * O <head> (CSS/JS/fonts) é montado pelo index.php raiz, como no doripel.
 */
$Read ??= new Read();
?>
<!-- start header -->
<header class="main-header">
    <div class="main-menu__top">
        <div class="main-menu__top-inner">
            <ul class="list-unstyled main-menu__contact-list">
                <li>
                    <div class="icon"><i class="icon-phone-call"></i></div>
                    <div class="text"><p><a href="tel:5400000000">+55 (54) 0000-0000</a></p></div>
                </li>
                <li>
                    <div class="icon"><i class="icon-email"></i></div>
                    <div class="text"><p><a href="mailto:contato@zevitor.com.br">contato@zevitor.com.br</a></p></div>
                </li>
                <li>
                    <div class="icon"><i class="icon-location1"></i></div>
                    <div class="text"><p>Lagoa Vermelha, RS</p></div>
                </li>
            </ul>
            <p class="main-menu__top-welcome-text">Bem-vindo à <?= SITE_NAME; ?></p>
            <div class="main-menu__top-right">
                <div class="main-menu__top-time">
                    <div class="main-menu__top-time-icon"><span class="fas fa-clock"></span></div>
                    <p class="main-menu__top-text">Seg - Sex: 08:00 - 18:00</p>
                </div>
                <div class="main-menu__social">
                    <?= (defined('SITE_SOCIAL_FB_PAGE') && SITE_SOCIAL_FB_PAGE) ? "<a href='https://facebook.com/" . SITE_SOCIAL_FB_PAGE . "' target='_blank'><i class='fab fa-facebook'></i></a>" : ''; ?>
                    <?= (defined('SITE_SOCIAL_INSTAGRAM') && SITE_SOCIAL_INSTAGRAM) ? "<a href='https://instagram.com/" . SITE_SOCIAL_INSTAGRAM . "' target='_blank'><i class='fab fa-instagram'></i></a>" : ''; ?>
                </div>
            </div>
        </div>
    </div>
    <nav class="main-menu">
        <div class="main-menu__wrapper">
            <div class="main-menu__wrapper-inner">
                <div class="main-menu__left">
                    <div class="main-menu__logo">
                        <a href="<?= BASE; ?>" title="<?= SITE_NAME; ?>"><img src="<?= INCLUDE_PATH; ?>/assets/images/resources/logo-1.png" alt="<?= SITE_NAME; ?>"></a>
                    </div>
                </div>
                <div class="main-menu__main-menu-box">
                    <a href="#" class="mobile-nav__toggler"><i class="fa fa-bars"></i></a>
                    <?php require REQUIRE_PATH . '/inc/menu.php'; ?>
                </div>
                <div class="main-menu__right">
                    <div class="main-menu__search-cart-box">
                        <div class="main-menu__search-box">
                            <a href="#search-header" class="main-menu__search searcher-toggler-box fal fa-search"></a>
                        </div>
                    </div>
                    <div class="main-menu__btn-box">
                        <a href="<?= BASE; ?>/contato" class="thm-btn">Solicite um orçamento<span><i class="icon-next"></i></span></a>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</header>
<!-- end header -->
