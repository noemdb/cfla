# Gestión de Lapsos (Control de Estudios) — Documento Técnico Completo

> **Versión:** 1.0 — Validada contra código fuente real (Laravel 8.x / PHP 8.2)
> **Módulo:** `control.configuraciones.lapsos` — CRUD de lapsos (periodos académicos: trimestres).
> **Propósito:** Replicación en NextJS + API REST (Laravel o Node).
> **Arquitectura fuente:** Controlador tradicional (sin Livewire para CRUD, con Livewire en sub-módulo Census) + Blade + modales + jQuery DataTables + Chart.js.

---

## 1. Introducción

El módulo **Gestión de Lapsos** dentro del submódulo **Configuraciones** del **Control de Estudios** (`is_control`) administra los períodos académicos (lapsos) del año escolar — típicamente 3 trimestres.

Un **Lapso** define:
- **Identificación**: código, abreviación, nombre (ej: "Primer Lapso")
- **Fechas**: inicio, fin, inicio actividades académicas, corte de nota
- **Censo escolar**: fechas y horas de inicio/fin del censo
- **Pre-cierre**: fecha y hora de pre-cierre de notas
- **Flag de último lapso**: `status_last` — si permite carga extemporánea de notas

Además del CRUD de lapsos, el módulo incluye un **sub-módulo de Censo Escolar** con 5 rutas CRUD para gestionar el censo de estudiantes y 3 rutas de gráficos (Chart.js).

**Importancia sistémica:** `Lapso::current()` es el método más utilizado en toda la aplicación para determinar el período académico activo.

---

## 2. Arquitectura del Dominio

### 2.1 Cadena de dependencias

```
Lapso (período académico: trimestre) ←── MÓDULO ACTUAL
    ├── pevaluacions[] → Pevaluacion (carga académica filtrada por lapso)
    ├── profesor_guias[] → ProfesorGuia (asignaciones por lapso)
    ├── ecualitativas[] → Ecualitativa (evaluaciones cualitativas)
    ├── edescriptivas[] → Edescriptiva (evaluaciones descriptivas)
    ├── evaluacions[] → Evaluacion (via pevaluacions → lapso_id)
    ├── boletins[] → Boletin (via evaluacions → pevaluacions → lapso)
    ├── baremos[] → Baremo (escalas de notas específicas por lapso)
    ├── diag_sessions[] → DiagSession (sesiones diagnósticas)
    ├── diag_reports[] → DiagReport (reportes diagnósticos)
    ├── diag_mains[] → DiagMain (evaluaciones diagnósticas)
    │
    └── (lapso_id referenciado por ~20 modelos y consultado via
         Lapso::current() en ~50+ Livewire components y controllers)
```

### 2.2 Árbol de archivos

```
routes/
  web.php                                              ← grupo /app/control (is_control)
  app/control.php                                      ← require __DIR__ . '/tab/lapso.php'
                                                         + require __DIR__ . '/charts/lapso.php'
  app/tab/lapso.php                                    ← 12 rutas CRUD (7 lapso + 5 census)
  app/charts/lapso.php                                 ← 3 rutas de gráficos (census)

app/
  Http/
    Controllers/
      Administracion/Tab/Configuracion/
        LapsoController.php                            ← 11 métodos (6 CRUD + 5 census)
      Administracion/Chart/
        LapsoController.php                            ← 3 métodos (census charts)

  Models/
    app/
      Pescolar/
        Lapso.php                                      ← modelo, SoftDeletes, 4 relaciones, 1 trait
        Functions/Lapso/
          Lists.php                                    ← list_lapso(), list_lapso_final()
      Estudiante/Functions/Estudiant/
        Lapsos.php                                     ← trait vacío (placeholder)

resources/views/
  administracion/configuraciones/lapsos/
    index.blade.php                                    ← Página principal (listado + modales)
    edit.blade.php                                     ← [STALE/ROTO: código del módulo Banco]
    census.blade.php                                   ← CRUD de censo escolar
    edit_census.blade.php                              ← Editar participante del censo
    indicator_census.blade.php                         ← Panel de gráficos del censo
    card.blade.php                                     ← Tarjeta visual del lapso
    form/
      fields.blade.php                                 ← 12 campos del formulario de lapso
      census/fields.blade.php                          ← 15 campos del censo (con Livewire)
    table/
      index.blade.php                                  ← DataTable de lapsos + modales
      census.blade.php                                 ← DataTable de participantes del censo
    partials/
      create.blade.php                                 ← Form POST → store
      edit.blade.php                                   ← Form PUT → update
      details.blade.php                                ← Detalle solo-lectura
    menus/
      index.blade.php                                  ← [+ Nuevo Lapso] [←] [↺]
      create.blade.php                                 ← [Listado] [←] [↺]
      edit.blade.php                                   ← [Listado] [Registrar] [←] [↺]
      show.blade.php                                   ← [Nuevo Usuario] [Listados] [←] [↺]
      crud.blade.php                                   ← [Pensums] [←] [↺]
      census.blade.php                                 ← [Lapsos] [Indicadores] [←] [↺]
      census_edit.blade.php                            ← [Lapsos] [←] [↺]
    chart/
      institution.blade.php                            ← Gráfico: instituciones de origen
      grados.blade.php                                 ← Gráfico: grados
      municipio.blade.php                              ← Gráfico: municipios de nacimiento
```

