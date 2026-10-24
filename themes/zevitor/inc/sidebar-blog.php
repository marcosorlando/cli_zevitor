<?php

    use App\Conn\Read;
    use App\Helpers\Check;

?>

<!--Start Sidebar-->
<div class='col-xl-4'>
	<div class='sidebar sidebar--three'>
		<!--Start Sidebar Single-->
		<div class='sidebar__single sidebar__search wow fadeInUp' data-wow-delay='.1s'>
			<form name='search' action='' method='post' class='sidebar__search-form'>
				<input type='search' name='s' placeholder='Pesquisar...' autocomplete='off' required>
				<button type='submit'><i class='fa fa-search'></i></button>
			</form>
		</div>
		<!--End Sidebar Single-->

		<!--Start Sidebar Single-->
		<div class='sidebar__single sidebar__category wow fadeInUp' data-wow-delay='.1s'>
			<h3 class='sidebar__title'>Categorias</h3>

            <?php
                $Read ??= new Read();
                $tags = []; // chave em minúsculas evita "Câmbio" e "câmbio" duplicadas

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
                                'SELECT c.category_id, c.category_title, c.category_name, COUNT(DISTINCT p.post_id) AS category_posts '
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
                                foreach ($Read->getResult() as $cat) {
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

                            // TAGS: da categoria aberta (artigos.php) ou das subcategorias do post (artigo.php);
                            // sem categoria (pesquisa), de todas as categorias listadas acima
                            $tagCategories = isset($category_id)
                                ? [(int)$category_id]
                                : array_filter(
                                    array_map('intval', explode(',', (string)($post_category_parent ?? '')))
                                );
                            $tagParams = [];
                            $tagFilter = [];
                            foreach (array_values($tagCategories) as $i => $tagCategory) {
                                $tagParams["c{$i}"] = $tagCategory;
                                $tagFilter[] = "p.post_category = :c{$i} OR FIND_IN_SET(:c{$i}, p.post_category_parent)";
                            }
                            if (!$tagFilter) {
                                $tagParams = ['c0' => $blogId];
                                $tagFilter[] = 'EXISTS (SELECT 1 FROM ' . DB_CATEGORIES . ' c WHERE c.category_parent = :c0 '
                                    . 'AND (p.post_category = c.category_id OR FIND_IN_SET(c.category_id, p.post_category_parent)))';
                            }

                            $Read->fullRead(
                                'SELECT p.post_tags FROM ' . DB_POSTS . ' p '
                                . 'WHERE p.post_status = 1 AND p.post_date <= NOW() '
                                . "AND p.post_tags IS NOT NULL AND p.post_tags != '' "
                                . 'AND (' . implode(' OR ', $tagFilter) . ') '
                                . 'ORDER BY p.post_date DESC',
                                http_build_query($tagParams)
                            );
                            foreach ($Read->getResult() ?: [] as $postTags) {
                                foreach (explode(',', $postTags['post_tags']) as $tag) {
                                    $tag = trim($tag);
                                    if ('' !== $tag) {
                                        $tags[mb_strtolower($tag)] ??= $tag;
                                    }
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
			<h3 class='sidebar__title'>Postagens recentes</h3>
			<div class='sidebar__post-box'>
                <?php
                    $Read->fullRead(
                        "SELECT * FROM " . DB_POSTS . " WHERE post_status != 0 ORDER BY post_date DESC LIMIT 4"
                    );
                    if ($Read->getResult()) {
                        foreach ($Read->getResult() as $post) {
                            ?>
							<a href="<?= BASE ?>/artigo/<?= $post['post_name'] ?>">
								<div class='sidebar__post-single'>
									<div class='sidebar-post__img'>
										<img src='<?= BASE ?>/tim.php?src=uploads/<?= $post['post_cover'] ?>&w=600&h=314
									     alt='<?= $post['post_title'] ?>'>
									</div>
									<div class='sidebar__post-content-box'>
										<h3><?= $post['post_title'] ?></h3></div>
								</div>
							</a>
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
				<p>Chame no Whatsapp</p>
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
                ?>

				<div class='sidebar__single sidebar__tags wow fadeInUp' data-wow-delay='.1s'>
					<h3 class='sidebar__title'>Nuvem de tags</h3>
					<ul class='sidebar__tags-list clearfix list-unstyled'>

                        <?php
                            foreach ($tags as $tag) {
                                $searchWord = trim(Check::getCapilalize($tag));
                                ?>

								<li><a href='<?= BASE ?>/pesquisa/<?= urlencode($tag) ?>'><?= Check::safeHtmlChars(
                                            $searchWord
                                        ) ?></a></li>

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
