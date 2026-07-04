# Gestión de Planes de Estudio (Control de Estudios) — Documento Técnico Completo

> **Versión:** 1.0 — Validada contra código fuente real (Laravel 8.x / PHP 8.2)
> **Módulo:** `control.configuraciones.pestudios` — CRUD de planes de estudio (Pestudio).
> **Propósito:** Replicación en NextJS + API REST (Laravel o Node).
> **Arquitectura fuente:** Controlador tradicional (sin Livewire) con Blade + jQuery + DataTables. Modelo pesado (~859 líneas + 5 traits).

---

## 1. Introducción

El módulo **Gestión de Planes de Estudio** (`Pestudio`) es la entidad central del sistema académico SAEFL. Un plan de estudio define la estructura curricular completa de un nivel educativo (por ejemplo: "Media General", "Educación Inicial", "Educación Primaria").

Cada Pestudio agrupa:
- **Grados** (1° a 5° año, etc.)
- **Asignaturas** (materias que lo componen)
- **Pensums** (asignación materia ↔ grado)
- **Baremos** (escalas de notas)
- **Secciones** (aulas agrupadas por grado)
- **Evaluaciones** (carga académica de profesores)
- **Inscripciones** (estudiantes matriculados)

A diferencia de los módulos de Planning, usa **CRUD tradicional con controlador + Blade** (sin Livewire) y **DataTables** para visualización. El modelo tiene validación prácticamente nula del lado del servidor.

---

## 2. Arquitectura del Dominio

### 2.1 Cadena de dependencias

```
Peducativo (programa educativo)
    └── Pestudio (plan de estudio) ←── MÓDULO ACTUAL
            ├── manager_id → User (responsable del plan)
            ├── scale → Escala (escala de evaluación asociada)
            ├── grados[] → Grado
            │       └── seccions[] → Seccion
            │             └── pevaluacions[] → Pevaluacion (carga académica)
            ├── asignaturas[] → Asignatura
            │       └── pensums[] → Pensum (asignatura + grado)
            │             └── pevaluacions[] → Pevaluacion
            ├── baremos[] → Baremo (tabla de calificaciones)
            ├── grupo_estables[] → GrupoEstable
            ├── registro_titulos[] → RegistroTitulo
            ├── pevaluacions() → (hasManyThrough Pensum → Pevaluacion)
            │
            └── (referenciado por ~30 modelos: Inscripcion, Boletin,
                 Evaluacion, Activity, HistoricoNota, DiagMain, PollMain,
                 Pase, Mailer, Preinscripcion, Census, etc.)
```

### 2.2 Árbol de archivos

```
routes/
  web.php                                              ← grupo /app/control con middleware is_control
  app/control.php                                      ← require __DIR__ . '/tab/pestudio.php'
  app/tab/pestudio.php                                 ← 7 rutas CRUD (control module)
  administracion/tab/pestudio.php                      ← 3 rutas CRUD (admin sidebar, mismo controlador)

app/
  Http/
    Controllers/Administracion/Tab/Configuracion/
      PestudioController.php                           ← 6 métodos, SIN form requests, SIN validación
    Requests/Administracion/Configuracion/
      (no existen — no hay FormRequest para Pestudio)
  Models/
    app/
      Pescolar/
        Pestudio.php                                   ← 859 líneas, SoftDeletes, 5 traits, 33 fillable
        Functions/Pestudio/
          Lists.php                                    ← list_pestudio(), getPestudios(), list_pestudio_grado_manage()
          Inscripcions.php                             ← a_inscritos(), a_varones(), a_hembras(), inscritos(), etc.
          Preinscripcions.php                          ← preinscripcions(), pre_varones(), pre_hembras()
          ActivitiesTrait.php                          ← getActivities(), getAvgActivitiesPerPlan(), getProfesors(), etc.
          EvaluacionTrait.php                          ← getEvaluacions(), getProfesorEvaluacions()

resources/views/
  administracion/configuraciones/pestudios/
    index.blade.php                                    ← listado principal
    create.blade.php                                   ← formulario de creación
    edit.blade.php                                     ← formulario de edición
    card.blade.php                                     ← tarjeta de resumen
    form/fields.blade.php                              ← ~30 campos compartidos
    table/index.blade.php                              ← DataTable
    show/details.blade.php                             ← modal de detalles
    menus/index.blade.php                              ← botones: [+ Nuevo] [↺] [← Volver]
    menus/create.blade.php                             ← botones: [Listado] [← Volver] [↺]
    menus/edit.blade.php                               ← botones: [Pensums] [← Volver] [↺]
    menus/crud.blade.php                               ← botones: [Listado] [← Volver] [↺]
    menus/show.blade.php                               ← botones: [Nuevo Usuario] [Listados] [← Volver] [↺]
```

---

## 3. Validación contra código fuente

### 3.1 Hallazgos clave

