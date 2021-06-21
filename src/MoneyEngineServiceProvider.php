<?php

namespace Laragrad\MoneyEngine;

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

        // Vendor config and translations publishing
        $this->publishes([
            __DIR__.'/../resources/lang/' => resource_path('lang/vendor/laragrad/laravel-money-engine'),
            __DIR__.'/../config/laravel-money-engine.php' => config_path('laragrad/laravel-money-engine.php'),
        ]);

        // Merge vendor default config with published customized config
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-money-engine.php', 'laragrad.laravel-money-engine');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->bootRules();

    }
}