# Plan de Implementación: Rol `estudiant` (Estudiante LMS)

**Staff Engineer Blueprint**
_Autor:_ Claude Architect
_Última revisión:_ 2026-07-29

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

### ¿Qué es el rol `estudiant`?

Un **Estudiante LMS** es un usuario que accede al sistema para consumir contenido educativo publicado por sus profesores. A diferencia de los roles administrativos (admin, planner, profesor), el estudiante es un **consumidor de contenido**: visualiza actividades publicadas, descarga recursos, accede a lecciones LMS y puede registrar interacciones (comentarios, visualizaciones).

### Las 6 responsabilidades del rol

| # | Responsabilidad | Modelos involucrados | Capacidades |
|---|----------------|---------------------|-------------|
| 1 | **Ver sus datos** | `User` → `Estudiant` | Perfil visible con datos personales e institucionales |
| 2 | **Información académica** | `Estudiant` → `Inscripcion` → `Seccion/Grado` → `Pestudio` | Ver inscripción, sección, grado, pensum asociado |
| 3 | **Planificación/Actividades** | Cadena hasta `Pevaluacion` → `Activity` | Resumen de actividades de planificación visibles |
| 4 | **Lecciones LMS** | `Activity` → `LmsActivityPublication/Section/Resource/Link/HtmlEmbed` | Visualizar lecciones publicadas por sus profesores |
| 5 | **Recursos compartidos** | `LmsActivityResource` | Listado de recursos descargables |
| 6 | **Interactividad (comentarios)** | `ActivityComment` (NUEVO) | Registrar comentarios por `activityId` |

### Principio de diseño

> **Máximo reuso de lo existente, mínimo código nuevo.** El estudiante ya tiene rutas (`/app/estudiante/*`), middleware (`IsStudent`) y componentes base (`StudentHome`, `ActivityView`). Faltan:
> - Columna `is_student` en `users` (nunca se agregó)
> - Servicio `StudentScopeService` para scoping por inscripción
> - Componente de perfil/datos personales
> - Sistema de comentarios del estudiante
> - Vista de pensums/planificación
> - Navegación completa

---

## 2. Arquitectura Actual (AS-IS)

### Modelo de roles actual

| Columna `users` | Middleware | Rutas que protege | ¿Existe? |
|----------------|-----------|-------------------|----------|
| `is_admin` | `IsAdmin` | `/admin/*` | ✅ |
| `is_diagnostic` | `IsAdminOrDiagnostic` | `/admin/*` | ✅ |
| `is_planner` | `IsPlanner` | `/app/planning/*` | ✅ |
| `is_profesor` | `IsProfesor` | `/app/profesors/*` | ✅ |
| `is_student` | `IsStudent` | `/app/estudiante/*` | ✅ |
| `is_leadership` | `IsLeadership` | `/app/leadership/*` | ✅ |

### Nota: `is_student` implementado

La columna `is_student` fue agregada mediante la migración `2026_07_28_000001_add_is_student_to_users_table.php`.
Ya no es un bug. El middleware `IsStudent` funciona correctamente con admin bypass.

### Componentes implementados

```
app/Livewire/Student/Lms/
├── Concerns/
│   └── HasStudentScope.php   → Trait de scoping por inscripción
├── StudentHome.php           → Vista principal: lista de pevaluacions con actividades publicadas
├── ActivityView.php          → Vista de una actividad: secciones, recursos, enlaces, embeds + comentarios
├── Profile.php               → Perfil del estudiante (datos personales, contacto, representante)
├── AcademicInfo.php          → Información académica (pensums, áreas de formación)
├── LessonList.php            → Listado de lecciones con filtros (búsqueda, lapso, asignatura)
└── ResourceList.php          → Recursos compartidos con modal de vista previa

resources/views/livewire/student/lms/
├── student-home.blade.php    → Listado de pevaluacions grupado
├── activity-view.blade.php   → Contenido de actividad individual + comentarios
├── profile.blade.php         → Perfil completo con stats, contacto, representante
├── academic-info.blade.php   → Información académica con stats por área
├── lesson-list.blade.php     → Lecciones con filtros y paginación
└── resource-list.blade.php   → Recursos con modal de vista previa

resources/views/student/layouts/
└── app.blade.php             → Layout del estudiante con navbar completa

app/Services/Estudiant/
└── StudentScopeService.php   → Scoping por inscripción del estudiante

app/Models/app/Academy/Lms/
└── ActivityComment.php       → Comentarios en actividades con moderación

app/Policies/
└── ActivityCommentPolicy.php → Política de autorización para comentarios
```

### Lo que se implementó

| Aspecto | Estado | Detalle |
|---------|--------|---------|
| Middleware `IsStudent` | ✅ | Con admin bypass y helper `isStudent()` |
| Grupo de rutas `/app/estudiante/` | ✅ | 7 rutas (home, profile, academic, lessons, resources, activity, resource.download) |
| `StudentHome` | ✅ | Refactorizado con `HasStudentScope` trait |
| `ActivityView` | ✅ | Comentarios, markComplete y LmsActivityLog |
| `ResourceDownloadController` | ✅ | Controlador para descarga de recursos |
| Layout `student.layouts.app` | ✅ | Navbar completa (Inicio, Perfil, Académica, Lecciones, Recursos) |
| Columna `is_student` | ✅ | Migración completada |
| `StudentScopeService` | ✅ | Namespace real: `Services\Estudiant\StudentScopeService` |
| Perfil del estudiante | ✅ | Datos personales, lugar de nacimiento, contacto, representante, stats, enlaces rápidos |
| Información académica | ✅ | Pensums, áreas de formación, stats por área |
| Comentarios | ✅ | Modelo + Policy + moderación (approve/reject) |
| Lecciones | ✅ | Listado con filtros (búsqueda, lapso, asignatura) y paginación |
| Recursos | ✅ | Grid con modal de vista previa (imagen, PDF, video) |
| Tests | ✅ | Ver sección de Testing más abajo |

---

## 3. Cadena de Modelos

### Árbol completo de navegación

```
User (is_student = true)
  │
  └── Estudiant (user_id → users.id)
        │
        ├── Inscripcion (estudiant_id)
        │     └── Seccion (seccion_id)
        │           ├── Grado (grado_id)
        │           │     └── Pestudio (pestudio_id)
        │           │           ├── Peducativo (peducativo_id)
        │           │           └── Pensum (pestudio_id + grado_id)
        │           │                 └── Pevaluacion (pensum_id)
        │           │                       └── Activity (pevaluacion_id)
        │           │                             ├── LmsActivityPublication (activity_id) ← solo visibleNow
        │           │                             ├── LmsActivitySection (activity_id) ← is_visible
        │           │                             │     └── LmsActivityContent (section_id)
        │           │                             │           └── Media (Polimórfica)
        │           │                             ├── LmsActivityResource (activity_id) ← is_visible
        │           │                             │     └── Media (Polimórfica)
        │           │                             ├── LmsActivityLink (activity_id) ← is_visible
        │           │                             ├── LmsHtmlEmbed (activity_id) ← is_visible
        │           │                             └── ActivityComment (activity_id) ← NUEVO
        │           │                                   └── User (author_id)
        │           │
        │           └── Profesor (via Pevaluacion)
        │
        └── Administrativa (estudiant_id) — datos administrativos
```

### Traducción a queries SQL

```sql
-- Obtener el estudiante logueado
SELECT e.* FROM estudiants e WHERE e.user_id = {userId};

-- Obtener pensums del estudiante (vía sección de inscripción)
SELECT p.* FROM pensums p
JOIN grados g ON g.id = p.grado_id
JOIN seccions s ON s.grado_id = g.id
JOIN inscripcions i ON i.seccion_id = s.id
JOIN estudiants e ON e.id = i.estudiant_id
WHERE e.user_id = {userId}
  AND s.status_active = 'true'
  AND s.status_inscription_affects = 'true';

-- Obtener actividades visibles para el estudiante
SELECT act.* FROM activities act
JOIN pevaluacions pev ON pev.id = act.pevaluacion_id
JOIN pensums p ON p.id = pev.pensum_id
JOIN grados g ON g.id = p.grado_id
JOIN seccions s ON s.grado_id = g.id
JOIN inscripcions i ON i.seccion_id = s.id
JOIN estudiants e ON e.id = i.estudiant_id
JOIN lms_activity_publications lms ON lms.activity_id = act.id
WHERE e.user_id = {userId}
  AND lms.status = 'PUBLISHED'
  AND (lms.publish_at IS NULL OR lms.publish_at <= NOW())
  AND (lms.expire_at IS NULL OR lms.expire_at >= NOW());

-- Recursos compartidos visibles para el estudiante
SELECT DISTINCT r.* FROM lms_activity_resources r
JOIN activities act ON act.id = r.activity_id
JOIN pevaluacions pev ON pev.id = act.pevaluacion_id
JOIN pensums p ON p.id = pev.pensum_id
JOIN grados g ON g.id = p.grado_id
JOIN seccions s ON s.grado_id = g.id
JOIN inscripcions i ON i.seccion_id = s.id
JOIN estudiants e ON e.id = i.estudiant_id
WHERE e.user_id = {userId}
  AND r.is_visible = true;
```

