# Gestión de Carga Académica (Pevaluacions) — Documento Técnico Completo

> **Versión:** 1.0 — Validada contra código fuente real (Laravel 8.x / Livewire 2.5)
> **Módulo:** `plannings.pevaluacions` — Asignación de planes de evaluación (carga académica) a profesores.
> **Propósito:** Replicación en NextJS + API REST (Laravel o Node).
> **Arquitectura fuente:** Server-rendered (Blade) + Livewire con sidebar de edición y formulario dinámico con cascada.

---

## 1. Introducción

El módulo **Asignación de Carga Académica** (Pevaluacions) dentro del módulo de Planificación (`is_planning`) permite a los **líderes/jefes de área de conocimiento** asignar profesores a combinaciones de **asignatura + grado + sección + lapso**, creando así un **Plan de Evaluación** que sirve como contenedor para las actividades (`Activity`) y evaluaciones (`Evaluacion`) posteriores.

Una "Pevaluacion" es el registro central que vincula:
- **Quién enseña** → `profesor_id`
- **Qué enseña** → `pensum_id` (asignatura + grado)
- **A quiénes** → `seccion_id`
- **Cuándo** → `lapso_id`
- **Configuración adicional** → `grupo_estable_id`, `status_note_report`, `escala_id`, `nota_type`, `status_official`, `status_baremo`

La validación de unicidad `(lapso_id + seccion_id + pensum_id)` evita duplicados. La condición `pestudio.planning_module = true` filtra los planes de estudio que participan del módulo de planificación.

---

## 2. Arquitectura del Dominio

### 2.1 Cadena de dependencias

```
Pevaluacion (plan de evaluación / carga académica)
  ├── profesor_id → Profesor (docente asignado)
  │     └── user_id → User (cuenta de sistema)
  ├── pensum_id → Pensum (asignatura + grado)
  │     ├── asignatura_id → Asignatura
  │     │     └── campo_conocimiento_id → CampoConocimiento
  │     │           └── area_conocimiento_id → AreaConocimiento
  │     │                 └── leader_id → Leader
  │     ├── grado_id → Grado
  │     │     └── pestudio_id → Pestudio
  │     │           ├── planning_module (boolean) ← filtra módulo activo
  │     │           └── peducativo_id → Peducativo
  │     └── (softDeletes)
  ├── seccion_id → Seccion (sección/grupo de estudiantes)
  ├── lapso_id → Lapso (período académico)
  │     └── finicial, ffinal ← controla bloqueo de edición
  ├── grupo_estable_id → GrupoEstable (grupo pedagógico estable)
  ├── escala_id → Escala (escala de notas: mínimo/máximo)
  │
  ├── activities[] → Activity (actividades planificadas)
  ├── evaluacions[] → Evaluacion (evaluaciones / notas)
  ├── boletin_ajustes[] → BoletinAjuste
  │
  └── debates[] → Debate
        └── questions[] → DebateQuestion
```

### 2.2 Árbol de archivos del módulo

```
routes/
  web.php                                          ← grupo /app/plannings con middleware is_planning
  app/plannings.php                                ← require de subrutas
  app/tab/plannings/pevaluacions.php               ← 2 rutas GET (index y evaluacions/index)

app/
  Http/
    Controllers/Planning/Tab/
      PevaluacionController.php                    ← 1 método: index (carga datos iniciales)
      UserDataInitializer.php                      ← trait compartido (pestudios, peducativos)
    Livewire/Planning/Pevaluacion/
      IndexComponent.php                           ← componente principal CRUD con filtros
      updatedTrait.php                             ← hooks reactivos: cambio de grado o pestudio
  Models/
    app/
      Pescolar/
        Leader.php                                 ← consultas scoped por líder (getPevaluacionesForLeader)
        Pestudio.php                               ← planning_module (booleano que activa módulo)
        Pensum.php                                 ← asignatura + grado
        Grado.php                                  ← list_pestudio_grado()
        Seccion.php                                ← list_seccion_grado()
        Lapso.php                                  ← current() scope
        Asignatura.php                             ← asignatura con code, name
        Profesor.php                               ← modelo rico con traits
        Peducativo.php                             ← plan educativo
        AreaConocimiento.php                       ← área de conocimiento (leader_id)
      Profesor/
        Pevaluacion.php                            ← modelo principal (687 líneas con accesors)
        Activity.php                               ← actividades relacionadas
      Estudiante/
        GrupoEstable.php                           ← grupo estable
      Profesor/Pevaluacion/
        Evaluacion.php                             ← evaluaciones (notas)

resources/views/
  plannings/pevaluacions/
    index.blade.php                                ← layout del módulo
    table/index.blade.php                          ← tabla resumen de profesores con carga
  livewire/planning/pevaluacion/
    index-component.blade.php                      ← layout del componente Livewire
    form/fileds.blade.php                          ← campos del formulario (compartido crear/editar)
    partials/
      assign.blade.php                             ← sidebar de nueva asignación
      edit.blade.php                               ← sidebar de edición
      filters.blade.php                            ← barra de filtros (pestudio, profesor, grado, sección, lapso)
    table/
      index.blade.php                              ← tabla del listado de asignaciones
```

