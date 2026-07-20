<?php

use App\Conn\Create;
use App\Conn\Read;
use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_SERVICES;
    if (!APP_SERVICES || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    $Read ??= new Read();
    $Create ??= new Create();

    $CategoryDefaults = [
        'category_id' => null,
        'category_parent' => null,
        'category_title' => '',
        'category_slug' => '',
        'category_desc' => '',
    ];

    $CatId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($CatId) {
        $Read->exeRead(DB_SERVICES_CATEGORIES, 'WHERE category_id = :id', 'id=' . $CatId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                array_replace($CategoryDefaults, $Read->getResult()[0])
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = Check::erro(
                sprintf(
                    '<b>OPPSS %s</b>, você tentou editar uma categoria de serviço que não existe ou foi removida recentemente!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
            header('Location: dashboard.php?wc=services/categories');

            exit;
        }
    } else {
        $Title = 'Nova Categoria - ' . date('Y-m-d H:i:s');
        $Create->exeCreate(DB_SERVICES_CATEGORIES, [
            'category_slug' => Check::name($Title),
        ]);
        header('Location: dashboard.php?wc=services/category&id=' . $Create->getResult());

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-price-tags"><?= $category_title ?: 'Nova Categoria'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="Serviços" href="dashboard.php?wc=services/home">Serviços</a>
			<span class="crumb">/</span>
			<a title="Categorias" href="dashboard.php?wc=services/categories">Categorias</a>
			<span class="crumb">/</span>
			Gerenciar Categoria
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Categorias!" href="dashboard.php?wc=services/categories" class="btn btn_blue icon-eye">Ver
			Todas</a>
		<a title="Nova Categoria" href="dashboard.php?wc=services/category" class="btn btn_green icon-plus">Adicionar
			Nova</a>
	</div>
</header>

<div class="dashboard_content">
	<div class="box box100">
		<div class="panel_header default">
			<h2 class="icon-price-tags">Dados da Categoria</h2>
		</div>
		<div class="panel">
			<form class="auto_save" name="category_add" action="" method="post" enctype="multipart/form-data">
				<div class="callback_return"></div>
				<input type="hidden" name="callback" value="Services"/>
				<input type="hidden" name="callback_action" value="category_add"/>
				<input type="hidden" name="category_id" value="<?= $CatId; ?>"/>

				<label class="label">
					<span class="legend">Nome:</span>
					<input class="font_large" type="text" name="category_title" value="<?= $category_title; ?>"
					       placeholder="Título da Categoria:" required/>
				</label>

				<label class="label">
					<span class="legend">Descrição:</span>
					<textarea class="font_medium" name="category_desc" rows="3"
					          placeholder="Sobre a Categoria:" required><?= $category_desc; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">Seção:</span>
					<select name="category_parent">
						<option value="">Essa é uma Seção!</option>
                        <?php
                            $Read->fullRead(
                                'SELECT category_id, category_title FROM ' . DB_SERVICES_CATEGORIES . ' WHERE category_parent IS NULL AND category_id != :ci ORDER BY category_title ASC',
                                'ci=' . $CatId
                            );
                            if ($Read->getResult()) {
                                foreach ($Read->getResult() as $Sess) {
                                    echo '<option';
                                    if ($Sess['category_id'] == $category_parent) {
                                        echo " selected='selected'";
                                    }
                                    echo sprintf(
                                        " value='%s'>&raquo;%s</option>",
                                        $Sess['category_id'],
                                        $Sess['category_title']
                                    );
                                }
                            }
                        ?>
					</select>
				</label>

				<div class="m_top">&nbsp;</div>
				<img class="form_load fl_right none" style="margin-left: 10px; margin-top: 2px;"
				     alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load.gif"/>
				<button class="btn btn_green icon-price-tags fl_right">Atualizar Categoria!</button>
				<div class="clear"></div>
			</form>
		</div>
	</div>
</div>
