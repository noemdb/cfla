# 03 — Componentes Livewire (docente, Evaluación, Académico)

> **Fuente:** `saefl/s2526/app/Http/Livewire/Inicial/` (6 componentes CRUD + 5 traits) · `app/Http/Livewire/Evaluacion/Inicial/` (7 componentes) · `app/Http/Livewire/Academico/Inicial/Eiplanningwk/` (1 huérfano) · vistas asociadas `resources/views/livewire/{inicial,evaluacion,academico}/`.
> El contexto de perspectivas (controladores, stats, menús) está en [`07-perspectivas-roles.md`](07-perspectivas-roles.md); las vistas/modales en detalle en [`04-vistas-ui.md`](04-vistas-ui.md).

## 0. Arquitectura común

### 0.1 Tres perspectivas de montaje

| Perspectiva | Ruta | Middleware | Vista anfitriona | Embebido |
|---|---|---|---|---|
| **Docente (A)** | `inicials.{entidad}.index` (`GET /eiplanningwks`…) | `['auth','is_inicial']` | `resources/views/inicials/{entidad}/index.blade.php` | `<livewire:inicial.{entidad}-component />` |
| **Evaluación (B)** | `evaluacions.inicials.index` | `['auth','is_evaluacion']` | `resources/views/evaluacions/inicilas/index.blade.php` | `<livewire:evaluacion.inicial.{entidad}-component :profesor_id="$profesor_id" :grado_id="$grado_id" />` |
| **Académico (C)** | `academicos.inicials.index` | `['auth','is_academico']` | `resources/views/academicos/inicilas/index.blade.php` | **Ninguno** (datos server-side; su componente está huérfano) |

### 0.2 Autorización del docente

`User::IsInicial()` (transcripción en [`07-perspectivas-roles.md`](07-perspectivas-roles.md) §7.2) devuelve el `Profesor` del usuario solo si el rol vigente cumple `area ∈ {SISTEMA, PROFESORADO}` + `rol ∈ {ADMINISTRADOR, INICIAL}` + `finicial <= now <= ffinal` + `is_active != 'disable'`. Todos los componentes docente hacen `User::findOrFail(Auth::id())` en `mount()`; si falla, `profesor_id` queda `null` y `render()` no devuelve datos (**no es un abort/403 explícito** — hallazgo para migración).

### 0.3 Patrones comunes a los 5 componentes CRUD de planes/evaluación

1. **Modelo tipado como propiedad pública** (`public Eiplanningwk $eiplanningwk;`) + validación **inline** `$this->validate([...])` con claves prefijadas `eiplanningwk.campo` (Livewire 2 mapea a la propiedad anidada).
2. **`WithPagination`** con `$paginationTheme = 'bootstrap-4'` (excepción: `EievaluationkComponent` usa `'bootstrap'`).
3. **Modal único** gobernado por `$showModal`, `$modalType`, `$editingId`; `openModal($type, $id = null)` con `switch` por tipo; `closeModal()` restaura defaults + `resetValidation()`.
4. **Hijos tipo lista** (summary/review/act/position): modelo tipado propio + FK explícita asignada antes de guardar + **reapertura del modal listador** (`openModal('summary', $this->eiplanningwk_id)`) tras cada save/delete.
5. **Hijos tipo grid** (strategies): array público `$strategies[día][momento] = ['id' => ?, 'estrategia' => texto, 'order' => ?]`, 5 días × 10 momentos = 50 celdas. **Quirk de persistencia**: el texto SIEMPRE se guarda en la columna `lunes`; `martes…viernes` se ponen en `null`; el día real se persiste en `day_of_week` y el momento en `momento_rutina_diaria`.
6. **Alertas**: `dispatchBrowserEvent('swal', [...])` (SweetAlert2) vía `showSuccessAlert()`/`showErrorAlert()`; Eifinalk usa `session()->flash()`.
7. **Traits Validate**: en runtime SOLO se usa `validationAttributes()` (etiquetas en español desde `COLUMN_COMMENTS`). El array `$rules` de los traits **no se aplica** — los componentes validan inline, con discrepancias (§5).

### 0.4 Constantes del grid de estrategias

- `WEEK_DAYS` = `lunes, martes, miercoles, jueves, viernes` (5).
- `LIST_MOMENT` (10 momentos): `Recibimiento`, `Momento Cívico`, `Aseo-Desayuno-Aseo`, `Periodo: Planificación`, `Periodo: Trabajo Libre`, `Periodo: Orden y limpieza`, `Periodo: Intercambio y Recuento`, `Periodo: Trabajos en Pequeños Grupos`, `Periodo: Actividades Colectivas`, `Periodo: Despedida`.