---

## 4. Target (TO-BE)

### Nuevo modelo de roles (completo)

```
users.is_student  →  middleware IsStudent  →  /app/estudiante/*
                                                    ├── /home              → student.lms.home (EXISTE)
                                                    ├── /perfil            → student.lms.profile (NUEVO)
                                                    ├── /academica         → student.lms.academic (NUEVO)
                                                    ├── /activity/{id}     → student.lms.activity (EXISTE)
                                                    ├── /lecciones         → student.lms.lessons (NUEVO)
                                                    ├── /recursos          → student.lms.resources (NUEVO)
                                                    └── /resource/{id}/download → student.lms.resource.download (EXISTE)
```

### Jerarquía de acceso

```
is_admin ──► pasa TODOS los middleware (incluyendo isStudentAdmin)
is_student ──► pasa isStudent solamente
```

**Nota:** A diferencia de otros roles (profesor, planner), el estudiante NO hereda automáticamente el acceso admin. Usamos el patrón `isStudent()` helper (no accessor con herencia) porque un admin no necesita actuar como estudiante — tiene acceso directo a los datos vía admin.

### Flujo de autenticación del estudiante

```
Login Form → Auth::attempt()
  → user encontrado
  → user->is_student === true
  → redirect → /app/estudiante/home
```

Los estudiantes pueden tener su propio flujo de login (actualmente login único para todos los roles).

---

## 5. Estrategia de Implementación

### Orden lógico (bloqueante en cascada)

```
Fase 1: Migration (is_student column) + User Model
    │
    ▼
Fase 2: StudentService (scoping por inscripción)
    │
    ├──► Fase 3a: Perfil / Datos personales (Livewire + Blade)
    ├──► Fase 3b: Información académica (inscripción, pensum)
    ├──► Fase 3c: Lecciones (mejora del StudentHome existente)
    ├──► Fase 3d: Recursos compartidos (nuevo listado)
    └──► Fase 3e: Comentarios (NUEVO modelo + componente)
    │
    ▼
Fase 4: Rutas adicionales + Navbar
    │
    ▼
Fase 5: Testing
    │
    ▼
Fase 6: Deploy
```

### Estructura de rutas definitiva

```
Route::prefix('app')->name('app.')
  ├── planning/     ── middleware: ['auth', 'isPlanner']        → app.planning.*
  ├── profesors/    ── middleware: ['auth', 'isProfesor']       → app.profesors.*
  ├── estudiante/   ── middleware: ['auth', 'isStudent']        → app.student.lms.*   ← MEJORAR
  └── leadership/   ── middleware: ['auth', 'isLeadership']     → app.leadership.*
```

---

## 6. Plan Detallado

### Fase 1: Base de Datos y Modelo

#### 1.1 Migration — `add_is_student_to_users_table`

```php
<?php
// database/migrations/2026_07_27_000001_add_is_student_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_student')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_student')
                    ->default(false)
                    ->after('is_profesor')
                    ->comment('Estudiante con acceso al módulo LMS');
                $table->index('is_student');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_student')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['is_student']);
                $table->dropColumn('is_student');
            });
        }
    }
};
```

**Patrón:** idéntico a las migraciones existentes de `is_planner`, `is_profesor`.

#### 1.2 User Model — cambios

```php
// app/Models/User.php

// En $fillable (agregar):
'is_student',

// En $casts (agregar):
'is_student' => 'boolean',

// Nuevo helper method (agregar después de isProfesor):
public function isStudent(): bool
{
    return $this->is_student ?? false;
}

// Relación con Estudiant:
public function estudiant()
{
    return $this->hasOne(\App\Models\app\Learner\Estudiant::class, 'user_id');
}

// Actualizar getRoleLabelAttribute:
public function getRoleLabelAttribute()
{
    if ($this->is_admin) return 'Administrador';
    if ($this->is_leadership) return 'Jefe de Área';
    if ($this->is_diagnostic) return 'Personal de Diagnóstico';
    if ($this->is_planner) return 'Planificación';
    if ($this->isProfesor()) return 'Profesor';
    if ($this->is_student) return 'Estudiante';        // ← NUEVO
    return 'Usuario Estándar';
}
```

#### 1.3 Migration — `create_activity_comments_table` (NUEVO)

```php
<?php
// database/migrations/2026_07_27_000002_create_activity_comments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activity_comments')) {
            Schema::create('activity_comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('activity_id')
                    ->constrained('activities')
                    ->cascadeOnDelete();
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->cascadeOnDelete();
                $table->text('body');
                $table->boolean('is_approved')->default(false);
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('approved_by')->nullable()
                    ->constrained('users');
                $table->timestamps();
                $table->softDeletes();

                $table->index(['activity_id', 'is_approved', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_comments');
    }
};
```

#### 1.4 Modelo ActivityComment (NUEVO)

```php
<?php
// app/Models/app/Academy/Lms/ActivityComment.php

namespace App\Models\app\Academy\Lms;

use App\Models\app\Academy\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityComment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'activity_id', 'user_id', 'body',
        'is_approved', 'approved_at', 'approved_by',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeForActivity($query, int $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    public function approve(int $userId): void
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);
    }
}
```

#### 1.5 Activity — nueva relación

```php
// app/Models/app/Academy/Activity.php

// Relación con comentarios:
public function comments()
{
    return $this->hasMany(\App\Models\app\Academy\Lms\ActivityComment::class, 'activity_id');
}

public function approvedComments()
{
    return $this->comments()->where('is_approved', true);
}
```

---

### Fase 2: Middleware y Autorización

#### 2.1 Actualizar `IsStudent` middleware

El middleware ya existe en `app/Http/Middleware/IsStudent.php`. Solo requiere la columna `is_student` funcione. Verificar que use el helper `isStudent()`:

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsStudent
{
    public function handle(Request $request, Closure $next): \Symfony\Component\HttpFoundation\Response
    {
        if (!auth()->check() || !auth()->user()->isStudent()) {
            abort(403, 'Acceso restringido a estudiantes.');
        }
        return $next($request);
    }
}
```

#### 2.2 Registrar en Kernel (verificar)

```php
// app/Http/Kernel.php — en $middlewareAliases:
'isStudent' => \App\Http\Middleware\IsStudent::class,  // ¿Ya existe? Verificar.
```

#### 2.3 Política para comentarios

```php
<?php
// app/Policies/ActivityCommentPolicy.php

namespace App\Policies;

use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\User;

class ActivityCommentPolicy
{
    public function viewAny(User $user, int $activityId): bool
    {
        // El estudiante puede ver comentarios aprobados de actividades visibles
        return $user->isStudent() || $user->is_admin;
    }

    public function create(User $user): bool
    {
        return $user->isStudent() || $user->is_admin;
    }

    public function update(User $user, ActivityComment $comment): bool
    {
        return $user->id === $comment->user_id || $user->is_admin;
    }

    public function delete(User $user, ActivityComment $comment): bool
    {
        return $user->id === $comment->user_id || $user->is_admin;
    }
}
```

---

### Fase 3: Servicios y Scope

#### 3.1 `StudentScopeService` — el corazón del scoping

```php
<?php
// app/Services/Estudiant/StudentScopeService.php

namespace App\Services\Lms;