---

## 3. Validación contra código fuente

### 3.1 Hallazgos clave

| # | Tópico | Detalle |
|---|--------|---------|
| 1 | **Columna `seccion_id` sin migración** | El modelo declara `seccion_id` en fillable, el formulario lo usa y el `save()` lo guarda, pero no existe migración que agregue esta columna a `pevaluacions` en los backups. Puede haberse añadido directamente en DB o en una migración no respaldada. |
| 2 | **`planning_module` es crítico pero invisible** | `Leader::getPevaluacionesForLeader()` aplica `->where('planning_module', true)` en `pestudios`, filtrando silenciosamente asignaciones de planes de estudio que no tienen el módulo activo. No hay indicación visual en la UI de este filtro. |
| 3 | **Scope por líder está comentado** | La línea `whereHas('pensum.asignatura.campoConocimientos.areaConocimiento', ...)` está comentada en `getPevaluacionesForLeader()`. Actualmente **NO se filtra por líder** — se muestran todas las pevaluacions del plan de estudio activo. |
| 4 | **El filtro `profesor_id` en la barra usa `$list_profesor`** (sin búsqueda), mientras que el formulario de asignación/edición usa `$list_profesors` (con `setProfesorLists()` y búsqueda por nombre). Son dos listas distintas. |
| 5 | **`paginate = 9999` no es paginación real** | Cuando se selecciona "Todos", el componente usa `get()` y lo envuelve en un `LengthAwarePaginator` manual para compatibilidad con Blade. |
| 6 | **`status_note_report` se guarda como booleano** pero los options del form usan `[true => 'SI', false => 'NO']`. El formulario se renderiza con valores PHP `true`/`false` convertidos a string "1"/"" en HTML. |
| 7 | **Dos tablas distintas coexisten** | La vista del controlador (`plannings.pevaluacions.table.index`) es una tabla resumen de profesores (similar al módulo anterior), mientras que la tabla Livewire (`livewire/planning/pevaluacion/table/index`) lista asignaciones individuales. |
| 8 | **Lapso cerrado bloquea edición pero no creación** | El botón editar se deshabilita cuando `$now->gt($lapso->ffinal)`, pero el botón "Nueva Asignación" permanece accesible incluso para lapsos cerrados. |
| 9 | **Eliminación condicionada a actividades** | Solo se permite eliminar si `$activities->isEmpty()`. Si tiene actividades, el botón aparece deshabilitado con tooltip. |
| 10 | **`grado_id` se guarda en `pensum_id` indirectamente** | El formulario pide `grado_id` pero NO se guarda directamente en la tabla `pevaluacions` — solo existe como filtro de UI. El grado se deriva de `pensum.grado_id` en las relaciones del modelo. |
| 11 | **Regla de unicidad compuesta** | `Rule::unique('pevaluacions')->where('lapso_id', ...)->where('seccion_id', ...)->where('pensum_id', ...)->whereNull('deleted_at')` — triple check + softdelete-aware. En edición, ignora el registro actual con `->ignore($id)`. |

### 3.2 Validación de rutas

| Ruta | Método | Controlador | Middleware | Archivo |
|------|--------|-------------|------------|---------|
| `GET /app/plannings/pevaluacions/index` | `index()` | `Planning\Tab\PevaluacionController` | `auth`, `is_planning` | `routes/app/tab/plannings/pevaluacions.php` |
| `GET /app/plannings/pevaluacions/evaluacions/index` | `index()` | mismo | `auth`, `is_planning` | mismo archivo |

Registro en `routes/web.php`:
```php
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Planning'], function () {
    Route::group(['prefix' => 'plannings', 'middleware' => ['is_planning']], function () {
        require (__DIR__ . '/app/plannings.php');  // → requiere tab/plannings/pevaluacions.php
    });
});
```

---

## 4. Lógica de Negocio — Especificación Completa

### 4.1 Reglas de negocio

**RN-01: Unicidad de asignación (lapso + sección + pensum).**
No puede existir más de un registro de `pevaluacions` con la misma combinación de `lapso_id`, `seccion_id` y `pensum_id`. Incluye `whereNull('deleted_at')` para respetar soft-deletes.

**RN-02: Solo módulo de planificación activo.**
Las asignaciones solo se muestran si el `Pestudio` asociado tiene `planning_module = true`. Esto filtra silenciosamente en la consulta principal.

**RN-03: Lapso cerrado bloquea edición.**
Si la fecha actual (`Carbon::now()`) supera `lapso.ffinal`, el botón de editar se deshabilita y la fila se muestra con clase `table-warning`. La creación no se bloquea.

