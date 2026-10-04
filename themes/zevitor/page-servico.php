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
			<h3>Serviço de freios</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='index.html'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Serviços</li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Serviço de freios</li>
				</ul>
			</div>
		</div>
	</div>
</section>
<!--Page Header End-->

<!--Service Details Start-->
<section class='service-details'>
	<div class='container'>
		<div class='row'>
			<div class='col-xl-4 col-lg-5'>
				<div class='service-details__sidebar'>
					<div class='service-details__services-box'>
						<h3 class='service-details__services-title'>Nossos serviços</h3>
						<ul class='service-details__services-list list-unstyled'>
							<li>
								<a href='international-transport.html'>Reparo do motor<span
											class='icon-next'></span></a>
							</li>
							<li>
								<a href='track-transport.html'>Troca de óleo e filtros<span
											class='icon-next'></span></a>
							</li>
							<li class='active'>
								<a href='personal-delivery.html'>Serviço de freios<span class='icon-next'></span></a>
							</li>
							<li>
								<a href='ocean-transport.html'>Reparo de ar-condicionado e aquecimento<span
											class='icon-next'></span></a>
							</li>
							<li>
								<a href='warehouse-facility.html'>Serviços de pneus e rodas<span
											class='icon-next'></span></a>
							</li>
							<li>
								<a href='emergency-transport.html'>Bateria e elétrica<span class='icon-next'></span></a>
							</li>
						</ul>
					</div>
					<div class='service-details__sidebar-contact'>
						<div class='service-details__sidebar-contact-img'>
							<div class='inner'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/services/service-details-sidebar-img.png'
								     alt=''>
							</div>
						</div>

						<div class='service-details__sidebar-contact-content'>
							<div class='icon'>
								<span class='icon-phone-call'></span>
							</div>
							<h2><a href='tel:585858575084'>+58 585 857 5084</a></h2>
							<p>Se precisar de ajuda<br>
								Entre em contato conosco</p>
						</div>
					</div>
					<div class='service-details__sidebar-download-box'>
						<h3 class='service-details__services-title'>Baixar</h3>
						<div class='service-details__sidebar-single-download'>

							<ul class='clearfix list-unstyled'>
								<li>
									<div class='content-box'>
										<div class='icon'>
											<span class='far fa-file-pdf'></span>
										</div>
										<div class='text-box'>
											<h2><a href='#'>Baixar PDF</a></h2>
											<p><a href='#'>Baixar</a></p>
										</div>
									</div>

									<div class='btn-box'>
										<a href='#'><span class='far fa-cloud-download'></span></a>
									</div>
								</li>

								<li>
									<div class='content-box'>
										<div class='icon'>
											<span class='far fa-file-pdf'></span>
										</div>
										<div class='text-box'>
											<h2><a href='#'>Baixar PDF</a></h2>
											<p><a href='#'>Baixar</a></p>
										</div>
									</div>

									<div class='btn-box'>
										<a href='#'><span class='far fa-cloud-download'></span></a>
									</div>
								</li>

								<li>
									<div class='content-box'>
										<div class='icon'>
											<span class='far fa-file-pdf'></span>
										</div>
										<div class='text-box'>
											<h2><a href='#'>Baixar PDF</a></h2>
											<p><a href='#'>Baixar</a></p>
										</div>
									</div>

									<div class='btn-box'>
										<a href='#'><span class='far fa-cloud-download'></span></a>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class='col-xl-8 col-lg-7'>
				<div class='service-details__left'>
					<div class='service-details__img'>
						<img src='<?= INCLUDE_PATH ?>/assets/images/services/service-details-img-3.jpg' alt=''>
					</div>
					<h3 class='service-details__title-1'>Serviço de freios</h3>
					<p class='service-details__text-1'>Ut enim ad minim veniam, quis nostrud exercitation
						ullamco laboris nisi ut aliquip ex ea comodo consequat. Duis aute irure dolor em
						reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Exceto
						sint occaecat cupidatat non proident, sunt in culpa</p>
					<p class='service-details__text-2'>Consectetur adipiscing elit, sed do eiusmod tempor
						incidente com Laborer e Dolore Magna Aliqua. Fora enigma ad minim veniam, quis nostrud
						exercitation ullamco laboris nisi ut aliquip ex ea comodo consequat. Duis aute inure
						dor no reprehenderit in voluptate velit esse cillum dolore eu fugiat null
						pariatur. Excepteur snit occaecat cupidatat non proident, sunt in culpa qui officia
						deserunt mollit anim id est laborum.</p>
					<ul class='service-details__points-list list-unstyled'>
						<li>
							<div class='icon'>
								<span class='icon-next'></span>
							</div>
							<p>É um fato há muito estabelecido que um leitor ficará distraído bioiiy pela razão
								dablea </p>
						</li>
						<li>
							<div class='icon'>
								<span class='icon-next'></span>
							</div>
							<p>Distribuir bioiiy o conteúdo legível de uma página ao observar seu layout
							</p>
						</li>
						<li>
							<div class='icon'>
								<span class='icon-next'></span>
							</div>
							<p>Conteúdo de uma página ao observar seu ponto de layout</p>
						</li>
						<li>
							<div class='icon'>
								<span class='icon-next'></span>
							</div>
							<p>O leitor será distraído bioiiy pelo conteúdo legível de uma página ao olhar
							</p>
						</li>
					</ul>
					<div class='service-details__img-box'>
						<div class='row'>
							<div class='col-xl-6'>
								<div class='service-details__img-box-single'>
									<div class='service-details__img-box-img'>
										<img src='<?= INCLUDE_PATH ?>/assets/images/services/service-details-img-box-img-1.jpg'
										     alt=''>
									</div>
									<div class='service-details__img-box-content'>
										<div class='service-details__img-box-content-icon-and-title'>
											<div class='service-details__img-box-content-icon'>
												<span class='icon-guarantee'></span>
											</div>
											<h3 class='service-details__img-box-content-title'>Trabalho Completo de
												Qualidade
											</h3>
										</div>
										<p class='service-details__img-box-content-text'>Duis arura dolor aguda
											em
											reprehenderit in voluptate velit esse cillum dolore Velit esse quam
											nihil molestiae thos consequatur, Velia alivia chillum dolore</p>
									</div>
								</div>
							</div>
							<div class='col-xl-6'>
								<div class='service-details__img-box-single'>
									<div class='service-details__img-box-img'>
										<img src='<?= INCLUDE_PATH ?>/assets/images/services/service-details-img-box-img-2.jpg'
										     alt=''>
									</div>
									<div class='service-details__img-box-content'>
										<div class='service-details__img-box-content-icon-and-title'>
											<div class='service-details__img-box-content-icon'>
												<span class='icon-satisfaction'></span>
											</div>
											<h3 class='service-details__img-box-content-title'>100% Trabalho
												Satisfação</h3>
										</div>
										<p class='service-details__img-box-content-text'>Duis arura dolor aguda
											em
											reprehenderit in voluptate velit esse cillum dolore Velit esse quam
											nihil molestiae thos consequatur, Velia alivia chillum dolore</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class='faq-page__single'>
						<div class='faq-one__left'>
							<div class='accrodion-grp' data-grp-name='faq-one-accrodion'>
								<div class='accrodion wow fadeInLeft' data-wow-delay='0ms' data-wow-duration='1500ms'>
									<div class='accrodion-title'>
										<h4>O que está incluído em um serviço de carro completo?</h4>
									</div>
									<div class='accrodion-content'>
										<div class='inner'>
											<p>Um serviço completo inclui troca de óleo, substituição de filtro, freio
												inspeção, verificação de pneus, teste de bateria e diagnóstico do motor.
											</p>
										</div><!-- /.inner -->
									</div>
								</div>
								<div class='accrodion active wow fadeInRight' data-wow-delay='100ms'
								     data-wow-duration='1500ms'>
									<div class='accrodion-title'>
										<h4>Vocês oferecem serviço de coleta e entrega?</h4>
									</div>
									<div class='accrodion-content'>
										<div class='inner'>
											<p>Recomendamos uma manutenção completa a cada 6 meses ou 5.000–10.000 km,
												dependendo
												no seu<br> hábitos de condução.
											</p>
										</div><!-- /.inner -->
									</div>
								</div>
								<div class='accrodion wow fadeInLeft' data-wow-delay='200ms' data-wow-duration='1500ms'>
									<div class='accrodion-title'>
										<h4>Quanto tempo leva um serviço padrão?</h4>
									</div>
									<div class='accrodion-content'>
										<div class='inner'>
											<p>A maioria dos serviços padrão são concluídos em 2–3 horas.Com certeza!
												Aproveite
												nossa confortável sala de espera com Wi-Fi e bebidas.
											</p>
										</div><!-- /.inner -->
									</div>
								</div>
								<div class='accrodion wow fadeInRight' data-wow-delay='300ms'
								     data-wow-duration='1500ms'>
									<div class='accrodion-title'>
										<h4>O seu serviço está coberto pela garantia?</h4>
									</div>
									<div class='accrodion-content'>
										<div class='inner'>
											<p>Sim, oferecemos garantia de serviço de até 3 meses, dependendo do
												tipo de
												reparo. Fornecemos estimativas antecipadas para que você saiba
												exatamente o que fazer
												espere.
											</p>
										</div><!-- /.inner -->
									</div>
								</div>
								<div class='accrodion wow fadeInLeft' data-wow-delay='400ms' data-wow-duration='1500ms'>
									<div class='accrodion-title'>
										<h4>Você fornece assistência rodoviária de emergência?</h4>
									</div>
									<div class='accrodion-content'>
										<div class='inner'>
											<p>Sim, reboque de emergência 24 horas por dia, 7 dias por semana e suporte
												para reparos na estrada são
												disponível.Nós
												forneça estimativas iniciais para que você saiba exatamente o que
												esperar.
											</p>
										</div><!-- /.inner -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!--Service Details End-->
