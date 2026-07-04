# Blueprint: Gestión de Diagnósticos (Módulo Profesor)

> **Módulo:** Profesor > Diagnósticos
> **Archivo Fuente:** `app/Http/Livewire/Profesor/Diagnostics/IndexComponent.php` (~1756 líneas)
> **Vistas:** `resources/views/livewire/profesor/diagnostics/*.blade.php` (1 principal + 7 partials)
> **Blueprint relacionado:** `blueprint/planning/gestion-diagnostics.md` (planning/shared)
> **Prioridad:** P3 (independiente, bajo acoplamiento)

---

## 0. Resumen Ejecutivo

El módulo de Diagnósticos del Profesor es un **Livewire component standalone** de 1756 líneas que permite al docente crear, gestionar y analizar evaluaciones diagnósticas para sus estudiantes. Es un componente completamente diferente al `livewire:evaluacion.diagnostic.index-component` documentado en `blueprint/planning/gestion-diagnostics.md` — aunque comparten las traits de IA (Gemini, DeepSeek, Qwen, OpenRouter).

**Arquitectura:** Controlador thin (solo `index()`) → Livewire component pesado con 4 tabs (dashboard, questions, sessions, analytics) → 7 partials Blade → 16 modelos de dominio `App\Models\app\Instrument`.

**Hallazgo crítico:** Es un módulo HUBS (pivote) que conecta 3 subsistemas — Instrumentos (diagnósticos), Pensums (asignaturas del profesor), y Estudiantes (sesiones). Cualquier cambio en el schema de `pensums`, `estudiants`, o `diag_*` tables puede romper este componente.

---

## 1. Validación contra Código Fuente

### 1.1 Routes

**Archivo:** `routes/app/tab/profesors/diagnostics.php`

| Método | URI | Action | Name | Estado |
|--------|-----|--------|------|--------|
| GET | `/diagnostics/index` | `DiagnosticController@index` | `profesors.diagnostics.index` | ✅ Funcional |

**Hallazgo:** Una sola ruta GET. **Toda la lógica** (CRUD, filtros, análisis, reportes) es vía Livewire AJAX — no hay rutas API ni endpoints REST para operaciones individuales. Esto significa migración completa a Livewire o reemplazo total.

### 1.2 Controller

**Archivo:** `app/Http/Controllers/Profesor/Tab/DiagnosticController.php`

```php
class DiagnosticController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id', Auth::id())->first();
            return $next($request);
        });
    }

    public function index()
    {
        return view('profesors.diagnostics.index', ['profesor' => $this->profesor]);
    }
}
```

**Hallazgo:** Controller ultra-thin (18 líneas). Toda la lógica de negocio está en el Livewire component. La migración a NextJS requiere reemplazar Livewire por estado React + API calls.

### 1.3 Livewire Component (Resumen Estructural)

**Archivo:** `app/Http/Livewire/Profesor/Diagnostics/IndexComponent.php`

| Sección | Líneas | Propósito |
|---------|--------|-----------|
| Properties | ~1-100 | 30+ propiedades reactivas |
| `mount()` | ~100-130 | Inicialización, cache keys, listas |
| `render()` | ~130-180 | Returns view con 7+ datasets paginados |
| `getStatsProperty()` | ~180-250 | Dashboard KPIs cacheados |
| `getSessionsPaginated()` | ~250-350 | Query complejo con agrupación por estudiante |
| `getStudentAccuracyStats()` | ~350-400 | Precisión múltiple choice |
| Tab switching | ~400-450 | `setActiveTab()`, `resetFilters()` |
| Question CRUD | ~450-750 | Wizard 3 pasos, validación por paso |
| `saveQuestion()` | ~750-850 | Transactional con manejo de DiagOption |
| `deleteQuestion()` | ~850-900 | Validación de dependencias |
| Student details | ~900-1000 | `showStudentDetails()`, `showStudentSessions()` |
| AI Report | ~1100-1756 | `getAIReport()`, `viewReport()`, deprecated `_deprecated_generateAIReport()` |
| Report data | ~1528-1606 | Payload assembly (11 secciones) |
| Report filtering | ~1694-1719 | `viewReport()` con filtro por pensumIds del profesor |
| Loading state | disperso | `startLoading()/stopLoading()` con flag booleano + dispatch |

### 1.4 AI Report Traits (Compartidas con Planning)

| Trait | Servicio | Namespace |
|-------|----------|-----------|
| `QwenReportTrait` | Qwen (Qwen) | `App\Http\Livewire\Evaluacion\Diagnostic\` |
| `DeepSeekReportTrait` | DeepSeek | `App\Http\Livewire\Evaluacion\Diagnostic\` |
| `GeminiReportTrait` | Gemini | `App\Http\Livewire\Evaluacion\Diagnostic\` |
| `OpenRouterReportTrait` | OpenRouter | `App\Http\Livewire\Evaluacion\Diagnostic\` |

**Hallazgo:** Las 4 traits de IA son las MISMAS que usa el módulo Planning. Cualquier cambio en ellas afecta ambos módulos. El profesor selecciona el servicio via `$this->selected_ai_service`.

---

## 2. Reglas de Negocio

### 2.1 Scope de Datos

- **Pensum Filtering (rígido):** Todas las queries se filtran por `whereIn('pensum_id', $this->pensumIds)` donde `$pensumIds` se obtiene de `$this->profesor?->pensums?->pluck('id')->toArray()`. El profesor **solo ve datos de sus áreas asignadas**.
- **No hay override:** No existe flag "ver todo" en queries de datos — solo el selector de UI `selectedPensumId` es un subfiltro adicional.
- **Cache de stats:** `Cache::remember('stats_{userId}_{pensumId}_{filtros}', 1800, ...)` — TTL 30 minutos.

### 2.2 Wizard de Creación de Preguntas

```
Paso 1: Seleccionar Área de Formación (pensum_id) + Tipo de Pregunta (multiple|open|scale)
    ↓ Validación: ambós requeridos
