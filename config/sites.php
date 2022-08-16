<?php

return [
    'dnszone' => env('DNSZONE', null),
    'tutoriaisUrl' =>env('TUTORIAIS_URL', null),
    'unidades_usp' => env('UNIDADES_USP',false),
    'deploy_secret_key' => env('DEPLOY_SECRET_KEY', false),
    'subdominio' => env('HABILITAR_SUBDOMINIO', false),
    'chamados' => env('CHAMADOS', 'local'),
    'siteManager' => env('SITE_MANAGER', 'aegir'),
];
