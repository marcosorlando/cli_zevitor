<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    /**
     * Depoimentos (home) — tema Zevitor.
     * Dinâmico: lê DB_DEPOSITIONS (ws_depositions) ordenado por depositions_order.
     * Visual: bloco .testimonial-one do template Servixa.
     * Foto: BASE/tim.php?src=uploads/<depositions_image> (fallback p/ imagem do template).
     */
    $Read ??= new Read();

    $Read->fullRead(
        'SELECT depositions_name, depositions_profession, depositions_text, depositions_image '
        . 'FROM ' . DB_DEPOSITIONS . ' ORDER BY depositions_order ASC, depositions_id ASC LIMIT :limit',
        'limit=12'
    );
?>
<!--Testimonial One Start -->
<section class='testimonial-one'>
	<div class='testimonial-one__wrap'>
		<div class='container'>
			<div class='section-title text-center sec-title-animation animation-style1'>
				<div class='section-title__tagline-box'>
					<div class='section-title__tagline-border'>
						<div class='section-title__shape-1'>
							<i class='section-title__circle'></i>
						</div>
					</div>
					<h6 class='section-title__tagline'>Aqueles que confiaram em nós</h6>
					<div class='section-title__tagline-border'>
						<div class='section-title__shape-2'>
							<i class='section-title__circle'></i>
						</div>
					</div>
				</div>
				<h3 class='section-title__title title-animation'>O que os nossos<br> clientes têm <span>a dizer</span>
				</h3>
			</div>
			<div class='swiper-container testimonial-one__carousel'>
				<div class='swiper-wrapper'>
                    <?php
                        if (!$Read->getResult()) {
                            echo Check::erro(
                                'Os depoimentos ainda serão cadastrados no painel. Volte em breve :)',
                                E_USER_NOTICE
                            );
                        } else {
                            foreach ($Read->getResult() as $Depo) {
                                extract($Depo);
                                $Foto = !empty($depositions_image)
                                    ? BASE . '/tim.php?src=uploads/depositions/' . $depositions_image . '&w=120&h=120'
                                    : INCLUDE_PATH . '/assets/images/testimonial/testimonial-1-1.jpg';
                    ?>
					<!--Testimonial One Single Start-->
					<div class='swiper-slide'>
						<div class='testimonial-one__single-inner'>
							<div class='testimonial-one__single'>
								<div class='testimonial-one__client-info'>
									<div class='testimonial-one__client-content'>
										<h4 class='testimonial-one__client-name'><?= Check::safeHtmlChars($depositions_name) ?></h4>
										<p class='testimonial-one__client-sub-title'><?= Check::safeHtmlChars((string) $depositions_profession) ?></p>
									</div>
								</div>
								<p class='testimonial-one__text'><?= Check::safeHtmlChars($depositions_text) ?></p>
							</div>
							<div class='testimonial-one__img'>
								<img src='<?= $Foto ?>' alt='<?= Check::safeHtmlChars($depositions_name) ?>'>
								<div class='testimonial-one__rating'>
									<span class='fas fa-star'></span>
									<span class='fas fa-star'></span>
									<span class='fas fa-star'></span>
									<span class='fas fa-star'></span>
									<span class='fas fa-star'></span>
								</div>
							</div>
							<div class='testimonial-one__quote'>
								<span class='icon-left'></span>
							</div>
						</div>
					</div>
					<!--Testimonial One Single End-->
                    <?php
                            }
                        }
                    ?>
				</div>
			</div>
		</div>
	</div>
	<div class='testimonial-one__nav'>
		<div class='swiper-button-next1'>
			<i class='icon-back'></i>
		</div>
		<div class='swiper-button-prev1'>
			<i class='icon-next'></i>
		</div>
	</div>
</section>
<!--Testimonial One End -->
