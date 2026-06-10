# Gestión de Instrumento Diagnóstico (Referentes Curriculares) — Documento Técnico Completo

> **Versión:** 1.0 — Validada contra código fuente real (Laravel 8.x / Livewire 2.5)
> **Módulo:** `administracion.diagnostics.referents` — Catálogo de referentes normativos, competencias e indicadores de logro.
> **Propósito:** Replicación en NextJS + API REST (Laravel o Node).
> **Arquitectura fuente:** Livewire CRUD con 3 niveles jerárquicos (Referente → Competencia → Indicador), modal detail, wizard de 3 pasos vía breadcrumb.

---

## 1. Introducción

El módulo **Instrumento Diagnóstico** dentro del submódulo de Configuración del panel de Administración gestiona el **catálogo de referentes curriculares/normativos** que sirven como marco de referencia para el sistema de Diagnóstico Educativo. Es la base conceptual sobre la que se construyen las preguntas, sesiones y reportes del diagnóstico pedagógico institucional.

**3 niveles jerárquicos obligatorios:**

```
Referente Normativo (DiagReferent)
  └── Competencia (DiagCompetency) — vinculada a un Área de Formación (Pensum)
        └── Indicador de Logro (DiagIndicator) — con nivel esperado
```

### 1.1 ¿Qué es un Referente Normativo?

Un referente normativo representa un **documento curricular oficial** que define el marco pedagógico para un Plan de Estudio (`Pestudio`) completo. Ejemplos:

- **"Reforma Educativa 2017"** — Plan de Estudio: "Educación Media General"
- **"Currículo Nacional Bolivariano 2025"** — Plan de Estudio: "Educación Primaria"
- **"Bases Curriculares 2024"** — Plan de Estudio: "Educación Inicial"

Cada referente tiene un **código** (ej. "RES-2017-01"), una **versión** (ej. "2.0"), y soporta **versionado**: solo puede haber 1 referente activo por Plan de Estudio a la vez.

### 1.2 Relación con el Diagnóstico Educativo

Los referentes se conectan con el módulo de Diagnóstico Educativo (`gestion-diagnostics.md`) a través de:

```
DiagReferent ──→ DiagMain (ciclo de diagnóstico, via referent_id)
                   └── DiagQuestion (pregunta, via competency_id, indicator_id)
                         └── DiagSession → DiagAnswer
                   └── DiagReport (informe, via referent_id)
                         └── DiagReportIndicatorResult (gap analysis)
```

### 1.3 ¿CRUD independiente o sub-módulo?

**Es un CRUD independiente** con interfaz de administración propia, pero forma parte del ecosistema del Diagnóstico. Sus datos son consumidos por:

- **`DiagMain`** (ciclo de diagnóstico) — referencia al referente
- **`DiagQuestion`** (preguntas) — referencia a competencia e indicador
- **`DiagReportIndicatorResult`** (gap analysis) — referencia al indicador
- **`AiReportService`** (generación de informes IA) — incluye datos del referente en el payload

---

## 2. Arquitectura del Dominio

### 2.1 Cadena de dependencias

```
Pestudio (Plan de Estudio)
  └── DiagReferent (1:N) — marco normativo
        ├── pestudio_id → Pestudio (versión por plan de estudio)
        ├── code (código/resolución)
        ├── version (ej. "1.0", "2.0")
        ├── active (solo 1 activo por pestudio)
        │
        └── DiagCompetency (1:N)
              ├── pensum_id → Pensum (Área de Formación)
              │     ├── grado_id → Grado
              │     └── asignatura_id → Asignatura
              │
              └── DiagIndicator (1:N)
                    ├── code (ej. "IND-001")
                    ├── description
                    └── expected_level (1-4)

Consumidores:
  DiagMain ──referent_id──→ DiagReferent
  DiagQuestion ──competency_id──→ DiagCompetency
  DiagQuestion ──indicator_id──→ DiagIndicator
  DiagReport ──referent_id──→ DiagReferent
  DiagReportIndicatorResult ──indicator_id──→ DiagIndicator
```

### 2.2 Árbol de archivos del módulo

```
routes/
  app/tab/diagnostics.php                           ← 1 ruta GET para vista contenedora

app/
  Http/
    Controllers/Administracion/Tab/Diagnostic/
      ReferentController.php                        ← 1 método: index (renderiza vista)
    
    Livewire/Administracion/Diagnostics/
      ReferentsMain.php                             ← ~800 líneas, CRUD completo Livewire
        ├── render() → 3 modos: referents / competencies / indicators
        ├── CRUD Referent (create/edit/delete/toggle)
        ├── CRUD Competency (create/edit/delete)
        └── CRUD Indicator (create/edit/delete)

  Models/app/Instrument/
    DiagReferent.php                                ← Marco referencial curricular
    DiagCompetency.php                              ← Competencias
    DiagIndicator.php                               ← Indicadores de logro

resources/views/
  administracion/diagnostics/referents/
    index.blade.php                                 ← Vista contenedora (1 línea: @livewire)
  livewire/administracion/diagnostics/
    referents-main.blade.php                        ← ~29KB, template principal del CRUD
      ├── Breadcrumb visual (3 pasos)
      ├── SearchBar + FilterBar
      ├── ReferentTable (paginated 10)
      ├── CompetencyTable (paginated 10, filtros grado/pensum)
      ├── IndicatorTable (paginated 10)
      ├── ReferentModal (crear/editar)
      ├── CompetencyModal (crear/editar)
      ├── IndicatorModal (crear/editar)
      └── ReferentDetailModal (vista detalle con filtros)

database/migrations/backUps/diagnostico/
  2026_01_14_210502_create_diag_referents_table.php
  2026_01_14_210514_create_diag_competencies_table.php
  2026_01_14_210528_create_diag_indicators_table.php
  2026_01_14_211242_update_diag_mains_table.php     ← Agrega referent_id a diag_mains
  2026_01_14_211251_update_diag_questions_table.php  ← Agrega competency_id, indicator_id
  2026_01_14_212539_create_diag_report_indicator_results_table.php  ← gap analysis
  2026_01_15_202659_add_pestudio_id_to_diag_referents_table.php     ← versión tardía
```

