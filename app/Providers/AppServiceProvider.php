<?php

namespace App\Providers;

use App\Models\app\Academy\AreaConocimiento;
use App\Observers\AreaConocimientoObserver;
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

        // Observer de invalidación de caché para el scope de liderazgo (ADR-007).
        // Invalida las claves "leadership:{userId}:{areas|asignaturas}" cuando se
        // reasigna un leader_id en AreaConocimiento, evitando que un líder saliente
        // siga viendo datos del área y que un líder nuevo tenga que esperar el TTL.
        AreaConocimiento::observe(AreaConocimientoObserver::class);
    }
}
