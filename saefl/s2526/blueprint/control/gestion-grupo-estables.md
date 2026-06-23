# Blueprint: Gestión de Grupos Estables (Grupo Estable)

> **Módulo:** Control de Estudios → Configuraciones → Grupos Estables  
> **Ruta base:** `/app/control/configuraciones/grupo_estables/index`  
> **Middleware:** `is_control` (heredado de `routes/app/control.php`)  
> **Propósito:** Administrar subgrupos fijos de estudiantes dentro de una sección para asignaturas/actividades específicas (educación especial, talleres, grupos de refuerzo, etc.).

---

## 1. Validación Contra Código Fuente

### 1.1. Rutas (`routes/app/tab/grupo_estables.php`)

| Método | URI | Acción | Nombre de Ruta |
|--------|-----|--------|----------------|
| GET | `/configuraciones/grupo_estables/index` | `GrupoEstableController@index` | `administracion.configuraciones.grupo_estables.index` |
| GET | `/configuraciones/grupo_estables/create` | `GrupoEstableController@create` | `administracion.configuraciones.grupo_estables.create` |
| POST | `/configuraciones/grupo_estables/store` | `GrupoEstableController@store` | `administracion.configuraciones.grupo_estables.store` |
| GET | `/configuraciones/grupo_estables/edit/{id}` | `GrupoEstableController@edit` | `administracion.configuraciones.grupo_estables.edit` |
| PUT | `/configuraciones/grupo_estables/update/{id}` | `GrupoEstableController@update` | `administracion.configuraciones.grupo_estables.update` |
| DELETE | `/configuraciones/grupo_estables/destroy/{id}` | `GrupoEstableController@destroy` | `administracion.configuraciones.grupo_estables.destroy` |

**Cargado por:** `routes/app/control.php` (línea 45: `require __DIR__ . '/tab/grupo_estables.php';`)

**Grupo de middleware aplicado:** `is_control` (definido en `routes/app/control.php`)

### 1.2. Controlador (`app/Http/Controllers/Administracion/Tab/Configuracion/GrupoEstableController.php`)

**6 métodos — CRUD tradicional (sin Livewire):**

| Método | Lógica | Vista |
|--------|--------|-------|
| `index()` | `GrupoEstable::all()->sortByDesc('created_at')` + COLUMN_COMMENTS | `administracion.configuraciones.grupo_estables.index` |
| `create()` | COLUMN_COMMENTS | `...grupo_estables.create` |
| `store(CreateGrupoEstableRequest)` | `GrupoEstable::create($request->all())` + flash | redirect → `create` |
| `edit($id)` | `GrupoEstable::findOrFail($id)` + `list_escala` + `list_pestudio` + COLUMN_COMMENTS | `...grupo_estables.edit` |
| `update(UpdateGrupoEstableRequest, $id)` | `findOrFail` + `fill($request->all())` + `save()` + flash | redirect → `edit` |
| `destroy($id, Request)` | `findOrFail` + `delete()` + respuesta JSON si AJAX | redirect → `index` |

### 1.3. Form Requests

**CreateGrupoEstableRequest** (`app/Http/Requests/Administracion/Configuracion/CreateGrupoEstableRequest.php`):
```php
'code'    => 'required|max:10|unique:grupo_estables',
'code_sm' => 'required|max:4',
'name'    => 'required',
```

**UpdateGrupoEstableRequest** (`app/Http/Requests/Administracion/Configuracion/UpdateGrupoEstableRequest.php`):
```php
'code'    => 'required|max:10|unique:grupo_estables,code,'.$this->route->parameter('id'),
'code_sm' => 'required|max:4',
'name'    => 'required',
```

### 1.4. Modelo (`app/Models/app/Estudiante/GrupoEstable.php`)

- **Tabla:** `grupo_estables`
- **SoftDeletes:** NO (comentado: `// use Illuminate\Database\Eloquent\SoftDeletes;`)
- **11 campos fillable:** `code`, `code_sm`, `name`, `hour_t_week`, `hour_p_week`, `size_max`, `description`, `observations`, `status_belongs_ins`, `status_active`
- **COLUMN_COMMENTS:** Mapeo nombre → etiqueta en español (10 entradas)
- **Relaciones:**
  - `inscripcion()` → `hasOne(Inscripcion)` — Un grupo pertenece a una inscripción (singular, pero probablemente debería ser hasMany)
  - `profesor_gestables()` → `hasMany(ProfesorGestable)` — Profesores asignados al grupo
  - `pestudio()` → `belongsTo(Pestudio)` — **COLUMNA `pestudio_id` NO EXISTE en DB** (relación huérfana)
  - `hnotas()` → `hasMany(Hnota)` — Notas históricas
