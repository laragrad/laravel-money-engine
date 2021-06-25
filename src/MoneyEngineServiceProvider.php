<?php

namespace Laragrad\MoneyEngine;

use Illuminate\Support\ServiceProvider;

class MoneyEngineServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang/', 'laragrad/laravel-money-engine');
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-money-engine.php', 'laragrad.laravel-money-engine');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        
        // lang
        $this->publishes([
            __DIR__.'/../resources/lang/' => resource_path('lang/vendor/laragrad/laravel-money-engine'),
        ], 'lang');

        // config
        $this->publishes([
            __DIR__.'/../config/laravel-money-engine.php' => config_path('laragrad/laravel-money-engine.php'),
        ], 'config');
        
        // migrations
        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'migrations');
        
    }
}