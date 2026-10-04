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
			<h3>Sobre nós</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='index.html'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Sobre nós</li>
				</ul>
			</div>
		</div>
	</div>
</section>
<!--Page Header End-->

<!--About One Start -->
<?php
    include_once REQUIRE_PATH . '/inc/about-one.php'; ?>
<!--About One End -->

<!-- Service Three Start -->
<section class='service-three'>
	<div class='container'>
		<div class='service-three__top'>
			<div class='section-title text-left sec-title-animation animation-style1'>
				<div class='section-title__tagline-box'>
					<div class='section-title__tagline-border'>
						<div class='section-title__shape-1'>
							<i class='section-title__circle'></i>
						</div>
					</div>
					<h6 class='section-title__tagline'>Serviços</h6>
					<div class='section-title__tagline-border'>
						<div class='section-title__shape-2'>
							<i class='section-title__circle'></i>
						</div>
					</div>
				</div>
				<h3 class='section-title__title title-animation'>Nosso carro automotivo <span>Serviços</span>
				</h3>
			</div>
			<div class='service-three__btn-box'>
				<a href='services-v1.html' class='thm-btn'>Todos os serviços
					<span class='icon-next'></span>
				</a>
			</div>
		</div>
		<div class='service-three__tab-box service-three-tabs-box'>
			<div class='row'>
				<div class='col-xl-3 col-lg-4'>
					<div class='service-three__tab-buttons-box'>
						<ul class='service-three-tab-buttons service-three-tab-btns clearfix list-unstyled'>

                            <?php
                                $Read ??= new Read();

                                $Read->exeRead(DB_SERVICES_CATEGORIES);
                                if ($Read->getResult()):
                                    $active = 0;
                                    foreach ($Read->getResult() as $category):
                                        $active++;
                                        $cats[] = $category['category_id'];
                                        ?>
										<li data-tab='#service-<?= $category['category_id'] ?>'
										    class=' p-tab-btn <?= $active === 1 ? 'active-btn' : '' ?>'>
											<span><?= $category['category_title'] ?></span><i
													class='fas fa-arrow-right'></i></li>

                                    <?php
                                    endforeach;
                                    unset($active);

                                endif;
                            ?>

							<!--<li data-tab='#service-2' class='p-tab-btn'>
								<span>Troca de óleo e filtros</span>
								<i class='fas fa-arrow-right'></i>
							</li>
							<li data-tab='#service-3' class='p-tab-btn'>
								<span>Serviço de freios</span>
								<i class='fas fa-arrow-right'></i>
							</li>
							<li data-tab='#service-4' class='p-tab-btn'>
								<span>Reparo de ar-condicionado e aquecimento</span>
								<i class='fas fa-arrow-right'></i>
							</li>
							<li data-tab='#service-5' class='p-tab-btn'>
								<span>Serviços de pneus e rodas</span>
								<i class='fas fa-arrow-right'></i>
							</li>
							<li data-tab='#service-6' class='p-tab-btn'>
								<span>Bateria e elétrica</span>
								<i class='fas fa-arrow-right'></i>
							</li>-->
						</ul>
					</div>
				</div>
				<div class='col-xl-9 col-lg-8'>
					<div class='service-three__tab-content-box'>
						<div class='p-tabs-content'>

                            <?php
                                if ($cats):

                                    foreach ($cats as $cat):

                                        ?>

										<!--tab-->
										<div class='p-tab active-tab' id='service-<?= $cat ?>'>
											<div class='service-three__inner'>
												<div class='service-three__carousel owl-carousel owl-theme'>

                                                    <?php
                                                        $Read->exeRead(
                                                            DB_SERVICES,
                                                            " WHERE svc_category = :cat",
                                                            "cat={$cat}"
                                                        );


                                                        if ($Read->getResult()):


                                                            foreach ($Read->getResult() as $service):


                                                                ?>


																<!-- Service Three Single Start -->
																<div class='item'>
																	<div class='service-three__single'>
																		<div class='service-three__img-box'>
																			<div class='service-three__img'>
																				<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-1.jpg'
																				     alt=''>
																			</div>
																			<div class='service-three__icon'>
																				<span class='icon-diagnostic'></span>
																			</div>
																		</div>
																		<div class='service-three__content'>
																			<div class='service-three__content-inner'>
																				<h3 class='service-three__title'>
																					<a href='engine-repair
																					.html'><?= $service['svc_title']
                                                                                        ?></a>
																				</h3>
																				<p class='service-three__text'><?=
                                                                                        $service['svc_subtitle']
                                                                                    ?></p>
																			</div>
																			<div class='service-three__btn-box-two'>
																				<a href='engine-repair.html'>Leia
																					mais<span
																							class='icon-next'></span></a>
																			</div>
																		</div>
																	</div>
																</div>

                                                            <?php
                                                            endforeach;
                                                        endif;
                                                    ?>
													<!-- Service Three Single End
													<!-- Service Three Single Start
													<div class='item'>
														<div class='service-three__single'>
															<div class='service-three__img-box'>
																<div class='service-three__img'>
																	<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-2.jpg'
																	     alt=''>
																</div>
																<div class='service-three__icon'>
																	<span class='icon-car-parts'></span>
																</div>
															</div>
															<div class='service-three__content'>
																<div class='service-three__content-inner'>
																	<h3 class='service-three__title'>
																		<a href='engine-repair.html'>Correia dentada
																			Substituição</a>
																	</h3>
																	<p class='service-three__text'>É um há muito
																		estabelecido
																		fato
																		que um leitor será
																		distraído pelo conteúdo legível de uma
																		página.</p>
																</div>
																<div class='service-three__btn-box-two'>
																	<a href='engine-repair.html'>Leia mais<span
																				class='icon-next'></span></a>
																</div>
															</div>
														</div>
													</div>
													<!-- Service Three Single End
													<!-- Service Three Single Start
													<div class='item'>
														<div class='service-three__single'>
															<div class='service-three__img-box'>
																<div class='service-three__img'>
																	<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-3.jpg'
																	     alt=''>
																</div>
																<div class='service-three__icon'>
																	<span class='icon-tools'></span>
																</div>
															</div>
															<div class='service-three__content'>
																<div class='service-three__content-inner'>
																	<h3 class='service-three__title'>
																		<a href='engine-repair.html'>Injeção de
																			Combustível
																			Limpeza</a>
																	</h3>
																	<p class='service-three__text'>É um há muito
																		estabelecido
																		fato
																		que um leitor será
																		distraído pelo conteúdo legível de uma
																		página.</p>
																</div>
																<div class='service-three__btn-box-two'>
																	<a href='engine-repair.html'>Leia mais<span
																				class='icon-next'></span></a>
																</div>
															</div>
														</div>
													</div>
													<!-- Service Three Single End
													<!-- Service Three Single Start
													<div class='item'>
														<div class='service-three__single'>
															<div class='service-three__img-box'>
																<div class='service-three__img'>
																	<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-4.jpg'
																	     alt=''>
																</div>
																<div class='service-three__icon'>
																	<span class='icon-fan'></span>
																</div>
															</div>
															<div class='service-three__content'>
																<div class='service-three__content-inner'>
																	<h3 class='service-three__title'>
																		<a href='engine-repair.html'>Sistema de
																			resfriamento
																			Reparar</a>
																	</h3>
																	<p class='service-three__text'>É um há muito
																		estabelecido
																		fato
																		que um leitor será
																		distraído pelo conteúdo legível de uma
																		página.</p>
																</div>
																<div class='service-three__btn-box-two'>
																	<a href='engine-repair.html'>Leia mais<span
																				class='icon-next'></span></a>
																</div>
															</div>
														</div>
													</div>
													<!-- Service Three Single End
													<!-- Service Three Single Start
													<div class='item'>
														<div class='service-three__single'>
															<div class='service-three__img-box'>
																<div class='service-three__img'>
																	<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-5.jpg'
																	     alt=''>
																</div>
																<div class='service-three__icon'>
																	<span class='icon-mechanical'></span>
																</div>
															</div>
															<div class='service-three__content'>
																<div class='service-three__content-inner'>
																	<h3 class='service-three__title'>
																		<a href='engine-repair.html'>Montagem do motor
																			Substituição</a>
																	</h3>
																	<p class='service-three__text'>É um há muito
																		estabelecido
																		fato
																		que um leitor será
																		distraído pelo conteúdo legível de uma
																		página.</p>
																</div>
																<div class='service-three__btn-box-two'>
																	<a href='engine-repair.html'>Leia mais<span
																				class='icon-next'></span></a>
																</div>
															</div>
														</div>
													</div>
													<!-- Service Three Single End
													<!-- Service Three Single Start
													<div class='item'>
														<div class='service-three__single'>
															<div class='service-three__img-box'>
																<div class='service-three__img'>
																	<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-6.jpg'
																	     alt=''>
																</div>
																<div class='service-three__icon'>
																	<span class='icon-spare-parts'></span>
																</div>
															</div>
															<div class='service-three__content'>
																<div class='service-three__content-inner'>
																	<h3 class='service-three__title'>
																		<a href='engine-repair.html'>Revisão do
																			motor</a>
																	</h3>
																	<p class='service-three__text'>É um há muito
																		estabelecido
																		fato
																		que um leitor será
																		distraído pelo conteúdo legível de uma
																		página.</p>
																</div>
																<div class='service-three__btn-box-two'>
																	<a href='engine-repair.html'>Leia mais<span
																				class='icon-next'></span></a>
																</div>
															</div>
														</div>
													</div>
													<!-- Service Three Single End
													<!-- Service Three Single Start
													<div class='item'>
														<div class='service-three__single'>
															<div class='service-three__img-box'>
																<div class='service-three__img'>
																	<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-2.jpg'
																	     alt=''>
																</div>
																<div class='service-three__icon'>
																	<span class='icon-car-parts'></span>
																</div>
															</div>
															<div class='service-three__content'>
																<div class='service-three__content-inner'>
																	<h3 class='service-three__title'>
																		<a href='engine-repair.html'>Correia dentada
																			Substituição</a>
																	</h3>
																	<p class='service-three__text'>É um há muito
																		estabelecido
																		fato
																		que um leitor será
																		distraído pelo conteúdo legível de uma
																		página.</p>
																</div>
																<div class='service-three__btn-box-two'>
																	<a href='engine-repair.html'>Leia mais<span
																				class='icon-next'></span></a>
																</div>
															</div>
														</div>
													</div>
													<!-- Service Three Single End -->
												</div>
											</div>
										</div>
										<!--Tab-->

                                    <?php


                                    endforeach;
                                endif;
                            ?>

							<!--tab-->
							<div class='p-tab' id='service-2'>
								<div class='service-three__inner'>
									<div class='service-three__carousel owl-carousel owl-theme'>
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-7.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-oil'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='oil-change-filters.html'>Óleo Convencional
																Mudança</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='oil-change-filters.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-8.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-car'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='oil-change-filters.html'>Óleo Sintético
																Mudança</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='oil-change-filters.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-9.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-spare-parts-1'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='oil-change-filters.html'>Filtro de óleo
																Substituição</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='oil-change-filters.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-10.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-spare-parts'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='oil-change-filters.html'>Filtro de ar de cabine
																Substituição</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='oil-change-filters.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-11.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-part'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='oil-change-filters.html'>Filtro de Combustível
																Substituição</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='oil-change-filters.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-12.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-clock-1'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='oil-change-filters.html'>Agendado
																Manutenção</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='oil-change-filters.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-8.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-spark-plug'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='oil-change-filters.html'>Óleo Sintético
																Mudança</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='oil-change-filters.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
									</div>
								</div>
							</div>
							<!--tab-->
							<!--tab-->
							<div class='p-tab' id='service-3'>
								<div class='service-three__inner'>
									<div class='service-three__carousel owl-carousel owl-theme'>
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-13.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-brake-disc'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='brake-service.html'>Pastilha de freio
																Substituição</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='brake-service.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-14.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-motor'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='brake-service.html'>Lavagem de fluido de freio</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='brake-service.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-15.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-brake'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='brake-service.html'>Rotor de freio
																Recapeamento</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='brake-service.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-16.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-diagnostic'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='brake-service.html'>Diagnóstico ABS</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='brake-service.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-17.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-technology'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='brake-service.html'>Reparo de pinça</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='brake-service.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-18.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-breakdown'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='brake-service.html'>Freio Completo
																Inspeção</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='brake-service.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-14.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-spare-parts-1'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='brake-service.html'>Lavagem de fluido de freio</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='brake-service.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
									</div>
								</div>
							</div>
							<!--tab-->
							<!--tab-->
							<div class='p-tab' id='service-4'>
								<div class='service-three__inner'>
									<div class='service-three__carousel owl-carousel owl-theme'>
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-19.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-spare-parts'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='ac-heating-repair.html'>Recarga de gás AC</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='ac-heating-repair.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-20.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-fan'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='ac-heating-repair.html'>Compressor CA</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='ac-heating-repair.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-21.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-motor'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='ac-heating-repair.html'>Motor soprador
																Reparar</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='ac-heating-repair.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-22.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-diagnostic'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='ac-heating-repair.html'>Clima da cabine
																Diagnóstico</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='ac-heating-repair.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-23.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-part'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='ac-heating-repair.html'>Núcleo de aquecimento
																Lavar</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='ac-heating-repair.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-24.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-breakdown'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='ac-heating-repair.html'>Controle de HVAC
																Calibração</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='ac-heating-repair.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-20.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-fan'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='ac-heating-repair.html'>Compressor CA</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='ac-heating-repair.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
									</div>
								</div>
							</div>
							<!--tab-->
							<!--tab-->
							<div class='p-tab' id='service-5'>
								<div class='service-three__inner'>
									<div class='service-three__carousel owl-carousel owl-theme'>
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-25.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-tire-1'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='tire-wheel-services.html'>Rotação dos pneus</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='tire-wheel-services.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-26.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-steering-wheel'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='tire-wheel-services.html'>Roda
																Alinhamento</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='tire-wheel-services.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-27.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-tire'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='tire-wheel-services.html'>Pneu
																Equilíbrio</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='tire-wheel-services.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-28.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-tyre'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='tire-wheel-services.html'>Pneu novo
																Instalação</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='tire-wheel-services.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-29.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-flat-tire'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='tire-wheel-services.html'>Punção
																Reparar</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='tire-wheel-services.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-30.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-spare-parts-1'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='tire-wheel-services.html'>Inspeção da jante e
																Reparar</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='tire-wheel-services.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-27.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-tyre'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='tire-wheel-services.html'>Pneu novo
																Instalação</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='tire-wheel-services.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
									</div>
								</div>
							</div>
							<!--tab-->
							<!--tab-->
							<div class='p-tab' id='service-6'>
								<div class='service-three__inner'>
									<div class='service-three__carousel owl-carousel owl-theme'>
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-31.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-battery'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='battery-electrical.html'>Bateria
																Teste</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='battery-electrical.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-32.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-battery'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='battery-electrical.html'>Bateria
																Substituição</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='battery-electrical.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-33.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-check-1'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='battery-electrical.html'>Alternador
																Verifique</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='battery-electrical.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-34.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-motor'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='battery-electrical.html'>Motor de partida
																Reparar</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='battery-electrical.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-35.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-low-beam'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='battery-electrical.html'>Farol
																Substituição</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='battery-electrical.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-36.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-diagnostic'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='battery-electrical.html'>Elétrica
																Diagnóstico</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='battery-electrical.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
										<!-- Service Three Single Start -->
										<div class='item'>
											<div class='service-three__single'>
												<div class='service-three__img-box'>
													<div class='service-three__img'>
														<img src='<?= INCLUDE_PATH ?>/assets/images/services/services-3-34.jpg'
														     alt=''>
													</div>
													<div class='service-three__icon'>
														<span class='icon-motor'></span>
													</div>
												</div>
												<div class='service-three__content'>
													<div class='service-three__content-inner'>
														<h3 class='service-three__title'>
															<a href='battery-electrical.html'>Motor de partida
																Reparar</a>
														</h3>
														<p class='service-three__text'>É um há muito estabelecido
															fato
															que um leitor será
															distraído pelo conteúdo legível de uma página.</p>
													</div>
													<div class='service-three__btn-box-two'>
														<a href='battery-electrical.html'>Leia mais<span
																	class='icon-next'></span></a>
													</div>
												</div>
											</div>
										</div>
										<!-- Service Three Single End -->
									</div>
								</div>
							</div>
							<!--tab-->
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- Service Three End -->

