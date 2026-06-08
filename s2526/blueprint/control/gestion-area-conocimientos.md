# Blueprint: Gestión de Áreas de Conocimiento (AreaConocimiento)

> **Módulo:** Control de Estudios → Configuraciones → Áreas de Conocimiento  
> **Ruta base:** `/app/control/configuraciones/area_conocimientos`  
> **Middleware:** `is_control` (definido en constructor del controlador)  
> **Propósito:** Administrar áreas de conocimiento académico, asignar materias (asignaturas) a cada área, y designar jefes de área (leader). Cada área agrupa asignaturas afines dentro de un plan de estudio, permitiendo generar reportes de rendimiento y gráficos estadísticos por área.

---

## 1. Validación Contra Código Fuente

### 1.1. Rutas — Módulo Principal

**Archivo:** `routes/app/tab/area_conocimientos.php` (cargado por `routes/app/control.php` línea 104)

| Método | URI | Acción | Nombre de Ruta |
|--------|-----|--------|----------------|
| GET | `/configuraciones/area_conocimientos` | `AreaConocimientoController@index` | `administracion.configuraciones.area_conocimientos` |
| GET | `/configuraciones/area_conocimientos/index` | `AreaConocimientoController@index` | `administracion.configuraciones.area_conocimientos.index` |
| GET | `/configuraciones/area_conocimientos/create` | `AreaConocimientoController@create` | `administracion.configuraciones.area_conocimientos.create` |
| POST | `/configuraciones/area_conocimientos/store` | `AreaConocimientoController@store` | `administracion.configuraciones.area_conocimientos.store` |
| GET | `/configuraciones/area_conocimientos/{id}` | `AreaConocimientoController@edit` | `administracion.configuraciones.area_conocimientos.edit` |
| PUT | `/configuraciones/area_conocimientos/{id}` | `AreaConocimientoController@update` | `administracion.configuraciones.area_conocimientos.update` |
| DELETE | `/configuraciones/area_conocimientos/destroy/{id}` | `AreaConocimientoController@destroy` | `administracion.configuraciones.area_conocimientos.destroy` |

**Nota:** Dos rutas GET para index (con y sin `/index`), ambas apuntan al mismo método.

### 1.2. Rutas — Sub-módulo CampoConocimiento (Asignatura ↔ Área)

**Archivo:** `routes/app/tab/campo_conocimientos.php` (cargado por `routes/app/control.php`)

| Método | URI | Acción | Nombre de Ruta |
|--------|-----|--------|----------------|
| GET | `/configuraciones/campo_conocimientos` | `CampoConocimientoController@index` | `administracion.configuraciones.campo_conocimientos` |
| GET | `/configuraciones/campo_conocimientos/index` | `CampoConocimientoController@index` | `administracion.configuraciones.campo_conocimientos.index` |
| GET | `/configuraciones/campo_conocimientos/create` | `CampoConocimientoController@create` | `administracion.configuraciones.campo_conocimientos.create` |
| POST | `/configuraciones/campo_conocimientos/store` | `CampoConocimientoController@store` | `administracion.configuraciones.campo_conocimientos.store` |
| GET | `/configuraciones/campo_conocimientos/{id}` | `CampoConocimientoController@edit` | `administracion.configuraciones.campo_conocimientos.edit` |
| PUT | `/configuraciones/campo_conocimientos/{id}` | `CampoConocimientoController@update` | `administracion.configuraciones.campo_conocimientos.update` |
| DELETE | `/configuraciones/campo_conocimientos/destroy/{id}` | `CampoConocimientoController@destroy` | `administracion.configuraciones.campo_conocimientos.destroy` |

### 1.3. Rutas — Chart (Estadísticas)

**Archivo:** `routes/app/charts/area_conocimientos.php`

| Método | URI | Acción | Nombre de Ruta |
|--------|-----|--------|----------------|
| GET | `charts/area_conocimientos/promedio_x_area` | `Chart\AreaConocimientoController@promedio_x_area` | `administracion.area_conocimientos.promedio_x_area.chart` |

**4 controladores delegados** reutilizan el mismo chart (Director, Academico, Controls, Bienestar), cada uno con su propio namespace y ruta:
- `routes/app/charts/directors/main.php` → `Director\Chart\AreaConocimientoController`
- `routes/app/charts/academicos/main.php` → `Academico\Chart\AreaConocimientoController`
- `routes/app/charts/controls/main.php` → `Controls\Chart\AreaConocimientoController`
- `routes/app/charts/bienestars/main.php` → `Bienestar\Chart\AreaConocimientoController`
- `routes/app/charts/representants/main.php` → **COMENTADO** (deshabilitado para representantes)

---

### 1.4. Controlador Principal

**Archivo:** `app/Http/Controllers/Administracion/Tab/Configuracion/AreaConocimientoController.php`

**6 métodos — CRUD tradicional (sin Livewire):**

| Método | Lógica | Vista |
|--------|--------|-------|
| `__construct()` | `$this->middleware(['auth','is_control'])` | — |
| `index()` | `AreaConocimiento::all()->sortByDesc('id')`, más `CampoConocimiento::all()`, 7 listas de selección (peducativo, pestudio, area, asignatura, user), COLUMN_COMMENTS | `...area_conocimientos.index` (con tabs) |
| `create()` | `COLUMN_COMMENTS` + `user_list` + `list_grado` | `...area_conocimientos.create` |
| `store(Request)` | `AreaConocimiento::create($request->all())` + flash | redirect → `index` |
| `edit($id)` | `AreaConocimiento::findOrFail($id)` + 4 listas + COLUMN_COMMENTS | `...area_conocimientos.edit` |
| `update(Request, $id)` | `findOrFail` + `fill($request->all())` + `save()` + flash | redirect → `index` |
| `destroy($id, Request)` | Verifica `status_delete` → `delete()` + JSON si AJAX | redirect → `index` |

