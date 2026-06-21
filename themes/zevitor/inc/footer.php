<?php

use App\Conn\Read;

/**
 * Footer — tema Zevitor.
 * Visual: template Servixa (.site-footer). Encanamento: WorkControl.
 * Links rápidos dinâmicos (DB_PAGES); sociais via constantes SITE_SOCIAL_* (como no doripel).
 */
$Read ??= new Read();
?>
<!-- start footer -->
<footer class="site-footer">
    <div class="site-footer__bg-shape" style="background-image: url(<?= INCLUDE_PATH; ?>/assets/images/shapes/site-footer-bg-shape.png);"></div>
    <div class="site-footer__top">
        <div class="container">
            <div class="site-footer__top-inner">
                <div class="row">
                    <!-- about -->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="footer-widget__column footer-widget__about">
                            <div class="footer-widget__logo">
                                <a href="<?= BASE; ?>"><img src="<?= INCLUDE_PATH; ?>/assets/images/resources/logo-1.png" alt="<?= SITE_NAME; ?>"></a>
                            </div>
                            <p class="footer-widget__about-text"><?= SITE_NAME; ?> — qualidade e confiança em cada serviço.</p>
                            <div class="site-footer__social">
                                <?= (defined('SITE_SOCIAL_FB_PAGE') && SITE_SOCIAL_FB_PAGE) ? "<a href='https://facebook.com/" . SITE_SOCIAL_FB_PAGE . "' target='_blank'><i class='icon-facebook-app-symbol'></i></a>" : ''; ?>
                                <?= (defined('SITE_SOCIAL_INSTAGRAM') && SITE_SOCIAL_INSTAGRAM) ? "<a href='https://instagram.com/" . SITE_SOCIAL_INSTAGRAM . "' target='_blank'><i class='icon-pinterest'></i></a>" : ''; ?>
                                <?= (defined('SITE_SOCIAL_LINKEDIN') && SITE_SOCIAL_LINKEDIN) ? "<a href='https://www.linkedin.com/in/" . SITE_SOCIAL_LINKEDIN . "' target='_blank'><i class='icon-linkedin'></i></a>" : ''; ?>
                            </div>
                        </div>
                    </div>
                    <!-- quick links (dinâmico) -->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="footer-widget__column footer-widget__quick-link">
                            <div class="footer-widget__title-box"><h3 class="footer-widget__title">Links rápidos</h3></div>
                            <ul class="footer-widget__quick-link-list list-unstyled">
                                <?php
                                if (defined('DB_PAGES')) {
                                    $Read->fullRead('SELECT page_title, page_name FROM ' . DB_PAGES . ' WHERE page_status = 1 ORDER BY page_order ASC, page_name ASC');
                                    if ($Read->getResult()) {
                                        foreach ($Read->getResult() as $Page) {
                                            echo "<li><a href='" . BASE . "/{$Page['page_name']}'><span class='fas fa-angle-right'></span>{$Page['page_title']}</a></li>";
                                        }
                                    }
                                }
?>
                                <li><a href="<?= BASE; ?>/contato"><span class="fas fa-angle-right"></span>Contato</a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- services -->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="footer-widget__column footer-widget__services">
                            <div class="footer-widget__title-box"><h3 class="footer-widget__title">Nossos serviços</h3></div>
                            <ul class="footer-widget__quick-link-list list-unstyled">
                                <!-- TODO: listar serviços dinâmicos (módulo services) quando a constante de tabela do Zevitor for confirmada -->
                                <li><a href="<?= BASE; ?>/servicos"><span class="fas fa-angle-right"></span>Serviços</a></li>
                            </ul>
                        </div>
                    </div>
                    <!-- contact -->
                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="footer-widget__column footer-widget__contact">
                            <div class="footer-widget__title-box"><h3 class="footer-widget__title">Informações de contato</h3></div>
                            <ul class="footer-widget__contact-list list-unstyled">
                                <li>
                                    <div class="icon"><span class="icon-location"></span></div>
                                    <div class="content"><span>Localização:</span><p>Lagoa Vermelha, RS</p></div>
                                </li>
                                <li>
                                    <div class="icon"><span class="icon-clock"></span></div>
                                    <div class="content"><span>Horário:</span><p>Seg - Sex: 08:00 - 18:00</p></div>
                                </li>
                                <li>
                                    <div class="icon"><span class="icon-phone-call"></span></div>
                                    <div class="content"><span>Telefone:</span><p><a href="tel:5400000000">(54) 0000-0000</a></p></div>
                                </li>
                            </ul>
                            <!-- TODO: ligar contato a constantes de config (SITE_*), como o doripel faz com SITE_SOCIAL_* -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="container">
            <div class="site-footer__bottom-inner">
                <p class="site-footer__bottom-text">&copy; <?= date('Y'); ?> <?= SITE_NAME; ?>. Todos os direitos reservados. Produzido por <a href="https://zen.ppg.br" target="_blank" title="Zen Agência Web">Zen Agência Web</a>.</p>
                <ul class="list-unstyled site-footer__bottom-menu">
                    <li><a href="<?= BASE; ?>/contato">Suporte</a></li>
                    <li><a href="<?= BASE; ?>/contato">Contato</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<!-- end footer -->
