# Gestión de Asignaturas (Control de Estudios) — Documento Técnico Completo

> **Versión:** 1.0 — Validada contra código fuente real (Laravel 8.x / PHP 8.2)
> **Módulo:** `control.configuraciones.asignatura` — CRUD de asignaturas del pensum académico.
> **Propósito:** Replicación en NextJS + API REST (Laravel o Node).
> **Arquitectura fuente:** Controlador tradicional (sin Livewire) con Blade + jQuery + DataTables.

---

## 1. Introducción

El módulo **Gestión de Asignaturas** dentro del submódulo **Configuraciones** del **Control de Estudios** (`is_control`) permite el mantenimiento completo del catálogo de asignaturas (materias) que conforman los planes de estudio de la institución.

Una asignatura define:
- **Identificación**: código único, abreviación, nombre
- **Plan de estudio**: `pestudio_id` (al cual pertenece)
- **Carga horaria**: horas teóricas y prácticas semanales
- **Unidades de crédito**: valor crediticio académico
- **Flags booleanos**: si afecta índice académico, sujeta a pérdida por reglamento, reparable, visible en documentos oficiales, grupo estable
- **Prelaciones**: texto libre con las materias previas requeridas
- **Escala de evaluación**: `escala_id` (numérica, cualitativa o mixta)

A diferencia de los módulos anteriores de Planning, este es un CRUD **tradicional** (sin Livewire) con DataTables para la visualización en tabla.

---

## 2. Arquitectura del Dominio

### 2.1 Cadena de dependencias

```
Asignatura (materia / asignatura)
  ├── pestudio_id → Pestudio (plan de estudio)
  ├── escala_id → Escala (escala de evaluación: numérica, cualitativa)
  ├── pensums[] → Pensum (asignación a grado+año)
  │     ├── grado_id → Grado
  │     └── pevaluacions[] → Pevaluacion (carga académica)
  ├── prelacions[] → Prelacion (prelaciones como relación)
  ├── campo_conocimientos[] → CampoConocimiento
  │     └── area_conocimiento_id → AreaConocimiento
  │           └── leader_id → Leader
  └── (SoftDeletes)
```

### 2.2 Árbol de archivos del módulo

```
routes/
  web.php                                              ← grupo /app/control con middleware is_control
  app/control.php                                      ← require de subrutas de control
  app/tab/asignaturas.php                              ← 6 rutas CRUD

app/
  Http/
    Controllers/Administracion/Tab/Configuracion/
      AsignaturaController.php                         ← 6 métodos: index, create, store, edit, update, destroy
    Requests/Administracion/Configuracion/
      CreateAsignaturaRequest.php                      ← validación: code unique
      UpdateAsignaturaRequest.php                      ← validación: code unique ignorando ID actual
  Models/
    app/
      Pescolar/
        Asignatura.php                                 ← modelo con 6 relaciones + trait Lists
        Functions/Asignatura/Lists.php                 ← trait: 3 métodos de listado para selects
        Pestudio.php                                   ← plan de estudio (belongsTo)
        Escala.php                                     ← escala de notas (belongsTo)
        Pensum.php                                     ← asignación a grado (hasMany)
        Prelacion.php                                  ← prelaciones (hasMany)
        CampoConocimiento.php                          ← campo de conocimiento (hasMany)

resources/views/
  administracion/configuraciones/asignaturas/
    index.blade.php                                    ← listado principal
    create.blade.php                                   ← formulario de creación
    edit.blade.php                                     ← formulario de edición
    form/fields.blade.php                              ← campos del formulario (compartido)
    table/index.blade.php                              ← DataTable
    partials/details.blade.php                         ← modal de detalles (solo lectura)
    partials/resumen/edit.blade.php                    ← resumen de relaciones (sidebar edición)
    partials/resumen/create.blade.php                  ← resumen informativo (sidebar creación)
    menus/index.blade.php                              ← botones de acción (listado)
    menus/create.blade.php                             ← botones de acción (crear)
    menus/edit.blade.php                               ← botones de acción (editar)
    menus/show.blade.php                               ← botones de acción (detalle)
```

---

## 3. Validación contra código fuente

### 3.1 Hallazgos clave