Paso 2: Texto de pregunta + Opciones (si multiple) o configuración de escala
    ↓ Validación: texto >= 10 chars, opciones >= 2 (si multiple)
Paso 3: Orden + Ponderación (1-5) + Dificultad (easy|medium|hard) + Activo
    ↓ Save: DB::transaction con DiagOption upsert
```

### 2.3 Protección de Eliminación

- `deleteQuestion()` bloquea eliminación si `DiagAnswer::where('question_id', $questionId)->exists()` — protege integridad de respuestas existentes.

### 2.4 AI Report — Data Assembly

El deprecated `_deprecated_generateAIReport()` construye un payload de 11 secciones:

| Sección | Contenido |
|---------|-----------|
| `institucion` | Nombre, dirección, director, coordinador |
| `estudiante` | ID, cédula, nombre, fecha nac., edad, sexo |
| `grado` | Nombre, sección, turno, tutor |
| `lapso_diagnostico` | Lapso actual, año escolar |
| `instrumento_aplicado` | Metadatos del diagnóstico |
| `sesiones` | Totales, completadas, incompletas con detalle |
| `resultados_globales` | Precisión, nivel cualitativo |
| `areas_evaluadas` | Por sesión: preguntas, fortalezas, necesidades |
| `contrastes_curriculares` | Gap analysis contra competencias esperadas |
| `referente_normativo` | Resolución, versión |
| `referente_curricular` | Competencias e indicadores por grado/pensum |

**Hallazgo:** El método está marcado como `_deprecated_` (prefijo underscore) pero **aún contiene toda la lógica completa**. El método activo es `getAIReport()` que solo busca un reporte existente y lo muestra — la generación delegada a un job/process externo o a las traits directamente.

### 2.5 Niveles Cualitativos (Duplicados en 3 lugares)

```
Global:    precision >= 90 → Advanced | >= 70 → Proficient | >= 50 → Developing | < 50 → Emergent
Por área:  precision >= 90 → Advanced | >= 70 → Proficient | >= 50 → Developing | < 50 → Emergent
Contraste: precision >= 90 → Outstanding | >= 70 → Satisfactory | >= 50 → Developing | < 50 → Insufficient
```

**Bug:** En contraste curricular se usa nomenclatura diferente (Outstanding/Satisfactory/Developing/Insufficient) vs las otras dos (Advanced/Proficient/Developing/Emergent). Misma lógica, labels distintos.

### 2.6 Filtros de Reporte en `viewReport()`

```php
// Línea crítica: Extrae pensum_id del area['id'] como substring
$pensumId = substr($area['id'], 5); // "SUBJ-{id}" → quita "SUBJ-"
```

**Bug potencial:** `substr($area['id'], 5)` asume formato fijo `SUBJ-{id}`. Si el formato cambia o `id` tiene otra estructura, el filtrado falla silenciosamente.

---

## 3. SQL Schema (Modelos de Dominio)

### 3.1 `diag_mains` — Diagnósticos (Catálogo)

| Columna | Tipo | Notas |
|---------|------|-------|
| id | bigint AI | |
| name | varchar(255) | Nombre del diagnóstico |
| description | text | nullable |
| token | varchar(100) | Identificador de acceso |
| active | tinyint(1) | ENUM('true','false') — ver hallazgo |
| referent_id | bigint FK → diag_referents | |
| lapso_id | bigint FK → lapsos | |
| pestudio_id | bigint FK → pestudios | |
| timestamps | | |

### 3.2 `diag_questions` — Preguntas

| Columna | Tipo | Notas |
|---------|------|-------|
| id | bigint AI | |
| pensum_id | bigint FK → pensums | Scope del profesor |
| pregunta | text | Mínimo 10 chars en validación |
| tipo_pregunta | varchar(20) | multiple|open|scale |
| orden | int | nullable |
| difficulty | varchar(20) | easy|medium|hard |
| weighing | int | 1-5 |
| activo | tinyint(1) | ENUM('true','false') |
| diag_main_id | bigint FK → diag_mains | |
| competency_id | bigint FK → diag_competencies | nullable |
| indicator_id | bigint FK → diag_indicators | nullable |
| timestamps | | |

### 3.3 `diag_options` — Opciones de Respuesta

| Columna | Tipo | Notas |
|---------|------|-------|
| id | bigint AI | |
| question_id | bigint FK → diag_questions | |
| opcion | text | Texto de la opción |
| valor | int | 1 = correcta, 0 = incorrecta |

### 3.4 `diag_sessions` — Sesiones de Diagnóstico

| Columna | Tipo | Notas |
|---------|------|-------|
| id | bigint AI | |
| estudiant_id | bigint FK → estudiants | |
| pensum_id | bigint FK → pensums | |
| iniciado_at | datetime | |
| completado_at | datetime | nullable |
| progreso | int | 0-100 |
| total_preguntas | int | |
| activo | tinyint(1) | boolean (ENUM 'true'/'false') |
| diag_main_id | bigint FK → diag_mains | |
| lapso_id | bigint FK → lapsos | |
| status | varchar(50) | nullable |

### 3.5 `diag_answers` — Respuestas

| Columna | Tipo | Notas |
|---------|------|-------|
| id | bigint AI | |
| estudiant_id | bigint FK → estudiants | |
| question_id | bigint FK → diag_questions | |
| option_id | bigint FK → diag_options | nullable (solo multiple) |
| session_id | bigint FK → diag_sessions | |
| respuesta | text | nullable (open/scale) |
| valor_numerico | int | nullable |
| completado_at | datetime | nullable |

### 3.6 Modelos Adicionales (11)

| Modelo | Tabla | Propósito |
|--------|-------|-----------|
| `DiagReport` | `diag_reports` | Reporte generado por estudiante+diagMain |
| `DiagResult` | `diag_results` | Resultados por sesión |
| `DiagReportPensum` | `diag_report_pensums` | Resultados por pensum dentro del reporte |
| `DiagReportIndicatorResult` | `diag_report_indicator_results` | Resultados por indicador |
| `DiagReportAiDraft` | `diag_report_ai_drafts` | Draft del LLM (input_hash, output_text, llm_model) |
| `DiagReportAuditLog` | `diag_report_audit_logs` | Auditoría de generación |
| `DiagRecommendation` | `diag_recommendations` | Recomendaciones pedagógicas |
| `DiagReferent` | `diag_referents` | Referente normativo (active, name, version, code) |
| `DiagCompetency` | `diag_competencies` | Competencias curriculares |
| `DiagIndicator` | `diag_indicators` | Indicadores de logro (code, description, expected_level) |
| `SectionDiagnosticReport` | `section_diagnostic_reports` | Reportes por sección (planning module) |

**Hallazgo:** El modelo `SectionDiagnosticReport` pertenece al módulo Planning, pero está en el mismo namespace `Instrument`. Esto crea acoplamiento — el profesor module podría accederlo accidentalmente.

---

## 4. Endpoints API (Migración NextJS Propuesta)

### 4.1 Endpoints Requeridos

| Método | Endpoint | Propósito | Reemplaza |
|--------|----------|-----------|-----------|
| GET | `/api/profesor/diagnostics` | Listar diagnósticos del profesor | `$diagMains` |
| GET | `/api/profesor/diagnostics/stats` | Dashboard KPIs cacheados | `getStatsProperty()` |
| GET | `/api/profesor/diagnostics/questions` | Listar preguntas (paginated, filtered) | Tabla questions |
| POST | `/api/profesor/diagnostics/questions` | Crear pregunta (wizard step 1-3) | `saveQuestion()` |
| PUT | `/api/profesor/diagnostics/questions/{id}` | Actualizar pregunta | `saveQuestion()` (editing) |
| DELETE | `/api/profesor/diagnostics/questions/{id}` | Eliminar pregunta (si no tiene answers) | `deleteQuestion()` |
| GET | `/api/profesor/diagnostics/sessions` | Listar sesiones agrupadas por estudiante | `getSessionsPaginated()` |
| GET | `/api/profesor/diagnostics/sessions/{estudiantId}` | Detalle de sesiones del estudiante | `showStudentSessions()` |
| GET | `/api/profesor/diagnostics/students/{id}` | Perfil del estudiante con stats | `showStudentDetails()` |
| GET | `/api/profesor/diagnostics/reports/{estudiantId}/{diagMainId}` | Obtener reporte AI | `getAIReport()` |
| POST | `/api/profesor/diagnostics/reports/generate` | Generar reporte AI (async job) | `_deprecated_generateAIReport()` |
| GET | `/api/profesor/diagnostics/analytics` | Analytics data | Tabla analytics |
| GET | `/api/profesor/diagnostics/filters` | Obtener opciones de filtro | Grados, secciones, subjects |

### 4.2 Queries Críticas (Deben Migrarse a SQL/API)

```sql
-- Dashboard stats (actualmente en getStatsProperty())
SELECT 
    COUNT(DISTINCT dq.id) as total_questions,
    COUNT(DISTINCT ds.id) as total_sessions,
    COUNT(DISTINCT CASE WHEN ds.completado_at IS NOT NULL THEN ds.id END) as completed_sessions,
    COUNT(DISTINCT CASE WHEN ds.activo = 1 THEN ds.id END) as active_sessions
