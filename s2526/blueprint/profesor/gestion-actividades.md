# Blueprint: Gestión de Actividades del Profesor (Activity)

> **Módulo:** Profesor → Actividades  
> **Ruta base:** `/app/profesors/activities/index`  
> **Middleware:** `is_profesor` (heredado de `routes/web.php`)  
> **Propósito:** Registrar y gestionar el Plan de Actividades por cada Área de Formación (Pevaluacion) que imparte un profesor. Permite crear actividades académicas con indicadores de logro, generar reportes PDF (formato completo y resumen), clonar actividades entre secciones, y visualizar el plan de evaluación.

---

## 1. Validación Contra Código Fuente

### 1.1. Rutas

**Archivo principal:** `routes/app/tab/profesors/activities.php` (cargado por `routes/app/profesors.php` línea 27)

| Método | URI | Acción | Nombre de Ruta |
|--------|-----|--------|----------------|
| GET | `/activities/index` | `ActivityController@index` | `profesors.activities.index` |
| GET | `/activities/format/{id}` | `ActivityController@format` | `profesors.activities.format` |
| GET | `/activities/resume/{id}` | `ActivityController@resume` | `profesors.activities.resume` |
| GET | `/activities/crud` | `ActivityController@crud` | `profesors.activities.crud` |
| GET | `/activities/create/{id}` | `ActivityController@create` | `profesors.activities.create` |
| POST | `/activities/store` | `ActivityController@store` | `profesors.activities.store` |
| GET | `/activities/edit/{id}` | `ActivityController@edit` | `profesors.activities.edit` |
| PUT | `/activities/update/{id}` | `ActivityController@update` | `profesors.activities.update` |
| DELETE | `/activities/destroy/{id}` | `ActivityController@destroy` | `profesors.activities.destroy` |
| GET | `/activities/clone/{id}` | `ActivityController@clone` | `profesors.activities.clone` |
| POST | `/activities/store_clone` | `ActivityController@store_clone` | `profesors.activities.store_clone` |

**Ruta auxiliar (helpers):** `routes/helpers/profesors.php`

| Método | URI | Acción |
|--------|-----|--------|
| GET | `/helpers/profesors/activities` | `ManulasController@activities` |

**⚠️ Rutas definidas sin método en controlador:** `crud()`, `store()`, `edit()`, `update()`, `destroy()` existen en el route file pero NO tienen implementación en `ActivityController`. Causarían Error 500 si se accede a ellas.

### 1.2. Controlador

**Archivo:** `app/Http/Controllers/Profesor/Tab/ActivityController.php` (137 líneas)

| Método | Parámetros | Descripción |
|--------|-------------|-------------|
| `__construct()` | — | Middleware para obtener `$this->profesor` desde `Auth::user()` |
| `index(Request)` | Filtros: `grado_id`, `seccion_id`, `lapso_id` | Lista Pevaluacions del profesor con `planning_module=true` |
| `create($pevaluacion_id)` | ID de Pevaluacion | Vista detalle con Livewire component para CRUD de actividades |
| `format($id)` | ID de Pevaluacion | Vista HTML para PDF: todas las actividades ordenadas por `finicial` |
| `resume($id)` | ID de Pevaluacion | Vista HTML para PDF: solo actividades con `description` no nulo |
| `clone($id)` | ID de Pevaluacion | Vista para clonar Pevaluacion a otra sección (view no existe en disco) |
| `store_clone(Request)` | POST data | Duplica Pevaluacion a otra `seccion_id`, retorna JSON o redirect |

**Métodos sin implementar:** `crud()`, `store()`, `edit()`, `update()`, `destroy()`.

### 1.3. Livewire Components

**Desktop (`app/Http/Livewire/Profesor/Activity/`):**

| Componente | Archivo | Propósito |
|------------|---------|-----------|
| `IndexComponent` | `IndexComponent.php` (316 líneas) | CRUD completo: actividades + indicadores de logro |
| — ValidateTrait | `ValidateTrait.php` | Reglas de validación y nombres de atributos |

**Mobile (`app/Http/Livewire/Movile/Profesor/Activity/`):**

| Componente | Archivo | Propósito |
|------------|---------|-----------|
| `IndexComponent` | `IndexComponent.php` (51 líneas) | Contenedor móvil, gestiona modo create/index |
| `ListComponent` | `ListComponent.php` | Lista actividades paginadas (5/page), inline edit |
| `CreateComponent` | `CreateComponent.php` | Formulario de creación standalone |
| `EditComponent` | `EditComponent.php` | Stub — sin lógica implementada |
| `ActivityValidateTrait` | `ActivityValidateTrait.php` | Validación mobile (misma que desktop) |

### 1.4. Modelos

