<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    /**
     * Marcas/Parceiros (home) — tema Zevitor.
     * Dinâmico: lê DB_PARTNERS (ws_partners) ordenado por partner_order.
     * Visual: bloco .brand-one do template Servixa.
     * Logo: BASE/tim.php?src=uploads/<partner_image>. Renderiza só se houver registros.
     */
    $Read ??= new Read();

    $Read->fullRead(
        'SELECT partner_name, partner_image, partner_page '
        . 'FROM ' . DB_PARTNERS . ' ORDER BY partner_id ASC LIMIT :limit',
        'limit=20'
    );

    if ($Read->getResult()):
?>
<!--Start Brand One-->
<section class='brand-one'>
	<div class='container'>
		<div class='brand-one__inner'>
			<div class='swiper-container brand-one__carousel'>
				<div class='swiper-wrapper'>
                    <?php foreach ($Read->getResult() as $Partner): extract($Partner);
                        $Logo = BASE . '/tim.php?src=uploads/partners/' . $partner_image . '&w=200&h=120';
                        $Link = !empty($partner_page) ? $partner_page : '#';
                    ?>
					<!--Start Brand One Single-->
					<div class='swiper-slide'>
						<div class='brand-one__single'>
							<div class='brand-one__single-inner'>
								<a href='<?= $Link ?>'><img src='<?= $Logo ?>'
								                            alt='<?= Check::safeHtmlChars((string) $partner_name) ?>'></a>
							</div>
						</div>
					</div>
					<!--End Brand One Single-->
                    <?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
<!--End Brand One-->
<?php endif; ?>
