<?php

    /**
     * Ícones do tema (flaticon.css) para o painel, sem carregar o CSS do tema (as classes icon-* colidem com as do admin).
     *
     * Define:
     * - $IconList: classe => código do glyph (ex.: 'icon-mechanical' => 'e900')
     * - $IconFonts: caminho da fonte icomoon do tema, sem extensão
     *
     * Para exibir um glyph: font-family 'wc-theme-icons' (@font-face com $IconFonts) e content: attr(data-glyph).
     */
    $IconList = [];
    $IconCss = __DIR__ . '/../../themes/' . THEME . '/assets/css/flaticon.css';
    if (is_file($IconCss) && preg_match_all(
        '/\.(icon-[\w-]+):before\s*\{\s*content:\s*"\\\\([0-9a-f]+)"/i',
        (string) file_get_contents($IconCss),
        $IconMatches,
        PREG_SET_ORDER
    )) {
        foreach ($IconMatches as $IconMatch) {
            $IconList[$IconMatch[1]] = $IconMatch[2];
        }
        ksort($IconList);
    }
    $IconFonts = BASE . '/themes/' . THEME . '/assets/fonts/icomoon';