| Modelo | Archivo | Tabla | SoftDeletes | Fillable |
|--------|---------|-------|-------------|----------|
| `Activity` | `app/Models/app/Profesor/Activity.php` | `activities` | ❌ No | `pevaluacion_id`, `finicial`, `ffinal`, `topic`, `thematic`, `references`, `teaching`, `learning`, `description`, `observations`, `comments`, `status` |
| `Achievement` | `app/Models/app/Profesor/Achievement.php` | `achievements` | ❌ No | `activity_id`, `name`, `weighting`, `status_quantitative_weighting` |
| `Pevaluacion` | `app/Models/app/Profesor/Pevaluacion.php` | `pevaluacions` | ✅ Sí | `profesor_id`, `lapso_id`, `seccion_id`, `pensum_id`, `grupo_estable_id`, `status_baremo`, `status_official`, `status_note_report`, `nota_type`, `escala_id`, `objetivo`, `description`, `observations`, `category` |

### 1.5. Migraciones

| Archivo | Tabla | Tipo |
|---------|-------|------|
| `backUps/activities/2024_08_29_205213_create_activities_table.php` | `activities` | Creación |
| `backUps/activities/2024_08_29_205947_create_achievements_table.php` | `achievements` | Creación |
| `backUps/activities/2025_01_07_215619_modify_weighting_nullable_in_achievements.php` | `achievements` | Modificación: `weighting` nullable |
| `backUps/activities/2025_01_07_215809_add_status_quantitative_weighting_to_achievements.php` | `achievements` | Add `status_quantitative_weighting` boolean |

### 1.6. Vistas

**Blade views (`resources/views/profesors/activities/`):**

| Archivo | Propósito |
|---------|-----------|
| `index.blade.php` | Layout principal: título + search partial + DataTable |
| `create.blade.php` | Sidebar resumen + Livewire component |
| `format.blade.php` | PDF: Plan de Actividades completo (9 columnas) |
| `resume.blade.php` | PDF: Resumen del Plan de Actividades (6 columnas) |
| `menus/index.blade.php` | Botones de navegación para index |
| `menus/create.blade.php` | Botones de navegación para create |
| `table/index.blade.php` | DataTable de Pevaluacions con acciones |
| `partials/search.blade.php` | Filtros: grado, sección, lapso + AJAX cascada |
| `partials/resumen.blade.php` | Sidebar metadata: grado, sección, lapso, asignatura, profesor, escala, objetivo |

**Livewire views (`resources/views/livewire/profesor/activity/`):**

| Archivo | Propósito |
|---------|-----------|
| `index-component.blade.php` | Observaciones, lista de actividades con inline edit + achievements |
| `form/fields.blade.php` | Campos del formulario (finicial, ffinal, topic, thematic, references, teaching, learning, description, observations) |
| `partials/create.blade.php` | Wrapper overlay del formulario + botón Guardar |

---

## 2. Reglas de Negocio

### RN-01: Acceso por Profesor
Cada profesor solo ve sus propias Pevaluacions (actividades de las áreas de formación que imparte). Filtro base: `pevaluacions.profesor_id = $profesor->id`.

### RN-02: Módulo de Planificación
Solo se listan Pevaluacions cuyo `Pestudio` tenga `planning_module = true` y `status_active = 'true'`. Esto asegura que solo aparezcan áreas de formación activas con módulo de planificación habilitado.

### RN-03: Estructura Jerárquica
```
Pevaluacion (Plan de Evaluación)
  ├── Activity (Actividades del Plan)
  │     └── Achievement (Indicadores de Logro)
  └── Achievement (vía hasManyThrough)
```

### RN-04: Fechas de Actividad
Toda actividad requiere `finicial` (fecha inicio) y `ffinal` (fecha fin). No hay validación de que `finicial <= ffinal` a nivel servidor.

### RN-05: Estado de Aprobación
El campo `status` (boolean, default false) indica si la actividad está aprobada. Se muestra "APROBADO" vs "EN REVISIÓN" en los reportes PDF.

### RN-06: Resumen del Plan
Solo actividades con `description` no nulo aparecen en el resumen (`format()` incluye todas, `resume()` solo las que tienen `description`). El accessor `getStatusResumeAttribute()` determina si una actividad forma parte del resumen.

### RN-07: Bloqueo por Precierre
Cuando el lapso tiene `status_preclosing = true` (fecha de precierre alcanzada), se deshabilita la edición: no se pueden crear, editar ni eliminar actividades. La UI deshabilita botones con `{{ $enable_edit ? null : 'disabled' }}`.

### RN-08: Clonación entre Secciones
Las actividades de una sección pueden clonarse a otra sección del mismo grado dentro del mismo Pevaluacion. La clonación copia actividades + achievements, reseteando `comments` a null. La clonación también está disponible desde el Livewire IndexComponent (vía selector de sección).