<!--Sliding Text Two Start-->
<section class='sliding-text-two sliding-text-three'>
	<div class='sliding-text-two__inner'>
		<ul class='sliding-text-two__list marquee_mode-3 list-unstyled'>
			<li>
				<div class='icon'>
					<span class='icon-courier-services'></span>
				</div>
				<h2>Serviço rápido e confiável</h2>
			</li>
			<li>
				<div class='icon'>
					<span class='icon-courier-services'></span>
				</div>
				<h2>Tornamos a manutenção fácil</h2>
			</li>
		</ul>
	</div>
</section>
<!--Sliding Text Two End-->

<!--Why Choose Two Start-->
<section class='why-choose-two about-page-why-choose'>
	<div class='container'>
		<div class='row'>
			<div class='col-xl-6'>
				<div class='why-choose-two__left'>
					<div class='section-title text-left sec-title-animation animation-style2'>
						<div class='section-title__tagline-box'>
							<div class='section-title__tagline-border'>
								<div class='section-title__shape-1'>
									<i class='section-title__circle'></i>
								</div>
							</div>
							<h6 class='section-title__tagline'>Por que nos escolher</h6>
							<div class='section-title__tagline-border'>
								<div class='section-title__shape-2'>
									<i class='section-title__circle'></i>
								</div>
							</div>
						</div>
						<h3 class='section-title__title title-animation'>Motivo da escolha
							Nosso <span>Serviços de carro!</span>
						</h3>
					</div>
					<p class='why-choose-two__text'>Uma empresa prestadora de serviços automotivos desempenha um papel
						fundamental no
						ecossistema global da cadeia de suprimentos, gerenciando eficientemente o movimento de
						mercadorias do ponto
						desde a origem até o destino final. Essas empresas oferecem uma gama diversificada.</p>
					<ul class='why-choose-two__points'>
						<li>
							<div class='icon'>
								<span class='icon-professional-services'></span>
							</div>
							<div class='content'>
								<h4>Soluções flexíveis</h4>
								<p>Existem muitas variações de passagens disponíveis, mas a maioria sofreu
									alteração de alguma forma, por injeção.</p>
							</div>
						</li>
						<li>
							<div class='icon'>
								<span class='icon-24-hours'></span>
							</div>
							<div class='content'>
								<h4>Suporte ilimitado 24 horas por dia, 7 dias por semana</h4>
								<p>Existem muitas variações de passagens disponíveis, mas a maioria sofreu
									alteração de alguma forma, por injeção.</p>
							</div>
						</li>
					</ul>
					<div class='why-choose-two__btn-and-call-box'>
						<div class='why-choose-two__btn-box'>
							<a href='about.html' class='thm-btn'>Leia mais<span class='icon-next'></span>
							</a>
						</div>
						<div class='why-choose-two__call-box'>
							<div class='icon'>
								<span class='icon-phone-call'></span>
							</div>
							<div class='content'>
								<p>Ligue agora</p>
								<h4><a href='tel:885747546027'>(88) 574 7546 027</a></h4>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class='col-xl-6'>
				<div class='why-choose-two__right'>
					<div class='why-choose-two__img-box'>
						<div class='why-choose-two__img'>
							<div class='before-after-twentytwenty' id='wrinkle-before-after'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/resources/why-choose-two-img-1.png' alt=''>
								<img src='<?= INCLUDE_PATH ?>/assets/images/resources/why-choose-two-img-2.png' alt=''>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!--Why Choose Two End-->