---

## 3. Validación contra código fuente

### 3.1 Hallazgos clave

| # | Tópico | Detalle |
|---|--------|---------|
| 1 | **Sin Livewire para CRUD** | CRUD de lapso con controlador tradicional + Blade + modales. |
| 2 | **Sin validación CRUD server-side** | `store()` y `update()` usan `$request->all()` sin Form Requests. Census sí tiene validación en `censusUpdate()`. |
| 3 | **Sub-módulo Census con Livewire** | El formulario de censo (`form/census/fields.blade.php`) usa `wire:model.defer` para 15 campos. |
| 4 | **`SoftDeletes` activo** | El modelo usa el trait `SoftDeletes`. La migración incluye softDeletes(). |
| 5 | **`status_delete` verifica 2 condiciones** | `getStatusDeleteAttribute()` retorna `true` solo si NO tiene `profesor_guias` AND NO tiene `pevaluacions`. |
| 6 | **`edit.blade.php` roto** | Contiene código de `$banco` (módulo Banco, no Lapso). No es usado porque la edición se hace via modal. |
| 7 | **Modelo con 15+ accessors** | `getBadgeAttribute`, `getClassAttribute`, `getcolorAttribute`, `getStatusPreclosingAttribute`, `getProfesorsAttribute`, `getGoalAsignPEAttribute`, `getRealNotasPEAttribute`, etc. |
| 8 | **`Lapso::current()` — método más usado del sistema** | Presente en ~50+ Livewire components y controllers. Determina el lapso activo por fecha. |
| 9 | **7 migraciones** + 1 FK externo | Desde creación (2019) hasta `academic_start_date` (2024). Todas en `backUps/`. |
| 10 | **Sin migraciones activas** | Todas las migraciones están en `database/migrations/backUps/`, ninguna en `database/migrations/`. |
| 11 | **`status_last` ENUM** | Marca el último lapso del año. Permite carga extemporánea de notas. |
| 12 | **Gráficos Chart.js** | 3 vistas de gráficos para datos del censo (instituciones, grados, municipios). |
| 13 | **`list_lapso_final()`** | Agrega entrada virtual "FINAL" (id = max+1) para representar el período definitivo de notas. |

### 3.2 Validación de rutas

#### Rutas CRUD de Lapso

| Método | URI | Controlador | Middleware | Nombre |
|--------|-----|-------------|------------|--------|
| GET | `/configuraciones/lapsos` | `LapsoController@index` | `auth`, `is_control` | `administracion.configuraciones.lapsos` |
| GET | `/configuraciones/lapsos/index` | `LapsoController@index` | `auth`, `is_control` | `administracion.configuraciones.lapsos.index` |
| GET | `/configuraciones/lapsos/create` | `LapsoController@create` | `auth`, `is_control` | `administracion.configuraciones.lapsos.create` |
| POST | `/configuraciones/lapsos/store` | `LapsoController@store` | `auth`, `is_control` | `administracion.configuraciones.lapsos.store` |
| GET | `/configuraciones/lapsos/{id}` | `LapsoController@edit` | `auth`, `is_control` | `administracion.configuraciones.lapsos.edit` |
| PUT | `/configuraciones/lapsos/{id}` | `LapsoController@update` | `auth`, `is_control` | `administracion.configuraciones.lapsos.update` |
| DELETE | `/configuraciones/lapsos/destroy/{id}` | `LapsoController@destroy` | `auth`, `is_control` | `administracion.configuraciones.lapsos.destroy` |

#### Rutas de Censo

