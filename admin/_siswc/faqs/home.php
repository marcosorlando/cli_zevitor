<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_FAQS;
    if (!APP_FAQS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO DELETE FAQ TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete ??= new Delete();
        $Delete->exeDelete(DB_FAQS, 'WHERE faq_question IS NULL AND faq_answer IS NULL AND faq_id >= :id', 'id=1');
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // faqs/end inclui esta view com $FaqView = 'end' para listar só as inativas
    $FaqView ??= 'home';

    $S = (string)filter_input(INPUT_GET, 's');
    $C = filter_input(INPUT_GET, 'cat', FILTER_VALIDATE_INT) ?: null;

    $Search = filter_input_array(INPUT_POST);
    if ($Search) {
        header(
            sprintf(
                'Location: dashboard.php?wc=faqs/%s&s=%s&cat=%s',
                $FaqView,
                urlencode((string)($Search['s'] ?? '')),
                urlencode((string)($Search['cat'] ?? ''))
            )
        );

        exit;
    }

    // FILTROS (sempre via bind)
    $WhereString = ('end' === $FaqView ? ' AND (f.faq_status IS NULL OR f.faq_status != 1)' : '');
    $WhereParse = '';
    if ('' !== $S) {
        $WhereString .= " AND (f.faq_question LIKE CONCAT('%', :s, '%') OR f.faq_answer LIKE CONCAT('%', :s, '%'))";
        $WhereParse .= '&s=' . urlencode($S);
    }
    if ($C) {
        $WhereString .= ' AND f.faq_category = :cat';
        $WhereParse .= '&cat=' . $C;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-question">FAQ's</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="Todas as Perguntas" href="dashboard.php?wc=faqs/home">Perguntas</a>
            <?= 'end' === $FaqView ? "<span class='crumb'>/</span> Inativas" : ''; ?>
            <?= '' !== $S ? "<span class='crumb'>/</span> <span class='icon-search'>" . Check::safeHtmlChars($S) . '</span>' : ''; ?>
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="search_faqs" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" value="<?= Check::safeHtmlChars($S); ?>" name="s" placeholder="Pesquisar:" style="width: 38%; margin-right: 3px;">
			<select name="cat" style="width: 45%; margin-right: 3px; padding: 5px 10px">
				<option value="">Todas as categorias</option>
                <?php
                    $Read->fullRead('SELECT category_id, category_title FROM ' . DB_SERVICES_CATEGORIES . ' ORDER BY category_title ASC');
                    foreach ($Read->getResult() ?: [] as $Category) {
                        printf(
                            '<option value="%d"%s>%s</option>',
                            $Category['category_id'],
                            (int)$Category['category_id'] === $C ? ' selected' : '',
                            Check::safeHtmlChars($Category['category_title'])
                        );
                    }
                ?>
			</select>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>
</header>

<div class="dashboard_content">
	<div class="box box100" style="text-align: right">
		<a title="Nova Pergunta" href="dashboard.php?wc=faqs/create" class="btn btn_green icon-plus">Nova Pergunta</a>
		<span class="btn btn_blue icon-spinner9 wc_drag_active" title="Ative e arraste as perguntas para ordenar">Ordenar</span>
	</div>
    <?php
        $Page = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT) ?: 1;
        $Pager = new Pager('dashboard.php?wc=faqs/' . $FaqView . $WhereParse . '&pg=', '<<', '>>', 5);
        $Pager->exePager($Page, 100);

        $Read->fullRead(
            'SELECT f.*, c.category_title FROM ' . DB_FAQS . ' f '
            . 'LEFT JOIN ' . DB_SERVICES_CATEGORIES . ' c ON c.category_id = f.faq_category '
            . 'WHERE 1 = 1' . $WhereString . ' '
            . 'ORDER BY f.faq_order IS NULL, f.faq_order ASC, f.faq_question ASC '
            . 'LIMIT :limit OFFSET :offset',
            sprintf('limit=%d&offset=%d', $Pager->getLimit(), $Pager->getOffset()) . $WhereParse
        );

        if (!$Read->getResult()) {
            $Pager->returnPage();
            echo Check::erro(
                sprintf(
                    'end' === $FaqView
                        ? 'Olá %s, não existem perguntas inativas no momento!'
                        : 'Ainda não existem perguntas cadastradas %s. Comece agora mesmo criando a primeira!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Faq) {
                extract($Faq);

                $FaqQuestion = $faq_question ? Check::safeHtmlChars($faq_question) : 'Edite esse rascunho para poder exibir a pergunta no seu site!';
                $FaqAnswer = $faq_answer ? Check::words(strip_tags($faq_answer), 30) : '';
                $FaqCategory = $category_title ? Check::safeHtmlChars($category_title) : 'Sem categoria';
                $FaqStatus = (1 !== (int)$faq_status ? 'inactive' : '');
                $FaqStatusLabel = (1 === (int)$faq_status ? "<span class='btn btn_green' style='padding: 2px 8px; font-size: 0.8em'>Publicada</span>" : "<span class='btn btn_yellow' style='padding: 2px 8px; font-size: 0.8em'>Rascunho</span>");

                echo "<article class='box box100 post_single wc_draganddrop {$FaqStatus}' callback='Faqs' callback_action='faq_order' id='{$faq_id}'>
                        <div class='box_content' style='display: flex; gap: 15px; align-items: center'>
                            <div style='flex: 1'>
                                <h1 class='icon-question' style='font-size: 1.1em; margin-bottom: 5px'>{$FaqQuestion}</h1>
                                <p style='color: #888; margin-bottom: 5px'>{$FaqAnswer}</p>
                                <p style='font-size: 0.85em'><b class='icon-price-tag'>{$FaqCategory}</b> {$FaqStatusLabel}</p>
                            </div>
                            <div style='white-space: nowrap'>
                                <a title='Editar Pergunta' href='dashboard.php?wc=faqs/create&id={$faq_id}' class='icon-pencil btn btn_blue icon-notext'></a>
                                <span rel='post_single' class='j_delete_action icon-cancel-circle btn btn_red icon-notext' id='{$faq_id}'></span>
                                <span rel='post_single' callback='Faqs' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$faq_id}'>Excluir?</span>
                            </div>
                        </div>
                    </article>";
            }

            $Pager->exePaginator(DB_FAQS . ' f', 'WHERE 1 = 1' . $WhereString, ltrim($WhereParse, '&'));
            echo $Pager->getPaginator();
        }
    ?>
</div>
