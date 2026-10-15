<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    /**
     * Faixa deslizante de serviços (home) — tema Zevitor.
     * Dinâmico: lê DB_SERVICES (svc_status = 1). Renderiza só se houver registros.
     * Ícone: svc_icon (fallback p/ icon-mechanical). Link: BASE/servico/<svc_name>.
     */
    $Read ??= new Read();

    $Read->fullRead(
        'SELECT svc_name, svc_title, svc_icon FROM ' . DB_SERVICES . ' WHERE svc_status = :st ORDER BY svc_id ASC',
        'st=1'
    );

    if ($Read->getResult()):
?>
<!--Sliding Text Start-->
<section class='sliding-text'>
	<div class='sliding-text__inner'>
		<div class='sliding-text__bg'
		     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/sliding-text-bg.jpg);'></div>
		<ul class='sliding-text__list marquee_mode-2 list-unstyled'>
            <?php foreach ($Read->getResult() as $Service): extract($Service);
                $Icon = !empty($svc_icon) ? $svc_icon : 'icon-mechanical';
                $Link = BASE . '/servico/' . $svc_name;
            ?>
			<li>
				<div class='icon'>
					<span class='<?= Check::safeHtmlChars($Icon) ?>'></span>
				</div>
				<p><a href='<?= $Link ?>'><?= Check::safeHtmlChars($svc_title) ?></a></p>
			</li>
            <?php endforeach; ?>
		</ul>
	</div>
</section>
<!--Sliding Text End-->
<?php endif; ?>
