# Gestión de Secciones (Control de Estudios) — Documento Técnico Completo

> **Versión:** 1.0 — Validada contra código fuente real (Laravel 8.x / PHP 8.2)
> **Módulo:** `control.configuraciones.seccions` — CRUD de secciones (aulas/grupos).
> **Propósito:** Replicación en NextJS + API REST (Laravel o Node).
> **Arquitectura fuente:** Controlador tradicional (sin Livewire) con Blade + modales + jQuery DataTables.

---

## 1. Introducción

El módulo **Gestión de Secciones** dentro del submódulo **Configuraciones** del **Control de Estudios** (`is_control`) administra las secciones (aulas) que pertenecen a cada grado académico.

Una **Sección** define:
- **Grado al que pertenece**: `grado_id` → Grado → Pestudio
- **Nombre**: un solo carácter (ej: "A", "B", "C")
- **Capacidad**: `amount_student` (1-50)
- **Estado**: activo/inactivo
- **Flag de inscripción**: `status_inscription_affects` — si contabiliza para métricas de pago/inscripción
- **Observaciones**: texto libre y comentario final para resumen

Cada Sección contiene **Inscripciones** (estudiantes), **Pevaluaciones** (carga académica de profesores), **Profesores Guía** y **Prosecuciones**.

Arquitectura: CRUD tradicional con **modales embebidos** en la página principal (crear, editar, detalle, eliminar desde DataTable).

---

## 2. Arquitectura del Dominio

### 2.1 Cadena de dependencias

```
Pestudio (plan de estudio)
    └── Grado (año académico)
            └── Seccion (aula/grupo) ←── MÓDULO ACTUAL
                    ├── inscripcions[] → Inscripcion (estudiantes matriculados)
                    ├── pevaluacions[] → Pevaluacion (carga académica profesores)
                    ├── profesor_guias[] → ProfesorGuia (docentes guía)
                    ├── prosecucions[] → Prosecucion (promoción)
                    │
                    └── (referenciado por: Inscripcion, Pevaluacion, ProfesorGuia,
                         Prosecucion, Estudiant, Representant, Boletin,
                         Evaluacion, Activity, RegistroTitulo, GrupoEstable,
                         Pase, IncidentOld, etc.)
```

### 2.2 Árbol de archivos

```
routes/
  web.php                                              ← grupo /app/control (is_control)
  app/control.php                                      ← require __DIR__ . '/tab/seccion.php'
  app/tab/seccion.php                                  ← 7 rutas CRUD completas
  app/common.php                                       ← requiere administracion/tab/seccion.php
  administracion/tab/seccion.php                       ← 3 rutas (solo index + edit + update, is_common)
  app/ajax.php                                         ← GET gradoByseccion/{id} → retorna secciones por grado
  administracion/tab/ajax.php                          ← GET gradoByseccion/{id} (duplicado)

app/
  Http/
    Controllers/Administracion/Tab/Configuracion/
      SeccionController.php                            ← 6 métodos, SIN validación, SIN Form Requests
  Models/
    app/
      Pescolar/
        Seccion.php                                    ← modelo SIN SoftDeletes, 8 relaciones, 1 trait
        Functions/Seccion/
          Lists.php                                    ← 1 método: list_seccion_grado()

resources/views/
  administracion/configuraciones/seccions/
    index.blade.php                                    ← Página principal (listado + modales)
    edit.blade.php                                     ← [STALE: copia del módulo Banco, no usada]
    card.blade.php                                     ← Tarjeta visual individual
    form/fields.blade.php                              ← 8 campos del formulario
    partials/create.blade.php                          ← Form POST → store
    partials/edit.blade.php                            ← Form PUT → update
    partials/details.blade.php                         ← Detalle solo-lectura
    table/index.blade.php                              ← DataTable + modales show/edit/delete
    menus/index.blade.php                              ← [+ Nueva Sección] [←] [↺]
    menus/create.blade.php                             ← [Listado] [Registrar] [←] [↺]
    menus/edit.blade.php                               ← [Listado] [Registrar] [←] [↺]
    menus/crud.blade.php                               ← [Pensums] [←] [↺]
    menus/show.blade.php                               ← [Nuevo Usuario] [Listados] [←] [↺]
```

---

