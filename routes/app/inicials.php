<?php

use App\Http\Controllers\Academico\InicialController as AcademicoInicialController;
use App\Http\Controllers\Evaluacion\InicialController as EvaluacionInicialController;
use App\Http\Controllers\Inicial\HomeInicialController;
use App\Http\Controllers\Inicial\Tab\EievaluationkController;
use App\Http\Controllers\Inicial\Tab\EifinalkController;
use App\Http\Controllers\Inicial\Tab\EiplanningbwkController;
use App\Http\Controllers\Inicial\Tab\EiplanningwkController;
use App\Http\Controllers\Inicial\Tab\EiprojectkController;
use App\Http\Controllers\Inicial\Tab\EispecialkController;
use App\Http\Controllers\Planning\InicialController as PlanningInicialController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Módulo de Educación Inicial (pestudio 6)
|--------------------------------------------------------------------------
| Blueprint: blueprint/inicial · decisiones D2 (RBAC por flags booleanos) y D4
| (paridad de estructura con el legacy, sin sus typos).
|
| Las 4 perspectivas sobre los MISMOS datos:
|
|   /app/inicials/*            docente      CRUD completo + formatos
|   /app/evaluacions/inicials/* coordinación revisión (escribe) + formatos
|   /app/plannings/inicials/*   planificación solo lectura + formatos
|   /app/academicos/inicials/*  académico   solo lectura limitada + formatos
|
| Los 6 documentos del módulo, en su prefijo canónico `ei*`:
|   eiplanningwk · eiplanningbwk · eiprojectk · eispecialk · eievaluationk · eifinalk
|
| ⚠️ En el legacy las rutas del quincenal se llamaban `eiplanningwbks` (wb≠bw)
| y los directorios de vistas de las perspectivas `inicilas`. Aquí se
| normaliza todo a `bw` y `inicials`.
|
| ⚠️ ESTAS RUTAS AÚN NO TIENEN CONTROLADOR (fases F2/F3/F5). F1 entrega el mapa
| de rutas y el acceso; los controladores `Inicial\Tab\*` llegan con el CRUD
| del docente (F2/F3) y los de solo lectura con las perspectivas (F5). No hay
| enlaces de navegación que apunten aquí todavía, así que el módulo es
| inalcanzable desde la UI.
|
*/

/*
|--------------------------------------------------------------------------
| 1. DOCENTE DE INICIAL  ·  auth + is_inicial
|--------------------------------------------------------------------------
| Los controladores son delgados: `index()` devuelve la vista que embebe el
| componente Livewire, `format()` la vista imprimible. El CRUD real lo hace el
| componente.
*/
Route::middleware(['auth', 'isInicial'])
    ->prefix('app/inicials')
    ->name('inicials.')
    ->group(function () {
        Route::get('/', [HomeInicialController::class, 'index'])->name('home');
        Route::get('/use-cases', [HomeInicialController::class, 'useCases'])->name('use-cases');
        Route::get('/users', [HomeInicialController::class, 'users'])->name('users');

        Route::resource('eiplanningwks', EiplanningwkController::class);
        Route::get('eiplanningwks/{eiplanningwk}/format', [EiplanningwkController::class, 'format'])
            ->name('eiplanningwks.format');

        Route::resource('eiplanningbwks', EiplanningbwkController::class);
        Route::get('eiplanningbwks/{eiplanningbwk}/format', [EiplanningbwkController::class, 'format'])
            ->name('eiplanningbwks.format');

        Route::resource('eiprojectks', EiprojectkController::class);
        Route::get('eiprojectks/{eiprojectk}/format', [EiprojectkController::class, 'format'])
            ->name('eiprojectks.format');

        Route::resource('eispecialks', EispecialkController::class);
        Route::get('eispecialks/{eispecialk}/format', [EispecialkController::class, 'format'])
            ->name('eispecialks.format');

        Route::resource('eievaluationks', EievaluationkController::class);
        Route::get('eievaluationks/{eievaluationk}/format', [EievaluationkController::class, 'format'])
            ->name('eievaluationks.format');

        Route::resource('eifinalks', EifinalkController::class);
        Route::get('eifinalks/{eifinalk}/format', [EifinalkController::class, 'format'])
            ->name('eifinalks.format');
    });

/*
|--------------------------------------------------------------------------
| 2. COORDINACIÓN DE EVALUACIÓN  ·  auth + isDiagnostic
|--------------------------------------------------------------------------
| NO es solo lectura: además de filtrar y ver, el coordinador escribe
| `observacion` en los planes y `recomendacion` en las evaluaciones
| (regla `min:5`). `pevaluacion.status_official` separa los informes
| oficiales de los generados por el componente.
*/
Route::middleware(['auth', 'isDiagnostic'])
    ->prefix('app/evaluacions/inicials')
    ->name('evaluacions.inicials.')
    ->group(function () {
        Route::get('/', [EvaluacionInicialController::class, 'index'])->name('index');

        foreach (['eiplanningwks', 'eiplanningbwks', 'eiprojectks', 'eispecialks', 'eievaluationks', 'eifinalks'] as $entidad) {
            Route::get($entidad.'/{id}', [EvaluacionInicialController::class, 'show'])
                ->whereNumber('id')
                ->defaults('entidad', $entidad)
                ->name($entidad.'.show');
            Route::get($entidad.'/{id}/format', [EvaluacionInicialController::class, 'format'])
                ->whereNumber('id')
                ->defaults('entidad', $entidad)
                ->name($entidad.'.format');
        }
    });

/*
|--------------------------------------------------------------------------
| 3. PLANIFICACIÓN  ·  auth + isPlanner
|--------------------------------------------------------------------------
| Solo lectura, renderizado en servidor (sin Livewire) con filtros por
| profesor / grado / sección.
*/
Route::middleware(['auth', 'isPlanner'])
    ->prefix('app/plannings/inicials')
    ->name('plannings.inicials.')
    ->group(function () {
        Route::get('/', [PlanningInicialController::class, 'index'])->name('index');

        foreach (['eiplanningwks', 'eiplanningbwks', 'eiprojectks', 'eispecialks', 'eievaluationks'] as $entidad) {
            Route::get($entidad.'/{id}', [PlanningInicialController::class, 'show'])
                ->whereNumber('id')
                ->defaults('entidad', $entidad)
                ->name($entidad.'.show');
            Route::get($entidad.'/{id}/format', [PlanningInicialController::class, 'format'])
                ->whereNumber('id')
                ->defaults('entidad', $entidad)
                ->name($entidad.'.format');
        }
    });

/*
|--------------------------------------------------------------------------
| 4. ACADÉMICO / DIRECCIÓN  ·  auth + isAdmin
|--------------------------------------------------------------------------
| Solo lectura y además LIMITADA: la perspectiva académica solo mira planes
| semanales y proyectos de aula, no los otros cuatro documentos.
*/
Route::middleware(['auth', 'isAdmin'])
    ->prefix('app/academicos/inicials')
    ->name('academicos.inicials.')
    ->group(function () {
        Route::get('/', [AcademicoInicialController::class, 'index'])->name('index');

        foreach (['eiplanningwks', 'eiprojectks'] as $entidad) {
            Route::get($entidad.'/{id}', [AcademicoInicialController::class, 'show'])
                ->whereNumber('id')
                ->defaults('entidad', $entidad)
                ->name($entidad.'.show');
            Route::get($entidad.'/{id}/format', [AcademicoInicialController::class, 'format'])
                ->whereNumber('id')
                ->defaults('entidad', $entidad)
                ->name($entidad.'.format');
        }
    });
