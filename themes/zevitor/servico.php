<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    /**
     * Serviço (single) — tema Zevitor. Rota: /servico/{svc_name}
     * Dinâmico: DB_SERVICES + DB_SERVICES_GALLERY (carrossel do topo) + DB_FAQS (mesma categoria).
     * Visual: service-details.html do template Servixa.
     */
    $Read ??= new Read();

    if (empty($URL[1])) {
        require REQUIRE_PATH . '/404.php';

        return;
    }

    $Read->exeRead(DB_SERVICES, 'WHERE svc_name = :nm AND svc_status = 1', "nm={$URL[1]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';

        return;
    }
    extract($Read->getResult()[0]);

    $SvcTitle = Check::safeHtmlChars($svc_title);

    // CATEGORIA
    $SvcCategory = null;
    if (!empty($svc_category)) {
        $Read->fullRead(
            'SELECT category_title FROM ' . DB_SERVICES_CATEGORIES . ' WHERE category_id = :id',
            "id={$svc_category}"
        );
        $SvcCategory = $Read->getResult() ? $Read->getResult()[0]['category_title'] : null;
    }

    // GALERIA (carrossel do topo) — fallback: capa do serviço > imagem do template
    $SvcImages = [];
    $Read->fullRead('SELECT image FROM ' . DB_SERVICES_GALLERY . ' WHERE svc_id = :id ORDER BY id ASC', "id={$svc_id}");
    foreach ($Read->getResult() ?: [] as $Gallery) {
        if (!empty($Gallery['image']) && file_exists('uploads/' . $Gallery['image'])) {
            $SvcImages[] = BASE . '/tim.php?src=uploads/' . $Gallery['image'] . '&w=850&h=400';
        }
    }
    if (!$SvcImages) {
        $SvcImages[] = !empty($svc_cover) && file_exists('uploads/' . $svc_cover)
            ? BASE . '/tim.php?src=uploads/' . $svc_cover . '&w=850&h=400'
            : INCLUDE_PATH . '/assets/images/services/service-details-img-3.jpg';
    }

    // SERVIÇOS (sidebar)
    $Read->fullRead(
        'SELECT svc_name, svc_title FROM ' . DB_SERVICES . ' WHERE svc_status = :st ORDER BY svc_id ASC',
        'st=1'
    );
    $SvcList = $Read->getResult() ?: [];

    // FAQ's da mesma categoria do serviço
    $SvcFaqs = [];
    if (!empty($svc_category)) {
        $Read->fullRead(
            'SELECT faq_question, faq_answer FROM ' . DB_FAQS . ' WHERE faq_status = :st AND faq_category = :cat '
            . 'ORDER BY faq_order IS NULL, faq_order ASC, faq_question ASC',
            "st=1&cat={$svc_category}"
        );
        $SvcFaqs = $Read->getResult() ?: [];
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
			<h3><?= $SvcTitle ?></h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='<?= BASE ?>' title='<?= SITE_NAME ?>'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li><a href='<?= BASE ?>/servicos' title='Serviços'>Serviços</a></li>
                    <?php
                        if ($SvcCategory && $SvcCategory !== $svc_title) { ?>
							<li><span class='fas fa-angle-right'></span></li>
							<li><?= Check::safeHtmlChars($SvcCategory) ?></li>
                            <?php
                        } ?>
					<li><span class='fas fa-angle-right'></span></li>
					<li><?= $SvcTitle ?></li>
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
                    <?php
                        if ($SvcList) { ?>
							<div class='service-details__services-box'>
								<h3 class='service-details__services-title'>Nossos serviços</h3>
								<ul class='service-details__services-list list-unstyled'>
                                    <?php
                                        foreach ($SvcList as $Service) { ?>
											<li<?= $Service['svc_name'] === $svc_name ? " class='active'" : '' ?>>
												<a href='<?= BASE ?>/servico/<?= $Service['svc_name'] ?>'
												   title='<?= Check::safeHtmlChars($Service['svc_title']) ?>'><?=
                                                        Check::safeHtmlChars($Service['svc_title'])
                                                    ?><span class='icon-next'></span></a>
											</li>
                                            <?php
                                        } ?>
								</ul>
							</div>
                            <?php
                        } ?>
					<div class='service-details__sidebar-contact'>
						<div class='service-details__sidebar-contact-img'>
							<div class='inner'>
								<img width='285' height='285' loading='lazy'
								     src='<?= BASE ?>/tim.php?src=<?= REQUIRE_PATH ?>/assets/images/services/service-details-sidebar-img.webp&w=285&h=285'
								     alt='Se precisar de ajuda entre em contato'>
							</div>
						</div>

						<div class='service-details__sidebar-contact-content'>
							<div class='icon'>
								<span class='icon-phone-call'></span>
							</div>
							<h2><a href='<?= Check::whatsMessage(
                                    SITE_ADDR_WHATS,
                                    "Olá, estou no site e quero agendar o serviço: {$svc_title}."
                                )
                                ?>' target="_blank" title="Enviar mensagem no WhatsApp"><?=
                                        SITE_ADDR_WHATS
                                    ?></a></h2>
							<p>Se precisar de ajuda<br>
								Entre em contato conosco</p>
						</div>
					</div>

				</div>
			</div>
			<div class='col-xl-8 col-lg-7'>
				<div class='service-details__left'>
                    <?php
                        if (count($SvcImages) > 1) { ?>
							<div class='service-details__img swiper-container thm-swiper__slider'
							     style='--swiper-theme-color: var(--servixa-base);'
							     data-swiper-options='{"slidesPerView": 1, "loop": true, "speed": 800,
						     "autoplay": {"delay": 5000, "disableOnInteraction": false},
						     "pagination": {"el": ".service-details__img-pagination", "clickable": true}}'>
								<div class='swiper-wrapper'>
                                    <?php
                                        foreach ($SvcImages as $Key => $Image) { ?>
											<div class='swiper-slide'>
												<img src='<?= $Image ?>' alt='<?= $SvcTitle ?> - Imagem <?= $Key + 1 ?>'
												     title='<?= $SvcTitle ?>'>
											</div>
                                            <?php
                                        } ?>
								</div>
								<div class='swiper-pagination service-details__img-pagination'></div>
							</div>
                            <?php
                        } else { ?>
							<div class='service-details__img'>
								<img src='<?= $SvcImages[0] ?>' alt='<?= $SvcTitle ?>' title='<?= $SvcTitle ?>'>
							</div>
                            <?php
                        } ?>

					<h1 class='service-details__title-1'><?= $SvcTitle ?></h1>
                    <?php
                        if (!empty($svc_subtitle)) { ?>
							<p class='service-details__text-1'><?= Check::safeHtmlChars($svc_subtitle) ?></p>
                            <?php
                        } ?>
                    <?php
                        if (!empty($svc_description)) { ?>
							<div class='service-details__text-2'><?= $svc_description ?></div>
                            <?php
                        } ?>

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
											<p class='service-details__img-box-content-title'>Diagnóstico completo
											</p>
										</div>
										<p class='service-details__img-box-content-text'>Diagnóstico honesto,
											peças de qualidade e mão de obra especializada. Você sabe o que o carro
											precisa antes de qualquer serviço.</p>
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
											<p class='service-details__img-box-content-title'>100% de
												transparencia</p>
										</div>
										<p class='service-details__img-box-content-text'>Atendimento transparente
											do orçamento à entrega, com garantia no serviço e o carro devolvido
											do jeito certo.</p>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <?php
                        if ($SvcFaqs) { ?>
							<div class='faq-page__single'>
								<div class='faq-one__left'>
									<div class='accrodion-grp' data-grp-name='faq-one-accrodion'>
                                        <?php
                                            foreach ($SvcFaqs as $Key => $Faq) { ?>
												<div class='accrodion<?= 0 === $Key ? ' active' : '' ?> wow <?=
                                                    0 === $Key % 2 ? 'fadeInLeft' : 'fadeInRight' ?>'
												     data-wow-delay='<?= $Key * 100 ?>ms' data-wow-duration='1500ms'>
													<div class='accrodion-title'>
														<h4><?= Check::safeHtmlChars($Faq['faq_question']) ?></h4>
													</div>
													<div class='accrodion-content'>
														<div class='inner'>
                                                            <?= $Faq['faq_answer'] ?>
														</div><!-- /.inner -->
													</div>
												</div>
                                                <?php
                                            } ?>
									</div>
								</div>
							</div>
                            <?php
                        } ?>
				</div>
			</div>
		</div>
	</div>
</section>
<!--Service Details End-->
