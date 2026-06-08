# Gestión de Grados (Control de Estudios) — Documento Técnico Completo

> **Versión:** 1.0 — Validada contra código fuente real (Laravel 8.x / PHP 8.2)
> **Módulo:** `control.configuraciones.grados` — CRUD de grados (años/niveles académicos).
> **Propósito:** Replicación en NextJS + API REST (Laravel o Node).
> **Arquitectura fuente:** Controlador tradicional (sin Livewire) con Blade + modales + jQuery DataTables.

---

## 1. Introducción

El módulo **Gestión de Grados** dentro del submódulo **Configuraciones** del **Control de Estudios** (`is_control`) administra los grados o años académicos que componen cada plan de estudio.

Un **Grado** define:
- **Plan de estudio al que pertenece**: `pestudio_id` (ej: "Media General")
- **Identificación**: nombre (ej: "1ER AÑO"), código, código reducido
- **Estado**: activo/inactivo
- **Horas comunitarias**: requeridas y totales
- **Orden**: posición de presentación

Cada Grado contiene múltiples **Secciones** (aulas) y está relacionado con **Pensums** (asignación de materias), **Profesores Guía**, **Inscripciones** de estudiantes, y datos estadísticos de rendimiento.

Arquitectura: **CRUD tradicional** con controlador + Blade. Las operaciones de crear/editar se realizan mediante **modales** embebidos en la página principal (no páginas separadas).

---

## 2. Arquitectura del Dominio

### 2.1 Cadena de dependencias

```
Pestudio (plan de estudio)
    └── Grado (grado/año académico) ←── MÓDULO ACTUAL
            ├── seccions[] → Seccion
            │     ├── inscripcions[] → Inscripcion (estudiantes)
            │     ├── pevaluacions[] → Pevaluacion (carga académica profesores)
            │     └── profesor_guias[] → ProfesorGuia
            ├── pensums[] → Pensum (asignatura + grado)
            │     └── pevaluacions[] → Pevaluacion
            ├── profesor_guias() → ProfesorGuia (directamente)
            ├── debates[] → Debate
            ├── community_actions[] → CommunityAction (horas comunitarias)
            ├── catchment_groups[] → CatchmentGroup (captación)
            │
            └── (referenciado por: Seccion, Pensum, Preinscripcion,
                 Inscripcion, Estudiant, Boletin, Evaluacion, Activity,
                 PollMain, Mailer, Pase, RegistroTitulo, Census,
                 Inicial/* models, etc.)
```

### 2.2 Árbol de archivos

```
routes/
  web.php                                              ← grupo /app/control con middleware is_control
  app/control.php                                      ← require __DIR__ . '/tab/grado.php'
  app/tab/grado.php                                    ← 7 rutas CRUD (activo)
  administracion/tab/grado.php                         ← 3 rutas (legacy, no cargado actualmente)

app/
  Http/
    Controllers/Administracion/Tab/Configuracion/
      GradoController.php                              ← 6 métodos, SIN form requests, SIN validación
  Models/
    app/
      Pescolar/
        Grado.php                                      ← modelo con SoftDeletes + 4 traits
        Functions/Grado/
          Lists.php                                    ← list_pestudio_grado_manage(), list_grado(), etc.
          Inscripcions.php                             ← inscritos(), varones(), hembras(), retirados(), etc.
          Preinscripcions.php                          ← preinscritos(), pre_varones(), pre_hembras()
          Indicators.php                               ← goal_notas_load(), real_notas_load()

resources/views/
  administracion/configuraciones/grados/
    index.blade.php                                    ← Página principal (listado + modales)
    create.blade.php                                   ← [NOTA: referencia a peducativos, no grados]
    edit.blade.php                                     ← Layout de edición (incluye partials.edit)
    card.blade.php                                     ← Tarjeta visual coloreada por grado
    table/index.blade.php                              ← DataTable + modales show/edit/delete
    form/fields.blade.php                              ← Campos del formulario (compartido)
    partials/create.blade.php                          ← Formulario POST store
    partials/edit.blade.php                            ← Formulario PUT update
    partials/details.blade.php                         ← Detalle solo-lectura
    partials/legenda.blade.php                         ← Leyenda de colores por grado
    menus/index.blade.php                              ← Botones: [+ Nuevo] [← Volver] [↺]
    menus/create.blade.php                             ← [NOTA: peducativos, no grados]
    menus/edit.blade.php                               ← [NOTA: peducativos, no grados]
    menus/show.blade.php                               ← Botones genéricos de usuarios
    menus/crud.blade.php                               ← [Pensums] [← Volver] [↺]
```