| # | Tópico | Detalle |
|---|--------|---------|
| 1 | **Sin Livewire** | El módulo usa controlador tradicional + Blade. Sin reactividad, sin componente Livewire. Las validaciones ocurren del lado del servidor con Form Requests. |
| 2 | **`is_control` middleware** | El grupo de rutas usa `is_control` (no `is_planning` ni `is_admin`). La ruta real es `/app/control/configuraciones/asignatura/index`. |
| 3 | **Namespace `Administracion`** | Aunque la ruta está bajo el grupo `control`, el namespace es `App\Http\Controllers\Administracion\Tab\Configuracion`, no `Control`. |
| 4 | **`escala_id` sin migración** | El modelo declara `escala_id` en fillable, pero la migración original no tiene esa columna. `tescala` (enum) está en la migración pero no en el modelo. La columna `escala_id` se agregó directamente o en migración no respaldada. |
| 5 | **SoftDeletes comentado en modelo** | La migración incluye `softDeletes()` pero el modelo tiene `// use SoftDeletes;` comentado. Sin embargo, el controlador llama `$asignatura->delete()` que funciona gracias al trait `SoftDeletes` de la clase base `Model` — en Laravel 8, `delete()` en el modelo hace soft-delete solo si la tabla tiene `deleted_at`. |
| 6 | **Prelaciones como texto libre** | El campo `prelacions` es un VARCHAR de texto libre, no una tabla relación. Existe la tabla `prelacions` y el modelo `Prelacion`, pero el formulario de asignatura guarda prelaciones como texto. |
| 7 | **5 campos booleanos como ENUM string** | `enable_academic_index`, `enable_lost_regulation`, `enable_official_doc`, `enable_repairable`, `enable_grupo_estable` son `ENUM('true','false')` en DB, no booleanos reales. |
| 8 | **DataTables con jQuery** | La tabla incluye `@include('administracion.datatables.default')` que agrega funcionalidad DataTables (búsqueda, ordenación, paginación en frontend). |
| 9 | **Sin paginación en backend** | `Asignatura::all()` obtiene TODOS los registros. La paginación visual es manejada por DataTables en el frontend. |
| 10 | **Destroy con soporte AJAX** | El método `destroy()` detecta `$request->ajax()` y retorna JSON en lugar de redirect, permitiendo eliminación sin recargar. |
| 11 | **`code_sm` con max 4 caracteres** | Validación: `max:4`. El campo `code` tiene `max:10` y es UNIQUE. |

### 3.2 Validación de rutas

| Ruta | Método | Controlador | Middleware | Nombre |
|------|--------|-------------|------------|--------|
| `GET /control/configuraciones/asignatura/index` | `index` | `Administracion\Tab\Configuracion\AsignaturaController` | `auth`, `is_control` | `administracion.configuraciones.asignaturas.index` |
| `GET /control/configuraciones/asignatura/create` | `create` | mismo | `auth`, `is_control` | `administracion.configuraciones.asignaturas.create` |
| `POST /control/configuraciones/asignatura/store` | `store` | mismo | `auth`, `is_control` | `administracion.configuraciones.asignaturas.store` |
| `GET /control/configuraciones/asignatura/edit/{id}` | `edit` | mismo | `auth`, `is_control` | `administracion.configuraciones.asignaturas.edit` |
| `PUT /control/configuraciones/asignatura/update/{id}` | `update` | mismo | `auth`, `is_control` | `administracion.configuraciones.asignaturas.update` |
| `DELETE /control/configuraciones/asignatura/destroy/{id}` | `destroy` | mismo | `auth`, `is_control` | `administracion.configuraciones.asignaturas.destroy` |

Registro en `routes/web.php`:
```php
// Grupo Control de Estudios
Route::group(['prefix' => 'control', 'middleware' => ['is_control']], function () {
    require (__DIR__ . '/app/control.php');
    // control.php → require __DIR__ . '/tab/asignaturas.php';
});
```

---

## 4. Lógica de Negocio — Especificación Completa

### 4.1 Reglas de negocio

**RN-01: Código único.**
El campo `code` debe ser único en toda la tabla `asignaturas`. En creación se valida con `unique:asignaturas`. En edición se ignora el ID actual: `unique:asignaturas,code,{id}`.

**RN-02: Abreviación máxima 4 caracteres.**
`code_sm` tiene límite de 4 caracteres y se convierte a mayúsculas automáticamente vía JavaScript (`$('#code_sm').keyup(...)`).

**RN-03: Código máximo 10 caracteres.**
`code` tiene límite de 10 caracteres y se convierte a mayúsculas automáticamente vía JavaScript.

