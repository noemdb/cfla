# Plan de Implementación: Rol `leadership` (Jefe de Área)

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
    - [Fase 3: Rutas](#fase-3-rutas)
    - [Fase 4: Servicios y Scope](#fase-4-servicios-y-scope)
    - [Fase 5: Livewire Components](#fase-5-livewire-components)
    - [Fase 6: Navegación y Vistas](#fase-6-navegación-y-vistas)
    - [Fase 7: Seguridad y Validación](#fase-7-seguridad-y-validación)
    - [Fase 8: Testing](#fase-8-testing)
7. [ADRs (Architecture Decision Records)](#7-adrs)
8. [Dependencias y Roadmap](#8-dependencias-y-roadmap)
9. [Checklist de Rollback](#9-checklist-de-rollback)

---

## 1. Resumen Ejecutivo

### ¿Qué es el rol `leadership`?

Un **Jefe de Área (Seguimiento)** es un usuario con capacidad de supervisar, comentar y aprobar las actividades de planificación y lecciones LMS de los profesores **asociados a sus áreas de conocimiento** asignadas.

### Las 3 responsabilidades del rol

| # | Responsabilidad | Modelos involucrados | Capacidades |
|---|----------------|---------------------|-------------|
| 1 | **Seguimiento y aprobación de actividades** | `AreaConocimiento` → `CampoConocimiento` → `Asignatura` → `Pensum` → `Pevaluacion` → `Activity` | Dejar comentarios, aprobar/rechazar, ver observaciones |
| 2 | **Seguimiento de lecciones LMS** | Misma cadena hasta `Activity` → `LmsActivityPublication/Section/Resource/Link/Log/HtmlEmbed` | Visualizar contenido publicado, auditoría de cambios |
| 3 | **Indicadores de profesores** | Misma cadena hasta `Pevaluacion.profesor_id` → `Profesor` → KPIs (IEE, IRE, metas de carga) | Dashboard con métricas de desempeño docente |

### Principio de diseño

> **Mínimo código nuevo, máximo reuso.** Las funcionalidades core ya existen:
> - `Planning\Activities\IndexComponent` ya tiene comentarios, observaciones, preview, aprobación
> - `Planning\Lms\LmsMonitor` y `ActivityAudit` ya monitorean contenido LMS
> - `Profesor` ya tiene métodos KPI (`getProfesorIEE`, `getProfesorIRE`, etc.)
>
> El nuevo rol solo necesita: (1) columna + middleware, (2) scoping por área, (3) dashboard de seguimiento

---

## 2. Arquitectura Actual (AS-IS)

### Modelo de roles actual

| Columna `users` | Middleware | Rutas que protege |
|----------------|-----------|-------------------|
| `is_admin` | `IsAdmin` | `/admin/*` (logs, DB backup) |
| `is_admin` / `is_diagnostic` | `IsAdminOrDiagnostic` | `/admin/*` (users, voting, educational) |
| `is_admin` / `is_planner` / `is_diagnostic` | `IsPlanner` | `/app/planning/*` (todos los CRUDs) |
| `is_profesor` / `is_admin` | `IsProfesor` | `/app/profesors/*` |
| `is_student` | `IsStudent` | `/app/estudiante/*` |

### Relaciones existentes que reusamos

```php
// AreaConocimiento (ya existe)
public function leader() {
    return $this->belongsTo(User::class, 'leader_id');  // FK existente
}
public function campo_conocimientos() {
    return $this->hasMany(CampoConocimiento::class, 'area_conocimiento_id');
}

// CampoConocimiento (pivote: area → asignatura)
public function area_conocimiento() { /* belongsTo AreaConocimiento */ }
public function asignatura() { /* belongsTo Asignatura */ }

// Activity tiene acceso a LMS
public function lmsPublication() { /* hasOne LmsActivityPublication */ }
public function lmsSections() { /* hasMany LmsActivitySection */ }
public function lmsResources() { /* hasMany LmsActivityResource */ }
public function lmsLogs() { /* hasMany LmsActivityLog */ }
```

---

## 3. Cadena de Modelos

### Árbol completo de navegación

```
AreaConocimiento (leader_id → users.id)
  │
  ├── CampoConocimiento (area_conocimiento_id)
  │     └── Asignatura (asignatura_id)
  │           └── Pensum (asignatura_id)
  │                 └── Pevaluacion (pensum_id)
  │                       ├── Activity (pevaluacion_id)
  │                       │     ├── LmsActivityPublication (activity_id)
  │                       │     ├── LmsActivitySection (activity_id)
  │                       │     ├── LmsActivityResource (activity_id)
  │                       │     ├── LmsActivityLink (activity_id)
  │                       │     ├── LmsActivityLog (activity_id)
  │                       │     └── LmsHtmlEmbed (activity_id)
  │                       │
  │                       └── Profesor (profesor_id) → KPIs (IEE, IRE, etc.)
  │
  └── pestudio() → Pestudio
  └── peducativo() → Peducativo
```

### Traducción a queries SQL

```sql
-- Obtener asignaturas bajo la supervisión de un líder
SELECT a.* FROM asignaturas a
JOIN campo_conocimientos cc ON cc.asignatura_id = a.id
JOIN area_conocimientos ac ON ac.id = cc.area_conocimiento_id
WHERE ac.leader_id = {userId};

-- Obtener actividades (vía la cadena completa)
SELECT act.* FROM activities act
JOIN pevaluacions pev ON pev.id = act.pevaluacion_id
JOIN pensums pen ON pen.id = pev.pensum_id
JOIN asignaturas a ON a.id = pen.asignatura_id
JOIN campo_conocimientos cc ON cc.asignatura_id = a.id
JOIN area_conocimientos ac ON ac.id = cc.area_conocimiento_id
WHERE ac.leader_id = {userId};

-- Obtener profesores asociados a las áreas del líder
SELECT DISTINCT p.* FROM profesors p
JOIN pevaluacions pev ON pev.profesor_id = p.id
JOIN pensums pen ON pen.id = pev.pensum_id
JOIN asignaturas a ON a.id = pen.asignatura_id
JOIN campo_conocimientos cc ON cc.asignatura_id = a.id
JOIN area_conocimientos ac ON ac.id = cc.area_conocimiento_id
WHERE ac.leader_id = {userId};
```

---

## 4. Target (TO-BE)

### Nuevo modelo de roles

```
users.is_leadership  →  middleware IsLeadership  →  /app/leadership/*
                                                       ├── /dashboard      → app.leadership.dashboard
                                                       ├── /activities     → app.leadership.activities
                                                       ├── /lessons        → app.leadership.lessons
                                                       └── /profesores     → app.leadership.profesores
```

**Importante:** Las rutas de leadership están AL MISMO NIVEL que planning, no anidadas. Esto evita que el middleware `isPlanner` interfiera con el middleware `isLeadership` (un Jefe de Área no necesariamente es planner).

### Jerarquía de acceso

```
is_admin ──► pasa TODOS los middleware (incluyendo isLeadership)
is_leadership ──► pasa isLeadership (NUEVO, independiente de isPlanner)
is_diagnostic ──► pasa isAdminOrDiagnostic, isPlanner
is_planner ──► pasa isPlanner unicamente
is_profesor ──► pasa isProfesor solamente
```

### Principio de herencia (patrón existente)

```php
// Admin siempre cuenta como leadership (mismo patrón que getIsPlannerAttribute)
public function getIsLeadershipAttribute()
{
    return $this->is_admin || ($this->attributes['is_leadership'] ?? false);
}
```

---

## 5. Estrategia de Implementación

### Orden lógico (bloqueante en cascada)

```
Fase 1: Migration + Model
    │
    ▼
Fase 2: Middleware + Kernel
    │
    ▼
Fase 3: LeadershipService (scoping)
    │
    ├──► Fase 4a: Rutas (grupo propio al nivel de planning)
    ├──► Fase 4b: Navbar items (item global, no anidado)
    │
    ├──► Fase 5a: Dashboard (Livewire + Blade)
    ├──► Fase 5b: Activities (parametrizar IndexComponent)
    ├──► Fase 5c: Lessons (nuevo componente scoped)
    └──► Fase 5d: Profesores KPIs (nuevo componente)
    │
    ▼
Fase 6: Testing
    │
    ▼
Fase 7: Deploy
```

### Estructura de rutas definitiva

```
Route::prefix('app')->name('app.')
  ├── planning/  ── middleware: ['auth', 'isPlanner']      → app.planning.*
  ├── profesors/ ── middleware: ['auth', 'isProfesor']     → app.profesors.*
  ├── estudiante/── middleware: ['auth', 'isStudent']      → app.student.lms.*
  └── leadership/── middleware: ['auth', 'isLeadership']   → app.leadership.*   ← NUEVO
```
```

---

## 6. Plan Detallado

### Fase 1: Base de Datos y Modelo

#### 1.1 Migration — `add_is_leadership_to_users_table`

```php
<?php
// database/migrations/2026_07_27_000001_add_is_leadership_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_leadership')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_leadership')
                    ->default(false)
                    ->after('is_profesor')
                    ->comment('Jefe de área con capacidad de seguimiento');
                $table->index('is_leadership');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_leadership')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['is_leadership']);
                $table->dropColumn('is_leadership');
            });
        }
    }
};
```

**Patrón**: idéntico a `bck/lms/2026_06_06_125944_add_is_planner_to_users_table.php`.

#### 1.2 User Model — cambios

```php
// app/Models/User.php

// En $fillable:
'is_leadership',

// En $casts:
'is_leadership' => 'boolean',

// Nuevo helper method:
public function isLeadership(): bool
{
    return $this->is_leadership ?? false;
}

// Accessor (herencia admin):
public function getIsLeadershipAttribute()
{
    return $this->is_admin || ($this->attributes['is_leadership'] ?? false);
}

// Relación directa con áreas (convenience):
public function leadershipAreas()
{
    return $this->hasMany(\App\Models\app\Academy\AreaConocimiento::class, 'leader_id');
}

// Actualizar getRoleLabelAttribute (is_leadership después de is_admin):
public function getRoleLabelAttribute()
{
    if ($this->is_admin) return 'Administrador';
    if ($this->is_leadership) return 'Jefe de Área';
    if ($this->is_diagnostic) return 'Personal de Diagnóstico';
    if ($this->is_planner) return 'Planificación';
    if ($this->isProfesor()) return 'Profesor';
    return 'Usuario Estándar';
}
```

#### 1.3 Relaciones adicionales en modelos existentes (opcionales)

```php
// AreaConocimiento — ya existe leader() y campo_conocimientos()

// Asignatura — agregar relación many-to-many vía CampoConocimiento:
public function areasConocimiento()
{
    return $this->belongsToMany(
        AreaConocimiento::class,
        'campo_conocimientos',
        'asignatura_id',
        'area_conocimiento_id'
    );
}
```

---

### Fase 2: Middleware y Autorización

#### 2.1 Crear `IsLeadership` middleware

```php
<?php
// app/Http/Middleware/IsLeadership.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsLeadership
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->is_leadership) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder al módulo de seguimiento.');
    }
}
```

#### 2.2 Registrar en Kernel

```php
// app/Http/Kernel.php — en $middlewareAliases:
'isLeadership' => \App\Http\Middleware\IsLeadership::class,
```

---

### Fase 3: Servicios y Scope

#### 3.1 `LeadershipService` — el corazón del scoping

```php
<?php
// app/Services/Planning/LeadershipService.php

namespace App\Services\Planning;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\app\Academy\Asignatura;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class LeadershipService
{
    public function __construct(
        protected User $user
    ) {}

    // ─── SCOPE HELPERS ──────────────────────────────────────────

    /**
     * Áreas de conocimiento donde este user es líder.
     * Si es admin, retorna TODAS las áreas.
     */
    public function getAssignedAreaIds(): Collection
    {
        return $this->user->is_admin
            ? AreaConocimiento::pluck('id')
            : AreaConocimiento::where('leader_id', $this->user->id)->pluck('id');
    }

    /**
     * IDs de asignaturas bajo su liderazgo.
     * Cadena: AreaConocimiento → CampoConocimiento → Asignatura
     */
    public function getAssignedAsignaturaIds(): Collection
    {
        $areaIds = $this->getAssignedAreaIds();
        if ($areaIds->isEmpty()) return collect();

        return Asignatura::whereHas('areasConocimiento', function ($q) use ($areaIds) {
            $q->whereIn('area_conocimientos.id', $areaIds);
        })->pluck('id');
    }

    /**
     * Aplica scope de liderazgo a una query de Pensums.
     */
    public function scopePensums(Builder $query): Builder
    {
        $asignaturaIds = $this->getAssignedAsignaturaIds();
        if ($asignaturaIds->isEmpty()) {
            return $query->whereRaw('1 = 0'); // sin resultados
        }
        return $query->whereIn('asignatura_id', $asignaturaIds);
    }

    /**
     * Aplica scope de liderazgo a una query de Pevaluacions.
     * Cadena: Pevaluacion → Pensum → Asignatura
     */
    public function scopePevaluacions(Builder $query): Builder
    {
        $asignaturaIds = $this->getAssignedAsignaturaIds();
        if ($asignaturaIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }
        return $query->whereHas('pensum', function ($q) use ($asignaturaIds) {
            $q->whereIn('asignatura_id', $asignaturaIds);
        });
    }

    /**
     * Aplica scope de liderazgo a una query de Activities.
     * Cadena: Activity → Pevaluacion → Pensum → Asignatura
     */
    public function scopeActivities(Builder $query): Builder
    {
        $asignaturaIds = $this->getAssignedAsignaturaIds();
        if ($asignaturaIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }
        return $query->whereHas('pevaluacion.pensum', function ($q) use ($asignaturaIds) {
            $q->whereIn('asignatura_id', $asignaturaIds);
        });
    }

    /**
     * Profesores asociados a las áreas del líder.
     * Cadena: Profesor → Pevaluacion → Pensum → Asignatura
     */
    public function getAssignedProfesores(): Collection
    {
        $asignaturaIds = $this->getAssignedAsignaturaIds();
        if ($asignaturaIds->isEmpty()) return collect();

        return Profesor::whereHas('pevaluacions.pensum', function ($q) use ($asignaturaIds) {
            $q->whereIn('asignatura_id', $asignaturaIds);
        })->distinct()->get();
    }

    // ─── MÉTRICAS DEL DASHBOARD ─────────────────────────────────

    public function dashboardMetrics(): array
    {
        $areaIds = $this->getAssignedAreaIds();
        $asignaturaIds = $this->getAssignedAsignaturaIds();

        if ($asignaturaIds->isEmpty()) {
            return $this->emptyMetrics();
        }

        $pevaQuery = Pevaluacion::whereHas('pensum', fn($q) =>
            $q->whereIn('asignatura_id', $asignaturaIds)
        );

        $activitiesInReview = Activity::whereHas('pevaluacion.pensum', fn($q) =>
            $q->whereIn('asignatura_id', $asignaturaIds)
        )->where('status', 0)->count();

        $profesoresCount = Profesor::whereHas('pevaluacions.pensum', fn($q) =>
            $q->whereIn('asignatura_id', $asignaturaIds)
        )->distinct()->count();

        return [
            'total_areas' => $areaIds->count(),
            'total_asignaturas' => $asignaturaIds->count(),
            'total_pevas' => $pevaQuery->count(),
            'activities_in_review' => $activitiesInReview,
            'total_profesores' => $profesoresCount,
            'areas' => AreaConocimiento::whereIn('id', $areaIds)
                ->withCount('campo_conocimientos')
                ->get()
                ->map(fn($area) => [
                    'id' => $area->id,
                    'name' => $area->name,
                    'code' => $area->code,
                    'description' => $area->description,
                    'total_asignaturas' => $area->campo_conocimientos_count,
                ]),
        ];
    }

    protected function emptyMetrics(): array
    {
        return [
            'total_areas' => 0,
            'total_asignaturas' => 0,
            'total_pevas' => 0,
            'activities_in_review' => 0,
            'total_profesores' => 0,
            'areas' => [],
        ];
    }
}
```

#### 3.2 Trait reutilizable para Livewire components

```php
<?php
// app/Livewire/Planning/Leadership/Concerns/HasLeadershipScope.php

namespace App\Livewire\Planning\Leadership\Concerns;

use App\Services\Planning\LeadershipService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

trait HasLeadershipScope
{
    protected LeadershipService $leadershipService;

    public function initializeHasLeadershipScope()
    {
        $this->leadershipService = app(LeadershipService::class, [
            'user' => Auth::user()
        ]);
    }

    protected function getAssignedAreaIds(): Collection
    {
        return $this->leadershipService->getAssignedAreaIds();
    }

    protected function getAssignedAsignaturaIds(): Collection
    {
        return $this->leadershipService->getAssignedAsignaturaIds();
    }
}
```

---

### Fase 4: Rutas + Navegación

#### 4.1 Nuevo grupo de rutas (al mismo nivel que `planning`)

```php
// routes/web.php — DENTRO del grupo `app` (hereda /app/ y nombre app.*)
// Colocar DESPUÉS del cierre del grupo planning (línea 237) y antes del grupo profesors (línea 242)

// ─── Leadership: Seguimiento Jefes de Área ────────────────────
Route::prefix('leadership')
    ->middleware(['auth', 'isLeadership'])
    ->name('leadership.')
    ->group(function () {
        // Dashboard con KPIs globales
        Route::get('/dashboard', \App\Livewire\Planning\Leadership\Dashboard::class)
            ->name('dashboard');

        // Activities (reuso IndexComponent scoped)
        Route::get('/activities', \App\Livewire\Planning\Leadership\ActivityOverview::class)
            ->name('activities');

        // Lecciones LMS por área
        Route::get('/lessons', \App\Livewire\Planning\Leadership\LessonMonitor::class)
            ->name('lessons');

        // Profesores con KPIs
        Route::get('/profesores', \App\Livewire\Planning\Leadership\ProfesorIndicators::class)
            ->name('profesores');
    });
```

**Nota:** El grupo NO está anidado dentro de `planning`. Usa su propio middleware `isLeadership` y su propio prefijo `/app/leadership/`. El layout `planning.layouts.app` se aplica vía atributo PHP `#[Layout]` en cada componente, no por herencia de ruta.
```

#### 4.2 Navbar — item global (no anidado en planning)

El item de leadership va en la barra de navegación principal (`x-role-navbar`), **no dentro del dropdown de Planificación**. Como las rutas son independientes (`app.leadership.*`), la navegación también debe serlo.

```blade
{{-- resources/views/components/navbars/planning-items.blade.php --}}
{{-- AGREGAR al inicio del archivo, como item independiente (no dentro del @if existente) --}}

@if(Auth::user()->is_leadership)
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" @click.outside="open = false"
            class="inline-flex items-center gap-1.5 text-sm font-medium rounded-lg px-3 py-1.5 transition-all duration-200 {{ request()->routeIs('app.leadership.*') ? 'bg-amber-500/10 text-amber-400' : 'text-gray-400 hover:text-amber-300 hover:bg-white/5' }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Seguimiento
            <svg class="w-3 h-3 ml-0.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
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
            <a href="{{ route('app.leadership.dashboard') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-amber-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.leadership.dashboard') ? 'text-amber-400 bg-amber-500/5' : '' }}">
                <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Dashboard
            </a>
            <a href="{{ route('app.leadership.activities') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-amber-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.leadership.activities') ? 'text-amber-400 bg-amber-500/5' : '' }}">
                <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Actividades
            </a>
            <a href="{{ route('app.leadership.lessons') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-amber-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.leadership.lessons') ? 'text-amber-400 bg-amber-500/5' : '' }}">
                <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Lecciones
            </a>
            <a href="{{ route('app.leadership.profesores') }}"
                class="flex items-center gap-2.5 px-3.5 py-2 text-sm text-gray-300 hover:text-amber-300 hover:bg-white/5 rounded-lg transition-colors {{ request()->routeIs('app.leadership.profesores') ? 'text-amber-400 bg-amber-500/5' : '' }}">
                <svg class="w-4 h-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profesores
            </a>
        </div>
    </div>
@endif
```

```blade
{{-- También en planning-items-mobile.blade.php para el menú mobile --}}
@if(Auth::user()->is_leadership)
    <div class="space-y-1">
        <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60 px-3 py-1.5">Seguimiento · Jefatura</div>
        <a href="{{ route('app.leadership.dashboard') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.leadership.dashboard') ? 'text-amber-400 bg-amber-500/5' : 'text-gray-300 hover:text-amber-300 hover:bg-white/5' }} transition-colors">Dashboard</a>
        <a href="{{ route('app.leadership.activities') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.leadership.activities') ? 'text-amber-400 bg-amber-500/5' : 'text-gray-300 hover:text-amber-300 hover:bg-white/5' }} transition-colors">Actividades</a>
        <a href="{{ route('app.leadership.lessons') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.leadership.lessons') ? 'text-amber-400 bg-amber-500/5' : 'text-gray-300 hover:text-amber-300 hover:bg-white/5' }} transition-colors">Lecciones</a>
        <a href="{{ route('app.leadership.profesores') }}" class="flex items-center gap-2.5 px-3.5 py-2 text-sm rounded-lg {{ request()->routeIs('app.leadership.profesores') ? 'text-amber-400 bg-amber-500/5' : 'text-gray-300 hover:text-amber-300 hover:bg-white/5' }} transition-colors">Profesores</a>
    </div>
@endif
```

---

### Fase 5: Livewire Components

#### 5.1 Dashboard

```php
<?php
// app/Livewire/Planning/Leadership/Dashboard.php

namespace App\Livewire\Planning\Leadership;

use App\Services\Planning\LeadershipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Dashboard extends Component
{
    public array $metrics = [];
    private LeadershipService $service;

    public function mount()
    {
        $this->service = app(LeadershipService::class, [
            'user' => Auth::user()
        ]);
        $this->metrics = $this->service->dashboardMetrics();
    }

    public function render()
    {
        return view('livewire.planning.leadership.dashboard', [
            'metrics' => $this->metrics,
        ]);
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
```

**Blade del Dashboard:**

```blade
{{-- resources/views/livewire/planning/leadership/dashboard.blade.php --}}
<div class="fade-in">
    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg font-extrabold text-gray-900 dark:text-white mb-2">Panel de Seguimiento</h1>
            <p class="text-amber-600 dark:text-amber-400 font-medium">
                {{ Auth::user()->username }} · {{ $metrics['total_areas'] }} área(s) asignada(s)
            </p>
        </div>
    </div>

    {{-- KPI Cards grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">Áreas</div>
            <div class="text-3xl font-black text-white">{{ $metrics['total_areas'] }}</div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">Asignaturas</div>
            <div class="text-3xl font-black text-white">{{ $metrics['total_asignaturas'] }}</div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">Profesores</div>
            <div class="text-3xl font-black text-white">{{ $metrics['total_profesores'] }}</div>
        </div>
        <div class="rounded-2xl border border-white/5 bg-gray-900 p-5 space-y-2">
            <div class="text-[10px] font-bold uppercase tracking-widest text-amber-400/60">En Revisión</div>
            <div class="text-3xl font-black {{ $metrics['activities_in_review'] > 0 ? 'text-amber-400' : 'text-emerald-400' }}">
                {{ $metrics['activities_in_review'] }}
            </div>
        </div>
    </div>

    {{-- Áreas expandidas --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($metrics['areas'] as $area)
            <div class="rounded-2xl border border-white/5 bg-gray-900 hover:border-amber-500/30 transition-all duration-200 p-5 space-y-3">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="text-sm font-bold text-white">{{ $area['name'] }}</h3>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/12 text-amber-400 border border-amber-500/20">
                        {{ $area['total_asignaturas'] ?? 0 }}
                    </span>
                </div>
                @if($area['description'] ?? false)
                    <p class="text-xs text-gray-500 line-clamp-2">{{ $area['description'] }}</p>
                @endif
                <div class="flex items-center gap-2 pt-2 border-t border-white/5">
                    <a href="{{ route('app.leadership.activities', ['area_id' => $area['id'] ?? 0]) }}"
                        class="text-[10px] font-bold uppercase tracking-widest text-amber-400 hover:text-amber-300 transition-colors">
                        Ver actividades →
                    </a>
                </div>
            </div>
        @endforeach
        @if(empty($metrics['areas']))
            <div class="col-span-full py-16 text-center">
                <svg class="w-14 h-14 text-gray-700 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p class="text-gray-500 font-medium mb-2">No tienes áreas asignadas</p>
                <p class="text-gray-600 text-sm">Contacta al administrador para asignarte como líder de áreas de conocimiento.</p>
            </div>
        @endif
    </div>
</div>
```

#### 5.2 ActivityOverview — Reuso del IndexComponent existente

**Decisión:** NO crear un componente nuevo. Parametrizar `IndexComponent` con `$leadershipMode`.

```php
// app/Livewire/Planning/Activities/IndexComponent.php — cambios

use App\Services\Planning\LeadershipService;

// Nuevo campo
public $leadershipMode = false;

// En mount():
public function mount()
{
    // Detectar si el usuario debe operar en modo liderazgo
    if (Auth::user()->is_leadership && !Auth::user()->is_admin) {
        $this->leadershipMode = true;
    }

    // ... resto del mount() existente ...
}

// En getPevaluaciones(), después de construir el query base:
protected function getPevaluaciones(array $filters)
{
    $query = Pevaluacion::with([...])->withCount('activities');
    
    // NUEVO: scope por liderazgo
    if ($this->leadershipMode) {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);
        $query = $service->scopePevaluacions($query);
    }
    
    // ... resto del método existente ...
}
```

**Nota:** El componente `ActivityOverview` es una ruta separada que apunta al mismo IndexComponent pero con `$leadershipMode` activado. Se puede hacer como una subclase:

```php
<?php
// app/Livewire/Planning/Leadership/ActivityOverview.php
// Alternativa: subclase que hereda todo del IndexComponent

namespace App\Livewire\Planning\Leadership;

use App\Livewire\Planning\Activities\IndexComponent;
use App\Services\Planning\LeadershipService;

class ActivityOverview extends IndexComponent
{
    public $leadershipMode = true;

    public function mount()
    {
        $this->leadershipMode = true;
        parent::mount();
    }

    // Override parcial del getPevaluaciones para asegurar scope
    protected function getPevaluaciones(array $filters)
    {
        $query = parent::getPevaluaciones($filters);
        
        // Asegurar scope incluso si el padre ya lo hizo
        $service = app(LeadershipService::class, ['user' => auth()->user()]);
        return $service->scopePevaluacions($query);
    }
}
```

#### 5.3 LessonMonitor — Monitoreo de lecciones LMS scoped

```php
<?php
// app/Livewire/Planning/Leadership/LessonMonitor.php

namespace App\Livewire\Planning\Leadership;

use App\Services\Planning\LeadershipService;
use App\Models\app\Academy\Activity;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

class LessonMonitor extends Component
{
    use WithPagination;

    public $search = '';
    public $paginate = 15;
    public $area_id = '';
    public $lapso_id = '';
    public $filter_published = false;

    public function render()
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);

        $query = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.lapso',
            'pevaluacion.seccion.grado',
            'pevaluacion.profesor',
            'lmsPublication',
            'lmsSections.contents',
        ])->whereHas('pevaluacion.pensum', function ($q) use ($service) {
            $asignaturaIds = $service->getAssignedAsignaturaIds();
            $q->whereIn('asignatura_id', $asignaturaIds);
        });

        if ($this->filter_published) {
            $query->whereHas('lmsPublication', fn($q) => $q->where('status', 'PUBLISHED'));
        }

        if ($this->search) {
            $query->where('topic', 'like', "%{$this->search}%");
        }

        if ($this->lapso_id) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapso_id));
        }

        $lessons = $query->orderBy('created_at', 'desc')
            ->paginate($this->paginate);

        $areas = $service->getAssignedAreaIds();
        $lapsos = \App\Models\app\Academy\Lapso::orderBy('name')->get();

        return view('livewire.planning.leadership.lesson-monitor', [
            'lessons' => $lessons,
            'lapsos' => $lapsos,
        ]);
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingPaginate() { $this->resetPage(); }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
```

#### 5.4 ProfesorIndicators — KPIs de profesores scoped

```php
<?php
// app/Livewire/Planning/Leadership/ProfesorIndicators.php

namespace App\Livewire\Planning\Leadership;

use App\Services\Planning\LeadershipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ProfesorIndicators extends Component
{
    public $selectedProfesorId = null;
    public $selectedLapsoId = null;
    public $profesores = [];

    public function mount()
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);
        $this->profesores = $service->getAssignedProfesores();
    }

    public function render()
    {
        $profesor = null;
        $kpi = null;

        if ($this->selectedProfesorId) {
            $profesor = \App\Models\app\Academy\Profesor::find($this->selectedProfesorId);
            if ($profesor) {
                $kpi = [
                    'iee' => $profesor->getProfesorIEE($this->selectedLapsoId),
                    'ire' => $profesor->getProfesorIRE(
                        request()->input('pestudio_id'),
                        $this->selectedLapsoId
                    ),
                    'goal_notas' => $profesor->goal_notas_load($this->selectedLapsoId),
                    'real_notas' => $profesor->real_notas_load($this->selectedLapsoId),
                    'total_pevas' => $profesor->pevaluacions()
                        ->when($this->selectedLapsoId, fn($q) => $q->where('lapso_id', $this->selectedLapsoId))
                        ->count(),
                ];
            }
        }

        $lapsos = \App\Models\app\Academy\Lapso::orderBy('name')->get();

        return view('livewire.planning.leadership.profesor-indicators', [
            'profesor' => $profesor,
            'kpi' => $kpi,
            'lapsos' => $lapsos,
        ]);
    }

    public function selectProfesor($id)
    {
        $this->selectedProfesorId = $id;
    }

    #[Layout('planning.layouts.app')]
    public function layout() {}
}
```

---

### Fase 6: Testing

#### 6.1 Pirámide de tests

```
    ┌──────────────────────────┐
    │  Feature: Flujo completo  │  ← 2 tests
    │  leadership               │
    ├──────────────────────────┤
    │  Feature: Scope por área  │  ← 4 tests
    │                           │
    ├──────────────────────────┤
    │  Unit: Model + Service    │  ← 4 tests
    │  + Middleware              │
    └──────────────────────────┘
