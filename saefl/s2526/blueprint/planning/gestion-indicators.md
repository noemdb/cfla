# Blueprint: Indicadores de Planificación (Dashboard Planning)

> **Módulo:** Planning > Indicators (Dashboard principal de planificación)
> **Controller:** `app/Http/Controllers/Planning/Tab/HomePlanningController.php` (53 líneas)
> **Livewire:** `app/Http/Livewire/Planning/Competition/DebateIndicators.php` (57 líneas)
> **Vistas:** `resources/views/plannings/partials/` (7+ archivos) + `livewire/planning/competition/` (1+ archivos)
> **Modelos base:** Pestudio, Profesor (traits Indicators), Grado (trait Indicators), Estudiant, Lapso, Pevaluacion
> **Chart.js + DataTables:** Dashboard interactivo
> **Prioridad:** P0 — landing page del módulo Planning

---

## 0. Resumen Ejecutivo

El módulo **Indicadores de Planificación** es el dashboard institucional del departamento de planificación. Proporciona KPIs agregados a nivel de **planes de estudio (Pestudio)** y **períodos académicos (Lapso)**, cubriendo 5 dimensiones:

1. **Indicadores Principales** — Inscritos, evaluaciones, actividades, profesores con carga
2. **Profesores** — Tabla por profesor con IEE, IEE-CN, IRE por lapso/pestudio
3. **Actividades** — 6 indicadores (cobertura curricular, participación, seguimiento, aprobación, supervisión)
4. **Planes de Evaluación** — Gráfico Chart.js de evaluaciones registradas
5. **Lecciones** — Tab vacía (sin implementar)

**Arquitectura principal:** Controller tradicional (no Livewire) con 3 niveles de tabs Bootstrap anidadas: `[Principales|Profesores|Actividades|Planes|Lecciones] → [Lapsos] → [Pestudios]`.

**Sub-módulo de Competiciones:** Livewire component separado (`DebateIndicators`) con precisión por grado, modales de detalle y stats, y una versión para líderes (con autorización por grado).

**Hallazgos críticos:** (1) La pestaña de Lecciones está vacía. (2) Las queries en vistas (a través de métodos de modelo) generan N+1 masivo — cada pestudio llama `inscritos()`, `getEvaluacions()`, `getActivities()`, `getProfesorEvaluacions()`. (3) Los traits de Pestudio y Profesor tienen métodos duplicados conceptualmente con los traits de Profesor del dashboard home. (4) `$profesors = Profesor::getProfesorForLeaderId($user->id)` — esta query scope a los profesores que el planning user puede ver.

---

## 1. Validación contra Código Fuente

### 1.1 Routes

**Archivo:** `routes/web.php` (líneas 46-50) + `routes/app/tab/plannings/home.php` (10 líneas)

```php
// Grupo: prefix 'app/plannings', middleware ['auth', 'is_planning'], namespace 'Planning'

// routes/app/tab/plannings/home.php:
Route::get('/home', 'HomePlanningController@home')->name('plannings.home');
Route::get('/indicators', 'HomePlanningController@indicators')->name('plannings.indicators');

// routes/app/tab/plannings/competitions.php:
Route::get('/competitions/index', 'CompetitionController@index')->name('plannings.competitions.index');
Route::get('/competitions/indicators', 'CompetitionController@indicators')->name('plannings.competitions.indicators');
```

### 1.2 Controllers

**Archivo:** `app/Http/Controllers/Planning/Tab/HomePlanningController.php` (53 líneas)

```php
class HomePlanningController extends Controller
{
    use UserDataInitializer;

    public function __construct()
    {
        $this->middleware(['auth', 'is_planning', function ($request, $next) {
            $this->initializeUserData();
            return $next($request);
        }]);
    }

    public function home()
    {
        // → plannings.home (página de inicio simple)
    }

    public function indicators()
    {
        $user = $this->user;
        $pestudios = $this->pestudios;
        $peducativos = $this->peducativos;
        $autoridad = $this->autoridad;
        $profesors = Profesor::getProfesorForLeaderId($user->id);
        $estudiants = Estudiant::select('estudiants.*')->active('true')->WidthInscripcion()->get();
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();
        $now = Carbon::now()->format('Y-m-d');
        $list_comment_autoridad = $this->listCommentAutoridad;

        return view('plannings.indicators', compact(
            'user', 'autoridad', 'list_comment_autoridad', 'pestudios',
            'lapsos', 'lapso_active', 'estudiants', 'now', 'profesors'
        ));
    }
}
```

**Archivo:** `app/Http/Controllers/Planning/Tab/UserDataInitializer.php` (39 líneas)

```php
trait UserDataInitializer
{
    public function initializeUserData()
    {
        $this->user = Auth::user();
        $this->autoridad = Autoridad::where('user_id', $this->user->id)->first();
        $this->listCommentAutoridad = $this->autoridad->COLUMN_COMMENTS ?? collect();
        $this->pestudios = Pestudio::all();
        $this->peducativos = Peducativo::all();
    }
}
```