**RN-04: 5 flags booleanos como ENUM('true','false').**
Los campos `enable_academic_index`, `enable_lost_regulation`, `enable_official_doc`, `enable_repairable`, `enable_grupo_estable` se almacenan como strings `'true'` o `'false'`. Se renderizan como checkboxes en el formulario.

**RN-05: Asignatura protegida si tiene pensums.**
El botón de eliminar se deshabilita cuando `$asignatura->pensums->isNotEmpty()`. La clase CSS `disabled` se agrega manualmente en el Blade (no hay validación en backend).

**RN-06: Eliminación lógica (soft-delete).**
El método `destroy()` llama `$asignatura->delete()` que marca `deleted_at`. Soporta eliminación vía AJAX (retorna JSON) y estándar (flash + redirect).

**RN-07: Orden de presentación.**
`order` es un valor numérico (1-12) que define la posición de la asignatura en listados. Los métodos del trait `Lists` ordenan por `asignaturas.order`.

**RN-08: Carga horaria semanal.**
`hour_t_week` (teóricas) y `hour_p_week` (prácticas) son valores enteros de 0 a 10. Se almacenan como nullable.

**RN-09: Prelaciones como metadato.**
El campo `prelacions` es texto libre. El usuario escribe las materias previas requeridas sin formato estructurado. Existe un modelo `Prelacion` separado pero no se usa desde este formulario.

**RN-10: Escala de evaluación asociada.**
`escala_id` referencia a la tabla `escalas`. Permite definir si la asignatura usa escala numérica (1-20), cualitativa (A-B-C-D) o mixta.

### 4.2 Flujo de datos completo

```
[Usuario autenticado con rol is_control]
    │
    ├─(1) GET /app/control/configuraciones/asignatura/index
    │     └─ AsignaturaController@index()
    │           ├─ Asignatura::orderBy('created_at','DESC')->get()
    │           └─ view('administracion.configuraciones.asignaturas.index')
    │                 ├── table/index.blade.php (DataTable)
    │                 │    └── por cada fila: [Mostrar] [Editar] [Eliminar]
    │                 └── menus/index.blade.php (botones: [+ Nuevo] [↺])
    │
    ├─(2) GET .../asignatura/create
    │     └─ AsignaturaController@create()
    │           ├─ Pestudio::active('true')->get() → select
    │           ├─ Escala::all() → select
    │           └─ view('...asignaturas.create')
    │                 └── form/fields.blade.php (campos del formulario)
    │
    ├─(3) POST .../asignatura/store
    │     └─ CreateAsignaturaRequest (valida code unique)
    │     └─ AsignaturaController@store()
    │           ├─ Asignatura::create($request->all())
    │           ├─ Session::flash('operp_ok', ...)
    │           └─ redirect → index
    │
    ├─(4) GET .../asignatura/edit/{id}
    │     └─ AsignaturaController@edit($id)
    │           ├─ Asignatura::findOrFail($id)
    │           ├─ Pestudio::active('true') → select
    │           ├─ Escala::all() → select
    │           └─ view('...asignaturas.edit')
    │                 ├── form/fields.blade.php (precargado)
    │                 └── partials/resumen/edit.blade.php (pensums relacionados)
    │
    ├─(5) PUT .../asignatura/update/{id}
    │     └─ UpdateAsignaturaRequest (valida code unique ignorando {id})
    │     └─ AsignaturaController@update($id)
    │           ├─ findOrFail → fill → save
    │           └─ redirect → index
    │
    └─(6) DELETE .../asignatura/destroy/{id}
          └─ AsignaturaController@destroy($id)
                ├─ findOrFail → delete()
                ├─ If AJAX: return response()->json(...)
                └─ Else: Session::flash + redirect → index
```

---

## 5. Esquemas de Base de Datos

### 5.1 Tabla `asignaturas`

