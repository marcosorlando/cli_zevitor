<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    /**
     * Main Slider / Banner (home) — tema Zevitor.
     * Dinâmico: lê DB_SLIDES (ws_slides) com status ativo e dentro da janela slide_start/slide_end.
     * Visual: bloco .main-slider do template Servixa (swiper com background + imagem em destaque).
     * Imagens: BASE/tim.php?src=uploads/<slide_background|slide_image|mobile_image>.
     * slide_google_review controla a exibição do bloco de avaliação do Google por slide.
     */
    $Read ??= new Read();

    $Read->fullRead(
        "SELECT slide_background, slide_image, mobile_image, slide_title, slide_subtitle, slide_paragraph, slide_btn_text, slide_btn_url, slide_google_review FROM " . DB_SLIDES . " WHERE slide_status = :status AND slide_start <= NOW() AND (slide_end >= NOW() OR slide_end IS NULL) ORDER BY slide_start ASC",
        'status=1'
    );

    $Slides = $Read->getResult();
?>
<!--Main Slider Start-->
<section class='main-slider'>
	<div class='swiper-container thm-swiper__slider' data-swiper-options='{"slidesPerView": 1, "loop": true,
                "effect": "fade",
                "pagination": {
                "el": "#main-slider-pagination",
                "type": "bullets",
                "clickable": true
                },
                "navigation": {
                "nextEl": "#main-slider__swiper-button-next",
                "prevEl": "#main-slider__swiper-button-prev"
                },
                "autoplay": {
                    "delay": 8000
                }
            }'>
		<div class='swiper-wrapper'>
            <?php
                if (!$Slides) {
                    ?>
					<div class='swiper-slide'>
						<div class='main-slider__bg'
						     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/slider-1-1.jpg);'></div>
						<div class='main-slider__img'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/resources/main-slider-img-1-1.png' alt=''>
						</div>
						<div class='main-slider__shape-1'></div>
						<div class='main-slider__shape-2'></div>
						<div class='main-slider__shape-3'></div>
						<div class='main-slider__shape-4'></div>
						<div class='main-slider__shape-5'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/shapes/main-slider-shape-5.png' alt=''>
						</div>
						<div class='main-slider__shape-6'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/shapes/main-slider-shape-6.png' alt=''
							     class='rotate-me'>
						</div>
						<div class='container'>
							<div class='row'>
								<div class='col-xl-12'>
									<div class='main-slider__content'>
										<h4 class='main-slider__sub-title'>Desde 1973 · Caxias do Sul – RS</h4>
										<h2 class='main-slider__title'>Mecânica de confiança para o seu carro nacional
											ou importado</h2>
										<p class='main-slider__text'>Da revisão completa ao diagnóstico eletrônico. Mais
											de 50 anos cuidando do seu carro com transparência.
										</p>
										<div class='main-slider__btn-and-review-box'>
											<div class='main-slider__btn-box'>
												<a href='<?= BASE ?>/sobre' class='thm-btn'>Descubra mais
													<span><i class='icon-next'></i></span>
												</a>
											</div>
											<div class='main-slider__review-box'>
												<ul class='clearfix'>
													<li>
														<div class='img-box'><img
																	src='<?= INCLUDE_PATH ?>/assets/images/resources/main-slider-review-1-1.jpg'
																	alt='#'>
														</div>
													</li>
													<li>
														<div class='img-box'><img
																	src='<?= INCLUDE_PATH ?>/assets/images/resources/main-slider-review-1-2.jpg'
																	alt='#'>
														</div>
													</li>
													<li>
														<div class='img-box'><img
																	src='<?= INCLUDE_PATH ?>/assets/images/resources/main-slider-review-1-3.jpg'
																	alt='#'>
														</div>
													</li>
												</ul>
												<div class='text-box'>
													<h2>Clientes satisfeitos</h2>
													<p>4,8⭐️ · +220 avaliações no Google</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                    <?php
                } else {
                    foreach ($Slides as $SlideIndex => $Slide) {
                        extract($Slide);

                        $Background = !empty($slide_background)
                            ? BASE . '/tim.php?src=uploads/' . $slide_background . '&w=1920&h=930'
                            : INCLUDE_PATH . '/assets/images/backgrounds/slider-1-1.jpg';
                        $Foreground = !empty($slide_image)
                            ? BASE . '/tim.php?src=uploads/' . $slide_image . '&w=950&h=650'
                            : INCLUDE_PATH . '/assets/images/resources/main-slider-img-1-1.png';
                        $MobileBg = !empty($mobile_image)
                            ? BASE . '/tim.php?src=uploads/' . $mobile_image . '&w=640&h=900'
                            : null;
                        $BtnText = !empty($slide_btn_text) ? $slide_btn_text : 'Descubra mais';
                        $BtnUrl = !empty($slide_btn_url) ? $slide_btn_url : BASE . '/sobre';
                        $SlideUid = 'main-slider-bg-' . $SlideIndex;
                        ?>
						<div class='swiper-slide'>
                            <?php
                                if ($MobileBg): ?>
									<style>@media (max-width: 767px) {
                                            #<?= $SlideUid ?> {
                                            background-image: url(<?= $MobileBg ?>);
                                        }
                                        }</style>
                                <?php
                                endif; ?>
							<div id='<?= $SlideUid ?>' class='main-slider__bg'
							     style='background-image: url(<?= $Background ?>);'></div>
							<div class='main-slider__img'>
								<img src='<?= $Foreground ?>' alt='<?= Check::safeHtmlChars((string)$slide_title) ?>'>
							</div>
							<div class='main-slider__shape-1'></div>
							<div class='main-slider__shape-2'></div>
							<div class='main-slider__shape-3'></div>
							<div class='main-slider__shape-4'></div>
							<div class='main-slider__shape-5'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/shapes/main-slider-shape-5.png' alt=''>
							</div>
							<div class='main-slider__shape-6'>
								<img src='<?= INCLUDE_PATH ?>/assets/images/shapes/main-slider-shape-6.png' alt=''
								     class='rotate-me'>
							</div>
							<div class='container'>
								<div class='row'>
									<div class='col-xl-12'>
										<div class='main-slider__content'>
                                            <?php
                                                if (!empty($slide_subtitle)): ?>
													<h4 class='main-slider__sub-title'><?= Check::safeHtmlChars(
                                                            $slide_subtitle
                                                        ) ?></h4>
                                                <?php
                                                endif; ?>
                                            <?php
                                                if (!empty($slide_title)): ?>
													<h2 class='main-slider__title'><?= Check::safeHtmlChars(
                                                            $slide_title
                                                        ) ?></h2>
                                                <?php
                                                endif; ?>
                                            <?php
                                                if (!empty($slide_paragraph)): ?>
													<p class='main-slider__text'><?= Check::safeHtmlChars(
                                                            $slide_paragraph
                                                        ) ?></p>
                                                <?php
                                                endif; ?>
											<div class='main-slider__btn-and-review-box'>
												<div class='main-slider__btn-box'>
													<a href='<?= Check::safeHtmlChars($BtnUrl) ?>'
													   class='thm-btn'><?= Check::safeHtmlChars($BtnText) ?>
														<span><i class='icon-next'></i></span>
													</a>
												</div>
                                                <?php
                                                    if ((int)($slide_google_review ?? 0) === 1): ?>
														<div class='main-slider__review-box'>
															<ul class='clearfix'>
																<li>
																	<div class='img-box'><img
																				src='<?= INCLUDE_PATH ?>/assets/images/resources/main-slider-review-1-1.jpg'
																				alt='#'>
																	</div>
																</li>
																<li>
																	<div class='img-box'><img
																				src='<?= INCLUDE_PATH ?>/assets/images/resources/main-slider-review-1-2.jpg'
																				alt='#'>
																	</div>
																</li>
																<li>
																	<div class='img-box'><img
																				src='<?= INCLUDE_PATH ?>/assets/images/resources/main-slider-review-1-3.jpg'
																				alt='#'>
																	</div>
																</li>
															</ul>
															<div class='text-box'>
																<h2>Clientes satisfeitos</h2>
																<p>4,8⭐️ · +220 avaliações no Google</p>
															</div>
														</div>
                                                    <?php
                                                    endif; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
                        <?php
                    }
                }
            ?>
		</div>

		<div class='swiper-pagination' id='main-slider-pagination'></div>
		<!-- If we need navigation buttons -->

	</div>
</section>
<!--Main Slider End-->
