<?php

use App\Conn\Read;
use App\Helpers\Check;

    $Read ??= new Read();
    $Read->fullRead(
        'SELECT p.project_name, p.project_title, p.project_cover, c.category_title '
        . 'FROM ' . DB_PROJECTS . ' p LEFT JOIN ' . DB_PROJECTS_CATEGORIES . ' c '
        . 'ON c.category_id = p.project_category '
        . 'WHERE p.project_status = :status ORDER BY p.project_id DESC',
        'status=1'
    );
    $Projects = $Read->getResult() ?: [];
?>

<section class='page-header'>
	<div class='page-header__bg'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/page-header-bg.jpg);'></div>
	<div class='container'>
		<div class='page-header__inner'>
			<div class='page-header__img-1'>
				<img src='<?= INCLUDE_PATH ?>/assets/images/resources/page-header-img-1.png' alt=''>
			</div>
			<h3>Projetos</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='<?= BASE ?>' title='<?= Check::safeHtmlChars(SITE_NAME) ?>'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Projetos</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<section class='project-page'>
	<div class='container'>
		<div class='project-page__inner'>
            <?php
                if (!$Projects) { ?>
					<p>Nenhum projeto publicado no momento.</p>
                <?php
                } else { ?>
					<div class='row'>
                        <?php
                            foreach ($Projects as $Project) {
                                $ProjectTitle = Check::safeHtmlChars($Project['project_title']);
                                $ProjectLink = BASE . '/projeto/' . rawurlencode($Project['project_name']);
                                $CoverPath = $Project['project_cover'] ?? '';
                                $Cover = $CoverPath && is_file('uploads/' . $CoverPath)
                                    ? BASE . '/tim.php?src=uploads/' . rawurlencode($CoverPath) . '&w=410&h=350'
                                    : INCLUDE_PATH . '/assets/images/project/project-1-1.jpg';
                                $FullCover = $CoverPath && is_file('uploads/' . $CoverPath)
                                    ? BASE . '/tim.php?src=uploads/' . rawurlencode($CoverPath) . '&w=1200&h=628'
                                    : INCLUDE_PATH . '/assets/images/project/project-1-1.jpg';
                                $Category = $Project['category_title'] ?: 'Projeto automotivo';
                                ?>
								<div class='col-xl-4 col-lg-4 col-md-6'>
									<div class='project-one__single'>
										<div class='project-one__img-box'>
											<div class='project-one__img'>
												<img src='<?= Check::safeHtmlChars($Cover) ?>'
												     alt='<?= $ProjectTitle ?>' loading='lazy'>
											</div>
											<div class='project-one__arrow'>
												<a href='<?= Check::safeHtmlChars($FullCover) ?>' class='img-popup'
												   title='Ampliar imagem de <?= $ProjectTitle ?>'
												   aria-label='Ampliar imagem de <?= $ProjectTitle ?>'>
													<span class='icon-next'></span>
												</a>
											</div>
											<div class='project-one__content-inner'>
												<div class='project-one__content'>
													<div class='project-one__sub-title-and-shape'>
														<span class='project-one__sub-title'><?= Check::safeHtmlChars(
                                                                $Category
                                                            ) ?></span>
														<div class='project-one__sub-title-bdr'></div>
													</div>
													<h3 class='project-one__title'>
														<a href='<?= Check::safeHtmlChars(
                                                            $ProjectLink
                                                        ) ?>'><?= $ProjectTitle ?></a>
													</h3>
												</div>
											</div>
										</div>
									</div>
								</div>
                            <?php
                            } ?>
					</div>
                <?php
                } ?>
		</div>
	</div>
</section>