| # | Tópico | Detalle |
|---|--------|---------|
| 1 | **Sin Livewire** | CRUD tradicional con controlador + Blade. Sin reactividad. |
| 2 | **Sin validación server-side** | El controlador usa `Illuminate\Http\Request` directamente. **No hay** `$request->validate()`, ni Form Requests, ni reglas de validación de ningún tipo. Toda la confianza está en la base de datos. |
| 3 | **`is_control` middleware** | El grupo usa `auth` + `is_control`. Ruta real: `/app/control/configuraciones/pestudios/index` |
| 4 | **Dos conjuntos de rutas** | `routes/app/tab/pestudio.php` (control) y `routes/administracion/tab/pestudio.php` (sidebar admin). El segundo tiene una convención de nombre distinta: usa `{id}` en lugar de `{pestudio}` y las rutas no tienen prefijo `configuraciones`. |
| 5 | **Modelo extremadamente pesado** | 859 líneas + 5 traits. Contiene lógica de indicadores, consultas agregadas, conteos de estudiantes, notas, evaluaciones. Viola SRP. |
| 6 | **~30 campos en fillable** | 33 campos fillable, muchos opcionales. No hay `$guarded` ni `$casts`. Los enum se almacenan como strings. |
| 7 | **ENUM('true','false') repetido** | `status_active`, `show_hr`, `status_a_cualitative`, `status_baremo`, `status_carga_notas`, `status_build_promotion`, `show_quantitative_indicators`, `status_socials` — 8 campos ENUM string. |
| 8 | **`COLUMN_COMMENTS` como metadatos** | El modelo define un array estático `COLUMN_COMMENTS` con 29 traducciones campo → etiqueta. Las vistas lo usan para generar labels automáticamente. |
| 9 | **Campos en fillable sin migración respaldada** | `remision_resumen_final`, `fecha_informe_final`, `fecha_certificacion`, `fecha_descriptivo`, `fecha_promocion`, `fecha_prosecucion`, `planning_module`, `activities_avr` existen en el fillable pero no aparecen en ninguna migración capturada. |
| 10 | **Sin paginación backend** | `Pestudio::orderBy('created_at','DESC')->get()` sin paginate(). La paginación visual es DataTables del lado del cliente. |
| 11 | **Dos convenciones de nombre de ruta** | Módulo control usa `administracion.configuraciones.pestudios.{action}`. Sidebar admin usa `administracion.configuraciones.pestudio.{action}` (sin 's'). El `update` del controlador redirige a la versión admin. |
| 12 | **`status_delete` lógica personalizada** | El modelo expone un accessor `getStatusDeleteAttribute()` que retorna `true` solo si `$this->grados()->count() === 0`. El controlador lo verifica antes de eliminar. |
| 13 | **SoftDeletes activo** | A diferencia de Asignatura, el trait `SoftDeletes` está activo y sin comentar en el modelo. |
| 14 | **`scale` como FK a Escala** | El campo `scale` es un `integer` que referencia `escalas.id`, NO un enum. El modelo tiene `belongsTo(Escala::class, 'scale')`. |
| 15 | **`color` como string de clase CSS** | Se almacena como `'info'`, `'primary'` o `'success'` — clases de Bootstrap 4 para el color de la card. |

### 3.2 Validación de rutas

#### Rutas del módulo Control (principal)

| Método | URI | Controlador | Middleware | Nombre |
|--------|-----|-------------|------------|--------|
| GET | `/configuraciones/pestudios` | `PestudioController@index` | `auth`, `is_control` | `administracion.configuraciones.pestudios` |
| GET | `/configuraciones/pestudios/index` | `PestudioController@index` | `auth`, `is_control` | `administracion.configuraciones.pestudios.index` |
| GET | `/configuraciones/pestudios/create` | `PestudioController@create` | `auth`, `is_control` | `administracion.configuraciones.pestudios.create` |
| POST | `/configuraciones/pestudios/store` | `PestudioController@store` | `auth`, `is_control` | `administracion.configuraciones.pestudios.store` |
| GET | `/configuraciones/pestudios/edit/{id}` | `PestudioController@edit` | `auth`, `is_control` | `administracion.configuraciones.pestudios.edit` |
| PUT | `/configuraciones/pestudios/update/{id}` | `PestudioController@update` | `auth`, `is_control` | `administracion.configuraciones.pestudios.update` |
| DELETE | `/configuraciones/pestudios/destroy/{id}` | `PestudioController@destroy` | `auth`, `is_control` | `administracion.configuraciones.pestudios.destroy` |

#### Rutas del sidebar Admin (secundarias)

| Método | URI | Controlador | Middleware | Nombre |
|--------|-----|-------------|------------|--------|
| GET | `/configuraciones/pestudio` | `PestudioController@index` | `auth`, `is_control` | `administracion.configuraciones.pestudio` |
| GET | `/configuraciones/pestudio/{id}` | `PestudioController@edit` | `auth`, `is_control` | `administracion.configuraciones.pestudio.edit` |
| PUT | `/configuraciones/pestudio/{id}` | `PestudioController@update` | `auth`, `is_control` | `administracion.configuraciones.pestudio.update` |

---

## 4. Lógica de Negocio — Especificación Completa