use App\Models\User;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Learner\Estudiant;
use App\Models\app\Academy\Lms\LmsActivityResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class StudentScopeService
{
    protected ?Estudiant $estudiant = null;
    protected ?Collection $seccionIds = null;
    protected ?Collection $gradoIds = null;

    public function __construct(
        protected User $user
    ) {
        $this->estudiant = Estudiant::where('user_id', $user->id)->first();
    }

    /**
     * Obtener el estudiante asociado al user.
     */
    public function getEstudiant(): ?Estudiant
    {
        return $this->estudiant;
    }

    /**
     * IDs de secciones del estudiante (vía inscripción activa).
     */
    public function getSeccionIds(): Collection
    {
        if ($this->seccionIds !== null) {
            return $this->seccionIds;
        }

        if (!$this->estudiant) {
            return $this->seccionIds = collect();
        }

        $inscripcion = $this->estudiant->inscripcion;
        if (!$inscripcion || !$inscripcion->seccion) {
            return $this->seccionIds = collect();
        }

        return $this->seccionIds = collect([$inscripcion->seccion_id]);
    }

    /**
     * IDs de grados del estudiante (vía inscripción activa).
     */
    public function getGradoIds(): Collection
    {
        if ($this->gradoIds !== null) {
            return $this->gradoIds;
        }

        $seccionIds = $this->getSeccionIds();
        if ($seccionIds->isEmpty()) return $this->gradoIds = collect();

        // Seccion tiene grado_id directo
        $seccion = \App\Models\app\Academy\Seccion::find($seccionIds->first());
        return $this->gradoIds = $seccion ? collect([$seccion->grado_id]) : collect();
    }

    /**
     * Scoping para Pevaluacions visibles al estudiante.
     */
    public function scopePevaluacions(Builder $query): Builder
    {
        $seccionIds = $this->getSeccionIds();
        if ($seccionIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }
        return $query->whereIn('seccion_id', $seccionIds);
    }

    /**
     * Scoping para Activities con publicación visible.
     */
    public function scopeActivities(Builder $query): Builder
    {
        $seccionIds = $this->getSeccionIds();
        if ($seccionIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->whereHas('pevaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds))
            ->whereHas('lmsPublication', fn($q) => $q->visibleNow());
    }

    /**
     * Scoping para Recursos compartidos visibles.
     */
    public function scopeResources(Builder $query): Builder
    {
        $seccionIds = $this->getSeccionIds();
        if ($seccionIds->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->where('is_visible', true)
            ->whereHas('activity.pevaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds));
    }

    /**
     * IDs de pensums asociados al estudiante.
     */
    public function getPensumIds(): Collection
    {
        $gradoIds = $this->getGradoIds();
        if ($gradoIds->isEmpty()) return collect();

        return Pensum::whereIn('grado_id', $gradoIds)->pluck('id');
    }

    /**
     * Datos completos de inscripción del estudiante.
     */
    public function getInscripcionData(): ?array
    {
        if (!$this->estudiant) return null;

        $inscripcion = $this->estudiant->inscripcion;
        if (!$inscripcion) return null;

        $seccion = $inscripcion->seccion;
        $grado = $seccion?->grado;
        $pestudio = $grado?->pestudio;

        return [
            'estudiant' => $this->estudiant,
            'inscripcion' => $inscripcion,
            'seccion' => $seccion,
            'grado' => $grado,
            'pestudio' => $pestudio,
            'peducativo' => $pestudio?->peducativo,
        ];
    }
}
```

#### 3.2 Trait para Livewire components

```php
<?php
// app/Livewire/Student/Lms/Concerns/HasStudentScope.php

namespace App\Livewire\Student\Lms\Concerns;

use App\Services\Estudiant\StudentScopeService;
use Illuminate\Support\Facades\Auth;

trait HasStudentScope
{
    protected StudentScopeService $studentService;

    public function initializeHasStudentScope(): void
    {
        $this->studentService = app(StudentScopeService::class, [
            'user' => Auth::user()
        ]);
    }

    protected function getStudentService(): StudentScopeService
    {
        return $this->studentService;
    }
}
```

---

### Fase 4: Rutas

#### 4.1 Ampliar grupo de rutas existente

```php
// routes/web.php — dentro del grupo existente (línea ~281)

// ─── LMS: Rutas de Estudiante ───────────────────────────────
Route::prefix('app/estudiante')
    ->name('student.lms.')
    ->middleware(['auth', 'isStudent'])
    ->group(function () {

    // EXISTENTES:
    Route::get('/home', \App\Livewire\Student\Lms\StudentHome::class)
        ->name('home');
    Route::get('/activity/{activity}', \App\Livewire\Student\Lms\ActivityView::class)
        ->name('activity');
    Route::get('/resource/{resource}/download', [
        \App\Http\Controllers\Lms\ResourceDownloadController::class, 'download'
    ])->name('resource.download');

    // NUEVAS:
    Route::get('/perfil', \App\Livewire\Student\Lms\Profile::class)
        ->name('profile');
    Route::get('/academica', \App\Livewire\Student\Lms\AcademicInfo::class)
        ->name('academic');
    Route::get('/lecciones', \App\Livewire\Student\Lms\LessonList::class)
        ->name('lessons');
    Route::get('/recursos', \App\Livewire\Student\Lms\ResourceList::class)
        ->name('resources');
});
```

---

### Fase 5: Livewire Components

#### 5.0 StudentHome — Dashboard de Progreso Académico (REFACTOR COMPLETO)

**El Home fue rediseñado como un dashboard de progreso** con 4 secciones, reemplazando la lista agrupada de actividades (que ahora vive en `/lecciones`):

| Sección | Descripción | Datos |
|---------|-------------|-------|
| **Stats Cards** | 4 tarjetas numéricas con íconos | Total actividades, completadas, comentarios, descargas |
| **Continuar Aprendiendo** | Últimas actividades interactuadas | Via `LmsActivityLog` (eventos `VIEW`/`COMPLETE`), deduplicadas por actividad, máx 5 |
| **Próximas Fechas Límite** | Actividades por vencer con indicador de urgencia | Activities con `ffinal >= now()`, ordenadas ascendente, badges rojo/ámbar/gris |
| **Distribución por Asignatura** | Barras de progreso por materia | Group by `asignatura.name`, con ratio completadas/totales + barra gradient |

**Componente PHP** (`app/Livewire/Student/Lms/StudentHome.php`):
```php
<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use Livewire\Component;
use WireUi\Traits\WireUiActions;

class StudentHome extends Component
{
    use WireUiActions;
    use Concerns\HasStudentScope;

    public function mount(): void
    {
        $this->initializeHasStudentScope();
    }

    public function render(): \Illuminate\View\View
    {
        $service = $this->getStudentService();
        $seccionIds = $service->getSeccionIds();

        $publishedActivityIds = LmsActivityPublication::query()
            ->visibleNow()->pluck('activity_id');

        $visibleActivityIds = Activity::whereIn('id', $publishedActivityIds)
            ->where('status', true)
            ->whereHas('pevaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds))
            ->pluck('id');

        // 1. Stats
        $totalActivities = $visibleActivityIds->count();
        $completedIds = LmsActivityLog::where('user_id', auth()->id())
            ->where('event', 'COMPLETE')
            ->whereIn('activity_id', $visibleActivityIds)
            ->pluck('activity_id')->unique();
        $stats = [
            'total'     => $totalActivities,
            'completed' => $completedIds->count(),
            'comments'  => ActivityComment::where('user_id', auth()->id())->count(),
            'downloads' => LmsActivityLog::where('user_id', auth()->id())
                ->where('event', 'RESOURCE_DOWNLOAD')->count(),
            'progress_pct' => $totalActivities > 0
                ? round(($completedIds->count() / $totalActivities) * 100) : 0,
        ];

        // 2. Continue Learning (últimas 5 actividades únicas interactuadas)
        $recentLogs = LmsActivityLog::with(['activity.pevaluacion.pensum.asignatura'])
            ->where('user_id', auth()->id())
            ->whereIn('event', ['VIEW', 'COMPLETE'])
            ->whereIn('activity_id', $visibleActivityIds)
            ->orderBy('created_at', 'desc')->take(10)->get()
            ->unique('activity_id')->take(5)->values();

        // 3. Próximas fechas límite
        $upcoming = Activity::with(['pevaluacion.pensum.asignatura', 'pevaluacion.lapso'])
            ->whereIn('id', $visibleActivityIds)
            ->whereNotNull('ffinal')->where('ffinal', '>=', now()->subDay())
            ->orderBy('ffinal', 'asc')->take(5)->get();

        // 4. Distribución por asignatura
        $activities = Activity::with('pevaluacion.pensum.asignatura')
            ->whereIn('id', $visibleActivityIds)->get();
        $completedIdsArray = $completedIds->toArray();
        $subjectDistribution = $activities
            ->groupBy(fn($a) => $a->pevaluacion?->pensum?->asignatura?->name ?? 'Sin asignatura')
            ->map(fn($acts, $name) => [
                'name'      => $name,
                'total'     => $acts->count(),
                'completed' => $acts->filter(fn($a) => in_array($a->id, $completedIdsArray))->count(),
            ])->values()->sortByDesc('total')->values();

        return view('livewire.student.lms.student-home', [
            'stats'              => $stats,
            'recentLogs'         => $recentLogs,
            'upcoming'           => $upcoming,
            'subjectDistribution' => $subjectDistribution,
        ])->layout('student.layouts.app');
    }
}
```

#### 5.1 Perfil del Estudiante — Implementado con secciones expandidas

**Archivos reales:**
- `app/Livewire/Student/Lms/Profile.php`
- `resources/views/livewire/student/lms/profile.blade.php`

**El componente `Profile` fue mejorado para incluir:**

| Sección | Campos |
|---------|--------|
| **Stats rápidas** | Total actividades, lecciones, comentarios (tarjetas numéricas) |
| **Datos Personales** | Nombre completo, cédula con nacionalidad (V/E), género, fecha de nacimiento, edad, nacionalidad |
| **Lugar de Nacimiento** | Ciudad, Estado, País (oculto si vacío) |
| **Contacto** | Correo electrónico, correo clases virtuales (gsemail), celular, teléfono, dirección de residencia |
| **Representante** | Nombre, cédula, celular, correo (oculto si no hay representante vinculado) |
| **Información Institucional** | Grado, sección, plan de estudio, programa educativo |
| **Enlaces Rápidos** | Accesos directos a Académica, Lecciones, Recursos, Inicio |

**Componente PHP** (`app/Livewire/Student/Lms/Profile.php`):
```php
<?php

namespace App\Livewire\Student\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Services\Estudiant\StudentScopeService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public ?array $profileData = null;
    public ?array $stats = null;

    public function mount(): void
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $this->profileData = $service->getInscripcionData();

        $seccionIds = $service->getSeccionIds();
        if ($seccionIds->isNotEmpty()) {
            $publishedActivityIds = LmsActivityPublication::query()
                ->visibleNow()->pluck('activity_id');

            $activities = Activity::whereIn('id', $publishedActivityIds)
                ->whereHas('pevaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds))
                ->get();

            $this->stats = [
                'total_activities' => $activities->count(),
                'total_lessons'    => $activities->whereHas('lmsPublication', fn($q) => $q->visibleNow())->count(),
                'total_comments'   => ActivityComment::whereIn('activity_id', $activities->pluck('id'))
                    ->approved()->count(),
            ];
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.profile')
            ->layout('student.layouts.app');
    }
}
```

**Vista Blade** — 7 secciones en cards con `divide-y`, etiquetas uppercase tracking-widest, y soporte dark mode.
Ver archivo completo en `resources/views/livewire/student/lms/profile.blade.php`.

#### 5.2 Información Académica (NUEVO)

```php
<?php
// app/Livewire/Student/Lms/AcademicInfo.php

namespace App\Livewire\Student\Lms;

use App\Services\Estudiant\StudentScopeService;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Lapso;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AcademicInfo extends Component
{
    public ?array $inscripcionData = null;
    public $pensums;
    public $pevaluacions;
    public $currentLapsoId;
    public Collection $areaStats;

    public function mount(): void
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $this->inscripcionData = $service->getInscripcionData();

        $this->currentLapsoId = Lapso::current()?->id;

        // Pensums del grado del estudiante
        $this->pensums = $service->getPensumsWithAsignatura();

        // Pevaluacions del estudiante (actividades de planificación)
        $this->pevaluacions = Pevaluacion::with([
            'pensum.asignatura',
            'lapso',
            'profesor',
        ])
        ->whereIn('seccion_id', $service->getSeccionIds())
        ->when($this->currentLapsoId, fn($q) => $q->where('lapso_id', $this->currentLapsoId))
        ->orderBy('created_at', 'desc')
        ->get();

        // Stats por área de formación
        $this->areaStats = $this->computeAreaStats($service);
    }

    protected function computeAreaStats(StudentScopeService $service): Collection
    {
        $seccionIds = $service->getSeccionIds();
        if ($seccionIds->isEmpty() || $this->pensums->isEmpty()) {
            return collect();
        }

        $stats = [];

        foreach ($this->pensums as $pensum) {
            $activityIds = Activity::whereHas('pevaluacion', fn($q) =>
                $q->whereIn('seccion_id', $seccionIds)
                  ->where('pensum_id', $pensum->id)
            )->pluck('id');

            $stats[$pensum->id] = [
                'activities' => $activityIds->count(),
                'lessons'    => Activity::whereIn('id', $activityIds)
                    ->whereHas('lmsPublication', fn($q) => $q->visibleNow())
                    ->count(),
                'comments'   => ActivityComment::where('is_approved', true)
                    ->whereIn('activity_id', $activityIds)
                    ->count(),
            ];
        }

        return collect($stats);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.student.lms.academic-info')
            ->layout('student.layouts.app');
    }
}
```

```blade
{{-- Cada fila de Planificación Académica muestra los stats del área (pensum) debajo del nombre --}}
@if($stat)
<div class="flex items-center gap-3 mt-2">
    <span class="inline-flex items-center gap-1 text-[10px] text-gray-400">
        <svg class="w-3 h-3 text-sky-400">...</svg>
        {{ $stat['activities'] }} activ.
    </span>
    <span class="inline-flex items-center gap-1 text-[10px] text-gray-400">
        <svg class="w-3 h-3 text-emerald-400">...</svg>
        {{ $stat['lessons'] }} lecc.
    </span>
    <span class="inline-flex items-center gap-1 text-[10px] text-gray-400">
        <svg class="w-3 h-3 text-amber-400">...</svg>
        {{ $stat['comments'] }} coment.
    </span>
</div>
@endif
```

#### 5.3 Listado de Lecciones (NUEVO — mejora sobre StudentHome)

```php
<?php
// app/Livewire/Student/Lms/LessonList.php

namespace App\Livewire\Student\Lms;

use App\Services\Estudiant\StudentScopeService;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lapso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class LessonList extends Component
{
    use WithPagination;

    public string $search = '';
    public $lapsoId = '';
    public $asignaturaId = '';
    protected $paginationTheme = 'tailwind';

    public function render(): \Illuminate\View\View
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);
        $seccionIds = $service->getSeccionIds();

        $query = Activity::with([
            'pevaluacion.pensum.asignatura',
            'pevaluacion.profesor',
            'pevaluacion.lapso',
            'lmsPublication',
        ])->whereHas('pevaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds))
          ->whereHas('lmsPublication', fn($q) => $q->visibleNow());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('topic', 'like', "%{$this->search}%")
                  ->orWhere('thematic', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%");
            });
        }

        if ($this->lapsoId) {
            $query->whereHas('pevaluacion', fn($q) => $q->where('lapso_id', $this->lapsoId));
        }

        if ($this->asignaturaId) {
            $query->whereHas('pevaluacion.pensum', fn($q) => $q->where('asignatura_id', $this->asignaturaId));
        }

        $activities = $query->orderBy('lmsPublication.published_at', 'desc')
            ->paginate(12);

        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.student.lms.lesson-list', [
            'activities' => $activities,
            'lapsos'     => $lapsos,
        ])->layout('student.layouts.app');
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
    public function updatingAsignaturaId() { $this->resetPage(); }
}
```

#### 5.4 Recursos Compartidos (NUEVO)

```php
<?php
// app/Livewire/Student/Lms/ResourceList.php

