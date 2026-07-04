<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // La tabla personal_access_tokens ya existe en la BD
        // (fue creada manualmente o importada). Ignoramos la
        // migración de Sanctum para evitar error "table already exists".
        Sanctum::ignoreMigrations();
    }
}
