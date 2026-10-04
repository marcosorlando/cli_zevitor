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

    $Page = (!empty($URL[2]) && is_numeric($URL[2]) ? (int)$URL[2] : 1);
    $Page = ($Page > 0 ? $Page : 1);
    $Pager = new Pager(
        BASE . "/pesquisa/{$SearchPage}/",
        '<i class="fas fa-angle-left"></i>',
        '<i class="fas fa-angle-right"></i>',
        5
    );
    $Pager->exePager($Page, 9);

    $SearchLike = "%{$Search}%";
    $SearchParams = http_build_query([
        'limit' => $Pager->getLimit(),
        'offset' => $Pager->getOffset(),
        'title' => $SearchLike,
        'subtitle' => $SearchLike,
        'tags' => $SearchLike,
        'month' => $Search,
    ]);

    $Read->fullRead(
        'SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, '
        . 'u.user_name, u.user_lastname, c.category_title '
        . 'FROM ' . DB_POSTS . ' p '
        . 'LEFT JOIN ' . DB_USERS . ' u ON p.post_author = u.user_id '
        . 'LEFT JOIN ' . DB_CATEGORIES . ' c ON p.post_category = c.category_id '
        . 'WHERE p.post_status = 1 AND p.post_date <= NOW() '
        . 'AND (p.post_title LIKE :title OR p.post_subtitle LIKE :subtitle OR p.post_tags LIKE :tags OR MONTH(p.post_date) = :month) '
        . 'ORDER BY p.post_date DESC LIMIT :limit OFFSET :offset',
        $SearchParams
    );
    $SearchResults = $Read->getResult() ?: [];

    var_dump($SearchResults);

    $Months = [
        1 => 'Jan',
        2 => 'Fev',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'Mai',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Set',
        10 => 'Out',
        11 => 'Nov',
        12 => 'Dez'
    ];
    $Animations = ['fadeInLeft', 'fadeInUp', 'fadeInRight'];

    $renderPostCard = static function (array $post, int $index) use ($Months, $Animations): void {

        $postDate = !empty($post['post_date']) ? strtotime((string)$post['post_date']) : time();
        $postDate = false !== $postDate ? $postDate : time();
        $cover = !empty($post['post_cover'])
            ? BASE . '/tim.php?src=uploads/' . $post['post_cover'] . '&w=420&h=300'
            : INCLUDE_PATH . '/assets/images/blog/blog-1-1.jpg';
        $link = BASE . '/artigo/' . ($post['post_name'] ?? '');
        $day = date('d', $postDate);
        $month = $Months[(int)date('n', $postDate)] ?? date('M', $postDate);
        $category = !empty($post['category_title']) ? (string)$post['category_title'] : 'Blog';
        $author = trim((string)($post['user_name'] ?? '') . ' ' . (string)($post['user_lastname'] ?? ''));
        $author = '' !== $author ? $author : SITE_NAME;
        $animation = $Animations[$index % count($Animations)];
        $delay = (($index % 3) + 1) * 100;
        ?>
		<!--Blog One Single Start-->
		<div class='col-xl-4 col-lg-4 col-md-6 wow <?= $animation ?>' data-wow-delay='<?= $delay ?>ms'>
			<div class='blog-one__single'>
				<div class='blog-one__single-inner'>
					<div class='blog-one__img-box'>
						<div class='blog-one__img'>
							<img src='<?= Check::safeHtmlChars($cover) ?>'
							     alt='<?= Check::safeHtmlChars((string)($post['post_title'] ?? '')) ?>'>
							<div class='blog-one__tags'>
								<span><?= Check::safeHtmlChars($category) ?></span>
							</div>
						</div>
						<div class='blog-one__date'>
							<p><?= $day ?> <span><?= $month ?></span></p>
						</div>
					</div>
					<div class='blog-one__content'>
						<ul class='blog-one__meta list-unstyled'>
							<li>
								<a href='<?= Check::safeHtmlChars($link) ?>'>
									<span class='fas fa-user'></span><?= Check::safeHtmlChars($author) ?>
								</a>
							</li>
						</ul>
						<h3 class='blog-one__title'>
							<a href='<?= Check::safeHtmlChars($link) ?>'><?= Check::safeHtmlChars(
                                    (string)($post['post_title'] ?? '')
                                ) ?></a>
						</h3>
						<p class='blog-one__text'><?= Check::safeHtmlChars(
                                Check::chars((string)($post['post_subtitle'] ?? ''), 140)
                            ) ?></p>
					</div>
				</div>
				<div class='blog-one__read-more-box'>
					<a href='<?= Check::safeHtmlChars($link) ?>' class='blog-one__read-more'>Leia mais<span
								class='fas fa-arrow-right'></span></a>
				</div>
			</div>
		</div>
		<!--Blog One Single End-->
        <?php
    };
