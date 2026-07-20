<?php

use App\Helpers\Check;

    use App\Conn\Create;
    use App\Conn\Read;

    $AdminLevel = LEVEL_WC_PROJECTS;
    if (!APP_PROJECTS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $ProjectDefaults = [
        'project_id' => null,
        'project_name' => '',
        'project_title' => '',
        'project_subtitle' => '',
        'project_description' => '',
        'project_cover' => '',
        'project_icon' => '',
        'project_category' => '',
        'project_status' => 0,
    ];

    $ProjectId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($ProjectId) {
        $Read->exeRead(DB_PROJECTS, 'WHERE project_id = :id', 'id=' . $ProjectId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                array_replace($ProjectDefaults, $Read->getResult()[0])
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar um projeto que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=projects/home');

            exit;
        }
    } else {
        $Read->fullRead('SELECT count(project_id) as Total FROM ' . DB_PROJECTS . ' WHERE project_status = :st', 'st=1');

        $ProjectCreate = [
            'project_created' => date('Y-m-d H:i:s'),
            'project_status' => 0,
        ];
        $Create->exeCreate(DB_PROJECTS, $ProjectCreate);
        header('Location: dashboard.php?wc=projects/create&id=' . $Create->getResult());

        exit;
    }

    $Search = filter_input_array(INPUT_POST);
    if ($Search && $Search['s']) {
        $S = urlencode((string)$Search['s']);
        header('Location: dashboard.php?wc=projects/home&s=' . $S);

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-hammer"><?php
                echo $project_title ?? 'Novo Projeto'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=projects/home">Projetos</a>
			<span class="crumb">/</span>
			Gerenciar Projeto
		</p>
	</div>

	<div class="dashboard_header_search">
		<a target="_blank" title="Ver no site" href="<?php
            echo BASE . ('/projeto/' . $project_name); ?>"
		   class="wc_view btn btn_green icon-eye">Ver no Site!</a>
	</div>
</header>

<div class="workcontrol_imageupload none" id="post_control">
	<div class="workcontrol_imageupload_content">
		<form name="workcontrol_post_upload" action="" method="post" enctype="multipart/form-data">
			<input type="hidden" name="callback" value="Projects"/>
			<input type="hidden" name="callback_action" value="sendimage"/>
			<input type="hidden" name="project_id" value="<?php
                echo $ProjectId; ?>"/>
			<div class="upload_progress none"
			     style="padding: 5px; background: #00B594; color: #fff; width: 0%; text-align: center; max-width: 100%;">
				0%
			</div>
			<div style="overflow: auto; max-height: 300px;">
				<img class="image image_default" alt="Nova Imagem" title="Nova Imagem"
				     src="../tim.php?src=admin/_img/no_image.jpg&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>"
				     default="../tim.php?src=admin/_img/no_image.jpg&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>"/>
			</div>
			<div class="workcontrol_imageupload_actions">
				<input class="wc_loadimage" type="file" name="image" required/>
				<span class="workcontrol_imageupload_close icon-cancel-circle btn btn_red" id="post_control"
				      style="margin-right: 8px;">Fechar</span>
				<button class="btn btn_green icon-image">Enviar e Inserir!</button>
				<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
				     title="Enviando Requisição!" src="_img/load.gif"/>
			</div>
			<div class="clear"></div>
		</form>
	</div>
</div>

<div class="dashboard_content single_project_form">
	<form class="auto_save" name="manage_project" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Projects"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="project_id" value="<?php
            echo $ProjectId; ?>"/>

		<div class="box box70">
			<div class="box_content">
				<label class="label">
					<span class="legend">Projeto:</span>
					<input class="font_large" type="text" name="project_title" value="<?php
                        echo $project_title; ?>"
					       placeholder="Nome do Projeto:" required/>
				</label>

				<label class="label">
					<span class="legend">Breve Descrição:</span>
					<textarea class="font_medium" name="project_subtitle" rows="3"
					          required><?php
                            echo $project_subtitle; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">Descrição Completa:</span>
					<textarea name="project_description" class="work_mce" rows="10"><?php
                            echo $project_description; ?></textarea>
				</label>

				<label class="label">
					<span class="legend">Categoria:</span>
					<select name="project_category">
						<option value="">Selecione uma categoria</option>
                        <?php
                            $Read->exeRead(
                                DB_PROJECTS_CATEGORIES,
                                'WHERE category_parent IS NULL ORDER BY category_title ASC'
                            );
                            if ($Read->getResult()) {
                                foreach ($Read->getResult() as $Category) {
                                    $Selected = ((string)$project_category === (string)$Category['category_id'] ? ' selected' : '');
                                    echo "<option value='{$Category['category_id']}'{$Selected}>{$Category['category_title']}</option>";

                                    $Read->exeRead(
                                        DB_PROJECTS_CATEGORIES,
                                        'WHERE category_parent = :parent ORDER BY category_title ASC',
                                        'parent=' . $Category['category_id']
                                    );
                                    if ($Read->getResult()) {
                                        foreach ($Read->getResult() as $SubCategory) {
                                            $Selected = ((string)$project_category === (string)$SubCategory['category_id'] ? ' selected' : '');
                                            echo "<option value='{$SubCategory['category_id']}'{$Selected}>&raquo;&raquo; {$SubCategory['category_title']}</option>";
                                        }
                                    }
                                }
                            }
                        ?>
					</select>
				</label>

				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">
			<div class="panel_header default">
				<h2 class="icon-file-picture">Imagem Principal do Projeto:</h2>
				<label class='label'>
					<span class='legend'>Tamanho (JPG <?php
                            echo IMAGE_W; ?>x<?php
                            echo IMAGE_H; ?>px):</span>
					<input type="file" class="wc_loadimage" name="project_cover"/>
				</label>
                <?php
                    $Image = (file_exists('../uploads/' . $project_cover) && !is_dir(
                        '../uploads/' . $project_cover
                    ) ? 'uploads/' . $project_cover : 'admin/_img/no_image.jpg');
                ?>
				<img class="project_cover" alt="Capa do Projeto" title="Capa do Projeto"
				     src="../tim.php?src=<?php
                         echo $Image; ?>&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>"
				     default="../tim.php?src=<?php
                         echo $Image; ?>&w=<?php
                         echo IMAGE_W; ?>&h=<?php
                         echo IMAGE_H; ?>">
                <?php
                    $Read->exeRead(DB_PROJECTS_GALLERY, 'WHERE project_id = :id', 'id=' . $project_id);
                    if ($Read->getResult()) {
                        echo '<div class="pdt_images gallery pdt_single_image">';
                        foreach ($Read->getResult() as $Image) {
                            $ImageUrl = ($Image['image'] && file_exists('../uploads/' . $Image['image']) && !is_dir(
                                '../uploads/' . $Image['image']
                            ) ? '../uploads/' . $Image['image'] : '_img/no_image.jpg');
                            echo sprintf(
                                "<img rel='Projects' id='%s' alt='Imagem em %s' title='Imagem em %s' src='%s'/>",
                                $Image['id'],
                                $project_title,
                                $project_title,
                                $ImageUrl
                            );
                        }
                        echo '</div>';
                    } else {
                        echo '<div class="pdt_images gallery pdt_single_image"></div>';
                    }
                ?>
			</div>

			<div class="box_content">
				<label class="label">
					<span class="legend">Fotos Adicionais (JPG <?php
                            echo IMAGE_W; ?>x<?php
                            echo IMAGE_H; ?>px):</span>
					<input type="file" name="image[]" multiple/>
				</label>

				<div class='label'>
					<label class='label'>
						<span class='legend'>ÍCONE (PNG <?php
                                echo AVATAR_W; ?>x<?php
                                echo AVATAR_H; ?>px):</span>
						<input type="file" class="wc_loadimage" name="project_icon"/>
					</label>
				</div>
                <?php
                    $icone = (file_exists('../uploads/' . $project_icon) && !is_dir(
                        '../uploads/' . $project_icon
                    ) ? 'uploads/' . $project_icon : 'admin/_img/no_image.jpg');
                ?>
				<img class="project_icon" alt="Ícone do Segmento" title="Ícone do Segmento"
				     src="../tim.php?src=<?php
                         echo $icone; ?>&w=<?php
                         echo AVATAR_W; ?>&h=<?php
                         echo AVATAR_H; ?>"
				     default="../tim.php?src=<?php
                         echo $icone; ?>&w=<?php
                         echo AVATAR_W; ?>&h=<?php
                         echo AVATAR_H; ?>">

				<div class="m_top">&nbsp;</div>

				<div class="wc_actions">
					<div class='switch'>
						<input name='project_status' type='checkbox' id='project_status'
						       value='1' <?php
                            echo 1 == $project_status ? 'checked' : ''; ?>>
						<label for="project_status" data-on="ON" data-off="OFF"></label>
					</div>

					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
				<div class="clear"></div>
                <?php
                    $URLSHARE = '/projeto/' . $project_name;
                    $pdt_title = $project_title;
                    $pdt_subtitle = $project_subtitle;

                    require __DIR__ . '/../../_tpl/share.wc.php';
                ?>
			</div>
		</div>
		<div class="clear"></div>
	</form>
</div>