**RN-04: Eliminación segura (soft-delete).**
Las asignaciones se eliminan con `softDeletes`. No se permite eliminar si la asignación tiene actividades hijas (`activities()->exists()`).

**RN-05: Scope por líder (desactivado actualmente).**
El método `getPevaluacionesForLeader()` tiene comentada la cláusula `whereHas('pensum.asignatura.campoConocimientos.areaConocimiento', leader_id)`. Cuando se reactive, cada líder solo verá asignaciones de las áreas que supervisa.

**RN-06: Formulario compartido crear/editar.**
El archivo `form/fileds.blade.php` se incluye tanto en el partial `assign` como en `edit`, con los mismos campos y validaciones. El modo se determina por `$pevaluacion_id`.

**RN-07: Cascada de listas dependientes.**
- Al cambiar `pestudio_id` → se refrescan `list_grado`, `list_profesor`, y se resetean `grado_id` y `profesor_id`.
- Al cambiar `grado_id` → se refrescan `list_seccion`, `list_pensum`, y se resetea `seccion_id`.

**RN-08: Búsqueda de profesor por nombre en el formulario.**
El campo `profesor_name` filtra `$list_profesors` mediante `setProfesorLists($value)`, que ejecuta una consulta `WHERE name LIKE %value% OR lastname LIKE %value%`.

**RN-09: Paginación "Todos".**
Cuando `paginate = 9999`, se obtienen todos los registros con `->get()` y se envuelven en un `LengthAwarePaginator` para mantener compatibilidad con los helpers de Blade.

**RN-10: `status_note_report` como booleano.**
Se guarda casteando a booleano: `(bool) $this->status_note_report`. El formulario usa `<select>` con opciones `true => 'SI'` / `false => 'NO'`.

### 4.2 Flujo de datos completo

```
[Usuario autenticado con rol is_planning]
    │
    ├─(1) GET /app/plannings/pevaluacions/index
    │     └─ PevaluacionController@index()
    │           ├─ $this->initializeUserData()   ← carga pestudios, peducativos desde Leader
    │           ├─ Profesor::getProfesorForLeaderId($user->id)
    │           ├─ Lapso::all()
    │           ├─ Lapso::current()
    │           └─ view('plannings.pevaluacions.index')
    │                 └─ @livewire('planning.pevaluacion.index-component')
    │
    └─ [COMPONENTE] IndexComponent (Livewire)
          │
          ├─ mount()
          │    ├─ Leader::getPestudioForLeader($leader_id)
          │    ├─ Leader::getPeducativosForLeader($leader_id)
          │    ├─ Leader::getGradosForLeader($leader_id)
          │    ├─ Leader::getPensumsForLeader($leader_id)
          │    ├─ Profesor::listProfesorsIndexado()
          │    ├─ GrupoEstable::list_grupo_estable_full()
          │    ├─ Lapso::current() → lapso por defecto
          │    └─ close() → modo índice
          │
          ├─ render()
          │    ├─ Construye array $filters de los campos activos
          │    └─ Leader::getPevaluacionesForLeader($leaderId, $filters, true, $this->paginate)
          │         ├─ with('pensum.asignatura.campoConocimientos.areaConocimiento')
          │         ├─ with('seccion.grado', 'profesor', 'lapso', 'pensum')
          │         ├─ withCount('activities')
          │         ├─ whereHas('pensum.pestudio', planning_module = true)
          │         ├─ when(filters...) → seccion_id, grado_id, lapso_id, pestudio_id, profesor_id
          │         ├─ whereNull('deleted_at')
          │         └─ paginate($perPage) o LengthAwarePaginator manual
          │
          ├─ setAssign() → modeAssign = true, limpia formulario
          │
          ├─ edit($id) → carga pevaluacion + grado + pestudio
          │    ├─ actualiza listas dependientes
          │    └─ modeEdit = true
          │
          ├─ save() → validación + guardado
          │    ├─ validate() con Rule::unique(...)
          │    ├─ crear o actualizar Pevaluacion
          │    └─ reset() + showSwal() + close()
          │
          ├─ delete($id) → soft-delete
          │
          ├─ sortBy($field) → toggle sortDirection
          │
          └─ resetFilters() → limpia todos los filtros
```

### 4.3 Máquina de estados del componente

```
┌─────────────────────────────────────────────┐
│        IndexComponent (modos)                │
├─────────────────────────────────────────────┤
│                                              │
│  ┌───────────┐   setAssign()   ┌──────────┐ │
│  │ modeIndex │ ───────────────►│modeAssign│ │
│  │  = true   │                 │ = true   │ │
│  │ modeAssign│◄──── close() ───│modeEdit  │ │
│  │  = false  │                 │ = false  │ │
│  │ modeEdit  │                 │          │ │
│  │  = false  │   edit($id)     │          │ │
│  └─────┬─────┘ ───────────────►└────┬─────┘ │
│        │                            │        │
│        │                   ┌────────┴──────┐ │
│        │                   │  modeAssign   │ │
│        │                   │  = false      │ │
│        │                   │  modeEdit     │ │
│        │                   │  = true       │ │
│        │                   └───────┬───────┘ │
│        │                           │         │
│        │                    save() │         │
│        └───────────────────────────┘         │
│                 close() / success            │
│                                              │
└─────────────────────────────────────────────┘
```