namespace App\Livewire\Student\Lms;

use App\Services\Estudiant\StudentScopeService;
use App\Models\app\Academy\Lms\LmsActivityResource;
use App\Models\app\Academy\Lapso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ResourceList extends Component
{
    use WithPagination;

    public string $search = '';
    public $lapsoId = '';
    public bool $showPreviewModal = false;
    public ?array $previewResource = null;
    protected $paginationTheme = 'tailwind';

    public function render(): \Illuminate\View\View
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);

        $query = LmsActivityResource::with([
            'activity.pevaluacion.pensum.asignatura',
            'activity.pevaluacion.profesor',
            'activity.pevaluacion.lapso',
            'media',
            'section',
        ]);

        $query = $service->scopeResources($query);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('display_name', 'like', "%{$this->search}%")
                  ->orWhereHas('activity', fn($aq) => $aq->where('topic', 'like', "%{$this->search}%"));
            });
        }

        if ($this->lapsoId) {
            $query->whereHas('activity.pevaluacion', fn($q) => $q->where('lapso_id', $this->lapsoId));
        }

        $resources = $query->orderBy('created_at', 'desc')->paginate(15);

        $lapsos = Lapso::orderBy('finicial', 'desc')->pluck('name', 'id');

        return view('livewire.student.lms.resource-list', [
            'resources' => $resources,
            'lapsos'    => $lapsos,
        ])->layout('student.layouts.app');
    }

    public function preview(int $resourceId): void
    {
        $service = app(StudentScopeService::class, ['user' => Auth::user()]);

        $resource = LmsActivityResource::with([
            'activity',
            'media',
            'section',
        ])->findOrFail($resourceId);

        // Security check: ensure resource belongs to student's section
        $seccionIds = $service->getSeccionIds();
        $belongsToStudent = $resource->activity?->pevaluacion
            && $seccionIds->contains($resource->activity->pevaluacion->seccion_id);

        if (!$belongsToStudent) {
            $this->notification()->error(
                title: 'Acceso denegado',
                description: 'Este recurso no está disponible para tu sección.'
            );
            return;
        }

        $this->previewResource = $resource->toArray();
        $this->showPreviewModal = true;
    }

    public function closePreview(): void
    {
        $this->showPreviewModal = false;
        $this->previewResource = null;
    }

    public function updatingSearch() { $this->resetPage(); }
    public function updatingLapsoId() { $this->resetPage(); }
}
```

#### 5.5 ActivityView — Comentarios, Progreso y Marcar Completada (mejora del componente existente)

Mejora del `ActivityView` existente para incluir comentarios, tracking de progreso (`LmsActivityProgress`) y marcado de completado:

**Scoping:** Si la actividad no es visible para el estudiante, se lanza `abort(404)` — no se usa el patrón `$accessDenied` con render condicional. Esto evita que el componente se renderice con datos incompletos y retorna HTTP 404.

**Propiedades nuevas:**
```php
use App\Models\app\Academy\Lms\ActivityComment;
use App\Models\app\Academy\Lms\LmsActivityProgress;
use App\Livewire\Student\Lms\Concerns\HasStudentScope;

