<?php

namespace App\Providers;

use Crell\Serde\SerdeCommon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            SerdeCommon::class,
            fn () => new SerdeCommon()
        );

        $this->app->alias(SerdeCommon::class, 'serde');
    }
}
