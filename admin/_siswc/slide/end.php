<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_SLIDES;
    if (!APP_SLIDE || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
        Check::accessBlocked();
    endif;

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;

    //AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH):
        $Delete = new Delete;
        $Delete->exeDelete(DB_SLIDES, "WHERE slide_image IS NULL AND slide_title IS NULL AND slide_id >= :st", "st=1");
    endif;
?>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-images">Destaques Inativos</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?= ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			<a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=slide/home">Em Destaque</a>
			<span class="crumb">/</span>
			Inativos
		</p>
	</div>

	<div class="dashboard_header_search">
		<a title="Novo Slide" href="dashboard.php?wc=slide/create" class="btn btn_green icon-plus">Adicionar</a>
	</div>
</header>
<div class="dashboard_content">
    <?php
        $getPage = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
        $Page = ($getPage ? $getPage : 0);
        $Pager = new Pager('dashboard.php?wc=slide/end&page=', "<<", ">>", 3);
        $Pager->exePager($Page, 5);
        $Read->exeRead(
            DB_SLIDES,
            "WHERE slide_status != 1 OR (slide_start >= NOW() OR slide_end <= NOW()) ORDER BY created_at DESC LIMIT :limit OFFSET :offset",
            "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}"
        );
        if (!$Read->getResult()):
            $Pager->returnPage();
            echo Check::erro(
                "Ainda não existe conteúdo em destaque inativo em seu site. Os destaques expirados ou fora de data são apresentados aqui!</span>",
                E_USER_NOTICE
            );
        else:
            foreach ($Read->getResult() as $Slide):
                extract($Slide);
                $slide_btn_url = $slide_btn_url ?? '';
                $slide_paragraph = $slide_paragraph ?? '';
                $TitleHtml = ($slide_btn_url
                    ? "<a target='_blank' href='" . BASE . "/{$slide_btn_url}' title='{$slide_title}'>{$slide_title}</a>"
                    : $slide_title);
                echo "<article class='box box100 slide_single' id='{$slide_id}'>
                    <header>
                        <h1>{$TitleHtml}</h1>
                    </header>
                    <div class='box_content'>
                    <img src='" . BASE . "/tim.php?src=uploads/{$slide_image}&w=" . SLIDE_W . "&h=" . SLIDE_H . "' title='{$slide_title}' alt='{$slide_title}'>
                    <p style='font-size: 1.2em; margin: 10px 0 20px 0;'><b>de " . date(
                        'd/m/Y H\hi',
                        strtotime($slide_start)
                    ) . " - " . ($slide_end ? date('d/m/Y H\hi', strtotime($slide_end)) : 'Sempre') . ":</b> {$slide_paragraph}</p>
                    <a title='Editar Destaque' href='dashboard.php?wc=slide/create&id={$slide_id}' class='icon-notext icon-pencil btn btn_blue'></a>
                    <span rel='slide_single' class='j_delete_action icon-notext icon-cancel-circle btn btn_red' id='{$slide_id}'></span>
                    <span rel='slide_single' callback='Slides' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$slide_id}'>Deletar Destaque?</span>
                    </div>
                </article>";
            endforeach;

            $Pager->exePaginator(DB_SLIDES, "WHERE slide_status != 1 OR (slide_start >= NOW() OR slide_end <= NOW())");
            echo $Pager->getPaginator();

        endif;
    ?>
</div>