---

## 3. Validación contra código fuente

### 3.1 Hallazgos clave

| # | Tópico | Detalle |
|---|--------|---------|
| 1 | **Sin Livewire** | CRUD tradicional con Blade. Crear/Editar mediante modales en la misma página del listado. |
| 2 | **Sin validación server-side** | El controlador usa `Illuminate\Http\Request` directamente. **No hay** `$request->validate()`, ni Form Requests, ni reglas de validación. |
| 3 | **`is_control` middleware** | Grupo con `auth` + `is_control`. Ruta real: `/app/control/configuraciones/grados/index`. |
| 4 | **Modal-based CRUD** | A diferencia de Asignaturas y Pestudios (páginas separadas), Grados usa **modales** embebidos en `table/index.blade.php` para crear, editar y ver detalle. |
| 5 | **Dos sets de rutas** | `routes/app/tab/grado.php` (activo, plural `grados`). `routes/administracion/tab/grado.php` (legacy/inactivo, singular `grado`). |
| 6 | **9 campos fillable** | `pestudio_id, name, code, code_sm, description, status_active, hour_social, total_hour_social, order`. Relativamente pequeño. |
| 7 | **`order` sin migración respaldada** | El campo `order` está en `$fillable` pero no aparece en ninguna migración capturada. |
| 8 | **SoftDeletes activo** | El modelo usa el trait `SoftDeletes`. |
| 9 | **`status_delete` lógica personalizada** | Accessor `getStatusDeleteAttribute()` retorna `true` solo si `$this->seccions()->count() === 0`. |
| 10 | **Modelo con múltiples accesores de color** | `getColorAttribute()`, `getClassCardColorAttribute()`, `getClassTextColorAttribute()` — colores cíclicos basados en ID. |
| 11 | **Vistas inconsistentes** | `create.blade.php` y `edit.blade.php` en el directorio de grados tienen referencias a `peducativos` (programas educativos), lo que sugiere vistas mal ubicadas o copiadas. |
| 12 | **Sin paginación backend** | `Grado::all()` sin paginate(). DataTables maneja la paginación visual. |
| 13 | **ENUM('true','false') para `status_active`** | Patrón consistente con el resto del sistema. |
| 14 | **`code` y `code_sm` en migración original** | A diferencia de otras entidades, `code` y `code_sm` sí aparecen en la migración original `create_grados_table`. |

### 3.2 Validación de rutas

#### Rutas activas (módulo Control)

| Método | URI | Controlador | Middleware | Nombre |
|--------|-----|-------------|------------|--------|
| GET | `/configuraciones/grados` | `GradoController@index` | `auth`, `is_control` | `administracion.configuraciones.grados` |
| GET | `/configuraciones/grados/index` | `GradoController@index` | `auth`, `is_control` | `administracion.configuraciones.grados.index` |
| GET | `/configuraciones/grados/create` | `GradoController@create` | `auth`, `is_control` | `administracion.configuraciones.grados.create` |
| POST | `/configuraciones/grados/store` | `GradoController@store` | `auth`, `is_control` | `administracion.configuraciones.grados.store` |
| GET | `/configuraciones/grados/{id}` | `GradoController@edit` | `auth`, `is_control` | `administracion.configuraciones.grados.edit` |
| PUT | `/configuraciones/grados/{id}` | `GradoController@update` | `auth`, `is_control` | `administracion.configuraciones.grados.update` |
| DELETE | `/configuraciones/grados/destroy/{id}` | `GradoController@destroy` | `auth`, `is_control` | `administracion.configuraciones.grados.destroy` |

