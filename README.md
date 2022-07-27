# sites

Frontend em laravel para gerenciar sites. 

Permite interagir com AEGIR (gerenciador do Drupal) ou operar standalone (LOCAL).

## Histórico

1.1.0 - 7/2022

- implementado gerenciador local
- diversas alterações visuais
- deve-se atualizar o env com novas variáveis

1.0.7

- Fork do fflch/sites

## Deploy para desenvolvimento

    composer install
    cp .env.example .env

* Ajustar .env conforme necessário

```
php artisan key:generate
php artisan migrate
php artisan serve
```