```

#### 6.2 Tests críticos

| Test | Tipo | Verifica |
|------|------|----------|
| `LeadershipMiddlewareTest` | Feature | Acceso con `is_leadership=true` → 200 |
| `LeadershipMiddlewareTest` | Feature | Acceso con `is_leadership=false` → 403 |
| `LeadershipMiddlewareTest` | Feature | Admin accede siempre → 200 |
| `LeadershipScopeTest` | Unit | `getAssignedAsignaturaIds()` filtra por área del líder |
| `LeadershipScopeTest` | Unit | Admin obtiene TODAS las asignaturas |
| `LeadershipScopeTest` | Unit | Usuario sin áreas → colección vacía |
| `LeadershipScopeTest` | Unit | `scopePevaluacions()` aplica WHERE correcto |
| `DashboardMetricsTest` | Feature | Dashboard retorna métricas correctas |
| `ActivityCommentScopeTest` | Feature | Líder comenta actividad en su área |
| `ProfesorKpiTest` | Feature | KPIs solo para profesores del área |
| `UserModelTest` | Unit | `getRoleLabelAttribute` retorna 'Jefe de Área' |

#### 6.3 Factory support

```php
// database/factories/UserFactory.php
public function leadership(): static
{
    return $this->state(fn (array $attributes) => [
        'is_leadership' => true,
    ]);
}
```

---

### Fase 7: Seguridad

#### 7.1 Matriz de autorización

| Recurso | Middleware | Scope | Admin bypass |
|---------|-----------|-------|-------------|
| `/app/leadership/dashboard` | `isLeadership` | Ninguno | Sí |
| `/app/leadership/activities` | `isLeadership` | `scopePevaluacions()` + check en saveComent | Sí |
| `/app/leadership/lessons` | `isLeadership` | `getAssignedAsignaturaIds()` | Sí |
| `/app/leadership/profesores` | `isLeadership` | `getAssignedProfesores()` | Sí |

#### 7.2 Validaciones

- Las validaciones existentes en `Activities/IndexComponent` (`saveObservation`, `saveComent`) permanecen igual
- El scope del lado del servidor es la única barrera: si el usuario intenta acceder a un ID fuera de su área, el query retorna 0 resultados (no un error 403 explícito — seguridad por oscuridad parcial)
- Para operaciones críticas (comentar actividad), se puede agregar un check explícito:

```php
// Dentro de saveComent() en IndexComponent (para leadershipMode):
if ($this->leadershipMode) {
    $service = app(LeadershipService::class, ['user' => Auth::user()]);
    $asignaturaIds = $service->getAssignedAsignaturaIds();
    $activityAsignaturaId = $this->activity->pevaluacion->pensum->asignatura_id;
    
    if (!$asignaturaIds->contains($activityAsignaturaId)) {
        $this->notification()->error(
            title: 'Acción no permitida',
            description: 'No puedes comentar actividades fuera de tus áreas asignadas.'
        );
        return;
    }
}
```

---

## 7. ADRs (Architecture Decision Records)

### ADR-001: `is_leadership` como columna booleana

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Columna booleana en `users` | spatie/laravel-permission, tabla pivote |
| **Razón** | Consistencia con `is_admin`, `is_diagnostic`, `is_planner`, `is_profesor` | |
| **Consecuencia** | Sin dependencias nuevas. El scope por área usa `area_conocimientos.leader_id` | |

### ADR-002: Scope vía `CampoConocimiento` (pivote existente)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Usar el pivote `campo_conocimientos` que relaciona `area_conocimiento` ↔ `asignatura` | Agregar `area_conocimiento_id` directamente a `asignaturas` |
| **Razón** | El pivote ya existe y está poblado. Agregar una FK directa requeriría migración de datos | |
| **Consecuencia** | Queries con un JOIN adicional a través de `campo_conocimientos` | |

### ADR-003: Servicio dedicado vs inyección en componentes

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | `LeadershipService` como clase independiente con métodos de scope | Inyectar lógica directamente en cada Livewire component |
| **Razón** | El scope se usa en 4 componentes diferentes. DRY. Testable de forma aislada | |
| **Consecuencia** | Binding via `App::make()` o inyección manual en `mount()` | |

### ADR-004: Subclase vs flag para ActivityOverview

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Subclase `ActivityOverview extends IndexComponent` | Flag `$leadershipMode` dentro de IndexComponent |
| **Razón** | La subclase permite agregar comportamiento específico sin tocar el componente base. Ruta separada = responsabilidad separada | |
| **Consecuencia** | `IndexComponent` permanece limpio. La subclase solo overrides lo necesario | |

### ADR-005: Monitor de lecciones separado (no reusar LmsMonitor)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Nuevo componente `LessonMonitor` específico para leadership | Reusar `Planning\Lms\LmsMonitor` existente |
| **Razón** | `LmsMonitor` es para coordinadores (ve todo). Leadership solo ve sus áreas. La mezcla de scopes en un solo componente sería confusa | |
| **Consecuencia** | Código duplicado mínimo (solo cambia el WHERE clause vs el monitor existente) |

### ADR-006: Middleware y rutas independientes de planning

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Middleware `IsLeadership` separado. Grupo de rutas al mismo nivel que `planning`, no anidado. | Anidar dentro del grupo `planning` |
| **Razón** | El middleware `isPlanner` del grupo planning bloquearía a usuarios leadership que no tienen `is_planner`. `withoutMiddleware()` es frágil y no recomendado. | |
| **Consecuencia** | URL: `/app/leadership/*`. Namespace: `app.leadership.*`. Navbar como item independiente (no dentro del dropdown de Planificación). Layout se aplica vía `#[Layout]`, no por herencia de ruta. |


## 8. Dependencias y Roadmap

### Mapa de archivos

```
NUEVOS:
  database/migrations/xxxx_add_is_leadership_to_users_table.php
  app/Http/Middleware/IsLeadership.php
  app/Services/Planning/LeadershipService.php
  app/Livewire/Planning/Leadership/Dashboard.php
  app/Livewire/Planning/Leadership/ActivityOverview.php
  app/Livewire/Planning/Leadership/LessonMonitor.php
  app/Livewire/Planning/Leadership/ProfesorIndicators.php
  app/Livewire/Planning/Leadership/Concerns/HasLeadershipScope.php
  resources/views/livewire/planning/leadership/dashboard.blade.php
  resources/views/livewire/planning/leadership/lesson-monitor.blade.php
  resources/views/livewire/planning/leadership/profesor-indicators.blade.php
  tests/Feature/Leadership/ (suite)

MODIFICADOS:
  app/Models/User.php
  app/Http/Kernel.php
  routes/web.php
  resources/views/components/navbars/planning-items.blade.php
  resources/views/components/navbars/planning-items-mobile.blade.php (si existe)
  (app/Livewire/Planning/Activities/IndexComponent.php — opcional, vía subclase)
```

### Timeline estimado

| Fase | Archivos | Tiempo |
|------|----------|--------|
| 1. Migration + Model | 2 | 30 min |
| 2. Middleware | 2 | 15 min |
| 3. LeadershipService | 1 | 45 min |
| 4. Routes + Navbar | 2 | 30 min |
| 5a. Dashboard | 2 (component + blade) | 45 min |
| 5b. ActivityOverview | 1 (subclase) | 30 min |
| 5c. LessonMonitor | 2 (component + blade) | 60 min |
| 5d. ProfesorIndicators | 2 (component + blade) | 60 min |
| 6. Testing | ~8 tests | 90 min |
| **Total** | **~16 archivos** | **~6-7 horas** |

---

## 9. Checklist de Rollback

- [ ] `php artisan migrate:rollback --step=1`
- [ ] Remover `is_leadership` de `$fillable`, `$casts`, accessor, helper en User model
- [ ] Revertir `getRoleLabelAttribute()` al original
- [ ] Eliminar `app/Http/Middleware/IsLeadership.php`
- [ ] Remover `'isLeadership' => ...` de `$middlewareAliases`
- [ ] Revertir rutas en `web.php` (eliminar grupo leadership)
- [ ] Eliminar `app/Livewire/Planning/Leadership/` (full directory)
- [ ] Eliminar `app/Services/Planning/LeadershipService.php`
- [ ] Revertir navbar items en `planning-items.blade.php`
- [ ] Eliminar archivos Blade en `resources/views/livewire/planning/leadership/`
- [ ] Eliminar tests de leadership
- [ ] `php artisan optimize:clear`