### 1.3 Livewire — Competition Indicators

**Archivo:** `app/Http/Livewire/Planning/Competition/DebateIndicators.php` (57 líneas)

| Propiedad | Tipo | Propósito |
|-----------|------|-----------|
| `$competition` | DebateCompetition | Modelo cargado en mount |
| `$peducativos` | Collection | Niveles educativos con grados |
| `$selectedGrado` | int|null | Grado seleccionado para detalle |
| `$showDetails` | bool | Mostrar modal de detalle |
| `$showStats` | bool | Mostrar modal de stats |
| `$statsGrado` | Collection|null | Datos de stats del grado |
| `$statsGradoId` | int|null | ID del grado en stats |
| `$seccions` | Collection|null | Secciones del grado |

Métodos: `showGradoDetails($gradoId)`, `closeDetails()`, `showGradoStats($gradoId)`, `closeStats()`, `render()`

### 1.4 Vistas — Árbol Completo

```
plannings.indicators (37 líneas)
├── Extiende: plannings.layouts.home.app
│   └── plannings.partials.index (63 líneas) ← MAIN
│       ├── 5 Bootstrap nav-tabs:
│       │   1. Indicadores Principales → estudiantil.blade.php
│       │   2. Profesores → seguimiento.blade.php
│       │   3. Actividades → activities.blade.php
│       │   4. Planes de Evaluación → charts/evaluacions/actividades.blade.php
│       │   5. Lecciones → (vacío)
│       └── DataTables JS/CSS
│
├── TAB 1: estudiantil.blade.php (69 líneas)
│   └── Por cada Pestudio:
│       ├── 4 indicator boxes (evaluacions.elements.boxes.indicators):
│       │   ├── INSCRITOS → $pestudio->inscritos()
│       │   ├── EVALUACIONES REGISTRADAS → $pestudio->getEvaluacions()->count()
│       │   ├── ACTIVIDADES REGISTRADAS → $pestudio->getActivities()->count()
│       │   └── PROFESORES CON CARGA → $pestudio->getProfesorEvaluacions()->count()
│       └── <hr> separador
│
├── TAB 2: seguimiento.blade.php (51 líneas)
│   └── Nav-tabs por Lapso
│       └── Nav-tabs anidados por Pestudio
│           └── profesors.blade.php (123 líneas)
│               ├── DataTable por profesor:
│               │   ├── N. Actividades [% aprobación]
│               │   ├── Planes de Evaluación
│               │   ├── N. Notas Cargadas
│               │   ├── IEE (Índice de Eficiencia en Evaluación) %
│               │   ├── IEE-CN (Corte de Notas) %
│               │   └── IRE (Índice Relativo de Rendimiento) %
│               └── DataTables init JS
│
├── TAB 3: activities.blade.php (52 líneas)
│   └── Nav-tabs por Lapso
│       └── Nav-tabs anidados por Pestudio
│           └── activity.blade.php (108 líneas)
│               ├── 6 indicator boxes (plannings.elements.boxes.indicators):
│               │   1. Total de actividades planificadas
│               │   2. Indicador de Cobertura Curricular (promedio x Área)
│               │   3. Indicador de Participación (% docentes activos)
│               │   4. Indicador de Seguimiento (% actividades con comentarios)
│               │   5. Indicador de Aprobación (% actividades aprobadas)
│               │   6. Indicador de Supervisión (% planes con observaciones)
│               └── <hr> separador
│
└── TAB 4: charts/evaluacions/actividades.blade.php
    └── Chart.js canvas

plannings.competitions.indicators (40 líneas)
├── Extiende: plannings.layouts.dashboard.app
└── livewire:planning.competition.debate-indicators
    └── debate-indicators.blade.php (122 líneas)
        ├── Card por Peducativo (nivel educativo)
        │   └── Tabla: Grado | Correctas% | Erradas% | Puntaje | Detalles
        │       └── Por fila: btn [Detalles] [Stats]
        ├── [cond] Modal Details (fullscreen overlay)
        └── [cond] Modal Stats
```

### 1.5 Shared Components

| Componente | Archivo | Usado por |
|------------|---------|-----------|
| `evaluacions.elements.boxes.indicators` | `views/evaluacions/elements/boxes/indicators.blade.php` (23 líneas) | Tab 1 (estudiantil), y otros módulos |
| `plannings.elements.boxes.indicators` | `views/plannings/elements/boxes/indicators.blade.php` (25 líneas) | Tab 3 (activity) |
| `plannings.elements.progress.bars.simple` | `views/plannings/elements/progress/bars/simple.blade.php` (20 líneas) | Barras de progreso |

---

## 2. Reglas de Negocio

### 2.1 Dimensiones de Indicadores por Pestudio

Cada Pestudio (plan de estudios, ej: "Educación Media General") agrega datos de todos los profesores, grados y secciones asociados.

### 2.2 Fórmulas de Indicadores de Actividad