### 0.5 Helpers de modelos usados por los componentes

- `{Eiplanningwk|Bwk|Projectk|Especialk|Evaluationk}::getPevaluacionsList($profesor_id, $lapso_id)`: join `pevaluacions`/`pensums`, pluck de etiqueta compuesta — alimenta el select de "pevaluación" de los hijos.
- `Profesor::list_grado($profesor_id)`: colección anidada `[pestudio => [grado_id => nombre]]`.
- `Lapso::list_lapso()` · `Seccion::scopeActive` (`status_active` truthy).
- `Eiprojectk::getForProfesorIdList`: etiquetas `"id: diagnostico (50 chars)"` para el select opcional de proyecto en Eiplanningwk.
- `Pevaluacion::list_pevaluacion($profesor_id)`: etiqueta `"lapso | grado sección | asignatura"` — usado por Eifinalk.
- `Pevaluacion::getEstudiantsAttribute`: estudiantes de la sección vía `inscripcions` con `created_at <= lapso.ffinal` — select de estudiante en Eifinalk.
- `Estudiant::hasEifinalkForLapso($lapsoId)` / `Estudiant::eifinalks()`.

---

## A) Componentes CRUD del docente (`app/Http/Livewire/Inicial/`)

### A.1 `EiplanningwkComponent` — Planificación Semanal (595 líneas)

Tag `<livewire:inicial.eiplanningwk-component />`; vista `livewire.inicial.eiplanningwk-component`; trait `EiplanningwkValidateTrait`; modales en `livewire/inicial/modal/eiplanningwk/`.

**Propiedades públicas:** `$eiplanningwk` (modelo tipado) · `$eiplanningwsummary` (tipado) · `$strategies` (grid 50 celdas) · `$activeDay='lunes'` · `$activeMoment='Recibimiento'` · `$eiplanningwk_id` (FK de contexto) · `$eiplanningwsummary_id` · `$profesor_id` · `$lapso_id` · `$activeTab='list'` · `$showModal` · `$modalType` (create|edit|view|summary|strategy|edit-summary) · `$editingId` · `$list_comment{,_summary,_strategy}` (COLUMN_COMMENTS) · `$list_moment` · `$list_grado/_seccion/_pevaluacion/_lapso/_eiprojectk` · `$weekDays` (5, hardcodeados) · `$moments` (10, hardcodeados) · `$search` · `$filterGrado`/`$filterSeccion`.

`$listeners`: **ninguno** (patrón en todo el módulo).

**`mount()`**: sin parámetros; `User::findOrFail(Auth::id())` → `($user->IsInicial()) ? $user->profesor : null` → `$profesor_id`; `initializeLists()` + `resetModels()`.

**Métodos:**

- `render()`: `Eiplanningwk::where('profesor_id', ...)->with(['grado','seccion','eiprojectk'])`; `when($search)` LIKE sobre `diagnostico`/`observacion`; `when($filterGrado/$filterSeccion)` por FK; `orderBy('created_at','desc')`; `paginate(10)`.
- `updatedEiplanningwkGradoId($value)`: cascada grado→sección (recarga `$list_seccion`, nulifica `seccion_id`).
- `updatedLapsoId($lapso_id)`: recarga `$list_pevaluacion` vía `getPevaluacionsList` (el select de pevaluación del summary depende del lapso).
- `updatedSearch()/updatedFilterGrado()`: `resetPage()`; el de grado refresca `$list_seccion`.
- `setActiveDay($day)` (resetea `activeMoment` al primero) / `setActiveMoment($moment)` · `nextMoment()/previousMoment()`.
- `getDayProgress($day)`: cuenta celdas con `estrategia` no vacía → `['completed'=>n, 'total'=>10, 'percentage'=>x]` (barras de progreso).
- `openModal($type, $id=null)` — `switch`: `create`→`resetModels()` · `edit`→`loadPlan($id)` (findOrFail + secciones) · `view`→solo `$editingId` (la vista resuelve por id) · `summary`→`loadPlanForSummary($id)` (fija FK, recarga `list_pevaluacion`, reset summary) · `strategy`→`loadPlanForStrategy($id)` + `loadStrategiesForPlan` · `edit-summary`→`loadSummary($id)`. Todo en try/catch (excepción → cierra modal + error alert). `$modalType=$type; $showModal=true;`
- `closeModal()`: restaura `activeDay/activeMoment` defaults + `resetValidation()`.
- `loadStrategiesForPlan($planId)`: por cada día×momento busca `Eiplanningwstrategy` con `where('day_of_week', $día)->where('momento_rutina_diaria', $momento)->first()`; mapea `['id'=>..., 'estrategia'=>$s?->lunes, 'order'=>...]` (el texto vive en `lunes`).
- `save()` — reglas inline reales (fuente de verdad para migración):
  ```php
  'eiplanningwk.profesor_id'    => 'required|integer',
  'eiplanningwk.grado_id'       => 'required|integer',
  'eiplanningwk.seccion_id'     => 'required|integer',
  'eiplanningwk.eiprojectk_id'  => 'nullable|integer',
  'eiplanningwk.finicial'       => 'required|date',
  'eiplanningwk.ffinal'        => 'required|date|after_or_equal:eiplanningwk.finicial',
  'eiplanningwk.tiempo_ejecucion' => 'required|integer|min:1',
  'eiplanningwk.diagnostico'    => 'required|string|min:10',
  'eiplanningwk.observacion'    => 'nullable|string',
  ```
