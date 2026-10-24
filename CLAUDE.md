# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Visão geral

Site institucional construído sobre o **Zen Control** (fork do WorkControl 3.5), um CMS PHP próprio sem framework, renderizado no servidor com jQuery no front. O tema ativo é `themes/zevitor` (vem do `APP_THEME` no `.env`). O tema `themes/doripel` fica no `.gitignore`. Requer PHP 8.5 e MySQL.

## Comandos

O PHP do projeto fica em `/opt/homebrew/opt/php@8.5/bin/php`. Os scripts do Composer já usam esse binário.

```bash
composer cs:check                 # php-cs-fixer em dry-run com diff (PSR-12 + regras do .php-cs-fixer.dist.php)
composer cs:fix                   # aplica o php-cs-fixer
composer stub                     # regenera _app/Config/constants_stub.php a partir do ConfigLoader
php -l caminho/arquivo.php        # lint de um arquivo (o script "composer lint" só cobre admin/_siswc/products)
find admin/_siswc/team -name '*.php' | xargs -n1 php -l   # lint de um módulo
composer clean:minmin             # remove arquivos *.min.min* gerados por engano pelos watchers
```

Não há suíte de testes automatizados. Para validar, rode o `php -l` e teste no navegador em `https://localhost/<APP_LOCALHOST>`.

No localhost, `?__debug_error=1` força uma exceção e `?__debug_error=warning` força um aviso, para conferir a tela do `ErrorHandler`.

**Minificação:** file watchers do PhpStorm (`.idea/watcherTasks.xml`) geram `*.min.css` com `csso` e `*.min.js` com `uglifyjs` sempre que o arquivo-fonte é salvo. Quando editar `style.css`, `workcontrol.css` ou um `.js`, regenere o `.min` correspondente (`csso -i style.css -o style.min.css`) para os dois não ficarem divergentes.

## Arquitetura