// Nuevas propiedades:
public $comments;
public string $newComment = '';
public $completed = false;
// NOTA: $accessDenied fue eliminado — se usa abort(404) en su lugar
```

**En mount(), se deben inicializar en orden:**
```php
public function mount(Activity $activity): void
{
    $this->initializeHasStudentScope();  // ← Obligatorio: inicializa StudentScopeService

    // Gate de visibilidad: abort(404) si no es visible
    if (!$this->studentService->isActivityVisible($activity)) {
        abort(404);
    }

    $this->activity = $activity;

    // Cargar secciones, recursos, enlaces, embeds (filtrados por is_visible)
    $this->sections = $activity->lmsSections()
        ->where('is_visible', true)
        ->with(['visibleContents.media'])->get();
    // ... resources, links, htmlEmbeds ...

    // Comentarios aprobados
    $this->comments = ActivityComment::with('user')
        ->forActivity($activity->id)
        ->approved()
        ->orderBy('created_at', 'desc')
        ->get();

    // Estado de completado (chequea ambas fuentes por retro-compatibilidad)
    $this->completed =
        LmsActivityProgress::where('activity_id', $activity->id)
            ->where('student_id', auth()->id())
            ->where('status', 'COMPLETED')->exists()
        ||
        LmsActivityLog::where('activity_id', $activity->id)
            ->where('user_id', auth()->id())
            ->where('event', 'COMPLETE')->exists();

    // Registrar/actualizar progreso
    $progress = LmsActivityProgress::firstOrCreate(
        ['activity_id' => $activity->id, 'student_id' => auth()->id()],
        ['status' => 'IN_PROGRESS', 'first_access_at' => now(), 'last_access_at' => now()]
    );
    if (!$progress->wasRecentlyCreated) {
        $progress->update(['last_access_at' => now()]);
    }

    LmsActivityLog::record($activity->id, auth()->id(), 'VIEW');
}
```

**Marcar como completada:**
```php
public function markComplete(): void
{
    LmsActivityLog::record($this->activity->id, auth()->id(), 'COMPLETE');

    LmsActivityProgress::updateOrCreate(
        ['activity_id' => $this->activity->id, 'student_id' => auth()->id()],
        ['status' => 'COMPLETED', 'completion_pct' => 100,
         'completed_at' => now(), 'last_access_at' => now()]
    );

    $this->completed = true;

    $this->notification()->success(
        title: '¡Actividad completada!',
        description: 'Has marcado esta actividad como completada.'
    );
}
```

**Guardar comentario:**
```php
public function saveComment(): void
{
    $this->validate(['newComment' => 'required|string|min:1|max:1000']);

    ActivityComment::create([
        'activity_id' => $this->activity->id,
        'user_id'     => auth()->id(),
        'body'        => $this->newComment,
        'is_approved' => false, // Pendiente de moderación
    ]);

    $this->newComment = '';
    $this->notification()->success(
        title: 'Comentario enviado',
        description: 'Tu comentario será visible una vez aprobado.'
    );
}
```

#### 5.6 Mermaid Diagrams — Fullscreen en ActivityView

Los diagramas Mermaid renderizados en `activity-view.blade.php` incluyen un toolbar con zoom y botón de pantalla completa. El toolbar aparece al hacer hover sobre el diagrama.

**Implementación:**
- El toolbar se genera automáticamente en `mermaidEmbed._createToolbar()` (`resources/js/lms-student-preview.js`)
- Incluye: zoom in/out, porcentaje, ajustar al ancho, **pantalla completa**, reset
- El botón fullscreen usa `element.requestFullscreen()` del Fullscreen API

**Estilos CSS** (al final de `activity-view.blade.php`, dentro del root div, sin `@once`):
- `[x-data="mermaidEmbed()"]:fullscreen` — fondo, centrado, padding
- `.mermaid-zoom-toolbar` en fullscreen — `position: fixed`, siempre visible
- `.mermaid-fill-height` — SVG ocupa todo el alto disponible

> **Nota:** Originalmente el bloque `<style>` estaba envuelto en `@once`, pero esto causaba que Livewire 3 detectara "Multiple root elements" (`@once` fuera del root div en compilaciones anteriores, o interferencia con re-renders). Se eliminó `@once` — CSS es idempotente y no hay penalidad en tenerlo sin envoltorio.
```

---

#### 5.6 Barra de navegación "Volver" en ActivityView

Se agregó una barra de navegación superior en `activity-view.blade.php` con un enlace para retroceder a la lista de lecciones:

```blade
{{-- resources/views/livewire/student/lms/activity-view.blade.php --}}
{{-- Arriba del header, antes del título de la actividad --}}
<nav class="flex items-center gap-3 px-1">
    <a href="{{ route('student.lms.lessons') }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium
              text-gray-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400
              hover:bg-emerald-50 dark:hover:bg-emerald-500/10
              border border-transparent hover:border-emerald-200 dark:hover:border-emerald-500/20
              transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Volver a Lecciones
    </a>
    <span class="text-[11px] text-gray-400 dark:text-gray-500 hidden sm:inline">
        / {{ $activity->pevaluacion?->pensum?->asignatura?->name ?? 'Actividad' }}
    </span>
</nav>
```

**Detalles:**
- Enlace directo a `route('student.lms.lessons')` como destino principal (origen más común)
- Indicador contextual de la asignatura actual (hidden en mobile)
- Estilos consistentes con el layout oscuro del estudiante
- Transiciones suaves en hover

---

#### 5.7 Vista Previa de Recursos (Modal)

Se agregó un modal de vista previa en `ResourceList` que permite al estudiante visualizar el contenido del recurso sin descargarlo.

**Componente** (`ResourceList.php`):
- `showPreviewModal` (bool) — controla apertura/cierre del modal
- `previewResource` (?array) — datos del recurso a previsualizar
- `preview(int $resourceId)` — carga el recurso con `media` y verifica que pertenezca a la sección del estudiante
- `closePreview()` — limpia el estado

**Modal** (`resource-list.blade.php`):
- Detecta el tipo MIME del recurso (vía `$media['mime_type']`) y renderiza:
  - **Imagen** (`image/*`): `<img>` con `object-contain`, max 60vh
  - **PDF** (`application/pdf`): `<iframe>` a 65vh con borde
  - **Video** (`video/*`): `<video controls>` con `<source>`
  - **Otros**: mensaje "Vista previa no disponible" + metadatos del archivo
- Botón "Vista previa" (ojo ícono) en cada tarjeta de recurso, al lado de "Descargar"
- Footer con nombre original, tamaño y botón de descarga directa
- Backdrop semitransparente con `backdrop-blur-sm`

```
Tarjeta de recurso:
┌──────────────────────────────────┐
│ [icono] display_name             │
│         actividad_topic          │
├──────────────────────────────────┤
│ asignatura   [Vista previa] [Descargar] │
└──────────────────────────────────┘
```

---

### Fase 6: Navegación y Vistas

#### 6.1 Navbar del estudiante (en layout existente)

```blade
{{-- resources/views/student/layouts/app.blade.php — agregar navbar --}}

{{-- Dentro del nav o header del layout --}}
<nav class="flex items-center gap-1">
    <a href="{{ route('student.lms.home') }}"
       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
              {{ request()->routeIs('student.lms.home') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
        Inicio
    </a>
    <a href="{{ route('student.lms.profile') }}"
       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
              {{ request()->routeIs('student.lms.profile') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
        Perfil
    </a>
    <a href="{{ route('student.lms.academic') }}"
       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
              {{ request()->routeIs('student.lms.academic') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
        Académica
    </a>
    <a href="{{ route('student.lms.lessons') }}"
       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
              {{ request()->routeIs('student.lms.lessons') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
        Lecciones
    </a>
    <a href="{{ route('student.lms.resources') }}"
       class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
              {{ request()->routeIs('student.lms.resources') ? 'bg-emerald-500/10 text-emerald-400' : 'text-gray-400 hover:text-emerald-300' }}">
        Recursos
    </a>
</nav>
```