- `saveSummary()`: requiere `eiplanningwk_id`; reglas: `pevaluacion_id/componente/objetivo/aprendizaje_esperado/indicadores` **required**, `linea_investigacion/enfasis_curriculares` nullable, `order` `nullable|integer|min:1` (vacío → `null`); tras guardar **reabre** `openModal('summary', $this->eiplanningwk_id)`.
- `saveStrategies()`: valida ≥1 celda con texto; por cada celda con contenido: si tiene `id` → update (`find`), si no → `new` con FK+`day_of_week`+`momento_rutina_diaria`; **siempre** `$strategy->lunes = $texto` y `martes…viernes = null`; reescribe el `id` en el array (evita duplicados al re-guardar).
- `saveCurrentStrategy()`: upsert de la celda activa; si vacía → error; al guardar llama `nextMoment()` (auto-avance).
- `delete($id)`: `findOrFail->delete()` (cascada por FK de BD) + swal.
- `deleteSummary($id)`: delete + swal + reabre listador.
- `deleteStrategy($day, $momento)`: localiza por el `id` embebido en la celda, elimina y resetea la celda.
- `initializeLists()` / `resetModels()` / `resetModelSummary()` / `resetModelStrategies()` (grid a 50 celdas vacías) / `showSuccessAlert()` / `showErrorAlert()`.

**Derivados usados por la vista:** `getOrderedSummaries()` (`CASE WHEN order IS NOT NULL THEN order ELSE id`), `getOrderedStrategies()`, `getStrategyByMomentAndDay($momento, $día)`, `week_days`, `list_moment` (accessors del modelo).

**Permisos:** `auth+is_inicial` + `IsInicial()` en mount; scope `profesor_id` propio en todas las queries. La vista muestra `observacion` etiquetada **"[Coord. Evaluación]"** (el docente la ve pero NO la edita — la escribe la perspectiva Evaluación).

### A.2 `EiplanningbwkComponent` — Planificación Quincenal (568 líneas)

Espejo del semanal con `Eiplanningbwk`/`Eiplanningbwsummary`/`Eiplanningbwstrategy`; vista `livewire.inicial.eiplanningbwk-component`; modales en `modal/eiplanningbwk/`. Diferencias:

- `$moments`/`$weekDays` **NO hardcodeados** — se asignan en `initializeLists()` desde `Eiplanningbwstrategy::LIST_MOMENT/WEEK_DAYS`.
- `openModal` agrega el caso **`edit-strategy` → `$this->loadStrategy($id)` — método INEXISTENTE** → lanza `Error` capturado por el catch → cierra modal + alerta de error (**BUG: la edición individual de estrategia del quincenal está rota en runtime**).
- Usa FQCN `\App\Models\app\Inicial\Eiplanningbwstrategy::` en los métodos de estrategia (los demás usan `use`).
- `render()` con `with(['grado','seccion'])` (sin eiprojectk). Mensaje: "Plan quincenal guardado exitosamente".
- Reglas inline de `save()`/`saveSummary()` idénticas al semanal.

### A.3 `EiprojectkComponent` — Proyecto de Aula (636 líneas)

El componente con MÁS tipos de hijos: **reviews (elección del tema), summaries (componentes) y strategies (grid)**. Tag `inicial.eiprojectk-component`; trait `EiprojectkValidateTrait`.

**Particularidades:**

