# 04 — Vistas y UI (docente)

> **Fuente:** `saefl/s2526/resources/views/livewire/inicial/` (104 archivos, ~13,670 líneas) · `resources/views/inicials/` (91 archivos, ~7,030 líneas).
> Los componentes que renderizan estas vistas: [`03-livewire-componentes.md`](03-livewire-componentes.md). Los formatos imprimibles: [`05-formatos-impresion.md`](05-formatos-impresion.md). Perspectivas de revisión: [`07-perspectivas-roles.md`](07-perspectivas-roles.md).

## 1. Mapa de vistas (estructura real del árbol)

```
resources/views/livewire/inicial/
├── {entidad}-component.blade.php × 6     ← VISTAS RAÍZ (las que viven: wk, bwk, projectk, especialk, evaluationk, finalk)
├── modal/                                ← CAPA VIVA (2ª generación: modal único con $modalType)
│   ├── eiplanningwk/    (main-modal, plan-form 138, plan-details 433, summary-form 111, summary-manager 90, strategy-form 301, strategy-manager 109)
│   ├── eiplanningbwk/   (main-modal, plan-form 131, plan-details 433, summary-form/manager, strategy-form 438, strategy-manager 88)
│   ├── eiprojectk/      (main-modal 137, project-form 99, project-details 486, review-form/manager, summary-form/manager, strategy-form 246, strategy-manager 88, strategy-progress 191)
│   ├── eispecialk/      (main-modal 125, plan-form 114, plan-details 319, details 319, activity-form/manager, strategy-form 246, strategy-manager 88, strategy-progress 191)
│   ├── eievaluationk/   (main-modal 114, evaluation-form 128, evaluation-details 207, position-form 126, position-manager 94)
│   └── eifinalk/         (create 250 — overlay custom, no modal Bootstrap)
├── table/  (5 × index 70–114 l.)         ← HUÉRFANA (ver §8)
├── overlay/ (21 archivos × 21–22 l.)     ← HUÉRFANA (ver §8)
├── forms/  (11 archivos)                 ← HUÉRFANA (ver §8)
├── partials/eiplanningwks/create.blade.php ← archivo VACÍO (0 bytes)
└── formats/                              ← formatos de impresión (doc 05)

resources/views/inicials/
├── home.blade.php · use-cases.blade.php · use-cases shell
├── {entidad}/index.blade.php × 6         ← wrappers del docente (§5)
├── card/profesor.blade.php (86 l.)        ← tarjeta de perfil
├── layouts/ (app, home, dashboard/, footer/)
├── elements/ (boxes, canvas, card, chart, forms, messeges[sic], progress, tabs — 18 archivos)
└── partials/use-cases/ (7 casos — doc 06)
```

**Stack visual de la capa viva:** Bootstrap 4 (cards `border-0 shadow-sm`, badges, dropdowns, btn-group), FontAwesome 5, LaravelCollective `Form::select` con `wire:model`, SweetAlert2 vía `dispatchBrowserEvent`, paginación `bootstrap-4`.

## 2. Anatomía común de las vistas raíz (6 componentes)

Todas las vistas raíz (`{entidad}-component.blade.php`, 240–265 líneas) siguen el mismo esqueleto de 5 bloques:

1. **Header card** (`border-0 shadow-sm`): título + subtítulo muted + botón `btn-primary` "Nuevo Plan" (`wire:click="openModal('create')"`).
2. **Filters card**: fila `row` con input `wire:model.debounce.300ms="search"` (col-md-4) + selects LaravelCollective `Form::select(..., ['wire:model' => 'filterGrado', 'placeholder' => 'Selecciones'])` + botón limpiar (`wire:click="$set('search',''); $set(...)"`).
3. **Lista**: **una tarjeta por registro** (no tabla): `card-header` con badge-pill numerado, título `grado->name - Sección {seccion->name}`, línea muted con fechas + unidad temporal + extras; **dropdown ellipsis** con acciones; `card-body` con bloques de texto (Str::limit + "Ver más..." → `openModal('view', id)`), badges de conteo de hijos y **quick actions** `btn-group-sm`. `@foreach` + `{{ $paginator->links() }}` + empty state (icono fa-3x + CTA).
4. **`@include('livewire.inicial.modal.{entidad}.main-modal')`**.
5. **`wire:loading`**: spinner `position-fixed` centrado (`top:50%; left:50%`).