### 4.1 Reglas de negocio

**RN-01: Código único (por base de datos).**
Aunque el controlador no valida, el código `code` debería ser único. No hay UNIQUE INDEX en la migración original, pero la lógica del sistema asume que cada Pestudio tiene un `code` único para identificarlo en selectores y reportes.

**RN-02: Asociación obligatoria a Programa Educativo.**
`peducativo_id` es requerido en el formulario y es FK a `peducativos`. Define el tipo de educación (Media General, Inicial, etc.).

**RN-03: Responsable del plan (manager).**
`manager_id` es FK a `users`. Asigna un usuario responsable. Solo visible en el formulario para usuarios con rol `@admin`.

**RN-04: Flags booleanos como ENUM('true','false').**
Ocho campos se almacenan como strings `'true'`/`'false'`: `status_active`, `show_hr`, `status_a_cualitative`, `status_baremo`, `status_carga_notas`, `status_build_promotion`, `show_quantitative_indicators`, `status_socials`. Se renderizan como selects con opciones SI/NO.

**RN-05: Protección contra eliminación.**
Un Pestudio solo se puede eliminar si NO tiene grados asociados. El accessor `getStatusDeleteAttribute()` evalúa `$this->grados()->count() === 0`. El controlador verifica esto antes de eliminar.

**RN-06: Eliminación lógica (soft-delete).**
`SoftDeletes` está activo. `$pestudio->delete()` marca `deleted_at`. Los métodos de consulta en los traits frecuentemente filtran `->whereNull('pestudios.deleted_at')` explícitamente.

**RN-07: Escala de evaluación predeterminada.**
El accessor `getEscalaAttribute()` mapea el Pestudio con `id=1` a `escala_id=3` y todos los demás a `escala_id=1`. Esto es una regla hardcodeada para compatibilidad legacy.

**RN-08: Orden de presentación (1-10).**
El campo `order` (select de 1 a 10) define la posición del Pestudio en listados. El controller ordena por `created_at DESC`, no por `order` — la ordenación se delega a DataTables.

**RN-09: Fechas de procesos académicos.**
Seis campos de fecha (`fecha_informe_final`, `fecha_certificacion`, `fecha_descriptivo`, `fecha_promocion`, `fecha_prosecucion`, `remision_resumen_final`) definen el calendario de cierre de cada Pestudio.

**RN-10: Módulo de planificación.**
`planning_module` (TINYINT, 0/1) habilita o deshabilita la integración con el módulo Planning para este plan de estudio.

### 4.2 Flujo de datos completo

```
[Usuario autenticado con rol is_control]
    │
    ├─(1) GET .../configuraciones/pestudios
    │     └─ PestudioController@index()
    │           ├─ Pestudio::orderBy('created_at','DESC')->get()
    │           ├─ carga COLUMN_COMMENTS + peducativos
    │           └─ view('...pestudios.index')
    │                 ├── table/index.blade.php (DataTable)
    │                 └── menus/index.blade.php
    │
    ├─(2) GET .../pestudios/create
    │     └─ PestudioController@create()
    │           ├─ Peducativo::pluck('name','id')
    │           ├─ User::pluck('username','id')
    │           └─ view('...pestudios.create')
    │                 ├── form/fields.blade.php (~30 campos)
    │                 └── menus/create.blade.php
    │
    ├─(3) POST .../pestudios/store
    │     └─ PestudioController@store(Request)
    │           ├─ (SIN VALIDACIÓN)
    │           ├─ Pestudio::create($request->all())
    │           ├─ Session::flash('operp_ok', ...)
    │           └─ redirect → administracion.configuraciones.pestudios.index
    │
    ├─(4) GET .../pestudios/edit/{id}
    │     └─ PestudioController@edit($id)
    │           ├─ Pestudio::findOrFail($id)
    │           ├─ Peducativo::pluck('name','id')
    │           ├─ User::pluck('username','id')
    │           └─ view('...pestudios.edit')
    │                 ├── form/fields.blade.php (precargado)
    │                 └── menus/edit.blade.php (→ Pensums)
    │
    ├─(5) PUT .../pestudios/update/{id}
    │     └─ PestudioController@update(Request, $id)
    │           ├─ (SIN VALIDACIÓN)
    │           ├─ findOrFail → fill → save
    │           └─ redirect → administracion.configuraciones.pestudio (admin sidebar)
    │
    └─(6) DELETE .../pestudios/destroy/{id}
          └─ PestudioController@destroy($id, Request)
                ├─ Pestudio::findOrFail($id)
                ├─ Verifica $pestudio->status_delete (grados count === 0)
                ├─ Si NO tiene grados → delete()
                ├── Si AJAX → response()->json([...])
                └── redirect → administracion.configuraciones.pestudios.index
```

---

## 5. Esquemas de Base de Datos

### 5.1 Tabla `pestudios`

