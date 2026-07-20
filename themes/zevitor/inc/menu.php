<?php

use App\Conn\Read;

$Read ??= new Read();

if (!function_exists('zevitorMenuEsc')) {
    function zevitorMenuEsc(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('zevitorMenuRenderCategories')) {
    function zevitorMenuRenderCategories(array $categories, string $segment, string $slugField = 'category_name'): void
    {
        if (!$categories) {
            return;
        }

        echo "<li class='dropdown'><a href='#'>Categorias</a><ul>";
        foreach ($categories as $category) {
            $title = zevitorMenuEsc($category['category_title'] ?? '');
            $slug = zevitorMenuEsc($category[$slugField] ?? '');
            $children = $category['children'] ?? [];

            if ($children) {
                echo "<li class='dropdown'><a href='" . BASE . "/{$segment}/{$slug}'>{$title}</a><ul>";
                foreach ($children as $child) {
                    $childTitle = zevitorMenuEsc($child['category_title'] ?? '');
                    $childSlug = zevitorMenuEsc($child[$slugField] ?? '');
                    echo "<li><a href='" . BASE . "/{$segment}/{$childSlug}'>{$childTitle}</a></li>";
                }
                echo '</ul></li>';
            } else {
                echo "<li><a href='" . BASE . "/{$segment}/{$slug}'>{$title}</a></li>";
            }
        }
        echo '</ul></li>';
    }
}

if (!function_exists('zevitorMenuReadBlogCategories')) {
    function zevitorMenuReadBlogCategories(Read $read): array
    {
        $read->fullRead(
            'SELECT c.category_id, c.category_title, c.category_name '
            . 'FROM ' . DB_CATEGORIES . ' c '
            . 'WHERE c.category_parent IS NULL '
            . 'AND c.category_id IN ('
            . 'SELECT post_category FROM ' . DB_POSTS . ' WHERE post_status = 1 AND post_date <= NOW()'
            . ') '
            . 'ORDER BY c.category_title ASC'
        );
        $parents = $read->getResult() ?: [];

        foreach ($parents as $index => $parent) {
            $read->fullRead(
                'SELECT c.category_id, c.category_title, c.category_name '
                . 'FROM ' . DB_CATEGORIES . ' c '
                . 'WHERE c.category_parent = :parent '
                . 'AND EXISTS ('
                . 'SELECT 1 FROM ' . DB_POSTS . ' p '
                . 'WHERE p.post_status = 1 AND p.post_date <= NOW() '
                . 'AND FIND_IN_SET(c.category_id, p.post_category_parent)'
                . ') '
                . 'ORDER BY c.category_title ASC',
                'parent=' . $parent['category_id']
            );
            $parents[$index]['children'] = $read->getResult() ?: [];
        }

        return $parents;
    }
}

if (!function_exists('zevitorMenuReadModuleCategories')) {
    function zevitorMenuReadModuleCategories(
        Read $read,
        string $categoryTable,
        string $contentTable,
        string $contentCategoryField,
        string $contentStatusField
    ): array {
        $read->fullRead(
            'SELECT c.category_id, c.category_title, c.category_slug '
            . "FROM {$categoryTable} c "
            . 'WHERE c.category_parent IS NULL '
            . "AND (c.category_id IN (SELECT {$contentCategoryField} FROM {$contentTable} WHERE {$contentStatusField} = 1) "
            . "OR c.category_id IN (SELECT category_parent FROM {$categoryTable} WHERE category_id IN "
            . "(SELECT {$contentCategoryField} FROM {$contentTable} WHERE {$contentStatusField} = 1) "
            . 'AND category_parent IS NOT NULL)) '
            . 'ORDER BY c.category_title ASC'
        );
        $parents = $read->getResult() ?: [];

        foreach ($parents as $index => $parent) {
            $read->fullRead(
                'SELECT c.category_id, c.category_title, c.category_slug '
                . "FROM {$categoryTable} c "
                . 'WHERE c.category_parent = :parent '
                . "AND c.category_id IN (SELECT {$contentCategoryField} FROM {$contentTable} WHERE {$contentStatusField} = 1) "
                . 'ORDER BY c.category_title ASC',
                'parent=' . $parent['category_id']
            );
            $parents[$index]['children'] = $read->getResult() ?: [];
        }

        return $parents;
    }
}

$ServiceCategories = (defined('DB_SERVICES_CATEGORIES') && defined('DB_SERVICES')
    ? zevitorMenuReadModuleCategories($Read, DB_SERVICES_CATEGORIES, DB_SERVICES, 'svc_category', 'svc_status')
    : []);

$ProjectCategories = (defined('DB_PROJECTS_CATEGORIES') && defined('DB_PROJECTS')
    ? zevitorMenuReadModuleCategories($Read, DB_PROJECTS_CATEGORIES, DB_PROJECTS, 'project_category', 'project_status')
    : []);

$BlogCategories = (defined('DB_CATEGORIES') && defined('DB_POSTS')
    ? zevitorMenuReadBlogCategories($Read)
    : []);
?>

<ul class="main-menu__list">
    <li><a href="<?= BASE; ?>" title="<?= SITE_NAME; ?> | Home">Home</a></li>

    <li class="<?= $ServiceCategories ? 'dropdown' : ''; ?>">
        <a href="<?= $ServiceCategories ? '#' : BASE . '/servicos'; ?>" title="<?= SITE_NAME; ?> | Serviços">Serviços</a>
        <?php if ($ServiceCategories): ?>
            <ul class="shadow-box">
                <?php zevitorMenuRenderCategories($ServiceCategories, 'servicos', 'category_slug'); ?>
            </ul>
        <?php endif; ?>
    </li>

    <li class="<?= $ProjectCategories ? 'dropdown' : ''; ?>">
        <a href="<?= $ProjectCategories ? '#' : BASE . '/projetos'; ?>" title="<?= SITE_NAME; ?> | Projetos">Projetos</a>
        <?php if ($ProjectCategories): ?>
            <ul class="shadow-box">
                <?php zevitorMenuRenderCategories($ProjectCategories, 'projetos', 'category_slug'); ?>
            </ul>
        <?php endif; ?>
    </li>

    <li><a href="<?= BASE; ?>/sobre" title="<?= SITE_NAME; ?> | Sobre nós">Sobre nós</a></li>

    <li class="<?= $BlogCategories ? 'dropdown menu-list-apper-right' : ''; ?>">
        <a href="#" title="<?= SITE_NAME; ?> | Blog">Blog</a>
        <?php if ($BlogCategories): ?>
            <ul class="shadow-box">
                <?php zevitorMenuRenderCategories($BlogCategories, 'artigos'); ?>
            </ul>
        <?php endif; ?>
    </li>

    <li><a href="<?= BASE; ?>/materiais" title="<?= SITE_NAME; ?> | Materiais">Materiais</a></li>
    <li><a href="<?= BASE; ?>/contato" title="<?= SITE_NAME; ?> | Contato">Contato</a></li>
</ul>