| Método | URI | Método | Nombre |
|--------|-----|--------|--------|
| GET | `/configuraciones/lapsos/census/index/{id}` | `census` | `...census.index` |
| GET | `/configuraciones/lapsos/census/indicators/{id}` | `censusIndicators` | `...census.indicators` |
| GET | `/configuraciones/lapsos/census/edit/{id}` | `censusEdit` | `...census.edit` |
| PUT | `/configuraciones/lapsos/census/update/{id}` | `censusUpdate` | `...census.update` |
| DELETE | `/configuraciones/lapsos/census/destroy/{id}` | `censusDestroy` | `...census.destroy` |

#### Rutas de Gráficos

| Método | URI | Método | Nombre |
|--------|-----|--------|--------|
| GET | `charts/lapso/census/institution` | `census_institution` | `administracion.lapso.census.chart.institution` |
| GET | `charts/lapso/census/grado` | `census_grado` | `administracion.lapso.census.chart.grado` |
| GET | `charts/lapso/census/municipio` | `census_municipio` | `administracion.lapso.census.chart.municipio` |

---

## 4. Lógica de Negocio — Especificación Completa

### 4.1 Reglas de negocio

**RN-01: Lapso activo por fecha.**
El método `Lapso::current($fecha = null)` determina el lapso activo: busca donde `finicial <= fecha <= ffinal`. Si no encuentra, retorna el primer lapso. Si no se pasa fecha, usa `Carbon::now()`. Este es el mecanismo central de determinación de período académico en todo SAEFL.

**RN-02: Tres lapsos por año escolar.**
El sistema asume típicamente 3 lapsos (ids 1, 2, 3) con colores distintivos:
- Lapso 1 → badge `info`, color `primary`
- Lapso 2 → badge `primary`, color `success`
- Lapso 3 → badge `success`, color `danger`

**RN-03: Último lapso para carga extemporánea.**
`status_last` (ENUM 'true'/'false') marca el lapso final. Cuando es 'true', permite carga de notas fuera del período regular.

**RN-04: Protección contra eliminación.**
Un lapso solo se puede eliminar si NO tiene `profesor_guias` AND NO tiene `pevaluacions`. El accessor `getStatusDeleteAttribute()` evalúa ambas condiciones.

**RN-05: Corte de nota habilitado por fecha y pagos.**
`getStatusEnableCorte($estudiant_id)` verifica: (a) fecha actual >= `date_cutnote`, (b) estudiante sin facturas vencidas en esa fecha. Controla si el corte de nota está habilitado.

**RN-06: Boletín habilitado por fecha y pagos.**
`getStatusEnableBoletin($estudiant_id)` similar: (a) fecha actual >= `ffinal`, (b) estudiante sin facturas vencidas. Controla si el boletín está disponible.

**RN-07: Censo activo por rango de fechas.**
`getLapsoCensusActive($fecha = null)` busca lapsos donde `date_start_census <= fecha <= date_end_census`. El accessor `getStatusActiveCensuAttribute` (sic — typo en código) verifica fecha + hora + día laboral (L-V).

**RN-08: Pre-cierre por fecha y hora.**
`getStatusPreclosingAttribute` compara `date_preclosing + time_preclosing` con `Carbon::now()`. `getFullDatePreclosingAttribute` combina ambos campos en un Carbon.

**RN-09: Métricas de carga académica.**
Múltiples accessors calculan:
- `getGoalAsignPEAttribute` — secciones totales esperadas
- `getRealAsignPEAttribute` — Pevaluacions registradas
- `getGoalNotasPEAttribute` — notas esperadas (pevaluacions × estudiantes)
- `getRealNotasPEAttribute` — notas reales (Boletins con nota no nula)

**RN-10: `list_lapso_final()` para período definitivo.**
Agrega una opción virtual "FINAL" con id = último_id + 1, usada en selectores de período para representar la nota definitiva del año.

### 4.2 Flujo de datos completo