```sql
CREATE TABLE `pestudios` (
  `id`                           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `peducativo_id`                INT UNSIGNED NOT NULL COMMENT 'Programa educativo (FK peducativos)',
  `manager_id`                   INT UNSIGNED NULL COMMENT 'Responsable (FK users)',
  `code`                         VARCHAR(255) NOT NULL COMMENT 'Código del plan',
  `code_oficial`                 VARCHAR(255) NULL COMMENT 'Código oficial',
  `name`                         VARCHAR(255) NOT NULL COMMENT 'Nombre del plan',
  `order`                        INT NOT NULL COMMENT 'Orden de presentación (1-10)',
  `description`                  VARCHAR(255) NOT NULL COMMENT 'Descripción',
  `description_aux`              VARCHAR(255) NULL COMMENT 'Descripción auxiliar',
  `mention`                      VARCHAR(255) NULL COMMENT 'Mención',
  `status_build_promotion`       ENUM('true','false') NOT NULL COMMENT 'Genera promoción',
  `title`                        VARCHAR(255) NULL COMMENT 'Título completo',
  `scale`                        INT NOT NULL COMMENT 'Escala de evaluación (FK escalas)',
  `profile`                      TEXT NULL COMMENT 'Perfil de egreso',
  `color`                        VARCHAR(255) NOT NULL COMMENT 'Clase CSS (info|primary|success)',
  `show_hr`                      ENUM('true','false') NOT NULL COMMENT 'Mostrar en cuadro de honor',
  `status_a_cualitative`         ENUM('true','false') NOT NULL COMMENT 'Asociado a notas cualitativas',
  `status_baremo`                ENUM('true','false') NOT NULL COMMENT 'Nota final literal (baremo)',
  `status_active`                ENUM('true','false') DEFAULT 'true' COMMENT 'Activo',
  `status_carga_notas`           ENUM('true','false') DEFAULT 'false' COMMENT 'Carga de notas activa',
  `status_inscripcion_active`    TINYINT(1) DEFAULT 1 COMMENT 'Inscripción activa',
  `show_quantitative_indicators` ENUM('true','false') DEFAULT 'true' COMMENT 'Mostrar indicadores cuantitativos',
  `status_socials`               TINYINT(1) DEFAULT 1 COMMENT 'Actividad social activa',
  `remision_resumen_final`       DATE NULL COMMENT 'Fecha remisión resumen final',
  `fecha_informe_final`          DATE NULL COMMENT 'Fecha informe final',
  `fecha_certificacion`          DATE NULL COMMENT 'Fecha certificación',
  `fecha_descriptivo`            DATE NULL COMMENT 'Fecha descriptivo',
  `fecha_promocion`              DATE NULL COMMENT 'Fecha promoción',
  `fecha_prosecucion`            DATE NULL COMMENT 'Fecha prosecución',
  `description_trainig_component` VARCHAR(255) NULL COMMENT 'Componente de formación',
  `planning_module`              TINYINT(1) DEFAULT 0 COMMENT 'Habilitar módulo Planning',
  `activities_avr`               INT NULL COMMENT 'AVR de actividades',
  `paper`                        ENUM('oficial','letter') DEFAULT 'letter' COMMENT 'Formato papel',
  `deleted_at`                   TIMESTAMP NULL,
  `created_at`                   TIMESTAMP NULL,
  `updated_at`                   TIMESTAMP NULL,

  INDEX `pestudios_peducativo_id_index` (`peducativo_id`),
  INDEX `pestudios_manager_id_index` (`manager_id`),

  CONSTRAINT `pestudios_peducativo_id_foreign`
    FOREIGN KEY (`peducativo_id`) REFERENCES `peducativos`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `pestudios_manager_id_foreign`
    FOREIGN KEY (`manager_id`) REFERENCES `users`(`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.2 Tabla `peducativos` (padre)

```sql
CREATE TABLE `peducativos` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code`          VARCHAR(255) NOT NULL,
  `name`          VARCHAR(255) NOT NULL,
  `description`   TEXT NULL,
  `orden`         INT DEFAULT 0,
  `status_active` ENUM('true','false') DEFAULT 'true',
  `deleted_at`    TIMESTAMP NULL,
  `created_at`    TIMESTAMP NULL,
  `updated_at`    TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.3 Tabla `escalas` (relacionada vía `scale`)

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

### 5.4 Tablas hijas (grados)

```sql
CREATE TABLE `grados` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pestudio_id`   INT UNSIGNED NOT NULL,
  `code`          VARCHAR(255) NOT NULL,
  `name`          VARCHAR(255) NOT NULL,
  `order`         INT NOT NULL,
  `description`   TEXT NULL,
  `status_active` ENUM('true','false') DEFAULT 'true',
  `deleted_at`    TIMESTAMP NULL,
  `created_at`    TIMESTAMP NULL,
  `updated_at`    TIMESTAMP NULL,
  FOREIGN KEY (`pestudio_id`) REFERENCES `pestudios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 6. Modelo de Datos — API REST para exportación

### 6.1 Endpoints propuestos

#### `GET /api/control/pestudios`

Listado paginado de planes de estudio.

**Query params:** `search`, `peducativo_id`, `status_active`, `page`, `per_page`, `sort_by`, `sort_dir`

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "peducativo": { "id": 2, "code": "MG", "name": "MEDIA GENERAL" },
      "manager": { "id": 3, "username": "jperez", "full_name": "Juan Pérez" },
      "code": "MG-2025",
      "code_oficial": "MG-OF-001",
      "name": "MEDIA GENERAL",
      "order": 1,
      "description": "Educación Media General",
      "scale": { "id": 1, "name": "Escala 1-20" },
      "color": "info",
      "status_active": true,
      "status_build_promotion": true,
      "show_hr": true,
      "status_a_cualitative": false,
      "status_baremo": true,
      "status_carga_notas": true,
      "planning_module": true,
      "grados_count": 5,
      "asignaturas_count": 12,
      "created_at": "2025-09-01T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 4
  }
}
```

