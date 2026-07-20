<?php

    use App\Conn\Read;

    $Read ??= new Read;

    $Read->exeRead(DB_PAGES, "WHERE page_name = :nm AND page_status = 1", "nm={$URL[0]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        extract($Read->getResult()[0]);
    }
?>

<!--Page Header Start-->
<section class='page-header'>
	<div class='page-header__bg'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/page-header-bg.jpg);'>
	</div>
	<div class='container'>
		<div class='page-header__inner'>
			<div class='page-header__img-1'>
				<img src='<?= INCLUDE_PATH ?>/assets/images/resources/page-header-img-1.png' alt=''>
			</div>
			<h3>Projeto V3</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='index.html'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Projeto V3</li>
				</ul>
			</div>
		</div>
	</div>
</section>
<!--Page Header End-->

<!--Project Three Start-->
<section class='project-three project-v-three'>
	<div class='project-three__shape-1'></div>
	<div class='container'>
		<div class='section-title text-center sec-title-animation animation-style1'>
			<div class='section-title__tagline-box'>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-1'>
						<i class='section-title__circle'></i>
					</div>
				</div>
				<h6 class='section-title__tagline'>Nossos projetos recentes</h6>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-2'>
						<i class='section-title__circle'></i>
					</div>
				</div>
			</div>
			<h3 class='section-title__title title-animation'>Apresentando Excelência em<br> Todo
				<span>Serviço</span>
			</h3>
		</div>
		<ul class='project-filter style1 post-filter has-dynamic-filters-counter list-unstyled'>
			<li data-filter='.filter-item' class='active'><span class='filter-text'>Todos</span></li>
			<li data-filter='.room'><span class='filter-text'>Dano de granizo</span></li>
			<li data-filter='.spa'><span class='filter-text'>Seguro contra inundações</span></li>
			<li data-filter='.swi'><span class='filter-text'>Reparação Elétrica</span></li>
			<li data-filter='.res'><span class='filter-text'>Atualização de iluminação</span></li>
			<li data-filter='.wed'><span class='filter-text last-pd-none'>Revestimento Cerâmico</span></li>
		</ul>
		<div class='row filter-layout'>
			<!--Project Three Single Start-->
			<div class='col-xl-3 col-lg-6 col-md-6 filter-item swi'>
				<div class='project-three__single'>
					<div class='project-three__img-box'>
						<div class='project-three__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-3-1.jpg' alt=''>
						</div>
						<div class='project-three__icon'>
							<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-3-1.jpg' class='img-popup'><span
										class='icon-next'></span></a>
						</div>
						<div class='project-three__content-inner'>
							<div class='project-three__content'>
								<span class='project-three__sub-title'>Tempo gasto: 3 dias</span>
								<h3 class='project-three__title'><a href='project-details.html'>Carro Clássico
										Restauração</a></h3>
							</div>
							<div class='project-three__video-link'>
								<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
									<div class='project-three__video-icon'>
										<span class='fas fa-play'></span>
										<i class='ripple'></i>
									</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--Project Three Single End-->
			<!--Project Three Single Start-->
			<div class='col-xl-3 col-lg-6 col-md-6 filter-item spa'>
				<div class='project-three__single'>
					<div class='project-three__img-box'>
						<div class='project-three__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-3-2.jpg' alt=''>
						</div>
						<div class='project-three__icon'>
							<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-3-2.jpg' class='img-popup'><span
										class='icon-next'></span></a>
						</div>
						<div class='project-three__content-inner'>
							<div class='project-three__content'>
								<span class='project-three__sub-title'>Tempo gasto: 3 dias</span>
								<h3 class='project-three__title'><a href='project-details.html'>Carro Clássico
										Restauração</a></h3>
							</div>
							<div class='project-three__video-link'>
								<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
									<div class='project-three__video-icon'>
										<span class='fas fa-play'></span>
										<i class='ripple'></i>
									</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--Project Three Single End-->
			<!--Project Three Single Start-->
			<div class='col-xl-3 col-lg-6 col-md-6 filter-item wed'>
				<div class='project-three__single'>
					<div class='project-three__img-box'>
						<div class='project-three__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-3-3.jpg' alt=''>
						</div>
						<div class='project-three__icon'>
							<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-3-3.jpg' class='img-popup'><span
										class='icon-next'></span></a>
						</div>
						<div class='project-three__content-inner'>
							<div class='project-three__content'>
								<span class='project-three__sub-title'>Tempo gasto: 3 dias</span>
								<h3 class='project-three__title'><a href='project-details.html'>Carro Clássico
										Restauração</a></h3>
							</div>
							<div class='project-three__video-link'>
								<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
									<div class='project-three__video-icon'>
										<span class='fas fa-play'></span>
										<i class='ripple'></i>
									</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--Project Three Single End-->
			<!--Project Three Single Start-->
			<div class='col-xl-3 col-lg-6 col-md-6 filter-item res'>
				<div class='project-three__single'>
					<div class='project-three__img-box'>
						<div class='project-three__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-3-4.jpg' alt=''>
						</div>
						<div class='project-three__icon'>
							<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-3-4.jpg' class='img-popup'><span
										class='icon-next'></span></a>
						</div>
						<div class='project-three__content-inner'>
							<div class='project-three__content'>
								<span class='project-three__sub-title'>Tempo gasto: 3 dias</span>
								<h3 class='project-three__title'><a href='project-details.html'>Carro Clássico
										Restauração</a></h3>
							</div>
							<div class='project-three__video-link'>
								<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
									<div class='project-three__video-icon'>
										<span class='fas fa-play'></span>
										<i class='ripple'></i>
									</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--Project Three Single End-->
			<!--Project Three Single Start-->
			<div class='col-xl-3 col-lg-6 col-md-6 filter-item res'>
				<div class='project-three__single'>
					<div class='project-three__img-box'>
						<div class='project-three__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-3-5.jpg' alt=''>
						</div>
						<div class='project-three__icon'>
							<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-3-5.jpg' class='img-popup'><span
										class='icon-next'></span></a>
						</div>
						<div class='project-three__content-inner'>
							<div class='project-three__content'>
								<span class='project-three__sub-title'>Tempo gasto: 3 dias</span>
								<h3 class='project-three__title'><a href='project-details.html'>Carro Clássico
										Restauração</a></h3>
							</div>
							<div class='project-three__video-link'>
								<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
									<div class='project-three__video-icon'>
										<span class='fas fa-play'></span>
										<i class='ripple'></i>
									</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--Project Three Single End-->
			<!--Project Three Single Start-->
			<div class='col-xl-3 col-lg-6 col-md-6 filter-item room'>
				<div class='project-three__single'>
					<div class='project-three__img-box'>
						<div class='project-three__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-3-6.jpg' alt=''>
						</div>
						<div class='project-three__icon'>
							<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-3-6.jpg' class='img-popup'><span
										class='icon-next'></span></a>
						</div>
						<div class='project-three__content-inner'>
							<div class='project-three__content'>
								<span class='project-three__sub-title'>Tempo gasto: 3 dias</span>
								<h3 class='project-three__title'><a href='project-details.html'>Carro Clássico
										Restauração</a></h3>
							</div>
							<div class='project-three__video-link'>
								<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
									<div class='project-three__video-icon'>
										<span class='fas fa-play'></span>
										<i class='ripple'></i>
									</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--Project Three Single End-->
			<!--Project Three Single Start-->
			<div class='col-xl-3 col-lg-6 col-md-6 filter-item wed'>
				<div class='project-three__single'>
					<div class='project-three__img-box'>
						<div class='project-three__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-3-7.jpg' alt=''>
						</div>
						<div class='project-three__icon'>
							<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-3-7.jpg' class='img-popup'><span
										class='icon-next'></span></a>
						</div>
						<div class='project-three__content-inner'>
							<div class='project-three__content'>
								<span class='project-three__sub-title'>Tempo gasto: 3 dias</span>
								<h3 class='project-three__title'><a href='project-details.html'>Carro Clássico
										Restauração</a></h3>
							</div>
							<div class='project-three__video-link'>
								<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
									<div class='project-three__video-icon'>
										<span class='fas fa-play'></span>
										<i class='ripple'></i>
									</div>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--Project Three Single End-->
			<!--Project Three Single Start-->
			<div class='col-xl-3 col-lg-6 col-md-6 filter-item wed'>
				<div class='project-three__single'>
					<div class='project-three__img-box'>
						<div class='project-three__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-3-8.jpg' alt=''>
						</div>
						<div class='project-three__icon'>
							<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-3-8.jpg' class='img-popup'><span
										class='icon-next'></span></a>
						</div>
						<div class='project-three__content-inner'>
							<div class='project-three__content'>
								<span class='project-three__sub-title'>Tempo gasto: 3 dias</span>
								<h3 class='project-three__title'><a href='project-details.html'>Carro Clássico
										Restauração</a></h3>
							</div>
							<div class='project-three__video-link'>
								<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
									<div class='project-three__video-icon'>
										<span class='fas fa-play'></span>
										<i class='ripple'></i>
									</div>
								</a>
							</div>
						</div>
					</div>
				</div>
				<!--Project Three Single End-->
			</div>
		</div>
	</div>
</section>
<!--Project Three End-->
