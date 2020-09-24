<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Paginator::useBootstrap();

        // Fix para MariaDB ao rodar migrations
        Schema::defaultStringLength(191);

        // força https na produção
        if (\App::environment('production')) {
            \URL::forceScheme('https');
        }

        // gates
        Gate::resource('sites', 'App\Policies\SitePolicy');

        Gate::define('admin', function($user){
            $admins = explode(',',config('sites.admins'));
            return in_array($user->codpes, $admins);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
