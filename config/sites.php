<?php

return [
    'dnszone' => env('DNSZONE', false),
    'unidades_usp' => env('UNIDADES_USP',false),
    'deploy_secret_key' => env('DEPLOY_SECRET_KEY', false),
    'admins' => env('ADMINS'),
    'email_principal' => env('EMAIL_PRINCIPAL','example@examplo.com'),
    'usar_replicado' => env('USAR_REPLICADO',false),
];
