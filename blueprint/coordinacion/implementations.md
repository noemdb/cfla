# Plan de Implementación: Rol `coordinacion` (Coordinador de Programa Educativo)

**Staff Engineer Blueprint**
_Autor:_ Claude Architect
_Última revisión:_ 2026-07-27

---

## Tabla de Contenidos

1. [Resumen Ejecutivo](#1-resumen-ejecutivo)
2. [Arquitectura Actual (AS-IS)](#2-arquitectura-actual-as-is)
3. [Cadena de Modelos](#3-cadena-de-modelos)
4. [Target (TO-BE)](#4-target-to-be)
5. [Estrategia de Implementación](#5-estrategia-de-implementación)
6. [Plan Detallado](#6-plan-detallado)
    - [Fase 1: Base de Datos y Modelo](#fase-1-base-de-datos-y-modelo)
    - [Fase 2: Middleware y Autorización](#fase-2-middleware-y-autorización)
    - [Fase 3: Servicios y Scope](#fase-3-servicios-y-scope)
    - [Fase 4: Rutas](#fase-4-rutas)
    - [Fase 5: Livewire Components](#fase-5-livewire-components)
    - [Fase 6: Navegación y Vistas](#fase-6-navegación-y-vistas)
    - [Fase 7: Seguridad y Validación](#fase-7-seguridad-y-validación)
    - [Fase 8: Testing](#fase-8-testing)
7. [ADRs (Architecture Decision Records)](#7-adrs)
8. [Dependencias y Roadmap](#8-dependencias-y-roadmap)
9. [Checklist de Rollback](#9-checklist-de-rollback)

---

## 1. Resumen Ejecutivo

### ¿Qué es el rol `coordinacion`?

Un **Coordinador de Programa Educativo** es un usuario responsable de supervisar y dar seguimiento a uno o varios Programas Educativos (`Peducativo`), identificado como `manager` de ese programa. Es un rol de **supervisión read-only + observaciones** — puede visualizar toda la información académica de su(s) programa(s) pero solo puede **registrar observaciones** en las planificaciones de evaluación (`Pevaluacion.observations`).

Es un rol reducido respecto a `is_planner`: no tiene capacidad de crear/editar/eliminar ninguna entidad (grados, secciones, pensums, profesores, etc.). Solo visualiza y comenta.

### Las 7 responsabilidades del rol

| # | Responsabilidad | Modelos involucrados | Capacidad |
|---|----------------|---------------------|-----------|
| 1 | **Ver sus datos** | `User` → `Profile` | Perfil visible |
| 2 | **Dashboard con indicadores** | `Peducativo` → `Pestudio` → KPIs | Mismos indicadores que `/app/planning/indicators` pero scoped a su(s) peducativo(s) |
| 3 | **Información académica: Pensums** | `Peducativo` → `Pestudio` → `Pensum` → `Asignatura` | Listar pensums de su(s) programa(s) |
| 4 | **Carga Académica** | `Peducativo` → `Pestudio` → `Pensum` → `Pevaluacion` | Visualizar pevaluacions (sin editar) |
| 5 | **Actividades de Planificación** | `Activity` → `Pevaluacion` | Ver formato/resumen de actividades + registrar `pevaluacion.observations` |
| 6 | **Lecciones LMS** | `Activity` → `LmsActivityPublication/Section/Resource/Link` | Visualizar contenido publicado |
| 7 | **Recursos compartidos** | `LmsActivityResource` | Listado de recursos descargables |

### Principio de diseño

> **Namespace propio, reuso de lógica.** El módulo `coordinacion` tiene su propio namespace completo (rutas, layout, componentes, vistas, servicios) pero reusa la lógica de negocio del módulo Planning. La estrategia es:
> 1. Independencia total: layout propio (`coordinacion.layouts.app`), navbar propio, componentes Livewire dedicados en `App\Livewire\Coordinacion\*`
> 2. Scoping: todo se filtra a `peducativos WHERE manager_id = auth()->id()` vía `CoordinacionScopeService`
> 3. Read-only: todas las vistas son modo consulta — sin botones de crear/editar/eliminar
> 4. Una sola acción de escritura: `pevaluacion.observations`
>
> El nuevo rol necesita: (1) columna + middleware, (2) `CoordinacionScopeService`, (3) 6 componentes Livewire dedicados + layout propio + navbar propio

---

## 2. Arquitectura Actual (AS-IS)

### Modelo de roles actual

| Columna `users` | Middleware | Rutas que protege |
|----------------|-----------|-------------------|
| `is_admin` | `IsAdmin` | `/admin/*` |
| `is_admin` / `is_diagnostic` | `IsAdminOrDiagnostic` | `/admin/*` (users, voting, educational) |
| `is_admin` / `is_planner` / `is_diagnostic` | `IsPlanner` | `/app/planning/*` (todos los CRUDs) |
| `is_profesor` / `is_admin` | `IsProfesor` | `/app/profesors/*` |
| `is_student` | `IsStudent` | `/app/estudiante/*` |

### Relaciones existentes que reusamos

```php
// Peducativo ya tiene manager_id → User
class Peducativo extends Model {
    public function manager() {
        return $this->belongsTo(User::class, 'manager_id');
    }
    // manager_id está en $fillable
    protected $fillable = [
        'pescolar_id', 'manager_id', 'deputy_id', 'assistant_id', ...
    ];
}

// Pestudio tiene peducativo_id + manager_id
class Pestudio extends Model {
    public function peducativo() {
        return $this->belongsTo(Peducativo::class, 'peducativo_id');
    }
    public function manager() {
        return $this->belongsTo(User::class, 'manager_id');
    }
}

// Pevaluacion ya tiene observations en fillable
class Pevaluacion extends Model {
    protected $fillable = [
        ..., 'observations', ...
    ];
    const COLUMN_COMMENTS = [
        'observations' => 'Observaciones',
    ];
}
```

### Lo que NO existe (necesario para el rol)

| Aspecto | Estado | Detalle |
|---------|--------|---------|
| Columna `is_coordinacion` | ❌ **Falta** | Migración pendiente |
| Middleware `IsCoordinacion` | ❌ **Falta** | Similar a `IsPlanner` pero con `is_coordinacion` |
| `getRoleLabelAttribute` → 'Coordinación' | ❌ **Falta** | Agregar en User model |
| `CoordinacionScopeService` | ❌ **Falta** | Scoping por `peducativo.manager_id` |
| Rutas `/app/coordinacion/*` | ❌ **Falta** | Grupo nuevo de rutas |

---

## 3. Cadena de Modelos

### Árbol completo de navegación (scoped)

```
User (is_coordinacion = true)
  │
  └── Peducativo (manager_id = user.id) ← ANCLA DEL SCOPE
        │
        ├── Pestudio (peducativo_id → peducativo.id)
        │     ├── Grado (pestudio_id → pestudio.id)
        │     └── Pensum (pestudio_id → pestudio.id)
        │           └── Pevaluacion (pensum_id → pensum.id) ← editable: observations
        │                 ├── Actividad de Planificación (pevaluacion_id → pevaluacion.id)
        │                 │     ├── Formato PDF (teaching sections)
        │                 │     └── Resumen PDF (description + achievements)
        │                 └── LmsActivityPublication (activity_id → activity.id)
        │                       ├── LmsActivitySection
        │                       ├── LmsActivityResource
        │                       ├── LmsActivityLink
        │                       └── LmsHtmlEmbed
        │
        └── (Sin acceso directo a: Profesor CRUD, Seccion CRUD, Grado CRUD, etc.)
```

### Scope SQL nativo

```sql
-- Peducativos del coordinador
SELECT * FROM peducativos
WHERE manager_id = {userId} AND status_active = 'true';

-- Pestudios del coordinador
SELECT p.* FROM pestudios p
JOIN peducativos pe ON pe.id = p.peducativo_id
WHERE pe.manager_id = {userId}
  AND p.status_active = 'true'
  AND p.planning_module = 1;

-- Pensums visibles
SELECT p.* FROM pensums p
JOIN pestudios pe ON pe.id = p.pestudio_id
JOIN peducativos ped ON ped.id = pe.peducativo_id
WHERE ped.manager_id = {userId};

-- Pevaluacions visibles
SELECT p.* FROM pevaluacions p
JOIN pensums pe ON pe.id = p.pensum_id
JOIN pestudios pes ON pes.id = pe.pestudio_id
JOIN peducativos ped ON ped.id = pes.peducativo_id
WHERE ped.manager_id = {userId};

-- Activities visibles
SELECT a.* FROM activities a
JOIN pevaluacions p ON p.id = a.pevaluacion_id
JOIN pensums pe ON pe.id = p.pensum_id
JOIN pestudios pes ON pes.id = pe.pestudio_id
JOIN peducativos ped ON ped.id = pes.peducativo_id
WHERE ped.manager_id = {userId};
```

---

## 4. Target (TO-BE)

### Nuevo modelo de roles

```
users.is_coordinacion  →  middleware IsCoordinacion  →  /app/coordinacion/*
                                                              ├── /                    → coordinacion.index (Dashboard)
                                                              ├── /pensums             → coordinacion.pensums
                                                              ├── /carga-academica     → coordinacion.carga-academica
                                                              ├── /activities          → coordinacion.activities
                                                              ├── /activities/format/{pev} → coordinacion.activities.format (PDF)
                                                              ├── /activities/resume/{pev} → coordinacion.activities.resume (PDF)
                                                              ├── /lecciones           → coordinacion.lessons
                                                              └── /recursos            → coordinacion.resources
```

### Jerarquía de middleware (similar a IsPlanner)

```
is_admin ──► pasa IsCoordinacion (admin bypass)
is_coordinacion ──► pasa IsCoordinacion
otros ──► 403
```

---

## 5. Estrategia de Implementación

### Decisión arquitectónica clave

El rol `coordinacion` es un **módulo completamente independiente** con su propio namespace. Aunque reusa lógica de negocio del módulo Planning, tiene su propia identidad:

1. **A nivel de ruta**: las rutas de coordinación pasan por `IsCoordinacion` middleware, bajo su propio prefix `/app/coordinacion/*`
2. **A nivel de layout**: layout dedicado `coordinacion.layouts.app` con navbar propio — **no** reusa el layout de planning
3. **A nivel de componente**: componentes Livewire dedicados en `App\Livewire\Coordinacion\*` que envuelven las queries scoped, separados de los CRUDs de Planning
4. **A nivel de vistas**: vistas Blade propias en `resources/views/coordinacion/` (layout) y `resources/views/livewire/coordinacion/` (componentes)
5. **A nivel de servicio**: servicio propio `CoordinacionScopeService`
6. **Los PDF**: se reusan los controladores existentes `ActivityPdfController` (son read-only y no requieren namespace propio)

### Orden lógico (bloqueante en cascada)

```
Fase 1: Migration (is_coordinacion column) + User Model
    │
    ▼
Fase 2: Middleware IsCoordinacion
    │
    ▼
Fase 3: CoordinacionScopeService
    │
    ├──► Fase 4a: Routes (/app/coordinacion/*)
    ├──► Fase 4b: Dashboard (reuse Indicator/index logic → scoped)
    ├──► Fase 4c: Pensums (reuse Pensum/IndexComponent → scoped + readonly)
    ├──► Fase 4d: Carga Académica (reuse Pevaluacion/Index logic → scoped + readonly)
    ├──► Fase 4e: Actividades (reuse Activities logic → scoped + readonly + observations)
    └──► Fase 4f: Lecciones + Recursos (reuse Lms monitores → scoped)
    │
    ▼
Fase 5: Navbar (coordinacion-items)
    │
    ▼
Fase 6: Testing
```

---

## 6. Plan Detallado

### Fase 1: Base de Datos y Modelo

#### 1.1 Migration — `add_is_coordinacion_to_users_table`

```php
<?php
// database/migrations/2026_07_27_000001_add_is_coordinacion_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_coordinacion')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_coordinacion')
                    ->default(false)
                    ->after('is_profesor')
                    ->comment('Coordinador de Programa Educativo');
                $table->index('is_coordinacion');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_coordinacion')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['is_coordinacion']);
                $table->dropColumn('is_coordinacion');
            });
        }
    }
};
```

#### 1.2 User Model — cambios

```php
// app/Models/User.php

// En $fillable (agregar después de 'is_profesor'):
'is_coordinacion',

// En $casts (agregar):
'is_coordinacion' => 'boolean',

// Nuevo helper method (después de isProfesor):
public function isCoordinacion(): bool
{
    return $this->is_coordinacion ?? false;
}

// Actualizar getRoleLabelAttribute — agregar antes de 'Planner':
if ($this->is_coordinacion) {
    return 'Coordinación';
}

// Actualizar getIsPlannerAttribute — la coordinacion NO hereda planner
// (sin cambios, pero evitar confusión: is_coordinacion ≠ is_planner)
```

#### 1.3 Asegurar Peducativos tienen manager_id

Este paso no requiere código — es configuración de datos. Verificar que los `Peducativo` existentes tengan `manager_id` asignado. Opcionalmente, seedear:

```php
// database/seeders/PeducativoSeeder.php (opcional)
foreach (Peducativo::whereNull('manager_id')->get() as $ped) {
    $ped->manager_id = User::where('is_coordinacion', true)->first()?->id;
    $ped->save();
}
```

---

### Fase 2: Middleware y Autorización

#### 2.1 Nuevo middleware `IsCoordinacion`

```php
<?php
// app/Http/Middleware/IsCoordinacion.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsCoordinacion
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && (Auth::user()->is_admin || Auth::user()->isCoordinacion())) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder al módulo de coordinación.');
    }
}
```

**Patrón:** idéntico a `IsPlanner` pero con `isCoordinacion()` en lugar de `is_planner`.

#### 2.2 Registrar en Kernel

```php
// app/Http/Kernel.php — en $middlewareAliases:
'isCoordinacion' => \App\Http\Middleware\IsCoordinacion::class,
```

#### 2.3 Policy para observaciones (opcional si usamos validación en componente)

```php
<?php
// app/Policies/PevaluacionObservationPolicy.php

namespace App\Policies;

use App\Models\User;
use App\Models\app\Academy\Pevaluacion;

class PevaluacionObservationPolicy
{
    /**
     * El coordinador puede editar observations si la pevaluacion
     * pertenece a un pensum de su(s) peducativo(s).
     */
    public function updateObservations(User $user, Pevaluacion $pevaluacion): bool
    {
        if ($user->is_admin) return true;

        return Peducativo::where('manager_id', $user->id)
            ->whereHas('pestudios.pensums.pevaluacions', fn($q) => $q->where('id', $pevaluacion->id))
            ->exists();
    }
}
```

---

### Fase 3: Servicios y Scope

#### 3.1 `CoordinacionScopeService`

```php
<?php
// app/Services/Lms/CoordinacionScopeService.php

namespace App\Services\Lms;

use App\Models\User;
use App\Models\app\Academy\Peducativo;
use App\Models\app\Academy\Pestudio;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityResource;
use Illuminate\Support\Collection;

class CoordinacionScopeService
{
    protected ?Collection $peducativoIds = null;
    protected ?Collection $pestudioIds = null;

    public function __construct(
        protected User $user
    ) {}

    /**
     * IDs de Peducativos donde el user es manager.
     */
    public function getPeducativoIds(): Collection
    {
        if ($this->peducativoIds !== null) {
            return $this->peducativoIds;
        }

        if ($this->user->is_admin) {
            // Admin ve todos los peducativos activos
            return $this->peducativoIds = Peducativo::where('status_active', 'true')
                ->pluck('id');
        }

        return $this->peducativoIds = Peducativo::where('manager_id', $this->user->id)
            ->where('status_active', 'true')
            ->pluck('id');
    }

    /**
     * Peducativos completos del coordinador.
     */
    public function getPeducativos(): Collection
    {
        $ids = $this->getPeducativoIds();
        if ($ids->isEmpty()) return collect();

        return Peducativo::whereIn('id', $ids)
            ->where('status_active', 'true')
            ->orderBy('order')
            ->get();
    }

    /**
     * IDs de Pestudios de los peducativos del coordinador.
     */
    public function getPestudioIds(): Collection
    {
        if ($this->pestudioIds !== null) {
            return $this->pestudioIds;
        }

        $peducativoIds = $this->getPeducativoIds();
        if ($peducativoIds->isEmpty()) return $this->pestudioIds = collect();

        return $this->pestudioIds = Pestudio::whereIn('peducativo_id', $peducativoIds)
            ->where('status_active', 'true')
            ->where('planning_module', 1)
            ->pluck('id');
    }

    /**
     * Pestudios completos del coordinador.
     */
    public function getPestudios(): Collection
    {
        $ids = $this->getPestudioIds();
        if ($ids->isEmpty()) return collect();

        return Pestudio::whereIn('id', $ids)
            ->where('status_active', 'true')
            ->get();
    }

    /**
     * Aplica scope de peducativo a query de Pestudio.
     */
    public function scopePestudios($query)
    {
        $peducativoIds = $this->getPeducativoIds();
        if ($peducativoIds->isEmpty()) return $query->whereRaw('1 = 0');

        return $query->whereIn('peducativo_id', $peducativoIds);
    }

    /**
     * Aplica scope de peducativo a query de Pensum.
     */
    public function scopePensums($query)
    {
        $pestudioIds = $this->getPestudioIds();
        if ($pestudioIds->isEmpty()) return $query->whereRaw('1 = 0');

        return $query->whereIn('pestudio_id', $pestudioIds);
    }

    /**
     * Aplica scope de peducativo a query de Pevaluacion.
     */
    public function scopePevaluacions($query)
    {
        $pestudioIds = $this->getPestudioIds();
        if ($pestudioIds->isEmpty()) return $query->whereRaw('1 = 0');

        return $query
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereIn('pensums.pestudio_id', $pestudioIds)
            ->select('pevaluacions.*');
    }

    /**
     * Aplica scope de peducativo a query de Activity.
     */
    public function scopeActivities($query)
    {
        $pestudioIds = $this->getPestudioIds();
        if ($pestudioIds->isEmpty()) return $query->whereRaw('1 = 0');

        return $query
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereIn('pensums.pestudio_id', $pestudioIds)
            ->select('activities.*');
    }

    /**
     * Aplica scope de peducativo a query de LmsActivityResource.
     */
    public function scopeResources($query)
    {
        $pestudioIds = $this->getPestudioIds();
        if ($pestudioIds->isEmpty()) return $query->whereRaw('1 = 0');

        return $query
            ->join('activities', 'lms_activity_resources.activity_id', '=', 'activities.id')
            ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereIn('pensums.pestudio_id', $pestudioIds)
            ->select('lms_activity_resources.*');
    }

    /**
     * Verifica si una Pevaluacion está dentro del scope del coordinador.
     */
    public function pevaluacionIsInScope(int $pevaluacionId): bool
    {
        return Pevaluacion::where('pevaluacions.id', $pevaluacionId)
            ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereIn('pensums.pestudio_id', $this->getPestudioIds())
            ->exists();
    }
}
```

#### 3.2 Trait para Livewire components

```php
<?php
// app/Livewire/Coordinacion/Concerns/HasCoordinacionScope.php

namespace App\Livewire\Coordinacion\Concerns;

use App\Services\Lms\CoordinacionScopeService;
use Illuminate\Support\Facades\Auth;

trait HasCoordinacionScope
{
    protected CoordinacionScopeService $coordinacionService;

    public function initializeHasCoordinacionScope(): void
    {
        $this->coordinacionService = app(CoordinacionScopeService::class, [
            'user' => Auth::user()
        ]);
    }

    protected function getCoordinacionService(): CoordinacionScopeService
    {
        return $this->coordinacionService;
    }
}
```

---

### Fase 4: Rutas

#### 4.1 Grupo de rutas para coordinación

```php
// routes/web.php — agregar junto al grupo planning:

Route::prefix('app/coordinacion')
    ->name('coordinacion.')
    ->middleware(['auth', 'isCoordinacion'])
    ->group(function () {

    // Dashboard con indicadores
    Route::get('/', \App\Livewire\Coordinacion\Dashboard::class)
        ->name('index');

    // Información Académica: Pensums
    Route::get('/pensums', \App\Livewire\Coordinacion\PensumList::class)
        ->name('pensums');

    // Carga Académica (Pevaluacions)
    Route::get('/carga-academica', \App\Livewire\Coordinacion\CargaAcademicaList::class)
        ->name('carga-academica');

    // Actividades de Planificación
    Route::get('/activities', \App\Livewire\Coordinacion\ActivityList::class)
        ->name('activities');
    Route::get('/activities/format/{pevaluacion}', [
        \App\Http\Controllers\Planning\ActivityPdfController::class, 'format'
    ])->name('activities.format');
    Route::get('/activities/resume/{pevaluacion}', [
        \App\Http\Controllers\Planning\ActivityPdfController::class, 'resume'
    ])->name('activities.resume');

    // Lecciones LMS
    Route::get('/lecciones', \App\Livewire\Coordinacion\LessonList::class)
        ->name('lessons');

    // Recursos Compartidos
    Route::get('/recursos', \App\Livewire\Coordinacion\ResourceList::class)
        ->name('resources');
});
```

---

### Fase 5: Livewire Components

#### 5.1 Dashboard con Indicadores

```php
<?php
// app/Livewire/Coordinacion/Dashboard.php

namespace App\Livewire\Coordinacion;

use App\Services\Lms\CoordinacionScopeService;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Lapso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    use Concerns\HasCoordinacionScope;

    public $selectedLapsoId;
    public $lapsos;
    public $lapsoActive;

    // ─── KPI boxes ───
    public $totalActivities = 0;
    public $totalProfesoresActivos = 0;
    public $totalPevaluacions = 0;
    public $totalResources = 0;

    // ─── Indicators per Peducativo ───
    public $peducativoIndicators = [];

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
        $service = $this->getCoordinacionService();

        $this->lapsos = Lapso::orderBy('id')->get();
        $this->lapsoActive = Lapso::current();
        $this->selectedLapsoId = $this->lapsoActive?->id ?? $this->lapsos->first()?->id;

        $this->loadIndicators();
    }

    public function updatedSelectedLapsoId(): void
    {
        $this->loadIndicators();
    }

    public function loadIndicators(): void
    {
        $service = $this->getCoordinacionService();
        $peducativos = $service->getPeducativos();

        $this->peducativoIndicators = $peducativos->map(function ($peducativo) use ($service) {
            $pestudios = $service->getPestudios()->where('peducativo_id', $peducativo->id);

            $totalActivities = 0;
            $totalProfesores = collect();

            foreach ($pestudios as $pestudio) {
                $totalActivities += $pestudio->getActivitiesCount($this->selectedLapsoId);
                $totalProfesores = $totalProfesores->merge(
                    $pestudio->getProfesors($this->selectedLapsoId)
                );
            }

            $pestudioIds = $pestudios->pluck('id');

            return (object) [
                'peducativo'        => $peducativo,
                'pestudios'         => $pestudios,
                'activities_count'  => $totalActivities,
                'profesores_count'  => $totalProfesores->unique('id')->count(),
                'lessons_count'     => Activity::leftJoin('lms_activity_publications', 'activities.id', '=', 'lms_activity_publications.activity_id')
                    ->join('pevaluacions', 'activities.pevaluacion_id', '=', 'pevaluacions.id')
                    ->join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
                    ->whereIn('pensums.pestudio_id', $pestudioIds)
                    ->where('pevaluacions.lapso_id', $this->selectedLapsoId)
                    ->count(\Illuminate\Support\Facades\DB::raw('DISTINCT activities.id')),
                'grados_count'      => \Illuminate\Support\Facades\DB::table('grados')
                    ->whereIn('pestudio_id', $pestudioIds)
                    ->whereNull('deleted_at')
                    ->count(),
                'pensums_count'     => \Illuminate\Support\Facades\DB::table('pensums')
                    ->whereIn('pestudio_id', $pestudioIds)
                    ->whereNull('deleted_at')
                    ->count(),
            ];
        });

        $this->totalActivities = $this->peducativoIndicators->sum('activities_count');
        $this->totalProfesoresActivos = Profesor::where('status_active', 'true')
            ->has('pevaluacions')
            ->count();
        $this->totalPevaluacions = Pevaluacion::join('pensums', 'pevaluacions.pensum_id', '=', 'pensums.id')
            ->whereIn('pensums.pestudio_id', $service->getPestudioIds())
            ->count();
        $this->totalResources = \App\Models\app\Academy\Lms\LmsActivityResource::query()
            ->where('is_visible', true)
            ->whereHas('activity.pevaluacion.pensum', fn($q) => $q->whereIn('pestudio_id', $service->getPestudioIds()))
            ->count();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.coordinacion.dashboard')
            ->layout('coordinacion.layouts.app');
    }
}
```

#### 5.2 Listado de Pensums

```php
<?php
// app/Livewire/Coordinacion/PensumList.php

namespace App\Livewire\Coordinacion;

use App\Models\app\Academy\Pensum;
use Livewire\Component;
use Livewire\WithPagination;

class PensumList extends Component
{
    use WithPagination, Concerns\HasCoordinacionScope;

    public string $search = '';
    public $peducativoId = '';
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getCoordinacionService();

        $query = Pensum::with([
            'pestudio.peducativo',
            'asignatura',
            'grado',
        ]);

        $query = $service->scopePensums($query);

        if ($this->peducativoId) {
            $pestudioIds = $service->getPestudios()
                ->where('peducativo_id', $this->peducativoId)
                ->pluck('id');
            $query->whereIn('pestudio_id', $pestudioIds);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('asignatura', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('grado', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('pestudio', fn($sq) => $sq->where('name', 'like', "%{$this->search}%"));
            });
        }

        $pensums = $query->orderBy('pestudio_id')->paginate(20);

        $peducativos = $service->getPeducativos();

        return view('livewire.coordinacion.pensum-list', [
            'pensums'      => $pensums,
            'peducativos'  => $peducativos,
        ])->layout('coordinacion.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPeducativoId() { $this->resetPage(); }
}
```

#### 5.3 Carga Académica (Pevaluacions)

```php
<?php
// app/Livewire/Coordinacion/CargaAcademicaList.php

namespace App\Livewire\Coordinacion;

use App\Models\app\Academy\Pevaluacion;
use Livewire\Component;
use Livewire\WithPagination;

class CargaAcademicaList extends Component
{
    use WithPagination, Concerns\HasCoordinacionScope;

    public string $search = '';
    public $peducativoId = '';
    public $lapsoId = '';
    public $pensumId = '';
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getCoordinacionService();

        $query = Pevaluacion::with([
            'profesor:id,name,lastname,ci_profesor',
            'seccion:id,name,grado_id',
            'seccion.grado:id,name',
            'pensum.asignatura',
            'pensum.pestudio.peducativo',
            'lapso',
        ]);

        $query = $service->scopePevaluacions($query);

        if ($this->lapsoId) {
            $query->where('pevaluacions.lapso_id', $this->lapsoId);
        }
        if ($this->pensumId) {
            $query->where('pevaluacions.pensum_id', $this->pensumId);
        }
        if ($this->search) {
            $query->whereHas('profesor', fn($q) => $q->where('lastname', 'like', "%{$this->search}%"))
                  ->orWhereHas('pensum.asignatura', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                  ->orWhereHas('seccion', fn($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        $pevaluacions = $query->orderBy('pevaluacions.created_at', 'desc')
            ->paginate(20);

        $lapsos = \App\Models\app\Academy\Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');
        $peducativos = $service->getPeducativos();

        return view('livewire.coordinacion.carga-academica-list', [
            'pevaluacions' => $pevaluacions,
            'lapsos'       => $lapsos,
            'peducativos'  => $peducativos,
        ])->layout('coordinacion.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
    public function updatingPeducativoId() { $this->resetPage(); }
}
```

#### 5.4 Actividades de Planificación (con observaciones editables)

```php
<?php
// app/Livewire/Coordinacion/ActivityList.php

namespace App\Livewire\Coordinacion;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Lapso;
use Livewire\Component;
use Livewire\WithPagination;

class ActivityList extends Component
{
    use WithPagination, Concerns\HasCoordinacionScope;

    public string $search = '';
    public $peducativoId = '';
    public $lapsoId = '';
    protected $paginationTheme = 'tailwind';

    // ─── Edición de observaciones ───
    public ?int $editingPevId = null;
    public string $observations = '';

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getCoordinacionService();

        $query = Activity::with([
            'pevaluacion' => fn($q) => $q->with([
                'profesor:id,name,lastname',
                'seccion.grado',
                'pensum.asignatura',
                'pensum.pestudio.peducativo',
                'lapso',
            ]),
        ]);

        $query = $service->scopeActivities($query);

        if ($this->lapsoId) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapsoId));
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                  ->orWhere('thematic', 'like', "%{$this->search}%");
            });
        }

        $activities = $query->orderBy('activities.created_at', 'desc')
            ->paginate(15);

        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.coordinacion.activity-list', [
            'activities' => $activities,
            'lapsos'     => $lapsos,
        ])->layout('coordinacion.layouts.app');
    }

    // ─── Edición de observaciones ───

    public function editObservations(int $pevId): void
    {
        $pev = Pevaluacion::findOrFail($pevId);
        if (!$this->getCoordinacionService()->pevaluacionIsInScope($pevId)) {
            abort(403);
        }
        $this->editingPevId = $pevId;
        $this->observations = $pev->observations ?? '';
    }

    public function cancelEdit(): void
    {
        $this->editingPevId = null;
        $this->observations = '';
    }

    public function saveObservations(): void
    {
        $this->validate(['observations' => 'nullable|string|max:2000']);

        $pev = Pevaluacion::findOrFail($this->editingPevId);
        if (!$this->getCoordinacionService()->pevaluacionIsInScope($pev->id)) {
            abort(403);
        }

        $pev->update(['observations' => $this->observations ?: null]);
        $this->editingPevId = null;
        $this->observations = '';

        $this->dispatch('observations-saved');
        $this->notification()->success(
            title: 'Observaciones guardadas',
            description: 'Las observaciones se han actualizado correctamente.'
        );
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
}
```

#### 5.5 Lecciones LMS

```php
<?php
// app/Livewire/Coordinacion/LessonList.php

namespace App\Livewire\Coordinacion;

use App\Models\app\Academy\Activity;
use Livewire\Component;
use Livewire\WithPagination;

class LessonList extends Component
{
    use WithPagination, Concerns\HasCoordinacionScope;

    public string $search = '';
    public $peducativoId = '';
    public $lapsoId = '';
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getCoordinacionService();

        $query = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.pensum.pestudio.peducativo',
            'pevaluacion.profesor',
            'pevaluacion.lapso',
            'lmsPublication',
            'lmsSections',
        ]);

        $query = $service->scopeActivities($query);
        $query->whereHas('lmsPublication'); // Solo actividades con publicación LMS

        if ($this->lapsoId) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapsoId));
        }
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                  ->orWhere('thematic', 'like', "%{$this->search}%");
            });
        }

        $lessons = $query->orderBy('activities.created_at', 'desc')
            ->paginate(15);

        $lapsos = \App\Models\app\Academy\Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.coordinacion.lesson-list', [
            'lessons' => $lessons,
            'lapsos'  => $lapsos,
        ])->layout('coordinacion.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
}
```

#### 5.6 Recursos Compartidos

```php
<?php
// app/Livewire/Coordinacion/ResourceList.php

namespace App\Livewire\Coordinacion;

use App\Models\app\Academy\Lms\LmsActivityResource;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceList extends Component
{
    use WithPagination, Concerns\HasCoordinacionScope;

    public string $search = '';
    public $peducativoId = '';
    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->initializeHasCoordinacionScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getCoordinacionService();

        $query = LmsActivityResource::with([
            'activity.topic',
            'activity.pevaluacion.pensum.asignatura',
            'activity.pevaluacion.pensum.pestudio.peducativo',
            'activity.pevaluacion.profesor',
            'media',
        ])->where('is_visible', true);

        $query = $service->scopeResources($query);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('display_name', 'like', "%{$this->search}%")
                  ->orWhereHas('activity', fn($sq) => $sq->where('topic', 'like', "%{$this->search}%"));
            });
        }

        $resources = $query->orderBy('lms_activity_resources.created_at', 'desc')
            ->paginate(20);

        return view('livewire.coordinacion.resource-list', [
            'resources' => $resources,
        ])->layout('coordinacion.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
}
```

---

### Fase 6: Navegación y Vistas

#### 6.1 Navbar items para coordinación

```blade
{{-- resources/views/components/navbars/coordinacion-items.blade.php --}}
@if(Auth::user()->is_admin || Auth::user()->isCoordinacion())
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" @click.outside="open = false"
            class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {{ request()->routeIs('coordinacion.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300 hover:bg-white/5' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Coordinación
            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        <div x-show="open" x-cloak
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute left-0 mt-1 w-56 bg-gray-800/95 backdrop-blur-xl border border-white/10 rounded-lg shadow-2xl shadow-black/50 p-2 z-50">

            <a href="{{ route('coordinacion.index') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('coordinacion.index') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('coordinacion.pensums') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('coordinacion.pensums') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Pensums
            </a>
            <a href="{{ route('coordinacion.carga-academica') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('coordinacion.carga-academica') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Carga Académica
            </a>
            <a href="{{ route('coordinacion.activities') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('coordinacion.activities') || request()->routeIs('coordinacion.activities.*') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Actividades
            </a>
            <a href="{{ route('coordinacion.lessons') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('coordinacion.lessons') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Lecciones
            </a>
            <a href="{{ route('coordinacion.resources') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-emerald-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('coordinacion.resources') ? 'text-emerald-400 bg-emerald-500/5' : '' }}">
                <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Recursos
            </a>
        </div>
    </div>
@endif
```

#### 6.2 Layout dedicado de coordinación

Se crea un layout independiente con su propio navbar. El layout debe incluir:
- Estructura HTML base (doctype, head, scripts)
- Sidebar o top-nav con los items de coordinación
- `{{ $slot }}` para el contenido del componente Livewire

```blade
{{-- resources/views/coordinacion/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Coordinación') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @wireUiScripts
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-900 text-gray-100 antialiased">
    {{-- Top navbar con los items de coordinación --}}
    <nav class="sticky top-0 z-50 bg-gray-800/80 backdrop-blur-xl border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-4">
                    <a href="{{ route('coordinacion.index') }}" class="text-lg font-semibold text-emerald-400">
                        Coordinación
                    </a>
                    @include('components.navbars.coordinacion-items')
                </div>
                {{-- User menu / logout --}}
                <div class="flex items-center gap-3">
                    <span class="text-sm text-gray-400">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-red-400 transition-colors">
                            Salir
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Contenido principal --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    @livewireScripts
    @stack('scripts')
</body>
</html>
```

```blade
{{-- resources/views/components/navbars/coordinacion-items-mobile.blade.php --}}
{{-- Menú responsive para mobile (opcional) --}}
```

---

### Fase 7: Seguridad y Validación

#### 7.1 Matriz de autorización

| Ruta | Middleware | Scope | Lectura | Escritura |
|------|-----------|-------|---------|-----------|
| `/app/coordinacion/` | `IsCoordinacion` | `peducativo.manager_id` | Dashboard indicadores | ❌ |
| `/app/coordinacion/pensums` | `IsCoordinacion` | `pestudio.peducativo_id` | Lista pensums | ❌ |
| `/app/coordinacion/carga-academica` | `IsCoordinacion` | `pensum.pestudio.peducativo` | Pevaluacions | ❌ |
| `/app/coordinacion/activities` | `IsCoordinacion` | `activity.pevaluacion.pensum...` | Actividades + PDF | Solo `pevaluacion.observations` |
| `/app/coordinacion/lecciones` | `IsCoordinacion` | `activity.pevaluacion.pensum...` | Lecciones LMS | ❌ |
| `/app/coordinacion/recursos` | `IsCoordinacion` | `resource.activity...` | Recursos compartidos | ❌ |

#### 7.2 Validación de observaciones

```php
// Única acción de escritura del rol
'observations' => 'nullable|string|max:2000',
```

#### 7.3 Protecciones adicionales

```php
// En ActivityList::saveObservations() — doble verificación de scope:
if (!$this->getCoordinacionService()->pevaluacionIsInScope($pev->id)) {
    abort(403);
}

// En los controladores PDF — verificar scope:
if (!app(CoordinacionScopeService::class, ['user' => auth()->user()])
    ->pevaluacionIsInScope($pevaluacion->id)) {
    abort(403);
}
```

---

### Fase 8: Testing

#### 8.1 Pirámide de tests

```
    ┌────────────────────────────────────┐
    │  Feature: Flujo completo coordinacion │  ← 2 tests
    ├────────────────────────────────────┤
    │  Feature: Scope y acceso             │  ← 3 tests
    ├────────────────────────────────────┤
    │  Unit: Model + Service + Middleware  │  ← 5 tests
    └────────────────────────────────────┘
```

#### 8.2 Tests críticos

| Test | Tipo | Verifica |
|------|------|---------|
| `CoordinacionMiddlewareTest` | Feature | `is_coordinacion = true` → 200 |
| `CoordinacionMiddlewareTest` | Feature | `is_coordinacion = false` → 403 |
| `CoordinacionMiddlewareTest` | Feature | Admin bypass → 200 |
| `CoordinacionScopeTest` | Unit | `getPeducativoIds()` retorna solo peducativos donde es manager |
| `CoordinacionScopeTest` | Unit | `scopePevaluacions()` solo retorna pevs de sus peducativos |
| `CoordinacionScopeTest` | Unit | `pevaluacionIsInScope()` retorna false para pev fuera de scope |
| `CoordinacionScopeTest` | Unit | Admin ve todos los peducativos |
| `ObservationTest` | Feature | Coordinador puede guardar observaciones en pev de su scope |
| `ObservationTest` | Feature | Coordinador NO puede guardar observaciones en pev fuera de scope |
| `DashboardTest` | Feature | Dashboard carga indicadores correctos |
| `PensumListTest` | Feature | Lista de pensums filtrada por su peducativo |

#### 8.3 Factory support

```php
// database/factories/UserFactory.php
public function coordinacion(): static
{
    return $this->state(fn (array $attributes) => [
        'is_coordinacion' => true,
    ]);
}

// Asegurar que el factory de Peducativo tenga manager_id
// database/factories/PeducativoFactory.php (SI EXISTE)
'manager_id' => User::factory()->coordinacion(),
```

---

## 7. ADRs (Architecture Decision Records)

### ADR-001: `is_coordinacion` como columna booleana

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Columna booleana en `users` | Tabla pivote roles |
| **Razón** | Consistencia con `is_admin`, `is_planner`, `is_profesor` | |
| **Consecuencia** | Migración simple. Consistencia con el ecosistema existente | |

### ADR-002: Scope vía `Peducativo.manager_id`

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Scoping por `peducativo.manager_id = user.id` | Scoping por tabla pivote `user_peducativo` |
| **Razón** | `manager_id` ya existe en `Peducativo` y `Pestudio`. Es el modelo de datos existente y el user ya puede ser manager de múltiples peducativos | |
| **Consecuencia** | `CoordinacionScopeService` recorre la cadena `Peducativo → Pestudio → Pensum → Pevaluacion → Activity`. Si un coordinador no tiene peducativos asignados → todo vacío | |

### ADR-003: Namespace y layout dedicado para coordinación

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Namespace completo propio: layout, componentes, vistas, servicios | Reusar layout de planning |
| **Razón** | El coordinador tiene un conjunto de permisos diferente y no debe ver los mismos controles de navegación que un planner. Tener namespace propio permite evolucionar el módulo independientemente, sin acoplar cambios a planning. Además, es más seguro: un error en el layout de coordinación no afecta a planning y viceversa | |
| **Consecuencia** | ~2 archivos adicionales de layout. El navbar de coordinación solo muestra las 6 rutas scoped. Más fácil de testear y mantener a largo plazo | |

### ADR-004: Componentes Livewire dedicados vs reuso directo

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Componentes dedicados en `Livewire/Coordinacion/` | Reusar directamente `Planning/*/IndexComponent` con parámetros |
| **Razón** | Los componentes de Planning son full-CRUD. Separarlos evita confusión de permisos y permite una experiencia read-only limpia | |
| **Consecuencia** | ~6 nuevos componentes Livewire (simples, que envuelven queries scoped) | |

### ADR-005: Una sola acción de escritura (`pevaluacion.observations`)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | El coordinador solo puede escribir `observations` en Pevaluacion | Permitir editar activities, comentar, aprobar |
| **Razón** | El usuario pidió explícitamente "funciones reducidas". Es un rol de supervisión, no de gestión | |
| **Consecuencia** | Validación doble: frontend (Livewire) + backend (scope check). Sin capacidad de crear/editar/eliminar entidades | |

### ADR-006: PDF reusado de Planning

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Reusar `ActivityPdfController` existente para format/resume | Nuevos controladores dedicados |
| **Razón** | Los PDFs no modifican datos y son pura visualización. Mismos formatos, mismo controlador | |
| **Consecuencia** | Las rutas `coordinacion.activities.format` y `.resume` apuntan al mismo controller. Se agrega verificación de scope en el controller | |

---

## 8. Dependencias y Roadmap

### Mapa de archivos

```
NUEVOS:
  database/migrations/xxxx_add_is_coordinacion_to_users_table.php
  app/Http/Middleware/IsCoordinacion.php
  app/Services/Lms/CoordinacionScopeService.php
  app/Livewire/Coordinacion/Concerns/HasCoordinacionScope.php
  app/Livewire/Coordinacion/Dashboard.php
  app/Livewire/Coordinacion/PensumList.php
  app/Livewire/Coordinacion/CargaAcademicaList.php
  app/Livewire/Coordinacion/ActivityList.php
  app/Livewire/Coordinacion/LessonList.php
  app/Livewire/Coordinacion/ResourceList.php
  resources/views/livewire/coordinacion/dashboard.blade.php
  resources/views/livewire/coordinacion/pensum-list.blade.php
  resources/views/livewire/coordinacion/carga-academica-list.blade.php
  resources/views/livewire/coordinacion/activity-list.blade.php
  resources/views/livewire/coordinacion/lesson-list.blade.php
  resources/views/livewire/coordinacion/resource-list.blade.php
  resources/views/components/navbars/coordinacion-items.blade.php
  resources/views/components/navbars/coordinacion-items-mobile.blade.php
  resources/views/coordinacion/layouts/app.blade.php

MODIFICADOS:
  app/Models/User.php
  app/Http/Kernel.php
  routes/web.php
  app/Models/app/Academy/Peducativo.php                     (posible: agregar scope para manager)
  app/Http/Controllers/Planning/ActivityPdfController.php   (posible: agregar scope check)
  database/factories/UserFactory.php                        (+ coordinacion state)
```

### Namespace completo del módulo

```
Routes:       /app/coordinacion/*        → Route::name('coordinacion.*')
Middleware:   IsCoordinacion             → app/Http/Middleware/IsCoordinacion.php
Controllers:  (reusados: ActivityPdfController de Planning)
Services:     CoordinacionScopeService   → app/Services/Lms/CoordinacionScopeService.php
Livewire:     App\Livewire\Coordinacion\* → 6 componentes + 1 trait
Layout:       coordinacion.layouts.app   → resources/views/coordinacion/layouts/app.blade.php
Views:        livewire.coordinacion.*    → resources/views/livewire/coordinacion/*.blade.php
Navbar:       components.navbars.coordinacion-items → 2 partials (desktop + mobile)
```

### Timeline estimado

| Fase | Archivos | Tiempo |
|------|----------|--------|
| 1. Migration + Model | 2 | 20 min |
| 2. Middleware + Kernel | 2 | 15 min |
| 3. CoordinacionScopeService | 1 | 45 min |
| 4. Rutas | 1 | 15 min |
| 5a. Dashboard | 2 | 60 min |
| 5b. PensumList | 2 | 30 min |
| 5c. CargaAcademicaList | 2 | 30 min |
| 5d. ActivityList (con observations) | 2 | 45 min |
| 5e. LessonList | 2 | 30 min |
| 5f. ResourceList | 2 | 30 min |
| 6. Layout + Navbar | 3 | 30 min |
| 7. Testing | ~11 tests | 60 min |
| **Total** | **~25 archivos** | **~7.5 horas** |

---

## 9. Checklist de Rollback

- [ ] `php artisan migrate:rollback --step=1` (is_coordinacion)
- [ ] Remover `is_coordinacion` de `$fillable`, `$casts`, `isCoordinacion()`, `getRoleLabelAttribute()` en User model
- [ ] Eliminar `app/Http/Middleware/IsCoordinacion.php`
- [ ] Remover `'isCoordinacion'` de `$middlewareAliases` en Kernel
- [ ] Eliminar `app/Services/Lms/CoordinacionScopeService.php`
- [ ] Eliminar `app/Livewire/Coordinacion/` (directorio completo)
- [ ] Eliminar `resources/views/livewire/coordinacion/` (directorio completo)
- [ ] Eliminar `resources/views/coordinacion/` (directorio completo, layout)
- [ ] Eliminar `resources/views/components/navbars/coordinacion-items*.blade.php`
- [ ] Revertir rutas en `web.php` (eliminar grupo coordinacion)
- [ ] Revertir cambios en `ActivityPdfController.php` (si se agregó scope check)
- [ ] Eliminar `coordinacion()` state en `UserFactory.php` (si se agregó)
- [ ] `php artisan optimize:clear`