### RN-09: Eliminación de Actividades con Dependencias
No se puede eliminar una actividad si tiene achievements asociados (el botón se deshabilita con `$disabled = ($achievements->count() > 0 || ! $enable_edit)`).

### RN-10: Ponderación de Indicadores
Los achievements pueden tener `status_quantitative_weighting` booleano. Si es true, requieren `weighting` (entero 1-20, nullable). La suma total de ponderaciones se muestra en la UI.

### RN-11: Palabras Significativas en Enseñanza
El método `teachingWordsMayorCount(int $num = 3): int` cuenta palabras del campo `teaching` con más de `$num` letras (usado para indicador de calidad textual).

---

## 3. SQL Schema

### Tabla `activities`

| Columna | Tipo | Requerido | Default | FK | Comentario |
|---------|------|-----------|---------|----|------------|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT | ✅ | — | — | |
| `pevaluacion_id` | BIGINT UNSIGNED | ✅ | — | `pevaluacions.id` ON DELETE CASCADE | Plan de Evaluación |
| `finicial` | DATE | ✅ | — | — | Fecha Inicial |
| `ffinal` | DATE | ✅ | — | — | Fecha Final |
| `topic` | TEXT | ❌ | NULL | — | Tema |
| `thematic` | TEXT | ❌ | NULL | — | Tejido temático |
| `references` | TEXT | ✅ | — | — | Referentes teórico prácticos |
| `teaching` | TEXT | ✅ | — | — | Enseñanza |
| `learning` | TEXT | ✅ | — | — | Aprendizaje |
| `description` | TEXT | ❌ | NULL | — | Descripción |
| `observations` | TEXT | ✅ | — | — | Observaciones |
| `comments` | TEXT | ❌ | NULL | — | Comentarios |
| `status` | BOOLEAN/TINYINT | ❌ | false | — | Aprobación |
| `created_at` | TIMESTAMP | ❌ | NULL | — | |
| `updated_at` | TIMESTAMP | ❌ | NULL | — | |

**Índices:** PK en `id`; FK en `pevaluacion_id`

### Tabla `achievements`

| Columna | Tipo | Requerido | Default | FK | Comentario |
|---------|------|-----------|---------|----|------------|
| `id` | BIGINT UNSIGNED AUTO_INCREMENT | ✅ | — | — | |
| `activity_id` | BIGINT UNSIGNED | ✅ | — | `activities.id` ON DELETE CASCADE | Actividad |
| `name` | VARCHAR(255) | ✅ | — | — | Nombre |
| `weighting` | DECIMAL(4,0) | ❌ | NULL | — | Ponderación |
| `status_quantitative_weighting` | BOOLEAN/TINYINT | ❌ | true | — | Indicador cuantitativo |
| `created_at` | TIMESTAMP | ❌ | NULL | — | |
| `updated_at` | TIMESTAMP | ❌ | NULL | — | |

**Índices:** PK en `id`; FK en `activity_id`

---

## 4. Endpoints API REST (Propuesta Migración)

### Colección: `/api/v1/profesor/activities`

| Método | Endpoint | Descripción | Query Params |
|--------|----------|-------------|--------------|
| GET | `/profesor/activities` | Listar actividades del profesor autenticado | `?pevaluacion_id`, `?grado_id`, `?seccion_id`, `?lapso_id`, `?page`, `?per_page` |
| GET | `/profesor/activities/{id}` | Obtener una actividad con achievements | — |
| POST | `/profesor/activities` | Crear nueva actividad | — |
| PUT | `/profesor/activities/{id}` | Actualizar actividad existente | — |
| DELETE | `/profesor/activities/{id}` | Eliminar actividad (solo sin achievements) | — |
| DELETE | `/profesor/activities/empty/{pevaluacion_id}` | Eliminar todas las actividades de un Pevaluacion | — |

### Colección: `/api/v1/profesor/achievements`

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/profesor/achievements?activity_id=` | Listar achievements de una actividad |
| POST | `/profesor/achievements` | Crear nuevo achievement |
| PUT | `/profesor/achievements/{id}` | Actualizar achievement |
| DELETE | `/profesor/achievements/{id}` | Eliminar achievement |

### Colección: `/api/v1/profesor/pevaluacions`

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/profesor/pevaluacions` | Listar Pevaluacions del profesor (con filtros) |
| GET | `/profesor/pevaluacions/{id}` | Obtener Pevaluacion con actividades y achievements |
| POST | `/profesor/pevaluacions/{id}/clone` | Clonar Pevaluacion a otra sección |

