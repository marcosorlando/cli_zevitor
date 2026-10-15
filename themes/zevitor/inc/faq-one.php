<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    /**
     * FAQ (home) — tema Zevitor.
     * Dinâmico: 5 perguntas aleatórias de DB_FAQS (faq_status = 1) a cada carregamento.
     * Renderiza só se houver registros. faq_answer vem do editor do painel (HTML).
     */
    $Read ??= new Read();

    $Read->fullRead(
        'SELECT faq_question, faq_answer FROM ' . DB_FAQS . ' WHERE faq_status = :st ORDER BY RAND() LIMIT :limit',
        'st=1&limit=5'
    );
    $Faqs = $Read->getResult() ?: [];

    if ($Faqs):
?>
<!--Faq One Start -->
<section class='faq-one'>
	<div class='faq-one__bg-shape'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/shapes/faq-one-bg-shape-1.png);'>
	</div>
	<div class='faq-one__bg-shape'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/shapes/faq-one-bg-shape-2.png);'>
	</div>
	<div class='container'>
		<div class='row'>
			<div class='col-xl-6'>
				<div class='faq-one__left'>
					<div class='section-title text-left sec-title-animation animation-style2'>
						<div class='section-title__tagline-box'>
							<div class='section-title__tagline-border'>
								<div class='section-title__shape-1'>
									<i class='section-title__circle'></i>
								</div>
							</div>
							<h6 class='section-title__tagline'>Perguntas comuns</h6>
							<div class='section-title__tagline-border'>
								<div class='section-title__shape-2'>
									<i class='section-title__circle'></i>
								</div>
							</div>
						</div>
						<h3 class='section-title__title title-animation'>Encontre respostas para o mais comum
							<span>Consultas de clientes</span>
						</h3>
					</div>
					<div class='accrodion-grp' data-grp-name='faq-one-accrodion'>
                        <?php foreach ($Faqs as $Key => $Faq): ?>
						<div class='accrodion<?= 0 === $Key ? ' active' : '' ?> wow <?= $Key % 2 ? 'fadeInRight' : 'fadeInLeft' ?>'
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
                        <?php endforeach; ?>
					</div>
				</div>
			</div>
			<div class='col-xl-6'>
				<div class='faq-one__right'>
					<div class='faq-one__img-box'>
						<div class='faq-one__img wow fadeInDown' data-wow-delay='100ms'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/resources/faq-one-img-1.png' alt=''
							     class='float-bob-y'>
						</div>
						<div class='faq-one__img-2'>
							<img src='<?= INCLUDE_PATH ?>/assets/images/resources/faq-one-img-2.png' alt=''>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!--Faq One End -->
<?php endif; ?>
