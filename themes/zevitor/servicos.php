<?php

declare(strict_types=1);

use App\Conn\Read;
use App\Helpers\Check;

$Read ??= new Read();
$Read->fullRead(
    'SELECT page_title FROM ' . DB_PAGES . ' WHERE page_name = :name AND page_status = :status LIMIT 1',
    'name=servicos&status=1'
);
$Page = $Read->getResult()[0] ?? null;
if ($Page === null) {
    require REQUIRE_PATH . '/404.php';

    return;
}

$PageTitle = trim((string) ($Page['page_title'] ?? ''));
if ($PageTitle === '') {
    $PageTitle = 'Serviços';
}

$Read->fullRead(
    'SELECT svc_name, svc_title, svc_subtitle, svc_description, svc_icon, svc_cover '
    . 'FROM ' . DB_SERVICES . ' WHERE svc_status = :status '
    . "AND svc_name IS NOT NULL AND svc_name <> '' AND svc_title IS NOT NULL AND svc_title <> '' "
    . 'ORDER BY svc_id ASC',
    'status=1'
);
$Services = $Read->getResult() ?: [];
?>
<section class='page-header'>
    <div class='page-header__bg'
         style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/page-header-bg.jpg);'></div>
    <div class='container'>
        <div class='page-header__inner'>
            <div class='page-header__img-1'>
                <img src='<?= INCLUDE_PATH ?>/assets/images/resources/page-header-img-1.png' alt=''>
            </div>
            <h3><?= Check::safeHtmlChars($PageTitle) ?></h3>
            <div class='thm-breadcrumb__inner'>
                <ul class='thm-breadcrumb list-unstyled'>
                    <li><a href='<?= BASE ?>'>Início</a></li>
                    <li><span class='fas fa-angle-right'></span></li>
                    <li><?= Check::safeHtmlChars($PageTitle) ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class='services-page'>
    <div class='container'>
        <div class='row'>
            <?php if ($Services === []) { ?>
                <div class='col-12'>
                    <p>Nenhum serviço publicado no momento.</p>
                </div>
            <?php } else { ?>
                <?php foreach ($Services as $Index => $Service) {
                    $Title = (string) $Service['svc_title'];
                    $Link = BASE . '/servico/' . rawurlencode((string) $Service['svc_name']);
                    $CoverPath = (string) ($Service['svc_cover'] ?? '');
                    $Cover = $CoverPath !== '' && !str_contains($CoverPath, '..')
                        && !str_starts_with($CoverPath, '/') && is_file('uploads/' . $CoverPath)
                        ? BASE . '/tim.php?src=' . rawurlencode('uploads/' . $CoverPath) . '&w=600&h=450'
                        : INCLUDE_PATH . '/assets/images/services/services-1-1.jpg';
                    $IconValue = (string) ($Service['svc_icon'] ?? '');
                    $Icon = preg_match('/^icon-[A-Za-z0-9_-]+$/', $IconValue) ? $IconValue : 'icon-mechanical';
                    $Description = trim((string) ($Service['svc_subtitle'] ?? ''));
                    if ($Description === '') {
                        $Description = mb_strimwidth(strip_tags((string) ($Service['svc_description'] ?? '')), 0, 120, '…');
                    }
                    ?>
                    <div class='col-xl-4 col-lg-4 col-md-6'>
                        <div class='services-one__single'>
                            <div class='services-one__img-box'>
                                <div class='services-one__img'>
                                    <img src='<?= Check::safeHtmlChars($Cover) ?>'
                                         alt='<?= Check::safeHtmlChars($Title) ?>' loading='lazy'>
                                </div>
                                <div class='services-one__count'><?= str_pad((string) ($Index + 1), 2, '0', STR_PAD_LEFT) ?></div>
                            </div>
                            <div class='services-one__content'>
                                <div class='services-one__icon'>
                                    <span class='<?= Check::safeHtmlChars($Icon) ?>'></span>
                                </div>
                                <h3 class='services-one__title'>
                                    <a href='<?= Check::safeHtmlChars($Link) ?>'><?= Check::safeHtmlChars($Title) ?></a>
                                </h3>
                                <?php if ($Description !== '') { ?>
                                    <p class='services-one__text'><?= Check::safeHtmlChars($Description) ?></p>
                                <?php } ?>
                                <div class='services-one__btn-box'>
                                    <a href='<?= Check::safeHtmlChars($Link) ?>'>Leia mais<span class='icon-next'></span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</section>
