<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Conn\Update;
    use App\Helpers\Check;
    use App\Models\Pager;

    if (empty($URL[1])) {
        require REQUIRE_PATH . '/404.php';
        return;
    }

    $Read ??= new Read();
    $Search = urldecode($URL[1]);
    $SearchPage = urlencode($Search);
    $SearchEscaped = Check::safeHtmlChars($Search);

    if (empty($_SESSION['search']) || !in_array($Search, $_SESSION['search'], true)) {
        $Read->fullRead(
            'SELECT search_id, search_count FROM ' . DB_SEARCH . ' WHERE search_key = :key',
            http_build_query(['key' => $Search])
        );

        if ($Read->getResult()) {
            $Update = new Update();
            $Update->exeUpdate(
                DB_SEARCH,
                ['search_count' => ((int)$Read->getResult()[0]['search_count']) + 1],
                'WHERE search_id = :id',
                http_build_query(['id' => $Read->getResult()[0]['search_id']])
            );
        } else {
            $Create = new Create();
            $Create->exeCreate(
                DB_SEARCH,
                [
                    'search_key' => $Search,
                    'search_count' => 1,
                    'search_date' => date('Y-m-d H:i:s'),
                    'search_commit' => date('Y-m-d H:i:s'),
                ]
            );
        }

        $_SESSION['search'][] = $Search;
    }
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
			<h3>Blog Mecânica Zé Vitor</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='<?= BASE ?>'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Pesquisa por: <a href="#search" class="wc_goto"><?= $SearchEscaped ?></a></li>
				</ul>
			</div>
		</div>
	</div>
</section>
<!--Page Header End-->

<!--Blog Right Sidebar Start -->
<section id="search" class='blog-right-sidebar'>
	<div class='container'>
		<div class='row'>
			<div class='col-xl-8'>
				<div class='row'>
                    <?php
                        $Page = (!empty($URL[2]) && is_numeric($URL[2]) ? (int)$URL[2] : 1);
                        $Page = ($Page > 0 ? $Page : 1);
                        $Pager = new Pager(BASE . "/pesquisa/{$SearchPage}/", '<', '>', 5);
                        $Pager->exePager($Page, 10);

                        $SearchLike = "%{$Search}%";
                        $SearchTerms = "post_status = 1 AND post_date <= NOW() AND (post_title LIKE :title OR post_subtitle LIKE :subtitle OR post_tags LIKE :tags OR MONTH(post_date) = :month)";
                        $SearchParams = [
                            'title' => $SearchLike,
                            'subtitle' => $SearchLike,
                            'tags' => $SearchLike,
                            'month' => $Search,
                        ];

                        $Read->fullRead(
                            'SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, p.post_author, p.post_views, u.user_name, u.user_lastname, u.user_thumb FROM ' . DB_POSTS . ' p, ' . DB_USERS . " u WHERE {$SearchTerms} AND post_author = user_id ORDER BY post_date DESC LIMIT :limit OFFSET :offset",
                            http_build_query(
                                ['limit' => $Pager->getLimit(), 'offset' => $Pager->getOffset()] + $SearchParams
                            )
                        );

                        if (!$Read->getResult()) {
                            $Pager->returnPage();
                            echo Check::erro(
                                "Não encontramos conteúdo para a palavra-chave <b>( {$SearchEscaped} )</b>. Confira os últimos artigos:",
                                E_USER_NOTICE
                            );

                            $Read->fullRead(
                                'SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, p.post_author, p.post_views, u.user_name, u.user_lastname, u.user_thumb FROM ' . DB_POSTS . ' p, ' . DB_USERS . ' u WHERE post_status = 1 AND post_date <= NOW() AND post_author = user_id ORDER BY post_date DESC LIMIT :limit',
                                'limit=4'
                            );
                        }

                        foreach ($Read->getResult() ?: [] as $Post) {
                            extract($Post);
                            $authorName = "{$user_name} {$user_lastname}";
                            require REQUIRE_PATH . '/inc/post.php';
                        }
                    ?>

					<!--Blog List Pagination-->
					<div class='blog-list__pagination'>
                        <?php
                            $Pager->exePaginator(DB_POSTS, "WHERE {$SearchTerms}", http_build_query($SearchParams));
                            echo $Pager->getPaginator();
                        ?>
					</div>
				</div>
			</div>

            <?php
                require_once REQUIRE_PATH . '/inc/sidebar-blog.php' ?>
		</div>
	</div>
</section>
<!--Blog Right Sidebar End-->
<script>
    // Após carregar tudo (imagens incluídas), rola suave até os resultados.
    // Espera o fade do #preloader (script.js: delay 200 + fadeOut slow ≈ 800ms);
    // o desconto de 110px é o mesmo do .wc_goto, para o header fixo não cobrir o topo.
    window.addEventListener('load', function () {
        const target = document.getElementById('search');
        if (!target) {
            return;
        }

        setTimeout(function () {
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({
                top: target.getBoundingClientRect().top + window.scrollY - 10,
                behavior: reduceMotion ? 'auto' : 'smooth'
            });
        }, 800);
    });
</script>
