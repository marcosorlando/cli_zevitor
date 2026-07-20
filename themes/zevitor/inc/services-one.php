<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    /**
     * Serviços (home) — tema Zevitor.
     * Dinâmico: lê DB_SERVICES (svc_status = 1) com a categoria pai.
     * Visual: bloco .services-one do template Servixa.
     * Encanamento: padrão do WC ($Read->fullRead + foreach + extract).
     * Capa: BASE/tim.php?src=uploads/<svc_cover> (fallback p/ imagem do template).
     */
    $Read ??= new Read();

    $Read->fullRead(
        'SELECT svc_name, svc_title, svc_subtitle, svc_description, svc_icon, svc_cover '
        . 'FROM ' . DB_SERVICES . ' WHERE svc_status = :st ORDER BY svc_id ASC LIMIT :limit',
        'st=1&limit=12'
    );
?>
<!--Services One Start -->
<section class='services-one'>
	<div class='container'>
		<div class='section-title text-center sec-title-animation animation-style2'>
			<div class='section-title__tagline-box'>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-1'>
						<i class='section-title__circle'></i>
					</div>
				</div>
				<h6 class='section-title__tagline'>Nossos serviços</h6>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-2'>
						<i class='section-title__circle'></i>
					</div>
				</div>
			</div>
			<h3 class='section-title__title title-animation'>Serviços de confiança para <span>cada carro</span>
			</h3>
		</div>
		<div class='swiper-container services-one__carousel'>
			<div class='swiper-wrapper'>
                <?php
                    if (!$Read->getResult()) {
                        echo Check::erro(
                            'Os serviços ainda serão cadastrados no painel. Volte em breve :)',
                            E_USER_NOTICE
                        );
                    } else {
                        $Count = 0;
                        foreach ($Read->getResult() as $Service) {
                            extract($Service);
                            $Count++;
                            $Num = str_pad((string) $Count, 2, '0', STR_PAD_LEFT);
                            $Cover = !empty($svc_cover)
                                ? BASE . '/tim.php?src=uploads/' . $svc_cover . '&w=600&h=450'
                                : INCLUDE_PATH . '/assets/images/services/services-1-1.jpg';
                            $Icon = !empty($svc_icon) ? $svc_icon : 'icon-mechanical';
                            $Link = BASE . '/servico/' . $svc_name;
                            $Text = !empty($svc_subtitle) ? $svc_subtitle : mb_strimwidth(
                                strip_tags((string) $svc_description),
                                0,
                                120,
                                '…'
                            );
                ?>
				<!--Services One Single Start -->
				<div class='swiper-slide'>
					<div class='services-one__single'>
						<div class='services-one__img-box'>
							<div class='services-one__img'>
								<img src='<?= $Cover ?>' alt='<?= Check::safeHtmlChars($svc_title) ?>'>
							</div>
							<div class='services-one__count'><?= $Num ?></div>
						</div>
						<div class='services-one__content'>
							<div class='services-one__icon'>
								<span class='<?= $Icon ?>'></span>
							</div>
							<h3 class='services-one__title'>
								<a href='<?= $Link ?>'><?= Check::safeHtmlChars($svc_title) ?></a>
							</h3>
							<p class='services-one__text'><?= Check::safeHtmlChars($Text) ?></p>
							<div class='services-one__btn-box'>
								<a href='<?= $Link ?>'>Leia mais<span class='icon-next'></span></a>
							</div>
						</div>
					</div>
				</div>
				<!--Services One Single End -->
                <?php
                        }
                    }
                ?>
			</div>
		</div>
	</div>
</section>
<!--Services One End -->