```
[Usuario autenticado con rol is_control]
    │
    ├─(1) GET .../configuraciones/lapsos
    │     └─ LapsoController@index()
    │           ├─ Lapso::all() ← sin orden
    │           └─ view('...lapsos.index')
    │                 ├── table/index.blade.php (DataTable + 3 modales)
    │                 └── menus/index.blade.php
    │
    ├─(2) POST .../lapsos/store
    │     └─ LapsoController@store(Request)
    │           ├─ SIN VALIDACIÓN
    │           ├─ Lapso::create($request->all())
    │           └─ redirect → lapsos.index
    │
    ├─(3) PUT .../lapsos/{id}
    │     └─ LapsoController@update(Request, $id)
    │           ├─ SIN VALIDACIÓN
    │           ├─ findOrFail → fill → save
    │           └─ redirect → lapsos.index
    │
    ├─(4) DELETE .../lapsos/destroy/{id}
    │     └─ LapsoController@destroy($id, Request)
    │           ├─ findOrFail
    │           ├─ Verifica status_delete (profesor_guias vacío AND pevaluacions vacío)
    │           ├─ delete() → soft-delete
    │           └─ JSON o redirect
    │
    ├─(5) GET .../lapsos/census/index/{lapsoId}
    │     └─ LapsoController@census($id)
    │           ├─ Census::whereBetween('created_at', [lapso.date_start_census, lapso.date_end_census])
    │           └─ view('...lapsos.census')
    │
    ├─(6) PUT .../lapsos/census/update/{id}
    │     └─ LapsoController@censusUpdate(Request, $id)
    │           ├─ VALIDA: ci_estudiant unique, lastname, name, gender, date_birth, etc.
    │           └─ Census::findOrFail → fill → save
    │
    └─(7) GET charts/lapso/census/{type}
          └─ Chart\LapsoController@{method}
                ├─ Enrollment::groupBy(town_hall_birth|institution|grado_id)
                └─ JSON { labels: [...], values: [...] }
```

---

## 5. Esquemas de Base de Datos

### 5.1 Tabla `lapsos`

```sql
CREATE TABLE `lapsos` (
  `id`                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code`                VARCHAR(255) NOT NULL COMMENT 'Código del lapso (1, 2, 3)',
  `code_sm`             VARCHAR(255) NOT NULL COMMENT 'Abreviación',
  `name`                VARCHAR(255) NOT NULL COMMENT 'Nombre (Primer Lapso, etc.)',
  `finicial`            DATE NOT NULL COMMENT 'Fecha de inicio',
  `ffinal`              DATE NOT NULL COMMENT 'Fecha de finalización',
  `academic_start_date` DATE NULL COMMENT 'Inicio actividades académicas',
  `date_cutnote`        DATE NULL COMMENT 'Fecha de corte de nota',
  `date_start_census`   DATE NULL COMMENT 'Inicio del censo escolar',
  `time_start_census`   TIME NULL COMMENT 'Hora inicio del censo',
  `date_end_census`     DATE NULL COMMENT 'Fin del censo escolar',
  `time_end_census`     TIME NULL COMMENT 'Hora fin del censo',
  `date_preclosing`     DATE NULL COMMENT 'Fecha de pre-cierre',
  `time_preclosing`     TIME NULL COMMENT 'Hora de pre-cierre',
  `status_last`         ENUM('true','false') DEFAULT 'false' COMMENT 'Último lapso / carga extemporánea',
  `deleted_at`          TIMESTAMP NULL,
  `created_at`          TIMESTAMP NULL,
  `updated_at`          TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.2 Datos típicos

```sql
-- Inserción típica de 3 lapsos
INSERT INTO lapsos (id, code, code_sm, name, finicial, ffinal, status_last, created_at) VALUES
(1, '1', '1', 'Primer Lapso',   '2025-09-15', '2025-12-20', 'false', NOW()),
(2, '2', '2', 'Segundo Lapso',  '2026-01-07', '2026-04-04', 'false', NOW()),
(3, '3', '3', 'Tercer Lapso',   '2026-04-14', '2026-07-31', 'true',  NOW());
```

### 5.3 Tablas relacionadas

```sql
CREATE TABLE `pevaluacions` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `lapso_id`    INT UNSIGNED NOT NULL,
  `profesor_id` INT UNSIGNED NOT NULL,
  `seccion_id`  INT UNSIGNED NOT NULL,
  `pensum_id`   INT UNSIGNED NOT NULL,
  FOREIGN KEY (`lapso_id`) REFERENCES `lapsos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `profesor_guias` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `lapso_id`    INT UNSIGNED NOT NULL,
  `profesor_id` INT UNSIGNED NOT NULL,
  `seccion_id`  INT UNSIGNED NOT NULL,
  FOREIGN KEY (`lapso_id`) REFERENCES `lapsos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE `baremos` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `lapso_id`      INT UNSIGNED NULL,  -- agregado en 2026
  `pestudio_id`   INT UNSIGNED NOT NULL,
  `lapso`         INT NULL,
  FOREIGN KEY (`lapso_id`) REFERENCES `lapsos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### 5.4 Migraciones

