# Blueprint: Dashboard del Profesor (Home)

> **Módulo:** Profesor > Home (Dashboard principal)
> **Controller:** `app/Http/Controllers/Profesor/HomeController.php` (165 líneas)
> **Livewire:** ❌ Ninguno — patrón Blade tradicional
> **Vistas:** `resources/views/profesors/home/` (14+ archivos, ~1,000+ líneas totales entre layouts, partials, modales)
> **Modelo Profesor:** `app/Models/app/Pescolar/Profesor.php` (687 líneas) + 4 traits (858 líneas adicionales)
> **Chart.js:** Dashboard interactivo con gráficos de rendimiento
> **Prioridad:** P0 — es la landing page del profesor

---

## 0. Resumen Ejecutivo

El **Dashboard del Profesor** es la página de inicio del módulo profesor (`/app/profesors/home`). A diferencia de los demás módulos documentados, **no usa Livewire** — es un controller tradicional que computa indicadores y pasa datos a la vista Blade vía `compact()`.

**Arquitectura:** `HomeController@home()` → computa 5 indicadores por lapso académico (período) → los pasa a la vista → la vista renderiza tabs con cards de indicadores + gráfico Chart.js + modal condicional para profesores guías.

**Diferencia fundamental con los demás blueprints:** Este no es un CRUD. No hay formularios, no hay persistencia desde este módulo. Es un **panel de analítica educativa** que agrega datos de todos los submódulos (evaluaciones, boletins, planes de evaluación) y los presenta en indicadores visuales.

**Hallazgos críticos:** (1) La lógica de cómputo de indicadores está en el controller y en traits del modelo Profesor — sin capa de servicio. (2) IRE (Índice de Rendimiento en Evaluación) es una métrica relativa que compara al profesor contra el promedio de sus pares. (3) Hay ~4 vistas legacy no usadas (labels, graphics, evaluativos, pvalaucion copy). (4) El modal de notificación de diagnóstico usa `localStorage` para "no mostrar más".

---

## 1. Validación contra Código Fuente

### 1.1 Route

**Archivo:** `routes/web.php` (línea 222)

```php
// Grupo: prefix 'app', middleware ['auth'], namespace 'Profesor'
// Subgrupo: prefix 'profesors', middleware ['is_profesor']

Route::get('/home', 'HomeController@home')->name('profesors.home');
Route::get('/users', 'HomeController@users')->name('profesors.users.index');
```

**URL completa:** `GET /app/profesors/home`
**Middleware:** `auth` + `is_profesor`

Sub-routes cargadas desde `routes/app/profesors.php` (19 archivos de rutas de submódulos).

### 1.2 Controller

**Archivo:** `app/Http/Controllers/Profesor/HomeController.php` (165 líneas)

```php
class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'is_profesor']);
    }

    public function users()
    {
        // → profesors.users.index (gestión de perfil)
    }

    public function home()
    {
        $profesor = Profesor::where('user_id', Auth::user()->id)->first();

        // 1. Verificar si es profesor guía (tutor)
        $esProfesorGuia = $profesor->seccion_guias?->count() > 0;
        $seccionesGuia  = $profesor->seccion_guias ?? collect();

        // 2. Verificar reportes de diagnóstico
        $tieneReportesDiagnosticos = SectionDiagnosticReport::whereIn('section_id', $seccionIds)->exists();
        $ultimoReporte = SectionDiagnosticReport::whereIn(...)->with(['section.grado', 'diagMain'])->latest()->first();

        // 3. Obtener lapso activo (con fallback a ID 2)
        $lapso = Lapso::getCurrentOrFirst() ?? Lapso::find(2);
        $lapsos = Lapso::all();

        // 4. Determinar si mostrar modal de notificación
        $mostrarModalNotificacion = $esProfesorGuia && $tieneReportesDiagnosticos
            && Carbon::now()->between($lapsoModal->finicial, $lapsoModal->ffinal);

        // 5. Computar indicadores por cada lapso
        foreach ($lapsos as $lapsoItem) {
            $pevaluacions       = Pevaluacion::where('profesor_id', $p->id)->where('lapso_id', $l->id)->get();
            $count_pevaluacions = $pevaluacions->count();
            $count_evaluacions  = (new Pevaluacion)->count_evaluacion_prof_lapso($p->id, $l->id);
            $goal_notas         = $profesor->goal_notas_load($l->id);
            $real_notas         = $profesor->real_notas_load($l->id);
            $porc_notas_load    = ($goal_notas > 0) ? min(round(100 * $real_notas / $goal_notas, 1), 100) : 0;
            $promedio           = $profesor->getPromedio($l->id, 2);
            $porc_aprobados     = $profesor->getPorcAprobados($l->id, 1);

            $indicadores->push(collect([...]));
        }

        return view('profesors.home', compact(
            'profesor', 'indicadores', 'lapsos', 'lapso_active', 'lapso',
            'esProfesorGuia', 'seccionesGuia', 'tieneReportesDiagnosticos',
            'ultimoReporte', 'mostrarModalNotificacion'
        ));
    }
}
```

### 1.3 Variables Computadas (5 indicadores × N lapsos)