<!--Start Brand One-->
<section class='brand-one brand-three'>
	<div class='container'>
		<div class='brand-one__inner'>
			<div class='swiper-container brand-one__carousel'>
				<div class='swiper-wrapper'>
					<!--Start Brand One Single-->
					<div class='swiper-slide'>
						<div class='brand-one__single'>
							<div class='brand-one__single-inner'>
								<a href='#'><img src='<?= INCLUDE_PATH ?>/assets/images/brand/brand-1-1.png' alt=''></a>
							</div>
						</div>
					</div>
					<!--End Brand One Single-->

					<!--Start Brand One Single-->
					<div class='swiper-slide'>
						<div class='brand-one__single'>
							<div class='brand-one__single-inner'>
								<a href='#'><img src='<?= INCLUDE_PATH ?>/assets/images/brand/brand-1-2.png' alt=''></a>
							</div>
						</div>
					</div>
					<!--End Brand One Single-->

					<!--Start Brand One Single-->
					<div class='swiper-slide'>
						<div class='brand-one__single'>
							<div class='brand-one__single-inner'>
								<a href='#'><img src='<?= INCLUDE_PATH ?>/assets/images/brand/brand-1-3.png' alt=''></a>
							</div>
						</div>
					</div>
					<!--End Brand One Single-->

					<!--Start Brand One Single-->
					<div class='swiper-slide'>
						<div class='brand-one__single'>
							<div class='brand-one__single-inner'>
								<a href='#'><img src='<?= INCLUDE_PATH ?>/assets/images/brand/brand-1-4.png' alt=''></a>
							</div>
						</div>
					</div>
					<!--End Brand One Single-->

					<!--Start Brand One Single-->
					<div class='swiper-slide'>
						<div class='brand-one__single'>
							<div class='brand-one__single-inner'>
								<a href='#'><img src='<?= INCLUDE_PATH ?>/assets/images/brand/brand-1-5.png' alt=''></a>
							</div>
						</div>
					</div>
					<!--End Brand One Single-->
				</div>
			</div>
		</div>
	</div>