## 3. Vistas raíz — detalle por entidad

### 3.1 `eiplanningwk-component.blade.php` (241 líneas)

- Filtros: `search` (placeholder "Buscar en diagnóstico u observaciones...") · `filterGrado` · `filterSeccion` + limpiar (×).
- Tarjeta: badge **badge-primary**; fechas d/m/Y; `{{ $plan->tiempo_ejecucion }} semana{{ ... != 1 ? 's' : '' }}`.
- Dropdown: Ver Detalles · Editar Plan · Gestionar Resumen · Gestionar Estrategias · **Generar PDF** (`route('inicials.eiplanningwks.format.index', $plan->id)` target=_blank) · Eliminar (`onclick="confirm('¿Está seguro...') || event.stopImmediatePropagation()"` + `wire:click.prevent="delete(id)"`).
- Body: **Diagnóstico** (Str::limit **300** + Ver más si >300) · **Observación [Coord. Evaluación]** (limit 100 — solo lectura para el docente) · badges `N Resúmenes` (`getOrderedSummaries()->count()`) / `N Estrategias` (`getOrderedStrategies()->count()`) · chip "Proyecto:" si `$plan->eiprojectk` · quick actions Detalles/Editar/Resumen/Estrategias.

### 3.2 `eiplanningbwk-component.blade.php` (265 líneas)

Casi idéntica a la semanal (con `<div>` raíz extra anidado y sangría anómala). Diferencias:

- badge **badge-success**; unidad **"quincena(s)"**; Diagnóstico limit **200**; ⚠️ **inconsistencia**: muestra `Str::limit($plan->observacion, 200)` pero el botón "Ver más" comprueba `strlen > 100`.
- **El dropdown NO tiene "Gestionar Estrategias"** (sí el botón quick-action "Estrategias" — el modal `strategy` abre por ahí).
- `@section('stylesheet')` propio con CSS hover (`card:hover { transform: translateY(-2px) }`) — la única raíz con estilos extra.
- `$plan->eiprojectk` referenciado (la relación **sí existe** en el modelo, verificado l.47), aunque `render()` no la eager-loada (lazy load por registro).

### 3.3 `eievaluationk-component.blade.php` (246 líneas)

- **4 filtros**: search ("Buscar en observaciones...") · `filterGrado` · `filterSeccion` · `filterLapso` etiquetado **"Filtrar por Momento"** (en Educación Inicial el lapso se llama "momento") + "Limpiar Filtros" con label.
- Tarjeta: badge-primary; línea muted = fechas • `lapso->name` • `profesor->fullname`.
- Dropdown: Ver Detalles · Editar Plan · **Gestionar Posiciones** · Generar PDF (`inicials.eievaluationks.format.index`) · Eliminar.
- Body: **Observaciones del Docente** (limit 300) · **Recomendación [Coord. Evaluación]** (limit 100, heading text-success — solo lectura) · **Control de Asistencia** (campo completo `asistencia`) · badge `N Posiciones` (`getOrderedEvaluationps()`) · "Creado: d/m/Y" · quick actions Detalles/Editar/Posiciones.

### 3.4 `eiprojectk-component` / `eispecialk-component` (raíces)

Mismo esqueleto (confirmado por análisis de agente): título "Proyectos de Aula" / "Planes Especiales"; search sobre `diagnostico` (projectk) o `justificacion`/`observacion` (especialk); filtros grado/sección; tarjetas con dropdown (Ver/Editar/Gestionar Revisión [projectk] o Actividades [especialk]/Gestionar Resumen/Gestionar Estrategias/PDF/Eliminar) y quick actions. Especialk muestra Justificación (en vez de Diagnóstico) + Observación [Coord. Evaluación].

### 3.5 `eifinalk-component` (raíz)

- **2 tabs**: `informesList` y `estudiantsList` (gobierno de `$activeTab`).
- **informesList**: filtros `filter_pevaluacion` / `filter_estudiant` / `filter_title` (debounce 300ms); tabla de informes con **expectativas agrupadas por área**; acciones: imprimir (`inicials.eifinalks.print`), editar (`edit($id)`), eliminar — **SIN confirmación** (única raíz con delete sin `confirm()` inline ni SweetAlert).
- **estudiantsList**: select `filter_pevaluacion_estudiantes` → `$estudiantes` con **badges por lapso** (`hasEifinalkForLapso` — verde si ya tiene informe) + links `print-all-for-lapso`.