### 1.5. Controlador — CampoConocimiento

**Archivo:** `app/Http/Controllers/Administracion/Tab/Configuracion/CampoConocimientoController.php`

**3 métodos:**

| Método | Lógica |
|--------|--------|
| `store(Request)` | **Patrón "sync":** elimina todos los `CampoConocimiento` existentes para `area_conocimiento_id`, luego recrea desde `asignatura_id[]` array |
| `update(Request, $id)` | `findOrFail` + `fill()` + `save()` |
| `destroy($id, Request)` | `findOrFail` + `delete()` + JSON si AJAX |

### 1.6. Controlador — Chart

**Archivo:** `app/Http/Controllers/Administracion/Chart/AreaConocimientoController.php`

- `promedio_x_area(Request)`: Acepta `pestudio_id` (filtro) y `range` (lapso_id). Calcula promedios por área mediante `AreaConocimiento::getPromedio()`. Retorna JSON compatible con Chart.js (`labels`, `datasets[].backgroundColor`, `borderColor`, `data`).

### 1.7. Form Requests (DESHABILITADOS)

| Archivo | `authorize()` | Reglas |
|---------|---------------|--------|
| `CreateAreaConocimientoRequest.php` | **`return false;`** | Vacío `[]` |
| `UpdateAreaConocimientoRequest.php` | **`return false;`** | Vacío `[]` |

**Los controladores usan `Illuminate\Http\Request` directamente** — NO hay validación server-side.

---

### 1.8. Modelo — AreaConocimiento

**Archivo:** `app/Models/app/Pescolar/AreaConocimiento.php` (319 líneas)

| Propiedad | Valor |
|-----------|-------|
| **Tabla** | `area_conocimientos` |
| **SoftDeletes** | ❌ Comentado (`// use SoftDeletes;`) — columna `deleted_at` existe en BD |
| **Campos fillable** | `peducativo_id`, `pestudio_id`, `leader_id`, `name`, `code`, `code_sm`, `description`, `observations`, `order`, `enable_academic_index` |
| **COLUMN_COMMENTS** | 10 entradas con etiquetas en español |

**Relaciones:**
| Método | Tipo | Target |
|--------|------|--------|
| `peducativo()` | `belongsTo` | Peducativo |
| `pestudio()` | `belongsTo` | Pestudio |
| `campo_conocimientos()` | `hasMany` | CampoConocimiento |
| `leader()` | `belongsTo` | User (`leader_id`) |

**Métodos clave:**
| Método | Descripción |
|--------|-------------|
| `getProfesorsIEEsPROM($lapso_id)` | Promedio de evaluaciones por profesor en el área |
| `estudiants($lapso_id)` | Estudiantes inscritos (join complejo: inscripcions → seccions → grados → pensums → asignaturas → campo_conocimientos → area_conocimientos, más administrativas → planpagos) |
| `inscritos($lapso_id)` | Conteo de estudiantes (mismos joins) |
| `getPevaluacions($lapso_id)` | Evaluaciones planificadas en el área |
| `getEvaluacions($lapso_id)` | Evaluaciones con notas |
| `getProfesorEvaluacions($lapso_id)` | Profesores asignados al área |
| `getBoletins($lapso_id)` | Boletines/notas del área |
| `getFullNameAttribute()` | Accessor: `"{name} [{pestudio->code}]"` |
| `getCheckIn($asignatura_id)` | Verifica si una asignatura pertenece al área |
| `getStatusDeleteAttribute()` | Retorna `true` si NO hay `campo_conocimientos` asociados |
| `getPromedio($lapso_id)` | Promedio de notas del área (sum_nota / count) |
| `getGradosAttribute()` | Grados vinculados vía `pestudio_id` |
| `getEvaluacionsForLeader($leader_id, $lapso_id)` | Método **estático** — evaluaciones filtradas por leader |

### 1.9. Modelo — CampoConocimiento

**Archivo:** `app/Models/app/Pescolar/CampoConocimiento.php` (34 líneas)

| Propiedad | Valor |
|-----------|-------|
| **Tabla** | `campo_conocimientos` |
| **SoftDeletes** | ❌ Comentado — columna `deleted_at` NO existe en la tabla |
| **Campos fillable** | `area_conocimiento_id`, `asignatura_id`, `observations` |
| **COLUMN_COMMENTS** | 3 entradas |
| **Relaciones** | `area_conocimiento()`, `asignatura()`, **y duplicada** `areaConocimiento()` (class-based) |

### 1.10. Migraciones (4 archivos)