## 3. Validación contra código fuente

### 3.1 Hallazgos clave

| # | Tópico | Detalle |
|---|--------|---------|
| 1 | **Sin Livewire** | CRUD tradicional con Blade. Crear/Editar/Detalle mediante **modales Bootstrap** en la misma página. |
| 2 | **Sin validación server-side** | `SeccionController::store()` y `update()` usan `$request->all()` directamente. **Sin** `$request->validate()`, sin Form Requests. |
| 3 | **Dos middlewares** | `is_control` (CRUD completo en `/app/control/...`) y `is_common` (solo index+edit en `/app/common/...`). |
| 4 | **SoftDeletes en migración pero NO en modelo** | La tabla tiene `deleted_at` (softDeletes en migración), pero el modelo **no importa** `SoftDeletes`. El método `delete()` ejecuta **borrado físico**. |
| 5 | **`status_delete` verifica 2 condiciones** | A diferencia de Grados (solo secciones), Sección verifica que NO tenga inscripciones Y NO tenga profesores guía. |
| 6 | **`name` es VARCHAR(1)** | Un solo carácter — "A", "B", "C", etc. |
| 7 | **`create.blade.php` no existe** | La ruta `seccions.create` apunta a `create.blade.php` que NO existe en el directorio. La creación se maneja via modal en `table/index.blade.php`. |
| 8 | **`edit.blade.php` es stale** | El archivo `edit.blade.php` en el directorio contiene código del módulo **Banco** (`$banco`), no de Secciones. No es usado. |
| 9 | **Dos endpoints AJAX duplicados** | `routes/app/ajax.php` y `routes/administracion/tab/ajax.php` definen el mismo endpoint `gradoByseccion/{id}` con nombres de ruta distintos. |
| 10 | **`status_inscription_affects`** | ENUM('true','false') que controla si la sección cuenta para métricas de inscripción/pago. Usado extensivamente en consultas financieras. |
| 11 | **Trait `Lists` minimal pero crítico** | Un solo método `list_seccion_grado()` usado en **30+ Livewire components** para poblar selects dinámicos. |
| 12 | **Sin paginación backend** | `Seccion::all()` sin paginate(). DataTables maneja paginación visual. |
| 13 | **`amount_student` como select 1-50** | En el formulario se renderiza como `Form::selectRange('amount_student', 1, 50)`, no como input numérico libre. |

### 3.2 Validación de rutas

#### Rutas CRUD (Control — `is_control`)

| Método | URI | Controlador | Middleware | Nombre |
|--------|-----|-------------|------------|--------|
| GET | `/configuraciones/seccions` | `SeccionController@index` | `auth`, `is_control` | `administracion.configuraciones.seccions` |
| GET | `/configuraciones/seccions/index` | `SeccionController@index` | `auth`, `is_control` | `administracion.configuraciones.seccions.index` |
| GET | `/configuraciones/seccions/create` | `SeccionController@create` | `auth`, `is_control` | `administracion.configuraciones.seccions.create` |
| POST | `/configuraciones/seccions/store` | `SeccionController@store` | `auth`, `is_control` | `administracion.configuraciones.seccions.store` |
| GET | `/configuraciones/seccions/{id}` | `SeccionController@edit` | `auth`, `is_control` | `administracion.configuraciones.seccions.edit` |
| PUT | `/configuraciones/seccions/{id}` | `SeccionController@update` | `auth`, `is_control` | `administracion.configuraciones.seccions.update` |
| DELETE | `/configuraciones/seccions/destroy/{id}` | `SeccionController@destroy` | `auth`, `is_control` | `administracion.configuraciones.seccions.destroy` |

#### Rutas comunes (`is_common`)

| Método | URI | Middleware | Nombre |
|--------|-----|------------|--------|
| GET | `/configuraciones/seccion` | `auth`, `is_common` | `administracion.configuraciones.seccion` |
| GET | `/configuraciones/seccion/{id}` | `auth`, `is_common` | `administracion.configuraciones.seccion.edit` |
| PUT | `/configuraciones/seccion/{id}` | `auth`, `is_common` | `administracion.configuraciones.seccion.update` |

#### Rutas AJAX