### Endpoints Auxiliares

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| GET | `/profesor/activities/{id}/format` | Generar PDF (Formato completo) |
| GET | `/profesor/activities/{id}/resume` | Generar PDF (Resumen) |
| GET | `/profesor/activities/teaching-words/{id}?min=` | Obtener `teachingWordsMayorCount` |

### Request Body (POST/PUT):

```json
{
  "pevaluacion_id": 1,
  "finicial": "2026-09-01",
  "ffinal": "2026-09-15",
  "topic": "Tema generador y Énfasis",
  "thematic": "Tejido temático / Tema Indispensable",
  "references": "Referentes teórico prácticos y éticos",
  "teaching": "Enseñanza/Actividad Globalizada",
  "learning": "Aprendizaje",
  "description": "Actividad Evaluativa (nullable)",
  "observations": "ODS / Sistematización"
}
```

---

## 5. UI Wireframes

### 5.1. Pantalla Index (Lista de Pevaluacions)

```
┌──────────────────────────────────────────────────────────────┐
│ [i] Módulo Plan de Actividades           Diseñado por: ...  │
├──────────────────────────────────────────────────────────────┤
│ ┌──────────────────────────────────────────────────────────┐ │
│ │ Grado: [▼]    Sección: [▼]    Lapso: [▼]    [Buscar]   │ │
│ └──────────────────────────────────────────────────────────┘ │
│                                                               │
│ Listado de Áreas de Formación [Prof: Juan Pérez]              │
│ ┌──────────────────────────────────────────────────────────┐ │
│ │ N │ Asignatura/Grado/Sec │ Cant.Act │ Cant.Ind │ Lapso │ │
│ ├──────────────────────────────────────────────────────────┤ │
│ │ 1 │ Biología 5to A       │    12    │    35    │ I    │ │
│ │   │ [PEM]                │          │          │      │ │
│ │   │ [📝][📄][📋]       │          │          │      │ │
│ │ 2 │ Química 5to B        │     8    │    24    │ I    │ │
│ │   │ [PEM]                │          │          │      │ │
│ │   │ [📝][📄][📋]       │          │          │      │ │
│ └──────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘
```

**Botones de acción por fila:**
- 📝 (info/outline-info): Registrar Actividades → `/activities/create/{id}`
- 📄 (secondary): Resumen PDF → `/activities/resume/{id}` (target _BLANK)
- 📋 (success): Plan Completo PDF → `/activities/format/{id}` (target _BLANK)

### 5.2. Vista Create (Detalle Pevaluacion + Livewire)

```
┌──────────────────────────────────────────────────────────────┐
│ [i] Módulo Plan de Actividades           Diseñado por: ...  │
├──────────────────────────────────────────────────────────────┤
│ ┌──────────┐ ┌─────────────────────────────────────────────┐ │
│ │ Resumen  │ │ Observación [Coord.Eval.]                   │ │
│ │          │ │ "..."                                       │ │
│ │ Grado    │ │ [📄][📋][🗑️][⚙️ Clonar] [+ Nuevo]         │ │
│ │ Lapso    │ ├─────────────────────────────────────────────┤ │
│ │ Área     │ │ Actividades registradas                     │ │
│ │ Profesor │ │ ┌─────────────────────────────────────────┐ │ │
│ │ Escala   │ │ │1. Tema generador...                     │ │ │
│ │ Objetivo │ │ │   [✏️][🗑️]                             │ │ │
│ │ ...      │ │ │   Indicadores:                          │ │ │
│ │          │ │ │   - Indicador 1 [3] [+ Nuevo]          │ │ │
│ └──────────┘ │ │   - Indicador 2 [3]                     │ │ │
│              │ │───────────────────────────────────────────│ │ │
│              │ │2. Tema generador...                       │ │ │
│              │ │   [✏️][🗑️][disabled]                    │ │ │
│              │ └─────────────────────────────────────────┘ │ │
│              └─────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘
```

### 5.3. Modal / Overlay Crear Actividad