| Archivo | Propósito |
|---------|-----------|
| `backUps/old/temp/2020_02_28_160157_create_area_conocimientos_table.php` | Creación: `id` (smallIncrements), `pestudio_id` (FK→pestudios CASCADE), `name`, `code`, `code_sm`, `description`, `observations`, `order`, `enable_academic_index` (ENUM), `softDeletes`, timestamps |
| `backUps/old/temp/2020_02_28_160224_create_campo_conocimientos_table.php` | Creación: `id`, `area_conocimiento_id` (FK→area_conocimientos CASCADE), `asignatura_id` (FK→asignaturas CASCADE), `observations`, timestamps |
| `backUps/lesson/2024_02_26_204707_add_leader_id_to_area_conocimientos.php` | Agrega `leader_id` (unsignedInteger, nullable) después de `pestudio_id` |
| `backUps/lesson/2024_02_28_072509_add_peducativo_id_to_area_conocimientos.php` | Agrega `peducativo_id` (smallInteger, unsigned, nullable) después de `id` |

### 1.11. Vistas (27 archivos)

```
resources/views/administracion/configuraciones/area_conocimientos/
├── index.blade.php            ✅ Activo — Tabs: "Áreas" | "Asignaturas adscritas"
├── edit.blade.php             ❌ **Banco copy** — Referencia $banco, bancoupdate, banco.form.field
├── card.blade.php             ✅ Activo — Card individual
├── chart/
│   ├── promedio_x_area.blade.php       ✅ Chart.js horizontalBar con tabs de lapso
│   ├── inscritoxgenero.blade.php       ✅ Chart.js pie
│   └── inscritoxgeneroxgrado.blade.php ✅ Chart.js barra
├── form/
│   ├── fields.blade.php                ✅ 10 campos: peducativo, pestudio, leader (@admin), name, code, code_sm, description, observations, order (1-15), enable_academic_index
│   └── campo_conocimientos/
│       └── fields.blade.php            ✅ Árbol de checkboxes: peducativo → pestudio → grado → pensum → asignatura
├── menus/
│   ├── index.blade.php        ✅
│   ├── create.blade.php       ✅
│   ├── edit.blade.php         ✅
│   ├── crud.blade.php         ⚠️ Stale — referencia pensums
│   └── show.blade.php         ❌ **Users copy** — referencia users.create, profiles, roles
├── modals/
│   ├── details.blade.php      ✅ Modal show
│   ├── edit.blade.php         ✅ Modal edit (usa partials/edit)
│   ├── create_campo.blade.php ✅ Modal asignar asignatura (usa partials/create_campo)
│   ├── legenda.blade.php      ✅ Modal leyenda
│   └── campo_conocimientos/
│       └── edit.blade.php     ✅ Modal edit campo
├── partials/
│   ├── create.blade.php       ✅ Form open store
│   ├── edit.blade.php         ✅ Form model update
│   ├── details.blade.php      ✅ Read-only display
│   ├── create_campo.blade.php ✅ Form open campo store
│   ├── edit_campo.blade.php   ✅ Form model campo update
│   └── legenda/
│       ├── details.blade.php  ✅ Leyenda detallada: áreas por pestudio
│       └── main.blade.php     ✅ Wrapper
└── table/
    ├── index.blade.php        ✅ DataTable (#table-data-area, custom datatables config)
    └── campo.blade.php        ✅ DataTable campo_conocimientos
```

### 1.12. JavaScript

| Archivo | Estado | Uso |
|---------|--------|-----|
| `public/js/models/area_conocimientos/destroy.js` | ✅ Activo | SweetAlert2, reemplaza `:AREA_CONOCIMIENTO_ID` en form action, AJAX DELETE |

### 1.13. Controladores que usan AreaConocimiento (14+)

| Controlador | Uso |
|-------------|-----|
| `Director/HomeController` | `AreaConocimiento::all()` para dashboard |
| `Academico/HomeController` | `AreaConocimiento::all()` para dashboard |
| `Leader/HomeController` | `AreaConocimiento::where('leader_id',$user->id)->get()` |
| `Director/Tab/ControlController` | `AreaConocimiento::all()` para índice de rendimiento |
| `Academico/Tab/ControlController` | `AreaConocimiento::all()` |
| `Controls/Tab/ControlController` | `AreaConocimiento::all()` |
| `Leader/Tab/ProfesorController` | Filtrado por `leader_id` |
| `Leader/Tab/ActivityController` | Filtrado por `leader_id` |
| `Leader/Tab/LearningController` | Filtrado por `leader_id` |
| `Leader/Chart/EvaluacionController` | Joins con filtro `leader_id` |
| `Inicial/Tab/HomeInicialController` | Importa el modelo |

### 1.14. Livewire que usan AreaConocimiento (7+)

| Componente | Uso |
|------------|-----|
| `Leader/EvaluacionComponent` | Joins con `leader_id` |
| `Leader/LessonComponent` | `AreaConocimiento::where('leader_id',$user->id)->get()` |
| `Leader/ActivityComponent` | Filtrado por `leader_id` |
| `Leader/Competition/IndexComponent` | Joins con `leader_id` |
| `Planning/Pevaluacion/IndexComponent` | Importa el modelo |
| `Movile/Evaluacion/LessonComponent` | Joins con area_conocimientos y peducativos |
| `Profesor/SocialActions/CommunityAction/IndexComponent` | Propiedad comentada |

---

## 2. Reglas de Negocio

