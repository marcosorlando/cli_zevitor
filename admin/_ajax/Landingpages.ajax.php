<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_LANDING_PAGES;

if (!APP_LANDING_PAGES || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
    $jSON['trigger'] = Check::ajaxErro(
        '<b>OPPSSS:</b> Você não tem permissão para essa ação ou não está logado como administrador!',
        E_USER_ERROR
    );
    echo json_encode($jSON);

    exit;
}

usleep(50000);

/**
 * Apaga uma imagem de uploads/landingpages/ só quando nenhuma outra página usa o mesmo arquivo:
 * o "Replicar" (reply.php) copia a página apontando para as mesmas imagens.
 */
function removeLandingImage(?string $file, int $pageId): void
{
    if (empty($file)) {
        return;
    }

    $read = new Read();
    $read->fullRead(
        'SELECT page_id FROM ' . DB_LANDING_PAGES . ' WHERE page_id != :id'
        . ' AND (page_logo = :f1 OR page_cover = :f2 OR page_mockup = :f3 OR page_social_media = :f4) LIMIT 1',
        sprintf('id=%d&f1=%2$s&f2=%2$s&f3=%2$s&f4=%2$s', $pageId, rawurlencode($file))
    );
    if ($read->getResult()) {
        return;
    }

    $baseDir = realpath(__DIR__ . '/../../uploads/landingpages');
    $path = realpath(__DIR__ . '/../../uploads/landingpages/' . $file);
    if ($baseDir && $path && is_file($path) && str_starts_with($path, $baseDir . DIRECTORY_SEPARATOR)) {
        unlink($path);
    }
}

// DEFINE O CALLBACK E RECUPERA O POST
$jSON = null;
$CallBack = 'Landingpages';
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
        // DELETE
        case 'delete':
            $DelId = (int)filter_var($PostData['del_id'] ?? 0, FILTER_VALIDATE_INT);
            $Read->fullRead(
                'SELECT page_logo, page_social_media, page_cover, page_mockup FROM ' . DB_LANDING_PAGES . ' WHERE page_id = :pi',
                'pi=' . $DelId
            );

            if ($Read->getResult()) {
                foreach ($Read->getResult()[0] as $Image) {
                    removeLandingImage($Image, $DelId);
                }
            }

            $Delete->exeDelete(DB_LANDING_PAGES, 'WHERE page_id = :id', 'id=' . $DelId);

            $jSON['success'] = true;

            break;

            // MANAGER
        case 'manage':
            $PageId = (int)filter_var($PostData['page_id'] ?? 0, FILTER_VALIDATE_INT);
            unset($PostData['page_id']);

            $PostData['page_status'] = (empty($PostData['page_status']) ? '0' : '1');
            $PostData['page_name'] = (empty($PostData['page_name']) ? Check::name(
                $PostData['page_title']
            ) : Check::name($PostData['page_name']));

            $Read->exeRead(DB_LANDING_PAGES, 'WHERE page_id= :id', 'id=' . $PageId);
            $ThisPage = $Read->getResult()[0];

            // UPLOAD LOGOTIPO
            if (!empty($_FILES['page_logo'])) {
                $File = $_FILES['page_logo'];

                removeLandingImage($ThisPage['page_logo'] ?? null, $PageId);

                $Upload = new Upload('../../uploads/landingpages/');
                $Upload->image($File, $PostData['page_name'] . '-logo', IMAGE_W);

                if ($Upload->getResult()) {
                    $PostData['page_logo'] = $Upload->getResult();
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR LOGO:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como Logotipo!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo json_encode($jSON);

                    return;
                }
            } else {
                unset($PostData['page_logo']);
            }

            // ENVIAR MOCKUP
            if (!empty($_FILES['page_mockup'])) {
                $File = $_FILES['page_mockup'];

                removeLandingImage($ThisPage['page_mockup'] ?? null, $PageId);

                $Upload = new Upload('../../uploads/landingpages/');
                $Upload->image($File, $PostData['page_name'] . '-mkp', 600);

                if ($Upload->getResult()) {
                    $PostData['page_mockup'] = $Upload->getResult();
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR Mockup:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como mockup!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo json_encode($jSON);

                    return;
                }
            } else {
                unset($PostData['page_mockup']);
            }

            // UPLOAD BACKGROUND-IMAGE - BOX
            if (!empty($_FILES['page_cover'])) {
                $File = $_FILES['page_cover'];

                removeLandingImage($ThisPage['page_cover'] ?? null, $PageId);

                $Upload = new Upload('../../uploads/landingpages/');
                $Upload->image($File, $PostData['page_name'] . '-bg', IMAGE_W);

                if ($Upload->getResult()) {
                    $PostData['page_cover'] = $Upload->getResult();
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como capa!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo json_encode($jSON);

                    return;
                }
            } else {
                unset($PostData['page_cover']);
            }

            // UPLOAD SOCIAL-MEDIA-IMAGE - BOX
            if (!empty($_FILES['page_social_media'])) {
                $File = $_FILES['page_social_media'];

                removeLandingImage($ThisPage['page_social_media'] ?? null, $PageId);

                $Upload = new Upload('../../uploads/landingpages/');
                $Upload->image($File, $PostData['page_name'] . '-social', IMAGE_W);

                if ($Upload->getResult()) {
                    $PostData['page_social_media'] = $Upload->getResult();
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem JPG ou PNG para enviar como Thumb para Rede Social!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                    echo json_encode($jSON);

                    return;
                }
            } else {
                unset($PostData['page_social_media']);
            }

            $Read->fullRead(
                'SELECT page_name FROM ' . DB_LANDING_PAGES . ' WHERE page_name = :nm AND page_id != :id',
                sprintf('nm=%s&id=%s', $PostData['page_name'], $PageId)
            );

            if ($Read->getResult()) {
                $PostData['page_name'] = sprintf('%s-%s', $PostData['page_name'], $PageId);
            }

            $jSON['name'] = $PostData['page_name'];
            $jSON['view'] = BASE . ('/' . $PostData['page_name']);

            $PageData = array_intersect_key($PostData, array_flip([
                'page_title', 'page_subtitle', 'page_name', 'page_content', 'page_coluna1', 'page_coluna2',
                'page_logo', 'page_cover', 'page_mockup', 'page_social_media',
                'page_header_color', 'page_box_color', 'page_bg_color', 'page_footer_color',
                'page_btn_text', 'page_btn_text_color', 'page_btn_bg_color', 'page_btn_bg_color_hover',
                'form_title', 'form_bg_color', 'form_text_color', 'page_destino', 'page_status',
            ]));
            $Update->exeUpdate(DB_LANDING_PAGES, $PageData, 'WHERE page_id = :id', 'id=' . $PageId);
            $jSON['trigger'] = Check::ajaxErro('<b>Página atualizada com sucesso!</b>');

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
    exit('<br><br><br><center><h1>Acesso Restrito!</h1></center>');
}