---

## 3. Validación contra código fuente

### 3.1 Hallazgos clave

| # | Tópico | Detalle |
|---|--------|---------|
| 1 | **Sin controlador HTTP CRUD** | `ReferentController` solo tiene `index()`. Todo el CRUD se maneja vía Livewire. No hay endpoints de creación/edición/eliminación vía HTTP tradicional. |
| 2 | **3 niveles en 1 solo componente** | `ReferentsMain.php` (~800 líneas) gestiona los 3 niveles con un `viewMode` ('referents'/'competencies'/'indicators') y paginación independiente por nivel. |
| 3 | **Regla de versionado implementada** | Solo 1 referente activo por Pestudio. Validación en `saveReferent()` y `toggleReferentActive()`. Bloquea con error si ya existe otro activo (no desactiva automáticamente). |
| 4 | **Unique compuesto en `code`** | `unique:diag_referents,code,...id,version,...referent_version` — el código debe ser único dentro de la misma versión. |
| 5 | **Unique compuesto en `code` de indicador** | `unique:diag_indicators,code,...id,competency_id,...competency_id` — el código debe ser único dentro de la misma competencia. |
| 6 | **Cascada de filtros manual** | `compFilterGrado` → filtra competencias por grado del pensum. `compFilterPensum` → filtra por pensum específico. Ambos resetean paginación. |
| 7 | **Modal de detalle separado** | `openDetailModal()` carga relaciones profundas (competencias → pensum → grado, indicadores). Tiene su propia paginación (`detailPage`) y filtros independientes (`detailFilterGradoId`, `detailFilterPensumId`). |
| 8 | **Niveles de logro hardcodeados** | `getLevelLabel()` mapea 1→Insuficiente(danger), 2→En desarrollo(warning), 3→Satisfactorio(info), 4→Avanzado(success). |
| 9 | **Sin soft-deletes** | Los 3 modelos NO usan `SoftDeletes`. La eliminación es física. |
| 10 | **Validación solo en Livewire** | No existen FormRequests. Las reglas de validación están inline en los métodos `save*()`. |
| 11 | **Eliminación protegida** | No se puede eliminar referente con competencias hijas. No se puede eliminar competencia con indicadores hijos. |
| 12 | **SweetAlert2 para feedback** | Toda la retroalimentación al usuario usa `dispatchBrowserEvent('swal', ...)`. No hay `session()->flash()`. |
| 13 | **Confirmación en 2 pasos** | `delete*()` → muestra confirmación SweetAlert → llama `confirmDelete*()` → ejecuta eliminación. |

### 3.2 Validación de rutas

| Ruta | Método | Controlador | Middleware | Archivo |
|------|--------|-------------|------------|---------|
| `GET /diagnostics/referents/index` | `index()` | `Administracion\Tab\Diagnostic\ReferentController` | `auth`, `is_admin` | `routes/app/tab/diagnostics.php` |

Registro en `routes/web.php`:
```php
Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth', 'is_admin'],
    'namespace' => 'Administracion'
], function () {
    require (__DIR__ . '/app/tab/diagnostics.php');
});
```

---

## 4. Lógica de Negocio — Especificación Completa

### 4.1 Reglas de negocio

**RN-01: Jerarquía obligatoria de 3 niveles.**
Todo referente debe tener al menos una competencia para ser útil. Toda competencia debe tener al menos un indicador de logro. El sistema protege contra eliminación de padres con hijos (ver RN-06).

**RN-02: Unicidad de referente activo por Pestudio (versionado).**
Solo puede haber 1 referente con `active=true` por `pestudio_id` a la vez. Si se intenta activar un segundo, el sistema bloquea con error: *"Ya existe un referente ACTIVO para este Plan de Estudio (X). Desactívelo primero."* La regla se aplica tanto en creación/edición como en toggle.

**RN-03: Código único por versión.**
El campo `code` en `diag_referents` debe ser único dentro del mismo valor de `version`. Esto permite tener diferentes versiones del mismo código de resolución.

**RN-04: Código de indicador único por competencia.**
El campo `code` en `diag_indicators` debe ser único dentro del mismo `competency_id`. Cada competencia tiene su propio espacio de códigos.

