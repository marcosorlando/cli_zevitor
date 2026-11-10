<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    if (!APP_LEADS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < LEVEL_WC_LEADS) {
        Check::accessBlocked();
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();

    $LeadId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($LeadId) {
        $Read->exeRead(DB_LEADS, 'WHERE lead_id = :id', 'id=' . $LeadId);
        if ($Read->getResult()) {
            $FormData = array_map(
                fn($v) => htmlspecialchars((string)(is_scalar($v) ? $v : ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                $Read->getResult()[0]
            );
            extract($FormData);
        } else {
            $_SESSION['trigger_controll'] = Check::erro(
                sprintf(
                    '<b>OPPSS %s</b>, você tentou editar uma Lead que não existe ou que foi removido recentemente!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
            header('Location: dashboard.php?wc=leads/home');

            exit;
        }
    } else {
        $LeadCreate = [
            'lead_status' => 1,
            'lead_conversion' => 'Cadastro Manual'
        ];
        $Create->exeCreate(DB_LEADS, $LeadCreate);
        header('Location: dashboard.php?wc=leads/create&id=' . $Create->getResult());

        exit;
    }
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-user-check"><?= $lead_name ?? 'Novo Lead '; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=leads/home">Leads</a>
			<span class="crumb">/</span>
			Gerenciar Leads
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Novo Lead" href="dashboard.php?wc=leads/create" class="btn btn_green icon-plus">Adiconar</a>

	</div>
</header>

<div class="dashboard_content">

	<form class="auto_save form_capitalize" name="lead_add" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Leads"/>
		<input type="hidden" name="callback_action" value="manage"/>
		<input type="text" readonly name="lead_conversion" value="<?= $lead_conversion ?? 'CADASTRO MANUAL' ?>"/>
		<input type="hidden" name="lead_id" value="<?php
            echo $LeadId; ?>"/>


		<div class="box box70">

			<div class="panel_header default">
				<h2 class="icon-page-break">Insira os dados do Lead</h2>
			</div>

			<div class="panel">

				<label class="label">
					<span class="legend">Nome:</span>
					<input class="font_medium" type="text" name="lead_name" value="<?php
                        echo $lead_name; ?>"
					       placeholder="Nome do lead:" required/>
				</label>
				<label class="label">
					<span class="legend">Email:</span>
					<input class="font_medium" type="text" name="lead_email" value="<?php
                        echo $lead_email; ?>"
					       placeholder="E-mail do lead:" required/>
				</label>

				<div class="label_50">
					<label class="label">
						<span class="legend">Telefone / WhatsApp:</span>
						<input class="font_medium formPhone" type="text" name="lead_phone" value="<?= $lead_phone ?? ''; ?>"
						       placeholder="Telefone do lead:"/>
					</label>
					<label class="label">
						<span class="legend">Assunto:</span>
						<input class="font_medium" type="text" name="lead_subject" value="<?= $lead_subject ?? ''; ?>"
						       placeholder="Assunto:"/>
					</label>
				</div>

				<div class="label_50">
					<label class="label">
						<span class="legend">Serviço:</span>
						<input class="font_medium" type="text" name="lead_service" value="<?= $lead_service ?? ''; ?>"
						       placeholder="Serviço de interesse:"/>
					</label>
					<label class="label">
						<span class="legend">Veículo (marca / modelo / ano):</span>
						<input class="font_medium" type="text" name="lead_vehicle" value="<?= $lead_vehicle ?? ''; ?>"
						       placeholder="Veículo:"/>
					</label>
				</div>

				<label class="label">
					<span class="legend">Mensagem:</span>
					<textarea class="font_medium" name="lead_message" rows="6"
					          placeholder="Mensagem do lead:"><?= $lead_message ?? ''; ?></textarea>
				</label>
				<div class="clear"></div>
			</div>
		</div>

		<div class="box box30">

			<div class="panel_header default">
				<h2>Foto (3X4)</h2>
			</div>

			<div class="panel">
				<label class="label">
					<input type="file" class="wc_loadimage" name="lead_thumb"/>
				</label>
				<div class="post_create_cover m_botton">
					<div class="upload_progress none">0%</div>
                    <?php
                        $LeadThumb = (!empty($lead_thumb) && file_exists('../uploads/leads/' . $lead_thumb) && !is_dir(
                            '../uploads/leads/' . $lead_thumb
                        ) ? 'uploads/leads/' . $lead_thumb : 'admin/_img/no_image.jpg');
                    ?>
					<img class="post_thumb lead_thumb" style="display: block; margin: 0 auto !important;"
					     src="../tim.php?src=<?php
                             echo $LeadThumb; ?>&w=500&h=auto"
					     default="../tim.php?src=<?php
                             echo $LeadThumb; ?>&w=500&h=auto"/>
				</div>

				<div class="m_top">&nbsp;</div>
				<div class="wc_actions" style="text-align: center; margin-bottom: 10px;">

                    <?= Check::switchOnOff(
                        'lead_status',
                        $lead_status
                    ) ?>

					<button name="public" value="1" class="btn btn_save"><img class='form_load'
					                                                          alt='Enviando Requisição!'
					                                                          title='Enviando Requisição!'
					                                                          src='_img/load_w.gif'/>Salvar
					</button>

				</div>
			</div>
		</div>
	</form>
</div>