</section>
<!--End Brand One-->

<!--Team One Start -->
<section class='team-one'>
	<div class='team-one__shape-1 float-bob-x'>
		<img src='<?= INCLUDE_PATH ?>/assets/images/shapes/team-one-shape-1.png' alt=''>
	</div>
	<div class='container'>
		<div class='section-title text-center sec-title-animation animation-style1'>
			<div class='section-title__tagline-box'>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-1'>
						<i class='section-title__circle'></i>
					</div>
				</div>
				<h6 class='section-title__tagline'>TÉCNICO ESPECIALISTA</h6>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-2'>
						<i class='section-title__circle'></i>
					</div>
				</div>
			</div>
			<h3 class='section-title__title title-animation'>Conheça os especialistas que mantêm seu <br>Carro
				<span>Correndo suavemente</span>
			</h3>
		</div>
		<div class='team-one__inner'>
			<div class='row'>
				<!--Team One Single Start-->
				<div class='col-xl-3 col-lg-6 col-md-6 wow fadeInLeft' data-wow-delay='100ms'>
					<div class='team-one__single'>
						<div class='team-one__img-box'>
							<div class='team-one__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/team/team-1-1.jpg' alt=''>
							</div>
							<div class='team-one__social-box'>
								<div class='team-one__plus'>
									<span class='fas fa-share-alt'></span>
									<div class='team-one__social-list'>
										<a href='team-details.html'><span class='icon-facebook-app-symbol'></span></a>
										<a href='team-details.html'><span class='icon-twitter-1'></span></a>
										<a href='team-details.html'><span class='icon-instagram'></span></a>
										<a href='team-details.html'><span class='icon-pinterest'></span></a>
									</div>
								</div>
							</div>
						</div>
						<div class='team-one__content'>
							<h3 class='team-one__title'><a href='team-details.html'>Adam Smith</a></h3>
							<p class='team-one__sub-title'>Técnico de pneus</p>
						</div>
					</div>
				</div>
				<!--Team One Single End-->
				<!--Team One Single Start-->
				<div class='col-xl-3 col-lg-6 col-md-6 wow fadeInLeft' data-wow-delay='200ms'>
					<div class='team-one__single'>
						<div class='team-one__img-box'>
							<div class='team-one__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/team/team-1-2.jpg' alt=''>
							</div>
							<div class='team-one__social-box'>
								<div class='team-one__plus'>
									<span class='fas fa-share-alt'></span>
									<div class='team-one__social-list'>
										<a href='team-details.html'><span class='icon-facebook-app-symbol'></span></a>
										<a href='team-details.html'><span class='icon-twitter-1'></span></a>
										<a href='team-details.html'><span class='icon-instagram'></span></a>
										<a href='team-details.html'><span class='icon-pinterest'></span></a>
									</div>
								</div>
							</div>
						</div>
						<div class='team-one__content'>
							<h3 class='team-one__title'><a href='team-details.html'>Alisha Martin</a></h3>
							<p class='team-one__sub-title'>Especialista em Transmissão</p>
						</div>
					</div>
				</div>
				<!--Team One Single End-->
				<!--Team One Single Start-->
				<div class='col-xl-3 col-lg-6 col-md-6 wow fadeInRight' data-wow-delay='300ms'>
					<div class='team-one__single'>
						<div class='team-one__img-box'>
							<div class='team-one__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/team/team-1-3.jpg' alt=''>
							</div>
							<div class='team-one__social-box'>
								<div class='team-one__plus'>
									<span class='fas fa-share-alt'></span>
									<div class='team-one__social-list'>
										<a href='team-details.html'><span class='icon-facebook-app-symbol'></span></a>
										<a href='team-details.html'><span class='icon-twitter-1'></span></a>
										<a href='team-details.html'><span class='icon-instagram'></span></a>
										<a href='team-details.html'><span class='icon-pinterest'></span></a>
									</div>
								</div>
							</div>
						</div>
						<div class='team-one__content'>
							<h3 class='team-one__title'><a href='team-details.html'>Herbert Spin</a></h3>
							<p class='team-one__sub-title'>Especialista em freios</p>
						</div>
					</div>
				</div>
				<!--Team One Single End-->
				<!--Team One Single Start-->
				<div class='col-xl-3 col-lg-6 col-md-6 wow fadeInRight' data-wow-delay='400ms'>
					<div class='team-one__single'>
						<div class='team-one__img-box'>
							<div class='team-one__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/team/team-1-4.jpg' alt=''>
							</div>
							<div class='team-one__social-box'>
								<div class='team-one__plus'>
									<span class='fas fa-share-alt'></span>
									<div class='team-one__social-list'>
										<a href='team-details.html'><span class='icon-facebook-app-symbol'></span></a>
										<a href='team-details.html'><span class='icon-twitter-1'></span></a>
										<a href='team-details.html'><span class='icon-instagram'></span></a>
										<a href='team-details.html'><span class='icon-pinterest'></span></a>
									</div>
								</div>
							</div>
						</div>
						<div class='team-one__content'>
							<h3 class='team-one__title'><a href='team-details.html'>Aiyana Ansu</a></h3>
							<p class='team-one__sub-title'>Especialista em motores</p>
						</div>
					</div>
				</div>
				<!--Team One Single End-->
			</div>
		</div>
	</div>
