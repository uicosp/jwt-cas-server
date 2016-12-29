<?php

namespace Uicosp\JwtCasServer;

use Illuminate\Support\ServiceProvider;

class CasServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('jwt-cas-server.php')
        ]);

        include __DIR__ . '/routes.php';
        $this->loadViewsFrom(__DIR__ . '/views/', 'JwtCasServer');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config.php', 'jwt-cas-server'
        );
    }
}
