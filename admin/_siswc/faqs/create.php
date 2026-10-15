<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_FAQS;
    if (!APP_FAQS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $FaqDefaults = [
        'faq_question' => '',
        'faq_answer' => '',
        'faq_category' => '',
        'faq_status' => 0,
    ];

    $FaqId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($FaqId) {
        $Read->exeRead(DB_FAQS, 'WHERE faq_id = :id', 'id=' . $FaqId);
        if ($Read->getResult()) {
            $FormData = array_map(
                static fn ($Value) => Check::safeHtmlChars(null === $Value ? null : (string)$Value),
                $Read->getResult()[0]
            );
            extract(array_merge($FaqDefaults, $FormData));
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar uma pergunta que não existe ou que foi removida recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=faqs/home');

            exit;
        }
    } else {
        $Create->exeCreate(DB_FAQS, ['faq_date' => date('Y-m-d H:i:s'), 'faq_status' => 0]);
        header('Location: dashboard.php?wc=faqs/create&id=' . $Create->getResult());

        exit;
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
			<span class="crumb">/</span>
			Gerenciar Pergunta
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Perguntas" href="dashboard.php?wc=faqs/home" class="btn btn_blue icon-eye">Ver Todas</a>
		<a title="Nova Pergunta" href="dashboard.php?wc=faqs/create" class="btn btn_green icon-plus">Adicionar</a>
	</div>
</header>

<div class="dashboard_content">
	<form name="faq_create" class="auto_save" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Faqs"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="faq_id" value="<?= $FaqId; ?>"/>

		<div class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Pergunta:</span>
					<input style="font-size: 1.4em;" type="text" name="faq_question" value="<?= $faq_question; ?>"
					       placeholder="Pergunta:" maxlength="300" required/>
				</label>

				<label class="label">
					<span class="legend">Resposta:</span>
					<textarea class="work_mce" rows="20" name="faq_answer"><?= $faq_answer; ?></textarea>
				</label>

				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">
			<div class="box_content">
				<label class="label">
					<span class="legend">Categoria:</span>
					<select name="faq_category">
						<option value="">Selecione uma categoria</option>
                        <?php
                            $Read->exeRead(
                                DB_SERVICES_CATEGORIES,
                                'WHERE category_parent IS NULL ORDER BY category_title ASC'
                            );
                            foreach ($Read->getResult() ?: [] as $Category) {
                                $Selected = ((string)$faq_category === (string)$Category['category_id'] ? ' selected' : '');
                                echo "<option value='{$Category['category_id']}'{$Selected}>{$Category['category_title']}</option>";

                                $Read->exeRead(
                                    DB_SERVICES_CATEGORIES,
                                    'WHERE category_parent = :parent ORDER BY category_title ASC',
                                    'parent=' . $Category['category_id']
                                );
                                foreach ($Read->getResult() ?: [] as $SubCategory) {
                                    $Selected = ((string)$faq_category === (string)$SubCategory['category_id'] ? ' selected' : '');
                                    echo "<option value='{$SubCategory['category_id']}'{$Selected}>&raquo;&raquo; {$SubCategory['category_title']}</option>";
                                }
                            }
                        ?>
					</select>
				</label>

				<div class="m_top">&nbsp;</div>

				<div class="wc_actions">
					<div class='switch'>
						<input name='faq_status' type='checkbox' id='faq_status' value='1' <?= 1 === (int)$faq_status ? 'checked' : ''; ?>>
						<label for="faq_status" data-on="ON" data-off="OFF"></label>
					</div>

					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		<div class="clear"></div>
	</form>
</div>