```sql
CREATE TABLE `asignaturas` (
  `id`                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pestudio_id`             INT UNSIGNED NOT NULL COMMENT 'Plan Estudio (FK pestudios)',
  `escala_id`               INT UNSIGNED NULL COMMENT 'Escala de Evaluación (FK escalas)',
  `code`                    VARCHAR(255) NOT NULL COMMENT 'Código único de la asignatura',
  `code_sm`                 VARCHAR(255) NOT NULL COMMENT 'Abreviación (máx 4 caracteres)',
  `name`                    VARCHAR(255) NOT NULL COMMENT 'Nombre de la asignatura',
  `tescala`                 ENUM('NUMÉRICA','CUALITATIVA','NUMÉRICA Y CUALITATIVA') NULL COMMENT 'Tipo de evaluación (migración legacy)',
  `order`                   INT NOT NULL COMMENT 'Orden de presentación (1-12)',
  `hour_t_week`             INT NULL COMMENT 'Horas teóricas semanales',
  `hour_p_week`             INT NULL COMMENT 'Horas prácticas semanales',
  `unid_credit`             INT NULL COMMENT 'Unidades de crédito',
  `approved_credit_unir`    INT NULL COMMENT 'Unidades de crédito aprobadas',
  `enable_academic_index`   ENUM('true','false') NULL COMMENT 'Afecta índice académico',
  `enable_lost_regulation`  ENUM('true','false') NULL COMMENT 'Sujeta a pérdida por reglamento',
  `enable_official_doc`     ENUM('true','false') NULL COMMENT 'Visible en documentos oficiales',
  `enable_repairable`       ENUM('true','false') NULL COMMENT 'Reparable',
  `enable_grupo_estable`    ENUM('true','false') NULL DEFAULT 'false' COMMENT 'Contiene grupo estable',
  `observations`            VARCHAR(255) NULL COMMENT 'Observaciones',
  `prelacions`              VARCHAR(255) NULL COMMENT 'Prelaciones (texto libre)',
  `deleted_at`              TIMESTAMP NULL,
  `created_at`              TIMESTAMP NULL,
  `updated_at`              TIMESTAMP NULL,

  INDEX `asignaturas_pestudio_id_index` (`pestudio_id`),
  UNIQUE INDEX `asignaturas_code_unique` (`code`),

  CONSTRAINT `asignaturas_pestudio_id_foreign`
    FOREIGN KEY (`pestudio_id`) REFERENCES `pestudios`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.2 Tabla `pestudios` (relacionada)

```sql
CREATE TABLE `pestudios` (
  `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `peducativo_id`     INT UNSIGNED NULL,
  `code`              VARCHAR(255) NOT NULL,
  `name`              VARCHAR(255) NOT NULL,
  `planning_module`   TINYINT(1) DEFAULT 0,
  `activities_avr`    INT NULL,
  `status_active`     ENUM('true','false') DEFAULT 'true',
  `deleted_at`        TIMESTAMP NULL,
  `created_at`        TIMESTAMP NULL,
  `updated_at`        TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.3 Tabla `escalas` (relacionada)

```sql
CREATE TABLE `escalas` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(255) NOT NULL,
  `minimo`      INT NULL,
  `maximo`      INT NULL,
  `created_at`  TIMESTAMP NULL,
  `updated_at`  TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.4 Tabla `pensums` (hija)

```sql
CREATE TABLE `pensums` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `asignatura_id`   INT UNSIGNED NOT NULL COMMENT 'FK asignaturas',
  `grado_id`        INT UNSIGNED NOT NULL COMMENT 'FK grados',
  `pestudio_id`     INT UNSIGNED NOT NULL COMMENT 'FK pestudios',
  `status_active`   TINYINT(1) DEFAULT 1,
  `status_active_diagnostic` TINYINT(1) DEFAULT 0,
  `deleted_at`      TIMESTAMP NULL,
  `created_at`      TIMESTAMP NULL,
  `updated_at`      TIMESTAMP NULL,
  FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 6. Modelo de Datos — API REST para exportación

### 6.1 Endpoints propuestos

#### `GET /api/control/asignaturas`

Listado completo de asignaturas (con paginación).

**Query params:** `search`, `pestudio_id`, `enable_academic_index`, `page`, `per_page`, `sort_by`, `sort_dir`

**Response (200):**
```json
{
  "data": [
    {
      "id": 15,
      "code": "MT-01",
      "code_sm": "MT",
      "name": "MATEMÁTICA",
      "pestudio": { "id": 2, "code": "MG", "name": "MEDIA GENERAL" },
      "escala": { "id": 1, "name": "Escala 1-20" },
      "order": 1,
      "hour_t_week": 4,
      "hour_p_week": 2,
      "unid_credit": 4,
      "enable_academic_index": true,
      "enable_lost_regulation": true,
      "enable_official_doc": true,
      "enable_repairable": true,
      "enable_grupo_estable": false,
      "observations": null,
      "prelacions": null,
      "pensums_count": 3,
      "created_at": "2025-09-01T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 62
  }
}
```

