<?php

    use App\Conn\Delete;
    use App\Conn\Read;
    use App\Helpers\Check;

    if (!APP_LEADS || empty($DashboardLogin) || empty($Admin) || $Admin['user_level'] < LEVEL_WC_LEADS) {
        Check::accessBlocked();
    }

    // AUTO DELETE POST TRASH
    if (DB_AUTO_TRASH !== 0) {
        $Delete = new Delete();
        $Delete->exeDelete(DB_LEADS, 'WHERE lead_name IS NULL AND lead_email IS NULL and lead_status = :st', 'st=1');
    }
?>
<div class="box box100">
	<header class="header_pink">
		<h1 class="icon-books">BASE DE LEADS:
			<a class="icon-download btn btn_yellow fl_right" style="margin-top: -5px;"
			   href="_siswc/leads/export.php"
			   title="Clique para Salvar em Excel">Download</a>
			<a class="icon-user-plus btn btn_green fl_right" style="margin-top: -5px; margin-right: 5px"
			   href="dashboard.php?wc=leads/create" title="Novo Lead">Inserir Lead</a>
		</h1>
	</header>
	<div class="panel dashboard_search">

		<article class="lead_header">
			<p>NOME</p>
			<p>DATA</p>
			<p>E-MAIL</p>
			<p>ID CONVERSÃO</p>
			<p>

			</p>
		</article>
        <?php
            $Read ??= new Read();
            $Read->exeRead(DB_LEADS, 'ORDER BY lead_date DESC');
            if (!$Read->getResult()) {
                echo Check::erro('<span>Ainda não existem Leads cadastrados!</span>', E_USER_NOTICE);
                echo "<div class='clear'></div>";
            } else {
                foreach ($Read->getResult() as $Lead) {
                    extract($Lead);
                    $lead_name = $lead_name ?: $lead_email;
                    if ($lead_conversion === 'Newsletter') {
                        $lead_conversion .= $lead_confirmed ? ' (confirmada)' : ' (aguardando confirmação)';
                    }
                    // Nome e e-mail vêm dos formulários públicos: sempre escapar.
                    $lead_id = (int)$lead_id;
                    $lead_name = htmlspecialchars((string)$lead_name, ENT_QUOTES, 'UTF-8');
                    $lead_email = htmlspecialchars((string)$lead_email, ENT_QUOTES, 'UTF-8');
                    $lead_conversion = htmlspecialchars((string)$lead_conversion, ENT_QUOTES, 'UTF-8');
                    echo " <article class='single_lead' id='{$lead_id}'>
                    <h1 class='icon-eye'><a href='#' title='Ver detalhes'>{$lead_name}</a></h1>
                      <p>DIA " . date('d/m/Y H\hi', strtotime((string)$lead_date)) . "</p>
                        <p><a href='mailto:{$lead_email}' title='Enviar e-mail para esse lead'>{$lead_email}</a></p>
                               <p>{$lead_conversion}</p>
                               <p>                            
                                    <a href='dashboard.php?wc=leads/create&id={$lead_id}' class='btn btn_blue icon-notext icon-pen wc_tooltip'>
                                      <span class='wc_tooltip_balloon'>Editar</span></button>
                                      </a>
                                    <span rel='single_lead' class='j_delete_action btn btn_red icon-notext icon-cross wc_tooltip' id='{$lead_id}'><span class='wc_tooltip_balloon'>Deletar</span></span>
                                    <span rel='single_lead' callback='Leads' callback_action='delete' class='j_delete_action_confirm btn btn_yellow icon-warning' style='display: none' id='{$lead_id}'>Remover lead?</span>
                                </p>
                            </article>
                        ";
                }
            }
        ?>
		<!--<a class="dashboard_searchnowlink" href="dashboard.php?wc=searchnow" title="Ver Mais">MAIS PESQUISAS!</a>-->
		<div class="clear"></div>
	</div>
</div>
