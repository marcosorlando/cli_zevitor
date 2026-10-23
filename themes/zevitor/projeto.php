<?php

use App\Conn\Read;
use App\Helpers\Check;

$Read ??= new Read();

if (empty($URL[1])) {
    require REQUIRE_PATH . '/404.php';

    return;
}

$Read->fullRead(
    'SELECT p.project_id, p.project_name, p.project_title, p.project_subtitle, '
    . 'p.project_description, p.project_cover, p.project_created, c.category_title '
    . 'FROM ' . DB_PROJECTS . ' p LEFT JOIN ' . DB_PROJECTS_CATEGORIES . ' c '
    . 'ON c.category_id = p.project_category '
    . 'WHERE p.project_name = :name AND p.project_status = :status LIMIT 1',
    'name=' . urlencode($URL[1]) . '&status=1'
);
$Project = $Read->getResult()[0] ?? null;
if (!$Project) {
    require REQUIRE_PATH . '/404.php';

    return;
}

$ProjectTitle = Check::safeHtmlChars($Project['project_title']);
$ProjectCategory = $Project['category_title'] ?? null;
$ProjectCover = null;
$ProjectGallery = [];

if (!empty($Project['project_cover']) && is_file('uploads/' . $Project['project_cover'])) {
    $ProjectCover = BASE . '/tim.php?src=uploads/' . rawurlencode($Project['project_cover']) . '&w=1200&h=628';
}

$Read->fullRead(
    'SELECT image FROM ' . DB_PROJECTS_GALLERY . ' WHERE project_id = :id ORDER BY id ASC',
    'id=' . (int)$Project['project_id']
);
foreach ($Read->getResult() ?: [] as $Gallery) {
    if (!empty($Gallery['image']) && is_file('uploads/' . $Gallery['image'])) {
        $ProjectGallery[] = BASE . '/tim.php?src=uploads/' . rawurlencode($Gallery['image']) . '&w=1200&h=628';
    }
}

if (!$ProjectCover && $ProjectGallery) {
    $ProjectCover = array_shift($ProjectGallery);
}
$ProjectCover ??= INCLUDE_PATH . '/assets/images/project/project-details-img-1.jpg';

$ProjectDate = !empty($Project['project_created']) && strtotime($Project['project_created'])
    ? date('d/m/Y', strtotime($Project['project_created']))
    : null;

$Read->fullRead(
    'SELECT project_name, project_title FROM ' . DB_PROJECTS
    . ' WHERE project_status = :status AND project_id < :id ORDER BY project_id DESC LIMIT 1',
    'status=1&id=' . (int)$Project['project_id']
);
$PreviousProject = $Read->getResult()[0] ?? null;

$Read->fullRead(
    'SELECT project_name, project_title FROM ' . DB_PROJECTS
    . ' WHERE project_status = :status AND project_id > :id ORDER BY project_id ASC LIMIT 1',
    'status=1&id=' . (int)$Project['project_id']
);
$NextProject = $Read->getResult()[0] ?? null;

$WhatsappUrl = Check::safeHtmlChars(Check::whatsMessage(
    SITE_ADDR_WHATS,
    'Olá, vi o projeto ' . $Project['project_title'] . ' e gostaria de conversar sobre meu carro.'
));
?>