1. **Área agrupa asignaturas:** Un `AreaConocimiento` agrupa múltiples `Asignatura`s a través de la tabla pivote `campo_conocimientos`.
2. **Jerarquía:** `Peducativo → Pestudio → Grado → Pensum → Asignatura` — el árbol de checkboxes en el formulario de asignación respeta esta jerarquía.
3. **Jefe de Área (`leader_id`):** Un usuario (User) puede ser designado como jefe del área. Solo visible para rol `@admin`.
4. **Plan Educativo y Plan de Estudio:** Cada área pertenece a un `Peductivo` y un `Pestudio`. Ambos son obligatorios.
5. **Orden de presentación:** El campo `order` (1-15) define la posición del área en listados y reportes.
6. **Índice académico:** `enable_academic_index` (SI/NO) determina si el área se incluye en el cálculo del índice/promedio académico.
7. **Sincronización de asignaturas:** `CampoConocimiento::store()` elimina todas las asignaciones existentes y las recrea — operación "sync", no incremental.
8. **Eliminación segura:** Solo se permite eliminar un área si no tiene `campo_conocimientos` asignados.
9. **Gráfico de promedios:** El chart `promedio_x_area` calcula el promedio de notas por área, filtrado por `pestudio_id` y `lapso_id` (range). Usa el `rgb_color` del pestudio para estilizar.
10. **Visibilidad multi-rol:** El chart de promedios está disponible para Director, Academico, Controls, Bienestar — pero NO para Representantes (ruta comentada).

---

## 3. SQL Schema

### Tabla `area_conocimientos`

```sql
CREATE TABLE `area_conocimientos` (
    `id`                    smallint(5) unsigned NOT NULL AUTO_INCREMENT,
    `peducativo_id`         smallint(5) unsigned DEFAULT NULL COMMENT 'Plan Educativo',
    `pestudio_id`           int(10) unsigned NOT NULL COMMENT 'Plan Estudio',
    `leader_id`             int(10) unsigned DEFAULT NULL COMMENT 'Jefe del Área',
    `name`                  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nombre',
    `code`                  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Código',
    `code_sm`               varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Abreviatura',
    `description`           varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descripción',
    `observations`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observaciones',
    `order`                 int(11) NOT NULL COMMENT 'Número de orden de presentación',
    `enable_academic_index` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tomada en cuenta para índice o promedio académico',
    `deleted_at`            timestamp NULL DEFAULT NULL,
    `created_at`            timestamp NULL DEFAULT NULL,
    `updated_at`            timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `area_conocimientos_pestudio_id_foreign` (`pestudio_id`),
    CONSTRAINT `area_conocimientos_pestudio_id_foreign` FOREIGN KEY (`pestudio_id`) REFERENCES `pestudios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabla `campo_conocimientos` (Pivote)

```sql
CREATE TABLE `campo_conocimientos` (
    `id`                    int(10) unsigned NOT NULL AUTO_INCREMENT,
    `area_conocimiento_id`  smallint(5) unsigned NOT NULL COMMENT 'Área de Conocimiento',
    `asignatura_id`         int(10) unsigned NOT NULL COMMENT 'Asignatura',
    `observations`          varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observaciones',
    `created_at`            timestamp NULL DEFAULT NULL,
    `updated_at`            timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `campo_conocimientos_area_conocimiento_id_foreign` (`area_conocimiento_id`),
    KEY `campo_conocimientos_asignatura_id_foreign` (`asignatura_id`),
    CONSTRAINT `campo_conocimientos_area_conocimiento_id_foreign` FOREIGN KEY (`area_conocimiento_id`) REFERENCES `area_conocimientos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `campo_conocimientos_asignatura_id_foreign` FOREIGN KEY (`asignatura_id`) REFERENCES `asignaturas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 4. Endpoints API REST (Propuesta Migración)

### Colección: `/api/v1/areas-conocimiento`

| Método | Endpoint | Descripción | Controlador Actual |
|--------|----------|-------------|-------------------|
| `GET` | `/api/v1/areas-conocimiento` | Listar (paginado, sort by id DESC) | `index()` |
| `GET` | `/api/v1/areas-conocimiento/:id` | Obtener un área | `edit()` parcial |
| `POST` | `/api/v1/areas-conocimiento` | Crear área | `store()` |
| `PUT` | `/api/v1/areas-conocimiento/:id` | Actualizar área | `update()` |
| `DELETE` | `/api/v1/areas-conocimiento/:id` | Eliminar (verificar `status_delete`) | `destroy()` |

### Colección: `/api/v1/areas-conocimiento/:id/campos-conocimiento`

| Método | Endpoint | Descripción | Controlador Actual |
|--------|----------|-------------|-------------------|
| `GET` | `/api/v1/areas-conocimiento/:id/campos-conocimiento` | Listar asignaturas del área | `CampoConocimiento@index` |
| `POST` | `/api/v1/areas-conocimiento/:id/campos-conocimiento` | Sincronizar asignaturas (reemplaza todas) | `CampoConocimiento@store` |
| `DELETE` | `/api/v1/campos-conocimiento/:id` | Eliminar asignación individual | `CampoConocimiento@destroy` |

### Endpoints de Chart/Estadísticas

| Método | Endpoint | Descripción |
|--------|----------|-------------|
| `GET` | `/api/v1/areas-conocimiento/promedio-x-area?pestudio_id=&range=` | Promedio por área (Chart.js JSON) |
| `GET` | `/api/v1/areas-conocimiento/:id/promedio?lapso_id=` | Promedio de un área específica |
| `GET` | `/api/v1/areas-conocimiento/:id/inscritos?lapso_id=` | Conteo de inscritos |
| `GET` | `/api/v1/areas-conocimiento/:id/evaluaciones?lapso_id=` | Evaluaciones del área |
| `GET` | `/api/v1/areas-conocimiento/:id/profesores?lapso_id=` | Profesores asignados |
| `GET` | `/api/v1/areas-conocimiento/:id/boletines?lapso_id=` | Boletines/notas |

### Endpoints auxiliares

| Método | Endpoint | Propósito |
|--------|----------|-----------|
| `GET` | `/api/v1/areas-conocimiento/select` | Dropdown: `[id => "name [code]"]` |
| `GET` | `/api/v1/areas-conocimiento/por-lider/:leader_id` | Áreas filtradas por jefe de área |

### Request Body (POST/PUT):

```json
{
    "peducativo_id": 1,
    "pestudio_id": 3,
    "leader_id": 5,
    "name": "Matemática y Ciencias Naturales",
    "code": "MAT-CN",
    "code_sm": "MAT",
    "description": "Área que agrupa las asignaturas de matemática y ciencias",
    "observations": "Responsable: coordinador de matemática",
    "order": 1,
    "enable_academic_index": "true"
}
```

### Sincronización de asignaturas (POST campos-conocimiento):

```json
{
    "asignatura_ids": [1, 2, 3, 5, 8, 13]
}
```

---

## 5. UI Wireframes

### 5.1. Pantalla de Listado (Index con Tabs)

```
┌─────────────────────────────────────────────────────────────┐
│  [Administración > Configuraciones]                         │
├─────────────────────────────────────────────────────────────┤
│  Listado de las Áreas de Conocimiento   [+ Nuevo] [← Atrás] │
│                                               [⟳ Refrescar] │
├─────────────────────────────────────────────────────────────┤
│  [Áreas de Conocimiento] [Asignaturas adscritas]            │
├─────────────────────────────────────────────────────────────┤
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ N│Plan Estudio│Nombre(C)  │Cód│Descripción│I.Ac│N.Asig│ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │1 │2024-2025   │Matemática │MAT│...        │ SI │ 6    │ │
│ │  │            │[leader]   │   │           │    │      │ │
│ │  │            │           │   │           │    │[📋][👁][✏️][🗑️]│
│ ├─────────────────────────────────────────────────────────┤ │
│ │2 │2024-2025   │Lenguaje   │LEN│...        │ SI │ 4    │ │
│ │  │            │[leader]   │   │           │    │[📋][👁][✏️][🗑️]│
│ └─────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

