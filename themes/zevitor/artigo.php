<?php

    use App\Conn\Read;
    use App\Conn\Update;

    $Read ??= new Read;

    if (empty($URL[1])) {
        require REQUIRE_PATH . '/404.php';
        return;
    }

    $Read->exeRead(DB_POSTS, "WHERE post_name = :nm", "nm={$URL[1]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        $Post = $Read->getResult()[0];
        extract($Post);
        $Update = new Update;
        $UpdateView = ['post_views' => (int)($post_views ?? 0) + 1, 'post_lastview' => date('Y-m-d H:i:s')];
        $Update->exeUpdate(DB_POSTS, $UpdateView, "WHERE post_id = :id", "id={$post_id}");

        $Read->fullRead(
            "SELECT category_title, category_name FROM " . DB_CATEGORIES . " WHERE category_id = :id",
            "id={$post_category}"
        );
        $PostCategory = ($Read->getResult() ? $Read->getResult()[0] : ['category_title' => '', 'category_name' => '']);

        $Read->fullRead(
            "SELECT user_name, user_lastname, user_thumb, user_genre,user_twitter, user_youtube, user_google, user_description FROM " . DB_USERS . " WHERE user_id = :user",
            "user={$post_author}"
        );
        $Author = ($Read->getResult() ? $Read->getResult()[0] : []);
        $AuthorName = trim(($Author['user_name'] ?? '') . ' ' . ($Author['user_lastname'] ?? ''));
    }
    extract($Author);
?>

<!--Page Header Start-->
<section class='page-header'>
	<div class='page-header__bg' style='background-image: url(assets/images/backgrounds/page-header-bg.jpg);'>
	</div>
	<div class='container'>
		<div class='page-header__inner'>
			<div class='page-header__img-1'>
				<img src='assets/images/resources/page-header-img-1.png' alt=''>
			</div>
			<h3>Detalhes do blog à direita</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='index.html'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Detalhes do blog à direita</li>
				</ul>
			</div>
		</div>
	</div>
</section>
<!--Page Header End-->