### 4.4 Flujo de cascada de listas (updatedTrait)

```
updatedPestudioId(value)
  ├─ list_grado = Grado::list_pestudio_grado(value)
  ├─ list_profesor = Profesor::list_profesors_pestudio(value)
  └─ grado_id = null, profesor_id = null

updatedGradoId(value)
  ├─ list_seccion = Seccion::list_seccion_grado(value)
  ├─ list_pensum = Leader::getPensumsForLeader(leader_id, value)
  └─ seccion_id = null
```

---

## 5. Esquemas de Base de Datos

### 5.1 Tabla `pevaluacions` (estructura actual, reconstruida)

```sql
CREATE TABLE `pevaluacions` (
  `id`                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `profesor_id`       INT UNSIGNED NOT NULL COMMENT 'Profesor (FK profesors)',
  `pensum_id`         BIGINT UNSIGNED NOT NULL COMMENT 'Asignatura+Grado (FK pensums)',
  `seccion_id`        INT UNSIGNED NULL COMMENT 'Sección (FK seccions)',
  `lapso_id`          INT UNSIGNED NOT NULL COMMENT 'Período (FK lapsos)',
  `grupo_estable_id`  INT UNSIGNED NULL COMMENT 'Grupo Estable (FK grupo_estables)',
  `escala_id`         INT UNSIGNED NULL COMMENT 'Escala de notas (FK escalas)',
  `status_baremo`     VARCHAR(255) NULL COMMENT 'Estado baremo (nota literal)',
  `nota_type`         VARCHAR(255) NULL COMMENT 'Tipo de nota (ACUMULATIVA/PROMEDIADA)',
  `status_official`   BOOLEAN DEFAULT TRUE COMMENT 'En documentos oficiales',
  `status_note_report` BOOLEAN DEFAULT TRUE COMMENT 'En Informe de Notas',
  `objetivo`          VARCHAR(255) NULL COMMENT 'Objetivo General',
  `description`       VARCHAR(255) NULL COMMENT 'Descripción de la asignación',
  `observations`      VARCHAR(255) NULL COMMENT 'Observaciones',
  `category`          VARCHAR(255) NULL COMMENT 'Categoría',
  `deleted_at`        TIMESTAMP NULL,
  `created_at`        TIMESTAMP NULL,
  `updated_at`        TIMESTAMP NULL,

  INDEX `pevaluacions_profesor_id_index` (`profesor_id`),
  INDEX `pevaluacions_pensum_id_index` (`pensum_id`),
  INDEX `pevaluacions_seccion_id_index` (`seccion_id`),
  INDEX `pevaluacions_lapso_id_index` (`lapso_id`),

  CONSTRAINT `pevaluacions_profesor_id_foreign`
    FOREIGN KEY (`profesor_id`) REFERENCES `profesors`(`id`) ON DELETE CASCADE,
  CONSTRAINT `pevaluacions_pensum_id_foreign`
    FOREIGN KEY (`pensum_id`) REFERENCES `pensums`(`id`) ON DELETE CASCADE,
  CONSTRAINT `pevaluacions_lapso_id_foreign`
    FOREIGN KEY (`lapso_id`) REFERENCES `lapsos`(`id`) ON DELETE CASCADE,
  CONSTRAINT `pevaluacions_seccion_id_foreign`
    FOREIGN KEY (`seccion_id`) REFERENCES `seccions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Nota:** La columna `seccion_id` no tiene migración documentada en `backUps/pevaluacions/` — se asume que existe en la BD real. Validar contra el esquema actual antes de migrar.

### 5.2 Tablas relacionadas

| Tabla | Columnas clave | Relación |
|-------|---------------|----------|
| `pensums` | `id`, `asignatura_id`, `grado_id`, `pestudio_id`, `deleted_at` | `pevaluacions.pensum_id` |
| `asignaturas` | `id`, `name`, `code` | `pensums.asignatura_id` |
| `grados` | `id`, `name`, `code`, `status_active` | `pensums.grado_id` |
| `seccions` | `id`, `name`, `grado_id`, `status_active` | `pevaluacions.seccion_id` |
| `lapsos` | `id`, `name`, `code_sm`, `finicial`, `ffinal`, `class` | `pevaluacions.lapso_id` |
| `pestudios` | `id`, `name`, `peducativo_id`, `planning_module`, `manager_id` | `pensums.pestudio_id` |
| `area_conocimientos` | `id`, `name`, `leader_id` | a través de campo → asignatura → pensum |
| `grupo_estables` | `id`, `name` | `pevaluacions.grupo_estable_id` |
| `escalas` | `id`, `name`, `minimo`, `maximo` | `pevaluacions.escala_id` |