**RN-05: Niveles de logro predefinidos (1-4).**
El `expected_level` del indicador usa 4 niveles fijos:
| Valor | Etiqueta | Clase CSS |
|-------|----------|-----------|
| 1 | Insuficiente | danger |
| 2 | En desarrollo | warning |
| 3 | Satisfactorio | info |
| 4 | Avanzado | success |

**RN-06: Eliminación segura en cascada.**
No se permite eliminar un referente que tenga competencias asociadas (`competencies()->count() > 0`). No se permite eliminar una competencia que tenga indicadores asociados (`indicators()->count() > 0`). No hay restricciones al eliminar indicadores (son nodos hoja).

**RN-07: Pestudio obligatorio en referente.**
`pestudio_id` es requerido en el formulario del referente (validación: `required|exists:pestudios,id`). El referente siempre pertenece a un Plan de Estudio.

**RN-08: Pensum opcional en competencia.**
`pensum_id` es nullable en competencia. Permite definir competencias generales no vinculadas a un Área de Formación específica.

**RN-09: Filtros de competencia basados en referente activo.**
`loadCompetencyLists()` obtiene los grados y pensums disponibles filtrando por las competencias que YA EXISTEN en el referente seleccionado. No muestra todos los grados/pensums del sistema.

**RN-10: Búsqueda textual en referentes.**
La búsqueda (`search`) aplica LIKE sobre 3 campos: `name`, `code`, `description`. No busca en competencias hijas.

### 4.2 Flujo de datos completo

```
[Usuario autenticado con rol is_admin]
    │
    ├─(1) GET /diagnostics/referents/index
    │     └─ ReferentController@index()
    │           └─ view('administracion.diagnostics.referents.index')
    │                 └─ @livewire('administracion.diagnostics.referents-main')
    │
    └─ [COMPONENTE] ReferentsMain (Livewire) — ~800 líneas
          │
          ├─ mount()
          │    ├─ loadPensums() → $this->pensums (Pensum activos con grado, asignatura)
          │    └─ loadReferentPestudios() → $this->referentPestudios (Pestudio activos)
          │
          ├─ render()
          │    ├─ viewMode === 'referents'
          │    │    ├─ DiagReferent::with('pestudio')->withCount('competencies')
          │    │    ├─ Filtro: search (name, code, description)
          │    │    ├─ Filtro: filterActive (all/active/inactive)
          │    │    └─ paginate(10)
          │    │
          │    ├─ viewMode === 'competencies'
          │    │    ├─ DiagCompetency::where('referent_id', selectedReferentId)
          │    │    │    ->with(['pensum.grado', 'indicators'])->withCount('indicators')
          │    │    ├─ Filtro: compFilterGrado (via whereHas pensum.grado_id)
          │    │    ├─ Filtro: compFilterPensum (via where pensum_id)
          │    │    └─ paginate(10)
          │    │
          │    └─ viewMode === 'indicators'
          │         ├─ DiagIndicator::where('competency_id', selectedCompetencyId)
          │         │    ->with('competency')
          │         └─ paginate(10)
          │
          ├─ CRUD Referente
          │    ├─ createReferent() → reset form → open modal
          │    ├─ editReferent(id) → load → open modal
          │    ├─ saveReferent() → validate → check versionado → updateOrCreate
          │    ├─ toggleReferentActive(id) → check versionado → save
          │    ├─ deleteReferent(id) → check competencies_count → confirm → delete
          │    └─ showReferentDetail(id) → switch to competencies mode
          │
          ├─ CRUD Competencia
          │    ├─ createCompetency() → reset + set referent_id → open modal
          │    ├─ editCompetency(id) → load → open modal
          │    ├─ saveCompetency() → validate → updateOrCreate
          │    ├─ deleteCompetency(id) → check indicators_count → confirm → delete
          │    ├─ showCompetencyDetail(id) → switch to indicators mode
          │    └─ loadCompetencyLists(id) → grados + pensums disponibles
          │
          ├─ CRUD Indicador
          │    ├─ createIndicator() → reset + set competency_id → open modal
          │    ├─ editIndicator(id) → load → open modal
          │    ├─ saveIndicator() → validate → updateOrCreate
          │    └─ deleteIndicator(id) → confirm → delete
          │
          └─ Modal Detalle
               ├─ openDetailModal(id) → load relaciones profundas → open
               ├─ getDetailCompetencies (filtrado por grado/pensum, paginate 5)
               ├─ getDetailGrados (from currentReferent.competencies)
               ├─ getDetailPensums (from currentReferent.competencies, filtrado por grado)
               └─ closeDetailModal()
```

### 4.3 Navegación entre niveles (Breadcrumb Visual)

```
 ┌─────────────────────────────────────────────────────────────────────────────┐
 │ [📘 Referentes Curriculares]   [📋 Competencias]   [🎯 Indicadores]   [+Nuevo] │
 │  Catálogo Institucional...                                                   │
 ├─────────────────────────────────────────────────────────────────────────────┤
 │                                                                              │
 │  • viewMode='referents': Paso 1 activo, resto oculto                        │
 │  • viewMode='competencies': Paso 1 completado (click → back), Paso 2 activo │
 │  • viewMode='indicators': Pasos 1 y 2 completados, Paso 3 activo            │
 │                                                                              │
 │  Cada paso es clickeable hacia atrás:                                       │
 │    Indicadores → click en "Competencias" → vuelve al listado de competencias │
 │    Competencias → click en "Referentes" → vuelve al listado de referentes    │
 └─────────────────────────────────────────────────────────────────────────────┘
```

