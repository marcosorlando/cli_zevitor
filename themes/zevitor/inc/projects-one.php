<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    /**
     * Projetos (home) — tema Zevitor.
     * Dinâmico: lê DB_PROJECTS (zv_projects) com status ativo.
     * Visual: bloco .project-one do template Servixa (carousel com lightbox).
     * Capa: BASE/tim.php?src=uploads/<project_cover>. Detalhe: BASE/projeto/<project_name>.
     */
    $Read ??= new Read();

    $Read->fullRead(
        'SELECT project_name, project_title, project_subtitle, project_cover '
        . 'FROM ' . DB_PROJECTS . ' WHERE project_status = :st ORDER BY project_id DESC LIMIT :limit',
        'st=1&limit=12'
    );
?>
<!--Project One Start -->
<section class='project-one'>
	<div class='container'>
		<div class='project-one__top'>
			<div class='section-title text-left sec-title-animation animation-style2'>
				<div class='section-title__tagline-box'>
					<div class='section-title__tagline-border'>
						<div class='section-title__shape-1'>
							<i class='section-title__circle'></i>
						</div>
					</div>
					<h6 class='section-title__tagline'>Projetos</h6>
					<div class='section-title__tagline-border'>
						<div class='section-title__shape-2'>
							<i class='section-title__circle'></i>
						</div>
					</div>
				</div>
				<h3 class='section-title__title title-animation'>Veja o resultado do nosso <br>trabalho na
					<span>Mecânica Zé Vitor.</span>
				</h3>
			</div>
			<div class='project-one__nav'>
				<div class='swiper-button-next1'>
					<i class='icon-back'></i>
				</div>
				<div class='swiper-button-prev1'>
					<i class='icon-next'></i>
				</div>
			</div>
		</div>
		<div class='swiper-container project-one__carousel'>
			<div class='swiper-wrapper'>
                <?php
                    if (!$Read->getResult()) {
                        echo Check::erro(
                            'Os projetos ainda serão cadastrados no painel. Volte em breve :)',
                            E_USER_NOTICE
                        );
                    } else {
                        foreach ($Read->getResult() as $Project) {
                            extract($Project);
                            $Thumb = BASE . '/tim.php?src=uploads/' . $project_cover . '&w=600&h=450';
                            $Full = BASE . '/tim.php?src=uploads/' . $project_cover . '&w=1200&h=900';
                            $Link = BASE . '/projeto/' . $project_name;
                            $Sub = !empty($project_subtitle) ? $project_subtitle : 'Veículos reparados';
                ?>
				<!--Project One Single Start-->
				<div class='swiper-slide'>
					<div class='project-one__single'>
						<div class='project-one__img-box'>
							<div class='project-one__img'>
								<img src='<?= $Thumb ?>' alt='<?= Check::safeHtmlChars($project_title) ?>'>
							</div>
							<div class='project-one__arrow'>
								<a href='<?= $Full ?>' class='img-popup'><span class='icon-next'></span></a>
							</div>
							<div class='project-one__content-inner'>
								<div class='project-one__content'>
									<div class='project-one__sub-title-and-shape'>
										<span class='project-one__sub-title'><?= Check::safeHtmlChars($Sub) ?></span>
										<div class='project-one__sub-title-bdr'></div>
									</div>
									<h3 class='project-one__title'><a href='<?= $Link ?>'><?= Check::safeHtmlChars($project_title) ?></a></h3>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--Project One Single End-->
                <?php
                        }
                    }
                ?>
			</div>
		</div>
	</div>
</section>
<!--Project One End -->