## 4. La capa viva de modales (`modal/`)

### 4.1 Shell `main-modal.blade.php` (114–142 líneas, 5 entidades con patrón idéntico)

- Envoltura: `@if ($showModal)` → `<div class="modal fade show d-block">` + backdrop — **sin JS de Bootstrap**: la visibilidad la controla Livewire re-renderizando; `tabindex="-1"`.
- Tamaño: `modal-xl` para `view`/`summary`/`strategy` (y `position` en evaluationk); `modal-lg` para el resto.
- **Router de includes**: `@switch($modalType)` → `@case('create'|'edit')` include `*-form` · `@case('view')` include `*-details` · `@case('summary')` include `summary-manager` · `@case('strategy')` include `strategy-*` · `@case('edit-summary')` include `summary-form` (+ por entidad: `review`, `activity`, `position`, `edit-position`, `edit-activity`, `edit-review`).
- Header con `wire:click="closeModal()"` (×) y footer Cancelar/Guardar según tipo.
- **modalTypes por entidad:** eiplanningwk/bwk 6 tipos (create, edit, view, summary, strategy, edit-summary) · eiprojectk **9** (+ review, edit-review) · eispecialk 7 (+ activity, edit-activity) · eievaluationk 5 (create, edit, view, position, edit-position).

### 4.2 Formularios de cabecera (`plan-form` / `project-form`)

- **wk `plan-form` (138 l.)**: 7 campos — grado_id (select; **live**: al cambiar dispara `updatedEiplanningwkGradoId` → recarga `$list_seccion`), seccion_id, eiprojectk_id (select opcional de proyectos del docente), finicial, ffinal, tiempo_ejecucion, diagnostico (textarea). Labels desde `$list_comment` (COLUMN_COMMENTS). `@error` por campo.
- **bwk `plan-form` (131 l.)**: misma estructura sobre `eiplanningbwk.*` — ⚠️ **BUG**: el bloque de observación evalúa `$eiplanningwk` (modelo ajeno, siempre null) → siempre muestra "No hay observaciones".
- **projectk `project-form` (99 l.)**: 6 campos (sin observación, sin eiprojectk_id — es su propia entidad).
- **especialk `plan-form` (114 l.)**: igual con **justificacion** en lugar de diagnostico + observacion.

### 4.3 Detalles (`plan-details` / `project-details` / `details`)

- **wk `plan-details` (433 l.)**: resuelve el plan server-side por `$editingId` con eager loading propio; secciones: cabecera (grado/sección/fechas/tiempo), Diagnóstico, Proyecto vinculado, **Observación [Coord. Evaluación]** (badge-info), Tabla Resumen (`getOrderedSummaries`: Área `pevaluacion->asignatura->name`, Componente, Objetivo, Aprendizaje Esperado, Indicadores, Línea, Énfasis), y **Matriz de Estrategias** (momentos × días con `getStrategyByMomentAndDay`). **Sin botón PDF** (el PDF vive en el dropdown de la raíz).
- **bwk `plan-details` (433 l.)**: copia del semanal con fixes: **corrige un label erróneo del wk**; sin bloque de proyecto.
- **projectk `project-details` (486 l.)**: cabecera + Diagnóstico + Tabla Revisión (`getOrderedViews()`: Posibles Temas, Elección del Tema, Qué Sabe, Qué Desean Aprender, Qué Necesitamos, Quiénes Apoyan, Estrategias) + Tabla Resumen + Matriz de Estrategias. **Sin botón PDF**.
- **especialk `plan-details` (319 l.) y `details` (319 l.)**: `details` es **copia byte-idéntica de plan-details** (huérfana). ⚠️ **especialk SÍ tiene botón PDF** (`inicials.eispecialks.format.index`) en plan-details — el único detalles con PDF embebido (inconsistencia con wk/projectk).

### 4.4 Hijos tipo lista (summary / review / activity / position)

