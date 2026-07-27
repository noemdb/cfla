# Plan de Implementación: Rol `leadership` (Jefe de Área)

**Staff Engineer Blueprint — v2 (revisado para escalabilidad y consistencia)**
_Autor:_ Claude Architect
_Última revisión:_ 2026-07-27

> **Nota de revisión:** esta versión corrige una contradicción arquitectónica del
> draft original (Fase 5.2 vs ADR-004), introduce memoización y bypass de admin
> sin `IN(...)` masivo en `LeadershipService`, agrega una estrategia de caché con
> invalidación explícita (ADR-007), reemplaza la "seguridad por oscuridad" por
> excepciones de autorización explícitas y testeables (ADR-008), y añade índices
> compuestos para la cadena de JOINs que el spec original no cubría. Los cambios
> puntuales están marcados con `// REVISIÓN v2:` en el código.

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

### Qué cambia en esta revisión (REVISIÓN v2)

| Área | Draft original | Esta revisión |
|------|-----------------|----------------|
| Resolución de scope | Recalcula áreas/asignaturas en cada llamada | Memoizado por instancia + caché de 5 min con invalidación por evento |
| Admin en queries scoped | `IN (id1, id2, ..., idN)` con todos los IDs | Sin filtro alguno (bypass real) |
| `IndexComponent` base | Se modifica para agregar `$leadershipMode` | No se toca — subclase pura (ya lo decía ADR-004, pero el código lo contradecía) |
| Autorización en escritura | Query retorna 0 filas silenciosamente | `assertCanAccessAsignatura()` lanza 403 explícito y testeable |
| Índices de BD | Solo `users.is_leadership` | + índices en `area_conocimientos.leader_id`, `campo_conocimientos.*`, `pensums.asignatura_id`, `pevaluacions.pensum_id` |

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

#### 1.1.b Migration adicional — índices para la cadena de JOINs (REVISIÓN v2)

> **Por qué:** el spec original solo indexa `users.is_leadership`, pero cada
> resolución de scope ejecuta la cadena `AreaConocimiento → CampoConocimiento →
> Asignatura → Pensum → Pevaluacion → Activity`. Sin índices en las FKs de esa
> cadena, cada consulta de `LeadershipService` degrada a full table scans a
> medida que crecen `pensums` y `pevaluacions` (las tablas con más filas del
> sistema). Esto es invisible con datos de prueba y se vuelve un problema real
> en producción con varios años de historial académico acumulado.

```php
<?php
// database/migrations/2026_07_27_000002_add_leadership_scope_indexes.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índices de solo-lectura para acelerar el scope de liderazgo.
     * No tocan datos existentes ni cambian tipos de columna: son
     * seguros de aplicar en caliente sobre una BD en producción.
     */
    public function up(): void
    {
        // area_conocimientos.leader_id — punto de entrada del scope completo
        if (!$this->hasIndex('area_conocimientos', 'area_conocimientos_leader_id_index')) {
            Schema::table('area_conocimientos', function (Blueprint $table) {
                $table->index('leader_id');
            });
        }

        // campo_conocimientos — pivote recorrido en AMBAS direcciones
        // (area_conocimiento_id → asignatura_id y viceversa), por lo que
        // necesita índice en cada columna, no solo en la FK "principal".
        if (!$this->hasIndex('campo_conocimientos', 'campo_conocimientos_area_conocimiento_id_index')) {
            Schema::table('campo_conocimientos', function (Blueprint $table) {
                $table->index('area_conocimiento_id');
            });
        }
        if (!$this->hasIndex('campo_conocimientos', 'campo_conocimientos_asignatura_id_index')) {
            Schema::table('campo_conocimientos', function (Blueprint $table) {
                $table->index('asignatura_id');
            });
        }

        // pensums.asignatura_id — usado en whereIn de scopePensums/scopeActivities
        if (!$this->hasIndex('pensums', 'pensums_asignatura_id_index')) {
            Schema::table('pensums', function (Blueprint $table) {
                $table->index('asignatura_id');
            });
        }

        // pevaluacions.pensum_id — recorrido en whereHas('pensum', ...) de cada scope
        if (!$this->hasIndex('pevaluacions', 'pevaluacions_pensum_id_index')) {
            Schema::table('pevaluacions', function (Blueprint $table) {
                $table->index('pensum_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('area_conocimientos', fn (Blueprint $t) => $t->dropIndex(['leader_id']));
        Schema::table('campo_conocimientos', fn (Blueprint $t) => $t->dropIndex(['area_conocimiento_id']));
        Schema::table('campo_conocimientos', fn (Blueprint $t) => $t->dropIndex(['asignatura_id']));
        Schema::table('pensums', fn (Blueprint $t) => $t->dropIndex(['asignatura_id']));
        Schema::table('pevaluacions', fn (Blueprint $t) => $t->dropIndex(['pensum_id']));
    }

    /**
     * Helper: evita errores si el índice ya existe (p.ej. si otra
     * migración lo agregó antes por una razón distinta).
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = Schema::getIndexes($table);
        foreach ($indexes as $index) {
            if ($index['name'] === $indexName) {
                return true;
            }
        }
        return false;
    }
};
```

