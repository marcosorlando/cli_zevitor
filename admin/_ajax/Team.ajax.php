<?php

use App\Conn\Delete;
use App\Conn\Read;
use App\Conn\Update;
use App\Helpers\Check;
use App\Models\Upload;

session_start();

require __DIR__ . '/../../vendor/autoload.php';
$NivelAcess = LEVEL_WC_TEAM;

if (!APP_TEAM || empty($_SESSION['userLogin']) || empty($_SESSION['userLogin']['user_level']) || $_SESSION['userLogin']['user_level'] < $NivelAcess) {
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
$CallBack = 'Team';
$PostData = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];

// VALIDA AÇÃO
if (isset($PostData['callback_action'], $PostData['callback']) && $PostData['callback'] === $CallBack) {
    // PREPARA OS DADOS
    $Case = $PostData['callback_action'];
    unset($PostData['callback'], $PostData['callback_action']);

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();
    // AUTO INSTANCE OBJECT UPDATE
    $Update ??= new Update();
    // AUTO INSTANCE OBJECT DELETE
    $Delete ??= new Delete();

    $Upload = new Upload('../../uploads/');

    // Remove a foto do funcionário, só se o arquivo estiver dentro de uploads/
    $RemoveImage = static function (?string $Image): void {
        $UploadsDir = realpath(__DIR__ . '/../../uploads');
        $ImagePath = ($UploadsDir && $Image ? realpath($UploadsDir . '/' . $Image) : false);

        if ($ImagePath && str_starts_with($ImagePath, $UploadsDir . DIRECTORY_SEPARATOR) && is_file($ImagePath)) {
            unlink($ImagePath);
        }
    };

    // SELECIONA AÇÃO
    switch ($Case) {
        case 'manager':
            $TeamId = (int)($PostData['user_id'] ?? 0);
            $Read->exeRead(DB_TEAM, 'WHERE user_id = :id', 'id=' . $TeamId);

            if (!$Read->getResult()) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>Erro ao atualizar:</b> Desculpe %s, mas não foi possível consultar o funcionário. Experimente atualizar a página!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                $Member = $Read->getResult()[0];
                $FullName = mb_substr(trim((string)($PostData['user_fullname'] ?? '')), 0, 255);

                if ('' === $FullName) {
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            '<b>OPSS:</b> %s, informe o nome completo do funcionário!',
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );

                    break;
                }

                // Só colunas editáveis vão para o UPDATE: o Update interpola os nomes das chaves no SQL
                $TeamData = [
                    'user_fullname' => $FullName,
                    'user_skill' => mb_substr(trim((string)($PostData['user_skill'] ?? '')), 0, 155),
                    // Ícone é uma classe do flaticon.css do tema (ex.: icon-mechanical)
                    'user_icon' => preg_match('/^icon-[\w-]+$/', (string)($PostData['user_icon'] ?? '')) ? $PostData['user_icon'] : '',
                    'user_order' => max(0, (int)($PostData['user_order'] ?? 0)),
                    'user_status' => (int)!empty($PostData['user_status']),
                ];

                if (isset($_FILES['user_image']) && (int)($_FILES['user_image']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
                    $Upload->image(
                        $_FILES['user_image'],
                        sprintf('%s-%s-', $TeamId, Check::name($FullName)) . time(),
                        600,
                        'team'
                    );

                    if (!$Upload->getResult()) {
                        $jSON['trigger'] = Check::ajaxErro(
                            sprintf(
                                "<b class='icon-image'>ERRO AO ENVIAR FOTO:</b> Olá %s, selecione uma imagem JPG, PNG ou WEBP de 600x560px!",
                                $_SESSION['userLogin']['user_name']
                            ),
                            E_USER_WARNING
                        );

                        break;
                    }

                    $TeamData['user_image'] = $Upload->getResult();
                }

                $Update->exeUpdate(DB_TEAM, $TeamData, 'WHERE user_id = :id', 'id=' . $TeamId);

                if (!$Update->getResult()) {
                    // Descarta a foto nova para não deixar arquivo órfão
                    $RemoveImage($TeamData['user_image'] ?? null);
                    $jSON['trigger'] = Check::ajaxErro(
                        sprintf(
                            '<b>Erro ao atualizar:</b> Desculpe %s, mas não foi possível salvar o funcionário. Tente novamente!',
                            $_SESSION['userLogin']['user_name']
                        ),
                        E_USER_WARNING
                    );

                    break;
                }

                // A foto antiga só sai depois que o registro já aponta para a nova
                if (isset($TeamData['user_image'])) {
                    $RemoveImage($Member['user_image']);
                }

                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>FUNCIONÁRIO ATUALIZADO:</b> Olá %s. Os dados de %s foram atualizados com sucesso!',
                        $_SESSION['userLogin']['user_name'],
                        htmlspecialchars($FullName, ENT_QUOTES, 'UTF-8')
                    )
                );
            }

            break;

        case 'delete':
            $TeamId = (int)($PostData['del_id'] ?? 0);
            $Read->exeRead(DB_TEAM, 'WHERE user_id = :id', 'id=' . $TeamId);
            $Member = $Read->getResult()[0] ?? null;

            if (!$Member) {
                $jSON['trigger'] = Check::ajaxErro(
                    sprintf(
                        '<b>OPSS:</b> Desculpe %s. Não foi possível deletar pois o funcionário não existe ou foi removido recentemente!',
                        $_SESSION['userLogin']['user_name']
                    ),
                    E_USER_WARNING
                );
            } else {
                // Remove o registro antes da foto: se o delete falhar, a foto continua válida
                $Delete->exeDelete(DB_TEAM, 'WHERE user_id = :id', 'id=' . $TeamId);
                $RemoveImage($Member['user_image']);
                $jSON['success'] = true;
                $jSON['redirect'] = 'dashboard.php?wc=team/home';
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