- **`summary-manager` (90 l., ×4 entidades idénticas)**: tabla de `getOrderedSummaries()` + botón "Agregar" (abre `edit-summary` con FK de contexto) + editar/eliminar por fila (`deleteSummary(id)`); badge de order.
- **`summary-form` (111 l.)**: lapso (select → `updatedLapsoId` recarga `$list_pevaluacion`) · pevaluacion_id · componente · objetivo · aprendizaje_esperado · indicadores · linea_investigacion · enfasis_curriculares · **order** (`Form::selectRange('order', 1, 20)` + null "S/N" — único campo con required HTML en todo el módulo). Etiquetas desde `$list_comment_summary`. Reabre el manager tras guardar.
- **`review-form` (106 l., projectk)**: "revisión" = **planificación dialogada con los niños** (no del coordinador): posibles_temas_interes, eleccion_tema_nombre, que_sabe, que_desean_aprender, que_necesitamos, quienes_nos_pueden_apoyar + order. ⚠️ Bug: atributo duplicado `rows="3" rows="3"`.
- **`activity-form` (111 l., especialk)**: espejo de summary-form para `Eispecialact` (justifica el rename "activity").
- **`position-form` (126 l., evaluationk)**: 9 campos (`eievaluationk_id` resuelto server-side desde `$eievaluationk_id` — no es input; pevaluacion_id, fecha **type=text (bug: sin date picker)**, nombre_ninos, aprendizaje_alcanzado, componente, indicadores, instrumento, observacion, order).
- **`position-manager` (94 l.)**: **query-in-view** (`Eievaluationk::find($eievaluationk_id)->getOrderedEvaluationps()`), `deletePosition` con confirm inline, badge "S/N" para order null.
- **`evaluation-details` (207 l.)**: 5 bloques (cabecera, Observaciones, **Recomendación [Coord. Evaluación] en alert read-only con `updated_at`**, Asistencia, Posiciones) + ⚠️ **enlace PDF muerto** (`href="#"`).

### 4.5 Hijos tipo grid — `strategy-form` / `strategy-manager` / `strategy-progress`

- **`strategy-form` (wk 301 / bwk 438 / projectk 246 / especialk 246 l.) — wizard de 50 celdas**: nav-tabs de días (`$weekDays`) + pills de momentos (`$moments`); textarea `wire:model="strategies.{$activeDay}.{$activeMoment}.estrategia"`; contadores del día (`getDayProgress`); botones `previousMoment()`/`nextMoment()` ("Momento N de 10"); submit **"Guardar y Continuar"** (`saveCurrentStrategy()` — guarda la celda y avanza) y **"Guardar Todas"** (`saveStrategies()` — persiste el grid completo). El formulario bindea `$strategies[día][momento] = ['id', 'estrategia', 'order']`.
- **`strategy-manager` (88–109 l.) — tabla alternativa** (solo wk lo enruta): filas de `getOrderedStrategies()` con momento, día, texto y acciones. ⚠️ **BUG de wiring**: invoca `deleteStrategy({$strategy->id})` con **1 argumento** pero la firma del componente es `deleteStrategy($day, $momento)` → llamada silenciosamente no-op (ver 03 §8.2). En **bwk/projectk/especialk el manager está HUÉRFANO** (main-modal enruta `strategy` directo al strategy-form).
- **`strategy-progress` (191 l., projectk+especialk)**: vista de progreso del grid — **criterio "celda completada" ⇔ `!empty(estrategia)`** (criterio de texto, distinto del "registro existente" usado en las tarjetas raíz — inconsistencia de criterios).
- ⚠️ **`eiplanningbwk/strategy-form` (438 l.)** duplica el wizard pero el main-modal de bwk **NO lo usa** (usa... ver §4.1: bwk enruta `strategy`→strategy-form — el manager es el huérfano).

### 4.6 `eifinalk/create.blade.php` (250 l.) — overlay, no modal

- **Overlay custom `position-fixed`** (no usa las clases `modal` de Bootstrap): div full-viewport con panel scrollable; cabecera con submit dinámico (`$selected_id ? 'Actualizar Informe' : 'Guardar Informe'` → `update()`/`save()`).
- **1 tab activa ("Datos del Informe") + 4 tabs COMENTADAS** en el código — incluida la del acordeón de expectativas (`selected_expectations`): **el flujo de expectativas existe en el componente pero está parcialmente desactivado en la UI** (ver 03 §8.9).
- Campos condicionales por `status_official`: oficial → `expected_learnings` / `achievements` / `individual_observations`; componente de formación → `specialist_observation`. ⚠️ **BUG l.111**: `$pevaluacion` no es propiedad del componente — variable derivada en Blade (undefined en algunos paths).
- 15 campos del informe (title, context_group, planing_eject, featured_project, special_activities, achievements, family_participation, individual_observations, conclusions, recommendations, expected_learnings, specialist_observation…) con labels de `Eifinalk::COLUMN_COMMENTS`.

