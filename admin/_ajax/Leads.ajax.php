<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../_app/Config.inc.php';

$NivelAcess = LEVEL_WC_LEADS;

$jSON = [];
if (
    !isset($_SESSION['userLogin']['user_level'])
    || (int)$_SESSION['userLogin']['user_level'] < (int)$NivelAcess
) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_WARNING
    );
    echo json_encode($jSON);

    exit;
}

usleep(50000);

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = [];
$CallBack = 'Leads';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?: [];

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

    // ELIMINA CÓDIGOS
    $PostData = array_map(static fn($v) => is_string($v) ? strip_tags($v) : $v, $PostData);

    // SELECIONA AÇÃO
    switch ($Case) {
        // DELETE
        case 'delete':
            $LeadId = filter_var($PostData['del_id'] ?? null, FILTER_VALIDATE_INT);
            if (!$LeadId) {
                $jSON['trigger'] = Check::ajaxErro('<b>OPSS:</b> Lead inválido para exclusão!', E_USER_WARNING);
                $jSON['error'] = true;
                break;
            }

            $Read->fullRead('SELECT lead_thumb FROM ' . DB_LEADS . ' WHERE lead_id = :id', "id={$LeadId}");

            if (
                !empty($Read->getResult()[0]['lead_thumb']) &&
                file_exists('../../uploads/leads/' . $Read->getResult()[0]['lead_thumb']) &&
                !is_dir('../../uploads/leads/' . $Read->getResult()[0]['lead_thumb'])
            ) {
                unlink('../../uploads/leads/' . $Read->getResult()[0]['lead_thumb']);
            }

            $Delete->exeDelete(DB_LEADS, 'WHERE lead_id = :id', "id={$LeadId}");
            $jSON['redirect'] = 'dashboard.php?wc=leads/home';
            $jSON['trigger'] = Check::ajaxErro(
                '<b>Lead Removido:</b> Esse LEAD foi removido com sucesso!'
            );

            break;

        // CAPTURA DE ACORDO COM CALLBACK-ACTION
        case 'manage':

            if (
                !Check::email((string) ($PostData['lead_email'] ?? '')) || !filter_var($PostData['lead_email'] ?? null, FILTER_VALIDATE_EMAIL)
            ) {
                $jSON['trigger'] = Check::ajaxErro(
                    '<strong>OPPSSS: </strong>' . ($PostData['lead_name'] ?? 'Lead') . ' o e-mail informado não é válido!',
                    E_USER_NOTICE
                );
            } else {
                $LeadId = filter_var($PostData['lead_id'] ?? null, FILTER_VALIDATE_INT);
                if (!$LeadId) {
                    $jSON['trigger'] = Check::ajaxErro('<strong>OPPSSS:</strong> Lead inválido para atualização!', E_USER_WARNING);
                    $jSON['error'] = true;
                    break;
                }

                // Só as colunas editáveis no formulário; lead_conversion fica como foi capturado.
                $LeadData = array_intersect_key($PostData, array_flip([
                    'lead_name',
                    'lead_email',
                    'lead_phone',
                    'lead_subject',
                    'lead_service',
                    'lead_vehicle',
                    'lead_message',
                ]));
                $LeadData['lead_status'] = (($PostData['lead_status'] ?? '') === '' ? '0' : '1');

                $Read->exeRead(DB_LEADS, 'WHERE lead_id= :id', "id={$LeadId}");
                $ThisPage = $Read->getResult()[0] ?? [];

                /* Upload: lead_thumb */
                if (isset($_FILES['lead_thumb']['error']) && $_FILES['lead_thumb']['error'] === UPLOAD_ERR_OK) {
                    $File = $_FILES['lead_thumb'];

                    if (
                        !empty($ThisPage['lead_thumb']) && file_exists(
                            '../../uploads/leads/' . $ThisPage['lead_thumb']
                        ) && !is_dir('../../uploads/leads/' . $ThisPage['lead_thumb'])
                    ) {
                        unlink('../../uploads/leads/' . $ThisPage['lead_thumb']);
                    }

                    $Upload = new Upload('../../uploads/leads/');
                    $Upload->image($File, (string) ($LeadData['lead_name'] ?? 'lead'), AVATAR_W);

                    if ($Upload->getResult()) {
                        $LeadData['lead_thumb'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR FOTO:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como foto!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);

                        return;
                    }
                }

                $Update->exeUpdate(DB_LEADS, $LeadData, 'WHERE lead_id = :id', "id={$LeadId}");
                $jSON['trigger'] = Check::ajaxErro('<b>OK!</b> Lead atualizado com sucesso!');
            }

            break;
    }

    // RETORNA O CALLBACK
    if ([] !== $jSON) {
        echo json_encode($jSON);
    } else {
        $jSON['trigger'] = Check::ajaxErro(
            '<b>OPSS:</b> Desculpe. Mas uma ação do sistema não respondeu corretamente. Ao persistir, contate o desenvolvedor!',
            E_USER_WARNING
        );
        echo json_encode($jSON);
    }
} else {
    // ACESSO DIRETO
    exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