#### `GET /api/control/pestudios/{id}`

Detalle completo con relaciones.

**Response (200):**
```json
{
  "id": 1,
  "peducativo": { "id": 2, "code": "MG", "name": "MEDIA GENERAL" },
  "manager": { "id": 3, "username": "jperez" },
  "code": "MG-2025",
  "code_oficial": "MG-OF-001",
  "name": "MEDIA GENERAL",
  "order": 1,
  "description": "Educación Media General",
  "description_aux": "Formación académica de adolescentes",
  "mention": null,
  "title": "Bachiller en Ciencias",
  "scale": { "id": 1, "name": "Escala 1-20" },
  "profile": "Perfil de egreso del bachiller...",
  "color": "info",
  "show_hr": true,
  "status_a_cualitative": false,
  "status_baremo": true,
  "status_active": true,
  "status_carga_notas": true,
  "status_inscripcion_active": true,
  "status_build_promotion": true,
  "show_quantitative_indicators": true,
  "status_socials": true,
  "grados": [
    { "id": 1, "code": "1A", "name": "1ER AÑO", "order": 1 }
  ],
  "fecha_informe_final": "2026-07-15",
  "fecha_certificacion": "2026-07-30",
  "fecha_promocion": "2026-08-01",
  "planning_module": true,
  "activities_avr": 3
}
```

#### `POST /api/control/pestudios`

Crear nuevo plan de estudio.

**Validaciones propuestas (no existen en fuente original):**
```json
{
  "peducativo_id": "required|integer|exists:peducativos,id",
  "code": "required|string|max:255",
  "code_oficial": "nullable|string|max:255",
  "name": "required|string|max:255",
  "order": "required|integer|min:1|max:10",
  "description": "required|string|max:255",
  "description_aux": "nullable|string|max:255",
  "mention": "nullable|string|max:255",
  "status_build_promotion": "required|boolean",
  "title": "nullable|string|max:255",
  "scale": "required|integer|exists:escalas,id",
  "profile": "nullable|string",
  "color": "required|in:info,primary,success",
  "show_hr": "required|boolean",
  "status_a_cualitative": "required|boolean",
  "status_baremo": "required|boolean",
  "status_active": "required|boolean",
  "status_carga_notas": "required|boolean",
  "status_inscripcion_active": "boolean",
  "show_quantitative_indicators": "boolean",
  "status_socials": "boolean",
  "remision_resumen_final": "nullable|date",
  "fecha_informe_final": "nullable|date",
  "fecha_certificacion": "nullable|date",
  "fecha_descriptivo": "nullable|date",
  "fecha_promocion": "nullable|date",
  "fecha_prosecucion": "nullable|date",
  "description_trainig_component": "nullable|string|max:255",
  "manager_id": "nullable|integer|exists:users,id",
  "planning_module": "boolean",
  "activities_avr": "nullable|integer"
}
```

#### `PUT /api/control/pestudios/{id}`

Actualizar plan de estudio. Mismas validaciones que create.

#### `DELETE /api/control/pestudios/{id}`

Eliminación lógica. Retorna 409 si tiene grados asociados.

**Response 200:**
```json
{
  "message": "Plan de estudio eliminado correctamente.",
  "deleted_at": "2026-06-06T12:00:00Z"
}
```

**Error 409:**
```json
{
  "message": "No se puede eliminar: el plan de estudio tiene grados asociados.",
  "grados_count": 5
}
```

#### `GET /api/control/pestudios/filters`

Datos para poblar filtros y formularios.

```json
{
  "peducativos": [ { "id": 1, "code": "MG", "name": "MEDIA GENERAL" } ],
  "escalas": [ { "id": 1, "name": "Escala 1-20" } ],
  "users": [ { "id": 3, "username": "jperez" } ],
  "color_options": ["info", "primary", "success"],
  "order_range": [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
}
```

---

## 7. Especificación de Componentes (NextJS + Tailwind)

### 7.1 Página principal: `ControlPestudiosPage`