```
┌──────────────────────────────────────────────────────────┐
│ Registro de la Actividad                           [✕]  │
├──────────────────────────────────────────────────────────┤
│ ┌──────────────────────┐ ┌────────────────────────────┐ │
│ │ Fecha Inicial        │ │ Fecha Final                │ │
│ │ [📅 2026-09-01]      │ │ [📅 2026-09-15]            │ │
│ └──────────────────────┘ └────────────────────────────┘ │
│ ┌──────────────────────┐ ┌────────────────────────────┐ │
│ │ Actividad Evaluativa │ │ Tema generador y Énfasis   │ │
│ │ [textarea rows=2]   │ │ [textarea rows=2]          │ │
│ └──────────────────────┘ └────────────────────────────┘ │
│ ┌──────────────────────┐ ┌────────────────────────────┐ │
│ │ Tejido temático      │ │ Referentes teórico-práct. │ │
│ │ [textarea rows=3]   │ │ [textarea rows=3]          │ │
│ └──────────────────────┘ └────────────────────────────┘ │
│ ┌──────────────────────┐ ┌────────────────────────────┐ │
│ │ Enseñanza/Act.Global.│ │ Aprendizaje                │ │
│ │ [textarea rows=6]   │ │ [textarea rows=6]          │ │
│ └──────────────────────┘ └────────────────────────────┘ │
│ ┌──────────────────────────────────────────────────────┐ │
│ │ ODS / Sistematización                                │ │
│ │ [textarea rows=3]                                   │ │
│ └──────────────────────────────────────────────────────┘ │
│                                                           │
│ [Guardar]                                                  │
└──────────────────────────────────────────────────────────┘
```

---

## 6. Component Tree / Estados por Componente

### 6.1. Árbol (Index — Lista de Pevaluacions)

```
ActivityController@index
├── SearchForm (GET filter)
│   ├── Grado Select
│   ├── Seccion Select (AJAX populate on grado change)
│   └── Lapso Select
├── PevaluacionDataTable
│   ├── Fila Pevaluacion
│   │   ├── Asignatura/Grado/Seccion display
│   │   ├── Cant.Act (activities count)
│   │   ├── Cant.Ind (achievements count)
│   │   ├── Lapso display
│   │   └── ActionButtons
│   │       ├── [Registrar Actividades] → create(id)
│   │       ├── [Resumen PDF] → resume(id)
│   │       └── [Plan PDF] → format(id)
│   └── DataTables jQuery (client-side search/sort/paginate)
└── destroy form + JS (models/pevaluacions/destroy.js)
```

### 6.2. Árbol (Create — Detalle Pevaluacion + Livewire)

```
ActivityController@create(id)
├── Sidebar (partials/resumen)
│   ├── ID (solo admin)
│   ├── Grado + Sección
│   ├── Lapso
│   ├── Área de Formación
│   ├── Profesor
│   ├── Tipo de nota final
│   ├── Nota - Escala
│   ├── Objetivo
│   ├── Descripción
│   ├── Observación [Coord.Eval.]
│   └── Fecha de Precierre
│
└── Livewire: IndexComponent (pevaluacion_id)
    ├── Alert (Observación Coord.Eval.)
    │   ├── Observación text
    │   ├── PDF Resume btn (resume route)
    │   ├── PDF Format btn (format route)
    │   ├── [Empty All Activities] (disabled si hay obs o precierre)
    │   └── Loading spinner (wire:loading)
    │
    ├── [Modal Create Overlay] (modeCreator = true)
    │   └── Form Fields (form/fields)
    │       ├── finicial (date)
    │       ├── ffinal (date)
    │       ├── description (textarea)
    │       ├── topic (textarea)
    │       ├── thematic (textarea)
    │       ├── references (textarea)
    │       ├── teaching (textarea)
    │       ├── learning (textarea)
    │       └── observations (textarea)
    │   └── [Guardar] button
    │
    ├── Header "Actividades registradas"
    │   ├── CloneSelect (solo si activities empty)
    │   │   ├── Seccion dropdown
    │   │   └── [Clone] button
    │   └── [+ Nuevo] button
    │
    └── ActivityList
        ├── ActivityItem (por cada activity)
        │   ├── Status badge (status_resume)
        │   ├── Topic display
        │   ├── Thematic display
        │   ├── Teaching display
        │   ├── Learning display
        │   ├── Comments display [J.Área]
        │   ├── [Editar] button (→ overlay edit)
        │   ├── [Eliminar] button (disabled si tiene achievements)
        │   │
        │   └── AchievementList
        │       ├── AchievementItem
        │       │   ├── Name + [weighting] si quantitative
        │       │   ├── [✏️ Edit] icon (wire:click)
        │       │   └── [🗑️ Delete] icon (wire:click)
        │       ├── [Agregar indicador] button
        │       └── Total Ponderaciones
        │
        └── [Empty State] "No hay datos" (li disabled)
```

### 6.3. Estados del Livewire IndexComponent

