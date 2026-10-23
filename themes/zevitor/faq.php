<?php

use App\Conn\Read;
use App\Helpers\Check;

$Read ??= new Read();
$Read->exeRead(DB_PAGES, 'WHERE page_name = :name AND page_status = 1', 'name=faq');
if (!$Read->getResult()) {
    require REQUIRE_PATH . '/404.php';

    return;
}

$Read->fullRead(
    'SELECT f.faq_question, f.faq_answer, f.faq_category, c.category_title FROM ' . DB_FAQS . ' f '
    . 'LEFT JOIN ' . DB_SERVICES_CATEGORIES . ' c ON c.category_id = f.faq_category '
    . 'WHERE f.faq_status = :status ORDER BY f.faq_order IS NULL, f.faq_order ASC, f.faq_question ASC',
    'status=1'
);
$Faqs = $Read->getResult() ?: [];
$FaqColumns = $Faqs ? array_chunk($Faqs, (int) ceil(count($Faqs) / 2)) : [];
$FaqCategories = [];
$HasUncategorized = false;
foreach ($Faqs as $Faq) {
    if (!empty($Faq['faq_category']) && !empty($Faq['category_title'])) {
        $FaqCategories[(string) $Faq['faq_category']] = $Faq['category_title'];
    } else {
        $HasUncategorized = true;
    }
}
natcasesort($FaqCategories);
?>

<section class='page-header'>
	<div class='page-header__bg'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/page-header-bg.jpg);'></div>
	<div class='container'>
		<div class='page-header__inner'>
			<div class='page-header__img-1'>
				<img src='<?= INCLUDE_PATH ?>/assets/images/resources/page-header-img-1.png' alt=''>
			</div>
			<h3>Perguntas frequentes</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='<?= BASE ?>' title='<?= Check::safeHtmlChars(SITE_NAME) ?>'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Perguntas frequentes</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<section class='faq-page'>
	<div class='container'>
		<?php if (!$Faqs): ?>
			<p>Nenhuma pergunta frequente publicada no momento.</p>
		<?php else: ?>
			<div class='faq-page__filters' role='search' aria-label='Filtrar perguntas frequentes'>
				<div class='faq-page__filter'>
					<label for='faq-search'>Buscar pergunta ou resposta</label>
					<input id='faq-search' type='search' placeholder='Digite sua dúvida...' autocomplete='off'>
				</div>
				<div class='faq-page__filter'>
					<label for='faq-category'>Categoria</label>
					<select id='faq-category' class='ignore'>
						<option value=''>Todas as categorias</option>
						<?php foreach ($FaqCategories as $CategoryId => $CategoryTitle): ?>
							<option value='<?= Check::safeHtmlChars($CategoryId) ?>'><?= Check::safeHtmlChars($CategoryTitle) ?></option>
						<?php endforeach; ?>
						<?php if ($HasUncategorized): ?>
							<option value='uncategorized'>Sem categoria</option>
						<?php endif; ?>
					</select>
				</div>
			</div>
			<p id='faq-results-count' class='faq-page__results' role='status' aria-live='polite'></p>
			<div class='row'>
				<?php foreach ($FaqColumns as $ColumnIndex => $ColumnFaqs): ?>
					<div class='col-xl-6'>
						<div class='faq-one__left'>
							<div class='faq-one-accrodion'>
								<?php foreach ($ColumnFaqs as $FaqIndex => $Faq): ?>
									<details class='accrodion' name='faq-column-<?= $ColumnIndex ?>'
									         data-category='<?= !empty($Faq['faq_category']) && !empty($Faq['category_title']) ? Check::safeHtmlChars((string) $Faq['faq_category']) : 'uncategorized' ?>'
									         <?= $FaqIndex === 0 ? 'open' : '' ?>>
										<summary class='accrodion-title'>
											<h4><?= Check::safeHtmlChars($Faq['faq_question']) ?></h4>
										</summary>
										<div class='accrodion-content'>
											<div class='inner'>
												<?= $Faq['faq_answer'] ?>
											</div>
										</div>
									</details>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<p id='faq-no-results' class='faq-page__no-results' hidden>Nenhuma pergunta encontrada para os filtros selecionados.</p>
		<?php endif; ?>
	</div>
</section>
<?php if ($Faqs): ?>
	<script src='<?= INCLUDE_PATH ?>/assets/js/faq-page.js' defer></script>
<?php endif; ?>