| Indicador | Fórmula | Fuente |
|-----------|---------|--------|
| **Cobertura Curricular** | `AVG(actividades por Área de Formación)` | `Pestudio::getAvgActivitiesPerPlan($lapsoId)` |
| **Participación** | `(activos / total_profesores) × 100` | `Pestudio::getActiveTeachersCount() / getTeachersCount()` |
| **Seguimiento** | `(activities con comments / total_activities) × 100` | Collection `where('comments','<>',null)` |
| **Aprobación** | `(activities con status=true / total_activities) × 100` | Collection `where('status',true)` |
| **Supervisión** | `(pevaluacions con observations / total_pevaluacions) × 100` | Collection `where('observations','<>',null)` |

### 2.3 KPIs de Profesor por Lapso/Pestudio

| KPI | Fórmula | Llamada en View |
|-----|---------|-----------------|
| **Actividades** | Count + `[aprobación %]` | `$profesor->getActivitiesPestudioLapso()` |
| **Planes Evaluación** | Count | `$profesor->getPevaluacionsPestudioLapso()` |
| **Notas Cargadas** | Count | `$profesor->getBoletinsPestudioLapso()` |
| **IEE** | `min(100, real/goal × 100)` | `$profesor->getProfesorIEE()` |
| **IEE-CN** | `min(100, real_corte/goal_corte × 100)` | `$profesor->getProfesorIEECN()` |
| **IRE** | `round(100 × boletins/ieePROM, 1)` | `$profesor->getProfesorIRE()` |

**ieePROM** es el promedio del IEE de todos los profesores del mismo Pestudio — calculado por `Pestudio::getProfesorsIEEsPROM($lapsoId)`.

### 2.4 Competition Indicators (sub-módulo)

| Métrica | Método | Notas |
|---------|--------|-------|
| Precisión por Grado | `getAccuracyForGrado($gradoId)` | % de respuestas correctas |
| Errores por Grado | `getWrongAnswerForGrado($gradoId)` | % de respuestas incorrectas |
| Puntaje por Grado | `getTotalScoreForGrado($gradoId)` | Suma de scores |
| Detalle por Grado | `showGradoDetails($gradoId)` | Modal con breakdown |
| Stats por Grado | `showGradoStats($gradoId)` | Modal con métricas avanzadas |

### 2.5 Scope de Datos (Planning vs Profesor)

| Aspecto | Planning Indicators (este) | Profesor Home (previo) |
|---------|---------------------------|----------------------|
| **Scope** | Todos los pestudios, todos los profesores | Un profesor específico |
| **Agregación** | Por Pestudio + Lapso | Por Lapso |
| **Profesores** | `Profesor::getProfesorForLeaderId($user->id)` | Un solo profesor (Auth) |
| **Estudiantes** | `Estudiant::active('true')->WidthInscripcion()->get()` | N/A |
| **Competition** | ✅ Livewire con IndicatorTrait | ❌ Sólo CRUD |

---

## 3. SQL Schema

### 3.1 Tablas Involucradas

| Tabla | Propósito | Métodos que la usan |
|-------|-----------|---------------------|
| `pestudios` | Planes de estudio | Base de agregación |
| `peducativos` | Proyectos educativos | Agrupación de pestudios |
| `pensums` | Pensum (pivote pestudio×asignatura) | Joins indirectos |
| `pevaluacions` | Planes de evaluación | `getPevaluacions()`, `getProfesorEvaluacions()` |
| `evaluacions` | Evaluaciones | `getEvaluacions()` |
| `boletins` | Notas | `getBoletinsPestudioLapso()`, IEE, IRE |
| `activities` | Actividades planificadas | `getActivities()`, indicadores de actividad |
| `inscripcions` | Inscripciones | Scope de estudiantes |
| `estudiants` | Estudiantes | Conteo de inscritos |
| `profesors` | Profesores | `getProfesorForLeaderId()`, KPIs |
| `profesor_gestables` | Asignación prof→grado | Scope de profesores |
| `lapsos` | Períodos académicos | Tabs y filtro temporal |
| `debate_competitions` | Competiciones | Competition indicators |
| `debate_questions/options/answers` | Respuestas de debates | Precisión y errores |

### 3.2 Patrón de Queries

```sql
-- Patrón típico para Pestudio Indicators:
pestudios → pensums → pevaluacions → evaluacions → boletins
                                    → activities
                      → profesor_gestables → profesors

-- Patrón para Competition Indicators:
debate_competitions → debates → debate_questions → debate_options
                                                            → debate_answers
                    → debate_groups
```

---

## 4. Endpoints API (Migración NextJS Propuesta)

### 4.1 Endpoints del Dashboard Principal

