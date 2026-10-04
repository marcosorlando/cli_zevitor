<?php

    use App\Conn\Read;
    use App\Helpers\Check;

?>

<!--Start Sidebar-->
<div class='col-xl-4'>
	<div class='sidebar sidebar--three'>
		<!--Start Sidebar Single-->
		<div class='sidebar__single sidebar__search wow fadeInUp' data-wow-delay='.1s'>
			<form action='#' name="search_blog" class='sidebar__search-form'>
				<input type='search' name="s" placeholder='Pesquisar...'>
				<button type='submit'><i class='fa fa-search'></i></button>
			</form>
		</div>
		<!--End Sidebar Single-->

		<!--Start Sidebar Single-->
		<div class='sidebar__single sidebar__category wow fadeInUp' data-wow-delay='.1s'>
			<h3 class='sidebar__title'>Categorias</h3>

            <?php
                $Read ??= new Read();

                $Read->exeRead(
                    DB_CATEGORIES,
                    " WHERE category_parent IS NULL AND category_id IN(SELECT post_category FROM " . DB_POSTS . " WHERE post_status != 0 AND post_date <= NOW()) ORDER BY category_title ASC "
                );


                if (!$Read->getResult()) {
                    echo Check::erro('Ainda não existem categorias cadastradas!', E_USER_NOTICE);
                } else {
                    $blogId = (int)$Read->getResult()[0]['category_id'];

                    ?>

					<ul class='sidebar__category-list list-unstyled'>

                        <?php
                            $Read->fullRead(
                                'SELECT c.category_id, c.category_title, c.category_name, COUNT(DISTINCT p.post_id) AS category_posts, p.post_tags '
                                . 'FROM ' . DB_CATEGORIES . ' c '
                                . 'INNER JOIN ' . DB_POSTS . ' p ON (p.post_category = c.category_id OR FIND_IN_SET(c.category_id, p.post_category_parent)) '
                                . 'WHERE c.category_parent = :pr '
                                . 'AND p.post_status = 1 '
                                . 'AND p.post_date <= NOW() '
                                . 'GROUP BY c.category_id, c.category_title, c.category_name '
                                . 'ORDER BY c.category_title ASC',
                                "pr={$blogId}"
                            );
                            if ($Read->getResult()) {
                                $tags = '';

                                foreach ($Read->getResult() as $cat) {
                                    $tags .= $cat['post_tags'] . ', ';
                                    $categoryPosts = (int)($cat['category_posts'] ?? 0);
                                    ?>

									<li class='active_'>
										<a href='<?= BASE ?>/artigos/<?= Check::safeHtmlChars(
                                            $cat['category_name']
                                        ) ?>'><?= Check::safeHtmlChars($cat['category_title']) ?>
											<span>(<?= $categoryPosts ?>)</span></a></li>

                                    <?php
                                }
                            }
                        ?>
					</ul>

                    <?php
                }
            ?>


		</div>
		<!--End Sidebar Single-->

		<!--Start Sidebar Single-->
		<div class='sidebar__single sidebar__post wow fadeInUp' data-wow-delay='.1s'>
			<h3 class='sidebar__title'>Postagem recente</h3>
			<div class='sidebar__post-box'>
                <?php
                    $Read->fullRead(
                        "SELECT * FROM " . DB_POSTS . " WHERE post_status != 0 ORDER BY post_date DESC LIMIT 4"
                    );
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $post) {
                            ?>
							<div class='sidebar__post-single'>
								<div class='sidebar-post__img'>
									<img src='assets/images/blog/recent-post-img-1.jpg'
									     alt='<?= $post['post_title'] ?>'>
								</div>
								<div class='sidebar__post-content-box'>
									<h3><a href='blog-details.html'><?= $post['post_title'] ?></a>
									</h3>
								</div>
							</div>
                            <?php
                        }
                    }
                ?>
			</div>
		</div>
		<!--End Sidebar Single-->

		<!--Start Sidebar Single-->
		<div class='sidebar__single sidebar__contact'>
			<div class='sidebar__contact-bg'
			     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/services/sidebar-contact-bg.jpg);'></div>
			<div class='sidebar__contact-icon'>
				<span class='fab fa-whatsapp'></span>
			</div>
			<div class='sidebar__contact-text'>
				<p>Chame no Whatsapp qualquer hora</p>
				<h2><a href='<?= Check::whatsMessage(
                        SITE_ADDR_WHATS,
                        'Olá, estou no site e quero falar com atendente.'
                    ) ?>'><?= SITE_ADDR_WHATS ?></a></h2>
			</div>
			<div class='sidebar__contact-btn'>
				<a class='thm-btn' title="Chamar no Whats o atendimento" target="_blank" href='<?= Check::whatsMessage(
                    SITE_ADDR_WHATS,
                    "Olá, estou no site e quero falar com atendente."
                ) ?>'>Contate-nos
					<span class='icon-next'></span>
				</a>
			</div>
		</div>
		<!--End Sidebar Single-->

		<!--Start Sidebar Single-->

        <?php
            if ($tags) {
                $tags = explode(',', $tags);
                $tags = array_values(
                    array_unique(
                        array_filter(
                            array_map(static fn($item) => is_string($item) ? trim($item) : $item, $tags),
                            static fn($item) => $item !== null && $item !== '' && $item !== []
                        ),
                        SORT_REGULAR
                    )
                );
                ?>

				<div class='sidebar__single sidebar__tags wow fadeInUp' data-wow-delay='.1s'>
					<h3 class='sidebar__title'>Nuvem de tags</h3>
					<ul class='sidebar__tags-list clearfix list-unstyled'>

                        <?php
                            foreach ($tags as $tag) {
                                $searchWord = trim(Check::getCapilalize($tag));
                                $searchSlug = Check::name($searchWord);
                                ?>

								<li><a href='<?= BASE ?>/pesquisa/<?= $searchSlug ?>'><?= $searchWord ?></a></li>

                                <?php
                            }
                        ?>

					</ul>
				</div>
                <?php
            }
        ?>
		<!--End Sidebar Single-->

	</div>
</div>
<!--End Sidebar-->