FROM diag_questions dq
LEFT JOIN diag_sessions ds ON ds.pensum_id = dq.pensum_id
WHERE dq.pensum_id IN ($pensumIds) AND dq.activo = 1;

-- Sessions grouped by student (actualmente en getSessionsPaginated())
SELECT 
    estudiant_id,
    COUNT(*) as total_sessions,
    COUNT(CASE WHEN completado_at IS NOT NULL THEN 1 END) as completed_sessions,
    AVG(TIMESTAMPDIFF(MINUTE, iniciado_at, COALESCE(completado_at, NOW()))) as avg_duration_minutes,
    MAX(iniciado_at) as last_session_date
FROM diag_sessions
WHERE pensum_id IN ($pensumIds)
GROUP BY estudiant_id
ORDER BY last_session_date DESC;
```

### 4.3 Cache Strategy (Migración)

```
Actual:     Cache::remember('stats_{userId}_{pensumId}_{filters}', 1800, ...)
Propuesta:  React Query con staleTime: 1800000 (30 min) + invalidación on mutate
           O Redis con TTL 1800s y keys namespaced por userId
```

---

## 5. UI Wireframes

### 5.1 Layout General

```
┌──────────────────────────────────────────────────────────────┐
│  Header: Nombre del Profesor                                 │
│  [Área de Formación: dropdown ▼]              [Nueva Pregunta]│
├──────────────────────────────────────────────────────────────┤
│  ┌───────────────────── 3-Column Filter Bar ────────────────┐│
│  │ [Diagnóstico ▼] x    [Grado ▼] x    [Sección ▼] x       ││
│  └──────────────────────────────────────────────────────────┘│
├──────────────────────────────────────────────────────────────┤
│  [Dashboard]  [Preguntas]  [Sesiones]  [Análisis]           │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│  ── Tab Content (one of 4) ──                                │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

