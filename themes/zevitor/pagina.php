<?php

use App\Conn\Read;

/**
 * Página genérica (DB-driven) — tema Zevitor.
 * Encanamento: doripel/pagina.php (exeRead DB_PAGES + extract).
 * Visual: banner .page-header do template Servixa.
 */
$Read ??= new Read();

$Read->exeRead(DB_PAGES, 'WHERE page_name = :nm AND page_status = 1', "nm={$URL[0]}");
if (!$Read->getResult()) {
    require REQUIRE_PATH . '/404.php';

    return;
}
extract($Read->getResult()[0]);
?>
<!-- start page header -->
<section class="page-header">
    <div class="page-header__bg" style="background-image: url(<?= INCLUDE_PATH; ?>/assets/images/backgrounds/page-header-bg.jpg);"></div>
    <div class="container">
        <div class="page-header__inner">
            <h3><?= $page_title; ?></h3>
            <div class="thm-breadcrumb__inner">
                <ul class="thm-breadcrumb list-unstyled">
                    <li><a href="<?= BASE; ?>">Início</a></li>
                    <li><span class="fas fa-angle-right"></span></li>
                    <li><?= $page_title; ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<!-- end page header -->

<!-- start page content -->
<section class="contact-page">
    <div class="container">
        <?= (isset($page_cover) && $page_cover) ? "<img class='img-fluid' style='margin-bottom:30px' alt='{$page_title}' src='" . BASE . '/tim.php?src=uploads/' . $page_cover . '&w=' . IMAGE_W . '&h=' . IMAGE_H . "'/>" : ''; ?>
        <div class="htmlchars">
            <?= $page_content; ?>
        </div>
    </div>
</section>
<!-- end page content -->