- **Métodos estáticos de listado** (usados por 30+ componentes Livewire en toda la app):
  - `list_grupo_estable_code()` → `[id => code]`
  - `list_grupo_estable()` → `[id => name]`
  - `list_grupo_estable_full()` → `[id => "name || code"]` order by name
  - `list_grupo_estable_full_inscriptions()` → Solo grupos con inscripciones activas (join con `inscripcions`, whereNull deleted_at)
  - `list_grupo_estable_active()` → Solo `status_active = 'true'`
- **Método de cálculo:** `getNota($estudiant_id, $seccion_id, $lapso_id, $round=2)` — Calcula promedio de notas del grupo mediante joins: `boletins → evaluacions → pevaluacions → grupo_estables`
- **Accessor:** `getStatusDeleteAttribute()` — Retorna `true` si NO existen `Hnota` para el grupo

### 1.5. Migraciones

| Archivo | Propósito |
|---------|-----------|
| `backUps/old/temp/2019_08_24_154140_create_grupo_estables_table.php` | Creación inicial: `id`, `name`, `code`, `code_sm`, `hour_t_week`, `hour_p_week`, `description`, `observations`, timestamps |
| `backUps/grupo_estable/2023_11_02_105156_add_status_active_to_grupo_estables.php` | Agrega `status_active` ENUM('true','false') default 'false' después de `status_belongs_ins` |

**Hallazgo crítico:** `size_max` y `status_belongs_ins` existen en la tabla física y en `$fillable`, pero **NO tienen migraciones**. Fueron agregados directamente a la BD o mediante una migración perdida.

### 1.6. Vistas (14 archivos en `resources/views/administracion/configuraciones/grupo_estables/`)

| Archivo | Estado | Notas |
|---------|--------|-------|
| `index.blade.php` | ✅ Activo | DataTable, layout dashboard |
| `create.blade.php` | ✅ Activo | Formulario estándar |
| `edit.blade.php` | ✅ Activo | Formulario poblado |
| `form/fields.blade.php` | ✅ Activo | Campos compartidos (name, code, code_sm, hour_t_week, hour_p_week, description, observations, status_active, status_belongs_ins) |
| `partials/details.blade.php` | ✅ Activo | Modal de detalles (show vía modal bootstrap) |
| `table/index.blade.php` | ✅ Activo | DataTable con columnas: N, Abreviación, Código, Grupo Estable, H.Teóricas, H.Prácticas, Acciones |
| `menus/index.blade.php` | ✅ Activo | Botones: Crear, Ir atrás, Refrescar |
| `menus/create.blade.php` | ✅ Activo | Botones: Listado, Ir atrás, Refrescar |
| `menus/edit.blade.php` | ✅ Activo | Botones: Listado, Crear nuevo, Ir atrás, Refrescar |
| `partials/resumen/create.blade.php` | ⚠️ Stale | Referencia `$planpagos`, `$autoridad4` — copiado de Plan de Pago |
| `partials/resumen/edit.blade.php` | ❌ **Incorrecto** | Referencia `$asignatura->pensums` — **copiado de Asignatura** (no relacionado a GrupoEstable) |
| `menus/show.blade.php` | ❌ **Stale** | Referencia `route('users.create')` — **copiado de Users** |
| `crud.blade.php` | ❌ Stale | Layout incompleto que referencia `administracion.retiros` — no usado |
| `table/crud.blade.php` | ❌ Stale | Tabla de estudiantes para retiros — no usado |

### 1.7. JavaScript

- **Usado por index:** `public/js/models/default/destroy.js` — genérico, reemplaza `:IDENT_ID` del form con el ID real de la fila
- **Archivo huérfano:** `public/js/models/grupo_estables/destroy.js` — **NO incluido por ninguna vista** (existe pero no se carga); usa `:GRUPO_ESTABLE_ID` que además no coincide con el placeholder `:IDENT_ID` del form