| Indicador | Método | Fuente | Fórmula |
|-----------|--------|--------|---------|
| `count_pevaluacions` | Query directa | `Pevaluacion::where(profesor_id, lapso_id)->count()` | Conteo simple |
| `count_evaluacions` | `Pevaluacion::count_evaluacion_prof_lapso()` | Modelo Pevaluacion | Conteo de evaluaciones registradas |
| `porc_notas_load` | Profesor trait | `Indicators::goal/real_notas_load()` | `min(100, real/goal × 100)` con redondeo y ajuste fino |
| `promedio` | Profesor trait | `Indicators::getPromedio()` | `SUM(boletins.nota) / COUNT(boletins.id)` |
| `porc_aprobados` | Profesor trait | `Indicators::getPorcAprobados()` | `aprobados / total_boletins × 100` |
| IRE | Profesor trait | `Indicators::getProfesorIRE()` | `boletins_count / IEE_promedio_pares` |

### 1.4 Variables de Estado (4)

| Variable | Tipo | Propósito |
|----------|------|-----------|
| `$esProfesorGuia` | bool | Si el profesor es tutor de sección(es) |
| `$seccionesGuia` | Collection | Secciones donde es tutor |
| `$tieneReportesDiagnosticos` | bool | Si existen reportes de diagnóstico |
| `$ultimoReporte` | Model|null | Último reporte generado |
| `$mostrarModalNotificacion` | bool | Si mostrar modal al cargar dashboard |

### 1.5 Vistas — Árbol Completo

```
profesors.home (58 líneas)
├── Extiende: profesors.layouts.dashboard.app
│   ├── Extiende: profesors.layouts.app (base HTML shell)
│   ├── @section('main')
│   │   ├── profesors.elements.messeges.oper_ok (flash messages)
│   │   ├── profesors.home.partials.indicadores (26 líneas)
│   │   │   ├── profesors.home.partials.boxes.pevaluacion (101 líneas) ← CORE
│   │   │   │   ├── Nav tabs → uno por lapso (Bootstrap nav-tabs)
│   │   │   │   ├── Por cada lapso:
│   │   │   │   │   ├── 5 indicator boxes (via @component 'boxes.chart')
│   │   │   │   │   │   ├── Planes de Evaluación Asignados
│   │   │   │   │   │   ├── Número de Evaluaciones Registradas
│   │   │   │   │   │   ├── Porcentaje de Notas Registradas
│   │   │   │   │   │   ├── Promedio General
│   │   │   │   │   │   └── Índice de Aprobados
│   │   │   │   │   ├── IRE (Índice de Rendimiento en Evaluación)
│   │   │   │   │   └── Explicación del IRE + fórmula
│   │   │   └── profesors.evaluacions.chart.actividades (gráfico Chart.js)
│   │   ├── [condicional] profesors.partials.modal-notificacion-diag (210 líneas)
│   │   │   ├── Modal Bootstrap con backdrop estático
│   │   │   ├── Último reporte (fecha, precisión, sección)
│   │   │   ├── Lista de secciones con reportes disponibles
│   │   │   ├── Beneficios del informe
│   │   │   └── Script con localStorage (noMostrarNotificacionDiagnostico)
│   │   └── [condicional] profesors.partials.card-profesor-guia (41 líneas)
│   │       ├── Badge por sección con conteo de reportes
│   │       └── Link a profesors.profesor_guias.index
│   └── @section('scripts')
│       ├── Chart.bundle.js
│       ├── ChartFunction.js (funciones para generar charts)
│       ├── ChartEvent.js (eventos click para charts por rango)
│       └── SweetAlert2 CDN
```

### 1.6 Legacy/Unused Partials (4+ archivos)

| Archivo | Líneas | Estado |
|---------|--------|--------|
| `home/partials/labels.blade.php` | 133 | No incluido desde home.blade.php |
| `home/partials/labels/bill_grado.blade.php` | — | Sub-partial de labels |
| `home/partials/labels/bill_state.blade.php` | — | Sub-partial de labels |
| `home/partials/labels/estudiantil.blade.php` | — | Sub-partial de labels |
| `home/partials/labels/pay_goals.blade.php` | — | Sub-partial de labels |
| `home/partials/graphics.blade.php` | 58 | Comentado/no incluido |
| `home/partials/boxes/evaluativos.blade.php` | 37 | No incluido |
| `home/partials/boxes/pvalaucion.blade copy.php` | 57 | Archivo `.copy` — backup manual |
| `home/partials/helps.blade.php` | 26 | Comentado en home.blade.php |
| `profesors.modals.home.main.blade.php` | — | Comentado en home.blade.php |

---

## 2. Reglas de Negocio

### 2.1 Lógica de Indicadores

**Porcentaje de Notas Registradas** (`porc_notas_load`):
```php
$porc_notas_load = ($goal_notas > 0) ? 100 * ($real_notas / $goal_notas) : 0;
if ($porc_notas_load > 99.85) { $porc_notas_load = 100.00; }
$porc_notas_load = round($porc_notas_load, 1);
if ($goal_notas - $real_notas > 0 && $goal_notas - $real_notas < 6) {
    $porc_notas_load = 100; // Ajuste por margen de error
}
```