| Archivo | Campo(s) |
|---------|----------|
| `2019_10_12_093258_create_lapsos_table` | Creación: id, code, code_sm, name, finicial, ffinal, softDeletes |
| `2022_03_07_141947_add_date_cutnote_to_lapsos` | `date_cutnote` date nullable after ffinal |
| `2023_02_09_201200_add_date_start_census_to_lapsos` | `date_start_census`, `time_start_census` |
| `2023_02_09_201102_add_date_end_census_to_lapsos` | `date_end_census`, `time_end_census` |
| `2023_03_27_090032_add_date_preclosing_to_lapsos` | `date_preclosing`, `time_preclosing` |
| `2023_07_04_150820_add_status_last_to_lapsos` | `status_last` enum('true','false') default 'false' |
| `2024_11_22_092433_add_academic_start_date_to_lapsos` | `academic_start_date` date nullable after ffinal |
| `2026_02_20_132241_add_lapso_id_to_baremos_table` | FK `lapso_id` en baremos |

---

## 6. API REST — Endpoints propuestos

### 6.1 `GET /api/control/lapsos`

Listado de todos los lapsos.

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "code": "1",
      "code_sm": "1",
      "name": "Primer Lapso",
      "finicial": "2025-09-15",
      "ffinal": "2025-12-20",
      "academic_start_date": "2025-09-15",
      "date_cutnote": "2025-11-30",
      "date_start_census": "2025-10-01",
      "date_end_census": "2025-10-31",
      "status_last": false,
      "is_current": true,
      "badge_color": "info",
      "text_color": "primary",
      "preclosing_open": false,
      "census_active": false,
      "profesor_guias_count": 25,
      "pevaluacions_count": 180,
      "created_at": "2025-09-01T10:00:00Z"
    }
  ]
}
```

### 6.2 `GET /api/control/lapsos/{id}`

Detalle del lapso.

```json
{
  "id": 1,
  "code": "1",
  "code_sm": "1",
  "name": "Primer Lapso",
  "finicial": "2025-09-15",
  "ffinal": "2025-12-20",
  "academic_start_date": "2025-09-15",
  "date_cutnote": "2025-11-30",
  "date_start_census": "2025-10-01",
  "time_start_census": "08:00:00",
  "date_end_census": "2025-10-31",
  "time_end_census": "16:00:00",
  "date_preclosing": "2025-12-15",
  "time_preclosing": "12:00:00",
  "status_last": false,
  "is_current": true,
  "badge_color": "info",
  "text_color": "primary",
  "goal_asign_pe": 45,
  "real_asign_pe": 42,
  "goal_notas_pe": 1680,
  "real_notas_pe": 1520
}
```

### 6.3 `POST /api/control/lapsos`

Crear nuevo lapso.

**Validaciones propuestas (no existen en fuente original):**
```json
{
  "code": "required|string|max:10",
  "code_sm": "required|string|max:4",
  "name": "required|string|max:255",
  "finicial": "required|date",
  "ffinal": "required|date|after_or_equal:finicial",
  "academic_start_date": "nullable|date",
  "date_cutnote": "nullable|date|after_or_equal:finicial",
  "date_start_census": "nullable|date",
  "time_start_census": "nullable|date_format:H:i:s",
  "date_end_census": "nullable|date|after_or_equal:date_start_census",
  "time_end_census": "nullable|date_format:H:i:s",
  "date_preclosing": "nullable|date",
  "time_preclosing": "nullable|date_format:H:i:s",
  "status_last": "boolean"
}
```

### 6.4 `GET /api/control/lapsos/current`

Obtiene el lapso activo (reemplaza `Lapso::current()`).

**Query params:** `date` (opcional, formato YYYY-MM-DD, default: today)

**Response:** Mismo formato que `GET /{id}`.

### 6.5 `GET /api/control/lapsos/{id}/census`

Participantes del censo para un lapso.

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "ci_estudiant": "12345678",
      "lastname": "Pérez",
      "name": "Juan",
      "gender": "M",
      "date_birth": "2010-05-15",
      "institution": "U.E. Fray Luis Amigo",
      "grado": { "id": 1, "name": "1ER AÑO" },
      "representant": {
        "ci": "87654321",
        "name": "María Pérez",
        "phone": "04121234567"
      }
    }
  ]
}
```