### 1.8. Relaciones en otros modelos (12+ modelos referencian `grupo_estable_id`)

| Modelo | Campo | Uso |
|--------|-------|-----|
| `Inscripcion` | `grupo_estable_id` | Asignación estudiante → grupo |
| `Enrollment` | `grupo_estable_id` | Enrollment (herencia de inscripción) |
| `Preinscripcion` | `grupo_estable_id` | Pre-inscripciones |
| `ProfesorGestable` | `grupo_estable_id` | Pivote profesor ↔ grupo |
| `Pevaluacion` | `grupo_estable_id` | Evaluación vinculada a grupo |
| `Hnota` | `grupo_estable_id` | Notas históricas |
| `Census` | `grupo_estable_id` | Censo estudiantil |
| `Pestudio` | `grupo_estables()` | `hasMany` (inversa) |
| `Pensum` | `getGrupoEstablesAttribute()` | Acceso vía joins |
| `Asignatura` | `enable_grupo_estable` | Flag booleano para activar grupos en una materia |
| `Estudiant` | `grupo_estable` (accessor) | `$this->inscripcion->grupo_estable` |
| `EvaluacionGestable` | `profesor_gestable_id` | Pivote evaluación ↔ profesor_gestable (indirecto) |

---

## 2. Reglas de Negocio

1. **Grupo como subconjunto de sección:** Un `GrupoEstable` representa un subgrupo fijo de estudiantes dentro de una `Seccion`, para una asignatura o actividad específica.
2. **Asignatura opta por grupos:** El flag `enable_grupo_estable = 'true'` en `Asignatura` determina si esa materia usa grupos estables.
3. **Cada estudiante pertenece a un grupo:** Via `inscripcions.grupo_estable_id`. Un estudiante solo puede estar en un grupo por período escolar.
4. **Profesor asignado a grupo:** Via `profesor_gestables` — pivot que relaciona profesor + grupo + evaluación.
5. **Evaluación por grupo:** `Pevaluacion` puede tener `grupo_estable_id`, vinculando la evaluación a ese subgrupo específico.
6. **Cálculo de nota grupal:** El método `getNota()` calcula el promedio de notas de un estudiante dentro del grupo, uniendo boletins → evaluacions → pevaluacions → grupo_estables.
7. **Eliminación segura:** Solo se permite eliminar un grupo si no tiene notas históricas (`Hnota`). El accessor `status_delete` verifica esta condición.
8. **Grupos activos:** El campo `status_active` (ENUM 'true'/'false') permite activar/desactivar grupos sin eliminarlos. Los listados `_active()` filtran solo grupos activos.
9. **Código único:** `code` debe ser único en la tabla (validado vía FormRequest).
10. **Horas semanales:** `hour_t_week` (teóricas) y `hour_p_week` (prácticas) se seleccionan de 0 a 10 horas.

---

## 3. SQL Schema — Tabla `grupo_estables`

```sql
CREATE TABLE `grupo_estables` (
    `id`                int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name`              varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre',
    `code`              varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código',
    `code_sm`           varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Abreviatura',
    `hour_t_week`       smallint(6) DEFAULT NULL COMMENT 'Número de horas teóricas dictadas en la semana',
    `hour_p_week`       smallint(6) DEFAULT NULL COMMENT 'Número de horas prácticas dictadas en la semana',
    `size_max`          smallint(6) DEFAULT NULL COMMENT 'Cupos Máximos',
    `description`       varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descripción',
    `observations`      varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observaciones',
    `status_belongs_ins` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'true' COMMENT 'Dictada en la institución',
    `status_active`     enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false' COMMENT 'Estado (Activo/Inactivo)',
    `created_at`        timestamp NULL DEFAULT NULL,
    `updated_at`        timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tablas con FK a `grupo_estables.id`:

- `inscripcions.grupo_estable_id` (no hay FK explícita)
- `enrollments.grupo_estable_id`
- `preinscripcions.grupo_estable_id`
- `profesor_gestables.grupo_estable_id`
- `pevaluacions.grupo_estable_id`
- `hnotas.grupo_estable_id`
- `censuses.grupo_estable_id`

---