#### 6.2 Vista de Recursos (Blade)

```blade
{{-- resources/views/livewire/student/lms/resource-list.blade.php --}}
<div class="max-w-6xl mx-auto py-8 px-4 space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold text-gray-900 dark:text-white">Recursos Compartidos</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Material descargable de tus actividades
            </p>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <input type="text" wire:model.live.debounce.300ms="search"
               placeholder="Buscar recurso o actividad…"
               class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm"/>
        <select wire:model.live="lapsoId"
                class="w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2 text-sm appearance-none">
            <option value="">Todos los lapsos</option>
            @foreach($lapsos as $id => $name)
                <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Grid de recursos --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($resources as $resource)
            <div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4 space-y-3 hover:border-emerald-500/30 transition-colors">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $resource->display_name }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">
                            {{ $resource->activity?->topic ?? '—' }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                    <span class="text-[10px] text-gray-400">
                        {{ $resource->activity?->pevaluacion?->pensum?->asignatura?->name ?? '' }}
                    </span>
                    <a href="{{ route('student.lms.resource.download', $resource) }}"
                       class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-[10px] font-bold text-emerald-400 hover:text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/20 transition-all">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <p class="text-gray-500 font-medium">No hay recursos disponibles</p>
                <p class="text-xs text-gray-400 mt-1">Los recursos aparecerán cuando los profesores los compartan.</p>
            </div>
        @endforelse
    </div>

    @if($resources->hasPages())
        <div class="pt-4">{{ $resources->links('vendor.livewire.custom-tailwind') }}</div>
    @endif
</div>
```

---

### Fase 7: Seguridad y Validación

#### 7.1 Matriz de autorización

| Recurso | Middleware | Scope | Admin bypass |
|---------|-----------|-------|-------------|
| `/app/estudiante/home` | `IsStudent` | `scopePevaluacions()` | Sí |
| `/app/estudiante/perfil` | `IsStudent` | Ninguno (solo su perfil) | Sí |
| `/app/estudiante/academica` | `IsStudent` | `getInscripcionData()` | Sí |
| `/app/estudiante/lecciones` | `IsStudent` | `scopeActivities()` | Sí |
| `/app/estudiante/recursos` | `IsStudent` | `scopeResources()` | Sí |
| `/app/estudiante/activity/{id}` | `IsStudent` | `isVisibleToStudents()` | Sí |
| `/app/estudiante/resource/{id}/download` | `IsStudent` | `scopeResources()` | Sí |

#### 7.2 Validaciones específicas

```php
// Validación de comentarios:
'newComment' => 'required|string|min:1|max:1000',

// Protección rate-limit para comentarios (opcional):
// 5 comentarios por minuto por usuario
```

#### 7.3 Moderación de comentarios

Los comentarios de estudiantes pasan por un flujo de aprobación:

```
Estudiante escribe comentario
    → is_approved = false (pendiente)
    → Profesor/Jefe de Área ve comentario pendiente
    → Aprueba o rechaza
    → Estudiante ve solo comentarios aprobados
```

---

### Fase 8: Testing

#### 8.1 Pirámide de tests

```
    ┌──────────────────────────┐
    │  Feature: Flujo completo │  ← 3 tests
    │  estudiante               │
    ├──────────────────────────┤
    │  Feature: Scope por      │  ← 4 tests
    │  inscripción              │
    ├──────────────────────────┤
    │  Unit: Model + Service   │  ← 6 tests
    │  + Middleware + Comments  │
    └──────────────────────────┘
```

#### 8.2 Tests críticos

| Test | Tipo | Verifica |
|------|------|----------|
| `StudentMiddlewareTest` | Feature | Acceso con `is_student=true` → 200 |
| `StudentMiddlewareTest` | Feature | Acceso con `is_student=false` → 403 |
| `StudentScopeTest` | Unit | `getSeccionIds()` retorna sección del estudiante |
| `StudentScopeTest` | Unit | `scopeActivities()` solo retorna actividades con publicación visible |
| `StudentScopeTest` | Unit | `scopeResources()` solo retorna recursos de su sección |
| `StudentScopeTest` | Unit | Estudiante sin inscripción → colecciones vacías |
| `ActivityCommentTest` | Feature | Estudiante crea comentario |
| `ActivityCommentTest` | Feature | Comentario aparece solo si está aprobado |
| `ProfileViewTest` | Feature | Perfil muestra datos correctos |
| `AcademicInfoTest` | Feature | Vista académica muestra pensums |
| `ResourceListTest` | Feature | Listado de recursos paginado |
| `UserModelTest` | Unit | `isStudent()` retorna booleano correcto |
| `UserModelTest` | Unit | `getRoleLabelAttribute` retorna 'Estudiante' |

#### 8.3 Factory support

```php
// database/factories/UserFactory.php
public function student(): static
{
    return $this->state(fn (array $attributes) => [
        'is_student' => true,
    ]);
}

// database/factories/ActivityCommentFactory.php (NUEVO)
public function definition(): array
{
    return [
        'activity_id' => Activity::factory(),
        'user_id'     => User::factory()->student(),
        'body'        => fake()->sentence(),
        'is_approved' => false,
    ];
}
```

---

## 7. ADRs (Architecture Decision Records)

### ADR-001: `is_student` como columna booleana

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Columna booleana en `users` | Tabla pivote roles |
| **Razón** | Consistencia con `is_admin`, `is_profesor`, `is_planner` | |
| **Consecuencia** | Sin dependencias nuevas. Migración inmediata | |

### ADR-002: Scope vía `Inscripcion → Seccion` en lugar de estudiante directo

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Scoping por `seccion_id` vía inscripción activa | Scoping por `user_id` directo |
| **Razón** | Un estudiante pertenece a una sección (vía inscripción). Las actividades se asignan a secciones, no a estudiantes individuales | |
| **Consecuencia** | `StudentScopeService` calcula sección desde inscripción activa. Si no hay inscripción → sin datos | |

### ADR-003: `StudentScopeService` como servicio dedicado

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | `StudentScopeService` como clase independiente | Lógica en cada componente Livewire |
| **Razón** | El scope se usa en 5-6 componentes. DRY. Testable de forma aislada | |
| **Consecuencia** | Binding via `app(StudentScopeService::class, ['user' => $user])` | |

### ADR-004: Layout dedicado vs layout global

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Mantener `student.layouts.app` como layout dedicado | Usar layout global compartido |
| **Razón** | La experiencia del estudiante es radicalmente distinta (consumidor vs gestor). Layout ya existe | |
| **Consecuencia** | Cada componente usa `#[Layout('student.layouts.app')]` | |

### ADR-005: Comentarios requieren aprobación (moderación)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Comentarios con `is_approved = false` por defecto. Solo visibles tras aprobación | Comentarios inmediatos sin moderación |
| **Razón** | Contenido generado por estudiantes requiere supervisión. El profesor debe poder moderar | |
| **Consecuencia** | `scopeActivities()` filtra por `is_approved = true`. El profesor necesita UI de moderación | |

### ADR-006: Modelo `ActivityComment` separado (no reusar tabla `comments` genérica)

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | Modelo dedicado `ActivityComment` con FK a `activities` | Tabla polimórfica `comments` |
| **Razón** | Relación directa 1:N con Activity. Simple, sin polymorphic overhead. Fácil de scoping por sección vía join | |
| **Consecuencia** | Una tabla nueva con migración. Consultas directas sin morph | |

### ADR-007: `abort(404)` en vez de `$accessDenied` flag

| | Decisión | Alternativa |
|--|----------|-------------|
| **Selección** | `abort(404)` cuando la actividad no es visible | `$accessDenied = true` con render condicional |
| **Razón** | Renderizar el componente con `$accessDenied=true` retorna HTTP 200 con contenido vacío. El consumidor esperado (estudiante) debe recibir un HTTP 404 auténtico si la actividad no existe o no es visible. Además, evita que Livewire procese un template condicional que podría tener problemas de múltiples root elements | |
| **Consecuencia** | El `mount()` lanza `NotFoundHttpException` si `isActivityVisible()` falla. No se necesita `@if($accessDenied)` en el template. Los tests verifican `assertStatus(404)` |

---

## 8. Dependencias y Roadmap

### Mapa de archivos