**Nota operativa:** en MariaDB, agregar índices sobre tablas grandes (`pevaluacions`,
`pensums`) puede tomar segundos a minutos y bloquea escrituras según el motor de
almacenamiento. Ejecutar en ventana de bajo tráfico o verificar que InnoDB permita
`ALGORITHM=INPLACE` antes de correr en producción.

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

> **REVISIÓN v2 — qué cambió respecto al draft original y por qué:**
> 1. **Memoización por instancia**: `getAssignedAreaIds()`/`getAssignedAsignaturaIds()`
>    se llaman 2-4 veces por request (dashboard, cada scope). El original repetía
>    la query cada vez. Ahora se resuelven una sola vez por instancia del servicio.
> 2. **Bypass de admin sin `IN(...)` masivo**: el original, para un admin,
>    hacía `AreaConocimiento::pluck('id')` (todas las filas) y luego arrastraba
>    ese arreglo completo como `WHERE ... IN (1,2,3,...,500)` en cascada por
>    4 queries adicionales. Ahora, si el usuario es admin, el scope simplemente
>    **no aplica ningún filtro** — es la forma correcta de expresar "sin restricción"
>    y evita cláusulas `IN` de cientos de elementos.
> 3. **`whereRaw('1 = 0')` eliminado**: Laravel ya compila `whereIn($col, [])`
>    como `0 = 1` de forma nativa (comportamiento documentado del query builder).
>    El chequeo manual era código muerto que además ocultaba ese comportamiento
>    a quien no lo supiera; se deja un comentario explicándolo en su lugar.
> 4. **DRY entre los 3 métodos `scope*()`**: eran casi idénticos salvo la
>    relación recorrida. Se extrajo un helper privado parametrizado por
>    "dot path" de relación.
> 5. **Caché opcional con invalidación explícita**: ver ADR-007. El cálculo
>    de áreas/asignaturas asignadas cambia solo cuando un admin reasigna un
>    `leader_id`, así que es un candidato perfecto para caché de corta duración
>    con invalidación por evento en vez de por TTL únicamente.

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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class LeadershipService
{
    /**
     * Segundos de vida de la caché de scope. Corto a propósito: preferimos
     * refrescar seguido antes que arriesgar que un líder vea datos de un
     * área que ya no le pertenece. La invalidación por evento (ver
     * AreaConocimientoObserver, ADR-007) cubre el caso de reasignación
     * inmediata; este TTL es solo una red de seguridad.
     */
    private const CACHE_TTL_SECONDS = 300;

    /** Memoización en memoria: evita recalcular 2 veces en el mismo request. */
    private ?Collection $memoizedAreaIds = null;
    private ?Collection $memoizedAsignaturaIds = null;

    public function __construct(
        protected User $user
    ) {}

    // ─── SCOPE HELPERS ──────────────────────────────────────────

    /**
     * Áreas de conocimiento donde este user es líder.
     *
     * Si es admin, retorna una colección vacía como señal interna de
     * "sin restricción" — NUNCA se usa para poblar un `whereIn` directamente
     * en ese caso, ver `isUnrestricted()`. El admin de verdad (todas las áreas
     * para mostrar en el dashboard) se resuelve aparte en `dashboardMetrics()`.
     */
    public function getAssignedAreaIds(): Collection
    {
        if ($this->memoizedAreaIds !== null) {
            return $this->memoizedAreaIds;
        }

        if ($this->isUnrestricted()) {
            return $this->memoizedAreaIds = collect();
        }

        return $this->memoizedAreaIds = Cache::remember(
            $this->cacheKey('areas'),
            self::CACHE_TTL_SECONDS,
            fn () => AreaConocimiento::where('leader_id', $this->user->id)->pluck('id')
        );
    }

    /**
     * IDs de asignaturas bajo su liderazgo.
     * Cadena: AreaConocimiento → CampoConocimiento → Asignatura
     */
    public function getAssignedAsignaturaIds(): Collection
    {
        if ($this->memoizedAsignaturaIds !== null) {
            return $this->memoizedAsignaturaIds;
        }

        if ($this->isUnrestricted()) {
            return $this->memoizedAsignaturaIds = collect();
        }

        $areaIds = $this->getAssignedAreaIds();
        if ($areaIds->isEmpty()) {
            return $this->memoizedAsignaturaIds = collect();
        }

        return $this->memoizedAsignaturaIds = Cache::remember(
            $this->cacheKey('asignaturas'),
            self::CACHE_TTL_SECONDS,
            fn () => Asignatura::whereHas('areasConocimiento', function ($q) use ($areaIds) {
                $q->whereIn('area_conocimientos.id', $areaIds);
            })->pluck('id')
        );
    }

    /**
     * True si el usuario no debe tener ninguna restricción de scope
     * (actualmente: solo admins). Centralizado aquí para que agregar un
     * futuro rol con el mismo privilegio (p.ej. "coordinador general")
     * solo requiera tocar este método.
     */
    private function isUnrestricted(): bool
    {
        return (bool) $this->user->is_admin;
    }

    /**
     * Aplica scope de liderazgo a una query de Pensums.
     */
    public function scopePensums(Builder $query): Builder
    {
        return $this->applyAsignaturaScope($query, relationPath: null);
    }

    /**
     * Aplica scope de liderazgo a una query de Pevaluacions.
     * Cadena: Pevaluacion → Pensum → Asignatura
     */
    public function scopePevaluacions(Builder $query): Builder
    {
        return $this->applyAsignaturaScope($query, relationPath: 'pensum');
    }

    /**
     * Aplica scope de liderazgo a una query de Activities.
     * Cadena: Activity → Pevaluacion → Pensum → Asignatura
     */
    public function scopeActivities(Builder $query): Builder
    {
        return $this->applyAsignaturaScope($query, relationPath: 'pevaluacion.pensum');
    }

    /**
     * Helper DRY compartido por los 3 métodos `scope*()` de arriba. El único
     * eje de variación entre ellos es cuántos saltos de relación hay que dar
     * hasta llegar a la columna `asignatura_id` — todo lo demás (bypass admin,
     * manejo de colección vacía, nombre de columna) es idéntico.
     *
     * @param  Builder  $query
     * @param  string|null  $relationPath  Ruta dot-notation hasta el modelo
     *         que tiene `asignatura_id` (null = la propia query ya es ese modelo).
     */
    private function applyAsignaturaScope(Builder $query, ?string $relationPath): Builder
    {
        if ($this->isUnrestricted()) {
            return $query; // admin: sin restricción, no tocar la query
        }

        $asignaturaIds = $this->getAssignedAsignaturaIds();

        // Nota: whereIn() con una Collection/array vacío ya compila como
        // "0 = 1" en el grammar de Laravel — no hace falta un whereRaw manual
        // para forzar "sin resultados" cuando el líder no tiene áreas.
        if ($relationPath === null) {
            return $query->whereIn('asignatura_id', $asignaturaIds);
        }

        return $query->whereHas($relationPath, function ($q) use ($asignaturaIds) {
            $q->whereIn('asignatura_id', $asignaturaIds);
        });
    }

    /**
     * Profesores asociados a las áreas del líder.
     * Cadena: Profesor → Pevaluacion → Pensum → Asignatura
     *
     * Nota de escalabilidad: retorna un Collection completo (no paginado).
     * Aceptable mientras el número de profesores por área se mantenga en
     * decenas; si SAEFL crece a instituciones con cientos de profesores por
     * área, este método debería recibir un `Builder` y dejar la paginación
     * al componente Livewire que lo consume (mismo patrón que LessonMonitor).
     */
    public function getAssignedProfesores(): Collection
    {
        if ($this->isUnrestricted()) {
            return Profesor::query()->distinct()->get();
        }

        $asignaturaIds = $this->getAssignedAsignaturaIds();
        if ($asignaturaIds->isEmpty()) return collect();

        return Profesor::whereHas('pevaluacions.pensum', function ($q) use ($asignaturaIds) {
            $q->whereIn('asignatura_id', $asignaturaIds);
        })->distinct()->get();
    }

    /**
     * Guarda de autorización explícita para acciones críticas (comentar,
     * aprobar/rechazar una actividad). Reemplaza el patrón de "silencio +
     * 0 resultados" del draft original (ver ADR-008): lanza una excepción
     * estándar de Laravel que el framework ya sabe convertir en 403,
     * en vez de dejar cada componente reimplementar su propio chequeo.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function assertCanAccessAsignatura(int $asignaturaId): void
    {
        if ($this->isUnrestricted()) {
            return;
        }

        if (!$this->getAssignedAsignaturaIds()->contains($asignaturaId)) {
            abort(403, 'No tienes permiso para operar sobre actividades fuera de tus áreas asignadas.');
        }
    }

    // ─── MÉTRICAS DEL DASHBOARD ─────────────────────────────────

    public function dashboardMetrics(): array
    {
        // Para el dashboard SÍ necesitamos los IDs reales de área incluso
        // si el usuario es admin (para listar las tarjetas de área), así
        // que aquí resolvemos explícito en vez de usar isUnrestricted().
        $areaIds = $this->isUnrestricted()
            ? AreaConocimiento::pluck('id')
            : $this->getAssignedAreaIds();

        if ($areaIds->isEmpty()) {
            return $this->emptyMetrics();
        }

        // Las 3 queries siguientes comparten el mismo whereIn de asignaturas;
        // se resuelve una sola vez gracias a la memoización de arriba en vez
        // de recalcularlo en cada llamada como hacía el draft original.
        $pevaQuery = Pevaluacion::query();
        $activityQuery = Activity::query();
        $profesorQuery = Profesor::query();

        if (!$this->isUnrestricted()) {
            $pevaQuery = $this->scopePevaluacions($pevaQuery);
            $activityQuery = $this->scopeActivities($activityQuery);
            $profesorQuery = $profesorQuery->whereHas('pevaluacions.pensum', function ($q) {
                $q->whereIn('asignatura_id', $this->getAssignedAsignaturaIds());
            });
        }

        return [
            'total_areas' => $areaIds->count(),
            'total_asignaturas' => $this->isUnrestricted()
                ? Asignatura::count()
                : $this->getAssignedAsignaturaIds()->count(),
            'total_pevas' => $pevaQuery->count(),
            'activities_in_review' => $activityQuery->where('status', 0)->count(),
            'total_profesores' => $profesorQuery->distinct()->count(),
            'areas' => AreaConocimiento::whereIn('id', $areaIds)
                ->withCount('campo_conocimientos')
                ->get()
                ->map(fn($area) => [
                    'id' => $area['id'] ?? $area->id,
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

    /** Prefijo de caché namespaced por usuario, para invalidación selectiva. */
    private function cacheKey(string $suffix): string
    {
        return "leadership:{$this->user->id}:{$suffix}";
    }
}
```

#### 3.1.b Observer de invalidación de caché (REVISIÓN v2)

> **Por qué:** la caché de `getAssignedAreaIds()`/`getAssignedAsignaturaIds()`
> tiene un TTL de 5 minutos como red de seguridad, pero si un administrador
> reasigna `leader_id` en `AreaConocimiento`, el líder saliente/entrante no
> debería tener que esperar hasta 5 minutos para ver el cambio reflejado.
> Este observer invalida ambas claves de caché (la del líder anterior y la
> del nuevo) apenas se guarda el modelo.

```php
<?php
// app/Observers/AreaConocimientoObserver.php

