<?php

$avisos =  [
    [
        'text' => 'Listar',
        'url'  => '/avisos',
    ],
    [
        'text' => 'Cadastrar',
        'url'  => '/avisos/create',
        'can'     => 'admin',
    ],
];

return [

    'title' => env('APP_NAME'),
    'dashboard_url' => '/',

    'logout_url' => '/logout',

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
            'text'    => 'Avisos',
            'submenu' => $avisos,
            'can'     => 'admin',
        ],
        [
            'text' => 'Chamados',
            'url'  => '/chamados',
            'can'  => 'admin'
        ],
        [
            'text' => 'Emails',
            'url'  => '/emails',
            'can'  => 'admin'
        ],
    ],

];