```
┌──────────────────────────────────────────────────────────────────────────┐
│  Control de Estudios > Configuraciones > Planes de Estudio               │
├──────────────────────────────────────────────────────────────────────────┤
│  [+ Nuevo Plan de Estudio] [↺ Refrescar]  (admin: [+])                   │
├──────────────────────────────────────────────────────────────────────────┤
│  [Buscar plan...]                                                         │
├─────┬───────────┬─────────────┬──────────────────────────┬────────┬──────┤
│  #  │ Código    │ Cód.Ofc.    │ Nombre                   │ Estado │ Acc. │
├─────┼───────────┼─────────────┼──────────────────────────┼────────┼──────┤
│  1  │ MG-2025   │ MG-OF-001   │ MEDIA GENERAL            │ ✅     │[👁][✏️][🗑]│
│  2  │ INI-2025  │ INI-OF-001  │ EDUCACIÓN INICIAL        │ ✅     │[👁][✏️][🗑]│
│  3  │ PRIM-2025 │ PRIM-OF-001 │ EDUCACIÓN PRIMARIA       │ ✅     │[👁][✏️][🗑]│
├─────┴───────────┴─────────────┴──────────────────────────┴────────┴──────┤
│  Showing 1 to 3 of 4 entries                     [1] [2]                 │
└──────────────────────────────────────────────────────────────────────────┘
```

### 7.2 Formulario de creación/edición (3 secciones)

```
┌─ Información General ─────────────────────────────────────────┐
│  Programa Educativo:   [MEDIA GENERAL         ▼]              │
│  Código:               [MG-2025            ]                 │
│  Código Oficial:       [MG-OF-001          ]                 │
│  Nombre:               [MEDIA GENERAL                        │
│  Orden:                [1 ▼]  Color: [info ▼]                │
│  Descripción:          [Educación Media General...]           │
│  Descripción Auxiliar: [____________________]                │
│  Mención:              [____________________]                │
│  Título:               [Bachiller en Ciencias...]             │
│  Perfil:               [textarea...]                          │
│  Componente Formación: [____________________]                │
│  Responsable (admin):  [jperez ▼]                             │
├─ Configuración ───────────────────────────────────────────────┤
│  Escala:               [Escala 1-20 ▼]                       │
│  ☑ Activo              ☑ Genera Promoción                     │
│  ☑ Cuadro de Honor     ☑ Asociado a Cualitativo              │
│  ☑ Nota Final Literal  ☑ Carga de Notas                      │
│  ☑ Indicadores Cuant.  ☑ Actividad Social                    │
│  ☑ Inscripciones Act.  ☑ Módulo Planning                     │
├─ Fechas de Cierre ────────────────────────────────────────────┤
│  Remisión Resumen:  [____/____/____]                         │
│  Informe Final:     [____/____/____]                         │
│  Certificación:     [____/____/____]                         │
│  Descriptivo:       [____/____/____]                         │
│  Promoción:         [____/____/____]                         │
│  Prosecución:       [____/____/____]                         │
├───────────────────────────────────────────────────────────────┤
│  [████ Guardar ████]                                          │
└───────────────────────────────────────────────────────────────┘
```

### 7.3 Árbol de componentes

```
ControlPestudiosPage
├── PageHeader (título + breadcrumb)
├── ActionBar (botones: Nuevo, Refrescar — admin: + Nuevo visible)
├── PestudioTable
│   ├── SearchInput (búsqueda client-side)
│   ├── TableHeader (columnas ordenables)
│   └── PestudioRow (× N)
│       ├── ShowButton → DetailsModal
│       ├── EditLink → EditPestudioPage
│       └── DeleteButton (verifica grados_count)
│           └── ConfirmDialog "¿Eliminar plan {name}?"
├── CreatePestudioPage
│   └── PestudioForm (3 secciones)
│       ├── GeneralInfoSection
│       ├── ConfigFlagsSection
│       └── DatesSection
└── EditPestudioPage
    ├── PestudioForm (precargado)
    └── QuickLinksCard
        ├── [Grados]
        ├── [Asignaturas]
        ├── [Pensums]
        ├── [Baremos]
        └── [Secciones]
```

### 7.4 Estados de cada componente

| Componente | Loading | Empty | Error | Success |
|-----------|---------|-------|-------|---------|
| `PestudioTable` | Skeleton 5 filas grises | "No hay planes de estudio registrados" con ilustración | Toast "Error al cargar datos" | DataTable con datos |
| `PestudioForm` | Spinner en submit | Campos vacíos (create) o precargados (edit) | Errores inline por campo + toast | Redirect al listado |
| `DetailsModal` | Spinner | N/A | Toast error | Todos los campos en 3 secciones |
| `DeleteButton` | Spinner en botón | N/A | Toast "Tiene grados asociados" | Eliminación + refresh |
| `ConfigFlagsSection` | N/A | N/A | N/A | Toggles SI/NO |
| `DatesSection` | N/A | Campos vacíos | Error formato fecha | Datepickers |

---

## 8. Validaciones y Edge Cases

### 8.1 Validaciones del servidor

