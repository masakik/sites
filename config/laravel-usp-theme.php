<?php

return [

    'title' => env('APP_NAME'),
    'dashboard_url' => '/',

    'logout_url' => 'logout',

    'logout_method' => 'post',

    'login_url' => 'login',

    'menu' => [
        [
            'text' => 'Solicitar um Site',
            'url'  => '/sites/create',
            'can'  => 'admin'
        ],
        [
            'text' => 'Meus Sites',
            'url'  => '/sites',
            'can'  => 'sites.create'
        ],
    ],

];