#### Rutas legacy (no cargadas actualmente)

| Método | URI | Controlador | Middleware | Nombre |
|--------|-----|-------------|------------|--------|
| GET | `/configuraciones/grado` | `GradoController@index` | `auth`, `is_control` | `administracion.configuraciones.grado` |
| GET | `/configuraciones/grado/{id}` | `GradoController@edit` | `auth`, `is_control` | `administracion.configuraciones.grado.edit` |
| PUT | `/configuraciones/grado/{id}` | `GradoController@update` | `auth`, `is_control` | `administracion.configuraciones.grado.update` |

---

## 4. Lógica de Negocio — Especificación Completa

### 4.1 Reglas de negocio

**RN-01: Grado pertenece a un Plan de Estudio.**
`pestudio_id` es requerido y FK a `pestudios`. Define el contexto educativo del grado.

**RN-02: Protección contra eliminación.**
Un Grado solo se puede eliminar si NO tiene secciones asociadas. El accessor `getStatusDeleteAttribute()` evalúa `$this->seccions()->count() === 0`. El controlador verifica esto antes de eliminar.

**RN-03: Eliminación lógica (soft-delete).**
`SoftDeletes` activo. `$grado->delete()` marca `deleted_at`. Las consultas en traits frecuentemente filtran `whereNull('grados.deleted_at')`.

**RN-04: Color cíclico dinámico.**
El color del grado se calcula en base a `$this->id` usando arrays fijos de colores Bootstrap. No se almacena en DB. Rota entre ~10 colores (indigo, purple, pink, red, orange, yellow, green, teal, cyan, gray).

**RN-05: Horas comunitarias.**
`hour_social` (requeridas) y `total_hour_social` (totales) son TINYINT nullable con default 60. Se usan en el módulo de Acción Social.

**RN-06: Estado activo.**
`status_active` (ENUM 'true'/'false') controla visibilidad en selectores. El `scopeActive()` filtra por este campo.

**RN-07: Orden de presentación.**
`order` define la posición del grado en listados. No hay UNIQUE constraint — múltiples grados pueden tener el mismo orden.

**RN-08: Código reducido.**
`code_sm` es una abreviatura corta del grado (ej: "1A" para "1ER AÑO").

**RN-09: Sin validación de unicidad.**
No hay UNIQUE INDEX ni en `code` ni en `code_sm` ni en `name`. El sistema no previene duplicados a nivel BD.

**RN-10: Modal-based CRUD.**
Todas las operaciones (crear, editar, ver detalle, eliminar) se realizan mediante modales Bootstrap en la misma página, usando DataTables. Sin navegación a páginas separadas.

### 4.2 Flujo de datos completo

```
[Usuario autenticado con rol is_control]
    │
    ├─(1) GET /app/control/configuraciones/grados
    │     └─ GradoController@index()
    │           ├─ Grado::all()  ← sin orden, sin paginación
    │           ├─ Pestudio activos formateados code_name
    │           ├─ COLUMN_COMMENTS
    │           └─ view('...grados.index')
    │                 ├── table/index.blade.php (DataTable + modales)
    │                 └── menus/index.blade.php
    │
    ├─(2) Abrir modal "Nuevo Grado"
    │     └── partials/create.blade.php (embebido en table/index)
    │           ├── form/fields.blade.php
    │           │     ├─ pestudio_id (select)
    │           │     ├─ name (text)
    │           │     ├─ code (text)
    │           │     ├─ code_sm (text)
    │           │     ├─ description (text)
    │           │     ├─ hour_social (number)
    │           │     └─ status_active (select SI/NO)
    │           └── Botón "Guardar"
    │
    ├─(3) POST .../grados/store
    │     └─ GradoController@store(Request)
    │           ├─ (SIN VALIDACIÓN)
    │           ├─ Grado::create($request->all())
    │           ├─ Session::flash('create_ok', ...)
    │           └─ redirect → administracion.configuraciones.grados
    │
    ├─(4) Abrir modal "Editar Grado" (con datos)
    │     └── partials/edit.blade.php (embebido, Form::model)
    │           ├── form/fields.blade.php (precargado)
    │           └── Botón "Actualizar"
    │
    ├─(5) PUT .../grados/{id}
    │     └─ GradoController@update(Request, $id)
    │           ├─ (SIN VALIDACIÓN)
    │           ├─ findOrFail → fill → save
    │           └─ redirect → administracion.configuraciones.grados
    │
    └─(6) DELETE .../grados/destroy/{id}
          └─ GradoController@destroy($id, Request)
                ├─ Grado::findOrFail($id)
                ├─ Verifica $grado->status_delete (seccions count === 0)
                ├─ Si OK → delete()
                ├── Si AJAX → response()->json([...])
                └── redirect → administracion.configuraciones.grados
```

