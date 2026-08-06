# Dashboard de Progreso del Estudiante

**Panel de Progreso Académico — `/app/estudiante/home`**
_Última revisión:_ 2026-08-05

---

## Tabla de Contenidos

1. [Visión General](#1-visión-general)
2. [Componente Livewire](#2-componente-livewire)
3. [Sección 0: Hero](#3-sección-0-hero)
4. [Sección 1: Stats Cards](#4-sección-1-stats-cards)
5. [Sección 2: Continuar Aprendiendo](#5-sección-2-continuar-aprendiendo)
6. [Sección 3: Próximas Publicaciones](#6-sección-3-próximas-publicaciones)
7. [Sección 4: Todas las Lecciones](#7-sección-4-todas-las-lecciones)
8. [Sección 5: Distribución por Asignatura](#8-sección-5-distribución-por-asignatura)
9. [Sección 6: Tu actividad reciente](#9-sección-6-tu-actividad-reciente)
10. [Empty State](#10-empty-state)
11. [Visibilidad y Scoping de Datos](#11-visibilidad-y-scoping-de-datos)
12. [Estructura de Routes](#12-estructura-de-routes)
13. [Referencia de Queries](#13-referencia-de-queries)
14. [Sistema de Tarjetas Unificado](#14-sistema-de-tarjetas-unificado)

---

## 1. Visión General

El Dashboard de Progreso es la página de inicio del área del estudiante (`/app/estudiante/home`). Muestra un resumen visual del avance académico del estudiante en las actividades LMS publicadas, combinando **7 secciones** de datos en una sola página.

### Propósito

- Dar al estudiante una vista rápida de **cuánto ha avanzado** en sus actividades (hero con donut de progreso)
- Saludarlo por nombre con **racha** de días consecutivos y ofrecerle la **siguiente lección** recomendada
- Mostrar **qué actividades visitó recientemente** para retomarlas
- Mostrar **próximas publicaciones** (lecciones que aún no se publican, con su fecha `publish_at`)
- Listar **todas las lecciones visibles** (publicadas y previews; catálogo completo) con **búsqueda en vivo, filtro por asignatura y paginación**
- Visualizar la **distribución de progreso por asignatura**
- Recapitular la **actividad reciente** del estudiante (comentarios y descargas)

### Arquitectura

```
StudentHome (Livewire full-page component)
├── routes/web.php → Route::prefix('app/estudiante')
│   └── GET /home → StudentHome::class → name: 'student.lms.home'
├── app/Livewire/Student/Lms/StudentHome.php
│   ├── mount() → initializeHasStudentScope()
│   ├── updatedSearch() / updatedSubjectFilter() → resetPage()
│   ├── resetFilters() → limpia búsqueda + filtro + página
│   └── render() → 14 view vars scoped
└── resources/views/livewire/student/lms/student-home.blade.php
    ├── 0. Hero (saludo, racha, siguiente lección, donut, countdown)
    ├── 1. Stats Cards (grid 4-col)
    ├── 2. Continuar Aprendiendo (o fallback "Publicaciones Recientes")
    ├── 3. Próximas Publicaciones (lecciones por publicarse, badges a publish_at)
    ├── 4. Todas las Lecciones (búsqueda + filtro + paginación + dots de estado)
    ├── 5. Distribución por Asignatura (barras de progreso)
    └── 6. Tu actividad reciente (comentarios + descargas)
```

La raíz del blade lleva `wire:poll.10s`, de modo que los datos del hero (especialmente el countdown de la siguiente lección) se refrescan sin recargar la página.

### Datos calculados en `render()` — no en `mount()`

Toda la carga de datos ocurre en el método `render()`, no en `mount()`. Esto es fundamental porque permite que Livewire reactive los datos en cada petición (subida de archivos, polling, eventos) sin recargar la página.

---

## 2. Componente Livewire

**Archivo:** `app/Livewire/Student/Lms/StudentHome.php`

### Propiedades y Traits

```php
class StudentHome extends Component
{
    use Concerns\HasStudentScope;
    use WireUiActions;
    use WithPagination;

    /** Búsqueda en vivo sobre el listado "Todas las Lecciones". */
    public string $search = '';

    /** Filtro por asignatura en el listado "Todas las Lecciones". */
    public string $subjectFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'subjectFilter' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->initializeHasStudentScope();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSubjectFilter(): void
    {
        $this->resetPage();
    }

    /** Limpia la búsqueda y el filtro de asignatura del listado global. */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->subjectFilter = '';
        $this->resetPage();
    }
}
```

- **`WireUiActions`**: Permite lanzar notificaciones con `$this->notification()->success(...)`.
- **`HasStudentScope`**: Trait propio que inicializa `StudentScopeService` para obtener los IDs de sección del estudiante autenticado.
- **`WithPagination`**: Habilita `->paginate(5)` en "Todas las Lecciones".
- **`$search` / `$subjectFilter`** + `$queryString`: la búsqueda y el filtro sobreviven recargas y quedan reflejados en la URL (`?search=...&subjectFilter=...`). Al cambiar cualquiera de los dos se vuelve a la página 1 (`resetPage()`).
- **`resetFilters()`**: botón "Limpiar filtros" del estado vacío; limpia ambos y vuelve a la página 1.

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
        ->where('status', true)
        ->whereHas('pevaluacion', fn ($q) => $q->whereIn('seccion_id', $seccionIds))
        ->pluck('id');

    // 0. Hero: firstName, racha, siguiente lección (publicada → fallback preview)
    // 1. Stats: total / completadas / comentarios / descargas + progress_pct
    // 2. Continuar Aprendiendo (+ fallback "Publicaciones Recientes")
    // 3. Próximas publicaciones
    // 4. Todas las lecciones (búsqueda + filtro + paginación) + asignaturas
    // 5. Distribución por asignatura
    // 6. Comentarios y descargas recientes

    return view('livewire.student.lms.student-home', [
        'greeting'           => $this->greetingForHour(now()->hour),
        'firstName'          => $firstName ?: 'estudiante',
        'streak'             => $streak,
        'nextLesson'         => $nextLesson,
        'stats'              => $stats,
        'recentLogs'         => $recentLogs,
        'suggestedActivities' => $suggestedActivities,
        'upcoming'           => $upcoming,
        'allLessons'         => $allLessons,
        'subjects'           => $subjects,
        'subjectDistribution' => $subjectDistribution,
        'recentComments'     => $recentComments,
        'recentDownloads'    => $recentDownloads,
        'downloadResources'  => $downloadResources,
    ])->layout('student.layouts.app');
}
```

**Layout:** `student.layouts.app` — navbar con 5 enlaces (Inicio, Perfil, Académica, Lecciones, Recursos), modo oscuro forzado, menú hamburguesa responsive.

---

## 3. Sección 0: Hero

> **Decisión de diseño (2026-08-05):** el panel ahora abre con un hero que personaliza la página por estudiante: saludo según la hora, nombre, racha de días consecutivos, un **donut de progreso** animado y un CTA a la **siguiente lección** con **countdown en vivo** cuando es una preview futura. Refresca vía `wire:poll.10s`.

### Vista

```
BUENOS TARDES
Ana
Tu avance en un vistazo. Sigue aprendiendo sin perder el ritmo.

🔥 3 días de racha                          ┌────────────────┐
                                            │      42%       │
[▶ Siguiente: Ecuaciones lineales]         │   completado   │
📅 Matemática · Comienza en 2h 14m 05s     │                │
                                            └────────────────┘
```

### Datos (calculados en `render()`)

| Dato | Cálculo |
|------|---------|
| `$greeting` | `greetingForHour($hour)`: 5–11h → "Buenos días"; 12–19h → "Buenas tardes"; resto → "Buenas noches" |
| `$firstName` | `explode(' ', trim(auth()->user()->full_name ?: ''))[0]`; si `full_name` vacío, `trim(auth()->user()->name ?: '')`; si ambos vacíos, `'estudiante'` |
| `$streak` | Fechas únicas (`toDateString`) de `LmsActivityLog` con evento `VIEW`/`COMPLETE`/`RESOURCE_DOWNLOAD`; cuenta hacia atrás desde hoy, y si hoy no hay actividad, desde ayer |
| `$nextLesson` | Query 0: lección publicada sin completar más reciente; si no hay, Query 0b: preview futura más próxima |
| `$stats['progress_pct']` | `round(completed / total * 100)` (mismo valor que la stat "Completadas") |

### Estructura del blade

- Raíz del panel: `<div ... wire:poll.10s>` — en cada poll Livewire re-renderiza las queries; el morphdom **no toca** los subárboles Alpine byte-idénticos, así el donut y el countdown conservan su estado entre polls.
- Contenedor `flex flex-col sm:flex-row sm:items-center gap-6`.
- **Izquierda:**
  - Label saludo: `text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400`.
  - `<h1 class="text-2xl font-bold">` con `$firstName`.
  - Subtítulo: *"Tu avance en un vistazo. Sigue aprendiendo sin perder el ritmo."* (`text-sm`).
  - Chip de racha (solo `@if($streak > 0)`): pill naranja con ícono de fuego — `text-orange-700 dark:text-orange-300 bg-orange-100 dark:bg-orange-500/10 border-orange-200 dark:border-orange-500/30`, texto `{{ $streak }} {{ $streak === 1 ? 'día' : 'días' }} de racha`.
  - CTA "Siguiente: {topic}": `bg-emerald-600 hover:bg-emerald-500 text-white`, topic truncado (`max-w-[16rem] sm:max-w-xs truncate`) + chevron que se desplaza `group-hover:translate-x-0.5`.
  - Sub-línea bajo el CTA: asignatura (ícono de calendario emerald) y, según el caso:
    - **Preview futura** → countdown vivo ámbar `x-text="label"`: Alpine `x-data="{ target: '<ISO8601>', label: '', timer: null, tick() {...}, init() {...} }"` que hace tick cada 1 s y escribe `"Comienza en {H}h {M}m {S}s"`; al llegar a 0 escribe `"Publicada ahora"` y detiene el intervalo.
    - **Publicada** → check emerald + `publish_at->diffForHumans()`.
  - **Sin `$nextLesson`** → mensaje: *"Aún no hay lecciones disponibles. Tus profesores publicarán contenido pronto."*
- **Derecha (donut):** contenedor `relative w-36 h-36` con Alpine:
  - `x-data="{ pct: 0, target: {{ $stats['progress_pct'] }} }"`.
  - `x-init`: count-up con `performance.now()` + `requestAnimationFrame`, `easeOutCubic` (`target * (1 - Math.pow(1 - k, 3))`), duración 1000 ms.
  - SVG `w-36 h-36 -rotate-90` viewBox `0 0 120 120`, r=52, stroke-width=10: círculo track (`stroke-gray-100 dark:stroke-gray-700/60`) + círculo progreso `stroke-emerald-500` con `stroke-dasharray="326.7"` (2π·52) y `stroke-dashoffset` enlazado a `pct` con transición CSS `1s cubic-bezier(0.22, 1, 0.36, 1)`. El viewBox escala proporcionalmente, así que el tamaño se controla solo con `w-*/h-*`.
  - Centro: `<span class="text-2xl font-bold tabular-nums" x-text="pct + '%'">` + label `text-xs uppercase tracking-wider` "completado".
- Todos los SVG decorativos del hero llevan `aria-hidden="true"`.

---

## 4. Sección 1: Stats Cards

### Vista

```
┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐ ┌─────────────────┐
│  📘 Lecciones   │ │  ✅ Completadas │ │  💬 Comentarios │ │  ⬇️ Descargas   │
│  12             │ │  5              │ │  3              │ │  7              │
│  Disponibles    │ │  42% del total  │ │  Que has dejado │ │  Recursos       │
│  para ti        │ │                 │ │                 │ │  descargados    │
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
- **Sin hover** — las cards de métricas no enlazan a nada; no llevan clases `hover:*` para no ofrecer affordance falsa (ver [Sistema de Tarjetas Unificado](#14-sistema-de-tarjetas-unificado))

Contenido interno:
1. **Ícono + etiqueta** en una fila: contenedor `w-8 h-8 rounded-lg` con color de fondo semitransparente (opacity 10%) e ícono SVG del mismo color; etiqueta a la derecha `text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500`.
2. **Número** — `text-2xl font-bold text-gray-900 dark:text-white`.
3. **Subtítulo** — `text-xs text-gray-500 dark:text-gray-400`.

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

| Card | Ícono | Color | Label | Microcopy | Data source |
|------|-------|-------|-------|-----------|-------------|
| **Lecciones** | Libro (sky) | `text-sky-400`, `bg-sky-500/10` | "Lecciones" | "Disponibles para ti" | `count($visibleActivityIds)` |
| **Completadas** | Check círculo (emerald) | `text-emerald-400`, `bg-emerald-500/10` | "Completadas" | `{{ $stats['progress_pct'] }}% del total` / "Sin actividades" | Distinct `activity_id` scoped al estudiante |
| **Comentarios** | Burbuja (amber) | `text-amber-400`, `bg-amber-500/10` | "Comentarios" | "Que has dejado" | Todos los comentarios del estudiante (sin scope de sección) |
| **Descargas** | Flecha abajo (purple) | `text-purple-400`, `bg-purple-500/10` | "Descargas" | "Recursos descargados" | Logs de descarga del estudiante (sin scope de sección) |

### Porcentaje

Se muestra un texto condicional:
- Si `$stats['total'] > 0`: "`{{ $stats['progress_pct'] }}% del total`"
- Si no hay actividades: "Sin actividades"

---

## 5. Sección 2: Continuar Aprendiendo

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
    'activity.lmsPublication',                   // Estado de publicación (chip "Vista previa")
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

Las filas pueden mostrar el chip ámbar **"Vista previa"** cuando `$act->lmsPublication?->isPreviewToStudents()`.

### Comportamiento de hover

```css
.group:hover .group-hover:text-emerald-600  /* título */
.group:hover .group-hover:text-emerald-500  /* flecha › */
.group:hover .hover:-translate-y-0.5        /* elevación sutil */
.group:hover .hover:shadow-md               /* sombra al elevar */
.group:hover .hover:border-emerald-500/40   /* borde de la card */
```

Transiciones: `transition-all duration-200` (elevación — ver [Sistema de Tarjetas Unificado](#14-sistema-de-tarjetas-unificado))

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

Mismo patrón de subquery que `$upcoming` (sección 6), pero con segundo argumento `'desc'` y filtro `publish_at <= now()`.

**Estructura de cada fila del fallback** (idéntica a las filas de logs, sin etiqueta de estado):

- Título de la sección en el fallback: **"Publicaciones Recientes"** (la rama con historial usa "Continuar Aprendiendo").
- Ícono **play emerald** (`bg-emerald-500/10`, `text-emerald-400`) — son lecciones para *empezar*, no completadas.
- Subtítulo de la sección: *"Actividades publicadas más recientes"*.
- Hint derecho: `$activity->lmsPublication?->publish_at?->diffForHumans()` → "hace 2 días".
- Sin etiqueta "Vista previa" (la query garantiza `publish_at <= now()`).
- Acentos 100% emerald (header, íconos, título, chevron) y hover de fila en emerald (`hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-500/40`) — ver [Sistema de Tarjetas Unificado](#14-sistema-de-tarjetas-unificado).

El blade usa `@if($recentLogs->isNotEmpty()) ... @elseif($suggestedActivities->isNotEmpty()) <section>...`.

---

## 6. Sección 3: Próximas Publicaciones

> **Decisión de diseño:** para el estudiante, `publish_at` es la fecha más relevante de la lección. `activity.ffinal` (fecha de cierre/corrección) generaba confusión, así que **desaparece de este panel**. La sección lista solo lecciones que aún **no** se han publicado (`publish_at` en el futuro), ordenadas por `publish_at` ascendente (la que se publica antes, primero). Las ya publicadas salen de la sección.

### Vista

```
⏰ Próximas Publicaciones

┌──────────────────────────────────────────────────────────────────────┐
│ 🟢 [reloj emerald]   Funciones cuadráticas            Matemática · L3 │
│                                   ┌──────────────────────────────┐ │
│                                   │ Se publica en 2 días         │ │
│                                   └──────────────────────────────┘ │
├──────────────────────────────────────────────────────────────────────┤
│ 🟢 [reloj emerald]   Ecuaciones                        Matemática · L3 │
│                                   ┌──────────────────────────────┐ │
│                                   │ Se publica el 9 ago          │ │
│                                   └──────────────────────────────┘ │
├──────────────────────────────────────────────────────────────────────┤
│ 🟢 [reloj emerald]   Trigonometría                      Matemática · L1 │
│                                   ┌──────────────────────────────┐ │
│                                   │ Se publica hoy a las 16:00   │ │
│                                   └──────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────┘
```

*(Todas las filas son lecciones en vista previa: `PUBLISHED` con `publish_at` futuro. Ya no hay urgencia roja/ámbar ni countdown en este panel — el countdown en vivo vive en el hero (Sección 0) para la siguiente lección.)*

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

### Badge de publicación (1 nivel, emerald)

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

- Badge e ícono siempre en emerald; el badge usa la forma de chip unificada (`rounded-full` + borde emerald — ver [Sistema de Tarjetas Unificado](#14-sistema-de-tarjetas-unificado)); ya no hay niveles de urgencia.
- Junto al título se muestra la etiqueta **"Vista previa"** (emerald), igual que en "Continuar Aprendiendo".
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

Hover: elevación sutil + borde `hover:border-emerald-500/40` (ver [Sistema de Tarjetas Unificado](#14-sistema-de-tarjetas-unificado)).

---

## 7. Sección 4: Todas las Lecciones

> **Decisión de diseño (2026-08-05):** el listado pasó a ser un **catálogo con herramienta de exploración**: búsqueda en vivo, filtro por asignatura y **paginación de 5 por página**. Muestra **todas** las lecciones visibles (`visibleNow()`: publicadas y previews) de la más reciente a la más antigua según `publish_at`, con un **punto de estado** (emerald = publicada, ámbar = vista previa) y una **leyenda**. Sigue siendo un listado compacto y **sutil** (no tarjetas): filas `divide-y` con hover solo de color.

### Vista

```
📚 Todas las Lecciones (N)
Tu catálogo completo, de la más reciente a la más antigua

[ 🔍 Buscar lección…          ] [ Asignatura ▾ ]

● Publicada   ● Vista previa   (leyenda)

● Ecuaciones lineales                    Matemática ·  9 ago 2026
● Análisis sintáctico        [Vista previa]  Lenguaje ·  4 ago 2026
● Trigonometría                                      · 28 jul 2026

           ‹ 1 2 3 ›   (paginación)
```

### Guard de la sección

```blade
@if($allLessons->total() > 0 || $this->search !== '' || $this->subjectFilter !== '')
```

La sección se dibuja si hay lecciones **o** si hay filtros activos (así el estado vacío siempre es visible mientras se busca).

### Origen de datos

```php
$allLessons = Activity::with([
    'pevaluacion.pensum.asignatura',   // Nombre de la materia
    'pevaluacion.lapso',               // Lapso (L1, L2, L3)
    'lmsPublication',                  // Datos de publicación (publish_at)
])
    ->whereIn('id', $visibleActivityIds)   // Solo actividades visibles
    ->when($this->search !== '', fn ($q) => $q->where('topic', 'like', '%'.$this->search.'%'))
    ->when($this->subjectFilter !== '', fn ($q) => $q->whereHas(
        'pevaluacion.pensum.asignatura',
        fn ($q2) => $q2->where('name', $this->subjectFilter)
    ))
    ->orderBy(                             // Más reciente primero (publish_at)
        LmsActivityPublication::select('publish_at')
            ->whereColumn('lms_activity_publications.activity_id', 'activities.id')
            ->orderByDesc('publish_at')
            ->limit(1),
        'desc'
    )
    ->paginate(5)                          // 5 por página
    ->withQueryString();                   // conserva search/subjectFilter en la URL
```

- Se ejecuta **siempre** (a diferencia del fallback de la sección 5, que solo corre sin historial).
- **`paginate(5)` + `withQueryString()`**: la paginación viaja con los filtros activos en la URL (`?page=2&search=...`).
- **Sin filtro de `publish_at`**: incluye publicadas y previews. El fallback (sección 5) sí filtra `publish_at <= now()`; "Próximas" (sección 6) filtra `publish_at > now()`; este listado solapa a ambas.
- Las previews (`publish_at` futuro) se ordenan **primero** (publish_at más alto) y además viven en "Próximas Publicaciones".
- Los cambios de filtro vuelven a la página 1 (`updatedSearch()` / `updatedSubjectFilter()` → `resetPage()`).

### Asignaturas para el filtro

```php
$subjects = Activity::with('pevaluacion.pensum.asignatura')
    ->whereIn('id', $visibleActivityIds)
    ->get()
    ->map(fn ($a) => $a->pevaluacion?->pensum?->asignatura?->name)
    ->filter(fn ($name) => filled($name))
    ->unique()
    ->sort()
    ->values();
```

### Controles de búsqueda y filtro

- **Input de búsqueda**: `wire:model.live.debounce.300ms="search"`, placeholder "Buscar lección…", con lupa decorativa a la izquierda y **botón de limpiar** (`wire:click="$set('search', '')"`) que aparece solo `@if($this->search !== '')`. Focus ring emerald: `focus:ring-emerald-500/30 focus:border-emerald-500`.
- **Select de asignatura**: `wire:model.live="subjectFilter"` (`sm:w-52`), primera opción "Todas las asignaturas" + una opción por `$subjects`.

### Leyenda de estado

```blade
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Publicada
<span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Vista previa
```

### Estructura de cada fila

Cada elemento es un `<a>` que enlaza a `route('student.lms.activity', $activity)`.

```
┌──────────────────────────────────────────────────────────────────────┐
│ ● título (truncated)   [Vista previa]  asignatura (hidden md)  fecha │
└──────────────────────────────────────────────────────────────────────┘
```

- Lista `<ul class="divide-y divide-gray-100 dark:divide-gray-800">` — separadores sutiles, **sin** tarjetas ni elevación.
- **Punto de estado** `w-1.5 h-1.5 rounded-full` + `@class(['bg-emerald-500' => !$isPreview, 'bg-amber-400' => $isPreview])` con `$isPreview = $activity->lmsPublication?->isPreviewToStudents()`.
- Título `text-sm font-medium` con hover solo de color: `group-hover:text-emerald-600 dark:group-hover:text-emerald-400`.
- Chip ámbar "Vista previa" solo `@if($isPreview)`.
- Asignatura `hidden md:inline text-xs text-gray-400 dark:text-gray-500` — no ocupa espacio en mobile.
- Fecha absoluta `translatedFormat('j M Y')` alineada a la derecha, `text-xs` fija.
- Header "Todas las Lecciones" con ícono de libro emerald (igual que el panel) + contador `({{ $allLessons->total() }})` — total global, no por página.

### Paginación

```blade
<div class="mt-4">
    {{ $allLessons->links() }}
</div>
```

### Estado vacío (búsqueda/filtro sin resultados)

Contenedor `text-center py-10 rounded-xl border border-dashed` con mensaje según los filtros activos:

| Filtros | Mensaje |
|:--------|:--------|
| search + subjectFilter | `No se encontraron lecciones para "{search}" en {subject}.` |
| solo search | `No se encontraron lecciones para "{search}".` |
| solo subjectFilter | `No se encontraron lecciones en {subject}.` |

Boton **"Limpiar filtros"** (`wire:click="resetFilters"`) en emerald, que vacía `search`, `subjectFilter` y vuelve a la página 1.

---

## 8. Sección 5: Distribución por Asignatura

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
<div class="bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 space-y-5 shadow-sm">
    <!-- cada asignatura -->
</div>
```

Misma base que las stats (`p-4 shadow-sm`), **sin hover** — no enlaza a nada (ver [Sistema de Tarjetas Unificado](#14-sistema-de-tarjetas-unificado)). Se conserva `space-y-5` para el aire interno entre barras.

---

## 9. Sección 6: Tu actividad reciente

> **Decisión de diseño (2026-08-05):** cierra el panel con una recapitulación de la **actividad propia** del estudiante: últimos 3 comentarios aprobados que dejó y últimas 3 descargas de recursos. Le da contexto de "lo que he hecho", con enlaces a cada actividad.

### Vista

```
⚡ Tu actividad reciente

┌─────────────────────────────────────┐  ┌─────────────────────────────────────┐
│ COMENTARIOS RECIENTES               │  │ DESCARGAS RECIENTES                 │
│ ─────────────────────────────────── │  │ ─────────────────────────────────── │
│ "Muy clara la explicación, gracias" │  │ Guía de ejercicios.pdf               │
│  Ecuaciones lineales · Matemática   │  │  Matemática · hace 1 día            │
│                                     │  │                                     │
│ "¿Pueden subir más ejemplos?"       │  │ Teoría de conjuntos.pdf              │
│  Análisis sintáctico · Lenguaje     │  │  Lenguaje · hace 3 días             │
└─────────────────────────────────────┘  └─────────────────────────────────────┘
```

### Origen de datos

```php
$recentComments = ActivityComment::with(['activity.pevaluacion.pensum.asignatura'])
    ->where('user_id', auth()->id())
    ->approved()                       // is_approved = true AND rejected_at IS NULL
    ->latest('created_at')
    ->take(3)
    ->get();

$recentDownloads = LmsActivityLog::with(['activity.pevaluacion.pensum.asignatura'])
    ->where('user_id', auth()->id())
    ->where('event', 'RESOURCE_DOWNLOAD')
    ->orderBy('created_at', 'desc')
    ->take(3)
    ->get();

$downloadResourceIds = $recentDownloads->pluck('context_id')->filter();
$downloadResources = $downloadResourceIds->isNotEmpty()
    ? LmsActivityResource::whereIn('id', $downloadResourceIds)->pluck('display_name', 'id')
    : collect();
```

### Estructura del blade

- Guard: `@if($recentComments->isNotEmpty() || $recentDownloads->isNotEmpty())`.
- Header con ícono de rayo emerald + título "Tu actividad reciente".
- `grid grid-cols-1 md:grid-cols-2 gap-3` con dos cards (cada una se dibuja `@if` su colección no está vacía):
  - **Comentarios recientes**: label `text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500`; cada item enlaza a la actividad y muestra `$comment->body` con `line-clamp-2` + `group-hover:text-emerald-600` y la línea `topic · asignatura`.
  - **Descargas recientes**: cada item muestra `{{ $downloadResources[$log->context_id] ?? $dAct->topic }}` (el `display_name` del recurso, o el topic como fallback) + `asignatura · diffForHumans()`.
- Cards con la base unificada (`rounded-xl`, borde, `p-4`, `shadow-sm`), **sin hover** de card; el hover de color vive en los títulos (`group-hover`).

---

## 10. Empty State

Ya **no existe un empty state global** al final del panel. El manejo de "sin datos" es **por sección**:

| Caso | Comportamiento |
|------|----------------|
| Sin lecciones visibles en absoluto | El hero muestra *"Aún no hay lecciones disponibles. Tus profesores publicarán contenido pronto."* en lugar del CTA de siguiente lección |
| Búsqueda/filtro sin resultados en "Todas las Lecciones" | Estado vacío con borde dashed + mensaje contextual + botón "Limpiar filtros" (ver [Sección 4](#7-sección-4-todas-las-lecciones)) |
| Sin historial en "Continuar Aprendiendo" | Fallback "Publicaciones Recientes" (ver [Sección 2](#5-sección-2-continuar-aprendiendo)) |
| Sin próximas publicaciones | La sección se oculta (`@if($upcoming->isNotEmpty())`) |
| Sin distribución | La sección se oculta (`@if($subjectDistribution->isNotEmpty())`) |
| Sin comentarios ni descargas | La sección se oculta (`@if($recentComments->isNotEmpty() || $recentDownloads->isNotEmpty())`) |

---

## 11. Visibilidad y Scoping de Datos

### Cadena de visibilidad completa

```
LmsActivityPublication.status === 'PUBLISHED'
    AND publish_at IS NOT NULL
    AND (unpublish_at IS NULL OR unpublish_at >= now())   // scopeVisibleNow()
        ↓
Activity.status === true
        ↓
Pevaluacion.seccion_id IN (seccionIds del estudiante)
        ↓
Resultado: visibleActivityIds (collection de IDs)
```

> **Importante:** `scopeVisibleNow()` **no** filtra `publish_at <= now()`. Exige solo `publish_at IS NOT NULL`, por lo que las **previews** (`PUBLISHED` con `publish_at` futuro) pasan la cadena y son visibles. El filtro temporal (`publish_at <= now()` vs `publish_at > now()`) se aplica **por sección** según lo que cada una muestra.

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
| `$firstName` / `$greeting` / `$streak` | ❌ | Datos del usuario y de su historial global (racha cuenta todos los eventos, no solo visibles) |
| `$nextLesson` | ✅ | Filtrado por `$visibleActivityIds`; publicada sin completar más reciente, o preview futura más próxima |
| `$completedIds` | ✅ | Filtrado por `$visibleActivityIds` |
| `$commentsCount` | ❌ | Cuenta total de comentarios del estudiante |
| `$downloadsCount` | ❌ | Cuenta total de descargas del estudiante |
| `$recentLogs` | ✅ | Filtrado por `$visibleActivityIds` |
| `$suggestedActivities` | ✅ | Filtrado por `$visibleActivityIds` + `publish_at <= now()`, ORDER BY subquery DESC (solo si `$recentLogs` está vacío) |
| `$upcoming` | ✅ | Filtrado por `$visibleActivityIds` + `publish_at > now()` |
| `$allLessons` | ✅ | Filtrado por `$visibleActivityIds` (sin filtro de `publish_at`) + `topic LIKE` (si `$search`) + asignatura (si `$subjectFilter`), ORDER BY subquery DESC, **paginación de 5** |
| `$subjects` | ✅ | Asignaturas distintas sobre `$visibleActivityIds` (para el filtro) |
| `$subjectDistribution` | ✅ | Calculado sobre `$visibleActivityIds` |
| `$recentComments` | ❌ | Comentarios aprobados del estudiante, sin scope de sección |
| `$recentDownloads` | ❌ | Descargas del estudiante, sin scope de sección |
| `$downloadResources` | ❌ | `display_name` de los recursos referenciados por `$recentDownloads` |

**Nota:** Comments y Downloads son contadores globales del estudiante, no scoped a las actividades visibles. Esto es intencional — muestran la actividad total del estudiante en el sistema, no solo lo que está actualmente publicado.

---

## 12. Estructura de Routes

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

## 13. Referencia de Queries

### Query 0: Hero — siguiente lección (publicada)

```sql
SELECT a.*
FROM activities a
WHERE a.id IN (?, ?, ...)              -- visibleActivityIds
  AND NOT EXISTS (
      SELECT 1 FROM lms_activity_logs l
      WHERE l.activity_id = a.id
        AND l.user_id = ?
        AND l.event = 'COMPLETE'
  )
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
LIMIT 1;
```

### Query 0b: Hero — fallback (preview futura más próxima)

Si Query 0 devuelve `null` (todas las lecciones publicadas están completas), se intenta la preview más próxima:

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
    ORDER BY p.publish_at ASC      -- la que se publica primero
    LIMIT 1
) ASC
LIMIT 1;
```

### Query 0c: Racha de días consecutivos

```sql
SELECT DISTINCT DATE(created_at) AS d
FROM lms_activity_logs
WHERE user_id = ?
  AND event IN ('VIEW', 'COMPLETE', 'RESOURCE_DOWNLOAD');
```

Post-procesamiento PHP: set de fechas (`toDateString` → `unique`); si hoy no está presente, el cursor parte de ayer; cuenta días consecutivos hacia atrás.

### Query 1: Actividades visibles (combinada)

```sql
SELECT a.id
FROM activities a
INNER JOIN pevaluacions p ON a.pevaluacion_id = p.id
WHERE a.id IN (
    SELECT activity_id
    FROM lms_activity_publications
    WHERE status = 'PUBLISHED'
      AND publish_at IS NOT NULL
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

### Query 3c: Listado "Todas las Lecciones" (búsqueda + filtro + paginación)

```sql
SELECT a.*
FROM activities a
WHERE a.id IN (?, ?, ...)                     -- visibleActivityIds
  [AND a.topic LIKE '%:busqueda%']            -- solo si search !== ''
  [AND EXISTS (                                -- solo si subjectFilter !== ''
      SELECT 1
      FROM pevaluacions pv
      INNER JOIN pensums pen ON pv.pensum_id = pen.id
      INNER JOIN asignaturas asig ON pen.asignatura_id = asig.id
      WHERE pv.id = a.pevaluacion_id
        AND asig.name = :asignatura
  )]
ORDER BY (
    SELECT p.publish_at FROM lms_activity_publications p
    WHERE p.activity_id = a.id
    ORDER BY p.publish_at DESC
    LIMIT 1
) DESC
LIMIT 5 OFFSET ?;   -- paginate(5) + withQueryString() (page en la URL)
```

Como Query 3b pero **sin filtro de `publish_at`** y con `LIMIT/OFFSET`: se ejecuta siempre (con o sin historial), lista el catálogo completo de lecciones visibles (publicadas y previews) filtrado por texto y/o asignatura, 5 por página. Solapa parcialmente con Query 4: las previews (`publish_at > now()`) aparecen en ambos listados.

### Query 3d: Asignaturas para el filtro

```sql
SELECT DISTINCT asig.name
FROM activities a
INNER JOIN pevaluacions pv ON a.pevaluacion_id = pv.id
INNER JOIN pensums pen ON pv.pensum_id = pen.id
INNER JOIN asignaturas asig ON pen.asignatura_id = asig.id
WHERE a.id IN (?, ?, ...);
```

Post-procesamiento PHP: `map(name)` → `filter(filled)` → `unique` → `sort` → `values`.

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

### Query 6: Comentarios recientes

```sql
SELECT * FROM activity_comments
WHERE user_id = ?
  AND is_approved = 1
  AND rejected_at IS NULL        -- scope approved()
ORDER BY created_at DESC
LIMIT 3;
```

Post-procesamiento: `$comment->body` en el blade (`line-clamp-2`), enlaza a `$comment->activity`.

### Query 7: Descargas recientes

```sql
SELECT * FROM lms_activity_logs
WHERE user_id = ?
  AND event = 'RESOURCE_DOWNLOAD'
ORDER BY created_at DESC
LIMIT 3;
```

Post-procesamiento:

```php
$downloadResources = LmsActivityResource::whereIn('id', $recentDownloads->pluck('context_id')->filter())
    ->pluck('display_name', 'id');
// Blade: {{ $downloadResources[$log->context_id] ?? $dAct->topic }}
```

---

## 14. Sistema de Tarjetas Unificado

> **Decisión de diseño:** las familias de tarjetas del panel (métricas, filas de cursos, panel de asignaturas, mini-listas de actividad) comparten una misma base visual para leerse como un solo sistema. La diferenciación se logra por **contenido y acento**, no rompiendo el lenguaje visual. El **acento emerald es ahora el color de todos los acentos de sección** (headers, íconos, hovers, chips), alineado con el navbar. Implementado el 2026-08-05.

### Token base (todas las tarjetas)

```
bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl p-4 shadow-sm
```

- Radio `rounded-xl` y borde gris (`gray-200` / `gray-700`) idénticos en todas las familias.
- `shadow-sm` uniforme — profundidad sutil y moderna, sin sombras agresivas.
- Padding `p-4` en todas (el panel de asignaturas pasó de `p-5` a `p-4`).

### Hover por tipo

| Tipo | Token | Dónde |
|------|-------|-------|
| **Estáticas** (métricas ×4, panel de asignaturas, mini-listas de actividad) | *(sin clases `hover:*`)* | No son clicables y no deben ofrecer affordance falsa. Sin brillo, sin elevación: responden solo al contenido |
| **Clicables** (filas Continuar/Recientes/Próximas) | `transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md hover:border-emerald-500/40` | Elevación sutil + sombra + borde acento. `emerald` en las tres familias de filas |

Los títulos y chevrons de las filas llevan `group-hover` con su acento (`group-hover:text-emerald-600`/`text-emerald-400`; chevron `group-hover:text-emerald-500`).

### Chip unificado (etiquetas y badges)

Un solo lenguaje: `rounded-full` + borde + `bg-{accent}-100 dark:bg-{accent}-500/10 border-{accent}-300 dark:border-{accent}-500/30`:

| Chip | Token |
|------|-------|
| "Vista previa" (amber en Continuar y listado; emerald en Próximas) | `shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider text-{accent}-700 dark:text-{accent}-300 bg-{accent}-100 dark:bg-{accent}-500/10 border border-{accent}-300 dark:border-{accent}-500/30` |
| Badge de publicación (emerald) | `shrink-0 text-xs font-medium whitespace-nowrap px-2.5 py-1 rounded-full border bg-emerald-100 dark:bg-emerald-500/10 border-emerald-300 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-300` |
| Racha (naranja, hero) | pill con ícono de fuego, `text-orange-700 dark:text-orange-300 bg-orange-100 dark:bg-orange-500/10 border-orange-200 dark:border-orange-500/30` |

Misma forma (pill + borde); la diferencia es de contenido: el badge lleva texto dinámico ("Se publica hoy a las H:i") que no puede ser uppercase de 9px.

### Paleta de acentos

| Acento | Rol | Uso |
|--------|-----|-----|
| **emerald** | avance / éxito / acento principal | **todas** las cabeceras de sección, íconos de sección, hovers de fila, CTA del hero, donut, barras de progreso, badge de publicación, chip "Vista previa" en Próximas, punto "Publicada", stat Completadas |
| **sky** | reproducción | íconos VIEW en Continuar Aprendiendo y stat Lecciones |
| **amber** | pendiente / aviso | stat Comentarios, chip "Vista previa" en Continuar y en el listado, punto "Vista previa" |
| **purple** | recursos | stat Descargas |
| **orange** | racha (hero) | chip "N días de racha" |

> **Por qué hover diferenciado:** las métricas, el panel de asignaturas y las mini-listas no enlazan a nada; elevarlas implicaría una affordance falsa (sugerir que son clicables). Por eso **no llevan ninguna clase hover**. Las filas sí enlazan a una actividad y se elevan con emerald, señalando interactividad.

> **Excepción deliberada:** el listado "Todas las Lecciones" (Sección 4) **no** es parte del sistema de tarjetas. A propósito no lleva caja ni elevación: es una lista compacta (`divide-y`, puntos de estado emerald/ámbar, hover solo de color) para que el catálogo completo no compita visualmente con las tarjetas de acción de las secciones 5 y 6.

---

## Historial de Cambios

| Fecha | Cambio | Autor |
|-------|--------|-------|
| 2026-08-05 | **Rediseño premium (hero + búsqueda + polish):** Sección 0 **Hero** con saludo por hora, `firstName`, chip de **racha**, **donut de progreso** animado (Alpine count-up `easeOutCubic`, SVG emerald) y CTA a la **siguiente lección** con **countdown en vivo** "Comienza en Hh Mm Ss" para previews futuras (Alpine tick 1 s + `wire:poll.10s`); "Todas las Lecciones" con **búsqueda en vivo** (`debounce.300ms`), **filtro por asignatura**, **paginación de 5** (`WithPagination` + `withQueryString`), **dots de estado** emerald/ámbar + leyenda y estado vacío con "Limpiar filtros"; nueva Sección 6 **"Tu actividad reciente"** (comentarios y descargas, 3 c/u); polish: tokens `text-xs`/`text-2xl`, sin hovers falsos en stats/distribución, acento **emerald unificado** en todos los acentos de sección (sustituye sky en Recientes y Próximas), `aria-hidden` en SVGs decorativos, microcopy ("Lecciones/Disponibles para ti", etc.); docs y tests actualizados | — |
| 2026-08-05 | **Sección 4 "Todas las Lecciones"** ahora lista **todas** las lecciones visibles (publicadas y previews): se omite la condición `publish_at <= now()`; la query `$publishedLessons` pasa a `$allLessons` (patrón subquery DESC, sin tope, sin filtro de fecha); las previews se ordenan primero y además viven en "Próximas Publicaciones"; subtítulo a "De la más reciente a la más antigua"; docs (Query 1/3c, scope) y tests actualizados | — |
| 2026-08-05 | Nueva **Sección 4 "Todas las Lecciones"**: listado compacto y sutil (filas `divide-y`, punto sky, título truncado, asignatura oculta en mobile, fecha absoluta a la derecha) con **todas** las lecciones publicadas (`publish_at <= now()`), ordenadas DESC por `publish_at`, sin tope; nueva query `$publishedLessons` (patrón subquery de 3b sin `LIMIT 5`, se ejecuta siempre); Distribución pasa a Sección 5; docs y tests actualizados (`StudentHomeTest`) | — |
| 2026-08-05 | **Sistema de tarjetas unificado** en todo el panel: base común `rounded-xl` + `border-gray-200/700` + `p-4` + `shadow-sm`; hover por tipo (estáticas = sin hover / sin lift; clicables = `-translate-y-0.5` + `shadow-md` + borde acento); fallback "Publicaciones Recientes" corregido a 100% emerald; chips unificados `rounded-full` + borde (Vista previa y badge de publicación); panel de asignaturas de `p-5` a `p-4` | — |
| 2026-08-05 | El fallback de "Continuar Aprendiendo" pasa a titularse **"Publicaciones Recientes"** (la rama con historial conserva "Continuar Aprendiendo"); docs y tests actualizados | — |
| 2026-08-05 | "Continuar Aprendiendo" gana fallback: sin historial de interacción (`LmsActivityLog` vacío) muestra las lecciones ya publicadas (`publish_at <= now()`), reciente primero (`publish_at` DESC), máx. 5; nueva query `$suggestedActivities`, fila play emerald + hint "hace X", subtítulo "Actividades publicadas más recientes" | — |
| 2026-08-05 | Sección 3 pasa a "Próximas Publicaciones": solo lecciones con `publish_at` futuro, ordenadas por `publish_at`; `ffinal` deja de usarse en el panel; badges a `publish_at` ("Se publica en X días" / "Se publica mañana" / "Se publica hoy a las H:i" / "Se publica el {j M}") | — |
| 2026-07-30 | Creación inicial del documento | — |