## 5. Vistas índice del docente (`inicials/{entidad}/index.blade.php` × 6)

**6 wrappers idénticos de 52–53 líneas** (`eiplanningwks`, `eiplanningbwks`, `eiprojectks`, `eispecialks`, `eievaluationks`, `eifinalks`):

- `@extends('inicials.layouts.dashboard.app')` + `@section('main')`.
- Título + `<livewire:inicial.{entidad}-component />` (eifinalks pasa además `:lapsos` y `Lapso::current()`).
- Script con **3 listeners** de eventos browser → `window.livewire.emit(...)`:
  - `swal:confirm` → `emit('remove', e.detail.id)` (borrado tras confirmación SweetAlert).
  - `swal:question` → `emit(e.detail.method, e.detail.id)`.
  - `swal` → toast.

Otras vistas raíz del docente: `home.blade.php` (jumbotron "Educación Inicial — Formatos para la Planificación y Evaluación." + lista de formatos + scripts Chart.bundle) · `use-cases.blade.php` (nav-pills + `.mermaid`, 7 casos — doc 06) · `card/profesor.blade.php` (86 l. — tarjeta de perfil del docente con avatar/username/email/fullname/CI/ti_teacher/nacimiento/dirección/teléfonos vía `Auth::user()->profesor`, incluye un `CREATE TABLE` SQL comentado).

## 6. Layouts (`inicials/layouts/`)