</section>
<!--Team One End -->

<!--Video One Start -->
<section class='video-one'>
	<div class='video-one__bg jarallax' data-jarallax data-speed='0.2' data-imgposition='50% 0%'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/video-one-bg.jpg);'>
	</div>
	<div class='container'>
		<div class='video-one__inner'>
			<div class='video-one__video-link'>
				<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
					<div class='video-one__video-icon'>
						<span class='fas fa-play'></span>
						<i class='ripple'></i>
					</div>
				</a>
			</div>
			<h2 class='video-one__title'>Mãos profissionais, ferramentas especializadas e <br>paixão pela perfeição.
			</h2>
			<div class='video-one__btn-box'>
				<div class='video-one__shape-1'>
					<img src='<?= INCLUDE_PATH ?>/assets/images/shapes/video-one-shape-1.png' alt=''>
				</div>
				<a href='about.html' class='thm-btn'>Ver mais
					<span class='icon-next'></span>
				</a>
			</div>
		</div>
	</div>
</section>
<!--Video One End -->

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
				<h3 class='section-title__title title-animation'>Explore os resultados do nosso <br>Carro especialista
					<span>Servixa Soluções.</span>
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
				<!--Project One Single Start-->
				<div class='swiper-slide'>
					<div class='project-one__single'>
						<div class='project-one__img-box'>
							<div class='project-one__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-1-1.jpg' alt=''>
							</div>
							<div class='project-one__arrow'>
								<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-1-1.jpg'
								   class='img-popup'><span
											class='icon-next'></span></a>
							</div>
							<div class='project-one__content-inner'>
								<div class='project-one__content'>
									<div class='project-one__sub-title-and-shape'>
										<span class='project-one__sub-title'>Veículos reparados</span>
										<div class='project-one__sub-title-bdr'></div>
									</div>
									<h3 class='project-one__title'><a href='project-details.html'>Carro Clássico
											Restauração</a></h3>
								</div>
								<div class='project-one__video-link'>
									<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
										<div class='project-one__video-icon'>
											<span class='fas fa-play'></span>
											<i class='ripple'></i>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--Project One Single End-->
				<!--Project One Single Start-->
				<div class='swiper-slide'>
					<div class='project-one__single'>
						<div class='project-one__img-box'>
							<div class='project-one__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-1-2.jpg' alt=''>
							</div>
							<div class='project-one__arrow'>
								<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-1-2.jpg'
								   class='img-popup'><span
											class='icon-next'></span></a>
							</div>
							<div class='project-one__content-inner'>
								<div class='project-one__content'>
									<div class='project-one__sub-title-and-shape'>
										<span class='project-one__sub-title'>Veículos reparados</span>
										<div class='project-one__sub-title-bdr'></div>
									</div>
									<h3 class='project-one__title'><a href='project-details.html'>Óleo no local
											Mudança</a></h3>
								</div>
								<div class='project-one__video-link'>
									<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
										<div class='project-one__video-icon'>
											<span class='fas fa-play'></span>
											<i class='ripple'></i>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--Project One Single End-->
				<!--Project One Single Start-->
				<div class='swiper-slide'>
					<div class='project-one__single'>
						<div class='project-one__img-box'>
							<div class='project-one__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-1-3.jpg' alt=''>
							</div>
							<div class='project-one__arrow'>
								<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-1-3.jpg'
								   class='img-popup'><span
											class='icon-next'></span></a>
							</div>
							<div class='project-one__content-inner'>
								<div class='project-one__content'>
									<div class='project-one__sub-title-and-shape'>
										<span class='project-one__sub-title'>Veículos reparados</span>
										<div class='project-one__sub-title-bdr'></div>
									</div>
									<h3 class='project-one__title'><a href='project-details.html'>Completo
											Bateria
											mudar</a></h3>
								</div>
								<div class='project-one__video-link'>
									<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
										<div class='project-one__video-icon'>
											<span class='fas fa-play'></span>
											<i class='ripple'></i>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--Project One Single End-->
				<!--Project One Single Start-->
				<div class='swiper-slide'>
					<div class='project-one__single'>
						<div class='project-one__img-box'>
							<div class='project-one__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-1-4.jpg' alt=''>
							</div>
							<div class='project-one__arrow'>
								<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-1-4.jpg'
								   class='img-popup'><span
											class='icon-next'></span></a>
							</div>
							<div class='project-one__content-inner'>
								<div class='project-one__content'>
									<div class='project-one__sub-title-and-shape'>
										<span class='project-one__sub-title'>Veículos reparados</span>
										<div class='project-one__sub-title-bdr'></div>
									</div>
									<h3 class='project-one__title'><a href='project-details.html'>Reparação de freios
											Maestria</a></h3>
								</div>
								<div class='project-one__video-link'>
									<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
										<div class='project-one__video-icon'>
											<span class='fas fa-play'></span>
											<i class='ripple'></i>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--Project One Single End-->
				<!--Project One Single Start-->
				<div class='swiper-slide'>
					<div class='project-one__single'>
						<div class='project-one__img-box'>
							<div class='project-one__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-1-5.jpg' alt=''>
							</div>
							<div class='project-one__arrow'>
								<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-1-5.jpg'
								   class='img-popup'><span
											class='icon-next'></span></a>
							</div>
							<div class='project-one__content-inner'>
								<div class='project-one__content'>
									<div class='project-one__sub-title-and-shape'>
										<span class='project-one__sub-title'>Veículos reparados</span>
										<div class='project-one__sub-title-bdr'></div>
									</div>
									<h3 class='project-one__title'><a href='project-details.html'>Freio e pneu
											Correção</a></h3>
								</div>
								<div class='project-one__video-link'>
									<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
										<div class='project-one__video-icon'>
											<span class='fas fa-play'></span>
											<i class='ripple'></i>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--Project One Single End-->
				<!--Project One Single Start-->
				<div class='swiper-slide'>
					<div class='project-one__single'>
						<div class='project-one__img-box'>
							<div class='project-one__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/project/project-1-6.jpg' alt=''>
							</div>
							<div class='project-one__arrow'>
								<a href='<?= INCLUDE_PATH ?>/assets/images/project/project-1-6.jpg'
								   class='img-popup'><span
											class='icon-next'></span></a>
							</div>
							<div class='project-one__content-inner'>
								<div class='project-one__content'>
									<div class='project-one__sub-title-and-shape'>
										<span class='project-one__sub-title'>Veículos reparados</span>
										<div class='project-one__sub-title-bdr'></div>
									</div>
									<h3 class='project-one__title'><a href='project-details.html'>Ajuste do motor
											Excelência</a></h3>
								</div>
								<div class='project-one__video-link'>
									<a href='https://www.youtube.com/watch?v=Get7rqXYrbQ' class='video-popup'>
										<div class='project-one__video-icon'>
											<span class='fas fa-play'></span>
											<i class='ripple'></i>
										</div>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!--Project One Single End-->
			</div>
		</div>
	</div>