| Estado actual | Descripción | Riesgo |
|--------------|-------------|--------|
| ❌ Sin validación | `$request->all()` se pasa directamente a `create()` | SQL injection potencial (aunque Eloquent escapa), datos corruptos |
| ❌ Sin Form Requests | No existen CreatePestudioRequest ni UpdatePestudioRequest | Validación cero |
| ✅ SoftDeletes activo | Eliminaciones marcan `deleted_at` | OK |
| ⚠️ `status_delete` | Solo verifica grados, no otras relaciones (asignaturas, pensums, baremos) | Puede dejar huérfanos |

### 8.2 Edge cases

| Caso | Comportamiento esperado |
|------|------------------------|
| Pestudio con grados → eliminar | Error: "No se puede eliminar" (solo verifica grados, no otras tablas) |
| Pestudio sin grados → eliminar | Soft-delete exitoso, marca `deleted_at` |
| `status_active` = 'false' | El plan no aparece en selectores que usan `scopeActive()` |
| `planning_module` = 1 | Habilita funcionalidades del módulo Planning (actividades, planificaciones) |
| Sin `manager_id` | El plan no tiene responsable asignado (navegación ok) |
| `color` inválido | Se renderiza igual pero sin el color Bootstrap esperado |
| Fechas en formato incorrecto | La BD acepta strings, no hay validación de formato fecha |
| `scale` igual a NULL | El accessor `getEscalaAttribute()` mapea a escala_id=1 por defecto |
| Actualización redirige a ruta admin | El `update()` redirige a `administracion.configuraciones.pestudio` (sin 's') |
| Código duplicado | La BD no tiene UNIQUE INDEX en `code` — se permite, pero el sistema asume unicidad |

---

## 9. Plan de Migración: Laravel/Blade → NextJS + API

### Fase 1: Backend API

| Prioridad | Endpoint | Descripción |
|-----------|----------|-------------|
| P0 | `GET /api/control/pestudios` | Listado paginado con filtros |
| P0 | `GET /api/control/pestudios/{id}` | Detalle con relaciones |
| P0 | `GET /api/control/pestudios/filters` | Opciones de filtros y formularios |
| P1 | `POST /api/control/pestudios` | Crear con validación completa |
| P1 | `PUT /api/control/pestudios/{id}` | Actualizar con validación completa |
| P1 | `DELETE /api/control/pestudios/{id}` | Soft-delete con verificación de grados |

### Fase 2: Frontend NextJS

| Prioridad | Página/Componente | Descripción |
|-----------|------------------|-------------|
| P0 | `usePestudios` | Hook: listado, paginación, búsqueda |
| P0 | `PestudioTable` | DataTable con ordenación |
| P1 | `PestudioForm` | Formulario con 3 secciones (General, Config, Fechas) |
| P1 | `ControlPestudiosPage` | Página principal |
| P1 | `PestudioCreatePage` | Página de creación |
| P1 | `PestudioEditPage` | Página de edición con QuickLinks |
| P2 | `PestudioDeleteButton` | Confirmación + eliminación |
| P2 | `PestudioFilters` | Filtros por programa educativo, estado activo |

### Fase 3: Migración de datos

| Tarea | Detalle |
|-------|---------|
| Normalizar ENUM('true','false') a boolean | 8 campos booleanos como strings → normalizar |
| Agregar UNIQUE INDEX en `code` | Garantizar unicidad de código |
| Validar integridad de `manager_id` | Verificar que referencias a users existan |
| Indexar `deleted_at` | Performance en soft-deletes |
| Agregar migración para campos fecha | `remision_resumen_final` y otros sin migración respaldada |

### Fase 4: Pruebas

| Tipo | Casos |
|------|-------|
| Unitarias | Validación de campos requeridos, ENUM a boolean |
| Integración | CRUD completo: crear → listar → editar → eliminar |
| Integración | Protección: eliminar con grados (debe fallar) |
| Integración | soft-delete + restore |
| Componente | Formulario: 3 secciones, loading states, errores inline |
| E2E | Flujo: login → listar → crear → verificar en tabla → editar → eliminar |

---

## 10. Dependencias y librerías

| Librería | Uso en el módulo |
|----------|------------------|
| jQuery + DataTables | Búsqueda, ordenación y paginación del lado del cliente |
| Bootstrap 4 | Layout, cards, tablas, formularios, modales, colores |
| FontAwesome 5 | Iconos en botones |
| Laravel Collective (Form) | `{!! Form::open() !!}`, `Form::select()`, `Form::text()`, `Form::textarea()` |
| Carbon | Manejo de fechas (modelo timestamps) |
| SweetAlert2 | Vía app.js global (confirmaciones) |

---

## 11. Estructura de la tabla (resumen visual)