- **`app.blade.php` (92 l.) — shell base:** `<html>` con **Bootstrap 4.3.1, jQuery 3.3.1, Alpine 3.11.1 (defer), SweetAlert2 11.4.8** + `@livewireStyles`/`@livewireScripts`; navbar dark sticky (color por `Session::get('pescolar_color')`: #004000 SAEFL / #FF0000 SAEFL.DEV); `@yield('main')`; `@stack('stylesheet')`/`@stack('scripts')`.
- **`dashboard/app.blade.php`:** 2 columnas — `col-md-2` con `@includeif('inicials.card.profesor')` + `col-md-10` con `@yield('main')`; script reloj() cada 60 s; include del navbar.
- **`dashboard/navbar/app.blade.php`:** 8 botones de navegación (tabla completa de rutas en [`07-perspectivas-roles.md`](07-perspectivas-roles.md) §8.2) con activo por `Request::is()`; dropdown usermenu + logout POST; "PE: Session::get('pescolar_name')"; `#reloj`. ⚠️ HTML malformado: 4 `</li>` duplicados.
- **`dashboard/sidebar/app.blade.php`:** HUÉRFANO — copy-paste del sidebar de administracion con `includeWhen` por URL.
- **`footer/`:** firma "NoeMDB".

## 7. `inicials/elements/` (18 archivos)

Subdirectorios: `boxes`, `canvas`, `card`, `chart`, `forms`, `messeges` (⚠️ typo de "messages"), `progress`, `tabs`. Elementos con LaravelCollective `Form::`. ⚠️ Typos internos: `buttomtext`, `goal_ammount`, `messeges`. Corresponden al kit de elementos heredado de otros módulos del legacy; los formularios vivos del módulo no dependen de ellos.

## 8. La capa muerta: `table/` → `overlay/` → `forms/` (HUÉRFANA — verificada por grep)

**Verificación exhaustiva** (grep de referencias `inicial.table` / `inicial.overlay` / `inicial.forms` en todo el árbol de vistas y en `app/`):

1. **`table/` (5 vistas, 70–114 l.):** **NADIE las incluye** (ni vistas ni componentes PHP). Eran la "primera generación" de UI: tabla de registros + `@includeWhen($show, 'livewire.inicial.overlay.{entidad}.{tipo}')` para abrir los formularios como overlays.
2. **`overlay/` (21 archivos, 21–22 l. c/u):** solo las incluye la capa `table/` (muerta). Patrón: `<div class="overlay">` + header `alert-primary` con título y **× (`wire:click="close()")** + `@include('livewire.inicial.forms.{entidad}.fields')` + `Form::button('Guardar', ['wire:click' => 'save()'])`. ⚠️ Llaman a `close()` que **no existe** en los componentes actuales (usan `closeModal()`) — wiring roto de la generación abandonada.
3. **`forms/` (11 archivos):** solo las incluyen los overlays (muertos). `forms/strategy/fields.blade.php` y `forms/summary/fields.blade.php` son **compartidos por wk/bwk/especialks pero bindean siempre `eiplanningwstrategy.*`** (prefijo del modelo semanal — bug para bwk/especialk); `selectRange('order', 1, 20)` como único required HTML; `forms/eispecialk/review.blade.php` tiene **refs rotas a `eiprojectreview.*`** (copy-paste); `forms/eiplanningbwk/strategy-form.blade.php` es copia byte-a-byte huérfana.
4. `partials/eiplanningwks/create.blade.php`: **archivo vacío (0 bytes)**.

**Conclusión de arquitectura:** el módulo pasó por **dos generaciones de UI**: (1ª) tablas + overlays + forms compartidos (abandonada, wiring roto); (2ª) tarjetas + modal único `$modalType` + forms propios por entidad (la viva). Solo el modal de eifinalk conserva el estilo overlay.

## 9. Bugs y hallazgos consolidados de UI

| # | Hallazgo | Dónde |
|---|---|---|
| 1 | Capa `table/overlay/forms` completa huérfana con wiring roto (`close()` inexistente) | §8 |
| 2 | `bwk plan-form` evalúa `$eiplanningwk` ajeno → siempre "No hay observaciones" | §4.2 |
| 3 | `strategy-manager` llama `deleteStrategy(id)` con firma `(day, momento)` → no-op | §4.5 / 03 §8.2 |
| 4 | bwk: dropdown sin "Gestionar Estrategias"; manager huérfano; `edit-strategy` llama `loadStrategy()` inexistente | §3.2 / 03 §8.1 |
| 5 | `eispecialk/details` copia byte-idéntica de `plan-details`; `forms/eispecialk/review` con refs rotas `eiprojectreview.*` | §4.3 / §8 |
| 6 | Inconsistencia PDF en detalles: especialk SÍ lo tiene; wk/projectk no (viven en el dropdown raíz); evaluation-details tiene `href="#"` muerto | §4.3–4.4 |
| 7 | `eifinalk/create`: 4 tabs comentadas (flujo de expectativas desactivado); `$pevaluacion` no definida (l.111); delete sin confirm en la raíz | §3.5 / §4.6 |
| 8 | bwk observación: límite de visual 200 vs check 100 | §3.2 |
| 9 | `position-form` fecha `type=text` sin date picker | §4.4 |
| 10 | `review-form` atributo duplicado `rows="3" rows="3"` | §4.4 |
| 11 | Criterio de progreso inconsistente: texto no-vacío (strategy-progress) vs registro existente (badges raíz) | §4.5 |
| 12 | Typos: `messeges`, `buttomtext`, `goal_ammount`, placeholder "Selecciones", "Actividaes", "Plan de Quincenal" | §7 y otros |
| 13 | `forms/strategy/fields` compartido bindeando siempre `eiplanningwstrategy.*` | §8 |
| 14 | HTML malformado (`</li>` duplicados) en navbar; bwk raíz con `<div>` anidado extra | §6 / §3.2 |
| 15 | `selectRange('order', 1, 20)` único required HTML de toda la capa forms (muerta) | §8 |

## 10. Recomendación para cfla

- **No portar** la capa muerta (`table/`, `overlay/`, `forms/`, `partials/` vacío, `elements/`): reconstruir solo la 2ª generación.
- Mantener la **anatomía de 5 bloques** (header card / filtros / tarjetas / modal / loading) — mapea 1:1 a Livewire 3 + componentes WireUI (`x-modal`/WireUI Modals en vez del switch `$modalType`; `wire:model.live` para cascadas; `x-select` nativo en vez de LaravelCollective).
- El **wizard de estrategias** (nav-tabs días + pills momentos + "Guardar y Continuar") es el patrón de mayor valor pedagógico — portarlo como componente propio (normalizando el quirk `lunes`, ver 02 §B.1 y 03 §6.2).
- Unificar el patrón de confirmación de borrado (raíces usan `confirm()` inline; índices usan SweetAlert `swal:confirm`; eifinalk no confirma) en un solo mecanismo Livewire 3 (`$dispatch` + WireUI dialogs).
- Activar el flujo completo de expectativas en eifinalk (tabs comentadas) y añadir date pickers (WireUI `x-datetime-picker`) a todas las fechas.