## 4. Endpoints API REST (Propuesta Migración)

### Colección: `/api/v1/grupo-estables`

| Método | Endpoint | Descripción | Controlador Actual |
|--------|----------|-------------|-------------------|
| `GET` | `/api/v1/grupo-estables` | Listar todos (paginado, ordenado por created_at DESC) | `index()` |
| `GET` | `/api/v1/grupo-estables/:id` | Obtener un grupo | `edit()` parcial |
| `POST` | `/api/v1/grupo-estables` | Crear grupo | `store()` |
| `PUT` | `/api/v1/grupo-estables/:id` | Actualizar grupo | `update()` |
| `DELETE` | `/api/v1/grupo-estables/:id` | Eliminar grupo (verificar `status_delete`) | `destroy()` |

**Endpoints adicionales:**

| Método | Endpoint | Propósito |
|--------|----------|-----------|
| `GET` | `/api/v1/grupo-estables/activos` | Solo grupos activos (`status_active = true`) |
| `GET` | `/api/v1/grupo-estables/con-inscripciones` | Solo grupos con inscripciones activas |
| `GET` | `/api/v1/grupo-estables/:id/nota?estudiant_id=&seccion_id=&lapso_id=` | Calcular promedio de nota |

### Request Body (POST/PUT):

```json
{
    "code": "GRUPO-A",
    "code_sm": "GA",
    "name": "Grupo A - Taller de Lectura",
    "hour_t_week": 4,
    "hour_p_week": 2,
    "size_max": 25,
    "description": "Grupo avanzado de lectura comprensiva",
    "observations": "Se reúne los martes y jueves",
    "status_belongs_ins": "true",
    "status_active": "true"
}
```

### Response (200 OK):

```json
{
    "data": {
        "id": 1,
        "code": "GRUPO-A",
        "code_sm": "GA",
        "name": "Grupo A - Taller de Lectura",
        "hour_t_week": 4,
        "hour_p_week": 2,
        "size_max": 25,
        "description": "Grupo avanzado de lectura comprensiva",
        "observations": "Se reúne los martes y jueves",
        "status_belongs_ins": "true",
        "status_active": "true",
        "created_at": "2026-06-06T12:00:00.000000Z",
        "updated_at": "2026-06-06T12:00:00.000000Z",
        "nota_promedio": 15.5
    }
}
```

---

## 5. UI Wireframes

### 5.1. Pantalla de Listado (Index)

```
┌─────────────────────────────────────────────────────────────┐
│  [Administración > Configuraciones]                         │
├─────────────────────────────────────────────────────────────┤
│  Listado de los Grupos Estables       [+ Crear] [← Atrás]   │
│                                               [⟳ Refrescar]  │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ ┌─────────┬──────────┬────────┬──────────────┬──────────────┐│
│ │ N│Abrev. │ Código  │Grupo   │ H.Teór │H.Prác│  Acciones   ││
│ ├─────────┼──────────┼────────┼──────────────┼──────────────┤│
│ │ 1│ GA    │GRUPO-A  │Grupo A │   4    │  2  │[👁][✏️][🗑️] ││
│ │ 2│ GB    │GRUPO-B  │Grupo B │   3    │  3  │[👁][✏️][🗑️] ││
│ │ 3│ GC    │GRUPO-C  │Grupo C │   2    │  2  │[👁][✏️][🗑️] ││
│ └─────────┴──────────┴────────┴──────────────┴──────────────┘│
│                                         Mostrando 1-3 de 3  │
└─────────────────────────────────────────────────────────────┘
```

**Componentes:**
- **Header:** Título con badge de conteo
- **Menu bar:** Botones "Crear nuevo Grupo Estable", "Ir atrás", "Refrescar"
- **DataTable:** Columnas: N°, Abreviación, Código, Grupo Estable, H.Teóricas, H.Prácticas, Acciones
- **Acciones por fila:** Show (modal), Edit (link), Delete (SweetAlert2)
- **Botón Delete:** Deshabilitado si `status_delete = false` (tiene notas históricas)

### 5.2. Pantalla de Creación (Create)

