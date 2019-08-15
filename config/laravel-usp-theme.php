<?php

return [

    'title' => env('APP_NAME'),
    'dashboard_url' => '/',

    'logout_url' => 'logout',

    'logout_method' => 'post',

    'login_url' => '/senhaunica/login',

    'menu' => [
        [
            'text' => 'Solicitar um Site',
            'url'  => '/sites/create',
            'can'  => 'sites.create'
        ],
        [
            'text' => 'Meus Sites',
            'url'  => '/sites',
            'can'  => 'sites.create'
        ],
        [
            'text' => 'Chamados',
            'url'  => '/chamados/abertos',
            'can'  => 'admin'
        ],
    ],

];
