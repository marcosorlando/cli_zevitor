<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $AdminLevel = LEVEL_WC_TEAM;
    if (!APP_TEAM || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < $AdminLevel) {
        Check::accessBlocked();
    }

    // AUTO DELETE TEAM TRASH (rascunhos com mais de 1 hora, para não apagar um que outro admin está editando)
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(
            DB_TEAM,
            'WHERE user_fullname IS NULL AND user_image IS NULL AND user_status = :st AND user_created < DATE_SUB(NOW(), INTERVAL 1 HOUR)',
            'st=0'
        );
    }

    // AUTO INSTANCE OBJECT READ
    $Read ??= new Read();

    $rawSearch = filter_input(INPUT_GET, 's', FILTER_DEFAULT);
    $searchTerm = is_string($rawSearch) ? trim($rawSearch) : '';

    $rawOpt = filter_input(INPUT_GET, 'opt', FILTER_DEFAULT);
    $filterOption = is_string($rawOpt) ? trim($rawOpt) : '';
    if (!in_array($filterOption, ['active', 'inactive'], true)) {
        $filterOption = '';
    }

    $Search = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?? [];
    if ($Search) {
        $query = array_filter([
            's' => trim((string)($Search['s'] ?? '')),
            'opt' => in_array($Search['opt'] ?? '', ['active', 'inactive'], true) ? $Search['opt'] : '',
        ]);

        $redirectUrl = 'dashboard.php?wc=team/home';
        if ([] !== $query) {
            $redirectUrl .= '&' . http_build_query($query);
        }

        header('Location: ' . $redirectUrl);

        exit;
    }

    $conditions = [];
    $queryParams = [];

    if ('' !== $searchTerm) {
        $conditions[] = '(user_fullname LIKE :search OR user_skill LIKE :search)';
        // Escapa % e _ para a busca ser literal
        $queryParams['search'] = '%' . addcslashes($searchTerm, '%_\\') . '%';
    }

    if ('active' === $filterOption) {
        $conditions[] = 'user_status = 1';
    } elseif ('inactive' === $filterOption) {
        $conditions[] = '(user_status IS NULL OR user_status != 1)';
    }

    $WhereClause = [] !== $conditions ? ' AND ' . implode(' AND ', $conditions) : '';

    // Glyphs do flaticon do tema para exibir o ícone de cada funcionário
    require __DIR__ . '/../../_tpl/icon-list.wc.php';
?>