### 6.6 `GET /api/control/lapsos/census/charts/{type}`

Datos para gráficos del censo.

**Types:** `institution`, `grado`, `municipio`

**Response:**
```json
{
  "labels": ["U.E. Fray Luis Amigo", "U.E. San José", "Otra"],
  "values": [120, 45, 23]
}
```

---

## 7. Especificación de Componentes (NextJS + Tailwind)

### 7.1 Página principal: `ControlLapsosPage`

```
┌───────────────────────────────────────────────────────────────────────────────┐
│  Control de Estudios > Configuraciones > Lapsos Académicos                    │
├───────────────────────────────────────────────────────────────────────────────┤
│  [+ Nuevo Lapso]  [↺ Refrescar]                                              │
├───────────────────────────────────────────────────────────────────────────────┤
│  [Buscar lapso...]                                                             │
├─────┬────────┬──────────┬──────────────┬──────────┬──────────┬──────────┬──────┤
│  #  │ Código │ Nombre   │ Inicio       │ Fin      │ Corte    │ Censo    │ Acc. │
├─────┼────────┼──────────┼──────────────┼──────────┼──────────┼──────────┼──────┤
│  1  │ 1      │🧿 1er L. │ 2025-09-15   │25-12-20  │25-11-30  │10/01-31  │[👁][✏️][🗑]│
│  2  │ 2      │🔵 2do L. │ 2026-01-07   │26-04-04  │26-03-15  │—         │[👁][✏️][🗑]│
│  3  │ 3      │🟢 3er L. │ 2026-04-14   │26-07-31  │26-06-30  │—         │[👁][✏️][🗑]│
├─────┴────────┴──────────┴──────────────┴──────────┴──────────┴──────────┴──────┤
│  Showing 1 to 3 of 3 entries                                                  │
└───────────────────────────────────────────────────────────────────────────────┘
```

### 7.2 Formulario de creación/edición

```
┌─ Datos del Lapso ─────────────────────────────────────────────┐
│  Nombre:          [Primer Lapso                      ]        │
│  Código:          [1       ]   Abreviación: [1  ]            │
├─ Fechas ──────────────────────────────────────────────────────┤
│  Inicio:          [15/09/2025]                                │
│  Fin:             [20/12/2025]                                │
│  Inicio Act. Acad:[15/09/2025]                                │
│  Corte de Nota:   [30/11/2025]                                │
├─ Censo Escolar ───────────────────────────────────────────────┤
│  Inicio Censo:    [01/10/2025]   Hora: [08:00  ]             │
│  Fin Censo:       [31/10/2025]   Hora: [16:00  ]             │
├─ Pre-Cierre ──────────────────────────────────────────────────┤
│  Fecha Pre-Cierre:[15/12/2025]   Hora: [12:00  ]             │
├─ Configuración ───────────────────────────────────────────────┤
│  ¿Último Lapso?:  [No ▼]                                      │
│                                                               │
│  [████ Guardar ████]                                          │
└───────────────────────────────────────────────────────────────┘
```

### 7.3 Árbol de componentes

```
ControlLapsosPage
├── PageHeader (título + breadcrumb)
├── ActionBar (Nuevo lapso → modal, Refrescar)
├── LapsoTable
│   ├── SearchInput
│   ├── TableHeader (código, nombre, fechas, censo, acciones)
│   └── LapsoRow (× N)
│       ├── ShowButton → LapsoDetailsModal
│       ├── EditButton → LapsoEditModal
│       ├── DeleteButton (deshabilitado si tiene pevaluacions o profesor_guias)
│       └── CensusLink → pagina de censo del lapso
├── LapsoCensusPage (ruta hija: /lapsos/{id}/censo)
│   ├── CensusDateInfo (rango de fechas del censo)
│   ├── CensusTable (DataTable + modales)
│   │   └── CensusRow
│   │       ├── EditButton → CensusEditPage
│   │       └── DeleteButton
│   └── CensusChartPanel
│       ├── InstitutionChart (Chart.js pie)
│       ├── GradoChart (Chart.js bar)
│       └── MunicipioChart (Chart.js pie)
├── CreateLapsoModal / EditLapsoModal
│   └── LapsoForm (3 secciones: fechas, censo, pre-cierre)
└── CurrentLapsoBadge (indicador visual del lapso activo)
```

### 7.4 Estados de cada componente