**Acciones por fila:**
- 📋 Asignar asignaturas (abre modal `create_campo`)
- 👁️ Show (abre modal `details`)
- ✏️ Edit (abre modal `edit`)
- 🗑️ Delete (SweetAlert2, deshabilitado si tiene campo_conocimientos)

### 5.2. Modal — Asignar Asignaturas (create_campo)

```
┌── Agregar asignatura al Área de Conocimiento ── [X] ─┐
│                                                        │
│  Área: Matemática y Ciencias Naturales [MAT]          │
│                                                        │
│  ┌─ Plan Educativo A ──────────────────────────────┐  │
│  │ ┌─ [2024-2025] ──────────────────────────────┐  │  │
│  │ │ ┌── 1er Año ───────────────────────────┐   │  │  │
│  │ │ │ ☑ Matemática (1hr)                    │   │  │  │
│  │ │ │ ☑ Física (1hr)                        │   │  │  │
│  │ │ │ ☐ Química (2hr)                       │   │  │  │
│  │ │ └──────────────────────────────────────┘   │  │  │
│  │ │ ┌── 2do Año ───────────────────────────┐   │  │  │
│  │ │ │ ☑ Matemática (2hr)                    │   │  │  │
│  │ │ │ ☐ Geología (1hr)                      │   │  │  │
│  │ │ └──────────────────────────────────────┘   │  │  │
│  │ └────────────────────────────────────────────┘  │  │
│  └─────────────────────────────────────────────────┘  │
│                                                        │
│  [💾 Registrar]                                        │
└────────────────────────────────────────────────────────┘
```

### 5.3. Modal — Editar Área (edit)

```
┌── Actualizar Área de Conocimiento ── [X] ─┐
│                                            │
│  Plan Educativo:   [Plan A ▼]             │
│  Plan Estudio:     [2024-2025 ▼]          │
│  Jefe del Área:    [Juan Pérez ▼]         │
│  Nombre:           [Matemática...   ]     │
│  Código:           [MAT             ]     │
│  Abreviatura:      [MAT             ]     │
│  Descripción:      [______________   ]    │
│  Observaciones:    [______________   ]    │
│  Orden:            [1 ▼]                 │
│  I. Académico:     [SI ▼]               │
│                                            │
│  [💾 Actualizar]                           │
└────────────────────────────────────────────┘
```

---

## 6. Component Tree / Estados por Componente

### 6.1. Árbol de Componentes (Index)

```
└── layouts/dashboard.app (Master Layout)
    └── administracion.configuraciones.area_conocimientos.index
        ├── elements.forms.errors
        ├── elements.messeges.oper_ok
        ├── menus.index
        └── nav-tabs
            ├── Tab "Áreas de Conocimiento" (active)
            │   └── table.index
            │       ├── DataTable (#table-data-area, custom config)
            │       │   └── Por fila:
            │       │       ├── Botón Asignar → modals.create_campo
            │       │       │   └── partials.create_campo → form.campo_conocimientos.fields
            │       │       ├── Botón Show → modals.details
            │       │       │   └── partials.details
            │       │       ├── Botón Edit → modals.edit
            │       │       │   └── partials.edit → form.fields
            │       │       └── Botón Destroy → JS destroy
            │       └── form-destroy-area (form oculto)
            └── Tab "Asignaturas adscritas"
                └── table.campo
```

