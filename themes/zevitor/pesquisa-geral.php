<?php

    use App\Conn\Create;
    use App\Conn\Read;
    use App\Conn\Update;
    use App\Helpers\Check;
    use App\Models\Pager;

    /**
     * Pesquisa geral (popup do menu principal) — tema Zevitor. Rota: /pesquisa-geral/{termo}/{página}
     * Busca em DB_SERVICES, DB_FAQS e DB_POSTS, nessa ordem.
     * Visual: services-page (servicos.php) + acordeão do inc/faq-one.php + blog-page (blog-2.html, sem sidebar).
     */
    if (empty($URL[1])) {
        require REQUIRE_PATH . '/404.php';
        return;
    }

    $Read ??= new Read();
    $Search = urldecode($URL[1]);
    $SearchPage = urlencode($Search);
    $SearchEscaped = Check::safeHtmlChars($Search);
    $SearchLike = "%{$Search}%";

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

    $Page = (!empty($URL[2]) && is_numeric($URL[2]) ? (int)$URL[2] : 1);
    $Page = ($Page > 0 ? $Page : 1);

    // SERVIÇOS: só na primeira página (a paginação é do blog)
    $Services = [];
    if (1 === $Page) {
        $Read->fullRead(
            'SELECT svc_name, svc_title, svc_subtitle, svc_description, svc_icon, svc_cover FROM ' . DB_SERVICES
            . ' WHERE svc_status = 1 AND (svc_title LIKE :title OR svc_subtitle LIKE :subtitle OR svc_description LIKE :description)'
            . ' ORDER BY svc_title ASC',
            http_build_query(['title' => $SearchLike, 'subtitle' => $SearchLike, 'description' => $SearchLike])
        );
        $Services = $Read->getResult() ?: [];
    }

    // FAQ: também só na primeira página
    $Faqs = [];
    if (1 === $Page) {
        $Read->fullRead(
            'SELECT faq_question, faq_answer FROM ' . DB_FAQS
            . ' WHERE faq_status = 1 AND (faq_question LIKE :question OR faq_answer LIKE :answer)'
            . ' ORDER BY faq_order IS NULL, faq_order ASC, faq_question ASC',
            http_build_query(['question' => $SearchLike, 'answer' => $SearchLike])
        );
        $Faqs = $Read->getResult() ?: [];
    }

    // BLOG
    $Pager = new Pager(BASE . "/pesquisa-geral/{$SearchPage}/", '<', '>', 5);
    $Pager->exePager($Page, 9);

    $PostTerms = "post_status = 1 AND post_date <= NOW() AND (post_title LIKE :title OR post_subtitle LIKE :subtitle OR post_tags LIKE :tags OR MONTH(post_date) = :month)";
    $PostParams = [
        'title' => $SearchLike,
        'subtitle' => $SearchLike,
        'tags' => $SearchLike,
        'month' => $Search,
    ];

    $Read->fullRead(
        'SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, p.post_author, p.post_views, u.user_name, u.user_lastname, u.user_thumb FROM ' . DB_POSTS . ' p, ' . DB_USERS . " u WHERE {$PostTerms} AND post_author = user_id ORDER BY post_date DESC LIMIT :limit OFFSET :offset",
        http_build_query(['limit' => $Pager->getLimit(), 'offset' => $Pager->getOffset()] + $PostParams)
    );
    $Posts = $Read->getResult() ?: [];

    if (!$Posts && $Page > 1) {
        $Pager->returnPage();
    }

    // NADA ENCONTRADO: sugere os últimos artigos
    $NotFound = !$Services && !$Faqs && !$Posts;
    if ($NotFound) {
        $Read->fullRead(
            'SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, p.post_author, p.post_views, u.user_name, u.user_lastname, u.user_thumb FROM ' . DB_POSTS . ' p, ' . DB_USERS . ' u WHERE post_status = 1 AND post_date <= NOW() AND post_author = user_id ORDER BY post_date DESC LIMIT :limit',
            'limit=3'
        );
        $Posts = $Read->getResult() ?: [];
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
			<h3>Pesquisa</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='<?= BASE ?>'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Pesquisa por: <a href='#search' class='wc_goto'><?= $SearchEscaped ?></a></li>
				</ul>
			</div>
		</div>
	</div>
</section>
<!--Page Header End-->

<div id='search'>
    <?php
        if ($Services) { ?>
		<!--Service Page Start-->
		<section class='services-page'>
			<div class='container'>
				<div class='section-title text-center'>
					<h3 class='section-title__title'>Serviços para <span><?= $SearchEscaped ?></span></h3>
				</div>
				<div class='row'>
                    <?php
                        foreach ($Services as $Count => $Service) {
                            extract($Service);
                            $Num = str_pad((string)($Count + 1), 2, '0', STR_PAD_LEFT);
                            $Cover = !empty($svc_cover)
                                ? BASE . '/tim.php?src=uploads/' . $svc_cover . '&w=600&h=450'
                                : INCLUDE_PATH . '/assets/images/services/services-1-1.jpg';
                            $Icon = !empty($svc_icon) ? $svc_icon : 'icon-mechanical';
                            $Link = BASE . '/servico/' . $svc_name;
                            $Text = !empty($svc_subtitle) ? $svc_subtitle : mb_strimwidth(
                                strip_tags((string)$svc_description),
                                0,
                                120,
                                '…'
                            );
                            ?>
						<!--Services One Single Start -->
						<div class='col-xl-4 col-lg-4 col-md-6'>
							<div class='services-one__single'>
								<div class='services-one__img-box'>
									<div class='services-one__img'>
										<img src='<?= $Cover ?>' alt='<?= Check::safeHtmlChars($svc_title) ?>'>
									</div>
									<div class='services-one__count'><?= $Num ?></div>
								</div>
								<div class='services-one__content'>
									<div class='services-one__icon'>
										<span class='<?= $Icon ?>'></span>
									</div>
									<h3 class='services-one__title'>
										<a href='<?= $Link ?>'><?= Check::safeHtmlChars($svc_title) ?></a>
									</h3>
									<p class='services-one__text'><?= Check::safeHtmlChars($Text) ?></p>
									<div class='services-one__btn-box'>
										<a href='<?= $Link ?>'>Leia mais<span class='icon-next'></span></a>
									</div>
								</div>
							</div>
						</div>
						<!--Services One Single End -->
                        <?php
                        }
                    ?>
				</div>
			</div>
		</section>
		<!--Service Page End-->
        <?php
        } ?>

    <?php
        if ($Faqs) { ?>
		<!--Faq Start -->
		<section class='faq-one'>
			<div class='container'>
				<div class='section-title text-center'>
					<h3 class='section-title__title'>Perguntas sobre <span><?= $SearchEscaped ?></span></h3>
				</div>
				<div class='row justify-content-center'>
					<div class='col-xl-10'>
						<div class='faq-one__left'>
							<div class='accrodion-grp' data-grp-name='faq-one-accrodion'>
                                <?php
                                    foreach ($Faqs as $Key => $Faq) { ?>
									<div class='accrodion<?= 0 === $Key ? ' active' : '' ?>'>
										<div class='accrodion-title'>
											<h4><?= Check::safeHtmlChars($Faq['faq_question']) ?></h4>
										</div>
										<div class='accrodion-content'>
											<div class='inner'>
                                                <?= $Faq['faq_answer'] ?>
											</div><!-- /.inner -->
										</div>
									</div>
                                    <?php
                                    } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!--Faq End -->
        <?php
        } ?>

    <?php
        if ($Posts || $NotFound) { ?>
	<!--Blog Page Start -->
	<section class='blog-page'>
		<div class='container'>
            <?php
                if ($NotFound) {
                    echo Check::erro(
                        "Não encontramos serviços, perguntas frequentes nem artigos para a palavra-chave <b>( {$SearchEscaped} )</b>. Confira os últimos artigos:",
                        E_USER_NOTICE
                    );
                } elseif ($Posts) { ?>
				<div class='section-title text-center'>
					<h3 class='section-title__title'>Artigos sobre <span><?= $SearchEscaped ?></span></h3>
				</div>
                <?php
                } ?>
			<div class='row'>
                <?php
                    $postColumn = 'col-xl-4 col-lg-6 col-md-6';
                    foreach ($Posts as $Post) {
                        extract($Post);
                        $authorName = "{$user_name} {$user_lastname}";
                        require REQUIRE_PATH . '/inc/post.php';
                    }
                    unset($postColumn);

                    if (!$NotFound) { ?>
					<!--Blog List Pagination-->
					<div class='blog-list__pagination'>
                        <?php
                            $Pager->exePaginator(DB_POSTS, "WHERE {$PostTerms}", http_build_query($PostParams));
                            echo $Pager->getPaginator();
                        ?>
					</div>
                    <?php
                    } ?>
			</div>
		</div>
	</section>
	<!--Blog Page End -->
    <?php
        } ?>
</div>

<script>
    // Após carregar tudo (imagens incluídas), rola suave até os resultados.
    // Espera o fade do #preloader (script.js: delay 200 + fadeOut slow ≈ 800ms).
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
