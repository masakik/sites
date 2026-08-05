# Plano de implementação — Laravel 12 e PHP 8.3

Data: 05/08/2026  
Estado atual: Laravel 8.83.29 e PHP `^8.0`  
Objetivo: Laravel 12 com PHP 8.3

## Direção

Atualizar na ordem Laravel 8 → 9 → 10 → 11 → 12. Cada salto deve gerar um commit funcional antes de iniciar o próximo.

Este plano não inclui criação de testes, estratégia de branches, refatorações gerais nem modernização do frontend. Como não há testes da aplicação, depois de cada commit deve ser feito o checklist manual descrito no fim do documento.

Antes de começar:

1. Fazer backup do banco, `.env`, `storage`, código e `composer.lock`.
2. Fazer a atualização primeiro em ambiente local/homologação.
3. Nunca usar `--ignore-platform-reqs`.
4. Nunca executar `composer update` em produção.

## Composer: cuidado com os scripts atuais

O `composer.json` atual publica os assets do tema com `--force` e atualiza o WP-CLI depois de um update. Para não misturar essas ações com a atualização do Laravel, usar:

```bash
composer update --with-all-dependencies --no-scripts
php artisan package:discover --ansi
php artisan optimize:clear
```

Publicar os assets do tema e atualizar o WP-CLI separadamente, depois de revisar o impacto.

## Pacotes que precisam ser tratados

| Pacote | Implementação |
|---|---|
| `fideloper/proxy` | Remover no Laravel 9 e usar o middleware nativo. |
| `fruitcake/laravel-cors` | Remover no Laravel 9 e usar o CORS nativo. |
| `glorand/laravel-model-settings` | Não foi encontrado uso no projeto; confirmar e remover antes do Laravel 9. |
| `facade/ignition` | Substituir por `spatie/laravel-ignition` no Laravel 9. |
| `laravel/ui` | Não há `Auth::routes()`; confirmar uso em runtime e remover se foi usado apenas como scaffold. |
| `barryvdh/laravel-dompdf` | Atualizar para uma release compatível e conferir o relatório PDF. |
| `laravellegends/pt-br-validator` | Atualizar; se não suportar Laravel 12, substituir somente as regras usadas. |
| `nunomaduro/collision` | Atualizar para a série compatível com cada Laravel. |
| `phpunit/phpunit` | Atualizar para `^11.0` no Laravel 12, pois já é dependência do projeto. |
| `laravel/tinker` | A versão atual já declara suporte até Laravel 12; atualizar apenas se necessário. |
| `guzzlehttp/guzzle` | Manter na série 7 e validar chamadas ao Aegir. |

Também atualizar para versões compatíveis com Laravel 12/PHP 8.3:

- `uspdev/senhaunica-socialite`;
- `uspdev/laravel-replicado`;
- `uspdev/laravel-tools`;
- `uspdev/laravel-usp-theme`;
- `uspdev/laravel-usp-validators`;
- `uspdev/laravel-usp-faker`.

O `laravel-usp-faker` deve ser movido para `require-dev` se não for usado em produção.

Antes de cada salto, identificar bloqueios com:

```bash
composer why-not laravel/framework '^9.0'
composer show nome/do-pacote --all
```

Trocar `^9.0` pela versão que estiver sendo preparada. Se um pacote bloquear o upgrade: atualizar, remover se não usado, substituir ou, somente se indispensável, manter um fork temporário.

## Commit 1 — preparar as dependências

Mensagem sugerida:

```text
chore(deps): prepara dependências para atualização do Laravel
```

Implementação:

1. Confirmar e remover `glorand/laravel-model-settings`.
2. Confirmar se `laravel/ui` é necessário em runtime; remover se não for.
3. Mover `laravel-usp-faker` para `require-dev`, se aplicável.
4. Atualizar antecipadamente DOMPDF, validator PT-BR e pacotes USP que tenham uma versão compatível tanto com Laravel 8 quanto com versões seguintes.
5. Gerar e revisar o novo `composer.lock`.
6. Confirmar que Artisan e as rotas continuam carregando.

## Commit 2 — Laravel 8 para Laravel 9

Mensagem sugerida:

```text
chore(deps): atualiza Laravel 8 para Laravel 9
```

No `composer.json`:

- `php`: pelo menos `^8.0.2`;
- `laravel/framework`: `^9.0`;
- remover `facade/ignition`;
- adicionar `spatie/laravel-ignition` compatível;
- atualizar Collision, DOMPDF, validators e pacotes USP;
- atualizar o lock com `--with-all-dependencies --no-scripts`.

No código/configuração:

1. Trocar `Fideloper\Proxy\TrustProxies` pelo middleware nativo.
2. Substituir `Request::HEADER_X_FORWARDED_ALL` pela combinação explícita dos headers encaminhados.
3. Remover `fideloper/proxy`.
4. Migrar CORS para o framework e remover `fruitcake/laravel-cors`.
5. Validar a migração de SwiftMailer para Symfony Mailer nas classes de `App\Mail`.
6. Validar Flysystem 3, mantendo o disco local em `storage/app`.
7. Atualizar as configurações:
   - `QUEUE_DRIVER` → `QUEUE_CONNECTION`;
   - `FILESYSTEM_DRIVER` → `FILESYSTEM_DISK`;
   - manter `MAIL_MAILER` no lugar de `MAIL_DRIVER`.
8. Atualizar `.env.example` e os secrets do ambiente de deploy.