#### `GET /api/control/asignaturas/{id}`

Detalle de asignatura con relaciones.

**Response (200):**
```json
{
  "id": 15,
  "code": "MT-01",
  "code_sm": "MT",
  "name": "MATEMÁTICA",
  "pestudio": { "id": 2, "code": "MG", "name": "MEDIA GENERAL" },
  "escala": { "id": 1, "name": "Escala 1-20", "minimo": 1, "maximo": 20 },
  "order": 1,
  "hour_t_week": 4,
  "hour_p_week": 2,
  "unid_credit": 4,
  "approved_credit_unir": 4,
  "enable_academic_index": true,
  "enable_lost_regulation": true,
  "enable_official_doc": true,
  "enable_repairable": true,
  "enable_grupo_estable": false,
  "observations": null,
  "prelacions": null,
  "pensums": [
    { "id": 89, "grado": { "id": 5, "name": "5TO AÑO" }, "status_active": true }
  ],
  "campo_conocimientos": []
}
```

#### `POST /api/control/asignaturas`

Crear nueva asignatura.

**Request body:**
```json
{
  "pestudio_id": 2,
  "escala_id": 1,
  "code": "MT-01",
  "code_sm": "MT",
  "name": "MATEMÁTICA",
  "order": 1,
  "hour_t_week": 4,
  "hour_p_week": 2,
  "unid_credit": 4,
  "approved_credit_unir": 4,
  "enable_academic_index": true,
  "enable_lost_regulation": true,
  "enable_official_doc": true,
  "enable_repairable": true,
  "enable_grupo_estable": false,
  "observations": "",
  "prelacions": ""
}
```

**Validaciones:**
```json
{
  "code": "required|max:10|unique:asignaturas",
  "code_sm": "required|max:4",
  "name": "required|string|max:255",
  "pestudio_id": "required|integer|exists:pestudios,id",
  "escala_id": "nullable|integer|exists:escalas,id",
  "order": "nullable|integer|min:1|max:12",
  "hour_t_week": "nullable|integer|min:0|max:10",
  "hour_p_week": "nullable|integer|min:0|max:10",
  "enable_academic_index": "boolean",
  "enable_lost_regulation": "boolean",
  "enable_official_doc": "boolean",
  "enable_repairable": "boolean",
  "enable_grupo_estable": "boolean"
}
```

#### `PUT /api/control/asignaturas/{id}`

Actualizar asignatura. `code` unique ignora el ID actual: `unique:asignaturas,code,{id}`.

#### `DELETE /api/control/asignaturas/{id}`

Eliminación lógica (soft-delete). Retorna 409 si tiene pensums asociados.

**Error 409:**
```json
{
  "message": "No se puede eliminar: la asignatura tiene pensums asociados.",
  "pensums_count": 3
}
```

#### `GET /api/control/asignaturas/filters`

Datos para poblar filtros y formularios.

**Response:**
```json
{
  "pestudios": [ { "id": 1, "code": "MG", "name": "MEDIA GENERAL" } ],
  "escalas": [ { "id": 1, "name": "Escala 1-20" } ],
  "options": {
    "order_range": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
    "hours_range": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
  }
}
```

---

## 7. Especificación de Componentes (NextJS + Tailwind)

### 7.1 Página principal: `ControlAsignaturasPage`

```
┌────────────────────────────────────────────────────────────────────┐
│  Control de Estudios > Configuraciones > Asignaturas               │
├────────────────────────────────────────────────────────────────────┤
│  [+ Nueva Asignatura] [↺ Refrescar]                               │
├────────────────────────────────────────────────────────────────────┤
│  [Buscar...]                                                       │
├─────┬────────┬────────┬────────────┬────────┬────────┬─────┬──────┤
│  N  │P.Est. │Abrev.  │ Código     │Nombre  │H.Teór.│H.Pr.│ Acc. │
├─────┼────────┼────────┼────────────┼────────┼────────┼─────┼──────┤
│  1  │MG      │MT      │ MT-01      │MATEMÁT.│  04    │ 02  │[👁][✏️][🗑]│
│  2  │MG      │LG      │ LG-01      │LENGUA  │  03    │ 02  │[👁][✏️][🗑]│
│  3  │MT      │FÍS     │ FS-01      │FÍSICA  │  02    │ 02  │[👁][✏️][🗑]│
├─────┴────────┴────────┴────────────┴────────┴────────┴─────┴──────┤
│  Showing 1 to 3 of 62 entries                   [1] [2] [3] ...  │
└────────────────────────────────────────────────────────────────────┘
```