<!--Blog Details Start -->
<section class='blog-details blog-details__left-sidebar'>
	<div class='container'>
		<div class='row'>
			<div class='col-xl-8 col-lg-7'>
				<div class='blog-details__left'>
					<div class='blog-details__img-box-1'>
						<div class='blog-details__img'>
							<img src='assets/images/blog/blog-details-img-1.jpg' alt=''>
						</div>
						<div class='blog-details__date'>
							<p>12<br><span>Nov</span></p>
						</div>
					</div>
					<div class='blog-details__content'>
						<div class='blog-details__user-and-meta'>
							<div class='blog-details__user'>
								<p><span class='icon-user-1'></span>Por administrador</p>
							</div>
							<ul class='blog-details__meta list-unstyled'>
								<li>
									<a href='#'><span class='fas fa-comments'></span>Comentários (05)</a>
								</li>
								<li>
									<a href='#'><span class='fas fa-clock'></span>4 min de leitura</a>
								</li>
							</ul>
						</div>
						<h3 class='blog-details__title'>Por que a manutenção regular do carro é essencial.</h3>
						<p class='blog-details__text-1'>Um carro é uma parte essencial de nossas vidas diárias.
							A manutenção regular não só melhora o desempenho, mas também garante a segurança no
							estrada. Muitos motoristas atrasam ou ignoram a manutenção, pensando que é desnecessário,
							mas isso
							muitas vezes leva a problemas maiores e mais caros no futuro. Manutenção regular do carro
							é crucial para manter seu veículo nas melhores condições. Isso não apenas torna o seu
							dirige mais suavemente, mas também garante segurança.</p>
						<p class='blog-details__text-2'>Um carro é uma parte essencial de nossas vidas diárias.
							A manutenção regular não só melhora o desempenho, mas também garante a segurança no
							estrada. Muitos motoristas atrasam ou ignoram a manutenção, pensando que é desnecessário,
							mas isso
							muitas vezes leva a problemas maiores e mais caros no futuro.</p>
						<div class='blog-details__author-box'>
							<h4 class='blog-details__author-text'>“O brilho, a determinação da sua equipe,
								e
								a confiança o levará a conquistar novas fronteiras; a grandeza está dentro
								você.
								a grandeza está na motivação O brilho, a determinação e a determinação da sua equipe
								confiança
								irá levá-lo a conquistar novas fronteiras; a grandeza está dentro de você”</h4>
							<p class='blog-details__author-name'>Kane Williamson<span> / CEO</span></p>
						</div>
						<h3 class='blog-details__title-2'>A manutenção oportuna prolonga a vida útil do seu carro.
						</h3>
						<p class='blog-details__text-3'>Fora enigma ad minim veniam, quis nostrud
							exercício
							ullamco laboris nisi ut aliquip ex ea comodo consequat. Duis aute inure dor
							em
							o reprehenderit in voluptate velit esse cillum dolore eu fugiat null pariatur.
							Excepteur snit occaecat cupidatat non proident, sunt in culpa qui officia
							desertor
							mollit anim id est laborum.</p>
						<div class='blog-details__img-box'>
							<div class='row'>
								<div class='col-xl-6'>
									<div class='blog-details__img-box-img'>
										<img src='assets/images/blog/blog-details-img-box-img-1.jpg' alt=''>
									</div>
								</div>
								<div class='col-xl-6'>
									<div class='blog-details__img-box-img'>
										<img src='assets/images/blog/blog-details-img-box-img-2.jpg' alt=''>
									</div>
								</div>
							</div>
						</div>
						<div class='blog-details__tag-and-share'>
							<div class='blog-details__tag'>
								<h3 class='blog-details__tag-title'>Etiquetas:</h3>
								<ul class='blog-details__tag-list list-unstyled'>
									<li>
										<a href='#'>#Serviço</a>
									</li>
									<li>
										<a href='#'>#Reparar</a>
									</li>
								</ul>
							</div>
							<div class='blog-details__share-box'>
								<h3 class='blog-details__share-title'>Compartilhar :</h3>
								<div class='blog-details__share'>
									<a href='#'><span class='icon-facebook-app-symbol'></span></a>
									<a href='#'><span class='icon-twitter'></span></a>
									<a href='#'><span class='icon-instagram'></span></a>
									<a href='#'><span class='icon-pinterest'></span></a>
								</div>
							</div>
						</div>
						<div class='comment-one'>
							<div class='comment-one__single'>
								<div class='comment-one__image'>
									<img src='assets/images/blog/comment-1-1.jpg' alt=''>
								</div>
								<div class='comment-one__content'>
									<h3>Teresa Webb</h3>
									<span>02 de junho de 2024 às 15h30</span>
									<p>O homem sábio, portanto, sempre se mantém nestas questões
										princípio de
										seleção. Ele rejeita prazeres para garantir outros prazeres maiores,
										ou
										caso contrário, ele suporta esforços para evitar dores piores até o ponto de
										seleção.
										Mas
										em certeza para todas essas circunstâncias</p>
									<div class='comment-one__btn-box'>
										<a href='blog-details.html' class='comment-one__btn'>Responder</a>
									</div>
								</div>
							</div>
							<div class='comment-one__single'>
								<div class='comment-one__image'>
									<img src='assets/images/blog/comment-1-2.jpg' alt=''>
								</div>
								<div class='comment-one__content'>
									<h3>Cameron Williamson</h3>
									<span>02 de junho de 2024 às 15h30</span>
									<p>O homem sábio, portanto, sempre se mantém nestas questões
										princípio de
										seleção. Ele rejeita prazeres para garantir outros prazeres maiores,
										ou
										caso contrário, ele suporta esforços para evitar dores piores até o ponto de
										seleção.
										Mas
										em certeza para todas essas circunstâncias</p>
									<div class='comment-one__btn-box'>
										<a href='blog-details.html' class='comment-one__btn'>Responder</a>
									</div>
								</div>
							</div>
						</div>
						<div class='comment-form'>
							<h3 class='comment-form__title'>Deixe uma resposta</h3>
							<p class='comment-form__text'>Ao usar o formulário você concorda com a mensagem enviada,
								você
								pode
								entre em contato conosco diretamente agora</p>
							<form action='assets/inc/sendemail.php' class='comment-one__form contact-form-validated'
							      novalidate='novalidate'>
								<div class='row'>
									<div class='col-xl-6'>
										<div class='comment-form__input-box'>
											<input type='text' placeholder='Seu nome' name='name'>
										</div>
									</div>
									<div class='col-xl-6'>
										<div class='comment-form__input-box'>
											<input type='email' placeholder='Seu e-mail' name='email'>
										</div>
									</div>
								</div>
								<div class='row'>
									<div class='col-xl-12'>
										<div class='comment-form__input-box text-message-box'>
											<textarea name='message' placeholder='Escreva sua mensagem'></textarea>
										</div>
										<div class='comment-form__btn-box'>
											<button type='submit' class='footer-widget__newsletter-btn thm-btn'>Enviar
												agora<span class='icon-next'></span>
											</button>
										</div>
									</div>
								</div>
							</form>
							<div class='result'></div>
						</div>
					</div>
				</div>
			</div>
            <?php
                require_once REQUIRE_PATH . '/inc/sidebar-blog.php'
            ?>
		</div>
	</div>
</section>
<!--Blog Details Start-->
