<?php
session_start();
require '../../_app/Config.inc.php';
$NivelAcess = LEVEL_WC_FAQS;

if (!APP_FAQS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess):
    $jSON['trigger'] = AjaxErro('<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!', E_USER_ERROR);
    echo json_encode($jSON);
    die;
endif;
usleep(50000);

//DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Faqs';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT);

//VALIDA AÇÃO
if ($PostData && $PostData['callback_action'] && $PostData['callback'] == $CallBack):
    //PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    // AUTO INSTANCE OBJECT READ
    if (empty($Read)):
        $Read = new Read;
    endif;
    // AUTO INSTANCE OBJECT CREATE
    if (empty($Create)):
        $Create = new Create;
    endif;
    // AUTO INSTANCE OBJECT UPDATE
    if (empty($Update)):
        $Update = new Update;
    endif;
    // AUTO INSTANCE OBJECT DELETE
    if (empty($Delete)):
        $Delete = new Delete;
    endif;

    //SELECIONA AÇÃO
    switch ($Case):
        //GERENCIA
        case 'manager':
            $FaqId = $PostData['faq_id'];

            if (in_array('', $PostData)):
                $jSON['trigger'] = AjaxErro('<b>ERRO AO CADASTRAR:</b> Para atualizar a pergunta, favor preencha todos os campos!', E_USER_ERROR);
                $jSON['error'] = true;
            else:
                $PostData['faq_date'] = date('Y-m-d H:i:s');
                $faq_status =  !empty($PostData['faq_status']) ? $PostData['faq_status'] : 0;

           // var_dump($PostData);

                $Update->ExeUpdate(DB_FAQS, $PostData, "WHERE faq_id = :id", "id={$FaqId}");

                $jSON['trigger'] = AjaxErro("<b>Tudo certo {$_SESSION['userLogin']['user_name']}</b>: A pergunta foi atualizada com sucesso.");
            endif;
            break;

        //DELETA
        case 'delete':

            $FaqId = $PostData['del_id'];
            $Delete->ExeDelete(DB_FAQS, "WHERE faq_id = :id", "id={$FaqId}");
            $jSON['success'] = true;
            break;
    endswitch;

    //RETORNA O CALLBACK
    if ($jSON):
        echo json_encode($jSON);
    else:
        $jSON['trigger'] = AjaxErro('<b>OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!', E_USER_ERROR);
        echo json_encode($jSON);
    endif;
else:
    //ACESSO DIRETO
    die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
endif;
