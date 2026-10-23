<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    $Read ??= new Read();
    $Read->fullRead(
        'SELECT page_title, page_subtitle, page_content, page_cover FROM ' . DB_PAGES
        . ' WHERE page_name = :name AND page_status = :status LIMIT 1',
        'name=sobre&status=1'
    );
    $Page = $Read->getResult()[0] ?? null;
    if (!$Page) {
        require REQUIRE_PATH . '/404.php';

        return;
    }

    $PageTitle = Check::safeHtmlChars($Page['page_title'] ?: 'Sobre nós');
    $PageSubtitle = Check::safeHtmlChars($Page['page_subtitle'] ?? '');
    $PageCoverPath = $Page['page_cover'] ?? '';
    $PageCover = $PageCoverPath && is_file('uploads/' . $PageCoverPath)
        ? BASE . '/tim.php?src=' . rawurlencode('uploads/' . $PageCoverPath) . '&w=850&h=700'
        : INCLUDE_PATH . '/assets/images/resources/about-one-img-1.jpg';

    $Read->fullRead('SELECT svc_id FROM ' . DB_SERVICES . ' WHERE svc_status = :status LIMIT 1', 'status=1');
    $HasServices = (bool)$Read->getResult();
    $Read->fullRead('SELECT project_id FROM ' . DB_PROJECTS . ' WHERE project_status = :status LIMIT 1', 'status=1');
    $HasProjects = (bool)$Read->getResult();
?>

<section class='page-header'>
	<div class='page-header__bg'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/page-header-bg.jpg);'></div>
	<div class='container'>
		<div class='page-header__inner'>
			<div class='page-header__img-1'>
				<img src='<?= INCLUDE_PATH ?>/assets/images/resources/page-header-img-1.png' alt=''>
			</div>
			<h3><?= $PageTitle ?></h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='<?= BASE ?>'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li><?= $PageTitle ?></li>
				</ul>
			</div>
		</div>
	</div>
</section>