namespace App\Observers;

use App\Models\app\Academy\AreaConocimiento;
use Illuminate\Support\Facades\Cache;

class AreaConocimientoObserver
{
    public function saved(AreaConocimiento $area): void
    {
        $this->forgetLeaderCache($area->leader_id);

        // Si leader_id cambió en este guardado, también invalidamos
        // la caché del líder ANTERIOR (que ya no debería ver esta área).
        if ($area->wasChanged('leader_id')) {
            $this->forgetLeaderCache($area->getOriginal('leader_id'));
        }
    }

    public function deleted(AreaConocimiento $area): void
    {
        $this->forgetLeaderCache($area->leader_id);
    }

    private function forgetLeaderCache(?int $userId): void
    {
        if (!$userId) {
            return;
        }
        Cache::forget("leadership:{$userId}:areas");
        Cache::forget("leadership:{$userId}:asignaturas");
    }
}
```

```php
// app/Providers/AppServiceProvider.php — registrar en boot()
use App\Models\app\Academy\AreaConocimiento;
use App\Observers\AreaConocimientoObserver;

AreaConocimiento::observe(AreaConocimientoObserver::class);
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

**Decisión (alineada con ADR-004, ver más abajo):** subclase pura de
`IndexComponent`. **NO se modifica `IndexComponent.php` base.**

