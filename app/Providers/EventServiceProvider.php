<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Uspdev\UspTheme\Events\UspThemeParseKey;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        if (config('sites.chamados') != 'none') {
            Event::listen(function (UspThemeParseKey $event) {
                if (isset($event->item['key']) && ($event->item['key'] == 'chamados')) {
                    $event->item = [
                        'text' => '<span class="text-danger">Chamados</span>',
                        'url' => 'chamados',
                        // 'can'  => 'admin'
                    ];
                }
                return $event->item;
            });
        }
        
        // adicionando listener de login para poder redirecionar para
        // meus sites logo que logar
        Event::listen(Login::class, function ($event) {
            session()->flash('loginRedirect', true);
        });

    }
}
