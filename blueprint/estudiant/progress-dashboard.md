# Dashboard de Progreso del Estudiante

**Panel de Progreso Académico — `/app/estudiante/home`**
_Última revisión:_ 2026-07-30

---

## Tabla de Contenidos

1. [Visión General](#1-visión-general)
2. [Componente Livewire](#2-componente-livewire)
3. [Sección 1: Stats Cards](#3-sección-1-stats-cards)
4. [Sección 2: Continuar Aprendiendo](#4-sección-2-continuar-aprendiendo)
5. [Sección 3: Próximas Fechas Límite](#5-sección-3-próximas-fechas-límite)
6. [Sección 4: Distribución por Asignatura](#6-sección-4-distribución-por-asignatura)
7. [Empty State](#7-empty-state)
8. [Visibilidad y Scoping de Datos](#8-visibilidad-y-scoping-de-datos)
9. [Estructura de Routes](#9-estructura-de-routes)
10. [Referencia de Queries](#10-referencia-de-queries)

---

## 1. Visión General

El Dashboard de Progreso es la página de inicio del área del estudiante (`/app/estudiante/home`). Muestra un resumen visual del avance académico del estudiante en las actividades LMS publicadas, combinando 4 secciones de datos en una sola página.

### Propósito

- Dar al estudiante una vista rápida de **cuánto ha avanzado** en sus actividades
- Mostrar **qué actividades visitó recientemente** para retomarlas
- Alertar sobre **fechas de entrega próximas** con códigos de urgencia
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
    ├── Upcoming Deadlines (lista con badges de urgencia)
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

    // 4-7. Calcular stats, logs recientes, próximos vencimientos, distribución

    return view('livewire.student.lms.student-home', [
        'stats'              => $stats,
        'recentLogs'         => $recentLogs,
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
- `p-4 space-y-2` — padding interior

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
.group:hover .hover:border-emerald-500/30   /* borde de la card */
```

Transiciones: `transition-all duration-200`

---

## 5. Sección 3: Próximas Fechas Límite

### Vista

```
⏰ Próximas Fechas Límite

┌──────────────────────────────────────────────────────────────────────┐
│ 🔴 [reloj red]    Trigonometría                       Matemática · │
│                                                       L1          │
│                                    ┌──────────────────────────────┐ │
│                                    │ Vence hoy                    │ │
│                                    └──────────────────────────────┘ │
├──────────────────────────────────────────────────────────────────────┤
│ 🟡 [reloj amber]  Comprensión lectora                 Lenguaje ·   │
│                                                       L2          │
│                                    ┌──────────────────────────────┐ │
│                                    │ 3 días rest.                 │ │
│                                    └──────────────────────────────┘ │
├──────────────────────────────────────────────────────────────────────┤
│ ⚪ [reloj gray]   Ecuaciones lineares                 Matemática · │
│                                                       L1          │
│                                    ┌──────────────────────────────┐ │
│                                    │ 12 días rest.                │ │
│                                    └──────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────┘
```

### Origen de datos

```php
$upcoming = Activity::with([
    'pevaluacion.pensum.asignatura',   // Nombre de la materia
    'pevaluacion.lapso',               // Lapso (L1, L2, L3)
])
    ->whereIn('id', $visibleActivityIds)  // Solo actividades visibles
    ->whereNotNull('ffinal')              // Tiene fecha de fin definida
    ->where('ffinal', '>=', now()->subDay()) // No haya expirado (margen 1 día)
    ->orderBy('ffinal', 'asc')            // Más urgente primero
    ->take(5)                             // Máximo 5
    ->get();
```

### Indicador de urgencia (3 niveles)

Calculado en el Blade con:

```php
$ffinal = \Carbon\Carbon::parse($activity->ffinal);
$daysLeft = now()->startOfDay()->diffInDays($ffinal->startOfDay(), false);
```

| `$daysLeft` | Color del badge | Texto | Color del ícono | Clases del badge |
|:-----------:|:---------------:|:------|:----------------:|:------------------|
| ≤ 0 | 🔴 Rojo | "Vence hoy" | `text-red-400` | `bg-red-500/10 text-red-400` |
| = 1 | 🔴 Rojo | "1 día restante" | `text-red-400` | `bg-red-500/10 text-red-400` |
| 2-3 | 🟡 Ámbar | "X días rest." | `text-amber-400` | `bg-amber-500/10 text-amber-400` |
| > 3 | ⚪ Gris | "X días rest." | `text-gray-400` | `bg-gray-100 dark:bg-gray-700/50 text-gray-500` |

### Estructura de cada fila

Cada elemento es un `<a>` que enlaza a `route('student.lms.activity', $activity)`.

```
┌──────────────────────────────────────────────────────────────────────┐
│ ┌─────────┐   título (truncated)                    ┌─────────────┐ │
│ │  reloj  │   materia · lapso                       │  badge      │ │
│ └─────────┘                                          └─────────────┘ │
└──────────────────────────────────────────────────────────────────────┘
```

Hover: el borde cambia a `hover:border-amber-500/30` para todas las cards, independientemente del nivel de urgencia.

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
<div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-5 space-y-5">
    <!-- cada asignatura -->
</div>
```

El mismo estilo de card que las stats, pero con `p-5` en lugar de `p-4`.

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
| `$upcoming` | ✅ | Filtrado por `$visibleActivityIds` |
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

### Query 4: Próximas fechas límite

```sql
SELECT a.*
FROM activities a
WHERE a.id IN (?, ?, ...)
  AND a.ffinal IS NOT NULL
  AND a.ffinal >= DATE_SUB(NOW(), INTERVAL 1 DAY)
ORDER BY a.ffinal ASC
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

## Historial de Cambios

| Fecha | Cambio | Autor |
|-------|--------|-------|
| 2026-07-30 | Creación inicial del documento | — |
