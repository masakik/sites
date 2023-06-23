<?php

$avisos = [
    [
        'text' => 'Listar',
        'url' => 'avisos',
    ],
    [
        'text' => 'Cadastrar',
        'url' => 'avisos/create',
        'can' => 'admin',
    ],
];

$menu = [
    [
        'text' => 'Meus sites',
        'url' => 'sites',
        'can' => 'sites.create',
    ],
    [
        'text' => 'Solicitar um Site',
        'url' => 'sites/create',
        'can' => 'sites.create',
    ],
    [
        'text' => '<span class="text-danger">Avisos</span>',
        'submenu' => $avisos,
        'can' => 'admin',
    ],
    [
        'key' => 'chamados',
    ],
    [
        'text' => '<span class="text-danger">Emails</span>',
        'url' => 'emails',
        'can' => 'admin',
    ],
    [
        'text' => '<span class="text-danger">Relatório</span>',
        'url' => 'sites/relatorio',
        'can' => 'admin',
    ],
    [
        # este item de menu será substituido no momento da renderização
        'key' => 'menu_dinamico',
    ],
];

$right_menu = [
    [
        // menu utilizado para views da biblioteca senhaunica-socialite.
        'key' => 'senhaunica-socialite',
    ],
    [
        // menu utilizado para views da biblioteca senhaunica-socialite.
        'key' => 'laravel-tools',
    ],
    [
        'text' => '<span class="text-danger" title="Configurações da aplicação"><i class="fas fa-cog"></i></span>',
        'url' => 'settings',
        'can' => 'gerente',
    ],
];

return [
    # valor default para a tag title, dentro da section title.
    # valor pode ser substituido pela aplicação.
    'title' => config('app.name'),

    # USP_THEME_SKIN deve ser colocado no .env da aplicação
    'skin' => env('USP_THEME_SKIN', 'uspdev'),

    # chave da sessão. Troque em caso de colisão com outra variável de sessão.
    'session_key' => 'laravel-usp-theme',

    # usado na tag base, permite usar caminhos relativos nos menus e demais elementos html
    # na versão 1 era dashboard_url
    'app_url' => config('app.url'),

    # login e logout
    'logout_method' => 'POST',
    'logout_url' => 'logout',
    'login_url' => 'login',

    # menus
    'menu' => $menu,
    'right_menu' => $right_menu,
];
