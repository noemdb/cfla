# Dashboard de Progreso del Estudiante

**Panel de Progreso Académico — `/app/estudiante/home`**
_Última revisión:_ 2026-07-30

---

## Tabla de Contenidos

1. [Visión General](#1-visión-general)
2. [Componente Livewire](#2-componente-livewire)
3. [Sección 1: Stats Cards](#3-sección-1-stats-cards)
4. [Sección 2: Continuar Aprendiendo](#4-sección-2-continuar-aprendiendo)
5. [Sección 3: Próximas Publicaciones](#5-sección-3-próximas-publicaciones)
6. [Sección 4: Distribución por Asignatura](#6-sección-4-distribución-por-asignatura)
7. [Empty State](#7-empty-state)
8. [Visibilidad y Scoping de Datos](#8-visibilidad-y-scoping-de-datos)
9. [Estructura de Routes](#9-estructura-de-routes)
10. [Referencia de Queries](#10-referencia-de-queries)
11. [Sistema de Tarjetas Unificado](#11-sistema-de-tarjetas-unificado)

---

## 1. Visión General

El Dashboard de Progreso es la página de inicio del área del estudiante (`/app/estudiante/home`). Muestra un resumen visual del avance académico del estudiante en las actividades LMS publicadas, combinando 4 secciones de datos en una sola página.

### Propósito

- Dar al estudiante una vista rápida de **cuánto ha avanzado** en sus actividades
- Mostrar **qué actividades visitó recientemente** para retomarlas
- Mostrar **próximas publicaciones** (lecciones que aún no se publican, con su fecha `publish_at`)
- Visualizar la **distribución de progreso por asignatura**

### Arquitectura

```
StudentHome (Livewire full-page component)
├── routes/web.php → Route::prefix('app/estudiante')
│   └── GET /home → StudentHome::class → name: 'student.lms.home'
├── app/Livewire/Student/Lms/StudentHome.php
│   ├── mount() → initializeHasStudentScope()
│   └── render() → 4 queries scoped → compact view data
└── resources/views/livewire/student/lms/student-home.blade.php
    ├── Header + Stats Cards (grid 4-col)
    ├── Continue Learning (lista de interacciones)
    ├── Próximas Publicaciones (lecciones por publicarse, badges a publish_at)
    ├── Subject Distribution (barras de progreso)
    └── Empty State (condicional)
```

### Datos calculados en `render()` — no en `mount()`

Toda la carga de datos ocurre en el método `render()`, no en `mount()`. Esto es fundamental porque permite que Livewire reactive los datos en cada petición (subida de archivos, polling, eventos) sin recargar la página.

---

## 2. Componente Livewire

**Archivo:** `app/Livewire/Student/Lms/StudentHome.php`

### Propiedades y Traits

```php
class StudentHome extends Component
{
    use WireUiActions;
    use Concerns\HasStudentScope;

    public function mount(): void
    {
        $this->initializeHasStudentScope();
    }
}
```

- **`WireUiActions`**: Permite lanzar notificaciones con `$this->notification()->success(...)`.
- **`HasStudentScope`**: Trait propio que inicializa `StudentScopeService` para obtener los IDs de sección del estudiante autenticado.

### Flujo de `render()`

```php
public function render(): \Illuminate\View\View
{
    // 1. Obtener IDs de sección del estudiante vía StudentScopeService
    $service = $this->getStudentService();
    $seccionIds = $service->getSeccionIds();

    // 2. Actividades publicadas vigentes (visibleNow)
    $publishedActivityIds = LmsActivityPublication::query()->visibleNow()->pluck('activity_id');

    // 3. Filtrar por secciones del estudiante
    $visibleActivityIds = Activity::whereIn('id', $publishedActivityIds)
        ->whereHas('pevaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds))
        ->pluck('id');

    // 4-7. Calcular stats, logs recientes (+ fallback), próximos vencimientos, distribución

    return view('livewire.student.lms.student-home', [
        'stats'              => $stats,
        'recentLogs'         => $recentLogs,
        'suggestedActivities' => $suggestedActivities,
        'upcoming'           => $upcoming,
        'subjectDistribution' => $subjectDistribution,
    ])->layout('student.layouts.app');
}
```

**Layout:** `student.layouts.app` — navbar con 5 enlaces (Inicio, Perfil, Académica, Lecciones, Recursos), modo oscuro forzado, menú hamburguesa responsive.

---

## 3. Sección 1: Stats Cards

### Vista

```
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│  📘 Totales     │ │  ✅ Completadas │ │  💬 Comentarios │ │  ⬇️ Descargas   │
│  12             │ │  5              │ │  3              │ │  7              │
│  Actividades    │ │  42% del total  │ │  En actividades │ │  Rec. descarg.  │
│  publicadas     │ │                 │ │                 │ │                 │
└─────────────────┘ └─────────────────┘ └─────────────────┘ └─────────────────┘
```

### Grid

```html
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
    <!-- 4 cards aquí -->
</div>
```

Responsive: 2 columnas en mobile (`grid-cols-2`), 4 columnas en ≥sm (`sm:grid-cols-4`).

### Estructura de cada card

Cada card es un `div` con:
- `bg-white dark:bg-gray-800/50` — fondo
- `border border-gray-200 dark:border-gray-700` — borde
- `rounded-xl` — esquinas redondeadas
- `p-4 space-y-2 shadow-sm` — padding interior + sombra base
- Hover **estático** (no clicable): `transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800/70 hover:border-gray-300 dark:hover:border-gray-600` — brillo sutil, sin lift (ver [Sistema de Tarjetas Unificado](#11-sistema-de-tarjetas-unificado))

Contenido interno:
1. **Ícono** — `w-8 h-8 rounded-lg` con color de fondo semitransparente (opacity 10%) e ícono SVG del mismo color (opacity 100%)
2. **Etiqueta** — `text-[10px] font-semibold uppercase tracking-wider`
3. **Número** — `text-2xl font-bold`
4. **Subtítulo** — `text-[11px]`

### Origen de datos

```php
$completedIds = LmsActivityLog::where('user_id', auth()->id())
    ->where('event', 'COMPLETE')
    ->whereIn('activity_id', $visibleActivityIds)
    ->pluck('activity_id')->unique();

$stats = [
    'total'       => $visibleActivityIds->count(),
    'completed'   => $completedIds->count(),
    'comments'    => ActivityComment::where('user_id', auth()->id())->count(),
    'downloads'   => LmsActivityLog::where('user_id', auth()->id())
                        ->where('event', 'RESOURCE_DOWNLOAD')->count(),
    'progress_pct' => $totalActivities > 0
        ? round(($completedIds->count() / $totalActivities) * 100)
        : 0,
];
```

| Card | Ícono | Color | Data source | Query |
|------|-------|-------|-------------|-------|
| **Totales** | Libro(sky) | `text-sky-400`, `bg-sky-500/10` | `count($visibleActivityIds)` | Actividades con publicación vigente + sección del estudiante |
| **Completadas** | Check(círculo)(emerald) | `text-emerald-400`, `bg-emerald-500/10` | `LmsActivityLog::where('event','COMPLETE')` | Distinct `activity_id` scoped al estudiante |
| **Comentarios** | Burbuja(chat)(amber) | `text-amber-400`, `bg-amber-500/10` | `ActivityComment::where('user_id')` | Todos los comentarios del estudiante (sin scope de sección) |
| **Descargas** | Flecha(abajo)(purple) | `text-purple-400`, `bg-purple-500/10` | `LmsActivityLog::where('event','RESOURCE_DOWNLOAD')` | Logs de descarga del estudiante (sin scope de sección) |

### Porcentaje

Se muestra un texto condicional:
- Si `$stats['total'] > 0`: "`{{ $stats['progress_pct'] }}% del total"`
- Si no hay actividades: "Sin actividades"

---

## 4. Sección 2: Continuar Aprendiendo

### Vista

```
📖 Continuar Aprendiendo

┌──────────────────────────────────────────────────────────────────────┐
│ ✅ [check emerald]  Ecuaciones lineales                Matemática · │
│                     Completado                       Prof. López   › │
├──────────────────────────────────────────────────────────────────────┤
│ 🎬 [play sky]      Análisis sintáctico                Lenguaje ·   │
│                     hace 2 días                       Prof. Pérez  › │
└──────────────────────────────────────────────────────────────────────┘
```

### Origen de datos

```php
$recentLogs = LmsActivityLog::with([
    'activity.pevaluacion.pensum.asignatura',    // Nombre de la materia
    'activity.pevaluacion.profesor',             // Nombre del profesor
])
    ->where('user_id', auth()->id())             // Solo este estudiante
    ->whereIn('event', ['VIEW', 'COMPLETE'])     // Solo eventos relevantes
    ->whereIn('activity_id', $visibleActivityIds) // Solo actividades visibles
    ->orderBy('created_at', 'desc')              // Más reciente primero
    ->take(10)                                   // Trae 10 para poder deduplicar
    ->get()
    ->unique('activity_id')                      // 1 log por actividad (el más reciente)
    ->take(5)                                    // Máximo 5 actividades
    ->values();
```

### Estructura de cada fila

Cada elemento es un `<a>` que enlaza a `route('student.lms.activity', $activity)`.

```
┌──────────────────────────────────────────────────────────────────────┐
│ ┌─────────┐   título (truncated)                              ›     │
│ │  ícono  │   materia · profesor                            tag/ts  │
│ └─────────┘                                                         │
└──────────────────────────────────────────────────────────────────────┘
```

**Diferenciación visual por tipo de evento:**

| Evento | Fondo del ícono | Ícono | Badge/Texto |
|--------|----------------|-------|-------------|
| `COMPLETE` | `bg-emerald-500/10` | Checkmark `text-emerald-400` | "Completado" |
| `VIEW` | `bg-sky-500/10` | Play `text-sky-400` | `$log->created_at->diffForHumans()` (ej. "hace 2 días") |

### Comportamiento de hover

```css
.group:hover .group-hover:text-emerald-600  /* título */
.group:hover .group-hover:text-emerald-500  /* flecha › */
.group:hover .hover:-translate-y-0.5        /* elevación sutil */
.group:hover .hover:shadow-md               /* sombra al elevar */
.group:hover .hover:border-emerald-500/40   /* borde de la card */
```

Transiciones: `transition-all duration-200` (elevación — ver [Sistema de Tarjetas Unificado](#11-sistema-de-tarjetas-unificado))

### Fallback: sin historial de interacción

> **Decisión de diseño:** cuando el estudiante no tiene eventos `VIEW`/`COMPLETE` en `LmsActivityLog` (historial recién limpiado o cuenta nueva), "Continuar Aprendiendo" no debe quedarse vacía. El fallback lista las lecciones **ya publicadas** (`publish_at <= now()`), de la **más recientemente publicada a la más lejana** (`publish_at` DESC), máximo 5. El fallback se muestra bajo el título propio **"Publicaciones Recientes"** (la rama con historial conserva "Continuar Aprendiendo"). Complementa a "Próximas Publicaciones" (que cubre `publish_at` futuro): **sin solapamiento**.

```php
// Solo se ejecuta cuando no hay logs (evita una query extra con historial)
$suggestedActivities = $recentLogs->isEmpty()
    ? Activity::with([
        'pevaluacion.pensum.asignatura',
        'pevaluacion.profesor',
        'lmsPublication',
    ])
        ->whereIn('id', $visibleActivityIds)
        ->whereHas('lmsPublication', fn ($q) => $q->where('publish_at', '<=', now()))
        ->orderBy(
            LmsActivityPublication::select('publish_at')
                ->whereColumn('lms_activity_publications.activity_id', 'activities.id')
                ->orderByDesc('publish_at')
                ->limit(1),
            'desc'
        )
        ->take(5)
        ->get()
    : collect();
```

Mismo patrón de subquery que `$upcoming` (sección 5), pero con segundo argumento `'desc'` y filtro `publish_at <= now()`.

**Estructura de cada fila del fallback** (idéntica a las filas de logs, sin etiqueta de estado):

- Título de la sección en el fallback: **"Publicaciones Recientes"** (la rama con historial usa "Continuar Aprendiendo").
- Ícono **play sky** (`bg-sky-500/10`, `text-sky-400`) — son lecciones para *empezar*, no completadas.
- Subtítulo de la sección: *"Actividades publicadas más recientes"*.
- Hint derecho: `$activity->lmsPublication?->publish_at?->diffForHumans()` → "hace 2 días".
- Sin etiqueta "Vista previa" (la query garantiza `publish_at <= now()`).
- Acentos 100% sky (header, íconos, título, chevron) y hover de fila en sky (`hover:-translate-y-0.5 hover:shadow-md hover:border-sky-500/40`) — ver [Sistema de Tarjetas Unificado](#11-sistema-de-tarjetas-unificado).

El blade usa `@if($recentLogs->isNotEmpty()) ... @elseif($suggestedActivities->isNotEmpty()) <section>...`.

---

## 5. Sección 3: Próximas Publicaciones

> **Decisión de diseño:** para el estudiante, `publish_at` es la fecha más relevante de la lección. `activity.ffinal` (fecha de cierre/corrección) generaba confusión, así que **desaparece de este panel**. La sección lista solo lecciones que aún **no** se han publicado (`publish_at` en el futuro), ordenadas por `publish_at` ascendente (la que se publica antes, primero). Las ya publicadas salen de la sección.

### Vista

```
⏰ Próximas Publicaciones

┌──────────────────────────────────────────────────────────────────────┐
│ 🔵 [reloj sky]   Funciones cuadráticas              Matemática · L3 │
│                                   ┌──────────────────────────────┐ │
│                                   │ Se publica en 2 días         │ │
│                                   └──────────────────────────────┘ │
├──────────────────────────────────────────────────────────────────────┤
│ 🔵 [reloj sky]   Ecuaciones                        Matemática · L3 │
│                                   ┌──────────────────────────────┐ │
│                                   │ Se publica el 9 ago          │ │
│                                   └──────────────────────────────┘ │
├──────────────────────────────────────────────────────────────────────┤
│ 🔵 [reloj sky]   Trigonometría                      Matemática · L1 │
│                                   ┌──────────────────────────────┐ │
│                                   │ Se publica hoy a las 16:00   │ │
│                                   └──────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────┘
```

*(Todas las filas son lecciones en vista previa: `PUBLISHED` con `publish_at` futuro. Ya no hay urgencia roja/ámbar ni countdown en este panel.)*

### Origen de datos

```php
$upcoming = Activity::with([
    'pevaluacion.pensum.asignatura',   // Nombre de la materia
    'pevaluacion.lapso',               // Lapso (L1, L2, L3)
    'lmsPublication',                  // Datos de publicación (publish_at)
])
    ->whereIn('id', $visibleActivityIds)   // Solo actividades visibles
    ->whereHas('lmsPublication', fn($q) => $q->where('publish_at', '>', now()))
    ->orderBy(                             // Orden: la más próxima primero
        LmsActivityPublication::select('publish_at')
            ->whereColumn('lms_activity_publications.activity_id', 'activities.id')
            ->orderByDesc('publish_at')
            ->limit(1)
    )
    ->take(5)                              // Máximo 5
    ->get();
```

> El filtro `publish_at > now()` garantiza que solo aparezcan publicaciones futuras. Cada `activity_id` tiene a lo sumo una fila en `lms_activity_publications`, así que la subquery de orden es estable. Las lecciones ya publicadas (`publish_at <= now()`) siguen visibles en "Continuar Aprendiendo" y en el listado de lecciones, pero ya no aparecen aquí.

### Badge de publicación (1 nivel, azul cielo)

Calculado en el Blade con:

```php
$publishAt = $activity->lmsPublication?->publish_at;
$daysLeft = $publishAt
    ? now()->startOfDay()->diffInDays($publishAt->copy()->startOfDay(), false)
    : null;
```

| Condición | Texto del badge |
|:----------|:----------------|
| `publish_at` es hoy | `Se publica hoy a las {H:i}` |
| `daysLeft === 1` | `Se publica mañana` |
| `daysLeft <= 7` | `Se publica en {X} días` |
| `daysLeft > 7` | `Se publica el {j M}` (traducción `es`, ej. "Se publica el 9 ago") |
| `publish_at` nulo (fallback) | `Próximamente` |

- Badge e ícono siempre en azul cielo; el badge usa la forma de chip unificada (`rounded-full` + borde sky — ver [Sistema de Tarjetas Unificado](#11-sistema-de-tarjetas-unificado)); ya no hay niveles de urgencia.
- Junto al título se muestra la etiqueta **"Vista previa"** (sky), igual que en "Continuar Aprendiendo".
- El countdown es respecto a `publish_at`, **no** a `ffinal`.

### Estructura de cada fila

Cada elemento es un `<a>` que enlaza a `route('student.lms.activity', $activity)`.

```
┌──────────────────────────────────────────────────────────────────────┐
│ ┌─────────┐   título (truncated)                    ┌─────────────┐ │
│ │  reloj  │   materia · lapso                       │  badge      │ │
│ └─────────┘                                          └─────────────┘ │
└──────────────────────────────────────────────────────────────────────┘
```

Hover: elevación sutil + borde `hover:border-sky-500/40` (ver [Sistema de Tarjetas Unificado](#11-sistema-de-tarjetas-unificado)).

---

## 6. Sección 4: Distribución por Asignatura

### Vista

```
📊 Distribución por Asignatura

┌──────────────────────────────────────────────────────────────────────┐
│ Matemática                                       5/12 · 42%         │
│ ██████████████████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░           │
│ (barra gradient verde, ancho = 42%)                                 │
│                                                                      │
│ Lenguaje                                         3/8  · 38%         │
│ ████████████████░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░           │
│ (barra gradient verde, ancho = 38%)                                 │
└──────────────────────────────────────────────────────────────────────┘
```

### Origen de datos

```php
$activities = Activity::with('pevaluacion.pensum.asignatura')
    ->whereIn('id', $visibleActivityIds)
    ->get();

$completedIdsArray = $completedIds->toArray();

$subjectDistribution = $activities
    ->groupBy(fn($a) => $a->pevaluacion?->pensum?->asignatura?->name ?? 'Sin asignatura')
    ->map(fn($acts, $name) => [
        'name'      => $name,
        'total'     => $acts->count(),
        'completed' => $acts->filter(fn($a) => in_array($a->id, $completedIdsArray))->count(),
    ])
    ->values()
    ->sortByDesc('total')           // Materia con más actividades primero
    ->values();
```

### Barra de progreso

```html
<div class="w-full h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
    <div class="h-full rounded-full transition-all duration-500"
         style="width: {{ $pct }}%; background: linear-gradient(90deg, #10b981, #34d399);">
    </div>
</div>
```

| Propiedad | Valor |
|-----------|-------|
| Alto | `h-2` (8px) |
| Esquinas | `rounded-full` |
| Fondo contenedor | `bg-gray-100` (light) / `bg-gray-700` (dark) |
| Relleno | Gradient horizontal de `#10b981` (emerald-500) a `#34d399` (emerald-400) |
| Animación | `transition-all duration-500` — se anima al cambiar el ancho |
| Ancho | `width: {{ $pct }}%` donde `$pct = round(($completed / $total) * 100)` |
| Overflow | `overflow-hidden` en el contenedor |

### Contenedor de la sección

```html
<div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-5 shadow-sm transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800/70 hover:border-gray-300 dark:hover:border-gray-600">
    <!-- cada asignatura -->
</div>
```

Misma base que las stats (`p-4 shadow-sm`, hover **estático** sin lift — ver [Sistema de Tarjetas Unificado](#11-sistema-de-tarjetas-unificado)). Se conserva `space-y-5` para el aire interno entre barras.

---

## 7. Empty State

Cuando **no hay actividades visibles** (`$stats['total'] === 0`, `$recentLogs->isEmpty()`, `$upcoming->isEmpty()`):

```
        📘
  No hay actividades publicadas
  Cuando tus profesores publiquen contenido,
         aparecerá aquí.
```

```html
<div class="text-center py-16">
    <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" ... />
    <p class="text-gray-500 dark:text-gray-400 font-medium">No hay actividades publicadas</p>
    <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">
        Cuando tus profesores publiquen contenido, aparecerá aquí.
    </p>
</div>
```

El ícono es el mismo de "libro" (Totales) pero en gris. Ocupa `py-16` centrado.

---

## 8. Visibilidad y Scoping de Datos

### Cadena de visibilidad completa

```
LmsActivityPublication.status === 'PUBLISHED'
    AND publish_at <= now()
    AND unpublish_at >= now()  // scopeVisibleNow()
        ↓
Activity.status === true
        ↓
Pevaluacion.seccion_id IN (seccionIds del estudiante)
        ↓
Resultado: visibleActivityIds (collection de IDs)
```

### StudentScopeService

El trait `HasStudentScope` provee:

```php
protected function getStudentService(): StudentScopeService
{
    return app(StudentScopeService::class);
}
```

### LmsActivityProgress

El modelo `LmsActivityProgress` (`app/Models/app/Academy/Lms/LmsActivityProgress.php`) registra el progreso individual de cada estudiante en cada actividad:

```php
// Creado o actualizado en ActivityView::mount()
LmsActivityProgress::firstOrCreate(
    ['activity_id' => $activityId, 'student_id' => auth()->id()],
    ['status' => 'IN_PROGRESS', 'first_access_at' => now(), 'last_access_at' => now()]
);

// Marcado como completado en ActivityView::markComplete()
LmsActivityProgress::updateOrCreate(
    ['activity_id' => $activityId, 'student_id' => auth()->id()],
    ['status' => 'COMPLETED', 'completion_pct' => 100, 'completed_at' => now()]
);
```

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `status` | enum(NOT_STARTED, IN_PROGRESS, COMPLETED) | Estado actual |
| `completion_pct` | decimal(5,2) | Porcentaje de avance |
| `time_spent_secs` | unsigned int | Tiempo acumulado (futuro) |
| `first_access_at` | datetime | Primera visita |
| `last_access_at` | datetime | Última visita |
| `completed_at` | datetime | Momento de completado |

**Flujo de resolución de `seccionIds`:**

```
User autenticado
  → Estudiant (modelo Learner)
    → Inscripcion (matrícula activa del año actual)
      → Seccion (sección asignada)
        → seccion_id (único o múltiple si tiene varias)
```

### Scope de LmsActivityLog y ActivityComment

Algunas queries **no** están scoped por sección:

| Query | Scoped por sección? | Nota |
|-------|:-------------------:|------|
| `$completedIds` | ✅ | Filtrado por `$visibleActivityIds` |
| `$commentsCount` | ❌ | Cuenta total de comentarios del estudiante |
| `$downloadsCount` | ❌ | Cuenta total de descargas del estudiante |
| `$recentLogs` | ✅ | Filtrado por `$visibleActivityIds` |
| `$suggestedActivities` | ✅ | Filtrado por `$visibleActivityIds` + `publish_at <= now()`, ORDER BY subquery DESC (solo si `$recentLogs` está vacío) |
| `$upcoming` | ✅ | Filtrado por `$visibleActivityIds` + `publish_at > now()` |
| `$subjectDistribution` | ✅ | Calculado sobre `$visibleActivityIds` |

**Nota:** Comments y Downloads son contadores globales del estudiante, no scoped a las actividades visibles. Esto es intencional — muestran la actividad total del estudiante en el sistema, no solo lo que está actualmente publicado.

---

## 9. Estructura de Routes

**Archivo:** `routes/web.php` (líneas 351-362)

```php
// ─── LMS: Rutas de Estudiante ─────────────────────────────────────
Route::prefix('app/estudiante')->name('student.lms.')->middleware(['auth', 'isStudent'])->group(function () {
    Route::get('/home', \App\Livewire\Student\Lms\StudentHome::class)->name('home');
    Route::get('/perfil', \App\Livewire\Student\Lms\Profile::class)->name('profile');
    Route::get('/academica', \App\Livewire\Student\Lms\AcademicInfo::class)->name('academic');
    Route::get('/lecciones', \App\Livewire\Student\Lms\LessonList::class)->name('lessons');
    Route::get('/recursos', \App\Livewire\Student\Lms\ResourceList::class)->name('resources');
    Route::get('/activity/{activity}', \App\Livewire\Student\Lms\ActivityView::class)->name('activity');
    Route::get('/resource/{resource}/download', [
        \App\Http\Controllers\Lms\ResourceDownloadController::class, 'download'
    ])->name('resource.download');
});
```

**Middleware:** `auth` + `isStudent` (solo usuarios con rol de estudiante).

**Navbar** (`student.layouts.app`):
| Ruta | Nombre | Label |
|------|--------|-------|
| `student.lms.home` | `home` | Inicio |
| `student.lms.profile` | `profile` | Perfil |
| `student.lms.academic` | `academic` | Académica |
| `student.lms.lessons` | `lessons` | Lecciones |
| `student.lms.resources` | `resources` | Recursos |

---

## 10. Referencia de Queries

### Query 1: Actividades visibles (combinada)

```sql
SELECT a.id
FROM activities a
INNER JOIN pevaluacions p ON a.pevaluacion_id = p.id
WHERE a.id IN (
    SELECT activity_id
    FROM lms_activity_publications
    WHERE status = 'PUBLISHED'
      AND publish_at <= NOW()
      AND (unpublish_at IS NULL OR unpublish_at >= NOW())
)
AND a.status = 1
AND p.seccion_id IN (?, ?, ?);  -- secciones del estudiante
```

### Query 2: IDs de actividades completadas

```sql
SELECT DISTINCT activity_id
FROM lms_activity_logs
WHERE user_id = ?
  AND event = 'COMPLETE'
  AND activity_id IN (?, ?, ...);  -- visibleActivityIds
```

### Query 3: Logs recientes (Continuar Aprendiendo)

```sql
SELECT l.*
FROM lms_activity_logs l
WHERE l.user_id = ?
  AND l.event IN ('VIEW', 'COMPLETE')
  AND l.activity_id IN (?, ?, ...)
ORDER BY l.created_at DESC
LIMIT 10;
```

Post-procesamiento PHP: `->unique('activity_id')->take(5)->values()`

### Query 3b: Fallback "Publicaciones Recientes" (sin historial)

```sql
SELECT a.*
FROM activities a
WHERE a.id IN (?, ?, ...)  -- visibleActivityIds
  AND EXISTS (
      SELECT 1 FROM lms_activity_publications p
      WHERE p.activity_id = a.id
        AND p.publish_at <= NOW()
  )
ORDER BY (
    SELECT p.publish_at FROM lms_activity_publications p
    WHERE p.activity_id = a.id
    ORDER BY p.publish_at DESC
    LIMIT 1
) DESC
LIMIT 5;
```

Solo se ejecuta cuando no hay logs recientes (`$recentLogs->isEmpty()`). Complementa a Query 4 sin solapamiento (`publish_at <= now()` vs `publish_at > now()`).

### Query 4: Próximas publicaciones

```sql
SELECT a.*
FROM activities a
WHERE a.id IN (?, ?, ...)
  AND EXISTS (
      SELECT 1 FROM lms_activity_publications p
      WHERE p.activity_id = a.id
        AND p.publish_at > NOW()
  )
ORDER BY (
    SELECT p.publish_at FROM lms_activity_publications p
    WHERE p.activity_id = a.id
    ORDER BY p.publish_at DESC
    LIMIT 1
) ASC
LIMIT 5;
```

### Query 5: Distribución por asignatura

```sql
SELECT a.id, a.topic, p.id as pevaluacion_id, pens.id as pensum_id, asig.name as asignatura
FROM activities a
INNER JOIN pevaluacions p ON a.pevaluacion_id = p.id
INNER JOIN pensums pens ON p.pensum_id = pens.id
INNER JOIN asignaturas asig ON pens.asignatura_id = asig.id
WHERE a.id IN (?, ?, ...);
```

Post-procesamiento PHP: `groupBy('asignatura')`, `map(completed/total)`, `sortByDesc('total')`

---

## 11. Sistema de Tarjetas Unificado

> **Decisión de diseño:** las cuatro familias de tarjetas del panel (métricas, filas de cursos, panel de asignaturas) comparten una misma base visual para leerse como un solo sistema. La diferenciación se logra por **contenido y acento**, no rompiendo el lenguaje visual. Implementado el 2026-08-05.

### Token base (todas las tarjetas)

```
bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm
```

- Radio `rounded-xl` y borde gris (`gray-200` / `gray-700`) idénticos en las 4 familias.
- `shadow-sm` uniforme — profundidad sutil y moderna, sin sombras agresivas.
- Padding `p-4` en todas (el panel de asignaturas pasó de `p-5` a `p-4`).

### Hover por tipo

| Tipo | Token | Dónde |
|------|-------|-------|
| **Estáticas** (métricas ×4, panel de asignaturas) | `transition-colors duration-200 hover:bg-gray-50 dark:hover:bg-gray-800/70 hover:border-gray-300 dark:hover:border-gray-600` | Brillo sutil: borde más visible + fondo +1 nivel. **Sin elevación** ni sombra — no son clicables y no deben ofrecer affordance falsa |
| **Clicables** (filas Continuar/Recientes/Próximas) | `transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-{accent}-500/40` | Elevación sutil + sombra + borde acento. `emerald` en Continuar Aprendiendo; `sky` en Recientes y Próximas |

Los títulos y chevrons de las filas llevan `group-hover` con su acento (`group-hover:text-emerald-600`/`text-emerald-400`, `group-hover:text-sky-600`/`text-sky-400`; chevron `group-hover:text-emerald-500` / `group-hover:text-sky-500`).

### Chip unificado (etiquetas y countdown)

Un solo lenguaje: `rounded-full` + borde + `bg-{accent}-100 dark:bg-{accent}-500/10 border-{accent}-300 dark:border-{accent}-500/30`:

| Chip | Token |
|------|-------|
| "Vista previa" (amber / sky) | `shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-semibold uppercase tracking-wider text-{accent}-700 dark:text-{accent}-300 bg-{accent}-100 dark:bg-{accent}-500/10 border border-{accent}-300 dark:border-{accent}-500/30` |
| Countdown (sky) | `shrink-0 text-[11px] font-medium whitespace-nowrap px-2.5 py-1 rounded-full border bg-sky-100 dark:bg-sky-500/10 border-sky-300 dark:border-sky-500/30 text-sky-700 dark:text-sky-300` |

Misma forma (pill + borde); la diferencia es de contenido: el countdown lleva texto dinámico ("Se publica hoy a las H:i") que no puede ser uppercase de 9px.

### Paleta de acentos

| Acento | Rol | Uso |
|--------|-----|-----|
| **emerald** | avance / éxito | stat Completadas, barras de progreso, íconos COMPLETE, sección Continuar Aprendiendo |
| **sky** | información / publicación | stat Totales, íconos VIEW, Publicaciones Recientes, Próximas Publicaciones, Asignaturas |
| **amber** | pendiente / aviso | stat Comentarios, etiqueta "Vista previa" (amber) |
| **purple** | recursos | stat Descargas |

> **Por qué hover diferenciado:** las métricas y el panel de asignaturas no enlazan a nada; elevarlas implicaría una affordance falsa (sugerir que son clicables). Responden solo con brillo sutil. Las filas sí enlazan a una actividad y se elevan con acento, señalando interactividad.

---

## Historial de Cambios

| Fecha | Cambio | Autor |
|-------|--------|-------|
| 2026-08-05 | **Sistema de tarjetas unificado** en todo el panel: base común `rounded-xl` + `border-gray-200/700` + `p-4` + `shadow-sm`; hover por tipo (estáticas = brillo sutil sin lift; clicables = `-translate-y-0.5` + `shadow-md` + borde acento); fallback "Publicaciones Recientes" corregido a 100% sky; chips unificados `rounded-full` + borde (Vista previa y countdown); panel de asignaturas de `p-5` a `p-4` | — |
| 2026-08-05 | El fallback de "Continuar Aprendiendo" pasa a titularse **"Publicaciones Recientes"** (la rama con historial conserva "Continuar Aprendiendo"); docs y tests actualizados | — |
| 2026-08-05 | "Continuar Aprendiendo" gana fallback: sin historial de interacción (`LmsActivityLog` vacío) muestra las lecciones ya publicadas (`publish_at <= now()`), reciente primero (`publish_at` DESC), máx. 5; nueva query `$suggestedActivities`, fila play sky + hint "hace X", subtítulo "Actividades publicadas más recientes" | — |
| 2026-08-05 | Sección 3 pasa a "Próximas Publicaciones": solo lecciones con `publish_at` futuro, ordenadas por `publish_at`; `ffinal` deja de usarse en el panel; badges a `publish_at` ("Se publica en X días" / "Se publica mañana" / "Se publica hoy a las H:i" / "Se publica el {j M}") | — |
| 2026-07-30 | Creación inicial del documento | — |
