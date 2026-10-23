# Zen Control
Timeline de desenvolvimento do Zen Control
Profissional Admin Painel!

## Feed do Instagram

O carrossel usa a [Instagram API com Instagram Login da Meta](https://www.postman.com/meta/instagram/folder/6raa77c/instagram-api-with-instagram-login). É necessária uma conta profissional (Empresa ou Criador) e a permissão `instagram_business_basic`. Não use token de Página do Facebook: esta integração consulta `graph.instagram.com` diretamente.

### Como obter o token

1. Acesse [Meta for Developers](https://developers.facebook.com/apps/) e crie um aplicativo. Adicione o produto **Instagram** e selecione **API setup with Instagram Login** (os nomes dos menus podem variar).
2. Em **Generate access tokens**, adicione sua conta profissional do Instagram e autorize o aplicativo. Se a Meta solicitar um testador, adicione a conta em **App roles** e aceite o convite no Instagram antes de continuar.
3. Gere o token para essa conta com a permissão `instagram_business_basic`. Copie-o imediatamente e guarde-o em local seguro. O token gerado pelo painel deve ser de longa duração; tokens curtos do fluxo OAuth precisam ser trocados por um token longo antes de usar a renovação automática. Consulte [Get Started](https://developers.facebook.com/docs/instagram-platform/instagram-api-with-instagram-login/get-started) e [Access Token](https://developers.facebook.com/docs/instagram-platform/reference/access_token) da Meta.
4. Adicione ao arquivo `_app/Config/.env` (sem versioná-lo):

   ```dotenv
   INSTAGRAM_ACCESS_TOKEN=cole_o_token_aqui
   INSTAGRAM_API_VERSION=v25.0
   ```

5. Abra a página inicial e verifique se o carrossel aparece. Sem token ou publicações válidas, o bloco não aparece.

### Renovação automática

O site tenta renovar o token pela [API de renovação da Meta](https://developers.facebook.com/docs/instagram-platform/reference/refresh_access_token) ao carregar o feed: na primeira utilização e novamente quando restam até 14 dias de validade. O token renovado e seu vencimento ficam em `_app/Config/.instagram-token.json`, protegido contra acesso HTTP pelo `.htaccess` de `_app/Config` e ignorado pelo Git. O valor original em `.env` não é alterado. Após uma falha, a tentativa é repetida no dia seguinte; o erro é registrado no log do PHP sem expor o token.

Para renovar mesmo quando o site fica sem visitas, agende diariamente no cron do servidor, usando o caminho absoluto do PHP e do projeto:

```cron
0 3 * * * /caminho/para/php /caminho/para/o/projeto/bin/refresh-instagram-token.php
```

O usuário que executa o PHP/cron precisa poder criar arquivos em `_app/Config`; não conceda permissão pública de escrita. Se o servidor não usa Apache, bloqueie também o acesso HTTP a arquivos ocultos nessa pasta. Tokens de longa duração só podem ser renovados depois de 24 horas de emissão e antes de expirar. Se expirarem, forem revogados ou a conta perder a permissão, gere um novo token e substitua `INSTAGRAM_ACCESS_TOKEN` em `.env`.

O servidor consulta `/me/media`, guarda os resultados por uma hora em `cache/instagram-feed.json` e pode usar o último resultado por até 24 horas se a API estiver indisponível. Nunca coloque o token no tema ou no JavaScript.
