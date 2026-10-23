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
    /**
     * Página "Sobre" — tema Zevitor (estática, como o doripel/page-sobre.php).
     * Visual: about.html do template Servixa (page-header + about-one).
     * Conteúdo é placeholder — substituir pelos textos reais do cliente.
     */

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
			<h3>Serviços</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='<?= BASE ?>'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Serviços Automotivos</li>
				</ul>
			</div>
		</div>
	</div>
</section>
<!--Page Header End-->

<!--Service Page Start-->
<section class='services-page'>
	<div class='container'>
		<div class='row'>
			<!--Services One Single Start -->
			<div class='col-xl-4 col-lg-4 col-md-6'>
				<div class='services-one__single'>
					<div class='services-one__img-box'>
						<div class='services-one__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-1-1.jpg' alt=''>
						</div>
						<div class='services-one__count'>01</div>
					</div>
					<div class='services-one__content'>
						<div class='services-one__icon'>
							<span class='icon-breakdown'></span>
						</div>
						<h3 class='services-one__title'>
							<a href='engine-repair.html'>Reparo automotivo</a>
						</h3>
						<p class='services-one__text'>É um fato há muito estabelecido que um leitor será
							distraído pelo conteúdo legível de uma página ao observar seu layout.</p>
						<div class='services-one__btn-box'>
							<a href='engine-repair.html'>Leia mais<span class='icon-next'></span></a>
						</div>
					</div>
				</div>
			</div>
			<!--Services One Single End -->
			<!--Services One Single Start -->
			<div class='col-xl-4 col-lg-4 col-md-6'>
				<div class='services-one__single'>
					<div class='services-one__img-box'>
						<div class='services-one__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-1-2.jpg' alt=''>
						</div>
						<div class='services-one__count'>02</div>
					</div>
					<div class='services-one__content'>
						<div class='services-one__icon'>
							<span class='icon-car'></span>
						</div>
						<h3 class='services-one__title'>
							<a href='brake-service.html'>Inspeção segura do freio</a>
						</h3>
						<p class='services-one__text'>É um fato há muito estabelecido que um leitor será
							distraído pelo conteúdo legível de uma página ao observar seu layout.</p>
						<div class='services-one__btn-box'>
							<a href='brake-service.html'>Leia mais<span class='icon-next'></span></a>
						</div>
					</div>
				</div>
			</div>
			<!--Services One Single End -->
			<!--Services One Single Start -->
			<div class='col-xl-4 col-lg-4 col-md-6'>
				<div class='services-one__single'>
					<div class='services-one__img-box'>
						<div class='services-one__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-1-3.jpg' alt=''>
						</div>
						<div class='services-one__count'>03</div>
					</div>
					<div class='services-one__content'>
						<div class='services-one__icon'>
							<span class='icon-mechanical'></span>
						</div>
						<h3 class='services-one__title'>
							<a href='engine-repair.html'>Diagnóstico do motor</a>
						</h3>
						<p class='services-one__text'>É um fato há muito estabelecido que um leitor será
							distraído pelo conteúdo legível de uma página ao observar seu layout.</p>
						<div class='services-one__btn-box'>
							<a href='engine-repair.html'>Leia mais<span class='icon-next'></span></a>
						</div>
					</div>
				</div>
			</div>
			<!--Services One Single End -->
			<!--Services One Single Start -->
			<div class='col-xl-4 col-lg-4 col-md-6'>
				<div class='services-one__single'>
					<div class='services-one__img-box'>
						<div class='services-one__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-1-4.jpg' alt=''>
						</div>
						<div class='services-one__count'>04</div>
					</div>
					<div class='services-one__content'>
						<div class='services-one__icon'>
							<span class='icon-tyre'></span>
						</div>
						<h3 class='services-one__title'>
							<a href='tire-wheel-services.html'>Troca de pneus</a>
						</h3>
						<p class='services-one__text'>É um fato há muito estabelecido que um leitor será
							distraído pelo conteúdo legível de uma página ao observar seu layout.</p>
						<div class='services-one__btn-box'>
							<a href='tire-wheel-services.html'>Leia mais<span class='icon-next'></span></a>
						</div>
					</div>
				</div>
			</div>
			<!--Services One Single End -->
			<!--Services One Single Start -->
			<div class='col-xl-4 col-lg-4 col-md-6'>
				<div class='services-one__single'>
					<div class='services-one__img-box'>
						<div class='services-one__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-1-5.jpg' alt=''>
						</div>
						<div class='services-one__count'>05</div>
					</div>
					<div class='services-one__content'>
						<div class='services-one__icon'>
							<span class='icon-oil'></span>
						</div>
						<h3 class='services-one__title'>
							<a href='oil-change-filters.html'>Troca de óleo</a>
						</h3>
						<p class='services-one__text'>É um fato há muito estabelecido que um leitor será
							distraído pelo conteúdo legível de uma página ao observar seu layout.</p>
						<div class='services-one__btn-box'>
							<a href='oil-change-filters.html'>Leia mais<span class='icon-next'></span></a>
						</div>
					</div>
				</div>
			</div>
			<!--Services One Single End -->
			<!--Services One Single Start -->
			<div class='col-xl-4 col-lg-4 col-md-6'>
				<div class='services-one__single'>
					<div class='services-one__img-box'>
						<div class='services-one__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-1-6.jpg' alt=''>
						</div>
						<div class='services-one__count'>06</div>
					</div>
					<div class='services-one__content'>
						<div class='services-one__icon'>
							<span class='icon-battery'></span>
						</div>
						<h3 class='services-one__title'>
							<a href='battery-electrical.html'>Serviço de bateria</a>
						</h3>
						<p class='services-one__text'>É um fato há muito estabelecido que um leitor será
							distraído pelo conteúdo legível de uma página ao observar seu layout.</p>
						<div class='services-one__btn-box'>
							<a href='battery-electrical.html'>Leia mais<span class='icon-next'></span></a>
						</div>
					</div>
				</div>
			</div>
			<!--Services One Single End -->
		</div>
	</div>
</section>
<!--Service Page End-->
