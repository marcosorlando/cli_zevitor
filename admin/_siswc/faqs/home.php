<?php
$AdminLevel = LEVEL_WC_FAQS;
if (!APP_FAQS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level']
    < $AdminLevel):
    die('<div style="text-align: center; margin: 5% 0; color: #C54550; font-size: 1.6em; font-weight: 400; background: #fff; float: left; width: 100%; padding: 30px 0;"><b>ACESSO NEGADO:</b> Você não esta logado<br>ou não tem permissão para acessar essa página!</div>');
endif;

//AUTO DELETE POST TRASH
if (DB_AUTO_TRASH):
    $Delete = new Delete;
    $Delete->ExeDelete(DB_FAQS, "WHERE faq_question IS NULL and faq_answer IS NULL", "");
endif;

// AUTO INSTANCE OBJECT READ
$Read = $Read ?? new Read();

$S = filter_input(INPUT_GET, "s", FILTER_DEFAULT);
$Search = filter_input_array(INPUT_POST);
if ($Search && (isset($Search['s']) || isset($Search['status']))):
    $S = (isset($Search['s']) ? urlencode($Search['s']) : $S);
    $SearchCat = (!empty($Search['searchcat']) ? $Search['searchcat'] : null);
    header("Location: dashboard.php?wc=faqs/home&s={$S}&cat={$SearchCat}&tag={$T}");
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
            <a title="Todas as Perguntas" href="dashboard
            .php?wc=faqs/home">Perguntas</a>
            <?= ($S ? "<span class='crumb'>/</span> <span class='icon-search'>{$S}</span>" : ''); ?>
        </p>
    </div>

    <div class="dashboard_header_search">
        <form name="search_faqs" action="" method="post"
              enctype="multipart/form-data" class="ajax_off">
            <input type="search" value="<?= $S; ?>" name="s" placeholder="Pesquisar:">
            <button class="btn btn_search"></button>
        </form>
    </div>
</header>

<div class="dashboard_content">
    <?php
    $getPage = filter_input(INPUT_GET, 'pg', FILTER_VALIDATE_INT);
    $Page = ($getPage ? $getPage : 1);
    $Paginator = new Pager("dashboard.php?wc=faqs/home&s={$S}&pg=", '<<', '>>', 5);
    $Paginator->ExePager($Page, 100);

    if (!empty($S)):
        $WhereString[0] = "AND (faq_question LIKE '%' :s '%' OR faq_answer LIKE '%' :s '%')";
        $WhereString[1] = "&s={$S}";
    else:
        $WhereString = [0 => '',1 => ''];
    endif;

    $Read->FullRead(
        "SELECT * FROM " . DB_FAQS . " WHERE 1=1 "
        . "{$WhereString[0]} "
        . "ORDER BY faq_order ASC, faq_question ASC "
        . "LIMIT :limit OFFSET :offset",
        "limit={$Paginator->getLimit()}&offset={$Paginator->getOffset()}{$WhereString[1]}"
    );

    if (!$Read->getResult()):
        $Paginator->ReturnPage();
        echo Erro("<span>Ainda não existem perguntas cadastradas. Comece agora mesmo criando a primeira!</span>", E_USER_NOTICE);
    else:
        foreach ($Read->getResult() as $Faq):
            extract($Faq);

            $faq_name = (!empty($faq_name) ? $faq_name : 'Edite esse rascunho para poder exibir a pergunta no seu site!');

            $CourseDragAndDrop = (empty($segment_title) ? 'wc_draganddrop' : null);

            echo "<article class='box box100 post_single {$CourseDragAndDrop}' callback='Faqs' callback_action='faq_order' id='{$faq_id}'>
					<div class='panel_header default'>                           
                        <div class='faq-c question'>
                            <div class='faq-q'> 
                            <span class='faq-t'>+</span>{$faq_question}
                            
                              <div class='actions' style='width: 120px; float: right'; top: 0>
	                        <a title='Editar Pergunta' href='dashboard.php?wc=faqs/create&id={$faq_id}' class='btn btn_edit btn_notext'>
	                        </a>
	                        
	                        <span rel='post_single' class='j_delete_action btn btn_delete btn_notext' id='{$faq_id}'></span>
	                        <span rel='post_single' callback='Faqs' callback_action='delete' class='j_delete_action_confirm btn btn_confirm' style='display: none' id='{$faq_id}'>Excluir?</span>                    
                        </div>
                            
                            </div>                
                            <div class='faq-a'> {$faq_answer} </div>
                        </div>
                        
                      
                    </div>
                  </article>";
        endforeach;

        $Paginator->ExePaginator(
            DB_FAQS,
            "WHERE ( faq_question LIKE '%' :s '%' OR faq_answer LIKE '%' :s '%')",
            "s={$S}"
        );
        echo $Paginator->getPaginator();
    endif;
    ?>
</div>