| Método | Endpoint | Propósito | Reemplaza |
|--------|----------|-----------|-----------|
| GET | `/api/planning/indicators` | Dashboard completo | `HomePlanningController@indicators` |
| GET | `/api/planning/indicators/main` | Indicadores principales por pestudio | Tab 1 (estudiantil) |
| GET | `/api/planning/indicators/teachers?lapsoId=&pestudioId=` | KPIs de profesores | Tab 2 + profesors table |
| GET | `/api/planning/indicators/activities?lapsoId=&pestudioId=` | Indicadores de actividad | Tab 3 (activity) |
| GET | `/api/planning/indicators/coverage?lapsoId=&pestudioId=` | Cobertura curricular | `getAvgActivitiesPerPlan()` |
| GET | `/api/planning/indicators/evaluations` | Datos de gráfico evaluaciones | Chart endpoint existente |

### 4.2 Endpoints de Competition Indicators

| Método | Endpoint | Propósito |
|--------|----------|-----------|
| GET | `/api/planning/competitions/{id}/indicators` | Dashboard de competición |
| GET | `/api/planning/competitions/{id}/grado/{gradoId}/accuracy` | Precisión por grado |
| GET | `/api/planning/competitions/{id}/grado/{gradoId}/wrong` | Errores por grado |
| GET | `/api/planning/competitions/{id}/grado/{gradoId}/score` | Puntaje por grado |
| GET | `/api/planning/competitions/{id}/grado/{gradoId}/details` | Detalle (modal) |
| GET | `/api/planning/competitions/{id}/grado/{gradoId}/stats` | Stats (modal) |

### 4.3 Respuestas Propuestas

```typescript
interface PlanningIndicatorsResponse {
  pestudios: PestudioIndicators[];
  lapsos: Lapso[];
  lapsoActivo: Lapso;
  totalEstudiantes: number;
  profesoresCount: number;
}

interface PestudioIndicators {
  id: number;
  name: string;
  code: string;
  color: string;
  inscritos: number;
  evaluacionesCount: number;
  activitiesCount: number;
  profesoresConCarga: number;
  ieePROM: number; // IEE promedio por lapso/pestudio
}

interface TeacherKPI {
  profesorId: number;
  fullName: string;
  activitiesCount: number;
  activitiesApprovalRate: number; // %
  pevaluacionsCount: number;
  notasCargadas: number;
  iee: number; // %
  ieeCN: number; // %
  ire: number; // %
}

interface ActivityIndicators {
  totalActividades: number;
  coberturaCurricular: number; // promedio
  participacion: number; // %
  seguimiento: number; // %
  aprobacion: number; // %
  supervision: number; // %
}

interface CompetitionIndicators {
  competitionId: number;
  competitionName: string;
  peducativos: Array<{
    id: number;
    name: string;
    grados: GradoAccuracy[];
  }>;
}

interface GradoAccuracy {
  gradoId: number;
  gradoName: string;
  accuracy: number; // %
  wrongPercentage: number; // %
  totalScore: number;
  answeredQuestions: number;
}
```

---

## 5. UI Wireframes

### 5.1 Layout Principal (5 Tabs)