---

## 6. Modelo de Datos — API REST para exportación

### 6.1 Endpoints propuestos

#### `GET /api/planning/pevaluacions`

Lista paginada de asignaciones de carga académica.

**Query params:**

| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| `search` | string | null | Búsqueda textual (profesor/asignatura) |
| `pestudio_id` | int | null | Filtrar por plan de estudio |
| `grado_id` | int | null | Filtrar por grado/año |
| `seccion_id` | int | null | Filtrar por sección |
| `lapso_id` | int | null | Filtrar por lapso (default: lapso actual) |
| `profesor_id` | int | null | Filtrar por profesor |
| `status_activities` | enum | null | `SI` (con actividades), `NO` (sin actividades) |
| `only_planning_module` | bool | `true` | Solo pestudios con planning_module activo |
| `sort_by` | string | `created_at` | Campo de ordenación |
| `sort_dir` | enum | `desc` | `asc` o `desc` |
| `per_page` | int | 15 | Elementos por página |
| `page` | int | 1 | Número de página |

**Response (200):**
```json
{
  "data": [
    {
      "id": 45,
      "profesor": {
        "id": 12,
        "fullname": "PÉREZ JUAN",
        "ci": "V-12345678"
      },
      "pensum": {
        "id": 89,
        "asignatura": { "id": 15, "name": "MATEMÁTICA", "code": "MT" },
        "grado": { "id": 5, "name": "5TO AÑO", "code": "5A" }
      },
      "seccion": { "id": 3, "name": "A", "grado_id": 5 },
      "lapso": { "id": 2, "name": "II MOMENTO", "finicial": "2026-01-15", "ffinal": "2026-04-15" },
      "description": "Matemática - 5to Año A",
      "activities_count": 4,
      "is_lapso_closed": false,
      "grupo_estable": null,
      "status_note_report": true,
      "status_official": true,
      "created_at": "2026-01-20T10:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 8,
    "per_page": 15,
    "total": 112
  }
}
```

#### `GET /api/planning/pevaluacions/{id}`

Detalle de una asignación con relaciones eager-loaded.

**Response (200):**
```json
{
  "id": 45,
  "profesor": { ... },
  "pensum": {
    "id": 89,
    "asignatura": { ... },
    "grado": { ... },
    "pestudio": { "id": 2, "name": "MEDIA GENERAL", "planning_module": true }
  },
  "seccion": { ... },
  "lapso": { ... },
  "activities": [
    { "id": 120, "topic": "Ecuaciones lineales", "teaching": "...", "status": true, "activities_avr": 50 }
  ],
  "activities_count": 4,
  "grupo_estable": null,
  "escala": { "id": 1, "name": "Escala 1-20", "minimo": 1, "maximo": 20 },
  "status_note_report": true,
  "status_official": true,
  "description": "Matemática - 5to Año A"
}
```

#### `POST /api/planning/pevaluacions`

Crear nueva asignación de carga académica.

**Request body:**
```json
{
  "profesor_id": 12,
  "pensum_id": 89,
  "seccion_id": 3,
  "lapso_id": 2,
  "grupo_estable_id": null,
  "description": "Matemática - 5to Año A",
  "status_note_report": true,
  "escala_id": 1,
  "nota_type": "PROMEDIADA",
  "status_official": true
}
```

**Validaciones:**
```json
{
  "profesor_id": "required|integer|exists:profesors,id",
  "pensum_id": "required|integer|exists:pensums,id",
  "seccion_id": "required|integer|exists:seccions,id",
  "lapso_id": "required|integer|exists:lapsos,id",
  "description": "nullable|string|max:500",
  "grupo_estable_id": "nullable|integer|exists:grupo_estables,id",
  "status_note_report": "nullable|boolean",
  "escala_id": "nullable|integer|exists:escalas,id",
  "status_official": "nullable|boolean",
  "**UNIQUE**": "pensum_id + seccion_id + lapso_id (con soft-delete)"
}
```

**Response (201):**
```json
{
  "id": 46,
  "message": "Asignación de carga académica creada exitosamente."
}
```

**Error 422 (duplicado):**
```json
{
  "message": "Ya existe un plan de evaluación asignado para esta asignatura, sección y lapso.",
  "errors": { "pensum_id": ["Ya existe un plan de evaluación asignado para esta asignatura, sección y lapso."] }
}
```

#### `PUT /api/planning/pevaluacions/{id}`

Actualizar asignación existente. Mismos campos que POST. La regla de unicidad ignora el ID actual.

#### `DELETE /api/planning/pevaluacions/{id}`

Eliminación lógica (soft-delete).