- `render()`: search SOBRE `diagnostico` (único campo); **sin FK a otro plan** (el proyecto es su propia entidad).
- `openModal` casos: `create`, `edit`, `view`, `review`, `summary`, `strategy`, `edit-review`, `edit-summary`.
- `save()` (reglas inline): igual patrón semanal **SIN `observacion`** y **sin FK a otro plan**: `profesor_id/grado_id/seccion_id/finicial/ffinal(after_or_equal)/tiempo_ejecucion(min:1)/diagnostico(required|string|min:10)`.
- `saveReview()` — hijo "Elección del tema" (conversación con los niños):
  ```php
  'eiprojectreview.eiprojectk_id'        => 'required|integer',
  'eiprojectreview.posibles_temas_interes' => 'required|string',
  'eiprojectreview.eleccion_tema_nombre' => 'required|string',
  'eiprojectreview.que_sabe'             => 'required|string',
  'eiprojectreview.que_desean_aprender'  => 'required|string',
  'eiprojectreview.que_necesitamos'      => 'required|string',
  'eiprojectreview.quienes_nos_pueden_apoyar' => 'required|string',
  'eiprojectreview.order'                => 'nullable|integer|min:1',
  ```
- `saveSummary()`: mismo set que los demás summaries + FK `eiprojectk_id` + `pevaluacion_id` required.
- `loadReview($id)/loadSummary($id)`: findOrFail con **fallback preservando el contexto** `$eiprojectk_id` (si el id no existe, mantienen el FK para no perder el modal).
- Grid strategies idéntico con `Eiprojectkstrategy`; `initializeLists()` **sin `list_eiprojectk`**.
- Derivados del modelo: `getOrderedReviews/getOrderedSummaries/getOrderedStrategies`.

### A.4 `EispecialkComponent` — Plan Especial (545 líneas)

- Cambia `diagnostico` por **`justificacion`** como campo requerido principal: `'eispecialk.justificacion' => 'required|string|min:10'`; `observacion` nullable.
- Hijo tipo lista: **`activity`** (`Eispecialact` — "actividad del plan"): reglas idénticas a un summary (`componente/objetivo/aprendizaje_esperado/indicadores` required; `linea_investigacion/enfasis_curriculares` nullable; FK `eispecialk_id`+`pevaluacion_id`); `loadPlanForActivity($id)` fija contexto y recarga `list_pevaluacion`.
- `openModal` casos: `create`, `edit`, `view`, `activity`, `strategy`, `edit-activity`.
- `render()`: search sobre **`justificacion`** y `observacion`; `with(['grado','seccion'])`.
- Grid strategies con `Eispecialstrategy`; `$moments/$weekDays` desde sus constantes.
- Modelo provee `getOrderedActivities()`.

### A.5 `EievaluationkComponent` — Plan de Evaluación (323 líneas)