### 5.2 Tab Dashboard

```
┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐
│Total    │ │Total    │ │Sesiones │ │Precisión│
│Preguntas│ │Sesiones │ │Completa-│ │Estudian-│
│   42    │ │  128    │ │das   89 │ │ 73.5%   │
└─────────┘ └─────────┘ └─────────┘ └─────────┘

┌── Progreso ────────────────────────────────────┐
│ Sesiones Completadas  ████████░░ 89/128        │
│ Sesiones Activas      ██░░░░░░░░ 23            │
│ Precisión Respuestas  ███████░░░ 73.5%         │
└────────────────────────────────────────────────┘

┌─ Preguntas Recientes ─┐ ┌─ Sesiones Recientes ───┐
│ [Multiple] ¿Qué es... │ │ [✅] María García       │
│ [Abierta] Explica...  │ │ [⏳] Juan Pérez         │
│ [Múltiple] ¿Cuál...   │ │ [❌] Ana Rodríguez      │
│ Ver todas →           │ │ Ver todas →            │
└───────────────────────┘ └────────────────────────┘
```

### 5.3 Tab Preguntas

```
┌─────────────── Filtros ──────────────────────────────────┐
│ [Buscar...🔍] [Tipo ▼] [Área de Formación ▼] [🔄]     │
└──────────────────────────────────────────────────────────┘

┌── Tabla de Preguntas ─────────────────────────────────────┐
│ Pregunta           │ Tipo     │ Área        │ Fecha     │ ⚙│
│────────────────────┼──────────┼─────────────┼───────────┼──│
│ ¿Qué es la fotosí… │ Múltiple │ Ciencias    │ 15/05/2026│ ✏🗑│
│ Explica el ciclo…  │ Abierta  │ Biología    │ 14/05/2026│ ✏🗑│
│ ...                │          │             │           │   │
└──────────────────────────────────────────────────────────┘
         « 1  2  3  ... 10 »    Mostrando 1-10 de 42
```

### 5.4 Tab Sesiones (Agrupado por Estudiante)

```
┌── Sesiones ─────────────────────────────────────────────┐
│ [Rango fechas: 7d ▼]                                    │
├─────────────────────────────────────────────────────────┤
│ Estudiante      │ Grados  │ Últ. Activ  │ Estado │ ⚙   │
│─────────────────┼─────────┼─────────────┼────────┼─────┤
│ María García    │ 1° A    │ 20/05 14:30 │✅ Comp│ 👁📋📄│
│ Juan Pérez      │ 1° A    │ 19/05 10:15 │⏳ Prog│ 👁📋  │
│ ...             │         │             │        │     │
└─────────────────────────────────────────────────────────┘
         « 1  2  ... 5 »    Mostrando 1-15 de 68

┌─ Stats ─┐ ┌─ Tasa Finalización ┐ ┌─ Tiempo Promedio ┐
│ 128     │ │ 69.5%              │ │ 45 min           │
│ Total   │ │                    │ │                   │
└─────────┘ └────────────────────┘ └───────────────────┘
```

### 5.5 Modal Detalle de Estudiante (3 secciones)

```
┌── Perfil Detallado del Estudiante ──────────────────────┐
│ ┌─ Info Estudiante ──┐ ┌─ Progreso General ┐          │
│ │ María García       │ │ ████████░░ 80%    │          │
│ │ CI: 32.456.789     │ │ 32/40 preguntas   │          │
│ └────────────────────┘ └───────────────────┘          │
│ ┌─ Stats ───────────────────────────────────────────┐│
│ │ Sesiones: 8 │ Completadas: 6 │ Tiempo: 32min │ ✅││
│ └───────────────────────────────────────────────────┘│
│ ┌─ Progreso por Área ───────────────────────────────┐│
│ │ Ciencias:      ████████░░ 80%  ████ 4/5 sesiones ││
│ │ Matemática:    ██████░░░░ 60%  ██   2/3 sesiones ││
│ │ Lengua:        ████░░░░░░ 40%  █    1/2 sesiones ││
│ │ ...                                                ││
│ └───────────────────────────────────────────────────┘│
│ ┌─ Historial ───────────────────────────────────────┐│
│ │ Primera: 10/05  Última: 20/05  Tasa: 75%        ││
│ └───────────────────────────────────────────────────┘│
└──────────────────────────────────────────────────────┘
```

### 5.6 Modal Reporte AI (7 secciones)