**Hallazgo:** Ajuste de precisión — si la diferencia entre goal y real es menor a 6 registros, se redondea a 100%. Esto sugiere que el sistema tolera un pequeño margen de error en la carga de notas.

### 2.2 Índice de Rendimiento en Evaluación (IRE)

**Fórmula:** `IRE = (IEE_del_profesor / Promedio_IEE_de_pares) × 100`

Donde **IEE** (Índice de Eficiencia en Evaluación) = `real_notas / goal_notas`

El IRE es una métrica **relativa**: compara la eficiencia del profesor (cantidad de notas cargadas vs esperadas) contra el promedio de los demás profesores que comparten el mismo plan de estudios (`Pestudio`). El valor se muestra como porcentaje.

**Implementación:**
```php
public function getProfesorIRE($pestudio_id, $lapso_id) {
    $pestudio = Pestudio::findOrFail($pestudio_id);
    $ieePROM  = $pestudio->getProfesorsIEEsPROM($lapso_id); // IEE promedio de pares
    $boletins = $this->getBoletins($lapso_id, $pestudio_id);
    return $boletins->count() / $ieePROM;
}
```

### 2.3 Cálculo de `goal_notas_load`

El goal (expectativa) se calcula como:
```sql
SUM( cantidad_estudiantes_en_seccion × cantidad_evaluaciones_registradas )
```

Esto recorre todas las secciones donde el profesor dicta clases, obtiene los estudiantes inscritos por lapso, y multiplica por la cantidad de evaluaciones que ha registrado.

### 2.4 Modal de Notificación — Reglas de Visibilidad

```php
$mostrarModalNotificacion = (
    $esProfesorGuia &&                              // Debe ser tutor
    $tieneReportesDiagnosticos &&                   // Debe haber reportes generados
    Carbon::now()->between($lapso2->finicial, $lapso2->ffinal)  // Fecha actual dentro del Lapso 2
);
```

Además, el modal se oculta si el usuario ya hizo click en "No mostrar más" (vía `localStorage.setItem('noMostrarNotificacionDiagnostico', 'true')`).

### 2.5 Tutor (Profesor Guía) — Accesos

```php
$profesor->seccion_guias  // Accessor: secciones donde es tutor (via ProfesorGuia pivot)
$profesor->isProfesorGuia // Accessor: bool
```

Las secciones guía se obtienen mediante join con `profesor_guias` table.

---

## 3. SQL Schema (Tablas Involucradas)

### 3.1 Tablas Consultadas por los Indicadores

| Tabla | Propósito | Columnas clave usadas |
|-------|-----------|----------------------|
| `pevaluacions` | Planes de evaluación | `id, profesor_id, lapso_id, seccion_id, pensum_id, escala_id` |
| `evaluacions` | Evaluaciones registradas | `id, pevaluacion_id, fecha` |
| `boletins` | Notas/calificaciones | `id, evaluacion_id, estudiant_id, nota` |
| `escalas` | Escalas de calificación | `id, minimo, maximo, aprobacion` |
| `estudiants` | Estudiantes | `id, status_active` |
| `inscripcions` | Inscripciones | `id, estudiant_id` |
| `seccions` | Secciones | `id` |
| `profesor_guias` | Profesores tutores | `profesor_id, seccion_id` |
| `section_diagnostic_reports` | Reportes de diagnóstico | `id, section_id, global_precision_avg` |
| `pensums` | Pensum | `id, asignatura_id, pestudio_id` |
| `pestudios` | Planes de estudio | `id, peducativo_id` |
| `peducativos` | Proyectos educativos | `id, name` |
| `lapsos` | Períodos académicos | `id, name, code, finicial, ffinal, date_cutnote` |

### 3.2 Joins Típicos (cada indicador hace 5-7 joins)

```sql
-- Patrón de consulta para promedio/aprobados:
boletins → evaluacions → pevaluacions → (escalas OR pensums → pestudios → peducativos)
                                       → (seccions)
                  → estudiants → inscripcions
```

---

## 4. Endpoints API (Migración NextJS Propuesta)

### 4.1 Endpoints de Dashboard

| Método | Endpoint | Propósito | Reemplaza |
|--------|----------|-----------|-----------|
| GET | `/api/profesor/dashboard` | Dashboard completo del profesor | `HomeController@home` |
| GET | `/api/profesor/dashboard/indicators` | Indicadores por lapso | Cómputo inline en controller |
| GET | `/api/profesor/dashboard/indicators/{lapsoId}` | Indicadores de un lapso específico | Box por tab |
| GET | `/api/profesor/dashboard/ire/{pestudioId}/{lapsoId}` | IRE específico | `getProfesorIRE()` |
| GET | `/api/profesor/dashboard/profile` | Perfil del profesor | `$profesor` data |
| GET | `/api/profesor/dashboard/tutor-info` | Info de tutor (secciones guía + reportes) | Lógica condicional |
| GET | `/api/profesor/dashboard/chart/activities` | Datos del gráfico de actividades | `actividades.chart` endpoint existente |

