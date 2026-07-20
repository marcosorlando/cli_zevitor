<?php

use App\Conn\Create;
use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_PROJECTS;

if (!APP_PROJECTS || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
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
$CallBack = 'Projects';
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

    $Upload = new Upload('../../uploads/');

    // SELECIONA AÇÃO
    switch ($Case) {
        case 'manager':
            $ProjectId = $PostData['project_id'];
            $PostData['project_status'] = (empty($PostData['project_status']) ? '0' : $PostData['project_status']);

            $Read->exeRead(DB_PROJECTS, 'WHERE project_id = :id', 'id=' . $ProjectId);

            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>Erro ao atualizar:</b> Desculpe %s, mas não foi possível consultar o projeto. Experimente atualizar a página!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Project = $Read->getResult()[0];

                // var_dump($PostData);
                unset($PostData['project_id'], $PostData['project_cover'], $PostData['image'], $PostData['project_icon']);

                $PostData['project_name'] = Check::name($PostData['project_title']);

                if (isset($_FILES['project_cover']) && (int)($_FILES['project_cover']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $File = $_FILES['project_cover'];

                    if (
                        $Project['project_cover'] && file_exists('../../uploads/' . $Project['project_cover']) && !is_dir(
                            '../../uploads/' . $Project['project_cover']
                        )
                    ) {
                        unlink('../../uploads/' . $Project['project_cover']);
                    }

                    $Upload->image($File, sprintf('%s-%s-', $ProjectId, $PostData['project_name']) . time(), 1200);
                    if ($Upload->getResult()) {
                        $PostData['project_cover'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem JPG de 1200X628px para a capa!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);

                        return;
                    }
                }

                if (isset($_FILES['project_icon']) && (int)($_FILES['project_icon']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $File = $_FILES['project_icon'];

                    if (
                        $Project['project_icon'] && file_exists('../../uploads/' . $Project['project_icon']) && !is_dir(
                            '../../uploads/' . $Project['project_icon']
                        )
                    ) {
                        unlink('../../uploads/' . $Project['project_icon']);
                    }

                    $Upload->image($File, sprintf('%s-%s-', $ProjectId, $PostData['project_name']) . time(), 1200);
                    if ($Upload->getResult()) {
                        $PostData['project_icon'] = $Upload->getResult();
                    } else {
                        $jSON['trigger'] = Check::ajaxErro(
                            sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR CAPA:</b> Olá %s, selecione uma imagem JPG de 1200X628px para a capa!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );
                        echo json_encode($jSON);

                        return;
                    }
                }

                if (!empty($_FILES['image']['tmp_name']) && is_array($_FILES['image']['tmp_name'])) {
                    $File = $_FILES['image'];
                    $gbCount = count($File['type']);
                    $gbKeys = array_keys($File);
                    $gbLoop = 0;

                    for ($gb = 0; $gb < $gbCount; ++$gb) {
                        if ((int)($File['error'][$gb] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                            continue;
                        }

                        foreach ($gbKeys as $Keys) {
                            $gbFiles[$gb][$Keys] = $File[$Keys][$gb];
                        }
                    }

                    $jSON['gallery'] = null;
                    foreach (($gbFiles ?? []) as $UploadFile) {
                        ++$gbLoop;
                        $Upload->image(
                            $UploadFile,
                            sprintf('%s-%d-', $ProjectId, $gbLoop) . time() . base64_encode(time()),
                            1000
                        );
                        if ($Upload->getResult()) {
                            $gbCreate = ['project_id' => $ProjectId, 'image' => $Upload->getResult()];
                            $Create->exeCreate(DB_PROJECTS_GALLERY, $gbCreate);
                            $jSON['gallery'] .= sprintf(
                                "<img rel='Projects' id='%s' alt='Imagem em %s' title='Imagem em %s' src='../uploads/%s'/>",
                                $Create->getResult(),
                                $PostData['project_title'],
                                $PostData['project_title'],
                                $Upload->getResult()
                            );
                        }
                    }
                }

                $Read->fullRead(
                    'SELECT project_id FROM ' . DB_PROJECTS . ' WHERE project_name = :nm AND project_id != :id',
                    sprintf('nm=%s&id=%s', $PostData['project_name'], $ProjectId)
                );
                if ($Read->getResult()) {
                    $PostData['project_name'] = sprintf('%s-%s', $PostData['project_name'], $ProjectId);
                }

                $jSON['name'] = $PostData['project_name'];
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<span><b>PROJETO ATUALIZADO:</b> Olá %s. O projeto %s foi atualizado com sucesso!<span>',
                        $_SESSION['userLogin']['user_name'],
                        $PostData['project_title']
                    )
                );

                $PostData['project_status'] = (empty($PostData['project_status']) ? '0' : '1');
                $PostData['project_category'] = (empty($PostData['project_category']) ? null : (int)$PostData['project_category']);

                $Update->exeUpdate(DB_PROJECTS, $PostData, 'WHERE project_id = :id', 'id=' . $ProjectId);
                $jSON['view'] = BASE . '/projeto/' . $PostData['project_name'];
            }

            break;

        case 'sendimage':
            $NewImage = $_FILES['image'];
            $Read->fullRead(
                'SELECT project_title, project_name FROM ' . DB_PROJECTS . ' WHERE project_id = :id',
                'id=' . $PostData['project_id']
            );
            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Desculpe %s, mas não foi possível identificar o projeto vinculado!",
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Upload = new Upload('../../uploads/');
                $Upload->image($NewImage, $PostData['project_id'] . '-' . time(), IMAGE_W);
                if ($Upload->getResult()) {
                    $PostData['image'] = $Upload->getResult();

                    $Create->exeCreate(DB_PROJECTS_IMAGE, $PostData);
                    $jSON['tinyMCE'] = sprintf(
                        "<img title='%s' alt='%s' src='../uploads/%s'/>",
                        $Read->getResult()[0]['project_title'],
                        $Read->getResult()[0]['project_title'],
                        $PostData['image']
                    );
                } else {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            "<b class='icon-image'>ERRO AO ENVIAR IMAGEM:</b> Olá %s, selecione uma imagem JPG ou PNG para inserir no projeto!",
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                }
            }

            break;

        case 'category_add':
            $CatId = $PostData['category_id'];
            unset($PostData['category_id']);

            $PostData['category_slug'] = Check::name($PostData['category_title']);
            $PostData['category_parent'] = ('' !== $PostData['category_parent'] && '0' !== $PostData['category_parent'] ? $PostData['category_parent'] : null);

            $Read->fullRead(
                'SELECT category_id FROM ' . DB_PROJECTS_CATEGORIES . ' WHERE category_slug = :cn AND category_id != :ci',
                sprintf('cn=%s&ci=%s', $PostData['category_slug'], $CatId)
            );
            if ($Read->getResult()) {
                $PostData['category_slug'] = $PostData['category_slug'] . '-' . $CatId;
            }

            $Read->fullRead(
                'SELECT category_id FROM ' . DB_PROJECTS_CATEGORIES . ' WHERE category_parent = :ci',
                'ci=' . $CatId
            );
            if (
                $Read->getResult()
                && isset($PostData['category_parent'])
                && '' !== $PostData['category_parent']
                && '0' !== $PostData['category_parent']
            ) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>OPPSSS: </b> %s, uma seção que possui categorias filhas não pode ser atribuída como filha de outra seção.',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Update->exeUpdate(DB_PROJECTS_CATEGORIES, $PostData, 'WHERE category_id = :id', 'id=' . $CatId);
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf('<b>TUDO CERTO: </b> A categoria <b>%s</b> foi atualizada com sucesso!', $PostData['category_title'])
                );
            }

            break;

        case 'category_remove':
            $PostData['category_id'] = $PostData['del_id'];
            $Read->fullRead(
                'SELECT category_title, category_id FROM ' . DB_PROJECTS_CATEGORIES . ' WHERE category_parent = :cat',
                'cat=' . $PostData['category_id']
            );

            if ($Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>OPPSSS: </b> Olá %s, para deletar uma categoria certifique-se que ela não tem categorias filhas cadastradas!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Read->fullRead(
                    'SELECT project_id FROM ' . DB_PROJECTS . ' WHERE project_category = :cat',
                    'cat=' . $PostData['category_id']
                );

                if ($Read->getResult()) {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            '<b>%s PROJETOS: </b> Olá %s, não é possível remover categorias com projetos cadastrados!',
                            $Read->getRowCount(),
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );
                } else {
                    $Delete->exeDelete(
                        DB_PROJECTS_CATEGORIES,
                        'WHERE category_id = :cat',
                        'cat=' . $PostData['category_id']
                    );
                    $jSON['success'] = true;
                }
            }

            break;

        case 'delete':
            $ProjectId = $PostData['del_id'];
            $Read->exeRead(DB_PROJECTS, 'WHERE project_id = :id', 'id=' . $ProjectId);
            $Project = $Read->getResult()[0] ?? null;

            if (!$Project) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>OPSS:</b> Desculpe %s. Não foi possível deletar pois o projeto não existe ou foi removido recentemente!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $ProjectCover = '../../uploads/' . $Project['project_cover'];

                if (file_exists($ProjectCover) && !is_dir($ProjectCover)) {
                    unlink($ProjectCover);
                }

                $Read->exeRead(DB_PROJECTS_IMAGE, 'WHERE project_id = :id', 'id=' . $Project['project_id']);
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $ProjectImage) {
                        $ProjectImageIs = '../../uploads/' . $ProjectImage['image'];
                        if (file_exists($ProjectImageIs) && !is_dir($ProjectImageIs)) {
                            unlink($ProjectImageIs);
                        }
                    }

                    $Delete->exeDelete(DB_PROJECTS_IMAGE, 'WHERE project_id = :id', 'id=' . $Project['project_id']);
                }

                $Read->exeRead(DB_PROJECTS_GALLERY, 'WHERE project_id = :id', 'id=' . $Project['project_id']);
                if ($Read->getResult()) {
                    foreach ($Read->getResult() as $ProjectGallery) {
                        $ProjectGalleryImage = '../../uploads/' . $ProjectGallery['image'];
                        if (file_exists($ProjectGalleryImage) && !is_dir($ProjectGalleryImage)) {
                            unlink($ProjectGalleryImage);
                        }
                    }

                    $Delete->exeDelete(DB_PROJECTS_GALLERY, 'WHERE project_id = :id', 'id=' . $Project['project_id']);
                }

                $Delete->exeDelete(DB_PROJECTS, 'WHERE project_id = :id', 'id=' . $Project['project_id']);
                $jSON['success'] = true;
            }

            break;

        case 'gbremove':
            $Read->fullRead('SELECT image FROM ' . DB_PROJECTS_GALLERY . ' WHERE id = :id', 'id=' . $PostData['img']);
            if ($Read->getResult()) {
                $ImageRemove = '../../uploads/' . $Read->getResult()[0]['image'];
                if (file_exists($ImageRemove) && !is_dir($ImageRemove)) {
                    unlink($ImageRemove);
                }

                $Delete->exeDelete(DB_PROJECTS_GALLERY, 'WHERE id = :id', 'id=' . $PostData['img']);
                $jSON['success'] = true;
            }

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