<section class='page-header'>
    <div class='page-header__bg' style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/page-header-bg.jpg);'></div>
    <div class='container'>
        <div class='page-header__inner'>
            <div class='page-header__img-1'>
                <img src='<?= INCLUDE_PATH ?>/assets/images/resources/page-header-img-1.png' alt=''>
            </div>
            <h3>Detalhes do projeto</h3>
            <div class='thm-breadcrumb__inner'>
                <ul class='thm-breadcrumb list-unstyled'>
                    <li><a href='<?= BASE ?>' title='<?= Check::safeHtmlChars(SITE_NAME) ?>'>Início</a></li>
                    <li><span class='fas fa-angle-right'></span></li>
                    <li><a href='<?= BASE ?>/projetos'>Projetos</a></li>
                    <li><span class='fas fa-angle-right'></span></li>
                    <li><?= $ProjectTitle ?></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class='project-details'>
    <div class='container'>
        <div class='row'>
            <div class='col-xl-8 col-lg-7'>
                <div class='project-details__left'>
                    <div class='project-details__img'>
                        <img src='<?= Check::safeHtmlChars($ProjectCover) ?>' alt='<?= $ProjectTitle ?>'>
                    </div>

                    <h1 class='project-details__title-1'><?= $ProjectTitle ?></h1>
                    <?php if (!empty($Project['project_subtitle'])) { ?>
                        <p class='project-details__text-1'><?= Check::safeHtmlChars($Project['project_subtitle']) ?></p>
                    <?php } ?>
                    <?php if (!empty($Project['project_description'])) { ?>
                        <div class='project-details__text-2'><?= $Project['project_description'] ?></div>
                    <?php } ?>
                    <?php if ($ProjectGallery) { ?>
                        <h2 class='project-details__title-2'>Imagens do projeto</h2>
                        <div class='row'>
                            <?php foreach ($ProjectGallery as $Index => $Image) { ?>
                                <div class='col-md-6 mb-4'>
                                    <div class='project-details__text-img'>
                                        <a href='<?= Check::safeHtmlChars($Image) ?>' class='img-popup'
                                           title='<?= $ProjectTitle ?> - imagem <?= $Index + 2 ?>'>
                                            <img src='<?= Check::safeHtmlChars($Image) ?>' class='img-fluid w-100'
                                                 alt='<?= $ProjectTitle ?> - imagem <?= $Index + 2 ?>' loading='lazy'>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class='col-xl-4 col-lg-5'>
                <div class='project-details__sidebar'>
                    <div class='project-details__information'>
                        <h2 class='project-details__information-title'>Informações do projeto</h2>
                        <ul class='project-details__information-list list-unstyled'>
                            <?php if ($ProjectCategory) { ?>
                                <li><h4>Categoria</h4><p><?= Check::safeHtmlChars($ProjectCategory) ?></p></li>
                            <?php } ?>
                            <?php if ($ProjectDate) { ?>
                                <li><h4>Publicado em</h4><p><?= $ProjectDate ?></p></li>
                            <?php } ?>
                        </ul>
                    </div>

                    <div class='project-details__get-started'>
                        <h2 class='project-details__get-started-title'>Seu carro precisa de atenção?</h2>
                        <p class='project-details__get-started-text'>Converse com a nossa equipe sobre o que está acontecendo com seu veículo.</p>
                        <ul class='project-details__get-started-points list-unstyled'>
                            <li>
                                <div class='icon'><span class='icon-phone-call'></span></div>
                                <p><a href='tel:<?= preg_replace('/\D+/', '', SITE_ADDR_PHONE_A) ?>'
                                      title='Ligar para a oficina'><?= Check::safeHtmlChars(SITE_ADDR_PHONE_A) ?></a></p>
                            </li>
                            <li>
                                <div class='icon'><span class='icon-mail'></span></div>
                                <p><a href='mailto:<?= Check::safeHtmlChars(SITE_ADDR_EMAIL) ?>'><?= Check::safeHtmlChars(SITE_ADDR_EMAIL) ?></a></p>
                            </li>
                            <li>
                                <div class='icon'><span class='icon-location'></span></div>
                                <p><?= Check::safeHtmlChars(SITE_ADDR_ADDR) ?><br>
                                    <?= Check::safeHtmlChars(SITE_ADDR_CITY . '/' . SITE_ADDR_UF) ?></p>
                            </li>
                        </ul>
                        <div class='project-details__get-started-btn-box'>
                            <a href='<?= $WhatsappUrl ?>' target='_blank' rel='noopener noreferrer' class='thm-btn'>Fale com a equipe
                                <span><i class='icon-next'></i></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($PreviousProject || $NextProject) { ?>
            <div class='row'>
                <div class='col-xl-12'>
                    <nav class='project-details__previous-next' aria-label='Navegação entre projetos'>
                        <ul>
                            <li>
                                <?php if ($PreviousProject) { ?>
                                    <div class='icon'><a href='<?= BASE ?>/projeto/<?= rawurlencode($PreviousProject['project_name']) ?>'
                                                         aria-label='Projeto anterior'><span class='icon-next'></span></a></div>
                                    <div class='text-box'><a href='<?= BASE ?>/projeto/<?= rawurlencode($PreviousProject['project_name']) ?>'
                                                             title='<?= Check::safeHtmlChars($PreviousProject['project_title']) ?>'>Projeto anterior</a></div>
                                <?php } ?>
                            </li>
                            <li>
                                <?php if ($NextProject) { ?>
                                    <div class='text-box'><a href='<?= BASE ?>/projeto/<?= rawurlencode($NextProject['project_name']) ?>'
                                                             title='<?= Check::safeHtmlChars($NextProject['project_title']) ?>'>Próximo projeto</a></div>
                                    <div class='icon'><a href='<?= BASE ?>/projeto/<?= rawurlencode($NextProject['project_name']) ?>'
                                                         aria-label='Próximo projeto'><span class='icon-next'></span></a></div>
                                <?php } ?>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
