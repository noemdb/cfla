<?php

namespace App\Providers;

use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Lms\LmsActivityContent;
use App\Observers\AreaConocimientoObserver;
use App\Observers\LmsActivityContentObserver;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
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
            $factoryClass = 'Database\\Factories\\'.$shortName.'Factory';
            if (class_exists($factoryClass)) {
                return $factoryClass;
            }

            // fallback al default de Laravel
            return 'Database\\Factories\\'.Str::after($modelFqn, 'App\\Models\\').'Factory';
        });

        // Observer de invalidación de caché para el scope de liderazgo (ADR-007).
        // Invalida las claves "leadership:{userId}:{areas|asignaturas}" cuando se
        // reasigna un leader_id en AreaConocimiento, evitando que un líder saliente
        // siga viendo datos del área y que un líder nuevo tenga que esperar el TTL.
        AreaConocimiento::observe(AreaConocimientoObserver::class);

        // Observer de sincronización de lms_activity_sections.content_type
        // (Spec "Campo content_type"): cualquier mutación de un contenido
        // (crear/editar/ocultar/eliminar) recalcula el tipo de su sección.
        LmsActivityContent::observe(LmsActivityContentObserver::class);

        // Observers de la bitácora de auditoría (Spec BINNACLE-001, Fase 1).
        // Registrar siempre al final: no deben colisionar con otros observers.
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\sys\Profile::observe(\App\Observers\ProfileObserver::class);
        \App\Models\app\Admon\Payment::observe(\App\Observers\PaymentObserver::class);

        // Observer genérico (Fase 2): cubre el resto de modelos críticos que
        // implementan App\Contracts\Auditable sin observer dedicado.
        \App\Models\app\Learner\Estudiant::observe(\App\Observers\AuditableModelObserver::class);
        \App\Models\app\Learner\Representant::observe(\App\Observers\AuditableModelObserver::class);
        \App\Models\app\Academy\Enrollment::observe(\App\Observers\AuditableModelObserver::class);
        \App\Models\app\Admon\Ingreso::observe(\App\Observers\AuditableModelObserver::class);
        \App\Models\app\Blog\Post::observe(\App\Observers\AuditableModelObserver::class);
    }
}