## Commit 3 — Laravel 9 para Laravel 10

Mensagem sugerida:

```text
chore(deps): atualiza Laravel 9 para Laravel 10
```

No `composer.json`:

- `php`: `^8.1`;
- `laravel/framework`: `^10.0`;
- `spatie/laravel-ignition`: série compatível;
- Collision: série compatível com Laravel 10;
- `laravel/ui`: `^4.0`, somente se o pacote tiver sido mantido;
- atualizar novamente pacotes USP, DOMPDF e validators;
- remover `minimum-stability: dev` ou mudar para `stable`.

No código/configuração:

1. Substituir eventuais `dispatchNow`/`dispatch_now` por `dispatchSync`/`dispatch_sync`.
2. Migrar eventuais propriedades `$dates` dos models para `$casts`.
3. Verificar conversões manuais de `DB::raw()` para string.
4. Confirmar que Form Requests não possuem método próprio chamado `after`.
5. Validar `stack`, `syslog`, `single` e `sites` com Monolog 3.

A inspeção inicial não encontrou esses usos incompatíveis no código próprio, mas a busca deve ser repetida depois da atualização dos pacotes.

## Commit 4 — Laravel 10 para Laravel 11

Mensagem sugerida:

```text
chore(deps): atualiza Laravel 10 para Laravel 11
```

No `composer.json` e ambiente:

- `php`: `^8.2`;
- `laravel/framework`: `^11.0`;
- Collision: `^8.1` ou versão compatível;
- atualizar ignition, DOMPDF, pacotes USP, Spatie Permission e demais dependências transitivas;
- confirmar curl `>=7.34`;
- confirmar SQLite `>=3.26`, caso seja usado.

No código/configuração:

1. Manter a estrutura atual da aplicação; não migrar middleware/providers para o skeleton enxuto do Laravel 11.
2. Executar com `E_ALL` e corrigir propriedades dinâmicas e assinaturas incompatíveis.
3. Procurar migrations com `change()` e repetir explicitamente todos os modificadores que devam permanecer.
4. Confirmar que nenhum model possui método/relacionamento chamado `casts`.
5. Validar Carbon 3 nas datas dos comentários, views e e-mails.
6. Validar cache do WordPress, Senha Única e permissões Spatie.

## Commit 5 — Laravel 11 para Laravel 12 e PHP 8.3

Mensagem sugerida:

```text
chore(deps): atualiza aplicação para Laravel 12 e PHP 8.3
```

No `composer.json` e ambiente:

- `php`: `^8.3`;
- `laravel/framework`: `^12.0`;
- `phpunit/phpunit`: `^11.0`;
- atualizar todas as dependências diretas e transitivas;
- confirmar Carbon 3;
- gerar o lock final usando PHP 8.3;
- configurar PHP 8.3 no servidor web, Artisan, cron e workers.

No código/configuração:

1. Manter explicitamente `storage/app` como raiz do disco `local`; o projeto já possui essa configuração.
2. Verificar o merge de arrays aninhados recebidos por requests.
3. Verificar uploads de imagem: SVG não é mais aceito automaticamente pela regra `image`.
4. Repetir a busca por UUIDs. Não foi encontrado `HasUuids` no código atual.
5. Confirmar que nenhum pacote instancia manualmente grammars ou blueprints do banco.
6. Revisar Carbon, container e classes do framework estendidas pelos pacotes.

## Commit 6 — atualizar a documentação

Mensagem sugerida:

```text
docs: atualiza requisitos para Laravel 12 e PHP 8.3
```

Atualizar o README com PHP 8.3, Laravel 12, extensões necessárias, instalação, deploy, cron, novas variáveis de ambiente e pacotes removidos/substituídos.

## Checklist manual após cada commit

Executar primeiro:

```bash
composer validate
composer check-platform-reqs
composer audit
php artisan --version
php artisan package:discover --ansi
php artisan optimize:clear
php artisan route:list
php artisan migrate:status
php artisan config:cache
php artisan route:cache
php artisan view:cache
git diff --check
```

Depois conferir manualmente:

- home, login, callback e logout da Senha Única;
- perfis admin, gerente e usuário;
- listagem, filtros, criação, edição, aprovação, ativação e desativação de sites;
- chamados, comentários e avisos;
- envio de e-mails;
- relatório PDF;
- consulta ao Replicado;
- Aegir e WP-CLI em um site de homologação;
- scheduler diário do WordPress;
- cache, sessões, storage e logs;
- páginas administrativas do `laravel-tools`;
- quantidade, URLs e middlewares de `php artisan route:list` comparados com o estado inicial.

Se algo falhar, corrigir no commit da versão atual antes de seguir para a próxima.

## Frontend

Laravel Mix 2, Bootstrap 4, jQuery, Axios e Lodash devem ser tratados depois, em commits separados. A atualização para Laravel 12 não exige migrar para Vite, Vue 3 ou Bootstrap 5, e misturar isso agora aumentaria o risco de regressão visual. Apenas atualizar o Laravel e o PHP, mantendo o frontend atual.

## Referências

- [Upgrade Laravel 9](https://laravel.com/docs/9.x/upgrade)
- [Upgrade Laravel 10](https://laravel.com/docs/10.x/upgrade)
- [Upgrade Laravel 11](https://laravel.com/docs/11.x/upgrade)
- [Upgrade Laravel 12](https://laravel.com/docs/12.x/upgrade)