### 4.2 Respuesta Propuesta

```typescript
interface DashboardResponse {
  profesor: ProfesorProfile;
  indicadores: LapsoIndicador[];
  tutorInfo: {
    esProfesorGuia: boolean;
    seccionesGuia: SeccionGuia[];
    tieneReportesDiagnosticos: boolean;
    ultimoReporte: DiagnosticReport | null;
  };
  lapsoActivo: Lapso;
}

interface LapsoIndicador {
  id: number;
  lapso: Lapso;
  name: string;
  code: string;
  countPevaluacions: number;
  countEvaluacions: number;
  porcNotasLoad: number;  // 0-100
  promedio: number | null;
  porcAprobados: number | null;
  ire: {
    value: number | null;
    pestudioCode: string | null;
    formula: string;
  };
}
```

### 4.3 Servicio Propuesto

```typescript
// DashboardService — separa la lógica de cómputo del controller
class DashboardService {
  async getIndicators(profesorId: number, lapsoId: number): Promise<LapsoIndicador>;
  async getIRE(profesorId: number, pestudioId: number, lapsoId: number): Promise<number>;
  async getGoalNotasLoad(profesor, lapsoId: number): Promise<number>;
  async getRealNotasLoad(profesor, lapsoId: number): Promise<number>;
  async checkTutorStatus(profesorId: number): Promise<TutorInfo>;
}
```

---

## 5. UI Wireframes

### 5.1 Dashboard Layout

```
┌── Profesor Dashboard ───────────────────────────────────────────┐
│ ┌─ Navbar (14+ módulos) ──────────────────────────────────────┐│
│ │ [🏠][📋][🔬][📊][📝][🎓][🏆][👥][🤝][⚠️][📄][🗳️][📈][👤]       ││
│ └──────────────────────────────────────────────────────────────┘│
│                                                                  │
│ ┌── col-9 ───────────────────────┐ ┌── col-3 ────────────────┐  │
│ │                                │ │ Si es Profesor Guía:     │  │
│ │ Flash Message ✅                │ │ ┌─ Card Tutor ────────┐ │  │
│ │                                │ │ │ Profesor Guía       │ │  │
│ │ ┌─ Card: Indicadores ───────┐ │ │ │ [4to-A] 2 reportes  │ │  │
│ │ │ [Lapso 1] [Lapso 2] [Lapso 3] │ │ │ [5to-B] 1 reporte  │ │  │
│ │ │ ┌─ Tab Content ─────────┐ │ │ │ └─────────────────────┘ │  │
│ │ │ │ ┌─── 6 Boxes ──────┐ │ │ │ └──────────────────────────┘  │
│ │ │ │ │ 📋 Planes: 12   │ │ │ │                                │
│ │ │ │ │ 📝 Evaluac: 45  │ │ │ │                                │
│ │ │ │ │ 📊 Notas: 87%   │ │ │ │                                │
│ │ │ │ │ 🎯 Promedio: 16 │ │ │ │                                │
│ │ │ │ │ ✅ Aprob: 76%   │ │ │ │                                │
│ │ │ │ │ 📈 IRE: 104%    │ │ │ │                                │
│ │ │ │ └─────────────────┘ │ │ │                                │
│ │ │ │ Fórmula IRE explicada │ │ │                                │
│ │ │ └────────────────────┘ │ │                                │
│ │ └────────────────────────┘ │                                │
│ │                            │                                │
│ │ ┌─ Card: Gráfico ────────┐ │                                │
│ │ │ [Lapso 1][Lapso 2][Lapso 3] │                                │
│ │ │ ┌─ Chart.js ──────────┐ │ │                                │
│ │ │ │ 📊 Actividades       │ │ │                                │
│ │ │ │ Evaluativas          │ │ │                                │
│ │ │ └─────────────────────┘ │ │                                │
│ │ └────────────────────────┘ │                                │
│ └────────────────────────────┘ └──────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────┘
```

### 5.2 Modal de Notificación (Condicional)

```
┌── Modal (data-backdrop="static") ───────────────────────────────┐
│ ¡INFORME DE DIAGNÓSTICO DISPONIBLE!                [✖]          │
├──────────────────────────────────────────────────────────────────┤
│                                                                  │
│ ℹ️ Estimado Profesor Tutor                                      │
│ Se han generado informes de diagnóstico académico para las      │
│ secciones que usted guía.                                       │
│                                                                  │
│ ┌── Último Informe ─────────────────────────────────────────┐   │
│ │ Diagnóstico: Diagnóstico Inicial 2025-2026                │   │
│ │ Sección: 4to Año - Sección A                              │   │
│ │ Generado: 15/10/2025                                      │   │
│ │ Precisión Global: 72.5%                                   │   │
│ └───────────────────────────────────────────────────────────┘   │
│                                                                  │
│ ┌── Secciones con Reportes ────────────────────────────────┐   │
│ │ 📁 4to Año - Sección A — 2 informe(s) disponible(s)     │   │
│ │ 📁 5to Año - Sección B — 1 informe(s) disponible(s)     │   │
│ └───────────────────────────────────────────────────────────┘   │
│                                                                  │
│ ⭐ ¿Qué encontrará en los informes?                             │
│ • Análisis de fortalezas y debilidades por área                 │
│ • Distribución del desempeño estudiantil                        │
│ • Recomendaciones pedagógicas específicas                       │
│                                                                  │
├──────────────────────────────────────────────────────────────────┤
│ [✖ Cerrar]         [No mostrar más]    [🔗 Ir a Informes]      │
└──────────────────────────────────────────────────────────────────┘
```