```
┌── INFORME DIAGNÓSTICO INTEGRAL ────────────────────────┐
│                                                        │
│ ┌─ 1. Identificación ──────────────────────────────┐  │
│ │ Nombre: María García | CI: 32.456.789            │  │
│ │ Grado: 1° A | Sección: A | Referente: EMG-2017  │  │
│ └──────────────────────────────────────────────────┘  │
│ ┌─ 2. Contexto ────────────────────────────────────┐  │
│ │ Instrumento: Diagnóstico Inicial 2025-2026       │  │
│ └──────────────────────────────────────────────────┘  │
│ ┌─ 3. Resultados Globales ─────────────────────────┐  │
│ │ ████████████████████████████████░ 73.5%          │  │
│ │ Análisis: "El estudiante muestra..."             │  │
│ └──────────────────────────────────────────────────┘  │
│ ┌─ 4. Análisis por Área ──────────────────────────┐  │
│ │ Ciencias:    🟢 Alto    ... Fortalezas/Necesid.  │  │
│ │ Matemática:  🟡 Medio   ...                    │  │
│ │ Lengua:      🔴 Bajo    ...                    │  │
│ └──────────────────────────────────────────────────┘  │
│ ┌─ 5. Contraste Curricular ───────────────────────┐  │
│ │ Brechas identificadas vs competencias esperadas  │  │
│ └──────────────────────────────────────────────────┘  │
│ ┌─ 6. Perfil Diagnóstico ─────────────────────────┐  │
│ │ Estilo: Racionalista | Aprendizaje: Visual      │  │
│ │ Fortalezas: ... | Necesidades: ...              │  │
│ │ Barreras: ... | Factores Actitudinales: ...     │  │
│ └──────────────────────────────────────────────────┘  │
│ ┌─ 7. Recomendaciones ────────────────────────────┐  │
│ │ 🔴 Alta: Reforzar comprensión lectora           │  │
│ │ 🟡 Media: Práctica adicional en ...             │  │
│ │ 🟢 Baja: Consolidar mediante ejercicios...      │  │
│ └──────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────┘
```

### 5.7 Estados de UI

| Estado | Dashboard | Preguntas | Sesiones | Analytics |
|--------|-----------|-----------|----------|-----------|
| **Loading** | Skeleton cards | Spinner en tabla | Spinner en tabla | Skeleton charts |
| **Empty (sin datos)** | "No hay preguntas" CTA buttons | "No se encontraron preguntas" + Create | "No hay estudiantes disponibles" | "No hay datos para analizar" + Create |
| **Error** | dispatchBrowserEvent('swal', icon:'error') | Misma lógica via SweetAlert | Error row en tabla | N/A |
| **Filtered (sin match)** | N/A | "Intenta ajustar los filtros" + Reset | N/A | N/A |
| **Loading overlay** | `wire:loading.flex` full-screen with spinner (z-index: 9999) | | | |

---

## 6. Árbol de Componentes

### 6.1 Livewire Component Hierarchy

```
IndexComponent (1756 líneas)
├── setActiveTab('dashboard')
│   └── partials/dashboard.blade.php (367 líneas)
│       ├── 4 KPI Cards (total_questions, total_sessions, completed, accuracy)
│       ├── Progress Overview (barra completadas, activas, precisión)
│       ├── Recent Questions (take(5))
│       └── Recent Sessions (take(5))
│
├── setActiveTab('questions')
│   └── partials/questions.blade.php (491 líneas)
│       ├── Search + Filter bar (search, filterType, filterSubject, reset)
│       ├── Results Summary
│       ├── Questions Table (paginated 10)
│       │   ├── Row: pregunta + tipo + área + dificultad + fecha + acciones
│       │   └── Empty state + Error state
│       ├── Pagination (10/page, full Livewire)
│       └── └── question-modal.blade.php (522 líneas)
│               ├── Wizard Step 1: pensum + tipo
│               ├── Wizard Step 2: texto + opciones/escala
│               ├── Wizard Step 3: orden + ponderación + dificultad + preview
│               └── Save/Cancel footer
│
├── setActiveTab('sessions')
│   └── partials/sessions.blade.php (925 líneas)
│       ├── Date Range filter
│       ├── Sessions Table grouped by student (paginated 15)
│       │   ├── Row: estudiante + grados + última actividad + tiempo + estado + acciones
│       │   └── Actions: 👁 details, 📋 sessions, 📄 AI report
│       ├── Session Stats (3 cards)
│       ├── └── session-modal.blade.php (415 líneas)
│       │       ├── Student Info card
│       │       ├── Session Info card
│       │       ├── Progress section
│       │       └── Answers table
│       └── [Student Details Modal] (inline en sessions.blade.php)
│           ├── Info + Progress
│           ├── Stats (4 cards)
│           ├── Progress by Pensum
│           └── Activity History
│
├── setActiveTab('analytics')
│   └── partials/analytics.blade.php (619 líneas)
│       ├── Performance Chart by Pensum (progress bars)
│       ├── Question Type Distribution (multiple/open/scale)
│       ├── Detailed Table por Área (8 columns)
│       ├── Performance Insights (2 rows of 3 cards)
│       └── Precision Insights (3 cards)
│
└── [AI Report Modal]
    └── partials/ai-report-modal.blade.php (709 líneas)
        ├── 7 sections + watermark + footer
        └── Chart.js data preparation (inline PHP)
```

### 6.2 Server Functions Called from Views

| Función | Propósito | Ubicación |
|---------|-----------|-----------|
| `number_format()` | Format KPI numbers | Dashboard, tablas |
| `Str::limit()` | Truncate text | Preguntas, sesiones |
| `Carbon::parse()->format()` | Date formatting | Tablas, modales |
| `markdown_to_bootstrap()` | Render AI markdown | AI Report modal |
| `chr(65 + $index)` | Option letter (A, B, C...) | Question wizard |
| `now()->diffForHumans()` | Relative time | Dashboard |