| Componente | Loading | Empty | Error | Success |
|-----------|---------|-------|-------|---------|
| `LapsoTable` | Skeleton 3 filas | "No hay lapsos configurados" | Toast error | DataTable |
| `LapsoForm` | Spinner en submit | Campos vacíos (create) o precargados (edit) | Errores inline (fechas: ffinal >= finicial) | Modal cierra, tabla refresca |
| `CensusTable` | Skeleton | "Sin participantes en este censo" | Toast error | DataTable |
| `ChartPanel` | Placeholder "Cargando gráficos..." | "Sin datos para gráficos" | Toast error | 3 gráficos Chart.js |
| `CurrentLapsoBadge` | N/A | N/A | N/A | Badge "Lapso activo: 1er Lapso" |

---

## 8. Edge Cases y Validaciones

### 8.1 Validaciones del servidor

| Estado actual | Descripción | Riesgo |
|--------------|-------------|--------|
| ❌ CRUD sin validación | `$request->all()` → `create()` | Fechas inválidas, datos corruptos |
| ✅ Census sí valida | Validación explícita en `censusUpdate()` | OK para censo |
| ✅ SoftDeletes | Eliminaciones marcan `deleted_at` | OK |

### 8.2 Edge cases

| Caso | Comportamiento esperado |
|------|------------------------|
| Lapso con pevaluacions → eliminar | Error (status_delete = false) |
| Lapso con profesor_guias → eliminar | Error (status_delete = false) |
| `ffinal` anterior a `finicial` | BD lo acepta — sin validación server-side |
| `status_last` = true en múltiples lapsos | El sistema permite lógicamente, pero debería ser solo 1 |
| `Lapso::current()` sin lapsos en rango | Retorna el primer lapso (`all()->first()`) |
| Fechas de censo invertidas | BD lo acepta — sin validación server-side |
| `edit.blade.php` invocado directamente | Muestra contenido del módulo Banco (está roto) |
| `getStatusActiveCensuAttribute` | Typo en el nombre del método (`Censu` vs `Census`) — consistente internamente |

---

## 9. `Lapso::current()` — El método más usado del sistema

```php
// Determina el lapso activo para cualquier fecha
public static function current($fecha = null)
{
    $fecha = $fecha ?? Carbon::now()->format('Y-m-d');
    
    $lapso = static::where('finicial', '<=', $fecha)
                   ->where('ffinal', '>=', $fecha)
                   ->orderBy('id')
                   ->first();
    
    return $lapso ?? static::all()->first();
}
```

**50+ sitios de uso** incluyen:
- `HomeController@dashboard` (todos los roles)
- `Profesor/Activity/IndexComponent` (Livewire)
- `Planning/Pevaluacion/IndexComponent` (Livewire)
- `Evaluacion/Pevaluacion/IndexComponent` (Livewire)
- `Bienestar/Incident/IndexComponent` (Livewire)
- `Movile/Profesor/IndexComponent` (Livewire)
- `BoletinController` — corte de nota
- `AdministrativaController` — filtros de inscripción

**Importancia en migración:** El endpoint `GET /api/control/lapsos/current` debe ser el primero en implementarse, ya que TODO el resto del sistema depende de él.

---

## 10. Plan de Migración: Laravel/Blade → NextJS + API

### Fase 1: Backend API — Prioridades

| Prioridad | Endpoint | Dependencia |
|-----------|----------|-------------|
| **P0** 🔥 | `GET /api/control/lapsos/current` | Todo el sistema lo necesita |
| P0 | `GET /api/control/lapsos` | Listado |
| P0 | `GET /api/control/lapsos/{id}` | Detalle |
| P0 | `GET /api/control/lapsos/{id}/census` | Censo |
| P0 | `GET /api/control/lapsos/census/charts/{type}` | Gráficos |
| P1 | `POST /api/control/lapsos` | CRUD |
| P1 | `PUT /api/control/lapsos/{id}` | CRUD |
| P1 | `DELETE /api/control/lapsos/{id}` | CRUD |

### Fase 2: Frontend NextJS

| Prioridad | Componente | Descripción |
|-----------|-----------|-------------|
| P0 | `useCurrentLapso` | Hook: obtiene lapso activo |
| P0 | `useLapsos` | Hook: listado CRUD |
| P0 | `CurrentLapsoProvider` | Context/Provider global para el lapso activo |
| P1 | `LapsoTable` | DataTable |
| P1 | `LapsoForm` | Formulario con secciones de fechas |
| P1 | `CensusTable` | DataTable del censo |
| P2 | `CensusChartPanel` | Chart.js para visualización del censo |