### 4.4 Modales

| Modal | Propósito | Campos |
|-------|-----------|--------|
| `ReferentModal` | Crear/Editar referente | pestudio_id (select), name, code, version, description, active (toggle) |
| `CompetencyModal` | Crear/Editar competencia | referent_id (hidden), pensum_id (select con fullname), name, description |
| `IndicatorModal` | Crear/Editar indicador | competency_id (hidden), code, description, expected_level (select 1-4) |
| `ReferentDetailModal` | Vista detalle con filtros | Filtros: grado, pensum. Tabla competencias filtradas paginadas (5) |

---

## 5. Esquemas de Base de Datos

### 5.1 Tabla `diag_referents`

```sql
CREATE TABLE `diag_referents` (
  `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pestudio_id` BIGINT UNSIGNED NULL,     -- Plan de Estudio asociado
  `name`        VARCHAR(255) NOT NULL,     -- Nombre del referente
  `code`        VARCHAR(50) NULL,          -- Código / Resolución oficial
  `version`     VARCHAR(50) NULL,          -- Versión del documento (ej. "1.0", "2.0")
  `description` TEXT NULL,                 -- Descripción o alcance
  `active`      TINYINT(1) DEFAULT 1,      -- Solo 1 activo por pestudio_id
  `created_at`  TIMESTAMP NULL,
  `updated_at`  TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Nota: No hay FK explícita a pestudios en la migración original.
-- La columna pestudio_id se agregó posteriormente (2026_01_15_202659).
```

### 5.2 Tabla `diag_competencies`

```sql
CREATE TABLE `diag_competencies` (
  `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `referent_id` BIGINT UNSIGNED NOT NULL,  -- FK → diag_referents(id)
  `pensum_id`   INT UNSIGNED NULL,         -- FK → pensums(id), nullable
  `name`        VARCHAR(255) NOT NULL,      -- Nombre de la competencia
  `description` TEXT NULL,                  -- Descripción
  `created_at`  TIMESTAMP NULL,
  `updated_at`  TIMESTAMP NULL,
  FOREIGN KEY (`referent_id`) REFERENCES `diag_referents`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.3 Tabla `diag_indicators`