**Hallazgo:** `markdown_to_bootstrap()` es una helper function personalizada (no Laravel core) — debe ser migrada o reemplazada en NextJS.

---

## 7. Plan de Migración (Fases)

### Fase 1 — API Layer (Backend Laravel)

| # | Tarea | Endpoints | Dependencias |
|---|-------|-----------|--------------|
| 1.1 | Crear `DiagnosticController` API | CRUD questions | Modelos Instrument |
| 1.2 | Crear `DiagnosticSessionController` | Sessions + stats | Modelos + Pagination |
| 1.3 | Crear `DiagnosticReportController` | Reports + AI generate | AI Traits + Jobs |
| 1.4 | Crear `DiagnosticAnalyticsController` | Analytics endpoints | Modelos + Aggregates |
| 1.5 | Cache layer con Redis keys namespaced | GET endpoints | Cache config |

### Fase 2 — Frontend Core (NextJS)

| # | Tarea | Componentes | Notas |
|---|-------|-------------|-------|
| 2.1 | Layout + Tabs | `DiagnosticsLayout`, `TabNav` | `nav-fill` + purple theme |
| 2.2 | Dashboard Tab | `DashboardTab` con KPI cards | React Query con cache |
| 2.3 | Questions Tab | `QuestionsTab` + `QuestionWizard` | Wizard 3 pasos como Stepper |
| 2.4 | Sessions Tab | `SessionsTab` + `SessionDetailModal` | Agrupación por estudiante |
| 2.5 | Analytics Tab | `AnalyticsTab` con charts | Recharts/Chart.js |
| 2.6 | AI Report Modal | `AIReportModal` con 7 secciones | markdown rendering |

### Fase 3 — AI Reports (Migración Async)

| # | Tarea | Detalle |
|---|-------|---------|
| 3.1 | Job queue para generación de reportes | Reemplazar `set_time_limit(300)` |
| 3.2 | WebSocket/SSE para progreso | Notificar al frontend cuando el reporte esté listo |
| 3.3 | Migrar traits a servicios standalone | `app/Services/LLM/` |

### Fase 4 — Limpieza

| # | Tarea | Detalle |
|---|-------|---------|
| 4.1 | Eliminar Livewire component | Después de validar feature parity |
| 4.2 | Eliminar controller thin | Quedan endpoints API |
| 4.3 | Eliminar partials Blade | Reemplazados por componentes React |
| 4.4 | Eliminar `_deprecated_generateAIReport()` | Migrado a job |

---

## 8. Edge Cases y Problemas Conocidos

### 8.1 Bugs Activos

| # | Bug | Lugar | Impacto | Solución Propuesta |
|---|-----|-------|---------|--------------------|
| 1 | `substr($area['id'], 5)` asume formato fijo `SUBJ-{id}` | `viewReport()` L1698 | Filtrado incorrecto si cambia formato ID | Usar `str_replace('SUBJ-', '', $area['id'])` o almacenar pensum_id explícito |
| 2 | Niveles cualitativos inconsistentes (Advanced vs Outstanding) | `_deprecated_generateAIReport()` L1262-1514 | Reportes con nomenclatura inconsistente | Unificar a un solo enum |
| 3 | `_deprecated_generateAIReport()` usa `set_time_limit(300)` | L1153 | No funciona en PHP modo seguro | Migrar a job de cola |
| 4 | N+1 query en sessions: carga `estudiant->name` por cada fila | `getSessionsPaginated()` | Rendimiento en listas grandes | Eager load o select específico |
| 5 | `ENUM('true','false')` como booleano | Varios modelos | Inconsistencia con NextJS booleano | Migrar a `boolean` |

### 8.2 Edge Cases

| # | Escenario | Comportamiento Actual | Riesgo |
|---|-----------|-----------------------|--------|
| 1 | Profesor sin pensums asignados | `$pensumIds = []` → queries devuelven 0 resultados | Todo el módulo vacío, UI muestra "no hay datos" |
| 2 | Profesor con 20+ pensums | Paginación funciona, pero analytics (client-side) procesa todo en PHP | Puede ser lento en migración |
| 3 | Estudiante sin inscripción | `$student->inscripcion?->seccion?->grado?->name` falla con `?->` | Null safety por optional chaining |
| 4 | Reporte AI con JSON inválido | `json_decode($draftRaw, true)` → null → `$draft = []` → reporte vacío | Degradación silenciosa |
| 5 | Modal de AI report sin latestDraft | `$selectedReport->latestDraft->output_text ?? '{}'` | Cae a default vacío |
| 6 | Dos profesores mismo pensum | Ambos ven los mismos estudiantes y sesiones | Exposición de datos entre profesores |
| 7 | Session detail modal con 500+ respuestas | Renderiza todas en un solo modal con scroll | Performance del modal (scroll de 80vh) |
| 8 | `ENUM('true','false')` → filtro SQL | `where('activo', 'true')` vs `where('activo', 1)` | Migración a boolean requiere refactor |

### 8.3 Cache Invalidation

El cache de stats tiene TTL de 1800 segundos. No se invalida al crear/actualizar preguntas o al completar sesiones. Esto significa:
- Dashboard puede mostrar datos desactualizados por hasta 30 minutos
- Al crear una pregunta, el count no se actualiza hasta que expire el cache

