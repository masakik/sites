# solicita-site

Frontend em laravel para gerenciar sites. 

Backends disponiveís:

 -Drupal servidor pelo [Aegir(SaaS)](https://www.aegirproject.org/)



# deploy para desenvolvimento:

    composer install
    cp .env.example .env # editar com seu ambiente
    php artisan key:generate
    php artisan migrate
    php artisan vendor:publish --provider="JeroenNoten\LaravelAdminLte\ServiceProvider" --tag=assets --force
    php artisan serve