**Error 409:**
```json
{
  "message": "No se puede eliminar: la asignación tiene actividades registradas.",
  "code": "HAS_ACTIVITIES"
}
```

#### `GET /api/planning/pevaluacions/filters`

Listas para poblar filtros del frontend.

**Response:**
```json
{
  "pestudios": [ { "id": 1, "name": "EDUCACIÓN MEDIA GENERAL", "planning_module": true } ],
  "grados": [ { "id": 5, "name": "5TO AÑO", "code": "5A" } ],
  "secciones": [ { "id": 3, "name": "A" } ],
  "lapsos": [ { "id": 1, "name": "I MOMENTO" }, { "id": 2, "name": "II MOMENTO" } ],
  "profesores": [ { "id": 12, "fullname": "PÉREZ JUAN" } ],
  "pensums": [ { "id": 89, "asignatura_fullname": "[MT] MATEMÁTICA" } ],
  "grupos_estables": [ { "id": 1, "name": "Grupo A" } ],
  "escalas": [ { "id": 1, "name": "Escala 1-20" } ],
  "pagination_options": [10, 20, 50, 100, 9999]
}
```

---

## 7. Especificación de Componentes (NextJS + Tailwind)

### 7.1 Página principal: `PlanningPevaluacionsPage`

```
┌────────────────────────────────────────────────────────────────────┐
│  Módulo Plan de Actividades                          2025-2026     │
│  Planes Educativos: Media General || Media Técnica                 │
├────────────────────────────────────────────────────────────────────┤
│  [+ Nueva Asignación]                                              │
├────────────────────────────────────────────────────────────────────┤
│  Filtros: [Plan Estudio ▼] [Profesor ▼] [Grado ▼] [Sección ▼]    │
│           [Momento ▼] [Ver: 10 ▼]  [⟳]                            │
├────────────────────────────────────────────────────────────────────┤
│  Tabla de Asignaciones                                             │
│  ┌─────┬──────────┬───────────────────┬──────┬─────────┬────────┐ │
│  │ #   │P. Estudio│ Asignatura/Grado/ │ Act. │ Profesor│ Acción │ │
│  │     │          │ Sección/Momento   │      │         │        │ │
│  ├─────┼──────────┼───────────────────┼──────┼─────────┼────────┤ │
│  │  1  │MEDIA     │ MATEMÁTICA    [II]│  04  │ PÉREZ   │ [✏️][🗑]│ │
│  │     │GENERAL   │ 5TO AÑO - A       │      │ JUAN    │        │ │
│  ├─────┼──────────┼───────────────────┼──────┼─────────┼────────┤ │
│  │  2  │MEDIA     │ [MT] LENGUA   [II]│  03  │ GARCÍA  │ [✏️][🗑]│ │
│  │     │GENERAL   │ 5TO AÑO - B       │      │ MARÍA   │        │ │
│  └─────┴──────────┴───────────────────┴──────┴─────────┴────────┘ │
│  ← 1 2 3 ... 8 →                                                  │
└────────────────────────────────────────────────────────────────────┘

     ┌─ Sidebar (modeAssign/modeEdit) ──────────────────────────┐
     │  [✖] Asignación de Carga Académica                       │
     │                                                          │
     │  Plan de Estudio: [MEDIA GENERAL ▼]                      │
     │  Grado/Año:        [5TO AÑO ▼]                           │
     │  Sección:          [A ▼]                                 │
     │  Momento:          [II MOMENTO ▼]                        │
     │  Área de Formación: [[MT] MATEMÁTICA ▼]                  │
     │  Profesor: [Buscar...] [PÉREZ JUAN ▼]                    │
     │  Descripción: [_________________________]                │
     │  Grupo Estable: [Seleccione ▼]                           │
     │  En Informe de Notas: [SI ▼]                             │
     │                                                          │
     │  [█████████████ Guardar █████████████] [Cerrar]          │
     └──────────────────────────────────────────────────────────┘
```

### 7.2 Árbol de componentes

```
PlanningPevaluacionsPage
├── PageHeader (título + badges de planes educativos)
├── IndexComponent (Livewire → NextJS container)
│   ├── FilterBar
│   │   ├── PestudioSelect (cascada: → grado → sección)
│   │   ├── ProfesorSelect
│   │   ├── GradoSelect (cascada: → pensum → sección)
│   │   ├── SeccionSelect
│   │   ├── LapsoSelect
│   │   ├── PaginateSelect
│   │   └── ResetButton
│   ├── MainPanel
│   │   └── PevaluacionTable
│   │       ├── SortableTableHeader
│   │       ├── PevaluacionRow (× N)
│   │       │   ├── PestudioBadge
│   │       │   ├── AsignaturaGradoSeccionCell
│   │       │   ├── ActivitiesBadge
│   │       │   ├── ProfesorName
│   │       │   └── ActionButtons
│   │       │       ├── EditButton (deshabilitado si lapso cerrado)
│   │       │       └── DeleteButton (deshabilitado si tiene activities)
│   │       └── EmptyState ("No se encontraron asignaciones")
│   │   └── Pagination
│   └── SidebarPanel (render condicional)
│       ├── AssignSidebar (modeAssign)
│       │   └── PevaluacionForm → save()
│       └── EditSidebar (modeEdit)
│           └── PevaluacionForm → save() + [✖] close
└── (SweetAlert toasts globales)
```