```
NUEVOS:
  database/migrations/xxxx_add_is_student_to_users_table.php
  database/migrations/xxxx_create_activity_comments_table.php
  app/Models/app/Academy/Lms/ActivityComment.php
  app/Policies/ActivityCommentPolicy.php
  app/Services/Estudiant/StudentScopeService.php
  app/Livewire/Student/Lms/Concerns/HasStudentScope.php
  app/Livewire/Student/Lms/Profile.php
  app/Livewire/Student/Lms/AcademicInfo.php
  app/Livewire/Student/Lms/LessonList.php
  app/Livewire/Student/Lms/ResourceList.php
  resources/views/livewire/student/lms/profile.blade.php
  resources/views/livewire/student/lms/academic-info.blade.php
  resources/views/livewire/student/lms/lesson-list.blade.php
  resources/views/livewire/student/lms/resource-list.blade.php
  tests/Feature/Student/ (suite)

MODIFICADOS:
  app/Models/User.php
  app/Models/app/Academy/Activity.php           (+ comments relation)
  app/Livewire/Student/Lms/StudentHome.php      (+ HasStudentScope trait)
  app/Livewire/Student/Lms/ActivityView.php     (+ comments section)
  resources/views/student/layouts/app.blade.php  (+ navbar items)
  routes/web.php                                 (+ nuevas rutas)
  database/factories/UserFactory.php             (+ student state)
```

### Timeline estimado

| Fase | Archivos | Tiempo |
|------|----------|--------|
| 1. Migration + Models | 4 | 40 min |
| 2. Middleware + Policy | 2 | 15 min |
| 3. StudentScopeService | 2 | 45 min |
| 4. Rutas | 1 | 15 min |
| 5a. Profile | 2 | 30 min |
| 5b. AcademicInfo | 2 | 45 min |
| 5c. LessonList | 2 | 45 min |
| 5d. ResourceList | 2 | 45 min |
| 5e. Comments en ActivityView | 1 | 30 min |
| 6. Navbar | 1 | 15 min |
| 7. Testing | ~12 tests | 90 min |
| **Total** | **~20 archivos** | **~7 horas** |

---

## 9. Checklist de Rollback

- [ ] `php artisan migrate:rollback --step=2` (is_student + activity_comments)
- [ ] Remover `is_student` de `$fillable`, `$casts`, `isStudent()`, `getRoleLabelAttribute()` en User model
- [ ] Eliminar `app/Models/app/Academy/Lms/ActivityComment.php`
- [ ] Eliminar `app/Policies/ActivityCommentPolicy.php`
- [ ] Eliminar `app/Services/Estudiant/StudentScopeService.php`
- [ ] Eliminar `app/Livewire/Student/Lms/Concerns/` (directorio completo)
- [ ] Eliminar `app/Livewire/Student/Lms/Profile.php`
- [ ] Eliminar `app/Livewire/Student/Lms/AcademicInfo.php`
- [ ] Eliminar `app/Livewire/Student/Lms/LessonList.php`
- [ ] Eliminar `app/Livewire/Student/Lms/ResourceList.php`
- [ ] Eliminar vistas Blade en `resources/views/livewire/student/lms/`
- [ ] Revertir navbar en `resources/views/student/layouts/app.blade.php`
- [ ] Revertir rutas en `web.php` (eliminar rutas nuevas, mantener existentes)
- [ ] Revertir cambios en `Activity.php` (remover relación `comments`)
- [ ] Revertir cambios en `StudentHome.php` y `ActivityView.php`
- [ ] Eliminar tests de estudiante
- [ ] `php artisan optimize:clear`

---

## 10. Registro de Cambios

| Fecha | Cambio | Archivos modificados |
|---|---|---|
| 2026-07-27 | Plan inicial (blueprint) | — |
| 2026-07-28 | Migración `is_student`, migración `activity_comments`, modelo `ActivityComment`, `StudentScopeService`, todos los componentes Livewire (Profile, AcademicInfo, LessonList, ResourceList), vistas Blade, navbar, rutas, policies. Namespace real: `Services\Estudiant\StudentScopeService` | ~20 archivos |
| 2026-07-28 | Campo `rejected_by`, `rejected_at`, `rejected_reason` en ActivityComment + migrations. Métodos `approve()`, `reject()`, `scopePending()`, `scopeRejected()`, `scopeApproved()` | 1 migración + ActivityComment.php |
| 2026-07-28 | Integración `LmsActivityLog` en ActivityView (eventos VIEW y COMPLETE). Método `markComplete()` | ActivityView.php |
| 2026-07-28 | Suite de tests de estudiante (~50+ tests) | `tests/Unit/Estudiant/`, `tests/Feature/Estudiant/`, `tests/Feature/Lms/` |
| **2026-07-29** | **Perfil mejorado**: stats cards (actividades, lecciones, comentarios), lugar de nacimiento, sección de contacto, sección de representante, edad, nacionalidad, enlaces rápidos. Documentación actualizada. | `Profile.php`, `profile.blade.php`, `implementations.md` |
| **2026-07-29** | **Fix WireUI Alpine error**: `@wireUiScripts` faltaba en `student/layouts/app.blade.php`. Sin esta directiva, el JS de WireUI no se cargaba en las páginas de estudiante, causando `wireui_notifications is not defined`. Se agregó antes de `@livewireScripts`. | `resources/views/student/layouts/app.blade.php` |
| **2026-07-30** | **Fix buscador Home**: se movió la carga de datos de `mount()` a `render()` para reactividad. `$search` ahora filtra por topic/thematic/description de activities, name de asignatura, y name/lastname de profesor. Se quitó `public $pevaluacions` (ahora es variable de vista). Se agregó `.debounce.300ms` al input search. | `StudentHome.php`, `student-home.blade.php` |
| **2026-07-30** | **Dashboard de progreso académico (Opción A)**: Rediseño completo del Home. Se reemplazó la lista agrupada de actividades por un dashboard con 4 secciones: (1) Stats cards (totales, completadas, comentarios, descargas), (2) Continuar Aprendiendo (últimas 5 actividades interactuadas vía LmsActivityLog), (3) Próximas fechas límite (actividades por vencer con indicador de urgencia), (4) Distribución por asignatura (barras de progreso por materia). El buscador se eliminó del Home porque la funcionalidad de búsqueda/filtro vive en Lecciones. | `StudentHome.php`, `student-home.blade.php` |
| **2026-07-30** | **LmsActivityProgress + COMPLETE event + Fix `@once` + `abort(404)`**: Nuevo modelo para tracking granular de progreso. Migración `add_complete_event_to_lms_activity_logs`. Refactor `ActivityView.mount()` con `firstOrCreate`. Fix `@once` en activity-view.blade.php (causaba "Multiple root elements"). `abort(404)` en lugar de `$accessDenied` (ADR-007). Test de root element único agregado. | `ActivityView.php`, `activity-view.blade.php`, `StudentAccessTest.php`, `implementations.md`, `activity-lifecycle.md` |
| **2026-08-08** | **P1+P2 · Fix rectángulo NEGRO en diagramas SVG (contenido IMAGE truncado)**: El generador (LLM) emitía SVG con un tag de apertura sin su `>` que se comía el `</svg>` (p.ej. `<rect ... rx="8"</svg>`); el navegador lo pintaba como rect negro sólido (fill por defecto) tapando medio diagrama (contenido 2232, actividad 85) y rompía el cierre del `<svg>`. **P1 (datos+generador)**: reparados los bodies 2232 (caja "PODER SIMBÓLICO" completada con contenido fiel de la sección) y 2238 (caja A de sección 2); validación `LmsSvgRepairService::isWellFormed()` en `GenerateIllustrationLesson::generate()` y `LessonWizard::generateSectionIllustration()` para rechazar SVG malformados antes de guardar. **P2 (defensa en render)**: `repair()` aplicado en los dos puntos que emiten body SVG crudo — `lessons-print.blade.php` (rama IMAGE) y `_content-renderer.blade.php` — elimina el tag roto y reinserta `</svg>`; un contenido dañado pierde solo el elemento incompleto, nunca pinta negro. Tests: `tests/Unit/Lms/LmsSvgRepairServiceTest.php` (8) + `StudentLessonsPrintTest::test_print_repairs_truncated_svg_content`. | `app/Services/Lms/LmsSvgRepairService.php` (nuevo), `GenerateIllustrationLesson.php`, `LessonWizard.php`, `lessons-print.blade.php`, `_content-renderer.blade.php`, `tests/Unit/Lms/`, `StudentLessonsPrintTest.php`, `implementations.md` |
| **2026-08-08** | **P3–P7 · Optimización de la vista de impresión + refactors**: **(P3)** 7 typos CSS corregidos en `lessons-print.blade.php` (`flex-wrap:gap:`→`flex-wrap:wrap;gap:`, `fontsize:`→`font-size:` ×4, `margin=`→`margin:`, `width=`→`width:`); clamp del topic a 2 líneas en `@media print` (en pantalla queda completo). **(P4)** Clasificador de contenido unificado `LmsContentClassifier` (isImageBody/isMermaidBody/extractMermaidCode — 8 sitios duplicados en 4 vistas → 1 fuente de verdad, 8 tests). **(P5)** `LmsPublicationStatus::label()/cssClass()` reemplaza los métodos privados duplicados en 3 controllers de impresión (student/director/profesor). **(P6)** no estructural (tradeoff de diseño documentado). **(P7)** Footer dinámico: "N secciones · M contenidos" reales en vez de "1 lección". | `lessons-print.blade.php`, `LmsContentClassifier.php` (nuevo), `LmsPublicationStatus.php` (nuevo), `ActivityPrintController.php`, `Director/LessonsPrintController.php`, `Profesor/Lms/LessonsPrintController.php`, `tests/Unit/Lms/` |
| **2026-08-08** | **Recorte de canvas SVG al contenido real (espacio vacío inferior)**: El generador dibuja sobre un canvas alto fijo (1000×950) usando solo la parte superior → al escalar a la columna de impresión quedaba 22–60% de espacio vacío (medido: 2232=60%, 2238=47%, 1928=22%, 1988=23%). `LmsSvgRepairService::cropToContent()` recalcula el viewBox al bounding box real (rects+texts, margen 10–40px, umbral conservador ≥20% de vacío) y se aplica en la generación (`GenerateIllustrationLesson`, `LessonWizard`) y a los datos de la actividad 85 (2232→420, 2238→455). Backups: `/tmp/pdf_analysis/backup_20260808_bodies.json`, `backup_svg_crop_20260808.json`. | `LmsSvgRepairService.php`, `GenerateIllustrationLesson.php`, `LessonWizard.php`, `tests/Unit/Lms/LmsSvgRepairServiceTest.php`, `implementations.md` |
| **2026-08-08** | **`LmsSvgAiRepairService` — reparación IA con fallback de diagramas SVG en BD**: Servicio usable desde cualquier lugar (comando `lms:repair-svgs`, tinker, Livewire, jobs). Pipeline: (1) detección de daños (`damageReport`: tag roto, svg sin cierre, atributo cortado, canvas desproporcionado); (2) reparación IA con cadena de modelos fallback vía `LmsAiOrchestrationService::askWithCompaction` (primario claude-sonnet-4 + 2 fallbacks, validador `isWellFormed` por modelo) y system prompt nivel Staff Engineer (`LmsSvgAiRepairService::getSystemPrompt()` — preserva diseño, completa solo lo inferible, nunca fill por defecto, viewBox ajustado); (3) normalización `cropToContent`; (4) red de seguridad determinista (`repair()`); (5) persistencia opcional con backup JSON. Comando: `php8.2 artisan lms:repair-svgs [--dry-run] [--ids=...] [--limit=...] [--models=...]`. E2E real: contenido 1928 reparado vía IA (tag_roto + canvas 22%) con claude-sonnet-4. DTO `SvgRepairResult`. 9 tests unitarios. | `app/Services/Lms/LmsSvgAiRepairService.php` (nuevo), `app/Services/Lms/SvgRepairResult.php` (nuevo), `app/Console/Commands/RepairSvgs.php` (nuevo), `tests/Unit/Lms/LmsSvgAiRepairServiceTest.php` (nuevo), `implementations.md` |