```
┌── Planificación Dashboard ──────────────────────────────────────┐
│ ┌─ Navbar (Planning) ─────────────────────────────────────────┐│
│ │ [🏠] [📊] [👨‍🏫] [📋] [📝] [🎓] [🏆] [🔬] [👤] [📈]                ││
│ └─────────────────────────────────────────────────────────────┘│
│                                                                 │
│ ┌── Main Content ────────────────────────────────────────────┐  │
│ │                                                             │  │
│ │ ┌─ 5 Main Tabs ──────────────────────────────────────────┐ │  │
│ │ │ [Indicadores] [Profesores] [Actividades] [Planes Eval] [Lecciones] │ │  │
│ │ └────────────────────────────────────────────────────────┘ │  │
│ │                                                             │  │
│ │ ┌─ TAB 1: Indicadores Principales ───────────────────────┐ │  │
│ │ │ Pestudio: Educación Media General [MG]                  │ │  │
│ │ │ ┌─────────┐ ┌──────────┐ ┌──────────┐ ┌────────────┐ │ │  │
│ │ │ │ 📋 450  │ │ 📝 1,230 │ │ 📊 890   │ │ 👨‍🏫 45     │ │ │  │
│ │ │ │Inscritos│ │Eval.Regis│ │Act.Regis │ │Prof.Carga  │ │ │  │
│ │ │ └─────────┘ └──────────┘ └──────────┘ └────────────┘ │ │  │
│ │ │ ───────────────────────────────────────────────────── │ │  │
│ │ │ Pestudio: Educación Primaria [PR]                     │ │  │
│ │ │ ┌─────────┐ ┌──────────┐ ┌──────────┐ ┌────────────┐ │ │  │
│ │ │ │ 📋 320  │ │ 📝 890   │ │ 📊 650   │ │ 👨‍🏫 28     │ │ │  │
│ │ │ └─────────┘ └──────────┘ └──────────┘ └────────────┘ │ │  │
│ │ └───────────────────────────────────────────────────────┘ │  │
│ │                                                             │  │
│ │ ┌─ TAB 2: Profesores ────────────────────────────────────┐ │  │
│ │ │ ┌── Lapso 1 ──┬── Lapso 2 ──┬── Lapso 3 ────────────┐ │ │  │
│ │ │ │ Pestudio: MG  │ Pestudio: PR                       │ │ │  │
│ │ │ │ ┌─ Teacher DataTable ────────────────────────────┐ │ │ │  │
│ │ │ │ │ Profesor│ Activ│ PlanE│Notas│IEE│IEE-CN│IRE  │ │ │ │  │
│ │ │ │ │ M.Rodrí │ 12[85]│ 8    │ 450 │87%│ 92%  │104%│ │ │ │  │
│ │ │ │ │ J.Pérez │ 8[62%]│ 5    │ 210 │65%│ 71%  │ 89%│ │ │ │  │
│ │ │ │ └───────────────────────────────────────────────┘ │ │ │  │
│ │ │ └────────────────────────────────────────────────────┘ │ │  │
│ │ └────────────────────────────────────────────────────────┘ │  │
│ │                                                             │  │
│ │ ┌─ TAB 3: Actividades ───────────────────────────────────┐ │  │
│ │ │ ┌── Lapso 1 ──┬── Lapso 2 ──┬── Lapso 3 ────────────┐ │ │  │
│ │ │ │ Pestudio: MG  │ Pestudio: PR                       │ │ │  │
│ │ │ │ ┌─ 6 Indicator Boxes ────────────────────────────┐ │ │ │  │
│ │ │ │ │ Total: 890 │ Cobertura: 4.2 │ Particip: 82%  │ │ │ │  │
│ │ │ │ │ Seguim: 65%│ Aprobac: 78%   │ Supervis: 45%  │ │ │ │  │
│ │ │ │ └───────────────────────────────────────────────┘ │ │ │  │
│ │ │ └────────────────────────────────────────────────────┘ │ │  │
│ │ └────────────────────────────────────────────────────────┘ │  │
│ │                                                             │  │
│ │ ┌─ TAB 4: Planes de Evaluación ─────────────────────────┐ │  │
│ │ │ ┌─ Chart.js ────────────────────────────────────────┐ │ │  │
│ │ │ │ 📊 Evaluaciones registradas por fecha              │ │ │  │
│ │ │ └────────────────────────────────────────────────────┘ │ │  │
│ │ └────────────────────────────────────────────────────────┘ │  │
│ │                                                             │  │
│ └─────────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

### 5.2 Competition Indicators Layout

```
┌── Indicadores de Competición ───────────────────────────────────┐
│                                                                  │
│  Indicadores de la Competición "Debate Científico 2026"          │
│                                                                  │
│  ┌── Media General ─────────────────┐ ┌── Primaria ───────────┐ │
│  │ Grado │ Corr │ Err │ Punt │ ⚙   │ │ Grado │ Corr │ Err │ ⚙ │ │
│  │───────┼──────┼─────┼──────┼─────┤ │───────┼──────┼─────┼───┤ │
│  │ 1ro   │ 72%  │ 28% │ 345  │ 📊📈│ │ 4to   │ 85%  │ 15% │📊📈│ │
│  │ 2do   │ 65%  │ 35% │ 280  │ 📊📈│ │ 5to   │ 78%  │ 22% │📊📈│ │
│  │ 3ro   │ 80%  │ 20% │ 410  │ 📊📈│ │ 6to   │ 90%  │ 10% │📊📈│ │
│  └──────────────────────────────────┘ └────────────────────────┘  │
│                                                                  │
│  ┌── Modal Details (overlay) ──────────────────────────────┐    │
│  │ Grado: 1ro - Media General                               │    │
│  │ Correctas: 72% (345/480)    Erradas: 28% (135/480)      │    │
│  │ Preguntas: 120              Puntaje Máx: 480             │    │
│  │ ┌─ Por Categoría ─────────────────────────────────────┐ │    │
│  │ │ Biología: 85%  |  Física: 68%  |  Química: 72%     │ │    │
│  │ └─────────────────────────────────────────────────────┘ │    │
│  └─────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────┘
```

### 5.3 Estados de UI

| Estado | Main Dashboard | Competition Indicators |
|--------|---------------|----------------------|
| **Loading** | Spinner en tabs | Livewire loading state |
| **Empty — sin pestudios** | Sin pestudios → sin datos | N/A (competition hardcodeado a ID 1) |
| **Empty — sin profesores** | DataTable sin filas | N/A |
| **Sin lapso activo** | Primer lapso como default | N/A |
| **Tab Lecciones** | Contenido vacío | N/A |
| **Modal Details** | N/A | Overlay fullscreen semitransparente |
| **Modal Stats** | N/A | Modal flotante |

---

## 6. Árbol de Componentes

### 6.1 Jerarquía Planning Indicators (Blade)

```
HomePlanningController@indicators()
│
├── $user → Auth::user()
├── $autoridad → Autoridad::where('user_id', $user->id)->first()
├── $pestudios → Pestudio::all()
├── $peducativos → Peducativo::all()
├── $profesors → Profesor::getProfesorForLeaderId($user->id)
├── $estudiants → Estudiant::active()->WidthInscripcion()->get()
├── $lapsos → Lapso::all()
├── $lapso_active → Lapso::current()
└── $now → Carbon::now()