</section>
<!--Project One End -->

<!--Pricing Two Start -->
<section class='pricing-two'>
	<div class='container'>
		<div class='section-title text-center sec-title-animation animation-style1'>
			<div class='section-title__tagline-box'>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-1'>
						<i class='section-title__circle'></i>
					</div>
				</div>
				<h6 class='section-title__tagline'>Pacotes diferentes</h6>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-2'>
						<i class='section-title__circle'></i>
					</div>
				</div>
			</div>
			<h3 class='section-title__title title-animation'>Escolha o seu preço <span>Plano</span>
			</h3>
		</div>
		<div class='row'>
			<div class='col-xl-4 col-lg-4'>
				<div class='pricing-two__price-list-box'>
					<ul class='pricing-two__price-list'>
						<li>
							<div class='icon'>
								<span class='icon-diagnostic'></span>
							</div>
							<div class='content-inner'>
								<div class='content'>
									<p>Diagnóstico do motor</p>
								</div>
								<div class='pricing-two__price'>
									<span>$25</span>
								</div>
								<div class='pricing-two__price-shape-1'></div>
							</div>
						</li>
						<li>
							<div class='icon'>
								<span class='icon-tyre'></span>
							</div>
							<div class='content-inner'>
								<div class='content'>
									<p>Troca de pneus</p>
								</div>
								<div class='pricing-two__price'>
									<span>$55</span>
								</div>
								<div class='pricing-two__price-shape-1'></div>
							</div>
						</li>
						<li>
							<div class='icon'>
								<span class='icon-oil'></span>
							</div>
							<div class='content-inner'>
								<div class='content'>
									<p>Troca de óleo</p>
								</div>
								<div class='pricing-two__price'>
									<span>$45</span>
								</div>
								<div class='pricing-two__price-shape-2'></div>
							</div>
						</li>
						<li>
							<div class='icon'>
								<span class='icon-battery'></span>
							</div>
							<div class='content-inner'>
								<div class='content'>
									<p>Serviço de bateria</p>
								</div>
								<div class='pricing-two__price'>
									<span>$15</span>
								</div>
								<div class='pricing-two__price-shape-2'></div>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class='col-xl-4 col-lg-4'>
				<div class='pricing-two__img-box'>
					<div class='pricing-two__img'>
						<img src='<?= INCLUDE_PATH ?>/assets/images/resources/pricing-one-img-1.png' alt=''>
					</div>
					<div class='pricing-two__top-img-1'>
						<img src='<?= INCLUDE_PATH ?>/assets/images/resources/pricing-two-top-img-1.png' alt=''>
					</div>
				</div>
			</div>
			<div class='col-xl-4 col-lg-4'>
				<div class='pricing-two__price-list-box'>
					<ul class='pricing-two__price-list pricing-two__price-list-2'>
						<li>
							<div class='content-inner'>
								<div class='content'>
									<p>Suspensão e direção</p>
								</div>
								<div class='pricing-two__price'>
									<span>$35</span>
								</div>
								<div class='pricing-two__price-shape-3'></div>
							</div>
							<div class='icon'>
								<span class='icon-steering-wheel'></span>
							</div>
						</li>
						<li>
							<div class='content-inner'>
								<div class='content'>
									<p>Serviços de correia dentada</p>
								</div>
								<div class='pricing-two__price'>
									<span>$19</span>
								</div>
								<div class='pricing-two__price-shape-3'></div>
							</div>
							<div class='icon'>
								<span class='icon-car-parts'></span>
							</div>
						</li>
						<li>
							<div class='content-inner'>
								<div class='content'>
									<p>Reparo de freios</p>
								</div>
								<div class='pricing-two__price'>
									<span>$42</span>
								</div>
								<div class='pricing-two__price-shape-4'></div>
							</div>
							<div class='icon'>
								<span class='icon-brake'></span>
							</div>
						</li>
						<li>
							<div class='content-inner'>
								<div class='content'>
									<p>Serviço de iluminação</p>
								</div>
								<div class='pricing-two__price'>
									<span>$35</span>
								</div>
								<div class='pricing-two__price-shape-4'></div>
							</div>
							<div class='icon'>
								<span class='icon-low-beam'></span>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<!--Pricing Two End -->

