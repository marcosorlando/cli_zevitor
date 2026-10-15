<?php

declare(strict_types=1);

namespace App\Bootstrap;

use ErrorException;
use ReflectionClass;
use Throwable;

use function count;
use function date;
use function defined;
use function dirname;
use function error_get_last;
use function error_log;
use function error_reporting;
use function explode;
use function file_get_contents;
use function header;
use function headers_list;
use function headers_sent;
use function htmlspecialchars;
use function http_response_code;
use function in_array;
use function is_array;
use function is_file;
use function is_numeric;
use function is_readable;
use function json_encode;
use function max;
use function md5;
use function ob_end_clean;
use function ob_get_level;
use function preg_match;
use function preg_replace;
use function rawurlencode;
use function register_shutdown_function;
use function rtrim;
use function set_error_handler;
use function set_exception_handler;
use function sprintf;
use function str_contains;
use function str_replace;
use function str_starts_with;
use function stripos;
use function strlen;
use function strpos;
use function strtolower;
use function strtr;
use function substr;
use function token_get_all;
use function trim;

/**
 * Tratamento de erros do PHP.
 *
 * - Warning / Notice / Deprecated: a página continua carregando e os avisos aparecem
 *   num painel flutuante (como o PHP padrão faria, mas sem quebrar o layout).
 * - Exceção não tratada / erro fatal: tela de erro com trecho do código, explicação
 *   em PT-BR e o caminho de chamadas até o erro.
 * - Produção (APP_DEBUG desligado): avisos vão só para o log e o fatal mostra uma
 *   página genérica, sem expor caminhos.
 * - AJAX/JSON: responde JSON (com detalhes apenas em debug).
 */
