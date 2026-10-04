<?php
    $AdminLevel = LEVEL_WC_FAQS;
    if (!APP_FAQS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel):
        die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
    endif;
    // AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;
    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)):
        $Create = new Create;
    endif;

    $FaqId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($FaqId):
        $Read->ExeRead(DB_FAQS, "WHERE faq_id = :id", "id={$FaqId}");
        if ($Read->getResult()):
            $FormData = array_map('htmlspecialchars', $Read->getResult()[0]);
            extract($FormData);
        else:
            $_SESSION['trigger_controll'] = "<b>OPPSS {$Admin['user_name']}</b>, você tentou editar uma pergunta que não existe ou que foi removida recentemente!";
            header('Location: dashboard.php?wc=faqs/home');
        endif;
    else:
        $FaqCreate = ['faq_date' => date('Y-m-d H:i:s')];
        $Create->ExeCreate(DB_FAQS, $FaqCreate);
        header('Location: dashboard.php?wc=faqs/create&id=' . $Create->getResult());
    endif;
?>

<header class="dashboard_header">
    <div class="dashboard_header_title">
        <h1 class="icon-question"> FAQ`s</h1>
        <p class="dashboard_header_breadcrumbs">
            &raquo; <?= ADMIN_NAME; ?>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
            <span class="crumb">/</span>
            <a title="<?= ADMIN_NAME; ?>" href="dashboard.php?wc=faqs/home">Perguntas</a>
            <span class="crumb">/</span>
            Gerenciar Perguntas
        </p>
    </div>

    <div class="dashboard_header_search">
        <a title="Ver dúvidas" href="dashboard.php?wc=faqs/home" class="btn btn_blue icon-eye">Ver
            Todas</a>
        <a title="Nova dúvida" href="dashboard.php?wc=faqs/create" class="btn btn_green icon-plus">Adicionar</a>
    </div>
</header>

<div class="dashboard_content">

    <form name="faq_create" class="auto_save" action="" method="post"
          enctype="multipart/form-data">
        <input type="hidden" name="callback" value="Faqs"/>
        <input type="hidden" name="callback_action" value="manager"/>
        <input type="hidden" name="faq_id" value="<?= $FaqId; ?>"/>

        <div class="box box70">
            <div class="box_content">
                <label class="label">
                    <span class="legend">Pergunta:</span>
                    <input style="font-size: 1.4em;" type="text"
                           name="faq_question" value="<?= $faq_question; ?>"
                           placeholder="Pergunta:" required/>
                </label>

                <label class="label">
                    <span class="legend">Resposta:</span>
                    <textarea class="work_mce"
                              rows="50" name="faq_answer"
                               class="" required><?= $faq_answer;
                              ?></textarea>
                </label>

                <div class="clear"></div>
            </div>
        </div>
        <div class="box box30">
            <div class="box_content">

                <div class="box_content">
                    <div class="m_top">&nbsp;</div>
                    <div class="wc_actions" style="text-align: center">
                        <label class="label_check label_publish <?=
                            ($faq_status == 1 ? 'active' : ''); ?>"><input
                                style="margin-top: -1px;" type="checkbox"
                                value="1" name="faq_status" <?= ($faq_status ==
                            1 ? 'checked' : ''); ?>> Publicar Agora!</label>

                        <button name="public" value="1" class="btn btn_update"> ATUALIZAR <img class="form_load" alt="Enviando Requisição!" title="Enviando Requisição!" src="_img/load_w.gif"/></button>

                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </form>
</div>