<!--Testimonial Two Start -->
<section class='testimonial-two'>
	<div class='container'>
		<div class='testimonial-two__top'>
			<div class='section-title text-left sec-title-animation animation-style2'>
				<div class='section-title__tagline-box'>
					<div class='section-title__tagline-border'>
						<div class='section-title__shape-1'>
							<i class='section-title__circle'></i>
						</div>
					</div>
					<h6 class='section-title__tagline'>Depoimentos</h6>
					<div class='section-title__tagline-border'>
						<div class='section-title__shape-2'>
							<i class='section-title__circle'></i>
						</div>
					</div>
				</div>
				<h3 class='section-title__title title-animation'>Ouça nosso <span>Clientes</span>
				</h3>
			</div>
			<div class='testimonial-two__nav'>
				<div class='swiper-button-next1'>
					<i class='icon-back'></i>
				</div>
				<div class='swiper-button-prev1'>
					<i class='icon-next'></i>
				</div>
			</div>
		</div>
		<div class='swiper-container testimonial-two__carousel'>
			<div class='swiper-wrapper'>
				<!--Testimonial Two Single Start-->
				<div class='swiper-slide'>
					<div class='testimonial-two__single-inner'>
						<div class='testimonial-two__single'>
							<div class='testimonial-two__single-bg-shape'
							     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/shapes/testimonial-two-single-bg-shape.png);'>
							</div>
							<div class='testimonial-two__author-box'>
								<div class='testimonial-two__author-img'>
									<img src='<?= INCLUDE_PATH ?>/assets/images/testimonial/testimonial-2-1.jpg' alt=''>
								</div>
								<div class='testimonial-two__author-content'>
									<h3 class='testimonial-two__author-name'><a href='testimonials-v1.html'>Adão
											Smith</a></h3>
									<p class='testimonial-two__author-sub-title'>Estados Unidos</p>
								</div>
							</div>
							<p class='testimonial-two__author-text'>Uma empresa prestadora de serviços logísticos
								desempenha um papel
								papel fundamental no mundo
								cadeia de suprimentos Uma empresa prestadora de serviços logísticos.</p>
							<div class='testimonial-two__quote'>
								<span class='fas fa-quote-right'></span>
							</div>
						</div>
						<div class='testimonial-two__days'>
							<span>há 5 dias</span>
						</div>
						<div class='testimonial-two__ratting'>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
						</div>
					</div>
				</div>
				<!--Testimonial Two Single End-->
				<!--Testimonial Two Single Start-->
				<div class='swiper-slide'>
					<div class='testimonial-two__single-inner'>
						<div class='testimonial-two__single'>
							<div class='testimonial-two__single-bg-shape'
							     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/shapes/testimonial-two-single-bg-shape.png);'>
							</div>
							<div class='testimonial-two__author-box'>
								<div class='testimonial-two__author-img'>
									<img src='<?= INCLUDE_PATH ?>/assets/images/testimonial/testimonial-2-2.jpg' alt=''>
								</div>
								<div class='testimonial-two__author-content'>
									<h3 class='testimonial-two__author-name'><a href='testimonials-v1.html'>Jecika
											Marrom</a></h3>
									<p class='testimonial-two__author-sub-title'>Estados Unidos</p>
								</div>
							</div>
							<p class='testimonial-two__author-text'>Uma empresa prestadora de serviços logísticos
								desempenha um papel
								papel fundamental no mundo
								cadeia de suprimentos Uma empresa prestadora de serviços logísticos.</p>
							<div class='testimonial-two__quote'>
								<span class='fas fa-quote-right'></span>
							</div>
						</div>
						<div class='testimonial-two__days'>
							<span>há 7 dias</span>
						</div>
						<div class='testimonial-two__ratting'>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
						</div>
					</div>
				</div>
				<!--Testimonial Two Single End-->
				<!--Testimonial Two Single Start-->
				<div class='swiper-slide'>
					<div class='testimonial-two__single-inner'>
						<div class='testimonial-two__single'>
							<div class='testimonial-two__single-bg-shape'
							     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/shapes/testimonial-two-single-bg-shape.png);'>
							</div>
							<div class='testimonial-two__author-box'>
								<div class='testimonial-two__author-img'>
									<img src='<?= INCLUDE_PATH ?>/assets/images/testimonial/testimonial-2-3.jpg' alt=''>
								</div>
								<div class='testimonial-two__author-content'>
									<h3 class='testimonial-two__author-name'><a href='testimonials-v1.html'>Herberto
											Girar</a></h3>
									<p class='testimonial-two__author-sub-title'>Estados Unidos</p>
								</div>
							</div>
							<p class='testimonial-two__author-text'>Uma empresa prestadora de serviços logísticos
								desempenha um papel
								papel fundamental no mundo
								cadeia de suprimentos Uma empresa prestadora de serviços logísticos.</p>
							<div class='testimonial-two__quote'>
								<span class='fas fa-quote-right'></span>
							</div>
						</div>
						<div class='testimonial-two__days'>
							<span>há 9 dias</span>
						</div>
						<div class='testimonial-two__ratting'>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
							<span class='fas fa-star'></span>
						</div>
					</div>
				</div>
				<!--Testimonial Two Single End-->
			</div>
		</div>
	</div>
</section>
<!--Testimonial Two End -->

<!--Appointment Two Start -->
<section class='appointment-two'>
	<div class='appointment-two__bg-color'>
		<div class='appointment-two__bg'
		     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/appointment-two-bg.jpg);'></div>
	</div>
	<div class='container'>
		<div class='row'>
			<div class='col-xl-7'>
				<div class='appointment-two__left'>
					<div class='appointment-two__bg-shape'
					     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/shapes/appointment-two-bg-shape.png);'></div>
					<div class='section-title text-left sec-title-animation animation-style2'>
						<div class='section-title__tagline-box'>
							<div class='section-title__tagline-border'>
								<div class='section-title__shape-1'>
									<i class='section-title__circle'></i>
								</div>
							</div>
							<h6 class='section-title__tagline'>Agendar atendimento</h6>
							<div class='section-title__tagline-border'>
								<div class='section-title__shape-2'>
									<i class='section-title__circle'></i>
								</div>
							</div>
						</div>
						<h3 class='section-title__title title-animation'>Marque agora uma consulta
						</h3>
					</div>
					<form class='contact-form-validated appointment-two__form'
					      action='<?= INCLUDE_PATH ?>/assets/inc/sendemail.php'
					      method='post' novalidate='novalidate'>
						<div class='row'>
							<div class='col-xl-6 col-lg-6 col-md-6'>
								<div class='appointment-two__input-box'>
									<input type='text' name='name' placeholder='Nome completo' required=''>
								</div>
							</div>
							<div class='col-xl-6 col-lg-6 col-md-6'>
								<div class='appointment-two__input-box'>
									<input type='email' name='email' placeholder='Endereço de e-mail' required=''>
								</div>
							</div>
							<div class='col-xl-6 col-lg-6 col-md-6'>
								<div class='appointment-two__input-box'>
									<input type='text' name='Phone' placeholder='Número de telefone' required=''>
								</div>
							</div>
							<div class='col-xl-6 col-lg-6 col-md-6'>
								<div class='appointment-two__input-box'>
									<input type='text' placeholder='mm/dd/aaaa' name='date' id='datepicker'>
								</div>
							</div>
							<div class='col-xl-12'>
								<div class='appointment-two__input-box'>
									<div class='select-box'>
										<select class='selectmenu wide'>
											<option selected>Tipo de serviço</option>
											<option>Tipo de serviço 01</option>
											<option>Tipo de serviço 02</option>
											<option>Tipo de serviço 03</option>
											<option>Tipo de serviço 04</option>
											<option>Tipo de serviço 05</option>
										</select>
									</div>
								</div>
							</div>
							<div class='col-xl-12'>
								<div class='appointment-two__input-box text-message-box'>
									<textarea name='message' placeholder='Mensagem'></textarea>
								</div>
								<div class='appointment-two__btn-box'>
									<button type='submit' class='thm-btn'>Agendar agora<span class='icon-next'></span>
									</button>
								</div>
							</div>
						</div>
						<div class='result'></div>
					</form>
				</div>
			</div>
			<div class='col-xl-5'>
				<div class='appointment-two__right'>
					<div class='appointment-two__img-1'>
						<img src='<?= INCLUDE_PATH ?>/assets/images/resources/appointment-two-img-1.png' alt=''>
					</div>
					<div class='appointment-two__shape-1'>
						<img src='<?= INCLUDE_PATH ?>/assets/images/shapes/appointment-two-shape-1.png' alt=''>
					</div>
					<div class='appointment-two__contact-info'>
						<div class='appointment-two__contact-info-bg-shape'
						     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/shapes/appointment-two-contact-info-bg-shape.png);'>
						</div>
						<h3 class='appointment-two__contact-info-title'>Informações de contato</h3>
						<p class='appointment-two__contact-info-text'>É um facto há muito estabelecido que um
							leitor fique pelo layout distraído legível.</p>
						<ul class='appointment-two__contact-info-list'>
							<li>
								<div class='icon'>
									<span class='icon-pin'></span>
								</div>
								<div class='content'>
									<p>4517Washington. mgManchester,
										<br>Kentucky 39495</p>
								</div>
							</li>
							<li>
								<div class='icon'>
									<span class='icon-call'></span>
								</div>
								<div class='content'>
									<p><a href='tel:885747546027'>(88) 574 7546 027</a></p>
									<p><a href='tel:885747546027'>(88) 574 7546 027</a></p>
								</div>
							</li>
							<li>
								<div class='icon'>
									<span class='icon-mail'></span>
								</div>
								<div class='content'>
									<p><a href='mailto:servixa@gmail.com'>servixa@gmail.com</a></p>
									<p><a href='mailto:servixa@gmail.com'>servixa@gmail.com</a></p>
								</div>
							</li>
						</ul>
					</div>
					<div class='appointment-two__btn-box-two'>
						<a href='contact.html' class='thm-btn'>Contate-nos
							<span class='icon-next'></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!--Appointment Two End -->