<!--About One Start -->
<section class='about-one'>
	<div class='about-one__shape-1 float-bob-x'>
		<img src='<?= INCLUDE_PATH ?>/assets/images/shapes/about-one-shape-1.png' alt=''>
	</div>
	<div class='about-one__shape-2 float-bob-y'>
		<img src='<?= INCLUDE_PATH ?>/assets/images/shapes/about-one-shape-2.png' alt=''>
	</div>
	<div class='container'>
		<div class='row'>
			<div class='col-xl-6'>
				<div class='about-one__left wow slideInLeft' data-wow-delay='100ms' data-wow-duration='2500ms'>
					<div class='about-one__img-box'>
						<div class='about-one__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/resources/about-one-img-1.jpg' alt=''>
							<div class='about-one__video-link'>
								<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
									<div class='about-one__video-icon'>
										<span class='fa fa-play'></span>
										<i class='ripple'></i>
									</div>
								</a>
							</div>
						</div>
						<div class='about-one__img-two'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/resources/about-one-img-2.jpg' alt=''>
						</div>
						<div class='about-one__experience-box'>
							<div class='about-one__experience-count'>
								<h3 class='odometer'
								    data-count='<?= (new DateTime("1973-12-18"))->diff(new DateTime())->y ?>'>00</h3>
								<span>+</span>
							</div>
							<p class='about-one__experience-count-text'>Anos de <br> Experiência</p>
						</div>
					</div>
				</div>
			</div>
			<div class='col-xl-6'>
				<div class='about-one__right'>
					<div class='section-title text-left sec-title-animation animation-style2'>
						<div class='section-title__tagline-box'>
							<div class='section-title__tagline-border'>
								<div class='section-title__shape-1'>
									<i class='section-title__circle'></i>
								</div>
							</div>
							<h6 class='section-title__tagline'>Sobre nós</h6>
							<div class='section-title__tagline-border'>
								<div class='section-title__shape-2'>
									<i class='section-title__circle'></i>
								</div>
							</div>
						</div>
						<h3 class='section-title__title title-animation'>Mais de 50 anos cuidando e
							<span>apaixonados por carros</span>
						</h3>
					</div>
                    <?php
                        if ($PageSubtitle): ?>
							<p class='about-one__text'><?= $PageSubtitle ?></p>
                        <?php
                        endif; ?>
					<div class='about-one__point-box'>
						<ul class='about-one__point'>
							<li>
								<div class='about-one__point-icon'>
									<span class='icon-affordable'></span>
								</div>
								<div class='about-one__point-content'>
									<h4>Serviço bem feito e preço justo</h4>
									<p>Você sabe o que o carro tem e quais são as opções antes de
										autorizar.</p>
								</div>
							</li>
							<li>
								<div class='about-one__point-icon'>
									<span class='icon-24-hours'></span>
								</div>
								<div class='about-one__point-content'>
									<h4>Pós-venda ativo e atento</h4>
									<p>Ouvimos cada cliente e transformamos o feedback em
										melhoria.</p>
								</div>
							</li>
						</ul>
					</div>
					<div class='about-one__point-two-and-review-box'>
						<ul class='about-one__point-two'>
							<li>
								<div class='icon'>
									<span class='fas fa-check'></span>
								</div>
								<div class='text'>
									<p>Nacionais e importados</p>
								</div>
							</li>
							<li>
								<div class='icon'>
									<span class='fas fa-check'></span>
								</div>
								<div class='text'>
									<p>Diagnóstico transparente</p>
								</div>
							</li>
							<li>
								<div class='icon'>
									<span class='fas fa-check'></span>
								</div>
								<div class='text'>
									<p>Equipe qualificada</p>
								</div>
							</li>
							<li>
								<div class='icon'>
									<span class='fas fa-check'></span>
								</div>
								<div class='text'>
									<p>Mais de 50 anos de história</p>
								</div>
							</li>
						</ul>
						<div class='about-one__review-box'>
							<ul class='list-unstyled about-one__review-list'>
								<li>
									<div class='about-one__review-img'>
										<img src='<?= INCLUDE_PATH ?>/assets/images/resources/about-one-review-img-1-1.jpg'
										     alt=''>
									</div>
								</li>
								<li>
									<div class='about-one__review-img'>
										<img src='<?= INCLUDE_PATH ?>/assets/images/resources/about-one-review-img-1-2.jpg'
										     alt=''>
									</div>
								</li>
								<li>
									<div class='about-one__review-img'>
										<img src='<?= INCLUDE_PATH ?>/assets/images/resources/about-one-review-img-1-3.jpg'
										     alt=''>
									</div>
								</li>
								<li>
									<div class='about-one__review-img'>
										<img src='<?= INCLUDE_PATH ?>/assets/images/resources/about-one-review-img-1-4.jpg'
										     alt=''>
									</div>
								</li>
							</ul>
							<div class='about-one__review-star'>
								<span class='fas fa-star'></span>
								<span class='fas fa-star'></span>
								<span class='fas fa-star'></span>
								<span class='fas fa-star'></span>
								<span class='fas fa-star'></span>
							</div>
							<p class='about-one__review-text'>Clientes 4,8 · +220 avaliações no Google</p>
						</div>
					</div>
					<div class='about-one__btn-and-author-box'>
                        <?php
                            $Read->fullRead(
                                'SELECT user_thumb FROM ' . DB_USERS . ' WHERE user_id = :ze OR user_id = :ramiro ORDER BY user_name DESC LIMIT 2',
                                'ze=8&ramiro=4'
                            );
                            if ($Read->getResult()) {
                                $zeThumb = BASE . '/tim.php?src=uploads/' . $Read->getResult()[0]['user_thumb'];
                                $ramiroThumb = BASE . '/tim.php?src=uploads/' . $Read->getResult()[1]['user_thumb'];
                            }
                        ?>

						<div class='about-one__author-box'>
							<div class='about-one__author-details'>
								<div class='about-one__author-img-box'>
									<div class='about-one__author-img'>
										<img src='<?= $zeThumb ?>'
										     alt=''>
									</div>
								</div>
								<div class='about-one__author-content'>
									<h4>José Luiz - Zé</h4>
									<p>Cofundador</p>
								</div>
							</div>
						</div>
						<div class='about-one__author-box'>
							<div class='about-one__author-details'>
								<div class='about-one__author-img-box'>
									<div class='about-one__author-img'>
										<img src='<?= $ramiroThumb ?>'
										     alt=''>
									</div>
								</div>
								<div class='about-one__author-content'>
									<h4>Ramiro Tisott</h4>
									<p>CEO</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-12">
                <?php
                    if (!empty($Page['page_content'])): ?>
						<div class='about-page__content htmlchars text-animation'><?= $Page['page_content'] ?></div>
                    <?php
                    endif; ?>
			</div>
		</div>
	</div>
</section>
<!--About One End -->


<?php
    if ($HasServices) {
        require REQUIRE_PATH . '/inc/services-one.php';
    }

    require REQUIRE_PATH . '/inc/team-one.php';

    if ($HasProjects) {
        require REQUIRE_PATH . '/inc/projects-one.php';
    }
?>