```
┌─────────────────────────────────────────────────────────────┐
│  [Administración > Configuraciones]                         │
├─────────────────────────────────────────────────────────────┤
│  Registrar un nuevo Grupo Estable                           │
│              [Listado] [← Atrás] [⟳ Refrescar]              │
├─────────────────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ [Resumen]                          Período: 2025-2026  │ │
│ │                                    Institución: U.E...  │ │
│ │                                    Autoridad: ...       │ │
│ │                                    N. Planes: ...       │ │
│ └─────────────────────────────────────────────────────────┘ │
│ ┌─── Datos del Grupo Estable ──────────────────────────────┐ │
│ │                                                          │ │
│ │  Nombre:        [________________________________]       │ │
│ │  Código:        [________] (max 10 caracteres)          │ │
│ │  Abreviación:   [____] (max 4 caracteres)              │ │
│ │  H.Teóricas:    [0-10 ▼]                               │ │
│ │  H.Prácticas:   [0-10 ▼]                               │ │
│ │  Descripción:   [________________________________]       │ │
│ │  Observaciones: [________________________________]       │ │
│ │  Estado:        [Activo ▼]                              │ │
│ │  Dictada en     [SI ▼]                                  │ │
│ │  institución:                                           │ │
│ │                                                          │ │
│ │  [💾 Registrar]                                          │ │
│ │                                                          │ │
│ └──────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### 5.3. Modal de Detalles (Show)

```
┌─ Detalles del Grupo Estable ──────── [X] ─┐
│                                            │
│  Código:            GRUPO-A               │
│  Abreviatura:       GA                    │
│  Nombre:            Grupo A               │
│  Descripción:       Grupo avanzado...     │
│  H.Teóricas:        4                     │
│  H.Prácticas:       2                     │
│  Observaciones:     Se reúne los martes   │
│  Dictada en         SI                    │
│  institución:                             │
│                                            │
└────────────────────────────────────────────┘
```

---

## 6. Component Tree / Estado por Componente

### 6.1. Árbol de Componentes (Vista Index)

```
└── layouts/dashboard.app (Master Layout)
    └── administracion.configuraciones.grupo_estables.index
        ├── elements.forms.errors (Errores de validación)
        ├── elements.messeges.oper_ok (Flash messages)
        ├── menus.index
        │   ├── elements.buttons.default (Crear nuevo)
        │   ├── elements.buttons.default (Ir atrás)
        │   └── elements.buttons.default (Refrescar)
        └── table.index
            ├── DataTable (#table-data-default)
            │   └── Por fila (grupo_estable):
            │       ├── Botón Show → elements.widgets.modal
            │       │   └── partials.details
            │       ├── Botón Edit → route('...edit', $id)
            │       └── Botón Destroy → JS SweetAlert2 → POST AJAX
            ├── form-destroy (Form oculto para DELETE)
            └── datatables.default (Script DataTables)
```

### 6.2. Estados por Componente

**Index — DataTable:**
| Estado | Condición | Comportamiento |
|--------|-----------|----------------|
| **Cargando** | Primera carga | DataTable muestra "Processing..." |
| **Con datos** | `$grupo_estables->count() > 0` | Tabla con filas, botones de acción |
| **Vacío** | Sin grupos registrados | DataTable muestra "No data available" |
| **Error** | Excepción en query | Error 500 (sin manejo explícito) |
| **Delete exitoso** | AJAX success | Fila fadeOut, SweetAlert "success" |
| **Delete fallido** | AJAX fail | Modal `#admin_oper_nook` con errores |

**Create/Edit — Formulario:**
| Estado | Condición | Comportamiento |
|--------|-----------|----------------|
| **Renderizado** | GET create/edit | Formulario vacío/poblado |
| **Validación fallida** | Errores FormRequest | Redirect back con `$errors` |
| **Guardado exitoso** | Store/update OK | Flash `operp_ok`, redirect a create/edit |
| **Error BD** | Query exception | Error 500 (sin manejo explícito) |
| **Edit: registro no existe** | ID inválido | `findOrFail` → 404 |

**Acción Delete:**
| Estado | Condición | Comportamiento |
|--------|-----------|----------------|
| **Renderizado** | `status_delete = true` | Botón 🗑️ activo |
| **Deshabilitado** | `status_delete = false` | Clase `disabled` en botón |
| **Confirmación** | Click en 🗑️ | SweetAlert2: "¿Estás seguro?" |
| **Cancelado** | Usuario cancela | No acción |
| **Ejecutado** | Confirmación + AJAX success | Fila fadeOut, mensaje success |
| **Error servidor** | AJAX fail | Modal de error |

---

## 7. Plan de Migración (Laravel → NextJS + API)

### Fase 1: API REST (Backend Laravel)

| Tarea | Esfuerzo | Dependencias |
|-------|----------|--------------|
| Crear `GrupoEstableController` API (Resource controller) | Bajo | — |
| Crear `GrupoEstableResource` (API Resource) | Bajo | Controller |
| Crear `StoreGrupoEstableRequest` / `UpdateGrupoEstableRequest` (reutilizar reglas) | Bajo | Requests actuales |
| Crear endpoint `/api/v1/grupo-estables/activos` | Bajo | Modelo |
| Crear endpoint `/api/v1/grupo-estables/con-inscripciones` | Bajo | Modelo |
| Crear endpoint `/api/v1/grupo-estables/:id/nota` | Medio | Método `getNota()` |
| Crear endpoint `/api/v1/grupo-estables/select` (para dropdowns masivos) | Bajo | Métodos `list_*()` |
| Middleware: `auth:api` + `scope:control` | Bajo | Config Passport |

### Fase 2: Frontend NextJS

| Tarea | Esfuerzo | Dependencias |
|-------|----------|--------------|
| CRUD Pages: `pages/grupo-estables/index.tsx` | Medio | API Fase 1 |
| CRUD Pages: `pages/grupo-estables/create.tsx` | Medio | API |
| CRUD Pages: `pages/grupo-estables/[id]/edit.tsx` | Medio | API |
| CRUD Pages: `pages/grupo-estables/[id]/show.tsx` | Bajo | API |
| Componente `GrupoEstableForm` (reutilizable) | Medio | Formulario |
| Componente `GrupoEstableDetails` (modal) | Bajo | Show API |
| Componente `GrupoEstableSelect` (dropdown providers) | Bajo | Endpoint select |
| DataTable reutilizable (columnas configurables) | Alto | — |
| Manejador de eliminación con SweetAlert2/notificación | Bajo | — |
| Traducciones i18n: `grupoEstables.*` | Bajo | — |

### Fase 3: Limpieza Legacy

| Tarea | Esfuerzo | Riesgo |
|-------|----------|--------|
| Eliminar vistas Blade del módulo | Bajo | Ninguno |
| Eliminar `routes/app/tab/grupo_estables.php` | Bajo | Verificar que no haya includes faltantes |
| Eliminar `GrupoEstableController.php` | Bajo | Verificar ProfesorGestableController |
| Archivos huérfanos: `crud.blade.php`, `table/crud.blade.php`, `menus/show.blade.php`, `partials/resumen/edit.blade.php`, `partials/resumen/create.blade.php`, `public/js/models/grupo_estables/destroy.js` | Bajo | Confirmar que no hay includes |
| Migrar `pestudio_id` pendiente: decidir si crear columna o eliminar relación huérfana | Medio | Requiere análisis de datos existentes |
| Agregar migración faltante para `size_max` y `status_belongs_ins` | Bajo | Migración histórica |

### Fase 4: Reemplazar llamadas a métodos estáticos

| Método Actual | Reemplazo API |
|---------------|---------------|
| `GrupoEstable::list_grupo_estable_full()` | `GET /api/v1/grupo-estables/select?format=full` |
| `GrupoEstable::list_grupo_estable_active()` | `GET /api/v1/grupo-estables/activos` |
| `GrupoEstable::list_grupo_estable_full_inscriptions()` | `GET /api/v1/grupo-estables/con-inscripciones` |
| `GrupoEstable::list_grupo_estable_code()` | `GET /api/v1/grupo-estables/select?format=code` |
| `GrupoEstable::list_grupo_estable()` | `GET /api/v1/grupo-estables/select?format=name` |
| `$grupoEstable->getNota(...)` | `GET /api/v1/grupo-estables/:id/nota?estudiant_id=&seccion_id=&lapso_id=` |
| `$grupoEstable->status_delete` | Incluir en `GrupoEstableResource` como `can_delete` |

---

## 8. Edge Cases y Validación

### 8.1. Validación Actual (FormRequest)

| Campo | Regla | Nota |
|-------|-------|------|
| `code` | `required`, `max:10`, `unique:grupo_estables` | Validado |
| `code_sm` | `required`, `max:4` | Validado |
| `name` | `required` | Validado |
| `hour_t_week` | **Sin validación** (solo select 0-10 en frontend) | ⚠️ Sin validación server-side |
| `hour_p_week` | **Sin validación** (solo select 0-10 en frontend) | ⚠️ Sin validación server-side |
| `size_max` | **Sin validación** (ni siquiera hay campo en el formulario) | ⚠️ Sin UI ni validación |
| `status_active` | **Sin validación** (solo select en frontend) | ⚠️ Debería ser `in:true,false` |
| `status_belongs_ins` | **Sin validación** (solo select en frontend) | ⚠️ Debería ser `in:true,false` |

### 8.2. Edge Cases Detectados

| # | Escenario | Comportamiento Actual | Comportamiento Esperado |
|---|-----------|----------------------|------------------------|
| 1 | Eliminar grupo con Hnotas | Botón deshabilitado, no se permite | Debe mostrar mensaje explicativo |
| 2 | Código duplicado | FormRequest `unique` lo rechaza con mensaje "El código ya existe" | ✅ Correcto |
| 3 | Grupo inactivo con inscripciones | Se permite inactivar pero `list_*_full_inscriptions()` lo excluye automáticamente | Comportamiento esperado |
| 4 | `size_max` sin formulario | Campo existe en DB y fillable pero no hay UI para establecerlo | Debe agregarse al formulario o eliminarse |
| 5 | `pestudio_id` relación huérfana | Relación definida en modelo pero columna no existe en DB | Decidir: crear columna o eliminar relación |
| 6 | Código con caracteres especiales | Sin validación de formato (solo length) | Debería validarse: alfanumérico + guiones |
| 7 | Eliminación física sin SoftDeletes | `delete()` físico, irreversible | Riesgo de pérdida de datos históricos |
| 8 | Create redirige a create (no a index) | `store()` redirige a `route('...create')` — patrón inusual | Podría redirigir a index tras crear |
| 9 | `status_belongs_ins` sin migración | No hay registro de cuándo/cómo se agregó a la tabla | Documentar como columna legacy |
| 10 | DataTable sin paginación server-side | Toda la colección se envía al cliente, DataTables.js hace paginación cliente | Riesgo de rendimiento con muchos registros |

### 8.3. Checklist de Validaciones Adicionales (Migración API)

- [ ] `POST /PUT`: Validar `code` formato (`alpha_dash` o `regex:/^[A-Za-z0-9-]+$/`)
- [ ] `POST /PUT`: Validar `hour_t_week` como `integer|min:0|max:10`
- [ ] `POST /PUT`: Validar `hour_p_week` como `integer|min:0|max:10`
- [ ] `POST /PUT`: Validar `size_max` como `integer|min:1|max:99`
- [ ] `POST /PUT`: Validar `status_active` como `in:true,false`
- [ ] `POST /PUT`: Validar `status_belongs_ins` como `in:true,false`
- [ ] `DELETE`: Verificar `status_delete` antes de permitir eliminación
- [ ] `GET /:id`: Retornar 404 si no existe
- [ ] `GET /nota`: Validar parámetros requeridos (`estudiant_id`, `seccion_id`, `lapso_id`)
- [ ] `GET /nota`: Validar que `estudiant_id` exista, `seccion_id` exista, `lapso_id` exista

---

## 9. Dependencias e Integraciones

| Componente/Sistema | Tipo | Naturaleza |
|-------------------|------|------------|
| Inscripcion (modelo) | Consumidor | `grupo_estable_id` FK |
| ProfesorGestable | Consumidor | Asignación profesor → grupo |
| Pevaluacion | Consumidor | Evaluaciones por grupo |
| Hnota (modelo) | Consumidor | Notas históricas vinculadas a grupo |
| Census | Consumidor | Censo estudiantil por grupo |
| Enrollment | Consumidor | Enrollment vinculado a grupo |
| Preinscripcion | Consumidor | Pre-inscripciones con grupo |
| Pestudio (modelo) | Relación inversa | `hasMany` desde Pestudio (relación huérfana) |
| Estudiant (modelo) | Consumidor (accessor) | `$estudiant->grupo_estable` vía inscripción |
| Asignatura (modelo) | Flag | `enable_grupo_estable` determina si usa grupos |
| EvaluacionGestable | Indirecto | Pivote vía ProfesorGestable |
| Listados Livewire (30+ componentes) | Consumidor | Usan `GrupoEstable::list_*()` para selects |
| FillPartialController (AJAX) | Consumidor | `inscripcionsGrupoEstableUpdate()` |

---

## 10. Comparación con Módulos Anteriores

| Característica | Asignaturas | Pestudios | Grados | Secciones | Lapsos | Baremos | **GrupoEstables** |
|----------------|-------------|-----------|--------|-----------|--------|---------|-------------------|
| Livewire CRUD | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | **❌** |
| FormRequest Validation | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ (inline) | **✅** |
| SoftDeletes | Comentado | ✅ | ✅ | ❌ (físico) | ✅ | ✅ | **❌ (comentado)** |
| Modal CRUD | ❌ | ❌ | ✅ | ✅ | ✅ | ❌ | **❌** |
| Admin Sidebar Routes | ❌ | ✅ | ✅ (legacy) | ✅ (is_common) | ❌ | ❌ | **❌** |
| Archivos huérfanos en views | Mínimo | Mínimo | Stale peducativos | 1 (edit=BANCO) | 1 (edit=BANCO) | — | **5+** |
| `status_delete` accessor | ❌ | ❌ | ✅ | ✅ | ✅ | ❌ | **✅** |
| `COLUMN_COMMENTS` | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | **✅** |
| Métodos `list_*()` estáticos | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | **✅ (5 métodos)** |
| Uso externo (>10 callers) | Bajo | Alto | Alto | **Muy alto** | **Máximo** | Bajo | **Alto** |
| Migraciones perdidas | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **✅ (size_max, status_belongs_ins)** |
| Relaciones huérfanas | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **✅ (pestudio_id)** |

---

## 11. Resumen de Hallazgos

### Críticos
1. **`pestudio_id` relación huérfana:** El modelo define `belongsTo(Pestudio)` pero la columna no existe en la tabla. Decidir si agregar la columna o eliminar el método de relación.
2. **`size_max` y `status_belongs_ins` sin migraciones:** Existen en BD y son fillable, pero no hay registro de migración.
3. **Eliminación física sin SoftDeletes:** Comentado en el modelo. Cualquier `delete()` es irreversible, lo que puede causar pérdida de referencias en `inscripcions`, `hnotas`, `profesor_gestables`.
4. **`menus/show.blade.php` referencia users.create:** Stale, fue copiado del módulo Users sin modificar.
5. **`partials/resumen/edit.blade.php` referencia `$asignatura->pensums`:** Copiado completo del módulo Asignatura, no relacionado a GrupoEstable.

### Moderados
6. **`size_max` sin UI:** Campo en fillable y BD pero sin control en el formulario. Funcionalidad incompleta.
7. **Módulo con más archivos huérfanos (5+)**: `crud.blade.php`, `table/crud.blade.php`, `menus/show.blade.php`, `partials/resumen/create.blade.php`, `partials/resumen/edit.blade.php`, `public/js/models/grupo_estables/destroy.js`.
8. **Falta validación server-side para campos enum:** `status_active` y `status_belongs_ins` no tienen reglas `in:` en FormRequest.
9. **Sin ruta admin sidebar:** A diferencia de Pestudios, Grados y Secciones, no hay un segundo set de rutas en `routes/administracion/tab/`.

### Bajos
10. **Redirección post-store a create (no a index):** Inconsistente con la mayoría de CRUDs que redirigen al index tras crear.
11. **Store no usa transacción:** `GrupoEstable::create()` sin `DB::transaction()`.
12. **`getNota()` sin manejo de división por cero:** Si hay boletins no-null pero sin notas, la división `$boletins->sum('nota')/$boletins->count()` podría ser 0/0 si count=0 (aunque hay check `isNotEmpty`).
13. **JavaScript huérfano específico del módulo:** `public/js/models/grupo_estables/destroy.js` existe pero la vista carga `default/destroy.js`.

---

*Documento generado: 2026-06-06. Validado contra código fuente.*
