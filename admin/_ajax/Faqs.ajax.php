<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_FAQS;

if (!APP_FAQS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo json_encode($jSON);

    exit;
}

usleep(50000);

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Faqs';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

// VALIDA AÇÃO
if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
    // PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT CREATE
    $Create ??= new Create();
    // AUTO INSTANCE OBJECT UPDATE
    $Update ??= new Update();
    // AUTO INSTANCE OBJECT DELETE
    $Delete ??= new Delete();

    // SELECIONA AÇÃO
    switch ($Case) {
        case 'manager':
            $FaqId = (int)($PostData['faq_id'] ?? 0);
            unset($PostData['faq_id'], $PostData['public']);

            $PostData['faq_status'] = (empty($PostData['faq_status']) ? 0 : 1);
            $PostData['faq_category'] = (filter_var($PostData['faq_category'] ?? null, FILTER_VALIDATE_INT) ?: null);

            if (empty(trim((string)($PostData['faq_question'] ?? ''))) || empty(trim(strip_tags((string)($PostData['faq_answer'] ?? ''))))) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<b>ERRO AO CADASTRAR:</b> Para atualizar a pergunta, preencha a pergunta e a resposta!',
                    E_USER_WARNING
                );
                $jSON['error'] = true;

                break;
            }

            $Read->exeRead(DB_FAQS, 'WHERE faq_id = :id', 'id=' . $FaqId);
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>Erro ao atualizar:</b> Desculpe %s, mas não foi possível consultar a pergunta. Experimente atualizar a página!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );

                break;
            }

            $PostData['faq_date'] = date('Y-m-d H:i:s');
            $Update->exeUpdate(DB_FAQS, $PostData, 'WHERE faq_id = :id', 'id=' . $FaqId);

            $jSON['trigger'] = Check::ajaxErro(
                sprintf(
                    '<b class="icon-checkmark">Tudo certo %s</b>: A pergunta foi atualizada com sucesso.',
                    $_SESSION['userLogin']['user_name']
                )
            );

            break;

        case 'faq_order':
            if (isset($PostData['Data']) && is_array($PostData['Data'])) {
                foreach ($PostData['Data'] as $RE) {
                    $Update->exeUpdate(
                        DB_FAQS,
                        ['faq_order' => (int)$RE[1]],
                        'WHERE faq_id = :id',
                        'id=' . (int)$RE[0]
                    );
                }

                $jSON['success'] = true;
            } else {
                $jSON['error'] = true;
            }

            break;

        case 'delete':
            $FaqId = (int)($PostData['del_id'] ?? 0);
            $Delete->exeDelete(DB_FAQS, 'WHERE faq_id = :id', 'id=' . $FaqId);
            $jSON['success'] = true;

            break;
    }

    // RETORNA O CALLBACK
    if ($jSON) {
        echo json_encode($jSON);
    } else {
        $jSON['trigger'] = Check::ajaxErro(
            '<b>OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
            E_USER_ERROR
        );
        echo json_encode($jSON);
    }
} else {
    // ACESSO DIRETO
    die('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