**Solución propuesta:** `Cache::forget()` después de `saveQuestion()` y dispatch de evento Livewire para refrescar stats.

---

## 9. Checklist de Validación

### 9.1 Funcional
- [ ] Dashboard muestra KPIs correctos después de cache invalidation
- [ ] Filtros (DiagMain, Grado, Sección) funcionan independientemente y combinados
- [ ] Wizard 3 pasos valida correctamente cada paso antes de avanzar
- [ ] Preguntas múltiple choice guardan opción correcta (valor=1)
- [ ] Eliminación bloqueada si hay respuestas existentes
- [ ] Sesiones se agrupan correctamente por estudiante
- [ ] AI Report se filtra por pensums del profesor (viewReport)
- [ ] Modal de detalle de estudiante muestra progreso por área correcto

### 9.2 Data
- [ ] Cache de stats se invalida al mutar datos
- [ ] N+1 queries optimizados (especialmente en sessions)
- [ ] `substr()` en viewReport reemplazado por lógica robusta
- [ ] `whereIn('pensum_id', $pensumIds)` presente en todas las queries de scope
- [ ] SoftDeletes en modelos (actualmente eliminación física en usuarios)

### 9.3 UI/UX
- [ ] Loading states en cada tab
- [ ] Empty states con CTAs apropiados
- [ ] Error states con mensajes descriptivos
- [ ] Paginación funcional (2 paginators: questions + sessions)
- [ ] Modal overlay z-index correcto
- [ ] SweetAlert on success/error/warning

### 9.4 Migración
- [ ] API endpoints cubren todas las queries del Livewire
- [ ] React Query cache strategy definida (staleTime 30 min)
- [ ] AI Report generation migrado a job async
- [ ] `markdown_to_bootstrap()` reemplazado por librería markdown React
- [ ] ENUM('true','false') migrado a boolean en schema

---

## 10. Dependencias y Acoplamiento

### 10.1 Dependencias del Módulo

```
Profesor Diagnostics
├── Profesor Model (pensums relationship) → Pensum Model
├── Estudiant Model (inscripcion → Seccion → Grado)
├── Pensum Model (asignatura relationship)
├── Lapso Model (Lapso::current())
├── Institucion Model (autoridads)
├── 16 Instrument Models (Acoplamiento fuerte)
│   ├── DiagMain, DiagQuestion, DiagOption
│   ├── DiagSession, DiagAnswer
│   ├── DiagReport, DiagResult, DiagReportPensum
│   ├── DiagReportIndicatorResult, DiagReportAiDraft, DiagReportAuditLog
│   ├── DiagRecommendation, DiagReferent
│   ├── DiagCompetency, DiagIndicator
│   └── SectionDiagnosticReport (Planning, compartido!)
├── 4 AI Traits (compartidas con Planning)
│   ├── QwenReportTrait, DeepSeekReportTrait
│   ├── GeminiReportTrait, OpenRouterReportTrait
└── Cache (Redis, key namespace stats_{userId}_...)
```

### 10.2 Acoplamiento con Planning Module

| Elemento | Planning | Profesor | Riesgo |
|----------|----------|----------|--------|
| AI Traits | ✅ Usa las 4 | ✅ Usa las 4 | **ALTO** — cambios en traits afectan ambos |
| Modelos Instrument | ✅ Todos | ✅ Todos | **ALTO** — schema compartido |
| SectionDiagnosticReport | ✅ Usa | ❌ No usa (mismo namespace) | **BAJO** — solo comparte namespace |
| DiagMain CRUD | ✅ Full CRUD | ❌ Solo lectura (selector) | Medio — planning controla catálogo |
| DiagQuestion CRUD | ✅ Gestión central | ✅ Gestión por profesor | **Medio** — ambos crean preguntas en mismas tablas |

### 10.3 Tabla de Modelos y su Uso

| Modelo | Planning | Profesor | Notas |
|--------|----------|----------|-------|
| `DiagMain` | CRUD + asignación | Solo filtro/selector | Planning controla catálogo |
| `DiagQuestion` | CRUD + gestión | CRUD por pensum asignado | Ambos crean preguntas |
| `DiagOption` | CRUD en cascada | CRUD en cascada | Dependiente de question |
| `DiagSession` | Vista general | Vista por profesor | Scoped por pensumIds |
| `DiagAnswer` | Vista general | Vista por profesor | Mismo scope |
| `DiagReport` | Generación | Visualización + filtrado | Profesor filtra por sus pensums |
| `DiagResult` | POST-generación | Solo lectura | Dependiente de reporte |
| `DiagReferent` | CRUD | Lectura (referente_normativo) | Datos maestro |
| `DiagCompetency` | CRUD | Lectura (contrastes) | Datos maestro |
| `DiagIndicator` | CRUD | Lectura (contrastes) | Datos maestro |
| `DiagRecommendation` | POST-generación | Lectura en reporte | Dependiente de reporte |
| `DiagReportAiDraft` | POST-generación | Lectura en modal | Dependiente de reporte |
| `DiagReportAuditLog` | POST-generación | No usa (implícito) | Auditoría |

---

## 11. Comparativa: Profesor vs Planning Diagnostics