### 5.3 Estados de UI

| Estado | Dashboard | Indicadores | Gráfico | Modal |
|--------|-----------|-------------|---------|-------|
| **Loading** | Spinner navbar | Skeleton boxes | Chart loading | N/A |
| **Empty — sin profesor** | Redirect/login | N/A | N/A | N/A |
| **Sin datos de lapso** | Muestra tabs pero boxes vacíos (0) | "0" en cada box | Chart vacío | N/A |
| **Sin guía/tutor** | Card tutor no se renderiza | N/A | N/A | Modal no se muestra |
| **Sin reportes diag** | N/A | N/A | N/A | Modal no se muestra |
| **Fuera de lapso 2** | N/A | N/A | N/A | Modal no se muestra |
| **Modal descartado** | N/A | N/A | N/A | No muestra por localStorage |

---

## 6. Árbol de Componentes

### 6.1 Jerarquía Blade (Sin Livewire)

```
HomeController@home()
│
├── $profesor           → Profesor::where('user_id', Auth::id())->first()
├── $esProfesorGuia     → $profesor->seccion_guias?->count() > 0
├── $seccionesGuia      → $profesor->seccion_guias
├── $tieneReportesDiagnosticos → SectionDiagnosticReport::whereIn()->exists()
├── $ultimoReporte      → SectionDiagnosticReport::whereIn()->with()->latest()->first()
├── $lapso              → Lapso::getCurrentOrFirst() ?? Lapso::find(2)
├── $lapsos             → Lapso::all()
├── $lapso_active       → $lapso (alias)
├── $mostrarModalNotificacion → bool (3 condiciones)
└── $indicadores        → Collection<LapsoIndicador>
    ├── count_pevaluacions → Pevaluacion::where(profesor_id, lapso_id)->count()
    ├── count_evaluacions  → (new Pevaluacion)->count_evaluacion_prof_lapso()
    ├── porc_notas_load    → Profesor::goal_notas_load() / real_notas_load()
    │   ├── goal_notas_load() → 7-join query + loop estudiantes
    │   └── real_notas_load() → 8-join query
    ├── promedio            → Profesor::getPromedio() → Boletin 5-join query
    └── porc_aprobados      → Profesor::getPorcAprobados() → Boletin 4-join query

View: profesors.home
├── @extends: layouts.dashboard.app
│   ├── layouts.app (HTML shell: Bootstrap 4.3.1, FA5, SweetAlert2, Alpine, jQuery 3.3.1)
│   │
│   ├── @section('navbar')
│   │   └── dashboard/navbar/app.blade.php (176 líneas)
│   │       ├── Logo / Home button
│   │       └── 14+ icon-buttons → href route() a submódulos
│   │
│   ├── @section('sidebar')
│   │   └── dashboard/sidebar/app.blade.php (foto, nombre, email)
│   │
│   ├── @section('main')
│   │   ├── oper_ok.blade.php (flash message)
│   │   ├── indicadores.blade.php
│   │   │   ├── boxes/pevaluacion.blade.php ← core
│   │   │   │   ├── nav-tabs (por lapso)
│   │   │   │   └── tab-content (por lapso)
│   │   │   │       ├── 6× @component('elements.boxes.chart')
│   │   │   │       └── IRE explanation text
│   │   │   └── evaluacions/chart/actividades.blade.php
│   │   │       └── Chart.js canvas
│   │   │
│   │   ├── [cond] modals/home/main.blade.php (COMENTADO)
│   │   ├── [cond] modal-notificacion-diag.blade.php
│   │   │   ├── Modal Bootstrap
│   │   │   ├── Último reporte info
│   │   │   ├── Secciones con reportes
│   │   │   └── Script con localStorage logic
│   │   │
│   │   └── [cond en sidebar] card-profesor-guia.blade.php
│   │       └── Lista de secciones + badge conteo → route()
│   │
│   └── @section('scripts')
│       ├── Chart.bundle.js
│       ├── ChartFunction.js
│       ├── ChartEvent.js
│       └── SweetAlert2 CDN
│
└── @section('footer')
    └── layouts/footer/dashboard.blade.php
```

### 6.2 Capa de Datos — Profesor Model (1,545 líneas totales)