final class ErrorHandler
{
    private const MAX_NOTICES = 50;
    private const EDITOR_URL = 'phpstorm://open?file=%s&line=%d';
    private const FATAL_TYPES = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];

    /** @var array<string, array{severity: int, message: string, file: string, line: int, count: int}> */
    private static array $notices = [];
    private static bool $asJson = false;
    private static bool $fatalRendered = false;

    /** @var array<string, string> */
    private static array $sourceCache = [];

    public static function register(bool $asJson = false): void
    {

        self::$asJson = $asJson;

        set_error_handler(self::handleError(...));
        set_exception_handler(self::handleException(...));
        register_shutdown_function(self::handleShutdown(...));
    }

    public static function handleError(int $severity, string $message, string $file = '', int $line = 0): bool
    {

        if ((error_reporting() & $severity) === 0) {
            return false;
        }

        // Erros "de verdade" disparados via trigger_error interrompem a execução.
        if (in_array($severity, [E_USER_ERROR, E_RECOVERABLE_ERROR], true)) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }

        $debug = self::isDebugEnabled();

        // AJAX em debug: mantém o comportamento estrito para o aviso aparecer no JSON.
        if (self::$asJson && $debug) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }

        if (!$debug) {
            error_log(sprintf('PHP %s: %s in %s:%d', self::severityLabel($severity), $message, $file, $line));

            return true;
        }

        $key = md5($severity . '|' . $message . '|' . $file . '|' . $line);
        if (isset(self::$notices[$key])) {
            self::$notices[$key]['count']++;
        } elseif (count(self::$notices) < self::MAX_NOTICES) {
            self::$notices[$key] = [
                'severity' => $severity,
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'count' => 1,
            ];
        }

        return true;
    }

    public static function handleException(Throwable $e): void
    {

        self::$fatalRendered = true;
        error_log(sprintf(
            'PHP %s: %s in %s:%d',
            (new ReflectionClass($e))->getShortName(),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));

        $debug = self::isDebugEnabled();

        if (self::$asJson) {
            self::clearOutputBuffers();
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: application/json; charset=UTF-8');
            }

            echo json_encode(self::jsonPayload($e, $debug), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return;
        }

        self::clearOutputBuffers();
        $standalone = !headers_sent();
        if ($standalone) {
            http_response_code(500);
            header('Content-Type: text/html; charset=UTF-8');
        }

        echo $debug ? self::renderFatal($e, $standalone) : self::renderProduction($standalone);
    }

    public static function handleShutdown(): void
    {

        $err = error_get_last();
        if (null !== $err && !self::$fatalRendered && in_array($err['type'], self::FATAL_TYPES, true)) {
            self::handleException(new ErrorException($err['message'], 0, $err['type'], $err['file'], $err['line']));

            return;
        }

        if ([] !== self::$notices && !self::$fatalRendered && !self::$asJson && self::isHtmlResponse()) {
            echo self::renderDock();
        }
    }

    // ------------------------------------------------------------------ render

    private static function renderFatal(Throwable $e, bool $standalone): string
    {

        $title = $e instanceof ErrorException
            ? self::severityLabel($e->getSeverity())
            : (new ReflectionClass($e))->getShortName();
        $class = $e::class;
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $hint = self::hintFor($message, $e instanceof ErrorException ? $e->getSeverity() : 0);

        $copy = $title . ': ' . $message . "\n" . self::relPath($file) . ':' . $line . "\n\n" . $e->getTraceAsString();

        // Argumento com tipo errado: o culpado é quem chamou a função, não a assinatura dela.
        $declared = '';
        if (1 === preg_match('/, called in (.+) on line (\d+)/', $message, $m)) {
            $declared = '<p class="declared">Função declarada em <a href="' . self::e(self::editorUrl($file, $line)) . '">'
                . self::e(self::relPath($file)) . '<b>:' . $line . '</b></a></p>';
            [$file, $line] = [$m[1], (int)$m[2]];
            $message = str_replace($m[0], '', $message);
        }
        $message = str_replace(dirname(__DIR__, 2) . '/', '', $message);

        $stack = '';
        foreach (self::frames($e) as $i => $f) {
            $vendor = str_contains($f['file'], '/vendor/') ? ' vendor' : '';
            $stack .= '<li class="frame' . $vendor . '">'
                . '<span class="fn">' . ($i === 0 ? 'local do erro' : self::e($f['call'])) . '</span>'
                . '<a class="loc" href="' . self::e(self::editorUrl($f['file'], $f['line'])) . '">'
                . self::e(self::relPath($f['file'])) . '<b>:' . $f['line'] . '</b></a></li>';
        }

        $notices = '';
        if ([] !== self::$notices) {
            $notices = '<section class="card"><h2>Avisos antes do erro <span class="count">'
                . count(self::$notices) . '</span></h2><div class="list">' . self::renderNoticeItems(false) . '</div></section>';
        }

        $method = self::e((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $uri = self::e((string)($_SERVER['REQUEST_URI'] ?? ''));

        $body = '<div class="wcx fatal-page"><main class="wrap">'
            . '<header class="hero">'
            . '<div class="eyebrow"><span class="pulse"></span>Erro fatal · HTTP 500 · PHP ' . PHP_VERSION . '</div>'
            . '<p class="type" title="' . self::e($class) . '">' . self::e($title) . '</p>'
            . '<h1>' . self::e($message) . '</h1>'
            . '<a class="where" href="' . self::e(self::editorUrl($file, $line)) . '" title="' . self::e($file) . '">'
            . self::e(self::relPath($file)) . '<b>:' . $line . '</b></a>'
            . $declared
            . '<div class="actions">'
            . '<button type="button" class="btn primary" data-copy>Copiar erro</button>'
            . '<a class="btn" href="' . self::e(self::editorUrl($file, $line)) . '">Abrir no PhpStorm</a>'
            . '<button type="button" class="btn" data-reload>Recarregar</button>'
            . '</div></header>'
            . ($hint ? '<section class="hint"><strong>O que isso significa</strong><p>' . $hint . '</p></section>' : '')
            . '<section class="card"><h2>Código</h2>' . (self::excerpt($file, $line) ?: '<p class="muted">Trecho indisponível.</p>') . '</section>'
            . '<section class="card"><h2>Caminho até o erro</h2><ol class="stack">' . $stack . '</ol></section>'
            . $notices
            . '<footer>' . $method . ' ' . $uri . ' · ' . date('d/m/Y H:i:s')
            . ' · APP_DEBUG ativo: em produção esta tela vira uma página genérica.</footer>'
            . '<textarea class="copy-src" hidden>' . self::e($copy) . '</textarea>'
            . '</main></div>';

        $comment = '<!-- PHP ' . self::e($title) . ': ' . self::e($message) . ' em ' . self::e($file) . ':' . $line . ' -->';
        $script = self::script('wc-php-error');

        if ($standalone) {
            return '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">'
                . '<meta name="viewport" content="width=device-width, initial-scale=1">'
                . '<title>' . self::e($title) . ' · ' . self::e(self::relPath($file)) . ':' . $line . '</title>'
                . '<style>html,body{margin:0;background:#0b0f17}' . self::css() . '</style></head><body>'
                . $comment . '<div id="wc-php-error">' . $body . '</div>' . $script . '</body></html>';
        }

        // A saída já foi enviada: sobrepõe a página isolando o CSS num shadow root.
        return $comment . '<div id="wc-php-error" style="position:fixed;inset:0;z-index:2147483647">'
            . '<template shadowrootmode="open"><style>' . self::css() . '</style>' . $body . '</template></div>' . $script;
    }

    private static function renderDock(): string
    {

        $total = 0;
        $comments = '';
        $onlyDeprecated = true;
        foreach (self::$notices as $n) {
            $total += $n['count'];
            $onlyDeprecated = $onlyDeprecated && in_array($n['severity'], [E_DEPRECATED, E_USER_DEPRECATED], true);
            $comments .= '<!-- PHP ' . self::severityLabel($n['severity']) . ': ' . self::e($n['message'])
                . ' em ' . self::e($n['file']) . ':' . $n['line'] . " -->\n";
        }

        $label = $total . ' ' . (1 === $total ? 'aviso' : 'avisos') . ' do PHP';
        $console = [];
        foreach (self::$notices as $n) {
            $console[] = '[PHP ' . self::severityLabel($n['severity']) . '] ' . $n['message']
                . ' (' . self::relPath($n['file']) . ':' . $n['line'] . ')';
        }

        $body = '<div class="wcx" data-collapsed="' . ($onlyDeprecated ? '1' : '0') . '">'
            . '<button type="button" class="pill" data-toggle title="Mostrar avisos do PHP">'
            . '<span class="dot"></span>' . $label . '</button>'
            . '<section class="dock" role="region" aria-label="Avisos do PHP">'
            . '<header class="dock-head"><span class="dot"></span><strong>' . $label . '</strong>'
            . '<span class="muted">nesta página · a página continuou carregando</span>'
            . '<button type="button" class="icon" data-toggle title="Minimizar">–</button></header>'
            . '<div class="list">' . self::renderNoticeItems(true) . '</div>'
            . '</section></div>';

        return "\n" . $comments
            . '<div id="wc-php-dock" style="all:initial"><template shadowrootmode="open"><style>' . self::css()
            . '</style>' . $body . '</template></div>'
            . self::script('wc-php-dock', $console);
    }

    private static function renderNoticeItems(bool $openFirst): string
    {

        $html = '';
        $first = true;
        foreach (self::$notices as $n) {
            [$label, $tone] = self::severityMeta($n['severity']);
            $hint = self::hintFor($n['message'], $n['severity']);
            $excerpt = self::excerpt($n['file'], $n['line'], 4);
            $html .= '<details class="item"' . ($first && $openFirst ? ' open' : '') . '>'
                . '<summary><span class="chip ' . $tone . '">' . $label . '</span>'
                . '<span class="msg">' . self::e($n['message']) . '</span>'
                . '<span class="loc">' . self::e(self::relPath($n['file'])) . '<b>:' . $n['line'] . '</b></span>'
                . ($n['count'] > 1 ? '<span class="times">×' . $n['count'] . '</span>' : '')
                . '</summary><div class="item-body">'
                . ($hint ? '<p class="hint-inline"><strong>O que significa:</strong> ' . $hint . '</p>' : '')
                . $excerpt
                . '<a class="btn small" href="' . self::e(self::editorUrl($n['file'], $n['line'])) . '">Abrir no PhpStorm</a>'
                . '</div></details>';
            $first = false;
        }

        return $html;
    }

    private static function renderProduction(bool $standalone): string
    {

        $html = '<div class="wcx prod"><main><h1>Algo deu errado</h1>'
            . '<p>Não conseguimos carregar esta página agora. Tente novamente em instantes.</p>'
            . '<a class="btn primary" href="javascript:location.reload()">Tentar novamente</a></main></div>';

        if ($standalone) {
            return '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8">'
                . '<meta name="viewport" content="width=device-width, initial-scale=1"><title>Erro</title>'
                . '<style>html,body{margin:0}' . self::css() . '</style></head><body>' . $html . '</body></html>';
        }

        return '<div style="position:fixed;inset:0;z-index:2147483647"><template shadowrootmode="open"><style>'
            . self::css() . '</style>' . $html . '</template></div>';
    }

    /**
     * @param list<string> $console
     */
    private static function script(string $hostId, array $console = []): string
    {

        $js = <<<'JS'
(function () {
  var host = document.getElementById('__HOST__');
  if (!host) return;
  var root = host.shadowRoot;
  if (!root) {
    var tpl = host.querySelector('template');
    if (tpl) { root = host.attachShadow({ mode: 'open' }); root.appendChild(tpl.content); tpl.remove(); }
    else { root = host; }
  }
  var logs = __CONSOLE__;
  logs.forEach(function (m) { console.warn(m); });

  var wrap = root.querySelector('.wcx');
  var key = 'wc-php-dock-collapsed';
  try { var saved = sessionStorage.getItem(key); if (saved !== null && wrap) wrap.dataset.collapsed = saved; } catch (e) {}
  root.querySelectorAll('[data-toggle]').forEach(function (b) {
    b.addEventListener('click', function () {
      wrap.dataset.collapsed = wrap.dataset.collapsed === '1' ? '0' : '1';
      try { sessionStorage.setItem(key, wrap.dataset.collapsed); } catch (e) {}
    });
  });

  root.querySelectorAll('[data-reload]').forEach(function (b) {
    b.addEventListener('click', function () { location.reload(); });
  });
  root.querySelectorAll('[data-copy]').forEach(function (b) {
    b.addEventListener('click', function () {
      var src = root.querySelector('.copy-src');
      var text = src ? src.value : '';
      var done = function () { var t = b.textContent; b.textContent = 'Copiado ✓'; setTimeout(function () { b.textContent = t; }, 1600); };
      if (navigator.clipboard) { navigator.clipboard.writeText(text).then(done, function () {}); }
    });
  });
})();
JS;

        return '<script>' . strtr($js, [
            '__HOST__' => $hostId,
            '__CONSOLE__' => (string)json_encode($console, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP),
        ]) . '</script>';
    }

    private static function css(): string
    {

        return <<<'CSS'
.wcx, .wcx * { box-sizing: border-box; }
.wcx {
  --bg: #0b0f17; --panel: #111722; --panel-2: #0d121b; --line: #232c3b; --line-2: #2e3a4d;
  --text: #e6e9ef; --muted: #8b95a7; --red: #ff5c7a; --amber: #fbbf24; --blue: #60a5fa;
  --violet: #a78bfa; --green: #34d399; --code-hl: rgba(255, 92, 122, .14);
  --mono: ui-monospace, "JetBrains Mono", SFMono-Regular, Menlo, Consolas, monospace;
  font: 14px/1.55 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
  color: var(--text); -webkit-font-smoothing: antialiased; text-align: left; color-scheme: dark;
}
.wcx a { color: inherit; text-decoration: none; }
.wcx .muted { color: var(--muted); }
.wcx b { font-weight: 600; }

/* botões */
.wcx .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px;
  border: 1px solid var(--line-2); background: var(--panel); color: var(--text); font: 600 13px/1 system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
  cursor: pointer; transition: background .15s, border-color .15s; }
.wcx .btn:hover { background: #1a2230; border-color: #3b4a61; }
.wcx .btn.primary { background: var(--text); color: var(--bg); border-color: var(--text); }
.wcx .btn.primary:hover { background: #fff; }
.wcx .btn.small { padding: 6px 10px; font-size: 12px; margin-top: 10px; }

/* página de erro fatal */
.wcx.fatal-page { position: absolute; inset: 0; overflow: auto; background:
  radial-gradient(900px 420px at 15% -10%, rgba(255, 92, 122, .16), transparent 60%), var(--bg); }
#wc-php-error > .wcx.fatal-page { position: static; min-height: 100vh; }
.wcx .wrap { max-width: 1080px; margin: 0 auto; padding: 48px 24px 64px; display: grid; gap: 20px; }
.wcx .hero { display: grid; gap: 10px; padding-bottom: 8px; }
.wcx .eyebrow { display: inline-flex; align-items: center; gap: 8px; color: var(--red); font-weight: 600;
  font-size: 12px; letter-spacing: .06em; text-transform: uppercase; }
.wcx .pulse { width: 8px; height: 8px; border-radius: 50%; background: var(--red);
  box-shadow: 0 0 0 0 rgba(255, 92, 122, .6); animation: wcx-pulse 1.8s infinite; }
@keyframes wcx-pulse { 70% { box-shadow: 0 0 0 10px rgba(255, 92, 122, 0); } 100% { box-shadow: 0 0 0 0 rgba(255, 92, 122, 0); } }
.wcx .type { margin: 6px 0 0; color: var(--muted); font: 600 13px/1.2 var(--mono); }
.wcx h1 { margin: 0; font-size: clamp(22px, 3.2vw, 32px); line-height: 1.25; font-weight: 700;
  letter-spacing: -.01em; overflow-wrap: anywhere; }
.wcx .where { justify-self: start; font: 14px/1.4 var(--mono); color: var(--blue); overflow-wrap: anywhere; }
.wcx .where:hover { text-decoration: underline; }
.wcx .where b, .wcx .loc b, .wcx .declared b { color: var(--amber); }
.wcx .declared { margin: -4px 0 0; color: var(--muted); font-size: 13px; }
.wcx .declared a { font-family: var(--mono); }
.wcx .declared a:hover { color: var(--blue); }
.wcx .actions { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
.wcx .hint { border: 1px solid rgba(251, 191, 36, .35); background: rgba(251, 191, 36, .07);
  border-radius: 12px; padding: 14px 16px; }
.wcx .hint strong { color: var(--amber); font-size: 13px; }
.wcx .hint p { margin: 4px 0 0; }
.wcx code { font: 12.5px var(--mono); background: rgba(255, 255, 255, .07); padding: 1px 6px; border-radius: 5px; }
.wcx .card { background: var(--panel); border: 1px solid var(--line); border-radius: 12px; overflow: hidden; }
.wcx .card h2 { margin: 0; padding: 12px 16px; font-size: 12px; text-transform: uppercase; letter-spacing: .08em;
  color: var(--muted); border-bottom: 1px solid var(--line); display: flex; align-items: center; gap: 8px; }
.wcx .card > p { margin: 0; padding: 14px 16px; }
.wcx .count { background: var(--line-2); color: var(--text); border-radius: 999px; padding: 1px 8px; font-size: 11px; }
.wcx .stack { list-style: none; margin: 0; padding: 6px 0; counter-reset: f; }
.wcx .frame { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 4px 16px; padding: 8px 16px;
  font: 12.5px/1.5 var(--mono); border-left: 2px solid transparent; }
.wcx .frame:first-child { border-left-color: var(--red); background: rgba(255, 92, 122, .06); }
.wcx .frame .fn { color: var(--text); overflow-wrap: anywhere; }
.wcx .frame:first-child .fn { color: var(--red); }
.wcx .frame .loc { color: var(--muted); overflow-wrap: anywhere; text-align: right; }
.wcx .frame .loc:hover { color: var(--blue); }
.wcx .frame.vendor { opacity: .5; }
.wcx footer { color: var(--muted); font-size: 12px; overflow-wrap: anywhere; }

/* trecho de código */
.wcx .code { font: 12.5px/1.65 var(--mono); background: var(--panel-2); overflow-x: auto; padding: 8px 0; }
.wcx .code > div { display: flex; min-width: max-content; padding-right: 16px; }
.wcx .code .ln { flex: none; width: 56px; padding-right: 14px; text-align: right; color: #4b5568; user-select: none; }
.wcx .code .src { white-space: pre; color: #c9d1dc; }
.wcx .code .hl { background: var(--code-hl); box-shadow: inset 3px 0 0 var(--red); }
.wcx .code .hl .ln { color: var(--red); font-weight: 700; }
.wcx .t-var { color: #7dd3fc; } .wcx .t-str { color: #a5e3a1; } .wcx .t-cmt { color: #6b7686; font-style: italic; }
.wcx .t-kw { color: #c4a6ff; } .wcx .t-num { color: #fdba74; } .wcx .t-tag { color: #ff8fa3; }
.wcx .t-id { color: #f0d58a; } .wcx .t-html { color: #8b95a7; }

/* lista de avisos (dock e página fatal) */
.wcx .list { overflow: auto; }
.wcx .item { border-bottom: 1px solid var(--line); }
.wcx .item:last-child { border-bottom: 0; }
.wcx .item summary { list-style: none; cursor: pointer; display: grid; align-items: baseline; gap: 4px 10px;
  grid-template-columns: auto minmax(0, 1fr) auto; padding: 10px 14px; }
.wcx .item summary::-webkit-details-marker { display: none; }
.wcx .item summary:hover { background: rgba(255, 255, 255, .03); }
.wcx .item .msg { font-weight: 500; overflow-wrap: anywhere; }
.wcx .item .loc { grid-column: 2 / -1; font: 12px var(--mono); color: var(--muted); overflow-wrap: anywhere; }
.wcx .item .times { grid-row: 1; grid-column: 3; color: var(--muted); font: 12px var(--mono); }
.wcx .item-body { padding: 0 14px 14px; }
.wcx .item-body .code { border: 1px solid var(--line); border-radius: 8px; }
.wcx .hint-inline { margin: 0 0 10px; color: #f3e3b5; font-size: 13px; }
.wcx .hint-inline strong { color: var(--amber); }
.wcx .chip { font: 700 10.5px/1 var(--mono); letter-spacing: .06em; text-transform: uppercase; padding: 4px 7px;
  border-radius: 5px; border: 1px solid currentColor; }
.wcx .chip.warn { color: var(--amber); background: rgba(251, 191, 36, .1); }
.wcx .chip.notice { color: var(--blue); background: rgba(96, 165, 250, .1); }
.wcx .chip.dep { color: var(--violet); background: rgba(167, 139, 250, .1); }
.wcx .chip.fatal { color: var(--red); background: rgba(255, 92, 122, .1); }

/* dock flutuante */
.wcx .dot { width: 8px; height: 8px; border-radius: 50%; background: var(--amber); flex: none;
  box-shadow: 0 0 0 3px rgba(251, 191, 36, .2); }
.wcx .dock { position: fixed; right: 16px; bottom: 16px; z-index: 2147483647; width: min(860px, calc(100vw - 32px));
  max-height: min(68vh, 620px); display: flex; flex-direction: column; background: rgba(11, 15, 23, .97);
  border: 1px solid var(--line-2); border-radius: 14px; overflow: hidden;
  box-shadow: 0 30px 70px -20px rgba(0, 0, 0, .7), 0 0 0 1px rgba(0, 0, 0, .3); animation: wcx-in .25s ease-out; }
@keyframes wcx-in { from { opacity: 0; transform: translateY(12px); } }
.wcx .dock-head { display: flex; align-items: center; gap: 10px; padding: 10px 10px 10px 14px;
  border-bottom: 1px solid var(--line); background: var(--panel); }
.wcx .dock-head .muted { font-size: 12px; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.wcx .icon { width: 28px; height: 28px; border-radius: 7px; border: 1px solid var(--line-2); background: transparent;
  color: var(--text); font: 600 16px/1 system-ui, sans-serif; cursor: pointer; }
.wcx .icon:hover { background: #1a2230; }
.wcx .pill { position: fixed; right: 16px; bottom: 16px; z-index: 2147483647; display: none; align-items: center;
  gap: 8px; padding: 9px 14px; border-radius: 999px; border: 1px solid var(--line-2); background: rgba(11, 15, 23, .95);
  color: var(--text); font: 600 13px/1 system-ui, sans-serif; cursor: pointer; box-shadow: 0 12px 30px -8px rgba(0, 0, 0, .6); }
.wcx .pill:hover { border-color: var(--amber); }
.wcx[data-collapsed="1"] .dock { display: none; }
.wcx[data-collapsed="1"] .pill { display: inline-flex; }
@media (max-width: 600px) {
  .wcx .dock { right: 8px; bottom: 8px; width: calc(100vw - 16px); }
  .wcx .dock-head .muted { display: none; }
  .wcx .frame { grid-template-columns: 1fr; }
  .wcx .frame .loc { text-align: left; }
  .wcx .wrap { padding: 28px 16px 48px; }
}

/* produção */
.wcx.prod { min-height: 100vh; display: grid; place-items: center; background: #f6f7f9; color: #1f2937; padding: 24px; }
.wcx.prod main { max-width: 420px; text-align: center; }
.wcx.prod h1 { font-size: 24px; margin: 0 0 8px; }
.wcx.prod p { margin: 0 0 20px; color: #6b7280; }
.wcx.prod .btn.primary { background: #111827; color: #fff; border-color: #111827; }
CSS;
    }

    // ----------------------------------------------------------------- helpers

    private static function excerpt(string $file, int $line, int $radius = 7): string
    {

        $rows = self::highlightLines($file, max(1, $line - $radius), $line + $radius);
        if ([] === $rows) {
            return '';
        }

        $html = '<div class="code">';
        foreach ($rows as $n => $code) {
            $html .= '<div' . ($n === $line ? ' class="hl"' : '') . '><span class="ln">' . $n . '</span>'
                . '<span class="src">' . ('' === $code ? ' ' : $code) . '</span></div>';
        }

        return $html . '</div>';
    }

    /**
     * Destaca a sintaxe usando o tokenizer do próprio PHP e devolve só as linhas pedidas.
     *
     * @return array<int, string>
     */
    private static function highlightLines(string $file, int $start, int $end): array
    {

        if (!isset(self::$sourceCache[$file])) {
            $source = is_file($file) && is_readable($file) ? @file_get_contents($file) : false;
            self::$sourceCache[$file] = (false === $source || strlen($source) > 2_000_000) ? '' : $source;
        }

        $source = self::$sourceCache[$file];
        if ('' === $source) {
            return [];
        }

        $rows = [];
        $current = 1;
        $buffer = '';
        foreach (token_get_all($source) as $token) {
            [$id, $text] = is_array($token) ? [$token[0], $token[1]] : [null, $token];
            $class = null === $id ? null : self::tokenClass($id, $text);

            foreach (explode("\n", $text) as $i => $part) {
                if ($i > 0) {
                    if ($current >= $start) {
                        $rows[$current] = $buffer;
                    }
                    $buffer = '';
                    $current++;
                    if ($current > $end) {
                        break 2;
                    }
                }

                if ('' === $part || $current < $start) {
                    continue;
                }

                $part = self::e(str_replace("\t", '    ', rtrim($part, "\r")));
                $buffer .= null === $class ? $part : '<span class="t-' . $class . '">' . $part . '</span>';
            }
        }

        if ($current >= $start && $current <= $end) {
            $rows[$current] = $buffer;
        }

        return $rows;
    }

    private static function tokenClass(int $id, string $text): ?string
    {

        return match (true) {
            T_WHITESPACE === $id => null,
            T_VARIABLE === $id => 'var',
            in_array($id, [T_CONSTANT_ENCAPSED_STRING, T_ENCAPSED_AND_WHITESPACE], true) => 'str',
            in_array($id, [T_COMMENT, T_DOC_COMMENT], true) => 'cmt',
            T_INLINE_HTML === $id => 'html',
            in_array($id, [T_LNUMBER, T_DNUMBER], true) => 'num',
            in_array($id, [T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, T_CLOSE_TAG], true) => 'tag',
            in_array($id, [T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED], true) => 'id',
            1 === preg_match('/^[a-z_]+$/i', $text) => 'kw',
            default => null,
        };
    }

    /**
     * @return list<array{file: string, line: int, call: string}>
     */
    private static function frames(Throwable $e): array
    {

        $frames = [['file' => $e->getFile(), 'line' => $e->getLine(), 'call' => '']];
        foreach ($e->getTrace() as $f) {
            if (self::class === ($f['class'] ?? '') || !isset($f['file'])) {
                continue;
            }

            $frames[] = [
                'file' => $f['file'],
                'line' => (int)($f['line'] ?? 0),
                'call' => ($f['class'] ?? '') . ($f['type'] ?? '') . $f['function'] . '()',
            ];
        }

        return $frames;
    }

    /**
     * Explicação em linguagem simples para os erros mais comuns.
     */
    private static function hintFor(string $message, int $severity): string
    {

        $hints = [
            '/^Undefined array key "?([^"]*)"?/' => 'O código tentou ler a chave `{1}` de um array, mas ela não existe. Normalmente o campo não veio do banco/formulário. Use `$var[\'{1}\'] ?? \'\'` ou confira com `isset()` antes de usar.',
            '/^Undefined variable \$(\w+)/' => 'A variável `${1}` foi usada sem ter sido definida antes neste ponto. Confira o nome, o `extract()` que deveria criá-la ou inicialize-a.',
            '/^Undefined property: ([\w\\\\]+)::\$(\w+)/' => 'O objeto `{1}` não tem a propriedade `{2}`. Confira o nome ou se ela foi declarada.',
            '/^Attempt to read property "(\w+)" on (\w+)/' => 'Tentou ler `->{1}` de algo que é `{2}`, não um objeto. Geralmente a consulta não retornou resultado. Verifique antes ou use `?->`.',
            '/^Trying to access array offset on (?:value of type )?(\w+)/' => 'Tentou acessar `[ ]` em um valor `{1}`, que não é array. Geralmente a consulta não retornou resultado; confira antes de acessar.',
            '/^Call to undefined function ([\w\\\\]+)/' => 'A função `{1}()` não existe: nome digitado errado, arquivo que a declara não foi incluído ou extensão do PHP não instalada.',
            '/^Call to undefined method ([\w\\\\]+)::(\w+)/' => 'A classe `{1}` não tem o método `{2}()`. Confira o nome do método ou a classe do objeto.',
            '/^Call to a member function (\w+)\(\) on (\w+)/' => 'Chamou `->{1}()` em algo que é `{2}`, não um objeto. A variável não foi inicializada ou a busca não encontrou nada.',
            '/^Class "([^"]+)" not found/' => 'A classe `{1}` não foi encontrada. Confira o `use`/namespace, o nome do arquivo e rode `composer dump-autoload` se for nova.',
            '/must be of type ([^,]+), (\S+) given/' => 'Um argumento chegou com o tipo errado (esperado `{1}`, recebido `{2}`). Com `strict_types` o PHP não converte sozinho; converta o valor antes.',
            '/syntax error/' => 'Erro de sintaxe: falta ou sobra algo (`;`, `)`, `}` ou aspas). O problema costuma estar na linha indicada ou logo antes dela.',
            '/^Allowed memory size/' => 'A memória do PHP acabou. Procure loops infinitos, recursão sem fim ou consultas que trazem dados demais.',
            '/^Maximum execution time/' => 'O script passou do tempo limite. Procure loops infinitos ou chamadas lentas (banco, API externa).',
            '/^Division by zero|^Modulo by zero/' => 'Divisão por zero. Verifique se o divisor é diferente de zero antes de calcular.',
            '/SQLSTATE/' => 'Erro no banco de dados. Confira nomes de tabela/coluna, os parâmetros da query e a conexão.',
            '/^Cannot redeclare/' => 'Uma função ou classe foi declarada duas vezes. Use `require_once`/`include_once` ou renomeie.',
            '/^Array to string conversion/' => 'Um array foi usado como texto (ex.: no `echo`). Use `implode()`, um índice específico ou `print_r()` para depurar.',
            '/Failed to open stream|No such file or directory/' => 'O arquivo não foi encontrado no caminho indicado. Confira o caminho e se o arquivo existe.',
            '/headers already sent|Cannot modify header information/i' => 'Algo foi impresso antes do `header()`/`session_start()`. Procure `echo`, espaços ou BOM antes do `<?php`.',
        ];

        foreach ($hints as $pattern => $text) {
            if (1 === preg_match($pattern, $message, $m)) {
                $text = strtr($text, ['{1}' => $m[1] ?? '', '{2}' => $m[2] ?? '']);

                return (string)preg_replace('/`([^`]+)`/', '<code>$1</code>', self::e($text));
            }
        }

        if (in_array($severity, [E_DEPRECATED, E_USER_DEPRECATED], true)) {
            return 'Funciona hoje, mas será removido numa versão futura do PHP. Vale atualizar o trecho quando possível.';
        }

        return '';
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function severityMeta(int $severity): array
    {

        return match ($severity) {
            E_WARNING, E_USER_WARNING, E_CORE_WARNING, E_COMPILE_WARNING => ['Warning', 'warn'],
            E_NOTICE, E_USER_NOTICE => ['Notice', 'notice'],
            E_DEPRECATED, E_USER_DEPRECATED => ['Deprecated', 'dep'],
            E_PARSE => ['Parse error', 'fatal'],
            default => ['Fatal error', 'fatal'],
        };
    }

    private static function severityLabel(int $severity): string
    {

        return self::severityMeta($severity)[0];
    }

    /**
     * @return array<string, mixed>
     */
    private static function jsonPayload(Throwable $e, bool $debug): array
    {

        $payload = [
            'error' => true,
            'message' => $debug ? $e->getMessage() : 'Erro interno',
            'type' => (new ReflectionClass($e))->getShortName(),
            'code' => $e->getCode(),
        ];

        if ($debug) {
            $payload['where'] = self::relPath($e->getFile()) . ':' . $e->getLine();
            $payload['trace'] = \array_slice(
                \array_map(
                    static fn(array $f): string => rtrim(self::relPath($f['file']) . ':' . $f['line'] . ' ' . $f['call']),
                    self::frames($e)
                ),
                0,
                6
            );
        }

        return $payload;
    }

    private static function isHtmlResponse(): bool
    {

        foreach (headers_list() as $h) {
            if (0 === stripos($h, 'Location:')) {
                return false;
            }

            if (0 === stripos($h, 'Content-Type:') && false === stripos($h, 'text/html')) {
                return false;
            }
        }

        return true;
    }

    private static function editorUrl(string $file, int $line): string
    {

        return sprintf(self::EDITOR_URL, str_replace('%2F', '/', rawurlencode($file)), $line);
    }

    private static function relPath(string $file): string
    {

        $root = dirname(__DIR__, 2) . '/';

        return str_starts_with($file, $root) ? substr($file, strlen($root)) : $file;
    }

    private static function e(string $value): string
    {

        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function clearOutputBuffers(): void
    {

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
    }

    private static function isDebugEnabled(): bool
    {

        if (defined('APP_DEBUG')) {
            $value = APP_DEBUG;
            if (is_numeric($value)) {
                return (int)$value === 1;
            }

            $normalized = strtolower(trim((string)$value));

            return in_array($normalized, ['1', 'true', 'on', 'yes'], true);
        }

        // Sem APP_DEBUG definido: em desenvolvimento (localhost) mostra os detalhes do
        // erro para facilitar o diagnóstico; em produção continua genérico.
        $host = strtolower(trim((string)($_SERVER['HTTP_HOST'] ?? '')));
        $colon = strpos($host, ':');
        if (false !== $colon) {
            $host = substr($host, 0, $colon);
        }

        return in_array($host, ['localhost', '127.0.0.1', '::1'], true);
    }
}