### 6.2. Estados por Componente

**Index — DataTable (#table-data-area):**
| Estado | Condición | Comportamiento |
|--------|-----------|----------------|
| **Cargando** | Primera carga | DataTable muestra "Processing..." |
| **Con datos** | `$area_conocimientos->count() > 0` | Tabla con filas + acciones |
| **Vacío** | Sin registros | DataTable muestra "No data available" |
| **Error** | Excepción | Error 500 (sin manejo explícito) |
| **Delete exitoso** | AJAX success | Fila fadeOut, SweetAlert success |
| **Delete fallido (status_delete=false)** | Tiene campo_conocimientos | Botón deshabilitado, no ejecuta |
| **Delete fallido AJAX** | Error servidor | Modal `#admin_oper_nook` |

**Modal CampoConocimiento (Árbol de checkboxes):**
| Estado | Condición |
|--------|-----------|
| **Cargando** | Modal abierto, render desde servidor |
| **Asignatura ya asignada** | `$area_conocimiento->getCheckIn($asignatura->id)` → checkbox checked |
| **Sin asignaturas** | No hay pensums para el pestudio del área |
| **Guardado** | Sync: borra todas + recrea desde `asignatura_id[]` |

---

## 7. Plan de Migración (Laravel → NextJS + API)

### Fase 1: API REST (Backend)

| Tarea | Esfuerzo | Dependencias |
|-------|----------|--------------|
| Crear `AreaConocimientoController` API (Resource) | Bajo | — |
| Crear `CampoConocimientoController` API (sync/nested) | Medio | AreaConocimiento API |
| Crear `AreaConocimientoResource`, `CampoConocimientoResource` | Bajo | Controllers |
| Crear `StoreAreaConocimientoRequest` API (con validación real) | Medio | Reglas de negocio |
| Endpoints de estadísticas: promedio, inscritos, evaluaciones, profesores, boletines | Alto | Modelo (muchos joins complejos) |
| Endpoint chart `promedio_x_area` | Medio | Controller chart actual |
| Endpoint `sincronizar-asignaturas` (POST reemplaza todas) | Medio | Patrón sync de CampoConocimiento |
| Endpoint `areas-por-lider/:leader_id` | Bajo | Modelo |
| Middleware: `auth:api` + scopes | Bajo | Passport config |

### Fase 2: Frontend NextJS

| Tarea | Esfuerzo | Dependencias |
|-------|----------|--------------|
| `pages/areas-conocimiento/index.tsx` (tabs: áreas + asignaturas) | Alto | API |
| `pages/areas-conocimiento/[id]/edit.tsx` | Medio | API |
| Componente `AreaConocimientoForm` (reutilizable) | Medio | — |
| Componente `CampoConocimientoTree` (árbol checkboxes jerárquico) | **Alto** | API asignaturas |
| Componente `AreaConocimientoChart` (Chart.js promedio_x_area) | Medio | API chart |
| Modal de detalles | Bajo | API |
| DataTable reutilizable | Alto | — |
| Manejador de eliminación con confirmación | Bajo | — |
| Traducciones i18n | Bajo | — |

### Fase 3: Limpieza Legacy

| Tarea | Esfuerzo | Riesgo |
|-------|----------|--------|
| Eliminar vistas Blade (27 archivos) | Medio | Verificar includes |
| Eliminar `routes/app/tab/area_conocimientos.php` y `campo_conocimientos.php` | Bajo | Confirmar que no hay includes faltantes |
| Eliminar chart routes (5 archivos) | Bajo | Solo si API reemplaza |
| Archivos huérfanos: `edit.blade.php` (banco copy), `menus/show.blade.php` (users copy), `menus/crud.blade.php` | Bajo | Confirmar no usados |
| Eliminar `AreaConocimientoController`, `CampoConocimientoController` | Bajo | — |
| Eliminar 4 Chart delegator controllers | Bajo | — |
| Eliminar FormRequests deshabilitados | Bajo | — |
| Corregir relación duplicada `areaConocimiento()` en `CampoConocimiento` | Bajo | Mantener una sola |

### Fase 4: Reemplazar llamadas

| Uso Actual | Reemplazo API |
|------------|---------------|
| `AreaConocimiento::all()` en dashboards | `GET /api/v1/areas-conocimiento` |
| `AreaConocimiento::where('leader_id',$id)->get()` | `GET /api/v1/areas-conocimiento/por-lider/:id` |
| `$area->getPromedio($lapso_id)` | `GET /api/v1/areas-conocimiento/:id/promedio?lapso_id=` |
| `$area->estudiants($lapso_id)` | `GET /api/v1/areas-conocimiento/:id/inscritos?lapso_id=` |
| `$area->getPevaluacions($lapso_id)` | `GET /api/v1/areas-conocimiento/:id/evaluaciones?lapso_id=` |
| `$area->getStatusDeleteAttribute()` | Incluir como `can_delete` en Resource |
| `$area->getFullNameAttribute()` | Incluir como `full_name` en Resource |
| `$area->getCheckIn($asignatura_id)` | `GET /api/v1/areas-conocimiento/:id/campos-conocimiento?asignatura_id=` |

---

## 8. Edge Cases y Validación

### 8.1. Validación Actual

| Campo | Regla Actual | Estado |
|-------|-------------|--------|
| `peducativo_id` | **Sin validación** | ⚠️ No validado |
| `pestudio_id` | **Sin validación** | ⚠️ No validado (FK en BD, pero sin validación server-side) |
| `leader_id` | **Sin validación** | ⚠️ No validado |
| `name` | **Sin validación** | ⚠️ No validado |
| `code` | **Sin validación** | ⚠️ No validado |
| `code_sm` | **Sin validación** | ⚠️ No validado |
| `order` | **Sin validación** | ⚠️ Solo select 1-15 en frontend |
| `enable_academic_index` | **Sin validación** | ⚠️ Solo select en frontend |

**Causa raíz:** Los FormRequests existen pero tienen `authorize() = false`, lo que los deshabilita completamente. El controlador usa `Illuminate\Http\Request` directamente. **No hay validación server-side en ningún campo.**

### 8.2. Edge Cases Detectados

| # | Escenario | Comportamiento Actual | Comportamiento Esperado |
|---|-----------|----------------------|------------------------|
| 1 | Eliminar área con campo_conocimientos | `status_delete=false`, botón deshabilitado | Debe mostrar mensaje: "Elimine primero las asignaturas asociadas" |
| 2 | `pestudio_id` inválido (inexistente) | Error FK en BD (500), sin validación previa | Debería validar existencia |
| 3 | `leader_id` apunta a usuario sin rol leader | Se asigna igual | Sin restricción |
| 4 | Sync de campo_conocimientos: pérdida de datos si falla a mitad | Elimina todos los existentes, luego crea nuevos. Si falla en el CREATE, los registros originales ya fueron eliminados | **Riesgo de pérdida de datos.** Debe ejecutarse en transacción. |
| 5 | Dos GET routes para index (con y sin /index) | Ambas funcionan, posible duplicación SEO | Unificar a una sola ruta canónica |
| 6 | Edit modal con `$area_conocimiento` de variable de loop | Renderiza bien en modal porque la variable `$area_conocimiento` está disponible en el foreach | ✅ Correcto |
| 7 | `edit.blade.php` (página completa) corrupto — copia de Banco | Renderiza `$banco` indefinido → Error 500 si alguien navega directamente | Ruta `edit/{id}` no usada (todo modal), pero sigue siendo accesible |
| 8 | `order` fuera de rango 1-15 | Sin validación, podría insertarse cualquier valor | Debe ser integer entre 1-15 |
| 9 | SoftDeletes comentado pero columna existe | Los registros eliminados físicamente no se pueden recuperar a pesar de que la columna `deleted_at` existe | Activar SoftDeletes o eliminar columna |
| 10 | Sin paginación server-side | `AreaConocimiento::all()` sin paginate — toda la colección en memoria | Riesgo de rendimiento con muchas áreas |

### 8.3. Checklist de Validaciones Adicionales (Migración API)

- [ ] `POST /PUT`: Validar `pestudio_id` como `exists:pestudios,id`
- [ ] `POST /PUT`: Validar `peducativo_id` como `exists:peducativos,id` (nullable)
- [ ] `POST /PUT`: Validar `leader_id` como `exists:users,id` (nullable)
- [ ] `POST /PUT`: Validar `name` como `required|string|max:255`
- [ ] `POST /PUT`: Validar `code` como `string|max:50|unique:area_conocimientos,code`
- [ ] `POST /PUT`: Validar `order` como `integer|min:1|max:15`
- [ ] `POST /PUT`: Validar `enable_academic_index` como `in:true,false`
- [ ] `POST /PUT`: Validar `asignatura_ids[]` como `array|exists:asignaturas,id`
- [ ] `DELETE`: Verificar `status_delete` antes de permitir
- [ ] `SYNC campos-conocimiento`: Envolver en `DB::transaction()`
- [ ] `GET /promedio`: Validar `pestudio_id` y `range` (lapso_id) como exists

---

## 9. Dependencias e Integraciones

| Componente/Sistema | Tipo | Naturaleza |
|-------------------|------|------------|
| Pestudio (modelo) | Relación directa | FK `pestudio_id` |
| Peducativo (modelo) | Relación directa | FK `peducativo_id` |
| User (modelo) | Relación leader | FK `leader_id` |
| Asignatura (modelo) | Indirecta vía CampoConocimiento | Pivote área ↔ asignatura |
| CampoConocimiento (modelo) | Sub-módulo | Relación hasMany, FK |
| Pensum (modelo) | Join indirecto | Via asignatura → campo_conocimientos |
| Grado (modelo) | Join indirecto | Via pensum en reportes |
| Seccion (modelo) | Join indirecto | Via grado en reportes |
| Inscripcion (modelo) | Join indirecto | Para conteo de estudiantes |
| Boletin (modelo) | Join indirecto | Para promedios y notas |
| Pevaluacion (modelo) | Join indirecto | Para evaluaciones del área |
| Evaluacion (modelo) | Join indirecto | Para notas del área |
| Profesor (modelo) | Join indirecto | Para reportes de profesores |
| Director dashboard | Consumidor | `AreaConocimiento::all()` |
| Academico dashboard | Consumidor | `AreaConocimiento::all()` |
| Leader dashboard | Consumidor | Filtrado por `leader_id` |
| 4 Chart delegators | Consumidor | Reutilizan chart controller |
| 7+ Livewire componentes | Consumidor | Usan `AreaConocimiento` para consultas |

---

## 10. Comparación con Módulos Anteriores

| Característica | Asignaturas | Pestudios | Grados | Secciones | Lapsos | Baremos | GrupoEstables | **AreaConocimiento** |
|----------------|-------------|-----------|--------|-----------|--------|---------|---------------|---------------------|
| Livewire CRUD | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | **❌** |
| FormRequest Validation | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ (inline) | ✅ | **❌ (deshabilitados)** |
| SoftDeletes | Comentado | ✅ | ✅ | ❌ (físico) | ✅ | ✅ | ❌ (comentado) | **❌ (comentado)** |
| Modal CRUD | ❌ | ❌ | ✅ | ✅ | ✅ | ❌ | ❌ | **✅ (modal-based)** |
| Sub-módulo anidado | ❌ | ❌ | ❌ | ❌ | ✅ (Census) | ❌ | ❌ | **✅ (CampoConocimiento)** |
| Chart.js integrado | ❌ | ❌ | ❌ | ❌ | ✅ (3 charts) | ❌ | ❌ | **✅ (3 charts)** |
| Admin Sidebar Routes | ❌ | ✅ | ✅ (legacy) | ✅ (is_common) | ❌ | ❌ | ❌ | **❌** |
| Archivos huérfanos en views | Mínimo | Mínimo | Stale peducativos | 1 (edit=BANCO) | 1 (edit=BANCO) | — | 5+ | **3+ (edit=BANCO, show=Users, menus/crud)** |
| `status_delete` accessor | ❌ | ❌ | ✅ | ✅ | ✅ | ❌ | ✅ | **✅** |
| `COLUMN_COMMENTS` | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | **✅** |
| Métodos `list_*()` estáticos | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ (5) | **❌** |
| Uso externo (>10 callers) | Bajo | Alto | Alto | Muy alto | Máximo | Bajo | Alto | **Alto (14+ controllers, 7+ Livewire)** |
| Migraciones perdidas | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ (2 campos) | **❌** |
| Relaciones huérfanas | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ (pestudio_id) | **❌** |
| Relaciones duplicadas | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **✅ (area_conocimiento + areaConocimiento)** |
| Controlador delegado multi-rol | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | **✅ (4 chart delegators)** |
| Tamaño de vistas | 12 | 15 | 15 | 14 | 23+ | 2+LV | 14 | **27 (el más grande)** |

---

## 11. Resumen de Hallazgos

### Críticos
1. **FormRequests deshabilitados:** Ambos `CreateAreaConocimientoRequest` y `UpdateAreaConocimientoRequest` retornan `authorize() = false`, lo que impide su uso. El controlador usa `Illuminate\Http\Request` sin validación server-side en ningún campo.
2. **`edit.blade.php` es copia de Banco:** Renderiza `$banco`, usa ruta `bancoupdate`, incluye `banco.form.field`. Si alguien accede a la ruta de edición directa (no modal), genera Error 500.
3. **Sync sin transacción:** `CampoConocimiento@store` elimina todos los registros existentes y luego recrea. Si falla en CREATE, los datos originales se pierden irreversiblemente. Riesgo de pérdida de datos.
4. **SoftDeletes comentado pero columna existe:** En ambos modelos (AreaConocimiento y CampoConocimiento). La columna `deleted_at` existe en `area_conocimientos` pero no en `campo_conocimientos`. Eliminación física sin recuperación posible.

### Moderados
5. **Bug en `edit()` del controlador:** `compact('area_conocimientos', ...)` — variable `$area_conocimientos` (snake_case, plural) no está definida; el modelo se llama `$AreaConocimiento` (PascalCase). También pasa `$list_comment_area` dos veces en lugar de `$list_comment_grupo`.
6. **Relación duplicada en CampoConocimiento:** `area_conocimiento()` (string-based) y `areaConocimiento()` (class-based) hacen exactamente lo mismo.
7. **Sin validación de unicidad para `code`:** A diferencia de Asignaturas y GrupoEstables, el código del área no tiene validación `unique:`.
8. **`menus/show.blade.php` copia de Users:** Referencia `route('users.create')`, completamente fuera de contexto.

### Bajos
9. **Dos rutas GET para index:** `/configuraciones/area_conocimientos` y `/configuraciones/area_conocimientos/index` — ambas hacen lo mismo.
10. **Sin paginación server-side:** `AreaConocimiento::all()` carga todos los registros en memoria.
11. **Chart delegators patrón inusual:** 4 controladores que instancian el mismo controller admin y delegan. Duplicación innecesaria — podría usarse un trait o un solo endpoint con permisos.
12. **`getPromedio()` sin manejo de división por cero:** Si `$count->value` es 0, `$count->sum_nota / 0` genera error de división.
13. **`peducativo_id` sin validación de consistencia:** Podría seleccionarse un peducativo que no contenga el pestudio seleccionado.
14. **Sin migración para `status_belongs_ins` heredado:** El modelo GrupoEstable tiene `status_belongs_ins`, pero Área de Conocimiento no tiene campos similares.

---

*Documento generado: 2026-06-06. Validado contra código fuente.*