<style>
	@font-face {
		font-family: 'wc-theme-icons';
		src: url('<?php
            echo $IconFonts; ?>.woff') format('woff'), url('<?php
            echo $IconFonts; ?>.ttf') format('truetype');
		font-display: block;
	}

	.single_team .team_glyph {
		margin-right: 6px;
		font-family: 'wc-theme-icons';
		font-style: normal;
		font-size: 1.4em;
		vertical-align: middle;
	}

	.single_team .team_glyph::before {
		content: attr(data-glyph);
	}
</style>

<header class="dashboard_header">
	<div class="dashboard_header_title">
		<h1 class="icon-users">Time</h1>
		<p class="dashboard_header_breadcrumbs">
			&raquo; <?php
                echo ADMIN_NAME; ?>
			<span class="crumb">/</span>
			<a title="<?php
                echo ADMIN_NAME; ?>" href="dashboard.php?wc=home">Dashboard</a>
			<span class="crumb">/</span>
			Time
		</p>
	</div>

	<div class="dashboard_header_search">
		<form name="searchTeam" action="" method="post" enctype="multipart/form-data" class="ajax_off">
			<input type="search" value="<?php
                echo htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8'); ?>" name="s" placeholder="Pesquisar:"
			       style="width: 38%; margin-right: 3px;"/>
			<select name="opt" style="width: 45%; margin-right: 3px; padding: 5px 10px">
				<option value="">Todos</option>
				<option <?php
                    echo 'active' === $filterOption ? "selected='selected'" : ''; ?> value="active">Ativos
				</option>
				<option <?php
                    echo 'inactive' === $filterOption ? "selected='selected'" : ''; ?> value="inactive">Inativos
				</option>
			</select>
			<button class="btn btn_green icon icon-search icon-notext"></button>
		</form>
	</div>
</header>

<div class="dashboard_content">
    <?php
        $Page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT) ?? 1;
        $PagerQuery = http_build_query(array_filter(['s' => $searchTerm, 'opt' => $filterOption]));
        $Pager = new Pager(
            'dashboard.php?wc=team/home' . ('' !== $PagerQuery ? '&' . $PagerQuery : '') . '&page=',
            '<<',
            '>>',
            5
        );
        $Pager->exePager($Page, 12);

        $Read->exeRead(
            DB_TEAM,
            sprintf('WHERE 1 = 1%s ORDER BY user_order ASC, user_fullname ASC LIMIT :limit OFFSET :offset', $WhereClause),
            http_build_query($queryParams + ['limit' => $Pager->getLimit(), 'offset' => $Pager->getOffset()])
        );

        if (!$Read->getResult()) {
            $Pager->returnPage();
            echo Check::erro(
                sprintf(
                    'Ainda não existem funcionários cadastrados %s. Comece agora mesmo cadastrando o primeiro integrante do time!',
                    $Admin['user_name']
                ),
                E_USER_NOTICE
            );
        } else {
            foreach ($Read->getResult() as $Member) {
                $MemberId = (int)$Member['user_id'];
                $MemberName = htmlspecialchars((string)($Member['user_fullname'] ?: 'Novo Funcionário'), ENT_QUOTES, 'UTF-8');
                $MemberSkill = htmlspecialchars((string)($Member['user_skill'] ?: 'Função não informada'), ENT_QUOTES, 'UTF-8');
                $MemberIcon = (string)$Member['user_icon'];
                $MemberIconInfo = (isset($IconList[$MemberIcon])
                    ? "<p class='info'><i class='team_glyph' data-glyph='&#x{$IconList[$MemberIcon]};' aria-hidden='true'></i>{$MemberIcon}</p>"
                    : "<p class='info icon-star-full'>" . htmlspecialchars($MemberIcon ?: 'sem ícone', ENT_QUOTES, 'UTF-8') . '</p>');
                $MemberOrder = (int)$Member['user_order'];
                $MemberImage = '../uploads/' . $Member['user_image'];
                $MemberImage = ($Member['user_image'] && file_exists($MemberImage) && !is_dir($MemberImage)
                    ? 'uploads/' . $Member['user_image']
                    : 'admin/_img/no_avatar.jpg');
                $MemberStatus = (1 != $Member['user_status'] ? ' inactive' : '');

                echo "<article class='single_user single_team box box25 al_center{$MemberStatus}' id='{$MemberId}'>
                    <div class='panel_header default'><h2>{$MemberName}</h2></div>
                    <div class='box_content wc_normalize_height'>
                        <img alt='{$MemberName}' title='{$MemberName}' src='../tim.php?src={$MemberImage}&w=300&h=280'/>
                        <p class='nivel icon-hammer'>{$MemberSkill}</p>
                        {$MemberIconInfo}
                        <p class='info icon-sort-numeric-asc'>Ordem: {$MemberOrder}</p>

                        <div class='single_user_actions'>
                            <a class='btn btn_blue icon-pencil' href='dashboard.php?wc=team/create&id={$MemberId}' title='Editar funcionário'>Editar</a>
                            <span rel='single_team' class='j_delete_action icon-cancel-circle btn btn_red' id='{$MemberId}'>Excluir</span>
                            <span rel='single_team' callback='Team' callback_action='delete' class='j_delete_action_confirm icon-warning btn btn_yellow' style='display: none' id='{$MemberId}'>Remover?</span>
                        </div>
                    </div>
                </article>";
            }

            $Pager->exePaginator(
                DB_TEAM,
                sprintf('WHERE 1 = 1%s', $WhereClause),
                [] !== $queryParams ? http_build_query($queryParams) : null
            );
            echo $Pager->getPaginator();
        }
    ?>
</div>