| Estado | Condición | Comportamiento |
|--------|-----------|----------------|
| **Loading** | `wire:loading` | Muestra "Procesando..." spinner en header |
| **Empty** | `$activities->isEmpty()` | Muestra "No hay datos" + Clonar selector visible |
| **With Activities** | `$activities->isNotEmpty()` | Lista actividades con inline edit |
| **Create Mode** | `$modeCreator = true` | Overlay formulario crear actividad |
| **Edit Mode** | `$modeEdit && $activity_id == item.id` | Overlay formulario editar actividad |
| **Achievement Create Mode** | `$modeCreatorAchievement = true` | Overlay formulario crear/editar achievement |
| **Disabled (Precierre)** | `$enable_edit = false` | Todos los botones disabled |
| **Error (Clone no sección)** | `!$seccion` | Swal error "Seleccione una sección" |
| **Error (Clone sin activities)** | `$activities->isEmpty()` | Swal error "NO se registró ninguna operación" |
| **Success (Clone)** | Clone completado | Swal success "Registro realizado exitosamente" |
| **Success (Save)** | Save completado | Swal success + close overlay |
| **Error (Empty delete)** | 0 rows deleted | Swal error "NO se eliminó ningún registro" |

---

## 7. Plan de Migración (Laravel → NextJS + API)

### Fase 1: API REST

| Paso | Acción | Archivos/Dependencias |
|------|--------|----------------------|
| 1.1 | Crear endpoint `GET /api/v1/profesor/pevaluacions` con filtros | Controller + FormRequest |
| 1.2 | Crear endpoint `GET/POST /api/v1/profesor/activities` | Controller + FormRequest |
| 1.3 | Crear endpoint `PUT/DELETE /api/v1/profesor/activities/{id}` | Controller |
| 1.4 | Crear endpoint `GET/POST/PUT/DELETE /api/v1/profesor/achievements` | Controller |
| 1.5 | Crear endpoint `POST /api/v1/profesor/pevaluacions/{id}/clone` | Controller |
| 1.6 | Crear endpoint `DELETE /api/v1/profesor/activities/empty/{pevaluacion_id}` | Controller |
| 1.7 | Agregar validación server-side en todos los endpoints | FormRequest rules |
| 1.8 | Implementar `teachingWordsMayorCount` en capa service | Service class |
| 1.9 | Migrar `COLUMN_COMMENTS` a constantes API (i18n) | Lang files |

### Fase 2: Frontend NextJS

| Paso | Acción | Componente |
|------|--------|------------|
| 2.1 | Crear layout profesor con sidebar | `profesor/layout.tsx` |
| 2.2 | Crear página Index con filtros + tabla | `profesor/activities/page.tsx` |
| 2.3 | Crear página Create/Detail con sidebar + lista actividades | `profesor/activities/[id]/page.tsx` |
| 2.4 | Crear componente ActivityForm (overlay modal) | `profesor/activities/activity-form.tsx` |
| 2.5 | Crear componente AchievementList con inline edit | `profesor/activities/achievement-list.tsx` |
| 2.6 | Crear componente CloneSelector | `profesor/activities/clone-selector.tsx` |
| 2.7 | Migrar lógica de clonación a cliente | API call + refresh |
| 2.8 | Implementar estados loading/empty/error/disabled | Component states |
| 2.9 | Migrar alertas SweetAlert a toast component | UI library |
| 2.10 | Implementar manejo de `enable_edit` (precierre) | Conditional rendering |

### Fase 3: PDF Generation

| Paso | Acción |
|------|--------|
| 3.1 | Migrar `format.blade.php` a PDF service en API (jsPDF/PDFKit) |
| 3.2 | Migrar `resume.blade.php` a PDF service (versión condensada) |
| 3.3 | Endpoint `GET /api/v1/profesor/activities/{id}/pdf?type=full\|resume` |

### Fase 4: Mobile

| Paso | Acción |
|------|--------|
| 4.1 | Migrar mobile Livewire components a React Native components |
| 4.2 | Adaptar endpoints para consumo mobile (paginación, filtros) |
| 4.3 | Implementar modo offline reducido |

---

## 8. Edge Cases y Validación

### 8.1. Validación Actual (Livewire ValidateTrait)

| Campo | Regla | En API |
|-------|-------|--------|
| `activity.pevaluacion_id` | `required\|integer` | `required\|integer\|exists:pevaluacions,id` |
| `activity.finicial` | `required\|date` | `required\|date\|before_or_equal:ffinal` |
| `activity.ffinal` | `required\|date` | `required\|date\|after_or_equal:finicial` |
| `activity.topic` | `required\|string` | `required\|string\|max:65535` |
| `activity.thematic` | `required\|string` | `required\|string\|max:65535` |
| `activity.references` | `required\|string` | `required\|string\|max:65535` |
| `activity.teaching` | `nullable\|string` | `nullable\|string\|max:65535` |
| `activity.learning` | `nullable\|string` | `nullable\|string\|max:65535` |
| `activity.observations` | `required\|string` | `required\|string\|max:65535` |
| `activity.description` | `nullable\|string` | `nullable\|string\|max:65535` |
| `achievement.name` | `required\|min:6` | `required\|string\|min:6\|max:255` |
| `achievement.weighting` | `nullable\|numeric\|min:0\|max:100` | `nullable\|numeric\|min:0\|max:100` |
| `achievement.status_quantitative_weighting` | `nullable\|boolean` | `nullable\|boolean` |

