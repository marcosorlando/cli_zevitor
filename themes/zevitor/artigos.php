<?php

    use App\Conn\Read;
    use App\Helpers\Check;
    use App\Models\Pager;

    $Read ??= new Read;

    if (empty($URL[1])) {
        require REQUIRE_PATH . '/404.php';
        return;
    }

    $Read->exeRead(DB_CATEGORIES, "WHERE category_name = :nm", "nm={$URL[1]}");
    if (!$Read->getResult()) {
        require REQUIRE_PATH . '/404.php';
        return;
    } else {
        extract($Read->getResult()[0]);
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
					<li><a href='index.html'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Sessão: <?= $category_title ?>  </li>
				</ul>
			</div>
		</div>
	</div>
</section>
<!--Page Header End-->

<!--Blog Right Sidebar Start -->
<section class='blog-right-sidebar'>
	<div class='container'>
		<div class='row'>
			<div class='col-xl-8'>
				<div class='row'>


                    <?php
                        $Page = (!empty($URL[2]) && is_numeric($URL[2]) ? (int)$URL[2] : 1);
                        $Page = ($Page > 0 ? $Page : 1);
                        $Pager = new Pager(BASE . "/artigos/{$category_name}/", '<', '>', 5);
                        $Pager->exePager($Page, 10);

                        $Read->fullRead(
                            'SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, p.post_author, p.post_views, u.user_name, u
    .user_lastname, u.user_thumb FROM ' . DB_POSTS . ' p, ' . DB_USERS . ' u WHERE post_status = 1 AND post_date <= NOW() AND (post_category = :ct OR FIND_IN_SET(:ct, post_category_parent)) AND post_author = user_id ORDER BY post_date DESC LIMIT :limit OFFSET :offset',
                            "limit={$Pager->getLimit()}&offset={$Pager->getOffset()}&ct={$category_id}"
                        );

                        if (!$Read->getResult()) {
                            $Pager->returnPage();
                            echo Check::erro(
                                'Ainda não existem artigos cadastrados nesta categoria. Favor volte mais tarde.',
                                E_USER_NOTICE
                            );
                        } else {
                            $delay = 0;
                            foreach ($Read->getResult() as $Post) {
                                extract($Post);
                                $delay += 100;
                                $authorName = "{$user_name} {$user_lastname}";
                                require REQUIRE_PATH . '/inc/post.php';
                            }
                        }


                    ?>

					<!--Blog List Pagination-->
					<div class='blog-list__pagination'>
                        <?php
                            $Pager->exePaginator(
                                DB_POSTS,
                                'WHERE post_status = 1 AND post_date <= NOW() AND (post_category = :ct OR FIND_IN_SET(:ct, post_category_parent))',
                                "ct={$category_id}"
                            );
                            echo $Pager->getPaginator();
                        ?>

						<!--<ul class='pg-pagination list-unstyled'>
							<li class='count active'><a href='#'>1</a></li>
							<li class='count'><a href='#'>2</a></li>
							<li class='count'><a href='#'>3</a></li>
							<li class='next'>
								<a href='#' aria-label='Próximo'><i class='fas fa-angle-right'></i></a>
							</li>
						</ul>-->
					</div>
				</div>
			</div>

            <?php
                require_once REQUIRE_PATH . '/inc/sidebar-blog.php' ?>
		</div>
	</div>
</section>
<!--Blog Right Sidebar End-->
