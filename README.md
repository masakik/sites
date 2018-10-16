# solicita-site

Frontend em laravel para gerenciar solicitação de sites. Os sites são gerenciados por um backend SaaS chamado [Aegir](https://www.aegirproject.org/), sendo a comunicação realizada através de requisições Restful API.

# Compile Assets:

    php artisan vendor:publish --provider="JeroenNoten\LaravelAdminLte\ServiceProvider" --tag=assets --force
