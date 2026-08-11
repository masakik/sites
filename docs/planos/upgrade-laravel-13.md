# Plano de implementação — Laravel 13

Data: 07/08/2026  
Estado atual: Laravel 12.65.0 e PHP `^8.3`  
Objetivo: Laravel 13 com PHP 8.3

## Direção

Atualizar Laravel 12 → 13 em um único commit funcional. A versão 13 mantém o requisito de PHP 8.3, portanto não há mudança de runtime nesta etapa.

Antes de começar:

1. Fazer backup do banco, `.env`, `storage`, código e `composer.lock`.
2. Fazer a atualização primeiro em ambiente local/homologação.
3. Nunca usar `--ignore-platform-reqs`.
4. Nunca executar `composer update` em produção.

## Composer: cuidado com os scripts atuais

O `composer.json` publica os assets do tema com `--force` e atualiza o WP-CLI após um update. Para não misturar essas ações com a atualização do framework, usar:

```bash
composer update --with-all-dependencies --no-scripts
php artisan package:discover --ansi
php artisan optimize:clear
```

Publicar os assets do tema e atualizar o WP-CLI separadamente, depois de revisar o impacto.

## Dependências atualizadas

| Pacote | Versão para Laravel 13 |
|---|---|
| `laravel/framework` | `^13.0` |
| `laravel/tinker` | `^3.0` |
| `phpunit/phpunit` | `^12.0` |
| `laravellegends/pt-br-validator` | `^13.0` |
| `rap2hpoutre/laravel-log-viewer` | `3.1.0 as 2.5.0` |

As demais dependências devem ser atualizadas pelo Composer com `--with-all-dependencies`. Se alguma bloquear o resolvedor, verificar antes com `composer why-not laravel/framework '^13.0'` e atualizar ou substituir apenas o pacote incompatível.

`uspdev/laravel-tools` 1.6.0 ainda limita o visualizador de logs à série 2, incompatível com Laravel 13. O projeto declara explicitamente `rap2hpoutre/laravel-log-viewer` 3.1 com alias de compatibilidade `2.5.0`, preservando o painel administrativo e permitindo ao Composer usar uma versão que declara suporte ao framework 13. Remover esse alias assim que `laravel-tools` declarar suporte nativo à série 3.

## Alterações no código e configuração

Esta atualização mantém a estrutura e as configurações atuais da aplicação. O middleware `VerifyCsrfToken` continua disponível como alias de compatibilidade no Laravel 13 e não é necessário alterá-lo nesta etapa.

A inspeção inicial não encontrou uso de `upsert`, listeners de `QueueBusy`/`JobAttempted` ou nomes de view de paginação obsoletos.

## Checklist manual após a atualização

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
php artisan test
git diff --check
```

Configurar um banco exclusivo para testes antes de executar testes que acessam models ou controllers. A suíte atual não deve usar o banco de desenvolvimento ou produção.

Depois conferir manualmente:

- home, login, callback e logout da Senha Única;
- perfis admin, gerente e usuário;
- listagem, filtros, criação, edição, aprovação, ativação e desativação de sites;
- chamados, comentários e avisos;
- envio de e-mails e relatório PDF;
- consulta ao Replicado, Aegir e WP-CLI em homologação;
- scheduler diário do WordPress;
- cache, sessões, storage e logs;
- páginas administrativas do `laravel-tools`.

## Referências

- [Upgrade Laravel 13](https://laravel.com/docs/13.x/upgrade)
- [Release notes Laravel 13](https://laravel.com/docs/13.x/releases)