```sql
CREATE TABLE `diag_indicators` (
  `id`             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `competency_id`  BIGINT UNSIGNED NOT NULL,  -- FK → diag_competencies(id)
  `code`           VARCHAR(50) NULL,           -- Código único por competencia
  `description`    TEXT NOT NULL,              -- Descripción del indicador de logro
  `expected_level` VARCHAR(50) NULL,           -- Nivel esperado (1-4)
  `created_at`     TIMESTAMP NULL,
  `updated_at`     TIMESTAMP NULL,
  FOREIGN KEY (`competency_id`) REFERENCES `diag_competencies`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.4 Tablas consumidoras (relacionadas)

#### `diag_mains` (referencia a referente)

```sql
CREATE TABLE `diag_mains` (
  `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `token`       VARCHAR(100) NULL,
  `active`      TINYINT(1) DEFAULT 1,
  `referent_id` BIGINT UNSIGNED NULL,       -- FK → diag_referents(id)
  `lapso_id`    INT UNSIGNED NULL,
  `pestudio_id` BIGINT UNSIGNED NULL,
  `created_at`  TIMESTAMP NULL,
  `updated_at`  TIMESTAMP NULL,
  FOREIGN KEY (`lapso_id`) REFERENCES `lapsos`(`id`),
  FOREIGN KEY (`pestudio_id`) REFERENCES `pestudios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### `diag_report_indicator_results` (gap analysis)

```sql
CREATE TABLE `diag_report_indicator_results` (
  `id`                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `report_id`         BIGINT UNSIGNED NOT NULL,       -- FK → diag_reports(id)
  `pensum_id`         INT UNSIGNED NULL,              -- FK → pensums(id)
  `indicator_id`      BIGINT UNSIGNED NULL,           -- FK → diag_indicators(id)
  `expected_level`    VARCHAR(50) NULL,               -- Copia del nivel esperado
  `observed_level`    VARCHAR(50) NULL,               -- Nivel observado
  `gap_value`         INT DEFAULT 0,                  -- Diferencia numérica
  `gap_label`         VARCHAR(100) NULL,               -- Clasificación de brecha
  `teacher_observation` TEXT NULL,                    -- Observación del docente
  FOREIGN KEY (`report_id`) REFERENCES `diag_reports`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.5 Mapa de migraciones (orden cronológico)

```
2026_01_14_210502 → create diag_referents
2026_01_14_210514 → create diag_competencies
2026_01_14_210528 → create diag_indicators
2026_01_14_211242 → update diag_mains (add referent_id)
2026_01_14_211251 → update diag_questions (add competency_id, indicator_id)
2026_01_14_212539 → create diag_report_indicator_results
2026_01_15_202659 → add pestudio_id to diag_referents
```

---

## 6. Modelo de Datos — API REST para exportación

### 6.1 Endpoints propuestos

#### `GET /api/admin/diagnostics/referents`

Lista paginada de referentes con conteo de competencias e indicadores.

**Query params:** `search`, `active` (all/active/inactive), `pestudio_id`, `page`, `per_page` (default 10)

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "pestudio_id": 3,
      "pestudio": { "id": 3, "name": "EDUCACIÓN MEDIA GENERAL" },
      "name": "Reforma Educativa 2017",
      "code": "RES-2017-01",
      "version": "2.0",
      "description": "Actualización curricular 2017 para educación media",
      "active": true,
      "competencies_count": 12,
      "total_indicators": 48,
      "created_at": "2026-01-15T10:00:00Z",
      "updated_at": "2026-06-01T15:30:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 42
  }
}
```

#### `GET /api/admin/diagnostics/referents/{id}`

Detalle completo del referente con competencias e indicadores anidados.

**Response:**
```json
{
  "id": 1,
  "pestudio_id": 3,
  "pestudio": { "id": 3, "name": "EDUCACIÓN MEDIA GENERAL" },
  "name": "Reforma Educativa 2017",
  "code": "RES-2017-01",
  "version": "2.0",
  "description": "Actualización curricular 2017 para educación media",
  "active": true,
  "competencies": [
    {
      "id": 10,
      "pensum_id": 45,
      "pensum": {
        "id": 45,
        "fullname": "MATEMÁTICA - 5TO AÑO",
        "grado": { "id": 5, "name": "5TO AÑO" },
        "asignatura": { "id": 8, "full_name": "MATEMÁTICA" }
      },
      "name": "Resuelve problemas matemáticos",
      "description": "Aplica razonamiento lógico-matemático",
      "indicators_count": 4,
      "indicators": [
        {
          "id": 100,
          "code": "IND-001",
          "description": "Resuelve ecuaciones lineales",
          "expected_level": 3,
          "expected_level_label": "Satisfactorio"
        }
      ]
    }
  ],
  "created_at": "2026-01-15T10:00:00Z",
  "updated_at": "2026-06-01T15:30:00Z"
}
```

#### `POST /api/admin/diagnostics/referents`

Crear nuevo referente con validación de versionado.

**Request:**
```json
{
  "pestudio_id": 3,
  "name": "Reforma Educativa 2025",
  "code": "RES-2025-01",
  "version": "1.0",
  "description": "Nuevo marco curricular 2025",
  "active": true
}
```

**Validation rules:**
- `pestudio_id`: required, exists:pestudios,id
- `name`: required, string, max:255
- `code`: required, string, max:100, unique con version (code + version)
- `version`: required, string, max:20
- `description`: nullable, string
- `active`: if true, verificar que no exista otro activo para el mismo pestudio_id

**Response (201 Created):** Objeto del referente creado.

**Error 409 (Conflict):**
```json
{
  "error": "versionado_conflicto",
  "message": "Ya existe un referente ACTIVO para este Plan de Estudio (2.0). Desactívelo primero.",
  "existing_active": { "id": 1, "version": "2.0", "name": "Reforma Educativa 2017" }
}
```

#### `PUT /api/admin/diagnostics/referents/{id}`

Actualizar referente. Mismas reglas de validación que POST, ignorando el ID actual en unique checks.

#### `DELETE /api/admin/diagnostics/referents/{id}`

Eliminar referente (físico, no soft-delete).

**Error 409 (Conflict):**
```json
{
  "error": "delete_conflict",
  "message": "No se puede eliminar un referente que tiene competencias asociadas.",
  "competencies_count": 5
}
```

#### `PATCH /api/admin/diagnostics/referents/{id}/toggle`

Activar/desactivar referente con verificación de versionado (misma regla que POST).

#### `GET /api/admin/diagnostics/referents/{id}/competencies`

Lista paginada de competencias de un referente.

**Query params:** `grado_id`, `pensum_id`, `page`, `per_page` (default 10)

#### `POST /api/admin/diagnostics/competencies`

Crear competencia.

**Validation rules:**
- `referent_id`: required, exists:diag_referents,id
- `pensum_id`: nullable, exists:pensums,id
- `name`: required, string, max:255
- `description`: nullable, string

#### `PUT /api/admin/diagnostics/competencies/{id}`

Actualizar competencia.

#### `DELETE /api/admin/diagnostics/competencies/{id}`

Eliminar competencia (físico).

**Error 409:** Si tiene indicadores asociados.

#### `GET /api/admin/diagnostics/competencies/{id}/indicators`

Lista paginada de indicadores de una competencia.

#### `POST /api/admin/diagnostics/indicators`

Crear indicador.

**Validation rules:**
- `competency_id`: required, exists:diag_competencies,id
- `code`: required, string, max:50, unique dentro de competency_id
- `description`: required, string
- `expected_level`: required, in:1,2,3,4

#### `PUT /api/admin/diagnostics/indicators/{id}`

Actualizar indicador. Mismas reglas que POST.

#### `DELETE /api/admin/diagnostics/indicators/{id}`

Eliminar indicador (físico, sin restricciones — es nodo hoja).

#### `GET /api/admin/diagnostics/referents/filters`

Listas para poblar filtros del frontend.

**Response:**
```json
{
  "pestudios": [
    { "id": 1, "name": "EDUCACIÓN INICIAL" },
    { "id": 3, "name": "EDUCACIÓN MEDIA GENERAL" }
  ],
  "niveles_logro": [
    { "value": 1, "label": "Insuficiente", "class": "danger" },
    { "value": 2, "label": "En desarrollo", "class": "warning" },
    { "value": 3, "label": "Satisfactorio", "class": "info" },
    { "value": 4, "label": "Avanzado", "class": "success" }
  ]
}
```

---

## 7. Lógica de Versionado (Algoritmo)

### 7.1 Regla de unicidad de activo por Pestudio

```typescript
interface VersionadoValidation {
  canActivate: boolean;
  error?: string;
  existingActive?: { id: number; version: string; name: string };
}

function validateVersionado(
  pestudioId: number,
  referentId: number | null,  // null si es creación
  active: boolean
): VersionadoValidation {
  if (!active) {
    // Siempre se puede desactivar sin verificación
    return { canActivate: true };
  }

  // Buscar otro referente activo para el mismo pestudio (excluyendo este)
  const existingActive = DiagReferent.query()
    .where('pestudio_id', pestudioId)
    .where('active', true)
    .where('id', '!=', referentId ?? 0)
    .first();

  if (existingActive) {
    return {
      canActivate: false,
      error: `Ya existe un referente ACTIVO para este Plan de Estudio (${existingActive.version}). Desactívelo primero.`,
      existingActive: {
        id: existingActive.id,
        version: existingActive.version,
        name: existingActive.name,
      },
    };
  }

  return { canActivate: true };
}
```

### 7.2 Mapa de niveles de logro

```typescript
const LEVEL_MAP: Record<number, { label: string; class: string }> = {
  1: { label: 'Insuficiente', class: 'danger' },
  2: { label: 'En desarrollo', class: 'warning' },
  3: { label: 'Satisfactorio', class: 'info' },
  4: { label: 'Avanzado', class: 'success' },
};

function getLevelLabel(level: number): { label: string; class: string } {
  return LEVEL_MAP[level] ?? { label: 'No definido', class: 'secondary' };
}
```

### 7.3 Unique compuesto (código + versión)

```sql
-- En la migración NextJS, agregar unique compuesto:
UNIQUE KEY `uq_referent_code_version` (`code`, `version`)
UNIQUE KEY `uq_indicator_code_competency` (`code`, `competency_id`)
```

---

## 8. Especificación de Componentes (NextJS + Tailwind)

### 8.1 Árbol de componentes

```
AdminDiagnosticsReferentsPage
├── PageHeader (título + breadcrumb)
├── StepperNavigation (3 pasos: Referentes → Competencias → Indicadores)
│   ├── StepReferents (click → backToReferents si no es el paso actual)
│   ├── StepCompetencies (click → backToCompetencies si viewMode=indicators)
│   └── StepIndicators (solo visible si selectedCompetencyId)
├── FilterBar (solo en viewMode=referents)
│   ├── SearchInput (debounce 300ms)
│   └── ActiveFilterSelect (all/active/inactive)
├── ReferentList (viewMode=referents)
│   ├── ReferentTable
│   │   ├── ReferentRow (name, code, version, pestudio badge, active badge, competencies count)
│   │   │   ├── ["Ver Detalle"] → showDetail
│   │   │   ├── [Editar] → editReferent
│   │   │   ├── [Activar/Desactivar] → toggleActive
│   │   │   └── [Eliminar] → deleteReferent (con confirmación)
│   │   └── EmptyState
│   └── Pagination
├── CompetencyList (viewMode=competencies)
│   ├── CompetencyFilterBar (gradoSelect, pensumSelect, resetButton)
│   ├── CompetencyTable
│   │   ├── CompetencyRow (name, pensum fullname, indicators count)
│   │   │   ├── ["Ver Indicadores"] → showIndicators
│   │   │   ├── [Editar] → editCompetency
│   │   │   └── [Eliminar] → deleteCompetency (con confirmación)
│   │   └── EmptyState
│   └── Pagination
├── IndicatorList (viewMode=indicators)
│   ├── IndicatorTable
│   │   ├── IndicatorRow (code, description, expected_level badge)
│   │   │   ├── [Editar] → editIndicator
│   │   │   └── [Eliminar] → deleteIndicator (con confirmación)
│   │   └── EmptyState
│   └── Pagination
├── ReferentModal (create/edit)
│   ├── PestudioSelect (from referentPestudios)
│   ├── NameInput
│   ├── CodeInput (con validación unique+version)
│   ├── VersionInput
│   ├── DescriptionTextarea
│   └── ActiveToggle
├── CompetencyModal (create/edit)
│   ├── ReferentIdField (hidden)
│   ├── PensumSelect (from pensums con fullname search)
│   ├── NameInput
│   └── DescriptionTextarea
├── IndicatorModal (create/edit)
│   ├── CompetencyIdField (hidden)
│   ├── CodeInput (con validación unique+competency)
│   ├── DescriptionTextarea
│   └── ExpectedLevelSelect (1-4 con labels)
├── ReferentDetailModal (vista detalle)
│   ├── DetailFilterGrado
│   ├── DetailFilterPensum
│   ├── CompetencyList (paginated, filtrado)
│   │   └── CompetencyDetailCard (competencia + sus indicadores)
│   └── CloseButton
└── ConfirmDeleteDialog (SweetAlert2 → reemplazar por shadcn AlertDialog)
```

### 8.2 Estados de cada componente

| Componente | Loading | Empty | Error | Success |
|-----------|---------|-------|-------|---------|
| `ReferentTable` | Skeleton (5 filas) | "No hay referentes registrados" | Toast error al cargar | Tabla paginada |
| `CompetencyTable` | Skeleton | "No hay competencias para este referente" | Toast error | Tabla paginada |
| `IndicatorTable` | Skeleton | "No hay indicadores para esta competencia" | Toast error | Tabla paginada |
| `ReferentModal` | Spinner al guardar | Formulario vacío (create) / precargado (edit) | Errores de validación inline | Cierra y refresca |
| `CompetencyModal` | Spinner al guardar | Formulario vacío / precargado | Errores de validación inline | Cierra y refresca |
| `IndicatorModal` | Spinner al guardar | Formulario vacío / precargado | Errores de validación inline | Cierra y refresca |
| `ReferentDetailModal` | Skeleton en carga de datos | "No hay competencias" | Toast error | Lista con filtros |
| `ConfirmDeleteDialog` | N/A | N/A | Error si tiene hijos | Elimina y refresca |
| `FilterBar` | N/A | N/A | N/A | Filtros aplicados |
| `StepperNavigation` | N/A | N/A | N/A | Pasos activos/completados |

### 8.3 Diagrama de navegación (estados y transiciones)

```
[Referents] ──showDetail──→ [Competencies] ──showIndicators──→ [Indicators]
     ↑                           ↑                                  │
     └─── backToReferents ───────┘                                  │
                                 └──────── backToCompetencies ───────┘

Cada nivel:
  [Lista] ──create──→ [Modal Crear] ──save──→ [Lista refrescada]
  [Lista] ──edit────→ [Modal Editar] ──save──→ [Lista refrescada]
  [Lista] ──delete──→ [Confirmación] ──confirm──→ [Lista refrescada]
```

---

## 9. Plan de Migración: Laravel/Livewire → NextJS + API

### Fase 1: Backend API

| Prioridad | Endpoint | Descripción |
|-----------|----------|-------------|
| P0 | `GET /api/admin/diagnostics/referents` | Lista paginada con filtros y conteos |
| P0 | `GET /api/admin/diagnostics/referents/{id}` | Detalle con competencias e indicadores |
| P0 | `GET /api/admin/diagnostics/referents/filters` | Listas para poblar filtros |
| P0 | `POST /api/admin/diagnostics/referents` | Crear con validación de versionado |
| P1 | `PUT /api/admin/diagnostics/referents/{id}` | Actualizar |
| P1 | `DELETE /api/admin/diagnostics/referents/{id}` | Eliminar con verificación de hijos |
| P1 | `PATCH /api/admin/diagnostics/referents/{id}/toggle` | Activar/desactivar |
| P1 | `GET /api/admin/diagnostics/referents/{id}/competencies` | Competencias paginadas |
| P1 | `POST /api/admin/diagnostics/competencies` | Crear competencia |
| P2 | `PUT /api/admin/diagnostics/competencies/{id}` | Actualizar competencia |
| P2 | `DELETE /api/admin/diagnostics/competencies/{id}` | Eliminar con verificación |
| P2 | `GET /api/admin/diagnostics/competencies/{id}/indicators` | Indicadores paginados |
| P2 | `POST /api/admin/diagnostics/indicators` | Crear indicador |
| P2 | `PUT /api/admin/diagnostics/indicators/{id}` | Actualizar indicador |
| P2 | `DELETE /api/admin/diagnostics/indicators/{id}` | Eliminar indicador |

### Fase 2: Frontend NextJS

| Prioridad | Componente | Descripción |
|-----------|------------|-------------|
| P0 | `useReferents` | Hook principal con react-query (list, create, update, delete, toggle) |
| P0 | `useCompetencies` | Hook para competencias (list por referente, CRUD) |
| P0 | `useIndicators` | Hook para indicadores (list por competencia, CRUD) |
| P0 | `AdminDiagnosticsReferentsPage` | Layout + StepperNavigation + FilterBar |
| P0 | `ReferentTable + ReferentRow` | Lista principal |
| P1 | `ReferentModal` | Formulario crear/editar con validación |
| P1 | `CompetencyTable + CompetencyRow` | Lista de competencias |
| P1 | `CompetencyModal` | Formulario crear/editar |
| P2 | `IndicatorTable + IndicatorRow` | Lista de indicadores |
| P2 | `IndicatorModal` | Formulario crear/editar |
| P2 | `ReferentDetailModal` | Modal de detalle con filtros |
| P2 | `ConfirmDeleteDialog` | Diálogo de confirmación (shadcn AlertDialog) |

### Fase 3: Validación y reglas de negocio en frontend

| Prioridad | Regla | Implementación |
|-----------|-------|----------------|
| P0 | Unique compuesto code+version | Validación antes de enviar (debounced check) |
| P0 | Unique compuesto code+competency_id | Validación antes de enviar |
| P0 | Versionado: 1 activo por pestudio | Manejar error 409 → mostrar mensaje al usuario |
| P1 | Eliminación bloqueada con hijos | Validación + manejar error 409 |
| P2 | Niveles de logro (1-4) | Select con labels y colores |

### Fase 4: Pruebas

| Tipo | Casos |
|------|-------|
| Unitarias | Validación de versionado (activar con existente, activar sin competencia) |
| Unitarias | Cálculo de niveles de logro |
| Integración | CRUD referente → crear competencia → crear indicador → eliminar todo |
| Integración | Error 409 al eliminar referente con competencias |
| Integración | Error 409 al activar segundo referente para mismo pestudio |
| Componente | StepperNavigation con todas las transiciones de estado |
| Componente | Modales en modo creación vs edición |
| Componente | Filtros de competencia con reset |
| E2E | Flujo completo: crear referente → crear competencia → crear indicador → ver detalle → editar → eliminar |

---

## 10. Edge Cases y validaciones

| Caso | Comportamiento esperado |
|------|------------------------|
| Crear referente sin competencias | Permitido (el CRUD no lo exige, pero no será útil hasta que tenga competencias) |
| Activar 2do referente para mismo Pestudio | Error 409: "Ya existe un referente ACTIVO..." |
| Eliminar referente con competencias hijas | Error 409: verificar `competencies()->count() > 0` |
| Eliminar competencia con indicadores hijos | Error: verificar `indicators()->count() > 0` |
| Código duplicado en referente (misma versión) | Error de validación unique compuesto |
| Código duplicado en indicador (misma competencia) | Error de validación unique compuesto |
| Búsqueda sin resultados en referentes | EmptyState: "No hay referentes que coincidan con la búsqueda" |
| Pestudio sin referentes | FilterBar muestra pestudios vacíos — tabla EmptyState |
| Pensum sin competencias en el detalle | Filtro de grado/pensum sin opciones — tabla vacía |
| Referente inactivo en toggle | SweetAlert warning: "No estará disponible para nuevos diagnósticos" |
| Modal abierto mientras se cambia de pestudio | Comportamiento actual: no hay validación cruzada |
| Nivel de logro no definido (null) | Mostrar "No definido" con clase secondary |

---

## 11. Dependencias y librerías

| Dependencia | Uso | Reemplazo en NextJS |
|-------------|-----|---------------------|
| Livewire | Reactividad server-side, validación inline | React Hook Form + Zod + TanStack Query |
| Bootstrap 4 | Layout, tabs, cards, modales | Tailwind CSS + shadcn/ui |
| FontAwesome 5 | Iconos en botones y breadcrumb | lucide-react |
| SweetAlert2 | Alertas de éxito/error, confirmaciones de eliminación | shadcn/ui Toast + AlertDialog |
| Pagination (Bootstrap) | Paginación en los 3 niveles + modal detalle | shadcn/ui Pagination + server-side |

---

## 12. Checklist de implementación para NextJS

- [ ] Migrar `diag_referents`, `diag_competencies`, `diag_indicators` a schema Prisma / Drizzle
- [ ] Agregar UNIQUE constraints compuestas: `(code, version)` en referents, `(code, competency_id)` en indicators
- [ ] Implementar `GET /api/admin/diagnostics/referents` con filtros y paginación
- [ ] Implementar `POST /api/admin/diagnostics/referents` con validación de versionado
- [ ] Implementar `PATCH /api/admin/diagnostics/referents/{id}/toggle` con verificación de versionado
- [ ] Implementar `DELETE /api/admin/diagnostics/referents/{id}` con verificación de hijos
- [ ] Implementar CRUD completo de competencias e indicadores
- [ ] Implementar `ReferentsPage` con StepperNavigation de 3 pasos
- [ ] Implementar `ReferentTable` con búsqueda y filtro activo
- [ ] Implementar `CompetencyTable` con filtros de grado y pensum
- [ ] Implementar `IndicatorTable` con niveles de logro coloreados
- [ ] Implementar 3 modales (create/edit) con validación Zod
- [ ] Implementar `ReferentDetailModal` con filtros y paginación independiente
- [ ] Implementar manejo de errores 409 (conflictos de versión e hijos)
- [ ] Implementar `ConfirmDeleteDialog` con shadcn/ui
- [ ] Probar flujo completo: crear → ver detalle → editar → eliminar
- [ ] Probar regla de versionado (activo único por pestudio)

---

*Documento generado a partir del análisis del código fuente de SAEFL. Validado contra: `ReferentController.php`, `ReferentsMain.php` (~800 líneas), `DiagReferent.php`, `DiagCompetency.php`, `DiagIndicator.php`, migraciones en `backUps/diagnostico/`, y la vista Blade `referents-main.blade.php` (~29KB).*
