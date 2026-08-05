# Sites

Frontend em laravel para gerenciar sites.

Permite interagir com AEGIR (gerenciador do Drupal) ou operar standalone (LOCAL).

## Requisitos

- PHP 8.3;
- Laravel 12;
- Composer;
- MySQL/MariaDB;

## Produção

- Deve rodar no cron
    * * * * * cd /home/sistemas/sites && php artisan schedule:run >> /dev/null 2>&1

## Histórico

1.3.0 - 05/08/2026

- migrando para Laravel 12 e PHP 8.3

1.2.0 - 4/8/2022

- migrando para senhaunica-socialite de v2 para v4
- implementando busca por categorias
- reorganização das views

  1.1.0 - 7/2022

- implementado gerenciador local
- diversas alterações visuais
- deve-se atualizar o env com novas variáveis

  1.0.7

- Fork do fflch/sites

## Deploy para desenvolvimento

    composer install
    cp .env.example .env

- Ajustar .env conforme necessário

```
php artisan key:generate
php artisan migrate
php artisan serve
```