---

## 11. Deploy y Verificación en Producción

### Pasos para desplegar cambios en producción

```bash
# 1. Deployar todos los archivos modificados y nuevos al servidor de producción
#    (rsync, deploy script, o git pull según el flujo del equipo)

# 2. Ejecutar migraciones pendientes
php8.2 artisan migrate

# 3. Limpiar vistas compiladas en disco
php8.2 artisan view:clear

# 4. Resetear OPCache — CRÍTICO si OPCache tiene validate_timestamps=0
#    (sin este paso, PHP sirve la versión cacheadas en memoria, no los archivos en disco)
php8.2 artisan opcache:reset
#    O alternativamente:
#    sudo systemctl restart php8.2-fpm

# 5. Limpiar cache de rutas y config (si se modificaron)
php8.2 artisan route:cache
php8.2 artisan config:cache

# 6. Verificar las URLs clave
```

### Verificación post-deploy

| URL | Qué verificar |
|-----|--------------|
| `http://cfla.local/app/estudiante/activity/{id}` | Actividades problemáticas cargan sin error "Multiple root elements" |
| `http://cfla.local/app/estudiante/home` | Dashboard de progreso carga correctamente |
| `http://cfla.local/app/estudiante/lecciones` | Listado de lecciones con filtros funciona |
| `http://cfla.local/app/estudiante/perfil` | Perfil con stats y datos personales |

### Nota sobre OPCache

La configuración `opcache.validate_timestamps=0` (común en producción) hace que PHP-FPM **no revise si los archivos PHP cambiaron en disco** — sirve la versión compilada en memoria hasta que se reinicie el pool o se llame a `opcache_reset()`. Esto significa que:

- `php8.2 artisan view:clear` borra las vistas compiladas del disco, pero OPCache puede seguir sirviendo la versión anterior de las vistas que Livewire acaba de re-compilar
- Si una vista Blade se modificó (como `activity-view.blade.php`), el cambio NO se refleja hasta que OPCache se resetee
- El flujo correcto es: **deploy → `view:clear` → `opcache:reset`** (o restart php-fpm)
- `php8.2 artisan opcache:reset` requiere el paquete `laravellegends/pt-br-validator-validator` o implementación propia (verificar si está instalado)

### Bug histórico: "Multiple root elements detected"

**Síntoma:** `http://cfla.local/app/estudiante/activity/24` lanza "Multiple root elements detected for component: [student.lms.activity-view]" mientras que `activity/40` funciona correctamente.

**Causa raíz (doble):**

1. **Template:** El bloque `<style>` en `activity-view.blade.php` estaba envuelto en `@once`, lo que en condiciones específicas de re-render de Livewire 3 provocaba que el `@once` se ubicara fuera del root div en la compilación de Blade, resultando en 2+ root elements. En producción, OPCache con `validate_timestamps=0` agravó el problema al no reflejar la corrección hasta que se reseteó manualmente.

2. **Datos (HTML malformado):** El **Content 432** de la actividad 24 contenía un diagrama Mermaid con HTML desbalanceado: **3 `<div>` de apertura pero 4 `</div>` de cierre** (1 extra). Esto causaba que al renderizar `{!! $content->body !!}`, el `</div>` sobrante cerrara prematuramente los wrappers del template (`text-sm` → `bg-gray-50` → `section-content`), dejando las secciones siguientes, recursos, enlaces, comentarios y el bloque `<style>` **fuera del root div** del componente. La actividad 40 no tenía este problema (su Content 1179 tenía 3 aperturas y 3 cierres, balanceado).

**Solución aplicada:**
1. Eliminar `@once` del bloque `<style>` (CSS es idempotente)
2. Resetear OPCache post-deploy
3. **Corregir el HTML del Content 432** — se eliminó el `</div>` extra de la base de datos (`preg_replace("/\s*<\/div>\s*$/", "")`)
4. Test agregado: `test_published_activity_has_single_root_element` en `StudentAccessTest.php`

**Recomendación a futuro:** Agregar validación de balance de etiquetas HTML al guardar `LmsActivityContent.body` para prevenir este tipo de corrupción de layout. Escanear otros contenidos existentes no encontró más casos de `<div>`s desbalanceados.

**Prueba de regresión:** El test `test_published_activity_has_single_root_element` replica la detección de Livewire (`DOMDocument::loadHTML` + conteo de `XML_ELEMENT_NODE` en `<body>`) y verifica exactamente 1 root element.

---

## 12. Archivos sin seguimiento (para commit)

Los siguientes archivos nuevos deben ser agregados al repositorio:

| Archivo | Propósito |
|---------|-----------|
| `app/Models/app/Academy/Lms/LmsActivityProgress.php` | Modelo de progreso individual por estudiante |
| `database/migrations/2026_07_30_155700_create_lms_activity_progress_table.php` | Migración: tabla `lms_activity_progress` |
| `database/migrations/2026_07_30_155701_add_complete_event_to_lms_activity_logs.php` | Migración: enum COMPLETE en `lms_activity_logs.event` |
| `blueprint/estudiant/activity-lifecycle.md` | Documentación del ciclo de vida de actividades |
| `blueprint/estudiant/progress-dashboard.md` | Documentación del dashboard de progreso |