| Método | URI | Controlador | Nombre |
|--------|-----|-------------|--------|
| GET | `/ajax/select/gradoByseccion/{id}` | `Common\Ajax\FillSelectController@gradoByseccion` | `ajax.fill.gradoByseccion` |
| GET | `/ajax/select/gradoByseccion/{id}` | `Ajax\FillSelectController@gradoByseccion` | `administracion.ajax.fill.gradoByseccion` |

**Ambos endpoints retornan:**
```php
Seccion::where('grado_id', $id)->where('status_active', 'true')->get()
```

---

## 4. Lógica de Negocio — Especificación Completa

### 4.1 Reglas de negocio

**RN-01: Sección pertenece a un Grado.**
`grado_id` es requerido y FK a `grados`. Define el año/nivel educativo de la sección.

**RN-02: Nombre de un solo carácter.**
`name` es VARCHAR(1). Los nombres típicos son letras: "A", "B", "C", "D". Cada grado puede tener múltiples secciones.

**RN-03: Protección contra eliminación.**
Una Sección solo se puede eliminar si NO tiene inscripciones activas Y NO tiene profesores guía asignados. El accessor `getStatusDeleteAttribute()` evalúa ambas condiciones.

**RN-04: Borrado físico (NO soft-delete).**
A pesar de que la migración incluye `softDeletes()`, el modelo **no importa** el trait `SoftDeletes`. El método `delete()` del controlador elimina el registro permanentemente de la base de datos. La columna `deleted_at` existe pero nunca se puebla.

**RN-05: Flag de inscripción.**
`status_inscription_affects` (ENUM 'true'/'false') determina si los estudiantes de esta sección cuentan para métricas de inscripción, pagos y reportes financieros. Es usado extensivamente en consultas de `Inscripcion`, `Administrativa`, `Enrollment` y `Representant`.

**RN-06: Capacidad de estudiantes.**
`amount_student` es un entero con default 40, limitado a 1-50 en el formulario. Representa la capacidad máxima de la sección.

**RN-07: Estado activo.**
`status_active` (ENUM 'true'/'false') controla visibilidad en selectores. El `scopeActive()` filtra por este campo.

**RN-08: Selector dinámico por grado.**
El método `list_seccion_grado($grado_id)` es el mecanismo estándar para poblar selects de secciones filtrados por grado. Usado en 30+ Livewire components.

**RN-09: CRUD modal.**
A diferencia de páginas separadas, Secciones usa modales Bootstrap para crear, editar y ver detalle. El botón "Nuevo" dispara el modal `modal_edit_create`.

**RN-10: Sin create.blade.php.**
La ruta `GET seccions/create` apunta a `SeccionController@create()` que retorna `view('...seccions.create')`, pero el archivo `create.blade.php` **no existe**. En la práctica, la creación se maneja via modal en el `index.blade.php`.

### 4.2 Flujo de datos completo

```
[Usuario autenticado con rol is_control o is_common]
    │
    ├─(1) GET /app/control/configuraciones/seccions
    │     └─ SeccionController@index()
    │           ├─ Seccion::all()  ← sin orden, sin paginación
    │           ├─ Grado::list_pestudio_grado()  → $list_grado
    │           ├─ Grado::list_pestudio_grado_all() → $list_grados
    │           ├─ COLUMN_COMMENTS
    │           └─ view('...seccions.index')
    │                 ├── table/index.blade.php (DataTable + 3 modales)
    │                 │     ├── Modal: Nueva Sección (partials/create)
    │                 │     ├── Modal: Editar Sección (partials/edit)
    │                 │     └── Modal: Detalle Sección (partials/details)
    │                 └── menus/index.blade.php
    │
    ├─(2) POST .../seccions/store
    │     └─ SeccionController@store(Request)
    │           ├─ (SIN VALIDACIÓN)
    │           ├─ Seccion::create($request->all())
    │           ├─ Session::flash('operp_ok', trans('db_oper_result.create_ok'))
    │           └─ redirect → administracion.configuraciones.seccions
    │
    ├─(3) PUT .../seccions/{id}
    │     └─ SeccionController@update(Request, $id)
    │           ├─ (SIN VALIDACIÓN)
    │           ├─ findOrFail → fill → save
    │           └─ redirect → administracion.configuraciones.seccions
    │
    └─(4) DELETE .../seccions/destroy/{id}
          └─ SeccionController@destroy($id, Request)
                ├─ Seccion::findOrFail($id)
                ├─ Verifica $seccion->status_delete (inscripciones vacías AND profesor_guias vacío)
                ├─ Si OK → delete()  ← BORRADO FÍSICO
                ├── Si AJAX → response()->json([...])
                └── redirect → administracion.configuraciones.seccions
```

