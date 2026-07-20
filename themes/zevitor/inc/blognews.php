<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    /**
     * Blog (home) — tema Zevitor.
     * Dinâmico: lê DB_POSTS (ws_posts) publicados, com autor (DB_USERS) e categoria (DB_CATEGORIES).
     * Visual: bloco .blog-one do template Servixa (3 cards).
     * Capa: BASE/tim.php?src=uploads/<post_cover>. Link: BASE/artigo/<post_name>.
     */
    $Read ??= new Read();

    $Read->fullRead(
        'SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, '
        . 'u.user_name, c.category_title '
        . 'FROM ' . DB_POSTS . ' p '
        . 'LEFT JOIN ' . DB_USERS . ' u ON p.post_author = u.user_id '
        . 'LEFT JOIN ' . DB_CATEGORIES . ' c ON p.post_category = c.category_id '
        . 'WHERE p.post_status = 1 AND p.post_date <= NOW() ORDER BY p.post_date DESC LIMIT :limit',
        'limit=3'
    );

    $Meses = [
        1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr', 5 => 'Mai', 6 => 'Jun',
        7 => 'Jul', 8 => 'Ago', 9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez',
    ];
    $Anim = ['fadeInLeft', 'fadeInUp', 'fadeInRight'];
?>
<!--Blog One Start-->
<section class='blog-one'>
	<div class='container'>
		<div class='section-title text-center sec-title-animation animation-style1'>
			<div class='section-title__tagline-box'>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-1'>
						<i class='section-title__circle'></i>
					</div>
				</div>
				<h6 class='section-title__tagline'>Nosso blog</h6>
				<div class='section-title__tagline-border'>
					<div class='section-title__shape-2'>
						<i class='section-title__circle'></i>
					</div>
				</div>
			</div>
			<h3 class='section-title__title title-animation'>Dicas e cuidados para<br> o seu <span>carro</span>
			</h3>
		</div>
		<div class='row'>
            <?php
                if (!$Read->getResult()) {
                    echo Check::erro(
                        'Os artigos ainda serão publicados no painel. Volte em breve :)',
                        E_USER_NOTICE
                    );
                } else {
                    $i = 0;
                    foreach ($Read->getResult() as $Post) {
                        extract($Post);
                        $Capa = !empty($post_cover)
                            ? BASE . '/tim.php?src=uploads/' . $post_cover . '&w=420&h=300'
                            : INCLUDE_PATH . '/assets/images/blog/blog-1-1.jpg';
                        $Link = BASE . '/artigo/' . $post_name;
                        $Dia = date('d', strtotime($post_date));
                        $Mes = $Meses[(int) date('n', strtotime($post_date))];
                        $Tag = !empty($category_title) ? $category_title : 'Blog';
                        $Autor = !empty($user_name) ? $user_name : 'Mecânica Zé Vitor';
                        $Wow = $Anim[$i % 3];
                        $i++;
            ?>
			<!--Blog One Single Start-->
			<div class='col-xl-4 col-lg-4 col-md-6 wow <?= $Wow ?>' data-wow-delay='<?= ($i * 100) ?>ms'>
				<div class='blog-one__single'>
					<div class='blog-one__single-inner'>
						<div class='blog-one__img-box'>
							<div class='blog-one__img'>
								<img src='<?= $Capa ?>' alt='<?= Check::safeHtmlChars($post_title) ?>'>
								<div class='blog-one__tags'>
									<span><?= Check::safeHtmlChars($Tag) ?></span>
								</div>
							</div>
							<div class='blog-one__date'>
								<p><?= $Dia ?> <span><?= $Mes ?></span></p>
							</div>
						</div>
						<div class='blog-one__content'>
							<ul class='blog-one__meta list-unstyled'>
								<li>
									<a href='<?= $Link ?>'>
										<span class='fas fa-user'></span><?= Check::safeHtmlChars($Autor) ?>
									</a>
								</li>
							</ul>
							<h3 class='blog-one__title'><a href='<?= $Link ?>'><?= Check::safeHtmlChars($post_title) ?></a></h3>
							<p class='blog-one__text'><?= Check::safeHtmlChars((string) $post_subtitle) ?></p>
						</div>
					</div>
					<div class='blog-one__read-more-box'>
						<a href='<?= $Link ?>' class='blog-one__read-more'>Leia mais<span
									class='fas fa-arrow-right'></span></a>
					</div>
				</div>
			</div>
			<!--Blog One Single End-->
            <?php
                    }
                }
            ?>
		</div>
	</div>
</section>
<!--Blog One End-->