---

## 5. Esquemas de Base de Datos

### 5.1 Tabla `grados`

```sql
CREATE TABLE `grados` (
  `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pestudio_id`       INT UNSIGNED NOT NULL COMMENT 'Plan de estudio (FK pestudios)',
  `name`              VARCHAR(255) NOT NULL COMMENT 'Nombre del grado (ej: 1ER AÑO)',
  `code`              VARCHAR(255) NOT NULL COMMENT 'Código (ej: 1A)',
  `code_sm`           VARCHAR(255) NOT NULL COMMENT 'Código reducido',
  `description`       VARCHAR(255) NULL COMMENT 'Descripción',
  `status_active`     ENUM('true','false') NOT NULL COMMENT 'Estado activo/inactivo',
  `hour_social`       TINYINT UNSIGNED DEFAULT 60 NULL COMMENT 'Horas sociales requeridas',
  `total_hour_social` TINYINT UNSIGNED DEFAULT 60 NULL COMMENT 'Horas sociales totales',
  `order`             INT NOT NULL DEFAULT 0 COMMENT 'Orden de presentación',
  `deleted_at`        TIMESTAMP NULL,
  `created_at`        TIMESTAMP NULL,
  `updated_at`        TIMESTAMP NULL,

  INDEX `grados_pestudio_id_index` (`pestudio_id`),

  CONSTRAINT `grados_pestudio_id_foreign`
    FOREIGN KEY (`pestudio_id`) REFERENCES `pestudios`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.2 Tabla `seccions` (hija directa)

```sql
CREATE TABLE `seccions` (
  `id`                          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `grado_id`                    INT UNSIGNED NOT NULL,
  `name`                        VARCHAR(255) NOT NULL,
  `description`                 VARCHAR(255) NULL,
  `amount_student`              INT NULL,
  `observation`                 TEXT NULL,
  `comment_final`               TEXT NULL,
  `status_active`               ENUM('true','false') DEFAULT 'true',
  `status_inscription_affects`  TINYINT(1) DEFAULT 0,
  `deleted_at`                  TIMESTAMP NULL,
  `created_at`                  TIMESTAMP NULL,
  `updated_at`                  TIMESTAMP NULL,

  FOREIGN KEY (`grado_id`) REFERENCES `grados`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.3 Tabla `pensums` (relacionada)

```sql
CREATE TABLE `pensums` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `asignatura_id`   INT UNSIGNED NOT NULL,
  `grado_id`        INT UNSIGNED NOT NULL,
  `pestudio_id`     INT UNSIGNED NOT NULL,
  `status_active`   TINYINT(1) DEFAULT 1,
  `deleted_at`      TIMESTAMP NULL,
  `created_at`      TIMESTAMP NULL,
  `updated_at`      TIMESTAMP NULL,

  FOREIGN KEY (`grado_id`) REFERENCES `grados`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 6. API REST — Endpoints propuestos

### 6.1 `GET /api/control/grados`

Listado paginado de grados.

**Query params:** `search`, `pestudio_id`, `status_active`, `page`, `per_page`

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "pestudio": { "id": 2, "code": "MG", "name": "MEDIA GENERAL" },
      "name": "1ER AÑO",
      "code": "1A",
      "code_sm": "1A",
      "description": "Primer año de educación media general",
      "status_active": true,
      "hour_social": 60,
      "total_hour_social": 60,
      "order": 1,
      "color": "#4F46E5",
      "seccions_count": 4,
      "pensums_count": 8,
      "estudiantes_count": 120,
      "created_at": "2025-09-01T10:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 15,
    "total": 25
  }
}
```

### 6.2 `GET /api/control/grados/{id}`

Detalle del grado con relaciones.

```json
{
  "id": 1,
  "pestudio": { "id": 2, "code": "MG", "name": "MEDIA GENERAL" },
  "name": "1ER AÑO",
  "code": "1A",
  "code_sm": "1A",
  "description": "Primer año de educación media general",
  "status_active": true,
  "hour_social": 60,
  "total_hour_social": 60,
  "order": 1,
  "color": "#4F46E5",
  "seccions": [
    { "id": 1, "name": "A", "description": "Sección A", "amount_student": 30,
      "status_active": true }
  ],
  "pensums": [
    { "id": 10, "asignatura": { "id": 15, "code": "MT-01", "name": "MATEMÁTICA" } }
  ]
}
```

### 6.3 `POST /api/control/grados`

Crear nuevo grado.

**Validaciones propuestas (no existen en fuente original):**
```json
{
  "pestudio_id": "required|integer|exists:pestudios,id",
  "name": "required|string|max:255",
  "code": "required|string|max:10",
  "code_sm": "required|string|max:4",
  "description": "nullable|string|max:255",
  "status_active": "required|boolean",
  "hour_social": "nullable|integer|min:0|max:255",
  "total_hour_social": "nullable|integer|min:0|max:255",
  "order": "nullable|integer|min:0"
}
```

### 6.4 `PUT /api/control/grados/{id}`

Actualizar grado. Mismas validaciones que create.

### 6.5 `DELETE /api/control/grados/{id}`

Eliminación lógica. Retorna 409 si tiene secciones asociadas.

**Response 200:**
```json
{
  "message": "Grado eliminado correctamente.",
  "operation": true
}
```

**Error 409:**
```json
{
  "message": "No se puede eliminar: el grado tiene secciones asociadas.",
  "seccions_count": 4
}
```

### 6.6 `GET /api/control/grados/filters`

Datos para poblar filtros.

```json
{
  "pestudios": [
    { "id": 2, "code": "MG", "name": "MEDIA GENERAL" },
    { "id": 1, "code": "INI", "name": "EDUCACIÓN INICIAL" }
  ]
}
```

---

## 7. Especificación de Componentes (NextJS + Tailwind)

### 7.1 Página principal: `ControlGradosPage`

```
┌───────────────────────────────────────────────────────────────────────────┐
│  Control de Estudios > Configuraciones > Grados                          │
├───────────────────────────────────────────────────────────────────────────┤
│  [+ Nuevo Grado]                                                         │
├───────────────────────────────────────────────────────────────────────────┤
│  [Buscar grado...]                                                        │
├─────┬─────────────┬────────────┬────────┬──────────────────┬────────┬─────┤
│  #  │ Plan Est.   │ Código     │ Nombre │ Descripción      │Estado  │ Acc.│
├─────┼─────────────┼────────────┼────────┼──────────────────┼────────┼─────┤
│  1  │ MG           │ 1A        │1ER AÑO │Primer año media… │ ✅     │[👁][✏️][🗑]│
│  2  │ MG           │ 2A        │2DO AÑO │Segundo año…      │ ✅     │[👁][✏️][🗑]│
│  3  │ MG           │ 3A        │3ER AÑO │Tercer año…       │ ✅     │[👁][✏️][🗑]│
├─────┴─────────────┴────────────┴────────┴──────────────────┴────────┴─────┤
│  Showing 1 to 3 of 10 entries                     [1] [2]                │
└───────────────────────────────────────────────────────────────────────────┘

┌─ Modal: Nuevo Grado ──────────────────────────────────┐
│  Plan de Estudio:  [MEDIA GENERAL         ▼]          │
│  Nombre:           [1ER AÑO                        ]  │
│  Código:           [1A  ]  Código Reducido: [1A ]     │
│  Descripción:      [Primer año de educación...    ]   │
│  Horas Sociales:   [60  ]                               │
│  Orden:            [1   ]                               │
│  Estado:           [Activo ▼]                           │
│                                                         │
│  [████ Crear Grado ████]                                │
└─────────────────────────────────────────────────────────┘
```

### 7.2 Árbol de componentes

```
ControlGradosPage
├── PageHeader (título + breadcrumb)
├── ActionBar (botón: Nuevo Grado → abre modal)
├── GradoTable
│   ├── SearchInput
│   ├── TableHeader (pestudio, código, nombre, desc, estado)
│   └── GradoRow (× N)
│       ├── ShowButton → GradoDetailsModal
│       ├── EditButton → GradoEditModal
│       └── DeleteButton (deshabilitado si tiene secciones)
│           └── ConfirmDialog → destroy
├── CreateGradoModal
│   └── GradoForm (incluye pestudio select, nombre, code, code_sm, etc.)
├── EditGradoModal
│   └── GradoForm (precargado)
└── GradoDetailsModal
    └── Campos read-only en 2 columnas
```

### 7.3 Estados de cada componente

| Componente | Loading | Empty | Error | Success |
|-----------|---------|-------|-------|---------|
| `GradoTable` | Skeleton 5 filas | "No hay grados registrados" con ilustración | Toast error | DataTable |
| `GradoForm` (modal) | Spinner en submit | Campos vacíos (create) o precargados (edit) | Errores inline + toast | Modal cierra, tabla refresca |
| `GradoDetailsModal` | Spinner | N/A | Toast error | Datos en 2 columnas |
| `DeleteButton` | Spinner en botón | N/A | Toast "Tiene secciones asociadas" | Fila eliminada + refresh |
| `GradoEditModal` | Skeleton preview | N/A | Error al cargar datos | Formulario precargado |

---

## 8. Edge Cases y Validaciones

### 8.1 Validaciones del servidor

| Estado actual | Descripción | Riesgo |
|--------------|-------------|--------|
| ❌ Sin validación | `$request->all()` → `create()` | Datos corruptos, SQL injection potencial |
| ❌ Sin Form Requests | No existen clases de validación | Cero validación server-side |
| ✅ SoftDeletes activo | Eliminaciones marcan `deleted_at` | OK |
| ⚠️ `status_delete` | Solo verifica secciones, no pensums, profesor_guias, ni debates | Puede dejar registros huérfanos |

### 8.2 Edge cases

| Caso | Comportamiento esperado |
|------|------------------------|
| Grado con secciones → eliminar | Error: "No se puede eliminar" (verifica solo secciones) |
| Grado con debates activos | El soft-delete no verifica debates — el grado se marca como eliminado pero los debates referencian `deleted` grado |
| `status_active` = 'false' | Grado invisible en selectores (scopeActive) |
| `hour_social` = null | Se muestra como 0; las funcionalidades de acción social se ven afectadas |
| Color cíclico | El color es dinámico (no almacenado). Al cambiar ID, cambia el color visual |
| Código duplicado | La BD lo permite — no hay UNIQUE INDEX |
| `order` = 0 para todos | No hay ordenación por `order` en el listado default (usa `all()`) |

---

## 9. Plan de Migración: Laravel/Blade → NextJS + API

### Fase 1: Backend API

| Prioridad | Endpoint | Descripción |
|-----------|----------|-------------|
| P0 | `GET /api/control/grados` | Listado paginado con filtros |
| P0 | `GET /api/control/grados/{id}` | Detalle con relaciones |
| P0 | `GET /api/control/grados/filters` | Opciones de filtros |
| P1 | `POST /api/control/grados` | Crear con validación completa |
| P1 | `PUT /api/control/grados/{id}` | Actualizar con validación |
| P1 | `DELETE /api/control/grados/{id}` | Soft-delete con verificación |

### Fase 2: Frontend NextJS

| Prioridad | Componente | Descripción |
|-----------|-----------|-------------|
| P0 | `useGrados` | Hook: listado, paginación, búsqueda |
| P0 | `GradoTable` | DataTable client-side |
| P1 | `GradoForm` | Formulario reutilizable (create/edit modal) |
| P1 | `CreateGradoModal` | Modal de creación |
| P1 | `EditGradoModal` | Modal de edición |
| P2 | `GradoDeleteButton` | Confirmación + eliminación |

### Fase 3: Migración de datos

| Tarea | Detalle |
|-------|---------|
| Normalizar ENUM('true','false') a boolean | `status_active` a `boolean` |
| Verificar integridad de `pestudio_id` | Todos los grados referencian pestudios existentes |
| Indexar `deleted_at` | Performance en soft-deletes |
| Agregar migración para `order` | Si no existe, agregar columna `order` |

### Fase 4: Pruebas

| Tipo | Casos |
|------|-------|
| Unitarias | Validación de campos requeridos |
| Integración | CRUD: crear → listar → editar → eliminar |
| Integración | Protección: eliminar grado con secciones (debe fallar) |
| Integración | Soft-delete + restore |
| Componente | Modal: apertura, cierre, validación inline |
| E2E | Flujo: crear grado → verificar en tabla → editar valor → eliminar |

---

## 10. Dependencias y librerías

| Librería | Uso en el módulo |
|----------|------------------|
| jQuery + DataTables | Búsqueda, ordenación, paginación |
| Bootstrap 4 | Modales, cards, tablas, formularios |
| FontAwesome 5 | Iconos en acciones y botones |
| Laravel Collective (Form) | `Form::open()`, `Form::model()`, `Form::select()` |
| SweetAlert2 | Vía app.js global |
| `js/models/default/destroy.js` | Script centralizado de eliminación AJAX |

---

## 11. Estructura de la tabla (resumen visual)

| Columna | Tipo | Requerido | Único | Default |
|---------|------|-----------|-------|---------|
| `id` | INT UNSIGNED AUTO_INCREMENT | ✅ | ✅ | — |
| `pestudio_id` | INT UNSIGNED (FK) | ✅ | ❌ | — |
| `name` | VARCHAR(255) | ✅ | ❌ | — |
| `code` | VARCHAR(255) | ✅ | ❌ | — |
| `code_sm` | VARCHAR(255) | ✅ | ❌ | — |
| `description` | VARCHAR(255) | ❌ | ❌ | NULL |
| `status_active` | ENUM('true','false') | ✅ | ❌ | — |
| `hour_social` | TINYINT UNSIGNED | ❌ | ❌ | 60 |
| `total_hour_social` | TINYINT UNSIGNED | ❌ | ❌ | 60 |
| `order` | INT | ✅ | ❌ | 0 |
| `deleted_at` | TIMESTAMP NULL | ❌ | ❌ | NULL |

---

## 12. Comparativa con módulos previos

| Aspecto | Asignaturas | Pestudios | **Grados** |
|---------|-------------|-----------|------------|
| Livewire | ❌ | ❌ | ❌ |
| Validación | ⚠️ Mínima | ❌ Ninguna | ❌ Ninguna |
| Form Requests | ✅ 2 clases | ❌ 0 | ❌ 0 |
| SoftDeletes | ⚠️ Comentado | ✅ Activo | ✅ Activo |
| Modelo (líneas) | ~150 + 1 trait | ~859 + 5 traits | ~300 + 4 traits |
| Fillable | ~20 | ~33 | 9 |
| CRUD UI | Páginas separadas | Páginas separadas | **Modales** |
| `status_delete` check | Sobre pensums | Sobre grados | Sobre **secciones** |
| Dos sets de rutas | ❌ | ✅ | ✅ (1 legacy) |

---

*Documento generado a partir del análisis del código fuente de SAEFL. Validado contra: `GradoController.php`, `Grado.php` (modelo + 4 traits), migraciones en `database/migrations/backUps/`, todas las vistas Blade del módulo, y rutas en `routes/app/tab/grado.php` + `routes/administracion/tab/grado.php`.*
