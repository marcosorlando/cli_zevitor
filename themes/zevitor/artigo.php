<?php

    use App\Conn\Read;
    use App\Conn\Update;
    use App\Helpers\DateHelper;

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

        $Read->fullRead(
            "SELECT COUNT(id) AS total FROM " . DB_COMMENTS . " WHERE post_id = :post",
            "post={$post_id}"
        );
        $PostComments = (int)($Read->getResult()[0]['total'] ?? 0);
    }
    extract($Author);

    ['day' => $PostDay, 'month' => $PostMonth] = DateHelper::dayMonth($post_date);
    $ShareLink = urlencode(BASE . "/artigo/{$post_name}");
    $ShareTitle = urlencode($post_title);
    $PostTags = array_filter(array_map('trim', explode(',', (string)($post_tags ?? ''))));
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
					<li>
						<a href='<?= BASE ?>/artigos/<?= $PostCategory['category_name'] ?>'
						   title='Ver mais: <?= $PostCategory['category_title'] ?> em <?= SITE_NAME ?>!'><?= $PostCategory['category_title'] ?></a>
					</li>
					<li><span class='fas fa-angle-right'></span></li>
					<li><?= $post_title ?></li>
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
                            <?php
                                if ($post_video) {
                                    echo "<div class='embed-container'>";
                                    echo "<iframe id='mediaview' width='640' height='360' src='https://www.youtube.com/embed/{$post_video}?rel=0&amp;showinfo=0&autoplay=0&origin=" . BASE . "' frameborder='0' allowfullscreen></iframe>";
                                    echo "</div>";
                                } else {
                                    echo "<img title='{$post_title}' alt='{$post_title}' src='" . BASE . "/tim.php?src=uploads/{$post_cover}&w=1200&h=628'/>";
                                }
                            ?>
						</div>
						<div class='blog-details__date'>
							<p><?= $PostDay ?><br><span><?= $PostMonth ?></span></p>
						</div>
					</div>
					<div class='blog-details__content'>
						<div class='blog-details__user-and-meta'>
							<div class='blog-details__user'>
								<p><span class='icon-user-1'></span>Por <?= $AuthorName ?></p>
							</div>
							<ul class='blog-details__meta list-unstyled'>
								<li>
									<time datetime='<?= DateHelper::iso($post_date) ?>'>
										<span class='fas fa-calendar-alt'></span><?= DateHelper::human($post_date) ?>
									</time>
								</li>
								<li>
									<a href='#comments' class='wc_goto'><span class='fas fa-comments'></span>Comentários (<?= str_pad((string)$PostComments, 2, '0', STR_PAD_LEFT) ?>)</a>
								</li>
								<li>
									<span class='fas fa-eye'></span><?= $post_views ?> views
								</li>
								<li>
									<span class='fas fa-clock'></span><?= $post_time ?> min de leitura
								</li>
							</ul>
						</div>
						<h1 class='blog-details__title'><?= $post_title ?></h1>
                        <?php
                            if (!empty($post_subtitle)) { ?>
								<p class='blog-details__text-1'><?= $post_subtitle ?></p>
                            <?php
                            } ?>
						<div class='htmlchars'>
                            <?= $post_content ?>
						</div>

						<div class='blog-details__tag-and-share'>
							<div class='blog-details__tag'>
                                <?php
                                    if ($PostTags) { ?>
										<h3 class='blog-details__tag-title'>Etiquetas:</h3>
										<ul class='blog-details__tag-list list-unstyled'>
                                            <?php
                                                foreach ($PostTags as $Tag) { ?>
													<li>
														<a href='<?= BASE ?>/pesquisa/<?= urlencode($Tag) ?>'>#<?= $Tag ?></a>
													</li>
                                                <?php
                                                } ?>
										</ul>
                                    <?php
                                    } ?>
							</div>
							<div class='blog-details__share-box'>
								<h3 class='blog-details__share-title'>Compartilhar :</h3>
								<div class='blog-details__share'>
									<a href='https://www.facebook.com/sharer/sharer.php?u=<?= $ShareLink ?>'
									   target='_blank' rel='noopener' title='Compartilhar no Facebook'><span
												class='icon-facebook-app-symbol'></span></a>
									<a href='https://twitter.com/intent/tweet?url=<?= $ShareLink ?>&text=<?= $ShareTitle ?>'
									   target='_blank' rel='noopener' title='Compartilhar no X'><span
												class='icon-twitter'></span></a>
									<a href='https://api.whatsapp.com/send?text=<?= $ShareTitle ?>%20<?= $ShareLink ?>'
									   target='_blank' rel='noopener' title='Compartilhar no WhatsApp'><span
												class='fab fa-whatsapp'></span></a>
									<a href='https://www.linkedin.com/sharing/share-offsite/?url=<?= $ShareLink ?>'
									   target='_blank' rel='noopener' title='Compartilhar no LinkedIn'><span
												class='fab fa-linkedin-in'></span></a>
								</div>
							</div>
						</div>

                        <?php
                            if ($Author) { ?>
								<!--Autor-->
								<div class='comment-one'>
									<div class='comment-one__single'>
										<div class='comment-one__image'>
											<img src='<?= BASE ?>/tim.php?src=uploads/<?= $user_thumb ?>&w=100&h=100'
											     alt='<?= $AuthorName ?>' title='<?= $AuthorName ?>'>
										</div>
										<div class='comment-one__content'>
											<h3><?= $AuthorName ?></h3>
											<span>Autor(a)</span>
											<p><?= $user_description ?></p>
										</div>
									</div>
								</div>
                            <?php
                            } ?>

						<!--Comentários-->
						<div class='comment-form'>
                            <?php
                                if (APP_COMMENTS && COMMENT_ON_POSTS) {
                                    $CommentKey = $post_id;
                                    $CommentType = 'post';
                                    require 'assets/widgets/comments/comments.php';
                                }
                            ?>
						</div>
					</div>
				</div>

                <?php
                    $Read->fullRead(
                        "SELECT p.post_title, p.post_name, p.post_cover, p.post_date, p.post_views, u.user_name, u.user_lastname, u.user_thumb FROM " . DB_POSTS . " p, " . DB_USERS . " u WHERE p.post_status = 1 AND p.post_date <= NOW() AND p.post_category = :ct AND p.post_id != :id AND p.post_author = u.user_id ORDER BY p.post_date DESC LIMIT 2",
                        "ct={$post_category}&id={$post_id}"
                    );

                    if ($Read->getResult()) { ?>
						<!--Artigos Relacionados-->
						<div class='blog-details__related'>
							<h3 class='comment-form__title'>Artigos Relacionados</h3>
							<div class='row'>
                                <?php
                                    foreach ($Read->getResult() as $More) {
                                        extract($More);
                                        $authorName = "{$user_name} {$user_lastname}";
                                        require REQUIRE_PATH . '/inc/post.php';
                                    }
                                ?>
							</div>
						</div>
                    <?php
                    } ?>
			</div>
            <?php
                require_once REQUIRE_PATH . '/inc/sidebar-blog.php'
            ?>
		</div>
	</div>
</section>
<!--Blog Details End-->