### 7.2 Formulario de creación/edición

```
┌─ Datos de la Asignatura ───────────────────────────────────────┐
│  Plan de Estudio:  [MEDIA GENERAL ▼]                            │
│  Código (10 car.): [MT-01       ]  (mayúsculas automático)      │
│  Abreviación (4):  [MT          ]  (mayúsculas automático)      │
│  Nombre:           [MATEMÁTICA                                  │
│  Orden:            [1 ▼]                                         │
│  Horas Teóricas:   [4 ▼]   Horas Prácticas: [2 ▼]               │
│  Unidades Crédito: [4    ]   Créd. Aprobados: [4   ]             │
│  Escala:           [Escala 1-20 ▼]                               │
│                                                                   │
│  ☑ Tomada en cuenta para índice académico                        │
│  ☑ Sujeta a pérdida por reglamento                               │
│  ☑ Mostrar en documentos oficiales                               │
│  ☑ Reparable                                                     │
│  ☐ Contiene grupo estable                                        │
│                                                                   │
│  Observaciones: [_____________________________]                  │
│  Prelaciones:   [_____________________________]                  │
│                                                                   │
│  [████ Guardar ████]                                              │
└───────────────────────────────────────────────────────────────────┘
```

### 7.3 Árbol de componentes

```
ControlAsignaturasPage
├── PageHeader (título + breadcrumb)
├── ActionBar (botones: Nueva Asignatura, Refrescar)
├── AsignaturaTable
│   ├── SearchInput (filtro DataTable-style)
│   ├── TableHeader (ordenable por columna)
│   └── AsignaturaRow (× N)
│       ├── ShowButton → DetailsModal (solo lectura)
│       ├── EditLink → navigate a edit page
│       └── DeleteButton (deshabilitado si tiene pensums)
│           └── ConfirmDialog → destroy
├── CreateAsignaturaPage (ruta separada)
│   └── AsignaturaForm
└── EditAsignaturaPage (ruta separada)
    ├── AsignaturaForm (precargado)
    └── PensumSummaryCard (pensums relacionados)
```

### 7.4 Estados de cada componente

| Componente | Loading | Empty | Error | Success |
|-----------|---------|-------|-------|---------|
| `AsignaturaTable` | Skeleton 5 filas | "No hay asignaturas registradas" | Toast error | DataTable |
| `AsignaturaForm` | N/A | Campos vacíos (create) o precargados (edit) | Errores inline (code unique, max length) | Submit exitoso → redirect |
| `DetailsModal` | Spinner | N/A | N/A | Todos los campos en 2 columnas |
| `DeleteButton` | Spinner en el botón | N/A | Toast error | Eliminación + refresh |
| `PensumSummaryCard` | Spinner | "Sin pensums asociados" | N/A | Lista de pensums |

---

## 8. Validaciones y Edge Cases

### 8.1 Validaciones del servidor

| Campo | Regla Create | Regla Update | Código fuente |
|-------|-------------|-------------|---------------|
| `code` | `required\|max:10\|unique:asignaturas` | `required\|max:10\|unique:asignaturas,code,{id}` | CreateAsignaturaRequest / UpdateAsignaturaRequest |
| `code_sm` | `required\|max:4` | `required\|max:4` | Ambos Requests |
| `name` | No validado explícitamente (solo `$request->all()`) | Igual | Se confía en la base de datos |

**Nota:** La validación es mínima — solo `code` y `code_sm` tienen reglas explícitas. Los demás campos se guardan sin validación del lado del servidor.

### 8.2 Edge cases

| Caso | Comportamiento esperado |
|------|------------------------|
| Código duplicado | Error 422: "El código ya existe" |
| Asignatura con pensums asociados | Botón eliminar deshabilitado visualmente (no hay validación backend de integridad referencial) |
| Eliminación vía AJAX | Retorna JSON `{ messenge, operation }`, sin recargar página |
| `enable_grupo_estable` = null | Se muestra como "NO" en tabla (tratado como false) |
| Campo `code` en minúsculas | JS `.toUpperCase()` convierte automáticamente |
| `escala_id` nulo | La asignatura no tiene escala asociada |
| `pestudio_id` eliminado (soft-delete) | La Foreign Key impide asignar a pestudio inexistente |

