<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_TEAM;
    if (!APP_TEAM || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $TeamDefaults = [
        'user_id' => null,
        'user_fullname' => '',
        'user_skill' => '',
        'user_icon' => '',
        'user_image' => '',
        'user_order' => 0,
        'user_status' => 0,
    ];

    $TeamId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($TeamId) {
        $Read->exeRead(DB_TEAM, 'WHERE user_id = :id', 'id=' . $TeamId);
        if ($Read->getResult()) {
            // Nome bruto da foto para o file_exists (o extract abaixo já vem escapado para HTML)
            $TeamImage = (string)$Read->getResult()[0]['user_image'];
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                array_replace($TeamDefaults, $Read->getResult()[0])
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = sprintf(
                '<b>OPPSS %s</b>, você tentou editar um funcionário que não existe ou que foi removido recentemente!',
                $Admin['user_name']
            );
            header('Location: dashboard.php?wc=team/home');

            exit;
        }
    } else {
        $TeamCreate = [
            'user_created' => date('Y-m-d H:i:s'),
            'user_status' => 0,
        ];
        $Create->exeCreate(DB_TEAM, $TeamCreate);
        header('Location: dashboard.php?wc=team/create&id=' . $Create->getResult());

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-user-plus"><?php
                echo $user_fullname ?: 'Novo Funcionário'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=team/home">Time</a>
			<span class="crumb">/</span>
			Gerenciar Funcionário
		</p>
	</div>

	<div class="dashboard_header_search" style="font-size: 0.875em; margin-top: 16px;" id="<?php
        echo $TeamId; ?>">
		<span rel='dashboard_header_search' class='j_delete_action icon-warning btn btn_red' id='<?php
            echo $TeamId; ?>'>Deletar Funcionário!</span>
		<span rel='dashboard_header_search' callback='Team' callback_action='delete'
		      class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='<?php
            echo $TeamId; ?>'>EXCLUIR AGORA!</span>
	</div>
</header>

<div class="dashboard_content">
	<form class="auto_save" name="manage_team" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Team"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="user_id" value="<?php
            echo $TeamId; ?>"/>

		<div class="box box70">
			<div class="panel_header default">
				<h2 class="icon-user-plus">Dados do Funcionário</h2>
			</div>

			<div class="box_content">
				<label class="label">
					<span class="legend">Nome Completo:</span>
					<input class="font_large" type="text" name="user_fullname" value="<?php
                        echo $user_fullname; ?>" placeholder="Nome completo:" required/>
				</label>

				<div class="label_50">
					<label class="label">
						<span class="legend">Especialidade / Função:</span>
						<input type="text" name="user_skill" maxlength="155" value="<?php
                            echo $user_skill; ?>" placeholder="Ex.: Especialista em Transmissão"/>
					</label>

					<label class="label">
						<span class="legend">Ordem de Exibição:</span>
						<input type="number" name="user_order" min="0" step="1" value="<?php
                            echo (int)$user_order; ?>" placeholder="0"/>
					</label>
				</div>

                <?php
                    $IconField = 'user_icon';
                    $IconValue = $user_icon;
                    $IconLabel = 'Ícone do funcionário';

                    require __DIR__ . '/../../_tpl/icon-select.wc.php';
                ?>

				<div class="m_top">&nbsp;</div>

				<div class="wc_actions">
					<div class='switch'>
						<input name='user_status' type='checkbox' id='user_status'
						       value='1' <?php
                            echo 1 == $user_status ? 'checked' : ''; ?>>
						<label for="user_status" data-on="ON" data-off="OFF"></label>
					</div>

					<button name="public" value="1" class="btn btn_green icon-share">ATUALIZAR</button>
					<img class="form_load none" style="margin-left: 10px;" alt="Enviando Requisição!"
					     title="Enviando Requisição!" src="_img/load.gif"/>
				</div>
				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">
			<div class="panel_header default">
				<h2 class="icon-image">Foto do Funcionário:</h2>
			</div>

			<div class="box_content">
				<label class='label'>
					<span class='legend'>Tamanho (WEBP 600x560px):</span>
					<input type="file" class="wc_loadimage" name="user_image"/>
				</label>
                <?php
                    $Image = ($TeamImage && is_file('../uploads/' . $TeamImage)
                        ? 'uploads/' . rawurlencode($TeamImage) : 'admin/_img/no_avatar.jpg');
                    $Image = htmlspecialchars(str_replace('%2F', '/', $Image), ENT_QUOTES, 'UTF-8');
                ?>
				<img class="user_image" style="width: 100%;" alt="Foto do Funcionário" title="Foto do Funcionário"
				     src="../tim.php?src=<?php
                         echo $Image; ?>&w=600&h=560"
				     default="../tim.php?src=<?php
                         echo $Image; ?>&w=600&h=560">
			</div>
		</div>
		<div class="clear"></div>
	</form>
</div>