### Fase 3: Migración de datos

| Tarea | Detalle |
|-------|---------|
| Normalizar ENUM('true','false') a boolean | `status_last` |
| Corregir typo `statusActiveCensu` → `statusActiveCensus` | Solo si se refactoriza |
| Validar integridad de `finicial <= ffinal` en datos existentes | Auditoría inicial |
| Indexar `finicial`, `ffinal`, `deleted_at` | Performance en `current()` |

### Fase 4: Pruebas

| Tipo | Casos |
|------|-------|
| Unitarias | `Lapso::current()` con fechas en rango/fuera de rango |
| Unitarias | `getStatusEnableCorte()` con fecha antes/después del corte |
| Integración | CRUD completo |
| Integración | Eliminar lapso con pevaluacions (debe fallar) |
| Componente | Formulario de fechas: validación finicial <= ffinal |
| E2E | Flujo: crear lapso → verificar en tabla → editar fechas → eliminar |

---

## 11. Dependencias y librerías

| Librería | Uso en el módulo |
|----------|------------------|
| jQuery + DataTables | Búsqueda, ordenación, paginación |
| Bootstrap 4 | Modales, cards, tablas, formularios |
| FontAwesome 5 | Iconos |
| Chart.js | 3 gráficos del censo (pie + bar) |
| Laravel Collective (Form) | `Form::open()`, `Form::model()`, `Form::select()`, `Form::date()` |
| Livewire | Solo en formulario de censo (`wire:model.defer`) |
| `js/models/default/destroy.js` | Eliminación AJAX |
| Carbon | Manejo de fechas (finicial, ffinal, fechas de censo) |

---

## 12. Estructura de la tabla (resumen visual)

| Columna | Tipo | Requerido | Default |
|---------|------|-----------|---------|
| `id` | INT UNSIGNED AUTO_INCREMENT | ✅ | — |
| `code` | VARCHAR(255) | ✅ | — |
| `code_sm` | VARCHAR(255) | ✅ | — |
| `name` | VARCHAR(255) | ✅ | — |
| `finicial` | DATE | ✅ | — |
| `ffinal` | DATE | ✅ | — |
| `academic_start_date` | DATE | ❌ | NULL |
| `date_cutnote` | DATE | ❌ | NULL |
| `date_start_census` | DATE | ❌ | NULL |
| `time_start_census` | TIME | ❌ | NULL |
| `date_end_census` | DATE | ❌ | NULL |
| `time_end_census` | TIME | ❌ | NULL |
| `date_preclosing` | DATE | ❌ | NULL |
| `time_preclosing` | TIME | ❌ | NULL |
| `status_last` | ENUM('true','false') | ❌ | 'false' |
| `deleted_at` | TIMESTAMP NULL | ❌ | NULL |

---

## 13. Comparativa con módulos anteriores

| Aspecto | Asignaturas | Pestudios | Grados | Secciones | **Lapsos** |
|---------|-------------|-----------|--------|-----------|------------|
| Livewire CRUD | ❌ | ❌ | ❌ | ❌ | ❌ |
| Validación CRUD | ⚠️ Mínima | ❌ | ❌ | ❌ | ❌ |
| Form Requests | ✅ 2 | ❌ | ❌ | ❌ | ❌ |
| SoftDeletes | ⚠️ Comentado | ✅ | ✅ | ❌ Físico | ✅ |
| `status_delete` | Pensums | Grados | Secciones | Inscripc. + ProfGuia | **Pevaluac. + ProfGuia** |
| Sub-módulos | ❌ | ❌ | ❌ | ❌ | **✅ Census (3 charts)** |
| Método crítico sistémico | ❌ | ❌ | ❌ | ❌ | **✅ `Lapso::current()`** |
| `edit.blade.php` roto | ❌ | ❌ | ❌ | ✅ (Banco) | ✅ (Banco) |
| `create.blade.php` ausente | ❌ | ❌ | ⚠️ | ✅ Ausente | ❌ Existe |

---

*Documento generado a partir del análisis del código fuente de SAEFL. Validado contra: `LapsoController.php` (CRUD + Census), `Chart/LapsoController.php`, `Lapso.php` (modelo + trait Lists), migraciones en `database/migrations/backUps/`, todas las vistas Blade del módulo, y rutas en `routes/app/tab/lapso.php` + `routes/app/charts/lapso.php`.*