---

## 5. Esquemas de Base de Datos

### 5.1 Tabla `seccions`

```sql
CREATE TABLE `seccions` (
  `id`                         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `grado_id`                   INT UNSIGNED NOT NULL COMMENT 'Grado (FK grados)',
  `name`                       VARCHAR(1) NOT NULL COMMENT 'Nombre (1 carácter: A, B, C...)',
  `description`                VARCHAR(255) NULL COMMENT 'Descripción',
  `amount_student`             INT DEFAULT 40 COMMENT 'Capacidad de estudiantes',
  `observation`                VARCHAR(255) NULL COMMENT 'Observaciones',
  `status_active`              ENUM('true','false') NOT NULL COMMENT 'Activo/inactivo',
  `comment_final`              TEXT NULL COMMENT 'Observaciones resumen final',
  `status_inscription_affects` ENUM('true','false') DEFAULT 'true' COMMENT 'Contabiliza inscripción',
  `deleted_at`                 TIMESTAMP NULL,
  `created_at`                 TIMESTAMP NULL,
  `updated_at`                 TIMESTAMP NULL,

  INDEX `seccions_grado_id_index` (`grado_id`),

  CONSTRAINT `seccions_grado_id_foreign`
    FOREIGN KEY (`grado_id`) REFERENCES `grados`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.2 Tablas hijas

```sql
CREATE TABLE `inscripcions` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `seccion_id`  INT UNSIGNED NOT NULL,
  `estudiant_id` INT UNSIGNED NOT NULL,
  `status`      ENUM('true','false') DEFAULT 'true',
  `deleted_at`  TIMESTAMP NULL,
  FOREIGN KEY (`seccion_id`) REFERENCES `seccions`(`id`)
) ENGINE=InnoDB;

CREATE TABLE `pevaluacions` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `seccion_id`  INT UNSIGNED NOT NULL,
  `profesor_id` INT UNSIGNED NOT NULL,
  `pensum_id`   INT UNSIGNED NOT NULL,
  FOREIGN KEY (`seccion_id`) REFERENCES `seccions`(`id`)
) ENGINE=InnoDB;

