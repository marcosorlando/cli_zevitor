<?php

    use App\Models\Newsletter;

    /**
     * Confirmação da newsletter (double opt-in): /newsletter/confirmar/<token>.
     * O GET só mostra o botão; a confirmação acontece no POST, para antivírus e pré-visualizações de e-mail
     * que abrem o link sozinhos não confirmarem a inscrição no lugar da pessoa.
     */
    if (($URL[1] ?? '') !== 'confirmar') {
        require REQUIRE_PATH . '/404.php';

        return;
    }

    $ConfirmToken = (string)($URL[2] ?? '');
    $NewsletterLead = Newsletter::findPendingByToken($ConfirmToken);
    $NewsletterStatus = $NewsletterLead ? 'pendente' : 'invalido';

    if (
        $NewsletterLead
        && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'
        && filter_input(INPUT_POST, 'confirmar') === '1'
    ) {
        $NewsletterStatus = Newsletter::confirm((int)$NewsletterLead['lead_id']) ? 'confirmado' : 'invalido';
    }

    $NewsletterEmail = htmlspecialchars((string)($NewsletterLead['lead_email'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!--Page Header Start-->
<section class='page-header'>
	<div class='page-header__bg'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/page-header-bg.jpg);'>
	</div>
	<div class='container'>
		<div class='page-header__inner'>
			<div class='page-header__img-1'>
				<img src='<?= INCLUDE_PATH ?>/assets/images/resources/page-header-img-1.png' alt=''>
			</div>
			<h3>Newsletter</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='<?= BASE ?>'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Newsletter</li>
				</ul>
			</div>
		</div>
	</div>
</section>
<!--Page Header End-->

<section class='error-page newsletter-confirm'>
	<div class='container'>
		<div class='error-page__inner text-center'>
			<div class='error-page__content'>
                <?php
                    if ($NewsletterStatus === 'confirmado') {
                        ?>
						<h2>Inscrição confirmada!</h2>
						<p>Pronto: <?= $NewsletterEmail ?> vai receber as novidades da Mecânica Zé Vitor.</p>
						<div class='btn-box'>
							<a href='<?= BASE ?>' class='thm-btn'>Voltar ao início<span><i class='icon-next'></i></span></a>
						</div>
                        <?php
                    } elseif ($NewsletterStatus === 'pendente') {
                        ?>
						<h2>Confirme sua inscrição</h2>
						<p>Clique no botão para confirmar que <?= $NewsletterEmail ?> quer receber as novidades da Mecânica Zé Vitor.</p>
						<form method='post' class='btn-box'>
							<input type='hidden' name='confirmar' value='1'>
							<button type='submit' class='thm-btn'>Confirmar inscrição<span><i class='icon-next'></i></span></button>
						</form>
                        <?php
                    } else {
                        ?>
						<h2>Link inválido ou expirado</h2>
						<p>
							Este link de confirmação não vale mais: ele já foi usado ou passou de
                            <?= Newsletter::TOKEN_DAYS ?> dias. Para receber um novo, inscreva-se de novo no
							formulário no fim da página.
						</p>
						<div class='btn-box'>
							<a href='<?= BASE ?>' class='thm-btn'>Voltar ao início<span><i class='icon-next'></i></span></a>
						</div>
                        <?php
                    }
                ?>
			</div>
		</div>
	</div>
</section>
