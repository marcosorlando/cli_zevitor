<?php

    use App\Conn\Read;
    use App\Helpers\Check;

    /**
     * Menu principal — tema Zevitor.
     * Serviços: DB_SERVICES ativos -> BASE/servico/<svc_name> (topo: BASE/servicos).
     * Projetos: só aparece se houver projeto ativo em DB_PROJECTS.
     * Blog: categorias de DB_CATEGORIES com post publicado -> BASE/artigos/<category_name>.
     *       Uma única categoria raiz vira o link do topo e as filhas formam o submenu.
     * Encanamento: padrão do WC ($Read->fullRead + foreach + extract).
     */
    $Read ??= new Read();

    // SERVIÇOS
    $Read->fullRead(
        'SELECT svc_name, svc_title FROM ' . DB_SERVICES . ' WHERE svc_status = :st ORDER BY svc_id ASC',
        'st=1'
    );
    $MenuServices = $Read->getResult() ?: [];

    // PROJETOS
    $Read->fullRead('SELECT project_id FROM ' . DB_PROJECTS . ' WHERE project_status = :st LIMIT 1', 'st=1');
    $MenuProjects = (bool) $Read->getResult();

    // BLOG (categorias raiz com post publicado + filhas com post publicado)
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
    $BlogLink = $MenuBlog ? BASE . '/artigos/' . $MenuBlog[0]['category_name'] : '#';
?>

<ul class="main-menu__list">
	<li><a href="<?= BASE ?>" title="<?= SITE_NAME ?> | Home">Home</a></li>

	<li class="<?= $MenuServices ? 'dropdown' : '' ?>">
		<a href="<?= BASE ?>/servicos" title="<?= SITE_NAME ?> | Serviços">Serviços</a>
        <?php if ($MenuServices): ?>
		<ul class="shadow-box">
            <?php foreach ($MenuServices as $Service): extract($Service); ?>
			<li><a href="<?= BASE ?>/servico/<?= $svc_name ?>"
			       title="<?= Check::safeHtmlChars($svc_title) ?>"><?= Check::safeHtmlChars($svc_title) ?></a></li>
            <?php endforeach; ?>
		</ul>
        <?php endif; ?>
	</li>

    <?php if ($MenuProjects): ?>
	<li><a href="<?= BASE ?>/projetos" title="<?= SITE_NAME ?> | Projetos">Projetos</a></li>
    <?php endif; ?>

	<li><a href="<?= BASE ?>/sobre" title="<?= SITE_NAME ?> | Sobre nós">Sobre nós</a></li>

    <?php if ($MenuBlog): ?>
	<li class="<?= $BlogItems ? 'dropdown menu-list-apper-right' : '' ?>">
		<a href="<?= $BlogLink ?>" title="<?= SITE_NAME ?> | Blog">Blog</a>
        <?php if ($BlogItems): ?>
		<ul class="shadow-box">
            <?php foreach ($BlogItems as $Category): extract($Category);
                $Children = $Category['children'] ?? [];
            ?>
			<li class="<?= $Children ? 'dropdown' : '' ?>">
				<a href="<?= BASE ?>/artigos/<?= $category_name ?>"
				   title="<?= Check::safeHtmlChars($category_title) ?>"><?= Check::safeHtmlChars($category_title) ?></a>
                <?php if ($Children): ?>
				<ul>
                    <?php foreach ($Children as $Child): ?>
					<li><a href="<?= BASE ?>/artigos/<?= $Child['category_name'] ?>"
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

	<li><a href="<?= BASE ?>/materiais" title="<?= SITE_NAME ?> | Materiais">Materiais</a></li>
	<li><a href="<?= BASE ?>/contato" title="<?= SITE_NAME ?> | Contato">Contato</a></li>
</ul>