### 8.2. Edge Cases Detectados

| # | Caso | Estado Actual | Acción Requerida |
|---|------|---------------|------------------|
| 1 | Actividad sin `description` | Se crea correctamente, no aparece en resumen | Comportamiento válido |
| 2 | `finicial > ffinal` | Sin validación, se guarda igual | Agregar `before_or_equal` / `after_or_equal` |
| 3 | Actividad sin `teaching` ni `learning` | Se crea (nullable), `teachingWordsMayorCount()` retorna 0 | Válido, pero mostrar advertencia |
| 4 | Editar después de precierre | `$enable_edit = false`, botones deshabilitados | Validar server-side también |
| 5 | Achievement sin `weighting` y `status_quantitative_weighting=false` | Se guarda sin peso | Válido |
| 6 | Achievement sin `status_quantitative_weighting` | Default true en migración | Decidir default en API |
| 7 | Múltiples clonaciones rápidas | Sin transacción ni lock, posible race condition | Agregar DB::transaction() en API |
| 8 | Pevaluacion eliminada (soft delete) | Livewire usa `findOrFail`, no existe | Retornar 404 con mensaje claro |
| 9 | Eliminar actividad con achievements | Botón deshabilitado, no se puede | Validar con `$activity->achievements()->exists()` |
| 10 | DataTable sin paginación server-side | `$pevaluacions->get()` carga todo | Usar `paginate()` en API |
| 11 | Rutas sin controlador (`crud`, `store`, `edit`, `update`, `destroy`) | Causarían 500 | Decidir si implementar o eliminar rutas |
| 12 | `clone.blade.php` no existe | `clone()` controller renderiza view inexistente | Eliminar método o implementar vista |

### 8.3. Checklist de Validaciones (Migración API)

- [ ] `POST /activities`: Validar `finicial <= ffinal` con `before_or_equal:ffinal`
- [ ] `POST /activities`: Validar `pevaluacion_id` exists + pertenece al profesor autenticado
- [ ] `POST /achievements`: Validar `activity_id` exists + pertenece al profesor
- [ ] `PUT /activities/{id}`: Validar que el profesor sea dueño de la pevaluacion
- [ ] `DELETE /activities/{id}`: Validar que no tenga achievements asociados
- [ ] `DELETE /activities/empty/{pevaluacion_id}`: Validar precierre no alcanzado
- [ ] `POST /profesor/pevaluacions/{id}/clone`: Validar seccion destino dentro del mismo grado
- [ ] `GET /profesor/activities`: Retornar 404 si pevaluacion_id no existe
- [ ] `POST /profesor/activities`: Verificar `enable_edit` server-side (fecha precierre)

---

## 9. Dependencias e Integraciones

| Componente/Sistema | Tipo | Naturaleza |
|-------------------|------|------------|
| Pevaluacion (modelo) | Relación directa | FK `pevaluacion_id` |
| Achievement (modelo) | Sub-módulo | belongsTo Activity |
| Lapso (modelo) | Indirecto | `pevaluacion.lapso` para `enable_edit` |
| Pensum (modelo) | Indirecto | Via pevaluacion.pensum |
| Grado (modelo) | Indirecto | Via pevaluacion.pensum.grado |
| Seccion (modelo) | Indirecto | Via pevaluacion.seccion |
| Asignatura (modelo) | Indirecto | Via pevaluacion.pensum.asignatura |
| Profesor (modelo) | Indirecto | Via pevaluacion.profesor |
| Pestudio (modelo) | Indirecto | Via pevaluacion.pensum.pestudio (filtro `planning_module`) |
| Inscripcion (modelo) | Indirecto | Via `grado->getSeccionsActiveInscriptionAffect()` |
| Coord.Eval. (observations) | Dato externo | Muestra observación de Pevaluacion |
| PDF rendering | Vista HTML | `format.blade.php` + `resume.blade.php` sin librería PDF |
| DataTables jQuery | UI | Tabla client-side en index |
| SweetAlert2 | UI | Alertas de éxito/error en Livewire |
| **Mobile components** | Paralelo | 4 Livewire components para Android |

---

## 10. Comparación con Módulos Anteriores