### Bootstrap e configuração
- O `composer.json` faz autoload PSR-4 de `App\` → `_app/` e carrega `_app/Config/bootstrap.php` via `files`. Por isso, um `require vendor/autoload.php` já executa o `ConfigLoader::boot()`.
- `_app/Config/ConfigLoader.php` é a fonte de verdade de **todas as constantes globais**. Ele lê `_app/Config/.env` (phpdotenv) e define, nesta ordem: nomes de tabelas (`DB_*`), credenciais (DEV ou PRODUCTION conforme o host), valores da tabela `ws_config` (`DB_CONF`), `Agency.inc.php`/`Client.inc.php`, `BASE`/`THEME`/`INCLUDE_PATH`/`REQUIRE_PATH`, flags de módulo (`APP_*`) e níveis de acesso (`LEVEL_WC_*`). Todas usam `defineIfNotDefined`, então quem define primeiro vence.
- Variável de ambiente `DISABLE_DB_ON_BOOT=1` pula a leitura do banco (o `bin/generate-constants-stub.php` usa isso).
- `constants_stub.php` existe só para o IDE autocompletar. Nunca é carregado em runtime.

### Front (site público)
- O `.htaccess` reescreve tudo para `index.php?url=...`. O `index.php` é o front controller e também o layout: `<head>`, CSS/JS do tema, `inc/header.php`, conteúdo e `inc/footer.php`.
- Ordem de resolução da rota (`$URL = explode('/', url)`): thank-you pages → landing pages → cartões linktree (os três vêm de tabelas no banco) → `page_name` em `DB_PAGES` (usa `page-<nome>.php` ou `pagina.php`) → `themes/<tema>/<URL[0]>.php` (e `cat-<URL[1]>.php` para `artigos`) → `<URL[0]>/<URL[1]>.php` → `404.php`.
- Os templates do tema usam `$Read`, `$URL` e `$SEO` herdados do `index.php`, e montam assets com `INCLUDE_PATH` (URL do tema) e `REQUIRE_PATH` (caminho de arquivo). Seções reutilizáveis ficam em `themes/zevitor/inc/` (`team-one.php`, `services-one.php`, `faq-one.php`...).
- O `?wctheme=<nome>` troca o tema via sessão para pré-visualização.

### Acesso a dados (`_app/Conn`)
`Read`, `Create`, `Update` e `Delete` encapsulam o PDO (`Conn::getConn()`) com prepared statements. Os parâmetros são passados como *parse string*:

```php
$Read->exeRead(DB_TEAM, 'WHERE user_id = :id AND user_status = :st', "id={$Id}&st=1");
$Read->fullRead('SELECT ... FROM ' . DB_PAGES . ' WHERE ...', 'k=v');
$Update->exeUpdate(DB_TEAM, $Data, 'WHERE user_id = :id', "id={$Id}");
$Read->getResult(); // array|null
```

Nos módulos, os objetos são instanciados com `$Read ??= new Read();`.

### Painel admin (`admin/`)
- `admin/dashboard.php?wc=<modulo>/<tela>` inclui `admin/_siswc/<modulo>/<tela>.php` (módulos customizados). Se não existir, cai em `admin/_sis/` (núcleo do WC). O menu lateral dos módulos customizados fica em `admin/_siswc/wc_menu.php`.
- Telas (`home.php`, `create.php`) começam checando `APP_<MOD>` e `$Admin['user_level'] >= LEVEL_WC_<MOD>`; se falhar, chamam `Check::accessBlocked()`.
- O formulário envia via jQuery (`admin/_js/workcontrol.js`) para `admin/_ajax/<Callback>.ajax.php` com os campos `callback` e `callback_action`. O dispatcher valida sessão e nível, faz `switch ($Case)` e responde JSON (`$jSON['trigger'] = Check::ajaxErro(...)`, `redirect`, etc.). Classes de comportamento usadas nas telas: `auto_save`, `j_delete_action`, `j_delete_action_confirm`.
- Uploads passam por `App\Models\Upload` para `uploads/`. Ao apagar um arquivo, confirme com `realpath` que ele está dentro de `uploads/` (veja `Team.ajax.php`).
- Seletor de ícones compartilhado: `admin/_tpl/icon-select.wc.php`, que espera `$IconField`, `$IconValue` e `$IconLabel` antes do `require`. A lista sai do `flaticon.css` do tema (`icon-list.wc.php`).

### Criar um módulo novo
1. Tabela no banco. Use o prefixo `zv_` para tabelas novas deste site (as antigas usam `ws_`) e documente o SQL em `DATABASELOG.md`.
2. No `ConfigLoader`, adicione `APP_<MOD>` em `defineApplicationModules`, `LEVEL_WC_<MOD>` em `defineLevelPermissions` e `DB_<MOD>` nas constantes de tabela. Depois rode `composer stub`.
3. Crie `admin/_siswc/<mod>/home.php` e `create.php`, `admin/_ajax/<Mod>.ajax.php` e a entrada no `wc_menu.php`.
4. Crie o template ou a seção no tema que lê a tabela.

## Convenções

- Respeite o estilo do `.php-cs-fixer.dist.php`: PSR-12, `no_alternative_syntax` (use chaves em vez de `endif`/`endforeach`) e imports em ordem alfabética.
- Código PHP novo deve ter `declare(strict_types=1)` quando o arquivo não for template misto.
- Em templates, escape a saída com `htmlspecialchars` e use o valor bruto só para checagens de arquivo (padrão do `team/create.php`).
- Em `UPDATE` vindo de POST, grave apenas as colunas de uma whitelist. Nunca repasse o `$PostData` inteiro.
- O `.github/agents` e o `.github/skills` têm os agentes e skills do projeto (WC module migrator, theme adapter, php-senior etc.). O script `.github/skills/wc_module_migrator/scripts/fix_php_legacy.sh` (`composer wc:module-dr` / `wc:module-apply`) moderniza módulos legados.