View: plannings.indicators
│
├── @extends: plannings.layouts.home.app
│
└── plannings.partials.index (63 líneas)
    ├── 5 tabs → tab-content
    │
    ├── TAB "Indicadores Principales"
    │   └── plannings.partials.estudiantil (69 líneas)
    │       └── @foreach($pestudios as $pestudio)
    │           ├── Pestudio header: name [code]
    │           ├── 4× @component('evaluacions.elements.boxes.indicators')
    │           └── <hr>
    │
    ├── TAB "Profesores"
    │   └── plannings.partials.seguimiento (51 líneas)
    │       └── @foreach($lapsos as $lapso)
    │           ├── Nav-tab: lapso->name + fechas
    │           └── @foreach($pestudios as $pestudio)
    │               ├── Nav-tab anidado: pestudio->name + ieePROM
    │               ├── $profesors = $pestudio->getProfesors()
    │               └── plannings.partials.profesors (123 líneas)
    │                   └── DataTable por profesor:
    │                       ├── fullname
    │                       ├── activities count + approval %
    │                       ├── pevaluacions count
    │                       ├── boletins count
    │                       ├── IEE %
    │                       ├── IEE-CN %
    │                       └── IRE %
    │
    ├── TAB "Actividades"
    │   └── plannings.partials.activities (52 líneas)
    │       └── @foreach($lapsos as $lapso)
    │           └── @foreach($pestudios as $pestudio)
    │               ├── $activities = $pestudio->getActivities($lapso->id)
    │               └── plannings.partials.activity (108 líneas)
    │                   ├── 6× @component('plannings.elements.boxes.indicators')
    │                   └── <hr>
    │
    ├── TAB "Planes de Evaluación"
    │   └── plannings.charts.evaluacions.actividades (Chart.js)
    │
    └── TAB "Lecciones" → vacío
```

### 6.2 Jerarquía Competition Indicators (Livewire)

```
CompetitionController@indicators()
│
└── View: plannings.competitions.indicators
    │
    └── livewire:planning.competition.debate-indicators
        │
        ├── mount($competitionId): carga DebateCompetition + peducativos
        │
        ├── Card: Indicadores de la Competición [name]
        │
        ├── @foreach($peducativos as $peducativo)
        │   ├── Card header: peducativo->name
        │   └── Table: Grados con:
        │       ├── grado->name
        │       ├── accuracy% (getAccuracyForGrado)
        │       ├── wrong% (getWrongAnswerForGrado)
        │       ├── totalScore (getTotalScoreForGrado)
        │       └── btn [Details] [Stats]
        │
        ├── [if $showDetails] → Modal Details (overlay fixed)
        │   └── livewire.planning.competition.modal.details
        │
        └── [if $showStats] → Modal Stats
            └── livewire.planning.competition.modal.stats