> **REVISIÓN v2 — corrección de una contradicción del draft original:** la
> versión anterior de este documento mostraba *dos* enfoques incompatibles en
> la misma sección: (a) esta subclase, y (b) agregar un campo `$leadershipMode`
> directamente dentro de `IndexComponent.php`. ADR-004 explícitamente eligió
> la subclase para no tocar el componente base — el fragmento (b) contradecía
> esa decisión y se elimina aquí. Si en el futuro se decide que la subclase no
> alcanza (p.ej. porque `IndexComponent` tiene métodos `private` que la subclase
> no puede sobreescribir), eso debe registrarse como un ADR nuevo que reemplace
> a ADR-004, no como un parche silencioso.

```php
<?php
// app/Livewire/Planning/Leadership/ActivityOverview.php

namespace App\Livewire\Planning\Leadership;

use App\Livewire\Planning\Activities\IndexComponent;
use App\Services\Planning\LeadershipService;
use Illuminate\Support\Facades\Auth;

class ActivityOverview extends IndexComponent
{
    /**
     * Override del scope de datos: toda instancia de este componente
     * opera SIEMPRE en modo liderazgo, sin excepción — a diferencia de
     * IndexComponent base, que puede o no tener scope según el rol.
     */
    protected function getPevaluaciones(array $filters)
    {
        $query = parent::getPevaluaciones($filters);

        $service = app(LeadershipService::class, ['user' => Auth::user()]);
        return $service->scopePevaluacions($query);
    }

    /**
     * Guarda de autorización explícita antes de comentar/aprobar (REVISIÓN v2).
     * Reemplaza el chequeo ad-hoc de la Fase 7 original: usa el método
     * centralizado `assertCanAccessAsignatura()` del servicio, que ya sabe
     * hacer bypass para admin y lanzar un 403 real y testeable.
     */
    public function saveComent(...$args)
    {
        $service = app(LeadershipService::class, ['user' => Auth::user()]);
        $service->assertCanAccessAsignatura(
            $this->activity->pevaluacion->pensum->asignatura_id
        );

        return parent::saveComent(...$args);
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
| `LeadershipScopeTest` | Unit | Admin no aplica ningún filtro (no genera `IN` gigante) — `[REVISIÓN v2]` |
| `LeadershipScopeTest` | Unit | Usuario sin áreas → colección vacía |
| `LeadershipScopeTest` | Unit | `scopePevaluacions()` aplica WHERE correcto |
| `LeadershipScopeTest` | Unit | Dos llamadas seguidas a `getAssignedAreaIds()` en la misma instancia solo ejecutan 1 query (memoización) — `[REVISIÓN v2]` |
| `LeadershipScopeTest` | Unit | `assertCanAccessAsignatura()` lanza `AuthorizationException` (403) para asignatura fuera de scope — `[REVISIÓN v2]` |
| `LeadershipCacheTest` | Unit | Reasignar `leader_id` en `AreaConocimiento` invalida la caché del líder anterior y del nuevo (`AreaConocimientoObserver`) — `[REVISIÓN v2]` |
| `DashboardMetricsTest` | Feature | Dashboard retorna métricas correctas |
| `ActivityCommentScopeTest` | Feature | Líder comenta actividad en su área |
| `ActivityCommentScopeTest` | Feature | Líder recibe 403 al intentar comentar actividad fuera de su área — `[REVISIÓN v2]` |
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

#### 7.2 Validaciones (REVISIÓN v2)

- Las validaciones existentes en `Activities/IndexComponent` (`saveObservation`, `saveComent`) permanecen igual.
- **Se elimina la aceptación de "seguridad por oscuridad parcial"** del draft
  original. Que un query sin scope explícito retorne 0 filas es un efecto
  colateral aceptable del filtrado normal de lectura (dashboard, listados),
  pero **no es una estrategia de autorización** para acciones de escritura:
  no deja rastro en logs, no es fácil de testear con un assert claro, y es
  fácil de romper por accidente si alguien reordena el query builder.
- Para toda acción de escritura (comentar, aprobar, rechazar) se usa la guarda
  centralizada `LeadershipService::assertCanAccessAsignatura()` (ver Fase 3.1 y
  la implementación en `ActivityOverview::saveComent()`, Fase 5.2): lanza un
  403 real de Laravel, queda cubierta por un test unitario dedicado
  (`LeadershipScopeTest::assert_can_access_asignatura_blocks_out_of_scope`),
  y centraliza el bypass de admin en un solo lugar en vez de repetirlo por
  componente.

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
| **Selección** | Nuevo componente `LessonMonitor` específico para leadership | Reusar `Planning\Lms\LmsMonitor` existente, parametrizado con un scope inyectable |
| **Razón** | `LmsMonitor` es para coordinadores (ve todo). Leadership solo ve sus áreas. La mezcla de scopes en un solo componente sería confusa | |
| **Consecuencia** | Código duplicado mínimo (solo cambia el WHERE clause vs el monitor existente) |

> **REVISIÓN v2 — tradeoff explícito:** esta decisión contradice parcialmente
> el "Principio de diseño" de la Sección 1 ("mínimo código nuevo, máximo
> reuso"), y merece que quede registrado en vez de asumirse. La alternativa
> descartada — parametrizar `LmsMonitor` con un `?LeadershipService $scope`
> inyectado opcionalmente — evitaría la duplicación pero acoplaría un
> componente usado por coordinadores (rol de mayor confianza) a lógica de un
> rol más restringido, aumentando el radio de impacto de cualquier bug futuro
> en `LeadershipService`. Se prioriza aislamiento de fallos sobre DRY estricto
> aquí porque el costo de duplicación es bajo (un WHERE clause) y el costo de
> un acoplamiento incorrecto en autorización es alto. Si `LessonMonitor`
> diverge significativamente de `LmsMonitor` con el tiempo, revisar si aún
> vale la pena mantenerlos separados o extraer un trait común solo para la
> UI (no para el scope de datos).

### ADR-006: Middleware y rutas independientes de planning

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Middleware `IsLeadership` separado. Grupo de rutas al mismo nivel que `planning`, no anidado. | Anidar dentro del grupo `planning` |
| **Razón** | El middleware `isPlanner` del grupo planning bloquearía a usuarios leadership que no tienen `is_planner`. `withoutMiddleware()` es frágil y no recomendado. | |
| **Consecuencia** | URL: `/app/leadership/*`. Namespace: `app.leadership.*`. Navbar como item independiente (no dentro del dropdown de Planificación). Layout se aplica vía `#[Layout]`, no por herencia de ruta. |

### ADR-007: Caché con invalidación por evento vs solo TTL vs sin caché (REVISIÓN v2)

| | Decisión | Alternativas descartadas |
|--|----------|---------------------------|
| **Selección** | `Cache::remember()` con TTL corto (5 min) + invalidación explícita vía `AreaConocimientoObserver` cuando cambia `leader_id` | (a) sin caché: recalcular la cadena de JOINs en cada llamada; (b) solo TTL largo sin observer |
| **Razón** | El scope de un líder cambia con muy poca frecuencia (solo cuando un admin reasigna áreas) pero se lee en cada request de cada componente de leadership. Es el patrón de libro para caché: escritura rara, lectura frecuente. Sin caché, cada `dashboardMetrics()` dispara 3-4 queries con varios JOINs cada vez que el líder recarga la página. Con solo TTL largo (sin invalidación), un admin que reasigna un área tendría que esperar el TTL completo para que el cambio surta efecto, lo cual es una brecha de seguridad temporal inaceptable (el líder saliente seguiría viendo datos del área por minutos) |
| **Consecuencia** | Se agrega una dependencia nueva del `Cache` facade (ya disponible en Laravel sin config extra si se usa el driver `file` o `database` por defecto) y un Observer nuevo. Si SAEFL corre en múltiples workers/servidores, el driver de caché debe ser compartido (Redis/Memcached/DB), no `array` ni `file` local por worker — verificar `config('cache.default')` antes de desplegar |

### ADR-008: Excepción de autorización explícita vs "silencio + 0 resultados" (REVISIÓN v2)

| | Decisión | Alternativa descartada |
|--|----------|-------------------------|
| **Selección** | `LeadershipService::assertCanAccessAsignatura()` lanza un 403 real (`abort(403, ...)`) antes de ejecutar cualquier acción de escritura | Dejar que el scope del query retorne 0 filas silenciosamente ("seguridad por oscuridad parcial", como proponía el draft original) |
| **Razón** | Para **lecturas** (dashboard, listados), que un query scoped retorne 0 filas es un comportamiento correcto y no necesita excepción. Para **escrituras** (comentar, aprobar/rechazar), el draft original dejaba la barrera implícita en el WHERE del query de origen — funciona mientras nadie cambie el orden de las validaciones, pero no deja rastro explícito, no es fácil de testear con un `expectException()` claro, y un refactor futuro que toque `IndexComponent::saveComent()` podría romper la protección sin que ningún test lo detecte | |
| **Consecuencia** | Un método centralizado y testeable (`LeadershipScopeTest::assert_can_access_asignatura_blocks_out_of_scope`), reusable por futuros componentes de escritura sin reimplementar el chequeo cada vez | |


## 8. Dependencias y Roadmap

### Mapa de archivos

```
NUEVOS:
  database/migrations/xxxx_add_is_leadership_to_users_table.php
  database/migrations/xxxx_add_leadership_scope_indexes.php        [REVISIÓN v2]
  app/Http/Middleware/IsLeadership.php
  app/Services/Planning/LeadershipService.php
  app/Observers/AreaConocimientoObserver.php                       [REVISIÓN v2]
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
  app/Providers/AppServiceProvider.php (registrar AreaConocimientoObserver)  [REVISIÓN v2]
  routes/web.php
  resources/views/components/navbars/planning-items.blade.php
  resources/views/components/navbars/planning-items-mobile.blade.php (si existe)

NO SE TOCA (a diferencia del draft original):
  app/Livewire/Planning/Activities/IndexComponent.php — ActivityOverview es
  una subclase pura (ADR-004); ver corrección en Fase 5.2.
```

### Timeline estimado

| Fase | Archivos | Tiempo |
|------|----------|--------|
| 1. Migration + Model | 2 | 30 min |
| 1.b Migration de índices [REVISIÓN v2] | 1 | 20 min |
| 2. Middleware | 2 | 15 min |
| 3. LeadershipService (con caché + memoización) | 1 | 60 min |
| 3.b Observer de invalidación [REVISIÓN v2] | 1 | 20 min |
| 4. Routes + Navbar | 2 | 30 min |
| 5a. Dashboard | 2 (component + blade) | 45 min |
| 5b. ActivityOverview (subclase, sin tocar IndexComponent) | 1 | 30 min |
| 5c. LessonMonitor | 2 (component + blade) | 60 min |
| 5d. ProfesorIndicators | 2 (component + blade) | 60 min |
| 6. Testing (incluye tests de caché y 403 explícito) | ~11 tests | 110 min |
| **Total** | **~18 archivos** | **~8 horas** |

> El total sube de ~6-7h a ~8h respecto al draft original: la diferencia son
> los 20+20+20 min de la migración de índices, el observer y los tests
> adicionales de ADR-007/ADR-008. Es tiempo bien gastado — evita depurar en
> producción un scope que se cae de performance con la primera institución
> grande, o una brecha de autorización silenciosa que nadie testeó.

---

## 9. Checklist de Rollback

- [ ] `php artisan migrate:rollback --step=2` (revierte tanto la columna `is_leadership` como la migración de índices — `[REVISIÓN v2]` cambia `--step=1` por `--step=2`)
- [ ] Remover `is_leadership` de `$fillable`, `$casts`, accessor, helper en User model
- [ ] Revertir `getRoleLabelAttribute()` al original
- [ ] Eliminar `app/Http/Middleware/IsLeadership.php`
- [ ] Remover `'isLeadership' => ...` de `$middlewareAliases`
- [ ] Revertir rutas en `web.php` (eliminar grupo leadership)
- [ ] Eliminar `app/Livewire/Planning/Leadership/` (full directory)
- [ ] Eliminar `app/Services/Planning/LeadershipService.php`
- [ ] Eliminar `app/Observers/AreaConocimientoObserver.php` y su registro en `AppServiceProvider::boot()` — `[REVISIÓN v2]`
- [ ] Limpiar entradas de caché huérfanas: `Cache::flush()` no es seguro en producción (borra TODA la caché de la app) — en su lugar, correr un comando puntual que borre solo las claves `leadership:*` si el driver lo soporta (Redis: `redis-cli --scan --pattern "leadership:*" | xargs redis-cli del`) — `[REVISIÓN v2]`
- [ ] Revertir navbar items en `planning-items.blade.php`
- [ ] Eliminar archivos Blade en `resources/views/livewire/planning/leadership/`
- [ ] Eliminar tests de leadership
- [ ] `php artisan optimize:clear`