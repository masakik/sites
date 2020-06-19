# sites

Frontend em laravel para gerenciar sites. 

# deploy para desenvolvimento:

    composer install
    cp .env.example .env

Variáveis obrigatórias no .env:

    DB_DATABASE=sites
    DB_USERNAME=master
    DB_PASSWORD=master

    SENHAUNICA_KEY=
    SENHAUNICA_SECRET=
    SENHAUNICA_CALLBACK_ID=
    SENHAUNICA_DEV=yes
    ADMINS=12334
    DNSZONE=.fflch.usp.br

    # false or true
    USAR_REPLICADO=true
    REPLICADO_HOST=
    REPLICADO_PORT=5005
    REPLICADO_DATABASE=fflch
    REPLICADO_USERNAME=fflch
    REPLICADO_PASSWORD=
    REPLICADO_CODUND=

EMAIL_PRINCIPAL=example@example.com

    php artisan key:generate
    php artisan migrate
    php artisan vendor:publish --provider="Uspdev\UspTheme\ServiceProvider" --tag=assets --force
    php artisan serve
