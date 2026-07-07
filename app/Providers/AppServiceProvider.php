<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
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

        // La tabla personal_access_toks ya existe en la BD
        // (fue creada manualmente o importada). Ignoramos la
        // migración de Sanctum para evitar error "table already exists".
        Sanctum::ignoreMigrations();

        // Las factories están en Database\Factories\ClassNameFactory (por nombre corto).
        // El resolver por defecto de Laravel usa el namespace completo (App\Models\app\Academy\...)
        // así que redirigimos al nombre corto de la clase.
        Factory::guessFactoryNamesUsing(function (string $modelFqn) {
            $shortName = class_basename($modelFqn);
            $factoryClass = 'Database\\Factories\\' . $shortName . 'Factory';
            if (class_exists($factoryClass)) {
                return $factoryClass;
            }
            // fallback al default de Laravel
            return 'Database\\Factories\\' . Str::after($modelFqn, 'App\\Models\\') . 'Factory';
        });
    }
}