```

---

## 7. Plan de Migración (Fases)

### Fase 1 — API Layer

| # | Tarea | Endpoints | Dependencias |
|---|-------|-----------|--------------|
| 1.1 | Pestudio indicators endpoint | GET /indicators/main | Pestudio (inscritos, evaluacions, activities, profesores) |
| 1.2 | Teacher KPIs endpoint | GET /indicators/teachers | Pestudio::getProfesors(), Profesor traits |
| 1.3 | Activity indicators endpoint | GET /indicators/activities | Pestudio::getActivities() + fórmulas |
| 1.4 | Competition indicators endpoint | GET /competitions/{id}/indicators | IndicatorTrait en DebateCompetition |
| 1.5 | Competition grade details | GET /competitions/{id}/grado/{gradoId} | IndicatorTrait methods |
| 1.6 | Chart data endpoint | GET /indicators/chart/evaluations | EvaluacionController existente |

### Fase 2 — Frontend NextJS

| # | Tarea | Componentes | Notas |
|---|-------|-------------|-------|
| 2.1 | Dashboard Layout | `PlanningLayout` | Navbar + sidebar + tab system |
| 2.2 | Indicator Tabs | `MainTabs` | 5-tab navigation |
| 2.3 | Pestudio Cards | `PestudioCard`, `IndicatorBox` | 4 KPIs por pestudio |
| 2.4 | Teacher DataTable | `TeacherKPITable` | DataTable con sort por columna |
| 2.5 | Activity Indicators | `ActivityIndicatorsGrid` | 6 boxes con fórmulas |
| 2.6 | Chart Component | `EvaluationChart` | Chart.js o Recharts |
| 2.7 | Competition Dashboard | `CompetitionIndicators` | Por peducativo con tabla |
| 2.8 | Detail Modal | `GradoDetailModal` | Overlay fullscreen |
| 2.9 | Stats Modal | `GradoStatsModal` | Modal flotante |

### Fase 3 — Optimizaciones

| # | Tarea | Detalle |
|---|-------|---------|
| 3.1 | Eliminar tab Lecciones vacío | O implementar o eliminar |
| 3.2 | Cache de indicadores | Pestudio indicators no cambian frecuentemente |
| 3.3 | Reducir N+1 en queries de pestudio | Eager loading en Pestudio->getProfesors() |
| 3.4 | Paginación de profesores | DataTable actualmente carga todos |
| 3.5 | Unificar modelo de Pestudio | Traits Indicators existen en Profesor, Grado, Pestudio |

---

## 8. Edge Cases y Problemas Conocidos

### 8.1 Bugs Activos

| # | Bug | Lugar | Impacto | Solución |
|---|-----|-------|---------|----------|
| 1 | **Tab "Lecciones" vacío** | `partials/index.blade.php:46-47` | 20% de navegación inútil | Implementar o eliminar |
| 2 | **N+1 masivo en TAB 1** | `estudiantil.blade.php` — 4 métodos por pestudio | 4 queries × N pestudios | withCount + eager loading |
| 3 | **N+1 masivo en TAB 3** | `activity.blade.php` — 6 indicadores por pestudio×lapso | 6 queries × N pestudios × N lapsos | Refactor a service |
| 4 | **`$profesors` inyectado pero no usado en TAB 1** | Controller pasa `$profesors`; estudiantil no lo usa | Variable inútil en contexto | Eliminar |
| 5 | **$pestudios duplicado en compact()** | `HomePlanningController.php:50` — `'pestudios'` aparece 2 veces | PHP lo sobrescribe (no causa error) | Limpiar |
| 6 | **Competition hardcodeado a ID 1** | `competitions/indicators.blade.php` — `:competitionId="1"` | Siempre muestra la misma competición | Hacer dinámico |

### 8.2 Edge Cases

| # | Escenario | Comportamiento Actual | Riesgo |
|---|-----------|-----------------------|--------|
| 1 | Sin pestudios registrados | `$pestudios = collect()` → tabs sin contenido | UI vacía |
| 2 | Sin lapsos registrados | `$lapsos = collect()` → tabs sin nav | No se renderiza contenido |
| 3 | Pestudio sin profesores asignados | `getProfesors()` → `$profesors = collect()` | DataTable vacío |
| 4 | Pestudio sin actividades | `getActivities()` → `$activities = collect()` | Todos los indicadores = 0 |
| 5 | Profesor con IEE = 0 | División por cero evitada por checks ternarios | IEE = 0% |
| 6 | Competition sin datos de respuestas | `getAccuracyForGrado()` devuelve objeto con 0s | Tabla muestra 0% |
| 7 | Usuario sin autoridad asociada | `Autoridad::where('user_id')` → null | Seteo de variables falla |

### 8.3 Problemas de Performance

| # | Área | Queries por Pestudio | Riesgo |
|---|------|---------------------|--------|
| 1 | Tab "Indicadores Principales" | 4 queries (`inscritos`, `getEvaluacions`, `getActivities`, `getProfesorEvaluacions`) | Para 5 pestudios → 20 queries |
| 2 | Tab "Profesores" | 1 query por profesor × pestudio × lapso | Para 10 profesores × 3 lapsos × 2 pestudios → 60 queries |
| 3 | Tab "Actividades" | 6 indicadores + queries internas | Similar al anterior |

---

## 9. Checklist de Validación

### 9.1 Funcional
- [ ] 5 tabs se renderizan correctamente
- [ ] Indicadores principales muestran datos por pestudio
- [ ] Profesores DataTable con IEE, IEE-CN, IRE por lapso/pestudio
- [ ] Actividades: 6 indicadores se calculan correctamente
- [ ] Gráfico de evaluaciones Chart.js funcional
- [ ] Competition indicators: precisión, errores, puntaje por grado
- [ ] Modales de detalle/stats en competition funcionan
- [ ] DataTables inicializado correctamente (spanish.json)

### 9.2 Data
- [ ] N+1 queries optimizadas (especialmente en tabs 1, 2, 3)
- [ ] Cache implementado para indicadores agregados
- [ ] Pestudio indicators refactorizados a service layer

### 9.3 UI/UX
- [ ] Tab "Lecciones" implementado o eliminado
- [ ] Loading states en tabs con datos pesados
- [ ] Empty states para pestudios, profesores, actividades
- [ ] DataTables con paginación para muchos profesores
- [ ] Competition modales accesibles

### 9.4 Migración
- [ ] `$pestudios` duplicado en compact corregido
- [ ] Competition ID hardcodeado reemplazado por selector
- [ ] Variable `$profesors` no usada en TAB 1 eliminada

---

## 10. Dependencias y Acoplamiento

### 10.1 Dependencias del Dashboard

```
Planning Indicators
├── HomePlanningController
│   ├── UserDataInitializer trait → Auth, Autoridad, Pestudio, Peducativo
│   ├── Pestudio Model → inscritos(), getEvaluacions(), getActivities(), getProfesorEvaluacions()
│   ├── Profesor Model → getProfesorForLeaderId(), getProfesorIEE(), getProfesorIRE()
│   ├── Estudiant Model → active()->WidthInscripcion()
│   ├── Lapso Model → current(), all()
│   └── Pestudio traits → (ProfesorsIEEsPROM, getAvgActivitiesPerPlan, etc.)
│
├── Competition Indicators (Livewire)
│   ├── DebateCompetition Model → IndicatorTrait
│   ├── Peducativo Model → grados relationship
│   └── Grado Model
│
└── Shared Components
    ├── evaluacions.elements.boxes.indicators (usado por múltiples módulos)
    ├── plannings.elements.boxes.indicators
    └── plannings.elements.progress.bars.simple