CREATE TABLE `profesor_guias` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `seccion_id`   INT UNSIGNED NOT NULL,
  `profesor_id`  INT UNSIGNED NOT NULL,
  FOREIGN KEY (`seccion_id`) REFERENCES `seccions`(`id`)
) ENGINE=InnoDB;
```

### 5.3 Migraciones

| Archivo | Campo agregado |
|---------|---------------|
| `2019_08_24_154135_create_seccions_table` | Creación: id, grado_id, name(1), description, amount_student(40), observation, status_active, softDeletes, timestamps |
| `2021_07_13_220951_add_comment_final_to_seccions` | `comment_final` TEXT nullable after observation |
| `2024_01_12_095249_add_status_inscription_affects_to_seccions` | `status_inscription_affects` ENUM('true','false') default 'true' after status_active |

---

## 6. API REST — Endpoints propuestos

### 6.1 `GET /api/control/seccions`

Listado paginado de secciones.

**Query params:** `search`, `grado_id`, `status_active`, `page`, `per_page`

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "grado": {
        "id": 1,
        "name": "1ER AÑO",
        "pestudio": { "id": 2, "code": "MG", "name": "MEDIA GENERAL" }
      },
      "name": "A",
      "description": "Sección A - Primer Año",
      "amount_student": 40,
      "observation": null,
      "comment_final": null,
      "status_active": true,
      "status_inscription_affects": true,
      "inscritos_count": 35,
      "profesor_guias_count": 1,
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

### 6.2 `GET /api/control/seccions/{id}`

Detalle con relaciones.

```json
{
  "id": 1,
  "grado": {
    "id": 1,
    "name": "1ER AÑO",
    "pestudio": { "id": 2, "code": "MG", "name": "MEDIA GENERAL" }
  },
  "name": "A",
  "description": "Sección A - Primer Año",
  "amount_student": 40,
  "observation": "Grupo numeroso",
  "comment_final": "Buen rendimiento general",
  "status_active": true,
  "status_inscription_affects": true,
  "inscripcions": [ ... ],
  "profesor_guias": [ ... ]
}
```

### 6.3 `POST /api/control/seccions`

Crear nueva sección.

**Validaciones propuestas (no existen en fuente original):**
```json
{
  "grado_id": "required|integer|exists:grados,id",
  "name": "required|string|max:1",
  "description": "nullable|string|max:255",
  "amount_student": "nullable|integer|min:1|max:50",
  "observation": "nullable|string|max:255",
  "comment_final": "nullable|string",
  "status_active": "required|boolean",
  "status_inscription_affects": "boolean"
}
```

### 6.4 `PUT /api/control/seccions/{id}`

Actualizar sección. Mismas validaciones.

### 6.5 `DELETE /api/control/seccions/{id}`

**Borrado físico.** Retorna 409 si tiene inscripciones o profesores guía.

**Response 200:**
```json
{
  "message": "Sección eliminada correctamente.",
  "operation": true
}
```

**Error 409:**
```json
{
  "message": "No se puede eliminar: la sección tiene inscripciones o profesores guía.",
  "inscripcions_count": 35,
  "profesor_guias_count": 1
}
```

### 6.6 `GET /api/control/seccions/by-grado/{gradoId}`

Endpoints para selector dinámico (reemplaza la ruta AJAX actual).

**Response:**
```json
[
  { "id": 1, "name": "A", "full_name": "1ER AÑO Secc. A" },
  { "id": 2, "name": "B", "full_name": "1ER AÑO Secc. B" }
]
```

### 6.7 `GET /api/control/seccions/filters`

Datos para poblar filtros.

```json
{
  "grados": [
    {
      "id": 1,
      "name": "1ER AÑO",
      "pestudio_name": "MEDIA GENERAL"
    }
  ]
}
```

---

## 7. Especificación de Componentes (NextJS + Tailwind)

### 7.1 Página principal: `ControlSeccionsPage`

```
┌───────────────────────────────────────────────────────────────────────────┐
│  Control de Estudios > Configuraciones > Secciones                       │
├───────────────────────────────────────────────────────────────────────────┤
│  [+ Nueva Sección]  [Filtrar por Grado: Todos ▼]                         │
├───────────────────────────────────────────────────────────────────────────┤
│  [Buscar sección...]                                                      │
├─────┬─────────────┬──────────┬────────┬──────────┬──────────┬──────┬──────┤
│  #  │ Plan Est.   │ Grado    │ Secc.  │ Estud.   │ Inscrip. │Est.  │ Acc. │
├─────┼─────────────┼──────────┼────────┼──────────┼──────────┼──────┼──────┤
│  1  │ MEDIA GNRL   │ 1ER AÑO │ A      │ 40       │ ✅ Sí    │ ✅   │[👁][✏️][🗑]│
│  2  │ MEDIA GNRL   │ 1ER AÑO │ B      │ 38       │ ✅ Sí    │ ✅   │[👁][✏️][🗑]│
│  3  │ MEDIA GNRL   │ 2DO AÑO │ A      │ 35       │ ✅ Sí    │ ✅   │[👁][✏️][🗑]│
├─────┴─────────────┴──────────┴────────┴──────────┴──────────┴──────┴──────┤
│  Showing 1 to 3 of 10 entries                     [1] [2]                │
└───────────────────────────────────────────────────────────────────────────┘