```
Profesor Model (687 líneas)
├── Traits
│   ├── SoftDeletes
│   ├── Indicators.php (339 líneas)  ← Métrica principal
│   │   ├── getPorcAprobados()
│   │   ├── getPromedio()
│   │   ├── getBoletins()
│   │   ├── getBoletinsPeducativo()
│   │   ├── getProfesorIREPeducativo()
│   │   ├── getProfesorIRE()
│   │   ├── getProfesorIEE()
│   │   ├── getProfesorIEECN()
│   │   ├── getProfesorIEECNPeducativo()
│   │   ├── getProfesorIEEForPeducativo()
│   │   ├── goal_notas_load()
│   │   ├── goal_notas_load_corte()
│   │   ├── real_notas_load_corte()
│   │   ├── real_notas_load()
│   │   ├── goal_pevaluacion_load()
│   │   └── real_pevaluacion_load()
│   ├── Lists.php (341 líginas)      ← Queries de listado
│   └── Statics.php (30 líneas)      ← Helpers estáticos
│   └── Evaluacions.php (148 líneas) ← Evaluaciones del profesor
├── Relationships
│   ├── pevaluacions()
│   ├── profesor_gestables()
│   ├── user()
│   ├── profesor_guias()
│   ├── incidents()
│   └── pensums()
└── Custom Accessors
    ├── getSeccionGuiasAttribute()
    ├── getIsProfesorGuiaAttribute()
    ├── getFullNameAttribute()
    ├── getPestudioAttribute()
    ├── getPeducativosAttribute()
    └── getGuiaEstudiantsAttribute()
```

---

## 7. Plan de Migración (Fases)

### Fase 1 — API Layer + Service

| # | Tarea | Endpoints | Dependencias |
|---|-------|-----------|--------------|
| 1.1 | Crear `DashboardService` con lógica de indicadores | — | Profesor, Pevaluacion, Boletin, Lapso |
| 1.2 | Endpoint de dashboard completo | GET /dashboard | DashboardService |
| 1.3 | Endpoint de perfil de profesor | GET /dashboard/profile | Profesor model |
| 1.4 | Endpoint de tutor info | GET /dashboard/tutor-info | SectionDiagnosticReport |
| 1.5 | Endpoint de indicadores por lapso | GET /dashboard/indicators/{lapsoId} | DashboardService |
| 1.6 | Endpoint de IRE | GET /dashboard/ire/{pestudioId}/{lapsoId} | Profesor::getProfesorIRE() |

### Fase 2 — Frontend NextJS

| # | Tarea | Componentes | Notas |
|---|-------|-------------|-------|
| 2.1 | Dashboard Layout | `DashboardLayout` | Navbar + sidebar + main area |
| 2.2 | Indicator Tabs | `IndicatorTabs` | Bootstrap-style nav-tabs por lapso |
| 2.3 | Indicator Box | `IndicatorBox` | Reusable card con icono, total, subtítulo |
| 2.4 | IRE Component | `IRECard`, `IREFormula` | Card + tooltip explicativo |
| 2.5 | Activity Chart | `ActivityChart` | Chart.js o Recharts |
| 2.6 | Tutor Card | `TutorSidebarCard` | Sidebar widget |
| 2.7 | Diagnostic Modal | `DiagnosticNotificationModal` | Modal con localStorage |
| 2.8 | Flash Messages | `FlashMessage` | Sistema de notificaciones toast |

### Fase 3 — Limpieza y Optimización

| # | Tarea | Detalle |
|---|-------|---------|
| 3.1 | Eliminar vistas legacy | labels, graphics, evaluativos, helps (comentados) |
| 3.2 | Eliminar archivo `.copy` | `boxes/pvalaucion.blade copy.php` |
| 3.3 | Separar lógica de indicadores del controller | Mover a `DashboardService` |
| 3.4 | Optimizar queries N+1 en traits de Profesor | 7-8 joins por indicador → queries agregadas |
| 3.5 | Cache de indicadores | Los indicadores no cambian frecuentemente → cache 5-15 min |

---

## 8. Edge Cases y Problemas Conocidos

### 8.1 Bugs Activos

| # | Bug | Lugar | Impacto | Solución |
|---|-----|-------|---------|----------|
| 1 | **N+1 pesado en goal_notas_load** | `Indicators.php:155` — loop de estudiantes por sección | Por cada sección, query adicional de estudiantes | Eager load + withCount |
| 2 | **`Lapso::findOrFail` en goal_notas_load** | `Indicators.php:157` — lanza 404 si lapso_id es null | Si se llama sin lapso_id, falla | Volver condicional |
| 3 | **Modal hardcodeado a Lapso ID 2** | `HomeController.php:89` | `Lapso::where('id', 2)` — asume que lapso 2 es el de diagnóstico | Hacer configurable |
| 4 | **Hardcoded `$lapso = Lapso::find(2)`** | `HomeController.php:74` | Fallback a lapso ID 2 si no hay activo | Usar primer lapso disponible |
| 5 | **Variable `$icon_menus` no definida localmente** | Múltiples vistas | Si no está definida globalmente, iconos no se muestran | Variable global/helper |
| 6 | **Condición de ajuste `$goal_notas - $real_notas < 6`** | `HomeController.php:127` | Margen arbitrario que infla porcentaje | Documentar o parametrizar |

### 8.2 Edge Cases