### 7.3 Estados de cada componente

| Componente | Loading | Empty | Error | Success |
|-----------|---------|-------|-------|---------|
| `PevaluacionTable` | Skeleton 5 filas | "No se encontraron asignaciones" + icono | Toast error + retry | Tabla normal |
| `FilterBar` | Inputs deshabilitados | Options cargados | Badge error | Normal |
| `AssignSidebar` | Botón guardar con spinner | Campos vacíos | Errores inline + mensaje | Sidebar se cierra, tabla refresca |
| `EditSidebar` | Botón guardar con spinner | Campos precargados | Errores inline | Sidebar se cierra, tabla refresca |
| `PevaluacionForm` | N/A | Según modo (vacío/precargado) | Error de unicidad en `pensum_id` | N/A |
| `ActionButtons` | N/A | N/A | N/A | Edit/Delete según reglas |
| `Pagination` | Links deshabilitados | Oculta | Oculta | Links funcionales |

### 7.4 Formulario compartido (PevaluacionForm)

```
┌─────────────────────────────────────┐
│  Plan de Estudio  [MEDIA GENERAL ▼] │  ← pestudio_id
│  Grado/Año        [5TO AÑO ▼]       │  ← grado_id (resetea sección)
│  Sección          [A ▼]             │  ← seccion_id
│  Momento          [II MOMENTO ▼]    │  ← lapso_id
│  Área de Formación [MT] MATEMÁTICA  │  ← pensum_id (filtrado por grado)
│                      ▼              │
│  Profesor:  [Buscar...] [PÉREZ ▼]   │  ← profesor_name (búsqueda) + profesor_id
│  Descripción [________________]     │
│  Grupo Estable [Seleccione ▼]       │
│  En Inf. Notas   [SI ▼]            │
└─────────────────────────────────────┘
```

---

## 8. Lógica de Filtros y Ordenación

### 8.1 Filtros en el componente Livewire

```php
// IndexComponent.php — render()
$filters = array_filter([
    'seccion_id' => $this->seccion_id ?? null,
    'grado_id' => $this->grado_id ?? null,
    'lapso_id' => $this->lapso_id ?? null,
    'pestudio_id' => $this->pestudio_id ?? null,
    'profesor_id' => $this->profesor_id ?? null,
], fn($v) => $v !== null && $v !== '');
```

### 8.2 Ordenación (sortBy)

```php
public $sortField = 'pevaluacions.id';
public $sortDirection = 'asc';

public function sortBy($field)
{
    if ($this->sortField === $field) {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        $this->sortField = $field;
        $this->sortDirection = 'asc';
    }
}
```

**Nota importante:** La ordenación se aplica dentro de `Leader::getPevaluacionesForLeader()`, pero el parámetro `sortField` nunca se pasa en el código fuente actual. Todas las consultas usan `->orderBy('created_at', 'desc')` fijo. La función `sortBy()` está preparada para uso futuro.

### 8.3 Reset de página en cada filtro

```php
// Cada updating*() resetea la página de paginación
public function updatingSearch()   { $this->resetPage(); }
public function updatingPestudioId() { $this->resetPage(); }
public function updatingGradoId()  { $this->resetPage(); }
// ... etc
```

---

## 9. Reglas de validación y casos borde

| Caso | Comportamiento esperado |
|------|------------------------|
| Duplicado exacto (lapso+sección+pensum) | Validación rechaza con error "Ya existe un plan de evaluación asignado..." |
| Editar y no cambiar pensum | `ignore($id)` en regla unique permite mantener el mismo valor |
| Lapso cerrado (`now > ffinal`) | Fila con clase `table-warning`, botón editar deshabilitado, botón borrar NO se bloquea por lapso |
| Asignación con actividades hijas | Botón borrar deshabilitado con tooltip "No se puede eliminar: tiene actividades registradas" |
| Sin resultados para filtros | Fila única: icono inbox + "No se encontraron asignaciones" |
| `paginate = 9999` | Paginador muestra todos los registros en una sola página |
| Profesor sin nombre en búsqueda | `setProfesorLists(null)` carga todos los profesores activos |
| `grupo_estable_id` nulo | Se guarda como null, no se valida requerido |
| `planning_module = false` | La asignación no aparece en el listado (filtro silencioso) |

---

## 10. Plan de Migración: Laravel/Livewire → NextJS + API

### Fase 1: Backend API