| Característica | Control (Config) | Planning (Actividades) | **Profesor Actividades** |
|----------------|-----------------|----------------------|--------------------------|
| Livewire CRUD | 1/9 (Baremos) | 4/4 | **✅ (Livewire puro)** |
| Validación Server-side | 5/9 sin | ✅ Nativa Livewire | **✅ (ValidateTrait)** |
| SoftDeletes | Inconsistente | No aplica | **❌ (eliminación física)** |
| DataTables jQuery | 9/9 | ❌ | **✅ (index)** |
| Modal CRUD / Overlay | 4/9 | ❌ | **✅ (overlay inline)** |
| PDF Generation | 1/9 (Pensums) | ❌ | **✅ (2 vistas PDF)** |
| Sub-módulo anidado | 2/9 (Census, Campo) | ❌ | **✅ (Achievements)** |
| COLUMN_COMMENTS | 5/9 | ❌ | **✅ (Activity + Achievement)** |
| Archivos huérfanos | 15+ | Mínimo | **⚠️ `clone.blade.php` ausente** |
| Mobile soporte | ❌ | ❌ | **✅ (Android Livewire)** |
| Método crítico compartido | `Lapso::current()` | — | **`teachingWordsMayorCount()`** |
| Inline edit | ❌ | ❌ | **✅ (vía Livewire overlay)** |
| Integración IA | ❌ | ✅ (Diagnóstico) | **❌** |
| Uso externo (>10 callers) | Alto | Medio | **Medio (solo profesor)** |

---

## 11. Resumen de Hallazgos

### Críticos
1. **Rutas definidas sin controlador (5 rutas):** `crud()`, `store()`, `edit()`, `update()`, `destroy()` existen en `activities.php` pero no en `ActivityController`. Causan Error 500. Posiblemente vestigios de un refactor pasado que migró el CRUD a Livewire.
2. **`clone.blade.php` no existe:** El método `ActivityController@clone()` referencia `profesors.activities.clone` que no está en disco. Causa Error 500 si se accede a la ruta.
3. **Eliminación física sin SoftDeletes:** `Activity::delete()` y `Achievement::delete()` son irreversibles. No hay `SoftDeletes` en ninguno de los dos modelos.
4. **`finicial > ffinal` sin validación:** La fecha inicial y final no se validan como `before_or_equal` / `after_or_equal` en ningún nivel (Livewire ni controlador).
5. **`emptyActivities()` sin transacción:** Elimina todas las actividades de un Pevaluacion sin `DB::transaction()` ni protección ante fallo parcial.

### Moderados
6. **Sin paginación server-side:** `Pevaluacion::get()` y `Activity::get()` cargan todo en memoria.
7. **`store_clone()` redirige a ruta admin:** El redirect post-clone va a `route('administracion.pevaluacions.crud')` — fuera del módulo profesor. Inconsistente con el resto del flujo.
8. **`$sección` typo en `store_clone()`:** Línea 115: `$sección = Pevaluacion::findOrFail(...)` variable con tilde — sintaxis válida en PHP pero mala práctica.
9. **`enable_edit` solo en frontend:** La validación de precierre se hace solo en la UI (deshabilitando botones). No hay chequeo server-side en `save()`.
10. **Sin validación de unicidad:** Un profesor podría crear múltiples actividades exactamente iguales para la misma Pevaluacion.
11. **DataTable sin paginación server-side:** `$pevaluacions->get()` en `index()` carga todo sin `paginate()`.

### Buenos (conservar en migración)
12. **Validación Livewire completa:** `ValidateTrait` define reglas claras y `validationAttributes()` con nombres en español desde `COLUMN_COMMENTS`.
13. **COLUMN_COMMENTS bien documentado:** Ambos modelos definen el array estático con todos los campos y labels en español.
14. **`teachingWordsMayorCount()` aislado:** Método autocontenido con lógica clara de conteo de palabras significativas — fácil de migrar a TypeScript.
15. **Clonación completa con achievements:** La clonación copia actividades + achievements correctamente.
16. **Doble vista PDF:** Formato completo y resumen cubren diferentes necesidades de reporte.
17. **Bloqueo de edición por precierre:** Previene modificaciones después de la fecha límite.
18. **Protección de eliminación:** Botón deshabilitado si la actividad tiene achievements asociados.
19. **`getStatusResumeAttribute()` útil:** Determina correctamente qué actividades incluir en resumen.
20. **Componente compartido con mobile:** Misma lógica de negocio en Livewire mobile y desktop.

---

*Documento generado: 2026-06-06. Validado contra código fuente de SAEFL (Laravel 8.83).*

*Archivos validados: `ActivityController.php`, `Activity.php`, `Achievement.php`, `Pevaluacion.php` (parcial), `IndexComponent.php` (Livewire), `ValidateTrait.php`, migraciones en `backUps/activities/`, todas las vistas Blade del módulo (index, create, format, resume, partials, table), vistas Livewire (index-component, form.fields, partials.create), rutas en `routes/app/tab/profesors/activities.php` + `routes/helpers/profesors.php`, componentes mobile en `Livewire/Movile/Profesor/Activity/`.*