| # | Escenario | Comportamiento Actual | Riesgo |
|---|-----------|-----------------------|--------|
| 1 | Profesor sin pevaluacions | `$count_pevaluacions = 0`, `$goal_notas = 0` → `$porc_notas_load = 0` | Dashboard muestra todo en 0 |
| 2 | Profesor sin estudiantes en sección | `$estudiants->count() = 0` → goal = 0 | División por cero evitada |
| 3 | Lapso activo no encontrado | Fallback a ID 2 | Puede no ser correcto |
| 4 | Profesor sin secciones guía | Card no se renderiza | OK |
| 5 | `$goal_notas = 0` → división | `$porc_notas_load = 0` | OK (ternary check) |
| 6 | `localStorage` bloqueado (incognito/Safari) | `localStorage.getItem()` lanza error | Script se rompe |
| 7 | Múltiples lapsos con diferentes configuraciones | Todos los lapsos se renderizan como tabs | OK |

### 8.3 Problemas de Performance

| # | Consulta | Joins | Riesgo |
|---|----------|-------|--------|
| 1 | `getPorcAprobados()` | 4 joins + `GROUP BY` | Por cada lapso, 1 query |
| 2 | `getPromedio()` | 5 joins + `GROUP BY` | Por cada lapso, 1 query |
| 3 | `goal_notas_load()` | 6 joins + loop de estudiantes | Por cada lapso + cada sección |
| 4 | `real_notas_load()` | 7 joins + `GROUP BY` | Por cada lapso, 1 query |
| 5 | `getBoletins()` | 7 joins | Usado por IRE |

**Total estimado por carga de dashboard:** 5 indicadores × N lapsos (típicamente 3) = **~15 queries pesadas**, cada una con 4-7 joins.

---

## 9. Checklist de Validación

### 9.1 Funcional
- [ ] Dashboard carga correctamente para profesor sin guía/tutor
- [ ] Dashboard carga correctamente para profesor guía con reportes
- [ ] Dashboard carga correctamente para profesor guía sin reportes
- [ ] Tabs de lapsos funcionan (uno activo por defecto)
- [ ] 5 indicadores se muestran correctamente por lapso
- [ ] IRE se calcula y muestra (o null si sin pestudio)
- [ ] Fórmula IRE visible como texto explicativo
- [ ] Gráfico Chart.js carga en el tab activo
- [ ] Modal de notificación se muestra SOLO si condiciones (guía + reportes + lapso 2)
- [ ] "No mostrar más" guarda en localStorage y oculta
- [ ] Card de profesor guía en sidebar (condicional)
- [ ] Flash messages se muestran si existen

### 9.2 Data
- [ ] Queries optimizadas (reducir joins, evitar N+1)
- [ ] Cache implementado para indicadores
- [ ] Fallback lapso robusto (no hardcodeado a ID 2)
- [ ] Ajuste de margen `goal - real < 6` justificado o parametrizado

### 9.3 UI/UX
- [ ] Loading skeleton mientras cargan indicadores
- [ ] Empty states para 0 valores
- [ ] Tooltip/help en cada indicador
- [ ] Responsive: boxes col-sm-3 se apilan en mobile
- [ ] Modal accesible (teclado, focus trap)
- [ ] SweetAlert en acciones importantes

### 9.4 Migración
- [ ] Vistas legacy eliminadas (labels, graphics, evaluativos, helps)
- [ ] Archivo `.copy` eliminado
- [ ] Service layer implementado para lógica de indicadores
- [ ] Traits de Profesor simplificados o refactorizados

---

## 10. Dependencias y Acoplamiento

### 10.1 Dependencias del Dashboard

```
Profesor Dashboard (HomeController)
├── Profesor Model (687 + 858 líneas traits)
│   ├── Indicators trait  → Boletin, Lapso, Seccion, Pestudio, Peducativo
│   ├── Lists trait       → Pevaluacion, Pestudio, Profesor
│   ├── Statics trait     → User
│   └── Evaluacions trait → Pevaluacion, Boletin
├── Pevaluacion Model (528 líneas)
├── Lapso Model (311 líneas)
├── SectionDiagnosticReport Model (109 líneas)
├── Pestudio Model (para IRE)
├── Peducativo Model (para IRE)
├── Boletin Model
├── Seccion Model
├── ProfesorGuia Model
├── Routes: 19 sub-route files para navegación
├── Layout: navbar con 14+ submódulos
└── Chart.js (bundled JS assets)
```

### 10.2 Acoplamiento con Submódulos

| Submódulo | Navbar | Dashboard Reference |
|-----------|--------|-------------------|
| Home | ✅ (🏠) | — |
| Activities | ✅ (📋) | ❌ (solo nav) |
| Diagnostics | ✅ (🔬) | ❌ (solo nav) |
| Pevaluacions | ✅ (📊) | ❌ (solo nav) |
| Evaluacions | ✅ (📝) | Indirecto (gráfico chart) |
| Boletins | ✅ (🎓) | ❌ |
| Competitions | ✅ (🏆) | ❌ |
| Profesor Guía | ✅ (👥) | ✅ (modal + sidebar card) |
| Estudiantes | ✅ (👤) | ❌ |
| Debates | ✅ (🗳️) | ❌ |
| Incidents | ✅ (⚠️) | ❌ |
| Social Actions | ✅ (🤝) | ❌ |
| Census | ✅ (📈) | ❌ |
| Users | ✅ (👤) | ❌ |