| Prioridad | Endpoint | Descripción |
|-----------|----------|-------------|
| P0 | `GET /api/planning/pevaluacions` | Lista paginada con filtros y ordenación |
| P0 | `GET /api/planning/pevaluacions/{id}` | Detalle completo con relaciones |
| P0 | `GET /api/planning/pevaluacions/filters` | Datos para poblar filtros y formulario |
| P1 | `POST /api/planning/pevaluacions` | Crear asignación con validación de unicidad |
| P1 | `PUT /api/planning/pevaluacions/{id}` | Actualizar asignación |
| P1 | `DELETE /api/planning/pevaluacions/{id}` | Soft-delete con verificación de actividades |

### Fase 2: Frontend NextJS

| Prioridad | Componente | Descripción |
|-----------|------------|-------------|
| P0 | `usePevaluacions` | Hook con filtros, paginación y ordenación |
| P0 | `PevaluacionTable` | Tabla con ordenación por columna |
| P0 | `FilterBar` | Filtros con cascada (pestudio → grado → sección) |
| P1 | `PevaluacionForm` | Formulario compartido crear/editar con sidebar |
| P1 | `ProfesorSearchSelect` | Select con búsqueda textual |
| P1 | `ActionButtons` | Edit/Delete con reglas de negocio (lapso cerrado, tiene activities) |

### Fase 3: Reglas de negocio en frontend

| Regla | Implementación |
|-------|---------------|
| Lapso cerrado → deshabilitar edición | Comparar `now` con `lapso.ffinal` en frontend antes de permitir edición |
| Bloquear delete si tiene actividades | Revisar `activities_count > 0` en frontend y rechazar en backend |
| Unicidad compuesta | Validar en backend con 422 + mensaje específico |
| Cascada de listas | Encadenar fetch de opciones: pestudio → grado → sección/pensum → profesor |
| planning_module | Backend filtra automáticamente; frontend no necesita lógica adicional |
| sortBy | Implementar toggle dirección + reset page |

### Fase 4: Pruebas

| Tipo | Casos a probar |
|------|----------------|
| Unitarias | Validación unicidad compuesta |
| Integración | CRUD completo + verificación soft-delete con actividades |
| Integración | Cascada de filtros → respuesta correcta de opciones |
| Componente | Formulario: crear, editar, errores inline |
| Componente | Tabla: ordenación, búsqueda, paginación, estados vacío |
| E2E | Flujo completo: login → ver asignaciones → crear → editar → eliminar |

---

## 11. Diagrama de Secuencia: Crear Asignación

```
Usuario                Frontend                 API Backend              DB
   │                       │                        │                    │
   │ Click [+ Nueva]       │                        │                    │
   │──────────────────────►│                        │                    │
   │                       │ GET /filters           │                    │
   │                       │───────────────────────►│                    │
   │                       │◄──── pestudios,        │                    │
   │                       │        grados,         │                    │
   │                       │        lapsos,         │                    │
   │                       │        profesores...   │                    │
   │                       │                        │                    │
   │ Selecciona pestudio   │                        │                    │
   │──────────────────────►│ Cascada: grados        │                    │
   │◄── grados filtrados   │ filtrados (local)      │                    │
   │                       │                        │                    │
   │ Selecciona grado      │                        │                    │
   │──────────────────────►│ Cascada: secciones +   │                    │
   │◄── secciones y        │ pensums filtrados      │                    │
   │     pensums filtrados │ (local)                │                    │
   │                       │                        │                    │
   │ Llena formulario      │                        │                    │
   │ Click [Guardar]       │                        │                    │
   │──────────────────────►│ POST /pevaluacions     │                    │
   │                       │───────────────────────►│                    │
   │                       │                        │─ INSERT pevaluacion│
   │                       │                        │───────────────────►│
   │                       │◄──── 201 Created       │◄── OK             │
   │◄── Éxito + refresca   │                        │                    │
   │     tabla             │                        │                    │
```

---

## 12. Dependencias y librerías

| Librería | Uso en el módulo |
|----------|------------------|
| Laravel Livewire 2.5 | Reactividad de filtros, cascada, formulario en sidebar |
| Bootstrap 4 | Sidebar, tabla, badges, formulario, paginación |
| FontAwesome 5 | Iconos (fa-edit, fa-trash, fa-plus, fa-inbox, fa-redo, fa-lock) |
| SweetAlert2 | Alertas de éxito y confirmación |
| Carbon (PHP) | Comparación de fechas para lapso cerrado |
| MySQL JOINs + subconsultas | Relaciones y withCount para actividades |

---

*Documento generado a partir del análisis del código fuente de SAEFL. Validado contra: `PevaluacionController.php`, `IndexComponent.php`, `updatedTrait.php`, `Leader.php` (getPevaluacionesForLeader), `Pevaluacion.php` (modelo), migraciones, y todas las vistas Blade del módulo.*