| Columna | Tipo | Requerido | Default | Grupo |
|---------|------|-----------|---------|-------|
| `id` | INT UNSIGNED AUTO_INCREMENT | ✅ | — | ID |
| `peducativo_id` | INT UNSIGNED (FK) | ✅ | — | General |
| `manager_id` | INT UNSIGNED (FK, nullable) | ❌ | NULL | General |
| `code` | VARCHAR(255) | ✅ | — | General |
| `code_oficial` | VARCHAR(255) | ❌ | NULL | General |
| `name` | VARCHAR(255) | ✅ | — | General |
| `order` | INT | ✅ | — | General |
| `description` | VARCHAR(255) | ✅ | — | General |
| `description_aux` | VARCHAR(255) | ❌ | NULL | General |
| `mention` | VARCHAR(255) | ❌ | NULL | General |
| `status_build_promotion` | ENUM('true','false') | ✅ | — | Config |
| `title` | VARCHAR(255) | ❌ | NULL | General |
| `scale` | INT (FK escalas) | ✅ | — | Config |
| `profile` | TEXT | ❌ | NULL | General |
| `color` | VARCHAR(255) | ✅ | — | Config |
| `show_hr` | ENUM('true','false') | ✅ | — | Config |
| `status_a_cualitative` | ENUM('true','false') | ✅ | — | Config |
| `status_baremo` | ENUM('true','false') | ✅ | — | Config |
| `status_active` | ENUM('true','false') | ❌ | 'true' | Config |
| `status_carga_notas` | ENUM('true','false') | ❌ | 'false' | Config |
| `status_inscripcion_active` | TINYINT(1) | ❌ | 1 | Config |
| `show_quantitative_indicators` | ENUM('true','false') | ❌ | 'true' | Config |
| `status_socials` | TINYINT(1) | ❌ | 1 | Config |
| `remision_resumen_final` | DATE | ❌ | NULL | Fechas |
| `fecha_informe_final` | DATE | ❌ | NULL | Fechas |
| `fecha_certificacion` | DATE | ❌ | NULL | Fechas |
| `fecha_descriptivo` | DATE | ❌ | NULL | Fechas |
| `fecha_promocion` | DATE | ❌ | NULL | Fechas |
| `fecha_prosecucion` | DATE | ❌ | NULL | Fechas |
| `description_trainig_component` | VARCHAR(255) | ❌ | NULL | General |
| `planning_module` | TINYINT(1) | ❌ | 0 | Config |
| `activities_avr` | INT | ❌ | NULL | Config |
| `paper` | ENUM('oficial','letter') | ❌ | 'letter' | Config |
| `deleted_at` | TIMESTAMP NULL | ❌ | NULL | SoftDelete |

---

## 12. Métodos del modelo que deben migrarse a servicios

La migración a NextJS requiere extraer la lógica de negocio del modelo Pestudio en servicios separados:

| Método Original | Servicio Propuesto | Prioridad |
|----------------|-------------------|-----------|
| `scopeActive()` | Filtro en query | P0 |
| `getStatusDeleteAttribute()` | `PestudoValidator::canDelete(pestudio)` | P0 |
| `getFullNameAttribute()` | Computado en frontend: `` `${code} ${name}` `` | P0 |
| `getEscalaAttribute()` | `PestudioService::resolveEscala(pestudio)` | P1 |
| `getRgbColorAttribute()` | Utilidad CSS: mapa color→RGB | P1 |
| `getGradosActive()` | `GradoService::getActive(pestudioId)` | P1 |
| `getSeccions()` | `SeccionService::getByPestudio(pestudioId, lapsoId)` | P2 |
| `getProfesors()` / `getProfesorsCount()` | `ProfesorService::getByPestudio(pestudioId, lapsoId)` | P2 |
| `getPevaluacions()` / `getPevaluacionsPensums()` | `PevaluacionService::getByPestudio(pestudioId, lapsoId)` | P2 |
| `getEvaluacions()` | `EvaluacionService::getByPestudio(pestudioId, lapsoId)` | P2 |
| `getBoletins()` | `BoletinService::getByPestudio(pestudioId, lapsoId)` | P2 |
| `a_inscritos()` / `inscritos()` | `InscripcionService::countByPestudio(pestudioId)` | P2 |
| `getActivities()` / `getAvgActivitiesPerPlan()` | `ActivityService::getByPestudio(pestudioId, lapsoId)` | P3 |
| `getIndicadores()` | `IndicadorService::calculateAll()` | P3 |
| `getPorcAprobados()` / `getPromedio()` | `IndicadorService::calculateApprovalRate()` | P3 |
| `goal_notas_load()` / `real_notas_load()` | `CargaNotasService::getExpected()` / `getActual()` | P3 |
| `getProfesorGuia()` | `ProfesorGuiaService::getByPestudio(pestudioId, lapsoId)` | P3 |

---

## 13. Esquema de la respuesta de eliminación vía AJAX

```json
// Success (200)
{
  "messenge": "Registro Eliminado con Exito",
  "code": 200,
  "operation": true
}

// Error por grados existentes (no hay validación backend — solo en frontend)
{
  "messenge": "No se puede eliminar el plan de estudio",
  "code": 409,
  "operation": false
}
```

---

*Documento generado a partir del análisis del código fuente de SAEFL. Validado contra: `PestudioController.php`, `Pestudio.php` (modelo + 5 traits), migraciones en `database/migrations/backUps/pestudios/`, todas las vistas Blade del módulo, y rutas en `routes/app/tab/pestudio.php` + `routes/administracion/tab/pestudio.php`.*