---

## 11. Comparativa con Otros Módulos

| Aspecto | Home (este) | Debates | Competitions | Diagnostics |
|---------|-------------|---------|--------------|-------------|
| **Patrón** | Controller + Blade | Livewire + Traits | Livewire + Compon. | Livewire |
| **Livewire?** | ❌ No | ✅ Sí | ✅ Sí | ✅ Sí |
| **CRUD?** | ❌ Solo lectura | ✅ 5 entidades | ✅ 2 entidades | ✅ 4 entidades |
| **Líneas controller** | 165 | 33 | 33 | — |
| **Líneas traits** | 858 (Profesor) | 1,014 | 193 (QuestionTrait) | — |
| **Cálculos DB pesados** | ✅ 15+ joins | ❌ CRUD simple | ❌ Query básica | ❌ |
| **Modal/Overlay** | 1 condicional | 7 | 1 | — |
| **AI Integration** | ❌ | ✅ Dual | ❌ | ✅ Gemini |
| **Vistas legacy** | 10+ | 0 | 7 | 0 |
| **Chart.js** | ✅ Dashboard | ❌ | ❌ | ❌ |

---

## 12. Hallazgos y Recomendaciones

### 12.1 Hallazgos Críticos

| # | Hallazgo | Impacto | Acción Requerida |
|---|----------|---------|------------------|
| H1 | **Sin Service Layer** — la lógica de indicadores está mezclada entre controller y traits del modelo | Violación SRP, difícil de testear | Crear `DashboardService` |
| H2 | **~10 vistas legacy** no usadas (labels, graphics, evaluativos, helps, pvalaucion copy) | ~400 líneas de código muerto | Eliminar en migración |
| H3 | **~15 queries pesadas por carga** con 4-7 joins cada una | Performance degradada al cargar dashboard | Cache + queries optimizadas |
| H4 | **Ajuste arbitrario en porc_notas_load** — si `goal - real < 6` → 100% | Infla artificialmente indicador | Documentar o parametrizar |
| H5 | **Lapso ID 2 hardcodeado** como fallback y para modal | Asume estructura de datos específica | Hacer configurable o dinámico |
| H6 | **Modal sin "No mostrar más" persistente** — localStorage frágil | En Safari/incognito puede fallar | Usar cookie o preferencia en DB |

### 12.2 Recomendaciones de Arquitectura (NextJS)

1. **Dashboard Service Layer**: Separar toda la lógica de cómputo de indicadores en un `DashboardService` con métodos individuales por indicador y agregados por lapso.

2. **Cache Estratégico**: Los indicadores (promedio, aprobación, carga de notas) no cambian minuto a minuto. Cachear por 5-15 minutos usando Redis o React Query.

3. **WebSocket Updates**: Idealmente, el gráfico de actividades y los indicadores podrían actualizarse vía WebSocket cuando un profesor registra una nueva evaluación.

4. **Dashboard Skeleton**: El dashboard es la landing page — priorizar perceived performance con skeleton loading states mientras se resuelven las queries pesadas.

5. **Persistencia de preferencias**: El "No mostrar más" del modal debe migrarse de localStorage a preferencia de usuario en DB (ej: `user_preferences.dont_show_diag_notification`).

6. **IRE como endpoint separado**: El IRE depende del promedio de pares — un endpoint específico permite cachear el valor independientemente.

7. **Gráfico Server-side vs Client-side**: Chart.js se renderiza en cliente con datos de un endpoint. Alternativa: generar SVG en servidor para reducir carga JS.

### 12.3 Datos de Referencia

```typescript
// Dashboard response unificado
interface DashboardData {
  profesor: {
    id: number;
    fullName: string;
    email: string;
    photoUrl?: string;
    tiTeacher: string;
    ciProfesor: string;
    isGuia: boolean;
    seccionesGuia: Array<{
      id: number;
      gradoName: string;
      seccionName: string;
      reportesCount: number;
    }>;
  };
  lapsoActivo: {
    id: number;
    name: string;
    code: string;
    fechaInicial: string;
    fechaFinal: string;
  };
  indicadores: Array<{
    lapsoId: number;
    lapsoName: string;
    lapsoCode: string;
    planesEvaluacion: number;
    evaluacionesRegistradas: number;
    porcentajeNotas: number;  // 0-100
    promedio: number | null;
    aprobados: number | null; // porcentaje
    ire: number | null;
    irePestudioCode: string | null;
  }>;
  notificacionDiagnostico: {
    mostrar: boolean;
    ultimoReporte?: {
      seccion: string;
      fecha: string;
      precisionGlobal: number | null;
    };
  };
}
```

---

> **Documentación generada:** 2026-06-06
> **Módulos relacionados:** [gestion-debates.md](gestion-debates.md), [gestion-competencias.md](gestion-competencias.md), [gestion-diagnostics.md](gestion-diagnostics.md), [gestion-actividades-planificacion.md](../planning/gestion-actividades-planificacion.md)
> **Ver también:** [RETROSPECTIVE.md](../RETROSPECTIVE.md) §4 (dependency graph)