?>

<section class='page-header'>
	<div class='page-header__bg'
	     style='background-image: url(<?= INCLUDE_PATH ?>/assets/images/backgrounds/page-header-bg.jpg);'>
	</div>
	<div class='container'>
		<div class='page-header__inner'>
			<div class='page-header__img-1'>
				<img src='<?= INCLUDE_PATH ?>/assets/images/resources/page-header-img-1.png' alt=''>
			</div>
			<h3>Blog</h3>
			<div class='thm-breadcrumb__inner'>
				<ul class='thm-breadcrumb list-unstyled'>
					<li><a href='<?= BASE ?>'>Início</a></li>
					<li><span class='fas fa-angle-right'></span></li>
					<li>Pesquisa por <span><?= $SearchEscaped ?></span></li>
				</ul>
			</div>
		</div>
	</div>
</section>

<section class='blog-page'>
	<div class='container'>
		<div class='row'>
            <?php
                if (!$SearchResults) {
                    $Pager->returnPage();
                    echo Check::ajaxErro(
                        "Não encontramos conteúdo para a palavra-chave <b>({$SearchEscaped})</b>.",
                        E_USER_NOTICE
                    );

                    $Read->fullRead(
                        'SELECT p.post_title, p.post_subtitle, p.post_name, p.post_cover, p.post_date, '
                        . 'u.user_name, u.user_lastname, c.category_title '
                        . 'FROM ' . DB_POSTS . ' p '
                        . 'LEFT JOIN ' . DB_USERS . ' u ON p.post_author = u.user_id '
                        . 'LEFT JOIN ' . DB_CATEGORIES . ' c ON p.post_category = c.category_id '
                        . 'WHERE p.post_status = 1 AND p.post_date <= NOW() '
                        . 'ORDER BY p.post_date DESC LIMIT :limit',
                        'limit=3'
                    );
                    $LatestPosts = $Read->getResult() ?: [];

                    foreach ($LatestPosts as $index => $post) {
                        $renderPostCard($post, $index);
                    }
                } else {
                    foreach ($SearchResults as $index => $post) {
                        $renderPostCard($post, $index);
                    }

                    $Pager->exePaginator(
                        DB_POSTS,
                        'WHERE post_status = 1 AND post_date <= NOW() '
                        . 'AND (post_title LIKE :title OR post_subtitle LIKE :subtitle OR post_tags LIKE :tags OR MONTH(post_date) = :month)',
                        http_build_query([
                            'title' => $SearchLike,
                            'subtitle' => $SearchLike,
                            'tags' => $SearchLike,
                            'month' => $Search,
                        ])
                    );
                    $Paginator = str_replace(
                        ["class='paginator pagination'", 'class="active"'],
                        ["class='pg-pagination list-unstyled'", "class='count active'"],
                        $Pager->getPaginator()
                    );

                    if ('' !== $Paginator) {
                        ?>
						<div class='blog-list__pagination'>
                            <?= $Paginator ?>
						</div>
                        <?php
                    }
                }
            ?>
		</div>
	</div>
</section>