---

## 9. Plan de Migración: Laravel/Blade → NextJS + API

### Fase 1: Backend API

| Prioridad | Endpoint | Descripción |
|-----------|----------|-------------|
| P0 | `GET /api/control/asignaturas` | Listado paginado |
| P0 | `GET /api/control/asignaturas/{id}` | Detalle con relaciones |
| P0 | `GET /api/control/asignaturas/filters` | Opciones de filtros |
| P1 | `POST /api/control/asignaturas` | Crear con validación |
| P1 | `PUT /api/control/asignaturas/{id}` | Actualizar con validación |
| P1 | `DELETE /api/control/asignaturas/{id}` | Soft-delete con verificación |

### Fase 2: Frontend NextJS

| Prioridad | Página/Componente | Descripción |
|-----------|------------------|-------------|
| P0 | `useAsignaturas` | Hook: listado, paginación, búsqueda |
| P0 | `AsignaturaTable` | DataTable con ordenación |
| P1 | `AsignaturaForm` | Formulario compartido crear/editar |
| P1 | `AsignaturaCreatePage` | Página de creación |
| P1 | `AsignaturaEditPage` | Página de edición |
| P2 | `AsignaturaDeleteButton` | Confirmación + eliminación |

### Fase 3: Migración de datos

| Tarea | Detalle |
|-------|---------|
| Migrar ENUM('true','false') a boolean | Normalizar flags a `boolean` real |
| Validar `escala_id` existente | Verificar que todas las asignaturas tengan escala asignada |
| Indexar `deleted_at` | Asegurar performance en soft-deletes |

### Fase 4: Pruebas

| Tipo | Casos |
|------|-------|
| Unitarias | Validación de code unique, transformación a mayúsculas |
| Integración | CRUD completo: crear → listar → editar → eliminar |
| Integración | Protección: eliminar asignatura con pensums (debe fallar) |
| Componente | Formulario: errores inline, loading states |
| E2E | Flujo: login → listar → crear → ver en tabla → editar → eliminar |

---

## 10. Dependencias y librerías

| Librería | Uso en el módulo |
|----------|------------------|
| jQuery + DataTables | Búsqueda, ordenación y paginación del lado del cliente |
| Bootstrap 4 | Layout, cards, tablas, formularios, modales |
| FontAwesome 5 | Iconos |
| Laravel Collective (Form) | Generación de formularios HTML (`Form::open`, `Form::select`, etc.) |
| Laravel Form Requests | Validación encapsulada (CreateAsignaturaRequest, UpdateAsignaturaRequest) |
| SweetAlert2 | Vía app.js global |

---

## 11. Estructura de la tabla (resumen visual)

| Columna | Tipo | Requerido | Único | Default |
|---------|------|-----------|-------|---------|
| `id` | INT UNSIGNED AUTO_INCREMENT | ✅ | ✅ | — |
| `pestudio_id` | INT UNSIGNED | ✅ | ❌ | — |
| `escala_id` | INT UNSIGNED | ❌ | ❌ | NULL |
| `code` | VARCHAR(255) | ✅ | ✅ | — |
| `code_sm` | VARCHAR(255) | ✅ | ❌ | — |
| `name` | VARCHAR(255) | ✅ | ❌ | — |
| `order` | INT | ✅ | ❌ | — |
| `hour_t_week` | INT | ❌ | ❌ | NULL |
| `hour_p_week` | INT | ❌ | ❌ | NULL |
| `enable_academic_index` | ENUM('true','false') | ❌ | ❌ | NULL |
| `enable_lost_regulation` | ENUM('true','false') | ❌ | ❌ | NULL |
| `enable_official_doc` | ENUM('true','false') | ❌ | ❌ | NULL |
| `enable_repairable` | ENUM('true','false') | ❌ | ❌ | NULL |
| `enable_grupo_estable` | ENUM('true','false') | ❌ | ❌ | 'false' |
| `deleted_at` | TIMESTAMP NULL | ❌ | ❌ | NULL |

---

*Documento generado a partir del análisis del código fuente de SAEFL. Validado contra: `AsignaturaController.php`, `Asignatura.php` (modelo), `Lists.php` (trait), `CreateAsignaturaRequest.php`, `UpdateAsignaturaRequest.php`, migración `create_asignaturas_table`, y todas las vistas Blade del módulo.*