┌─ Modal: Nueva Sección ────────────────────────────┐
│  Grado:         [1ER AÑO ▼]                        │
│  Nombre (1 car):[A                               ]  │
│  Descripción:   [Sección A - Primer...            ]  │
│  Capacidad:     [40 ▼]  (select 1-50)              │
│  Observaciones: [________________________]          │
│  Comentario Final: [textarea...]                    │
│  Estado:        [Activo ▼]                          │
│  Contabiliza Inscripción: [Sí ▼]                    │
│                                                     │
│  [████ Crear Sección ████]                          │
└─────────────────────────────────────────────────────┘
```

### 7.2 Árbol de componentes

```
ControlSeccionsPage
├── PageHeader (título + breadcrumb)
├── FilterBar
│   ├── GradoFilter (select → filtra secciones por grado)
│   └── NewButton (+ Nueva Sección → modal)
├── SeccionTable
│   ├── SearchInput
│   ├── TableHeader (grado, nombre, capacidad, estado, inscripción)
│   └── SeccionRow (× N)
│       ├── ShowButton → SeccionDetailsModal
│       ├── EditButton → SeccionEditModal
│       └── DeleteButton (deshabilitado si tiene inscripciones o profesores guía)
│           └── ConfirmDialog → destroy
├── CreateSeccionModal
│   └── SeccionForm
│       ├── GradoSelect (carga grados disponibles)
│       ├── NameInput (single char, maxLength=1)
│       ├── AmountStudentSelect (1-50)
│       ├── DescriptionInput
│       ├── ObservationInput
│       ├── CommentFinalTextarea
│       ├── StatusSelect (activo/inactivo)
│       └── InscriptionAffectsToggle
├── EditSeccionModal
│   └── SeccionForm (precargado)
└── SeccionDetailsModal
    └── ReadOnlyFields (2 columnas)