<!--Blog Two Start -->
<section class='blog-two'>
	<div class='container'>
		<div class='section-title text-center sec-title-animation animation-style1'>
			<div class='section-title__tagline-box'>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-1'>
						<i class='section-title__circle'></i>
					</div>
				</div>
				<h6 class='section-title__tagline'>Blog e notícias</h6>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-2'>
						<i class='section-title__circle'></i>
					</div>
				</div>
			</div>
			<h3 class='section-title__title title-animation'>Últimas notícias diretamente
				<br>De <span>Nosso blog</span>
			</h3>
		</div>
		<div class='row'>
			<!--Blog Two Single Start-->
			<div class='col-xl-4 col-lg-6 col-md-6 wow fadeInLeft' data-wow-delay='100ms'>
				<div class='blog-two__single'>
					<div class='blog-two__single-inner'>
						<div class='blog-two__img-box'>
							<div class='blog-two__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/blog/blog-2-1.jpg' alt=''>
								<div class='blog-two__plus'>
									<a href='blog-details.html'><i class='fas fa-plus'></i></a>
								</div>
								<div class='blog-two__tag'>
									<a href='blog-details.html'>Reparo automotivo</a>
								</div>
							</div>
							<div class='blog-two__date'>
								<p>25 <span>Mar</span></p>
							</div>
						</div>
						<div class='blog-two__content'>
							<ul class='blog-two__meta list-unstyled'>
								<li>
									<a href='blog-details.html'>
										<span class='fas fa-calendar-alt'></span>10 de maio de 2025</a>
								</li>
								<li>
									<a href='blog-details.html'>
										<span class='fas fa-comments'></span>Comentário
									</a>
								</li>
							</ul>
							<h3 class='blog-two__title'><a href='blog-details.html'>Conselhos de especialistas para
									manter seu
									Passeio
									Suave</a></h3>
						</div>
					</div>
					<div class='blog-two__author-info'>
						<div class='blog-two__author-img-box'>
							<div class='blog-two__author-img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/blog/blog-two-author-img-1.jpg' alt=''>
							</div>
						</div>
						<div class='blog-two__author-content'>
							<h5>Jane Cooper</h5>
							<p>10 de maio de 2025</p>
						</div>
					</div>
					<div class='blog-two__read-more'>
						<a href='blog-details.html'><span class='icon-next'></span>Leia mais</a>
					</div>
				</div>
			</div>
			<!--Blog Two Single End-->
			<!--Blog Two Single Start-->
			<div class='col-xl-4 col-lg-6 col-md-6 wow fadeInUp' data-wow-delay='200ms'>
				<div class='blog-two__single'>
					<div class='blog-two__single-inner'>
						<div class='blog-two__img-box'>
							<div class='blog-two__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/blog/blog-2-2.jpg' alt=''>
								<div class='blog-two__plus'>
									<a href='blog-details.html'><i class='fas fa-plus'></i></a>
								</div>
								<div class='blog-two__tag'>
									<a href='blog-details.html'>Reparo automotivo</a>
								</div>
							</div>
							<div class='blog-two__date'>
								<p>18 <span>Ago</span></p>
							</div>
						</div>
						<div class='blog-two__content'>
							<ul class='blog-two__meta list-unstyled'>
								<li>
									<a href='blog-details.html'>
										<span class='fas fa-user'></span>Administrador
									</a>
								</li>
								<li>
									<a href='blog-details.html'>
										<span class='fas fa-comments'></span>Comentário
									</a>
								</li>
							</ul>
							<h3 class='blog-two__title'><a href='blog-details.html'>Sua manutenção automática semanal
									Guia</a></h3>
						</div>
					</div>
					<div class='blog-two__author-info'>
						<div class='blog-two__author-img-box'>
							<div class='blog-two__author-img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/blog/blog-two-author-img-2.jpg' alt=''>
							</div>
						</div>
						<div class='blog-two__author-content'>
							<h5>David Cooper</h5>
							<p>10 de maio de 2025</p>
						</div>
					</div>
					<div class='blog-two__read-more'>
						<a href='blog-details.html'><span class='icon-next'></span>Leia mais</a>
					</div>
				</div>
			</div>
			<!--Blog Two Single End-->
			<!--Blog Two Single Start-->
			<div class='col-xl-4 col-lg-6 col-md-6 wow fadeInRight' data-wow-delay='300ms'>
				<div class='blog-two__single'>
					<div class='blog-two__single-inner'>
						<div class='blog-two__img-box'>
							<div class='blog-two__img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/blog/blog-2-3.jpg' alt=''>
								<div class='blog-two__plus'>
									<a href='blog-details.html'><i class='fas fa-plus'></i></a>
								</div>
								<div class='blog-two__tag'>
									<a href='blog-details.html'>Reparo automotivo</a>
								</div>
							</div>
							<div class='blog-two__date'>
								<p>16 <span>Fev</span></p>
							</div>
						</div>
						<div class='blog-two__content'>
							<ul class='blog-two__meta list-unstyled'>
								<li>
									<a href='blog-details.html'>
										<span class='fas fa-user'></span>Administrador
									</a>
								</li>
								<li>
									<a href='blog-details.html'>
										<span class='fas fa-comments'></span>Comentário
									</a>
								</li>
							</ul>
							<h3 class='blog-two__title'><a href='blog-details.html'>Últimas tendências em conserto de
									automóveis
									e serviço</a></h3>
						</div>
					</div>
					<div class='blog-two__author-info'>
						<div class='blog-two__author-img-box'>
							<div class='blog-two__author-img'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/blog/blog-two-author-img-3.jpg' alt=''>
							</div>
						</div>
						<div class='blog-two__author-content'>
							<h5>Jecika Brown</h5>
							<p>10 de junho de 2025</p>
						</div>
					</div>
					<div class='blog-two__read-more'>
						<a href='blog-details.html'><span class='icon-next'></span>Leia mais</a>
					</div>
				</div>
			</div>
			<!--Blog Two Single End-->
		</div>
	</div>
</section>
<!--Blog Two End -->
