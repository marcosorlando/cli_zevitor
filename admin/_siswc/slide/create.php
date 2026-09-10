<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Helpers\Check;

    $AdminLevel = LEVEL_WC_SLIDES;
    if (!APP_SLIDE || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
        Check::accessBlocked();
    endif;

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;

    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)):
        $Create = new Create;
    endif;

    $SlideId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($SlideId):
        $Read->exeRead(DB_SLIDES, "WHERE slide_id = :id", "id={$SlideId}");
        if ($Read->getResult()):
            $FormData = array_map(static fn($value) => Check::safeHtmlChars($value), $Read->getResult()[0]);
            extract($FormData);
        else:
            $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar um slide que não existe ou que foi removido recentemente!";
            header('Location: dashboard.php?wc=slide/home');
            exit;
        endif;
    else:
        $SlideCreate = [
            'slide_start' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $Create->exeCreate(DB_SLIDES, $SlideCreate);
        header('Location: dashboard.php?wc=slide/create&id=' . $Create->getResult());
        exit;
    endif;

    $slide_title ??= '';
    $slide_subtitle ??= '';
    $slide_paragraph ??= '';
    $slide_background ??= '';
    $slide_image ??= '';
    $mobile_image ??= '';
    $slide_start ??= '';
    $slide_end ??= '';
    $slide_btn_text ??= '';
    $slide_btn_url ??= '';
    $slide_google_review ??= '0';
    $slide_status ??= '0';
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-camera"><?= $slide_title ?: 'Novo Slide'; ?></h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=slide/home">Slides</a>
			<span class="crumb">/</span>
			Gerenciar Banner
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Ver Slides!" href="dashboard.php?wc=slide/home" class="btn btn_blue icon-eye">Ver todos</a>
		<a title="Novo Slide!" href="dashboard.php?wc=slide/create" class="btn btn_green icon-plus">Adicionar</a>
	</div>
</header>

<div class="dashboard_content">
	<form name="post_create" class="auto_save" action="" method="post" enctype="multipart/form-data">
		<input type="hidden" name="callback" value="Slides"/>
		<input type="hidden" name="callback_action" value="manager"/>
		<input type="hidden" name="slide_id" value="<?= $SlideId; ?>"/>

		<article class="box box70">
			<div class="panel_header default">
				<h2 class="icon-images">Dados dos Banner destaque:</h2>
			</div>

			<div class="panel">
				<label class="label">
					<span class="legend">Sub-título (chamada acima do título):</span>
					<input type="text" name="slide_subtitle" maxlength="100"
					       value="<?= $slide_subtitle; ?>"/>
				</label>

				<label class="label">
					<span class="legend"><strong>Título:</strong> (para destacar em vermelho tag: 'span' e br paraquebra delinha)
						.</small></span>
					<input type="text" name="slide_title" maxlength="100"
					       value="<?= $slide_title; ?>" required/>
				</label>

				<label class="label">
					<span class="legend">Parágrafo: (MÁXIMO 155 caracteres)</span>
					<textarea maxlength="155" name="slide_paragraph" rows="3"
					          required><?= $slide_paragraph; ?></textarea>
				</label>

				<div class="label_50">
					<label class="label">
						<span class="legend">Texto do Botão CTA:</span>
						<input type="text" name="slide_btn_text" maxlength="50"
						       value="<?= $slide_btn_text; ?>"/>
					</label>
					<label class="label">
						<span class="legend">URL do Botão CTA:</span>
						<input type="text" name="slide_btn_url" maxlength="50"
						       value="<?= $slide_btn_url; ?>"/>
					</label>
				</div>

				<div class="label_50">

					<label class="label">
						<span class="legend">Divulgar <b>a partir de:</b></span>
						<input type="text" class="formTime" name="slide_start"
						       value="<?= (!empty($slide_start) ? date('d/m/Y H:i:s', strtotime($slide_start)) : date(
                                   'd/m/Y H:i:s'
                               )); ?>" required/>
					</label>

					<label class="label">
						<span class="legend">Divulgar <b>até dia:</b> (opcional)</span>
						<input type="text" class="formTime" name="slide_end"
						       value="<?= (!empty($slide_end) ? date('d/m/Y H:i:s', strtotime($slide_end)) : date(
                                   'd/m/Y H:i:s',
                                   strtotime("+1month")
                               )); ?>"/>
					</label>

				</div>
				<div class="clear"></div>

				<div class="wc_actions" style="background:#dedede; padding: 10px">

                    <?php
                        echo Check::switchOnOff(
                            'slide_google_review',
                            $slide_google_review,
                            'Avaliaçãos Google',
                            'SIM',
                            'NÃO'
                        );
                        echo Check::switchOnOff(
                            'slide_status',
                            $slide_status,
                            'Publicar:',
                            'SIM',
                            'NÃO'
                        );
                    ?>

					<button name="public" value="1" class="btn btn_save">
						<img class='form_load none' alt='Enviando Requisição!'
						     title='Enviando Requisição!' src='_img/load_w.gif'/> Salvar
					</button>
				</div>
				<div class="clear"></div>
			</div>
		</article>

		<aside class='box box30'>

			<div class='panel_header default'>
				<div class='upload_progress none'>0%</div>
                <?php
                    $BackgroundImage = (!empty($slide_background) && file_exists(
                        "../uploads/{$slide_background}"
                    ) && !is_dir(
                        "../uploads/{$slide_background}"
                    ) ? "uploads/{$slide_background}" : 'admin/_img/no_image.jpg');
                    $SlideImage = (!empty($slide_image) && file_exists("../uploads/{$slide_image}") && !is_dir(
                        "../uploads/{$slide_image}"
                    ) ? "uploads/{$slide_image}" : 'admin/_img/no_image.jpg');
                    $MobileImage = (!empty($mobile_image) && file_exists("../uploads/{$mobile_image}") && !is_dir(
                        "../uploads/{$mobile_image}"
                    ) ? "uploads/{$mobile_image}" : 'admin/_img/no_image.jpg');
                ?>


				<label class='label m_top'>
					<span class='legend'>Background: (.webp:  <?= SLIDE_W; ?>x<?= SLIDE_H; ?>px)</span>
					<input type="file" class="wc_loadimage" name="slide_background"/>
				</label>
				<img class='slide_background post_cover' alt='Fundo' title='Imagem de fundo do banner'
				     src="../tim.php?src=<?= $BackgroundImage; ?>&w=<?= SLIDE_W / 3; ?>&h=<?= SLIDE_H / 3; ?>"
				     default="../tim.php?src=<?= $BackgroundImage; ?>&w=<?= SLIDE_W / 3; ?>&h=<?= SLIDE_H / 3; ?>"/>


				<label class="label m_top">
					<span class="legend">Imagem: (.webp:  950x650px)</span>
					<input type="file" class="wc_loadimage" name="slide_image"/>
				</label>
				<img class='slide_image post_cover' alt='Capa' title='Imagem principal (destaque)'
				     src="../tim.php?src=<?= $SlideImage; ?>&w=950&h=650"
				     default="../tim.php?src=<?= $SlideImage; ?>&w=950&h=650"/>

				<label class="label m_top">
					<span class="legend">Mobile Slide: (.webp:  640X900px)</span>
					<input type="file" class="wc_loadimage" name="mobile_image"/>
				</label>

				<img class='post_cover mobile_image' alt='Mobile Image' title='Imagem para Mobile'
				     src="../tim.php?src=<?= $MobileImage; ?>&w=640&h=900"
				     default="../tim.php?src=<?= $MobileImage; ?>&w=640&h=900"/>
			</div>

		</aside>


	</form>
</div>