| Aspecto | Profesor Diagnostics | Planning Diagnostics |
|---------|---------------------|---------------------|
| **Componente** | `livewire:profesor.diagnostics.index-component` | `livewire:evaluacion.diagnostic.index-component` |
| **Tamaño** | ~1756 líneas | ~2500+ líneas (est.) |
| **Tabs** | 4: dashboard, questions, sessions, analytics | 5: dashboard, DiagMain, questions, sessions, analytics |
| **Scope de datos** | Solo pensums asignados al profesor | Todos los pensums (vista admin) |
| **DiagMain CRUD** | ❌ No (solo selector) | ✅ Sí (full CRUD) |
| **Section Reports** | ❌ No | ✅ Sí (SectionDiagnosticReport) |
| **AI Traits** | ✅ Compartidas (4) | ✅ Compartidas (4) |
| **Cache stats** | ✅ `stats_{userId}_...` (30 min TTL) | ❌ No cache |
| **WithPagination** | ✅ Sí (15 sesiones, 10 preguntas) | ✅ Sí |
| **Complejidad de queries** | Scoped por pensum del profesor | Sin scope adicional |
| **Modal wizard** | ✅ 3 pasos | ✅ 3 pasos (similar) |
| **Estados de UI** | Loading, empty, error, filtered | Loading, empty, error |
| **N+1 queries** | Sí (estudiant name por fila) | Probablemente sí |
| **Deprecados** | `_deprecated_generateAIReport()` (aún funcional) | Similar |

---

## 12. Hallazgos y Recomendaciones

### 12.1 Hallazgos Críticos

| # | Hallazgo | Impacto | Acción Requerida |
|---|----------|---------|------------------|
| H1 | **Componente monolítico de 1756 líneas** | Mantenibilidad cero, testing imposible | Descomponer en subcomponentes React con hooks |
| H2 | **`substr()` para extraer pensum_id** | Bug silencioso si formato cambia | Reemplazar por parsing robusto o campo explícito |
| H3 | **Niveles cualitativos inconsistentes** | Reportes con nomenclatura contradictoria | Unificar a un solo enum en toda la app |
| H4 | **4 AI traits compartidas con Planning** | Cambios en traits afectan 2 módulos | Extraer a servicio independiente `app/Services/LLM/` |
| H5 | **Cache sin invalidación** | Dashboard desactualizado hasta 30 min | Cache::forget() en mutaciones |
| H6 | **ENUM('true','false')** | Migración a NextJS requiere normalización | Schema migration a boolean |
| H7 | **set_time_limit(300) en método deprecated** | No funciona en PHP modo seguro | Migrar a job queue |
| H8 | **N+1 en listado de sesiones** | Queries N+1 por estudiante | Eager load o select columns explícito |

### 12.2 Recomendaciones de Arquitectura (NextJS)

1. **Separar AI Services**: Extraer las 4 traits LLM a `app/Services/LLM/` con interfaz común (`LLMServiceInterface` con `generateReport(payload): Response`). Esto permite testing y reemplazo independiente.

2. **Refactor Queries**: Las queries de agregación (stats, sessions group by student) deben migrarse a endpoints API dedicados con paginación server-side nativa, no Livewire.

3. **Form Wizard como Stepper Component**: El wizard de 3 pasos es candidato perfecto para un componente React `StepWizard` reutilizable con validación por paso y estado persistente.

4. **Chart.js → Recharts**: La preparación de datos Chart.js en inline PHP debe migrarse a Recharts (React nativo) con data preparation en el frontend.

5. **Async AI Reports**: `_deprecated_generateAIReport()` con `set_time_limit(300)` debe reemplazarse por:
   - Frontend: Botón "Generar Reporte" → POST a API → muestra "Generando..."
   - Backend: Job queue con progress tracking
   - Polling/SSE: Notificar al frontend cuando el reporte esté listo

6. **Optimistic UI**: En operaciones CRUD de preguntas, implementar optimistic updates + rollback on error para mejor UX.

### 12.3 Modelo de Datos Propuesto (API Response)

```typescript
// API response types para el módulo migrado
interface DiagnosticsStats {
  total_questions: number;
  total_sessions: number;
  completed_sessions: number;
  active_sessions: number;
  student_accuracy: number;
  correct_answers: number;
  total_answered: number;
}

interface Question {
  id: number;
  pensum_id: number;
  pregunta: string;
  tipo_pregunta: 'multiple' | 'open' | 'scale';
  options?: DiagOption[];
  difficulty: 'easy' | 'medium' | 'hard';
  weighing: number;
  activo: boolean;
  created_at: string;
}

interface StudentSession {
  estudiant_id: number;
  estudiante: { full_name: string; email: string };
  grados: { id: number; name: string }[];
  total_sessions: number;
  completed_sessions: number;
  avg_duration_minutes: number;
  last_session_date: string;
}

interface AIReport {
  id: number;
  student_id: number;
  diag_main_id: number;
  contenido: {
    identificacion: object;
    contexto: object;
    resultados_globales: object;
    areas_evaluadas: AreaEvaluada[];
    contrastes: Contraste[];
    perfil: object;
    recomendaciones: Recomendacion[];
  };
  generated_at: string;
  llm_model: string;
}
```

---

> **Documentación generada:** 2026-06-06
> **Módulos relacionados:** [gestion-actividades.md](gestion-actividades.md), [gestion-profesors.md](../planning/gestion-profesors.md), [gestion-diagnostics.md](../planning/gestion-diagnostics.md)
> **Ver también:** [RETROSPECTIVE.md](../RETROSPECTIVE.md) §4 (dependency graph), §5 (priority justification)
