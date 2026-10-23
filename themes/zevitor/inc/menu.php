<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    $Read ??= new Read();

    $Read->fullRead(
        'SELECT page_name, page_title, page_status, page_menu FROM ' . DB_PAGES
        . ' ORDER BY page_order IS NULL, page_order ASC, page_title ASC'
    );
    $RegisteredPages = $Read->getResult() ?: [];
    $MenuPages = array_values(array_filter(
        $RegisteredPages,
        static fn(array $Page): bool => (int)$Page['page_status'] === 1
            && (int)$Page['page_menu'] === 1
            && !empty($Page['page_name'])
            && !in_array($Page['page_name'], ['index', 'home', 'projeto', 'servico', 'artigo', 'artigos'], true)
    ));

    $Read->fullRead(
        'SELECT svc_name, svc_title FROM ' . DB_SERVICES . ' WHERE svc_status = :st ORDER BY svc_id ASC',
        'st=1'
    );
    $MenuServices = $Read->getResult() ?: [];

    $Read->fullRead('SELECT project_id FROM ' . DB_PROJECTS . ' WHERE project_status = :st LIMIT 1', 'st=1');
    $MenuProjects = (bool) $Read->getResult();

    $RegisteredSlugs = array_column($RegisteredPages, 'page_name');
    if ($MenuProjects && !in_array('projetos', $RegisteredSlugs, true)) {
        array_unshift($MenuPages, ['page_name' => 'projetos', 'page_title' => 'Projetos']);
    }

    $Read->fullRead(
        'SELECT category_id, category_title, category_name FROM ' . DB_CATEGORIES . ' '
        . 'WHERE category_parent IS NULL AND category_id IN ('
        . 'SELECT post_category FROM ' . DB_POSTS . ' WHERE post_status = 1 AND post_date <= NOW()'
        . ') ORDER BY category_title ASC'
    );
    $MenuBlog = $Read->getResult() ?: [];
    foreach ($MenuBlog as $Key => $Category) {
        $Read->fullRead(
            'SELECT category_title, category_name FROM ' . DB_CATEGORIES . ' c '
            . 'WHERE c.category_parent = :parent AND EXISTS ('
            . 'SELECT 1 FROM ' . DB_POSTS . ' p WHERE p.post_status = 1 AND p.post_date <= NOW() '
            . 'AND FIND_IN_SET(c.category_id, p.post_category_parent)'
            . ') ORDER BY c.category_title ASC',
            "parent={$Category['category_id']}"
        );
        $MenuBlog[$Key]['children'] = $Read->getResult() ?: [];
    }

    $BlogRoot = 1 === count($MenuBlog) ? $MenuBlog[0] : null;
    $BlogItems = $BlogRoot ? $BlogRoot['children'] : $MenuBlog;
    $BlogLink = $MenuBlog ? BASE . '/artigos/' . rawurlencode($MenuBlog[0]['category_name']) : null;
?>

<ul class="main-menu__list">
	<li><a href="<?= BASE ?>" title="<?= SITE_NAME ?> | Home">Home</a></li>

    <?php foreach ($MenuPages as $Page):
        $PageName = $Page['page_name'];
        $PageTitle = Check::safeHtmlChars($Page['page_title'] ?: $PageName);
        $SubServices = 'servicos' === $PageName ? $MenuServices : [];
    ?>
    <li class="<?= $SubServices ? 'dropdown' : '' ?>">
        <a href="<?= BASE ?>/<?= rawurlencode($PageName) ?>" title="<?= $PageTitle ?>"><?= $PageTitle ?></a>
        <?php if ($SubServices): ?>
            <ul class="shadow-box">
                <?php foreach ($SubServices as $Service): ?>
                    <li><a href="<?= BASE ?>/servico/<?= rawurlencode($Service['svc_name']) ?>"
                           title="<?= Check::safeHtmlChars($Service['svc_title']) ?>"><?= Check::safeHtmlChars($Service['svc_title']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>

    <?php if ($BlogLink): ?>
	<li class="<?= $BlogItems ? 'dropdown menu-list-apper-right' : '' ?>">
		<a href="<?= $BlogLink ?>" title="<?= SITE_NAME ?> | Blog">Blog</a>
        <?php if ($BlogItems): ?>
		<ul class="shadow-box">
            <?php foreach ($BlogItems as $Category): extract($Category);
                $Children = $Category['children'] ?? [];
            ?>
			<li class="<?= $Children ? 'dropdown' : '' ?>">
				<a href="<?= BASE ?>/artigos/<?= rawurlencode($category_name) ?>"
				   title="<?= Check::safeHtmlChars($category_title) ?>"><?= Check::safeHtmlChars($category_title) ?></a>
                <?php if ($Children): ?>
				<ul>
                    <?php foreach ($Children as $Child): ?>
					<li><a href="<?= BASE ?>/artigos/<?= rawurlencode($Child['category_name']) ?>"
					       title="<?= Check::safeHtmlChars($Child['category_title']) ?>"><?= Check::safeHtmlChars($Child['category_title']) ?></a></li>
                    <?php endforeach; ?>
				</ul>
                <?php endif; ?>
			</li>
            <?php endforeach; ?>
		</ul>
        <?php endif; ?>
	</li>
    <?php endif; ?>

</ul>
