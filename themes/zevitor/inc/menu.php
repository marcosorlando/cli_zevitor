<?php

use App\Conn\Read;

/**
 * Menu principal — tema Zevitor.
 * Visual: template Servixa (classes .main-menu__list).
 * Encanamento: WorkControl (referência: themes/doripel/inc/header.php).
 * Itens dinâmicos a partir do núcleo do WC: Páginas (DB_PAGES) e Blog (DB_CATEGORIES).
 */
$Read ??= new Read();
?>
<ul class="main-menu__list">
    <li><a href="<?= BASE; ?>" title="<?= SITE_NAME; ?> | Início">Início</a></li>

    <?php
    // Páginas institucionais (dinâmico)
    if (defined('DB_PAGES')) {
        $Read->fullRead(
            'SELECT page_title, page_name FROM ' . DB_PAGES . ' WHERE page_status = 1 AND page_id != 2 ORDER BY page_order ASC, page_name ASC'
        );
        if ($Read->getResult()) {
            foreach ($Read->getResult() as $Page) {
                echo "<li><a title='" . SITE_NAME . " | {$Page['page_title']}' href='" . BASE . "/{$Page['page_name']}'>{$Page['page_title']}</a></li>";
            }
        }
    }

// Blog por categoria (dinâmico)
if (defined('DB_CATEGORIES')) {
    $Read->exeRead(DB_CATEGORIES, 'WHERE category_parent IS NULL ORDER BY category_name ASC');
    if ($Read->getResult()) {
        echo "<li class='dropdown'><a href='" . BASE . "/artigos'>Blog</a><ul class='shadow-box'>";
        foreach ($Read->getResult() as $Cat) {
            echo "<li><a title='" . SITE_NAME . " | {$Cat['category_title']}' href='" . BASE . "/artigos/{$Cat['category_name']}'>{$Cat['category_title']}</a></li>";
        }
        echo '</ul></li>';
    }
}

/*
 * TODO — serviços/produtos por categoria:
 * espelhar o padrão do doripel/inc/header.php (loop sobre a tabela de categorias),
 * usando a constante de tabela do schema do Zevitor quando confirmada.
 */
?>

    <li><a href="<?= BASE; ?>/contato" title="<?= SITE_NAME; ?> | Contato">Contato</a></li>
</ul>