```

### 10.2 Acoplamiento con Profesor Home Dashboard

| Aspecto | Planning Indicators | Profesor Home |
|---------|-------------------|---------------|
| **Scope** | Institucional (todos los profesores/pestudios) | Individual (un profesor) |
| **Controller** | HomePlanningController | HomeController (Profesor) |
| **Livewire** | Sub-módulo Competition | ❌ |
| **KPIs compartidos** | IEE, IEE-CN, IRE (Idénticos) | IEE, IEE-CN, IRE |
| **Traits usados** | Profesor::Indicators, Pestudio methods | Profesor::Indicators |
| **N+1** | Severo (múltiples pestudios × lapsos) | Moderado (un profesor) |
| **Tab Lecciones** | Vacío | N/A |

---

## 11. Comparativa con Módulos Relacionados

| Aspecto | Planning Indicators (este) | Profesor Home | Profesor Dashboard |
|---------|---------------------------|---------------|-------------------|
| **Propósito** | KPIs institucionales | KPIs individuales |
| **Livewire** | Parcial (Competition) | ❌ |
| **N tabs** | 5 (1 vacío) | 1 (con tabs por lapso) |
| **DataTables** | ✅ Profesores | ❌ |
| **Chart.js** | ✅ Planes Evaluación | ✅ Actividades |
| **Inscritos** | ✅ Por pestudio | ❌ |
| **Profesores individuall** | ✅ DataTable con KPIs | ❌ (solo el prof actual) |
| **Competition** | ✅ Livewire con modales | ❌ |
| **Vistas legacy** | 1 tab vacío | 10+ vistas legacy |
| **N+1 Severidad** | **ALTA** (N pestudios × N lapsos) | **MODERADA** (1 profesor) |

---

## 12. Hallazgos y Recomendaciones

### 12.1 Hallazgos Críticos

| # | Hallazgo | Impacto | Acción Requerida |
|---|----------|---------|------------------|
| H1 | **Tab Lecciones vacío** — sin implementar | 20% de navegación del tab principal | Implementar o eliminar |
| H2 | **N+1 masivo** en los 3 tabs principales — cada pestudio ejecuta queries independientes | Para 5 pestudios × 3 lapsos → ~60+ queries | Crear DashboardService con queries agregadas |
| H3 | **Variable `$pestudios` duplicada** en compact() | No causa error pero es código sucio | Limpiar |
| H4 | **Competition ID hardcodeado a 1** | Siempre muestra la misma competición | Hacer selector dinámico |
| H5 | **Lógica de indicadores en traits de modelo** — mezclada con lógica de negocio | Violación SRP, difícil de testear | Mover a service layer |
| H6 | **Indicadores de Actividad calculados en view** (activity.blade.php) — colecciones filtradas inline | Lógica en template, no testeable | Mover a service |

### 12.2 Recomendaciones de Arquitectura (NextJS)

1. **Dashboard Service Layer**: Crear `PlanningIndicatorService` con métodos agregados que ejecuten queries optimizadas (withCount, JOINs agregados) en lugar de métodos por pestudio.

2. **API por Tab**: Cada tab debe ser un endpoint independiente para lazy loading:
   - Tab 1: `GET /api/planning/indicators/main`
   - Tab 2: `GET /api/planning/indicators/teachers?lapsoId=&pestudioId=`
   - Tab 3: `GET /api/planning/indicators/activities?lapsoId=&pestudioId=`

3. **React Query Cache**: Los indicadores agregados cambian poco — configurar staleTime de 5-15 minutos.

4. **Reutilizar IndicatorBox**: El componente `IndicatorBox` (shared entre evaluacions, plannings, leaders, academicos, controls) debe ser un componente base reutilizable en NextJS.

5. **Eliminar Tab Lecciones**: Si no hay plan de implementarlo, eliminarlo del layout para no confundir usuarios.

6. **Competition Selector Dinámico**: El `:competitionId="1"` hardcodeado debe reemplazarse con un selector de competición (dropdown o ruta dinámica).

---

> **Documentación generada:** 2026-06-06
> **Módulos relacionados:** [gestion-home.md](gestion-home.md), [gestion-debates.md](gestion-debates.md), [gestion-competencias.md](gestion-competencias.md)
> **Ver también:** [RETROSPECTIVE.md](../RETROSPECTIVE.md) §4 (dependency graph)