```

### 7.3 Estados de cada componente

| Componente | Loading | Empty | Error | Success |
|-----------|---------|-------|-------|---------|
| `SeccionTable` | Skeleton 5 filas | "No hay secciones registradas" | Toast error | DataTable |
| `SeccionForm` (modal) | Spinner en submit | Campos vacíos (create) o precargados (edit) | Errores inline (name max 1 char, grado requerido) | Modal cierra, tabla refresca |
| `SeccionDetailsModal` | Spinner | N/A | Toast error | Datos en 2 columnas |
| `DeleteButton` | Spinner en botón | N/A | Toast "Tiene inscripciones o profesores guía" | Fila eliminada |
| `GradoFilter` | Spinner | "Sin grados disponibles" | Error al cargar grados | Select poblado |

---

## 8. Edge Cases y Validaciones

### 8.1 Validaciones del servidor

| Estado actual | Descripción | Riesgo |
|--------------|-------------|--------|
| ❌ Sin validación | `$request->all()` → `create()` | Datos corruptos |
| ❌ Sin Form Requests | No existen clases de validación | Cero validación |
| ⚠️ Borrado físico | A pesar de `softDeletes()` en migración, el modelo no importa `SoftDeletes` | Pérdida irreversible de datos |

### 8.2 Edge cases

| Caso | Comportamiento esperado |
|------|------------------------|
| Sección con inscripciones → eliminar | Error: "Tiene inscripciones o profesores guía" |
| Sección con profesores guía → eliminar | Mismo error (status_delete chequea ambas) |
| Sección sin inscripciones ni profesores → eliminar | Borrado físico — no hay `deleted_at` |
| `name` de más de 1 carácter | La BD lo trunca silenciosamente (VARCHAR(1)) |
| `status_inscription_affects` = 'false' | Estudiantes de esta sección NO cuentan en métricas financieras |
| `amount_student` = 0 | El formulario select comienza en 1, pero podría inyectarse 0 vía API |
| Grado eliminado (soft-delete) → secciones hijas | ON DELETE CASCADE elimina las secciones |

---

## 9. Comparativa: Secciones en CRUD modal vs páginas separadas

| Aspecto | Asignaturas | Pestudios | Grados | **Secciones** |
|---------|-------------|-----------|--------|---------------|
| UI | Páginas separadas | Páginas separadas | Modales | **Modales** |
| Livewire | ❌ | ❌ | ❌ | ❌ |
| Validación | ⚠️ Mínima | ❌ | ❌ | ❌ |
| SoftDeletes | ⚠️ Comentado | ✅ Activo | ✅ Activo | **❌ No activo** (borrado físico) |
| `status_delete` verifica | Pensums | Grados | Secciones | **Inscripciones AND ProfesorGuia** |
| Create view existe | ✅ | ✅ | ⚠️ Parcial | **❌ No existe** (modal) |
| Edit view propia | ✅ | ✅ | ❌ (modal) | **❌ No existe** (stale/irrelevante) |
| `name` tipo | VARCHAR | VARCHAR | VARCHAR | **VARCHAR(1)** |

---

## 10. Plan de Migración: Laravel/Blade → NextJS + API

### Fase 1: Backend API

| Prioridad | Endpoint | Descripción |
|-----------|----------|-------------|
| P0 | `GET /api/control/seccions` | Listado paginado |
| P0 | `GET /api/control/seccions/{id}` | Detalle con relaciones |
| P0 | `GET /api/control/seccions/by-grado/{gradoId}` | Selector dinámico (reemplaza AJAX) |
| P0 | `GET /api/control/seccions/filters` | Opciones de filtros |
| P1 | `POST /api/control/seccions` | Crear con validación |
| P1 | `PUT /api/control/seccions/{id}` | Actualizar |
| P1 | `DELETE /api/control/seccions/{id}` | Eliminar (físico) |

### Fase 2: Frontend NextJS

| Prioridad | Componente | Descripción |
|-----------|-----------|-------------|
| P0 | `useSeccions` | Hook: listado, paginación, búsqueda |
| P0 | `SeccionTable` | DataTable client-side |
| P0 | `SeccionForm` | Formulario modal reutilizable |
| P1 | `CreateSeccionModal` | Modal de creación |
| P1 | `EditSeccionModal` | Modal de edición |
| P1 | `GradoFilter` | Filtro por grado (dispara recarga de tabla) |
| P2 | `SeccionDeleteButton` | Confirmación + eliminación |

### Fase 3: Migración de datos

| Tarea | Detalle |
|-------|---------|
| Normalizar ENUM('true','false') a boolean | `status_active`, `status_inscription_affects` |
| Revisar borrado físico vs soft-delete | Decidir si implementar SoftDeletes real en el modelo |
| Verificar `name` VARCHAR(1) | Confirmar que todos los nombres sean 1 carácter |
| Indexar `grado_id` y `deleted_at` | Performance en consultas |

### Fase 4: Pruebas

| Tipo | Casos |
|------|-------|
| Unitarias | name max 1 char, validación grado_id |
| Integración | CRUD con modales: crear → ver en tabla → editar → eliminar |
| Integración | Restricción: eliminar sección con inscripciones/profesor guía (debe fallar) |
| Integración | Selector dinámico by-grado retorna solo secciones activas |
| Componente | Modal form con estados loading/error |
| E2E | Flujo completo con dependencias Grado → Sección |

---

## 11. Dependencias y librerías

| Librería | Uso en el módulo |
|----------|------------------|
| jQuery + DataTables | Búsqueda, ordenación, paginación client-side |
| Bootstrap 4 | Modales, cards, tablas, formularios |
| FontAwesome 5 | Iconos en botones de acción |
| Laravel Collective (Form) | `Form::open()`, `Form::model()`, `Form::selectRange()`, `Form::select()` |
| `js/models/default/destroy.js` | Script centralizado de eliminación AJAX |

---

## 12. Estructura de la tabla (resumen visual)

| Columna | Tipo | Requerido | Único | Default |
|---------|------|-----------|-------|---------|
| `id` | INT UNSIGNED AUTO_INCREMENT | ✅ | ✅ | — |
| `grado_id` | INT UNSIGNED (FK) | ✅ | ❌ | — |
| `name` | VARCHAR(1) | ✅ | ❌ | — |
| `description` | VARCHAR(255) | ❌ | ❌ | NULL |
| `amount_student` | INT | ❌ | ❌ | 40 |
| `observation` | VARCHAR(255) | ❌ | ❌ | NULL |
| `status_active` | ENUM('true','false') | ✅ | ❌ | — |
| `comment_final` | TEXT | ❌ | ❌ | NULL |
| `status_inscription_affects` | ENUM('true','false') | ❌ | ❌ | 'true' |
| `deleted_at` | TIMESTAMP NULL | ❌ | ❌ | NULL |

---

*Documento generado a partir del análisis del código fuente de SAEFL. Validado contra: `SeccionController.php`, `Seccion.php` (modelo + trait Lists), migraciones en `database/migrations/backUps/`, todas las vistas Blade del módulo, y rutas en `routes/app/tab/seccion.php` + `routes/administracion/tab/seccion.php`.*