- **`$paginationTheme = 'bootstrap'`** (único que no usa `bootstrap-4`).
- **Filtro extra `$filterLapso`** (la evaluación se referencia a un lapso). **NO tiene grid de strategies**.
- `render()`: `with(['grado','seccion','lapso','profesor'])`; search sobre `observaciones`, `recomendacion`, `asistencia`; filtros grado/sección/**lapso**.
- `openModal` casos: `create`, `edit`, `view`, `position`, `edit-position`.
- `save()` — único de los 5 con **`lapso_id` obligatorio**:
  ```php
  'eievaluationk.profesor_id'  => 'required|integer',
  'eievaluationk.grado_id'     => 'required|integer',
  'eievaluationk.lapso_id'     => 'required|integer',
  'eievaluationk.seccion_id'    => 'required|integer',
  'eievaluationk.finicial'     => 'required|date',
  'eievaluationk.ffinal'      => 'required|date|after_or_equal:eievaluationk.finicial',
  'eievaluationk.observaciones' => 'required|string|min:10',
  'eievaluationk.recomendacion' => 'nullable|string',
  'eievaluationk.asistencia'    => 'required|string',
  ```
- `savePosition()` — hijo "posición/registro evaluativo" (`Eievaluationp`): **todo opcional salvo las 2 FKs** (`eievaluationk_id`, `pevaluacion_id` required; `fecha/nombre_ninos/aprendizaje_alcanzado/componente/indicadores/instrumento/observacion` nullable|string; `order` nullable integer). Los campos `nombre_ninos`/`aprendizaje_alcanzado` describen por niño.
- El listador de posiciones usa `getOrderedEvaluationps()`.

### A.6 `EifinalkComponent` — Informe Final (361 líneas) — **patrón DISTINTO**

Tag `inicial.eifinalk-component`; vista con **2 tabs: "informesList" y "estudiantsList"**; el controlador `EifinalkController@index` pasa `lapsos` y `Lapso::current()`. **Sin `WithPagination`, sin trait** (valida con método propio `rules()`); propiedades escalares sin tipo + `fill()`.

**Propiedades:** `$profesor_id` · `$eifinalk` (contenedor) · `$selected_id` · `$activeTab='informesList'` · 15 campos del informe (`order`, `pevaluacion_id`, `lapso_id`, `estudiant_id`, `title`, `context_group`, `planing_eject`, `featured_project`, `special_activities`, `achievements`, `individual_observations`, `family_participation`, `conclusions`, `recommendations`, `expected_learnings`, `specialist_observation`) · listas (`$list_comment`, `$list_pevaluacion`, `$pevaluacions`, `$learning_areas`, `$selected_expectations=[]`) · filtros (`filter_pevaluacion`, `filter_estudiant`, `filter_title`, `filter_pevaluacion_estudiantes`, `filter_estudiant_search`) · `$estudiantes=[]`.

**`mount()`**: `IsInicial()` → `profesor_id`; `$list_pevaluacion = Pevaluacion::list_pevaluacion($profesor_id)`; `$pevaluacions = Pevaluacion::where('profesor_id',...)->with(['seccion','lapso','pensum.grado'])->get()`; `$learning_areas = collect()`.

**Métodos:**

- `rules()` (protected, método): `order` required · `pevaluacion_id` `required|exists:pevaluacions,id` · `estudiant_id` `required|exists:estudiants,id` · `title` `required|string|max:191` · los 12 campos restantes `nullable|string`.
- `updatedPevaluacionId($value)`: carga la Pevaluacion; fija `$lapso_id`; `$learning_areas = Eilearningarea::with('expectations')->where('grado_id', $pevaluacion->grado->id)->get()` — áreas con sus expectativas para el acordeón.
- `updatedFilterPevaluacionEstudiantes($value)`: fija `$lapso_id` y `$estudiantes = $pevaluacion->estudiants` (accesor con inscripciones válidas del lapso).
- `render()`: `Eifinalk::query()->with(['pevaluacion','estudiant','expectations.area'])`; scope `byProfesor`; filtros (pevaluacion FK, estudiant `whereHas` name/ci LIKE, title LIKE); `orderBy('order','asc')`; **`get()` SIN paginación**.
- `openModal()` **SIN ARGUMENTOS** (rompe el patrón modalType): siempre create.
- `edit($id)`: `findOrFail` → `fill($record->toArray())`; recarga `learning_areas` por el grado; `$selected_expectations = $record->expectations->pluck('id')->toArray()`.
- `save()`: `create($this->modelData())`; si hay selección: **`attach`** con mapa pivote `[expectationId => ['eilearningarea_id'=>..., 'pevaluacion_id'=>...]]`; `close()`; **`$this->emit('eifinalkAdded')`** (único emit del módulo).
- `update()`: `update(modelData())`; reconstruye el mapa y **`expectations()->sync($expectationsData)`** (reemplazo total — elimina las desmarcadas); `session()->flash('message', ...)`.
- `delete($id)`: findOrFail→delete + flash.
- `modelData()`: los 15 campos fillable. `resetInput()`: ~17 props + `learning_areas`/`selected_expectations`.

**Relación con pevaluaciones/estudiantes:** el informe nace de una `Pevaluacion` (grado/sección/lapso/asignatura/profesor delegados por accessors) y un `Estudiant` de esa sección. Las **expectativas** son `belongsToMany Eilearningexpectation` vía pivote **`eifinalk_expectation`** con `withPivot('eilearningarea_id','pevaluacion_id')->withTimestamps()`; el modal muestra el acordeón de `$learning_areas` → checkboxes → `$selected_expectations`. El modal condiciona campos según `$pevaluacion->status_official`: oficial → `expected_learnings`/`achievements`/`individual_observations`; componente → `specialist_observation`.

**Vista (2 tabs):**
- **informesList**: tabla con 3 filtros; columnas con expectativas agrupadas por área; botones imprimir (`inicials.eifinalks.print`), editar, eliminar.
- **estudiantsList**: select de pevaluación → `$estudiantes` con badges por lapso (`hasEifinalkForLapso` — verde si ya tiene informe) + links `route('inicials.eifinalks.print-all-for-lapso', [$estudiant, $lapso])`.

**Flujos de impresión (controladores docente):** `print(Eifinalk)` carga `pevaluacion.pensum.grado/seccion/lapso/profesor` + `estudiant` + `expectations.area`; `printAllforLapso(Estudiant, Lapso)` consulta los informes del estudiante en el lapso y los separa en **`$eifinalks_oficial` (`status_official=true`) y `$eifinalks_component` (false)**, ambas `orderBy('order','asc')` → `formats.eifinalks.index-all`.

---

## 5. Traits de validación (`app/Http/Livewire/Inicial/`) — reglas EXACTAS

> **⚠️ Advertencia:** en **NINGÚN** componente CRUD se aplican los `$rules` de estos traits en runtime; los componentes validan inline dentro de `save*()`. Lo único vivo es `validationAttributes()` (nombres en español para mensajes de error, desde `COLUMN_COMMENTS`). Las reglas se transcriben como diseño original y para contrastar divergencias.

### 5.1 `EiplanningwkValidateTrait`

Plan: igual al inline EXCEPTO `observacion` **required|string** (inline: nullable) y `ffinal` sin `after_or_equal`. Summary: igual EXCEPTO `linea_investigacion`/`enfasis_curriculares` **required|string** (inline: nullable). **Strategy**: `momento_rutina_diaria` required; **`lunes`…`viernes` TODOS required|string** (⚠️ diverge radicalmente del runtime: una sola celda con texto en `lunes`, resto null) + `order` nullable.

### 5.2 `EiplanningbwkValidateTrait`

Mismo bloque para `eiplanningbwk`/`eiplanningbwsummary` (aquí `observacion` SÍ nullable en el trait; summary con `linea_investigacion`/`enfasis_curriculares` required). **NO contiene bloque para `eiplanningbwstrategy`** (aunque la clase existe).

### 5.3 `EiprojectkValidateTrait`

Plan: patrón semanal SIN observacion. Review: los 6 campos temáticos required + **`order` required|string (⚠️ anómalo: string en vez de integer)** + `estrategias` nullable. Summary: como los demás + `estrategias` nullable.

### 5.4 `EispecialkValidateTrait`

Plan: `justificacion` required; **`observacion` required (⚠️ inline: nullable)**. Activity: igual al summary con `linea_investigacion`/`enfasis_curriculares` **required (⚠️ inline: nullable)**.

### 5.5 `EievaluationkValidateTrait`

Cabecera: igual al inline de `save()` (con `lapso_id`) pero **sin `after_or_equal` ni `min:10`**. Posición: idéntico al inline (todo nullable salvo FKs).

Particularidad: su `validationAttributes()` usa `$comments['campo'] ?? 'fallback'` (los demás indexan directo).

### 5.6 Tabla de divergencias trait ↔ runtime (fuente de verdad = INLINE)

| Campo | Trait | Runtime (inline) |
|---|---|---|
| `*.observacion` (wk/especialk) | required | **nullable** |
| `summary.linea_investigacion`/`enfasis_curriculares` | required | **nullable** |
| `strategy.lunes…viernes` | todos required | **solo celda activa con texto en lunes**; resto null |
| `eiprojectreview.order` | required\|string | **nullable\|integer** |
| `eiplanningwk.observacion` (bwk trait) | nullable | nullable ✓ |

---

## B) Componentes de la perspectiva Evaluación (`app/Http/Livewire/Evaluacion/Inicial/`)

> Contexto de montaje, controlador con cache y stats: [`07-perspectivas-roles.md`](07-perspectivas-roles.md) §5–6. Aquí el detalle por componente.

### B.1–B.4 Los 4 componentes de planes (wk, bwk, projectk, especialk)

Patrón común read-only + escritura de observación:

- **Props:** `$eiplanningwks` (colección resultado), `$selectedEiplanningwkId`, `$showForm=false`, `$profesor_id`, `$grado_id`, `$seccion_id`, `$observacion`, `$showStrategiesModal=false`, `$selectedEiplanningwk=null`; `$rules = ['observacion' => 'required|string|min:5']`.
- **`mount($profesor_id, $grado_id)`**: recibe por posición desde el blade anfitrión.
- **`render()`**: query propia filtrada por `grado_id`/`profesor_id` → `get()` a la colección pública (**sin paginación, sin orderBy** — orden natural de BD).
- `showForm($id)`: carga el plan + su `observacion` actual. `cancelForm()`: reset. **`saveObservacion()`**: valida → `findOrFail` → asigna → `save()` → flash → swal.
- `viewStrategies($id)` (solo wk/bwk): `with([grado,seccion,profesor])->find($id)` → modal **en solo lectura** con `week_days`/`list_moment`/`getStrategyByMomentAndDay`.
- Diferencias menores: bwk valida con `$this->validate()` (propiedad `$rules`) vs inline del semanal; projectk/especialk **sin modal de estrategias** (listan reviews/summaries/activities en lectura).

### B.5 `EievaluationkComponent` (Evaluación)

- **Escribe `recomendacion`** (no observación): `$rules = ['recomendacion' => 'required|string|min:5']`; `mount($profesor_id=null, $grado_id=null, $lapso_id=null)` — el filtro diferencial es el **lapso**; `render()` filtra por los 3. La vista lista las evaluaciones con sus `eievaluationps` (`nombre_ninos`/`aprendizaje_alcanzado`) en lectura + form de recomendación.

### B.6 `EifinalkComponent` (Evaluación) — listado puro

- Props: `$eifinalks`, `$profesor_id`, `$seccion_id`, `$lapso_id`, `$lapsos` (`Lapso::all()` en mount). `mount($profesor_id=null, $seccion_id=null, $lapso_id=null)`.
- `render()`: `with([pevaluacion.profesor, pevaluacion.lapso, pevaluacion.seccion.grado, estudiant])`; scope `byProfesor`; `whereHas('pevaluacion')` para lapso/sección; `orderBy('estudiant_id')` + `orderBy('pevaluacion_id')` + **`groupBy('estudiant_id')`** + `get()` — ⚠️ SQL dudoso (solo funciona en MySQL permisivo) pero produce una fila por estudiante.
- `downloadFormat($id)`: `dispatchBrowserEvent('show-format')` → JS de la vista abre `GET /evaluacions/eifinalks/format/{id}`.
- ⚠️ **BUG de montaje:** el blade anfitrión pasa `:profesor_id` y `:grado_id`, pero `mount` firma `(profesor_id, seccion_id, lapso_id)` → **el `grado_id` cae posicionalmente en `$seccion_id`** (el "filtro de sección" filtra en realidad por grado).

### B.7 `EifinalksComponent` (Evaluación) — **HUÉRFANO con bugs críticos**

- `render()`: scopes `byProfesor`+`byLapsoYSeccion` … y termina en `$this->eifinalks = $query->get(); dd();` — **el `dd()` mata CADA render**.
- `saveRecommendations()`: asigna `recommendations` pero **NUNCA llama `save()`** → pérdida silenciosa.
- Renderiza la vista del hermano (`eifinalk-component`); no existe vista propia. **Sin ningún uso** (views/routes) — intento abandonado de "editar recomendaciones desde Evaluación". Descartar.

---

## C) `Academico/Inicial/Eiplanningwk/IndexComponent` — HUÉRFANO

- Clase de **13 líneas**: `render()` retorna `view('livewire.academico.inicial.eiplanningwk.index-component')`. Sin props, sin mount, sin listeners, sin lógica. Su vista es un div placeholder ("Care about people's approval and you will be their prisoner.").
- La perspectiva Académico real es **server-side sin Livewire** (data-passthrough de solo lectura). En la misma carpeta hay vistas huérfanas: `index-component.blade.php` (copia de la tabla del wk docente sin wiring) y `observation-component.blade.php` (placeholder "Be like water."). **Descartar en migración.**

---

## 6. Patrones transversales de gestión de sub-entidades (síntesis)

1. **Hijos tipo lista** (summary/review/act/position) — 4 pasos invariantes: (a) `openModal('<hijo>', $planId)` fija la FK de contexto y recarga `list_pevaluacion` según `lapso_id`; (b) el form guarda con el modelo tipado, validación inline y `order ?: null`; (c) swal de éxito; (d) **reapertura automática** del listador. Edición vía `edit-<hijo>` → `load<Hijo>($id)`. Borrado directo `delete<Hijo>($id)`. Los listadores usan `getOrdered{Summaries|Reviews|Activities|Evaluationps}()` (order no nulo primero, luego id).
2. **Hijo tipo grid (strategy)** — array `$strategies[día][momento]['id'|'estrategia'|'order']`; carga con `loadStrategiesForPlan` (busca por `day_of_week`+`momento_rutina_diaria`, lee texto de `lunes`); guardado masivo `saveStrategies()` o por celda `saveCurrentStrategy()` (auto-avance `nextMoment()`); borrado por celda `deleteStrategy($day,$momento)` vía el `id` embebido. **Persistencia deforme**: texto→`lunes`, resto→null, día→`day_of_week`. Para migrar: normalizar a un único campo `estrategia` + `day_of_week` + `momento`.
3. **Eifinalk + expectativas (pivote)** — checkboxes desde `$learning_areas` (áreas del grado de la pevaluación, con `expectations`); create→`attach` con pivote enriquecido, update→`sync` con reconstrucción del mapa `[id => ['eilearningarea_id','pevaluacion_id']]`.
4. **Eventos**: docentes → `dispatchBrowserEvent('swal')`; Eifinalk → `emit('eifinalkAdded')` + `session()->flash`; vistas anfitrionas → `window.livewire.emit('remove'/'method', id)` para confirmaciones de borrado con SweetAlert; Evaluación → `dispatchBrowserEvent('show-format')`. **Ningún componente declara `protected $listeners`**.

## 7. Matriz A (docente) vs B (Evaluación)

| Aspecto | Docente (A) | Evaluación (B) |
|---|---|---|
| Middleware | `auth + is_inicial` | `auth + is_evaluacion` |
| Contexto | `User::IsInicial()` en `mount()` → profesor propio | Props inyectadas por la vista — **sin verificación de rol en el componente** |
| CRUD | Completo de plan + hijos + Eifinalk con attach/sync | **Read-only** salvo `observacion` (planes) / `recomendacion` (evaluación) |
| Datos en render | Query + `with` + search + filtros + `created_at desc` + `paginate(10)` | `get()` sin orden ni paginación, filtrado solo por props |
| Validación | Inline masiva + `validationAttributes` | `$rules` mínimos (`required|string|min:5`) |
| Grid estrategias | Editable (50 celdas, upserts) | Solo visualización |
| Eifinalk | Create/edit con attach/sync, tabs, `emit('eifinalkAdded')` | Listado agrupado por estudiante con links `print-all-for-lapso` |
| Modales | Único modal con `switch` por `$modalType` | Modal simple `$showForm` + modal de estrategias |
| Huérfanos/bugs | `edit-strategy` bwk roto; `deleteStrategy` mal llamado desde manager | `EifinalksComponent` con `dd()`/sin `save()`; mount desalineado |

## 8. Hallazgos y bugs del legacy

| # | Hallazgo | Impacto migración |
|---|---|---|
| 1 | `EiplanningbwkComponent::openModal('edit-strategy')` llama a `loadStrategy($id)` **inexistente** → catch cierra modal con error | La edición individual de estrategia del quincenal está rota; reescribir |
| 2 | Vistas `strategy-manager` invocan `deleteStrategy({$strategy->id})` con **1 argumento**; la firma es `deleteStrategy($day, $momento)` → **llamada silenciosamente no-op**; el borrado solo funciona desde `strategy-form` | Corregir wiring |
| 3 | `EifinalksComponent::render()` con `dd()` | Descartar |
| 4 | `saveRecommendations()` sin `save()` | Descartar |
| 5 | Mount desalineado en `evaluacions/inicilas/index.blade.php`: pasa `:grado_id` a un `mount($profesor_id, $seccion_id, $lapso_id)` → **el grado cae en seccion_id** | Corregir con props nombradas (Livewire 3 lo hace natural) |
| 6 | Huérfanos: `Evaluacion\Inicial\EifinalksComponent` y `Academico\Inicial\Eiplanningwk\IndexComponent` (+ vistas placeholder) | No portar |
| 7 | Traits `$rules` = **documentación muerta** (runtime valida inline) con divergencias (§5.6) | Unificar en Form Requests con las reglas **inline** como fuente de verdad |
| 8 | `$paginationTheme` inconsistente (`bootstrap-4` vs `bootstrap`) | Livewire 3: Tailwind nativo |
| 9 | Modal docente `eifinalk/create.blade.php` condiciona campos por `$pevaluacion->status_official` donde `$pevaluacion` es variable derivada en Blade (riesgo de undefined); **el acordeón de expectativas está comentado** — el flujo de expectativas está parcialmente desactivado en la UI docente pese a existir en el componente | Activar el flujo completo en cfla |
| 10 | `EifinalkComponent (Evaluación)::render()` con `groupBy` + `orderBy` de columnas no agrupadas — SQL inválido en modo estricto | Reemplazar por `get()->groupBy()` de colección o `distinct()` |
| 11 | **Persistencia deforme del grid** (texto siempre en `lunes`) | Cualquier migración de schema DEBE normalizar; hoy los getters compensan en lectura |
| 12 | Sin `protected $listeners` en ningún componente (coordinación vía `dispatchBrowserEvent` + emits sueltos) | En Livewire 3 usar el sistema de eventos nativo |
| 13 | `IsInicial()` falla de forma silenciosa (profesor_id null → lista vacía), sin 403 | Migrar a abort(403) explícito o middleware |
