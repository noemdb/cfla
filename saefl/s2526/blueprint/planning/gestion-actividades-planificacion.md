# Gestión de Actividades de Planificación — Documento Técnico Completo

> **Versión:** 2.0 — Validada contra código fuente real (Laravel 8.x / Livewire 2.5)
> **Propósito:** Replicación de la funcionalidad en NextJS + API Backend (Laravel o Node)
> **Arquitectura fuente:** Server-rendered (Blade) + Livewire. Destino: SPA (NextJS) con API REST.

---

## 1. Introducción

Este documento describe exhaustivamente el módulo de **Gestión de Actividades de Planificación** del sistema SAEFL (Sistema de Administración Educativa Fray Luis). El módulo permite a **líderes/jefes de área de conocimiento** revisar, comentar, aprobar y generar documentos imprimibles de los planes de actividades que los profesores han diseñado dentro de sus planes de evaluación (`Pevaluacion`).

A diferencia de un CRUD tradicional, este módulo es un **tablero de revisión y control de calidad pedagógica**: el líder no crea actividades, sino que supervisa, retroalimenta y certifica las que el profesor ya registró.

---

## 2. Arquitectura del Dominio

### 2.1 Cadena de dependencias (topología del dato)

```
Peducativo (Plan Educativo Nacional)
  └── Pestudio (Plan de Estudio institucional)
        ├── activities_avr (int|null) ← umbral de calidad textual
        ├── planning_module (boolean) ← activa el módulo de planificación
        └── Pensum (asignatura + grado)
              ├── Asignatura (materia)
              ├── Grado (año escolar)
              └── Pevaluacion (plan de evaluación del profesor)
                    ├── observations (texto) ← observaciones del coordinador
                    ├── Lapso (período escolar)
                    ├── Seccion (sección/aula)
                    ├── Profesor (docente responsable)
                    └── Activity (actividad planificada)
                          ├── teaching (texto) ← campo analizado por calidad
                          ├── comments (texto) ← comentario del jefe de área
                          ├── status (boolean) ← aprobado/en revisión
                          └── Achievement (indicador de logro)
                                ├── name
                                └── weighting (ponderación)
```

El filtro por **líder/jefe de área** atraviesa la relación:
```
Leader → areaConocimiento → campoConocimientos → asignatura → pensum → pevaluacion
```

### 2.2 Árbol de archivos del módulo

```
routes/
  web.php                             ← define grupo /app/plannings con middleware is_planning
  app/plannings.php                   ← require de subrutas
  app/tab/plannings/activities.php    ← 3 rutas GET del módulo

app/
  Http/
    Controllers/Planning/Tab/
      UserDataInitializer.php         ← trait: carga pestudios/peducativos del líder autenticado
      ActivityController.php          ← 3 métodos: index, format, resume
    Livewire/
      Planning/Activity/
        IndexComponent.php            ← componente principal (tabla + filtros + modos)
        updatedTrait.php              ← hooks de cascada dropdown (grado→seccion/pensum)
      Leader/
        CommentComponent.php          ← componente de comentario individual por actividad
  Models/
    app/
      Profesor/
        Activity.php                  ← modelo Activity con lógica de calidad textual
        Achievement.php               ← modelo Achievement (indicadores de logro)
        Pevaluacion.php               ← modelo Pevaluacion (plan de evaluación)
      Pescolar/
        Leader.php                    ← helper de consultas scoped por líder
        Pestudio.php                  ← contiene activities_avr, planning_module
        Pensum.php                    ← join table pestudio + grado + asignatura
        Lapso.php                     ← períodos escolares (current(), fechas)
        Grado.php, Seccion.php, Asignatura.php, Profesor.php

resources/views/
  plannings/activities/
    index.blade.php                   ← layout del módulo (extiende dashboard planning)
    format.blade.php                  ← documento imprimible completo (9 columnas)
    resume.blade.php                  ← resumen ejecutivo (6 columnas)
  livewire/planning/activity/
    index-component.blade.php         ← contenedor Livewire principal
    table/index.blade.php             ← filtros + tabla + tabs de actividades
    partials/
      create.blade.php                ← formulario inline de comentario (desde IndexComponent)
      observation.blade.php           ← formulario de observación de coordinación
  livewire/leader/
    comment-component.blade.php       ← componente de comentario inline por actividad
    partials/comments.blade.php       ← formulario comentario + estado (aprobado/revisión)

database/migrations/backUps/activities/
  2024_08_29_205213_create_activities_table.php
```

---

## 3. Validación contra código fuente

### 3.1 Hallazgos y correcciones al blueprint original

| # | Tópico | Blueprint original | Realidad (código fuente) |
|---|--------|---------------------|--------------------------|
| 1 | Filtro `planning_module` | No mencionado | `Leader::getPevaluacionesForLeader()` filtra con `where('pestudos.planning_module', true)` — sin esto no se muestran planes |
| 2 | Activities Avr | Dice "valor promedio esperado desde el plan de estudio" | Correcto, pero el campo es `Pestudio.activities_avr` (nullable int) y se accede via `$activity->activities_avr` (accessor con 3 `optional()` anidados) |
| 3 | Componente de comentario | Menciona uno solo | Hay **dos** mecanismos: (a) `livewire:leader.comment-component` independiente (con status radio + validación) y (b) parcial `create.blade.php` inline en IndexComponent (más simple, sin status radio) |
| 4 | Radio de estado Aprobado/Revisión | No documentado | El CommentComponent incluye `wire:model.defer="status"` con dos radios: Aprobado (1) y En revisión (0) |
| 5 | Observaciones condicionales | Menciona pero sin detalle | Las observaciones solo se muestran si `$item->observations` tiene valor; el formulario de observación se incluye condicionalmente con `@includeWhen($show, ...)` |
| 6 | Resumen solo con description | Menciona "actividades donde description no es nulo" | Correcto: `whereNotNull('description')` — el resumen solo lista actividades que tienen descripción evaluativa |
| 7 | UserDataInitializer trait | No documentado | El controlador usa un trait que carga `$pestudios`, `$peducativos` del líder, y datos de `Autoridad` |
| 8 | updatedTrait cascading dropdown | No documentado | Al cambiar grado, se recargan secciones y pensums dinámicamente. Al cambiar pestudio, se recargan grados y profesores |
| 9 | Profesors solo activos | No mencionado | `setProfesorLists()` filtra con `where('status_active','true')` |
| 10 | Lapso actual por defecto | Menciona "lapso actual" | Código: `$this->lapso_id = Lapso::current()->id ?? null` — el filtro arranca con el lapso vigente |
| 11 | Pestudio (Plan de Estudio) vs Peducativo (Plan Educativo) | Confunde ambos | Pestudio = plan institucional (ej. "Media General 5to Año"), Peducativo = plan nacional (ej. "Educación Media General") |

### 3.2 Validación de rutas

| Ruta | Método | Controlador | Middleware | Archivo |
|------|--------|-------------|------------|---------|
| `GET /app/plannings/activities/index` | `index()` | `Planning\Tab\ActivityController` | `auth`, `is_planning` | `routes/app/tab/plannings/activities.php` |
| `GET /app/plannings/activities/format/{id}` | `format($id)` | `Planning\Tab\ActivityController` | `auth`, `is_planning` | `routes/app/tab/plannings/activities.php` |
| `GET /app/plannings/activities/resume/{id}` | `resume($id)` | `Planning\Tab\ActivityController` | `auth`, `is_planning` | `routes/app/tab/plannings/activities.php` |

El grupo de rutas se registra en `routes/web.php`:

```php
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Planning'], function () {
    Route::group(['prefix' => 'plannings', 'middleware' => ['is_planning']], function () {
        require (__DIR__ . '/app/plannings.php');
    });
});
```

Y `routes/app/plannings.php` hace `require` de `tab/plannings/activities.php` (entre otros).

---

## 4. Lógica de Negocio — Especificación Completa

### 4.1 Reglas de negocio

**RN-01: Visibilidad por líder.**
Un líder/jefe de área solo ve `Pevaluacion` cuyas asignaturas pertenezcan a su área de conocimiento. La ruta es: `asignatura → campoConocimientos → areaConocimiento → leader_id`.

**RN-02: Módulo de planificación activo.**
Solo se muestran `Pevaluacion` cuyo `Pestudio` tenga `planning_module = true`. Sin esta bandera, el plan de estudio no participa del módulo de planificación.

**RN-03: Lapso actual por defecto.**
Al cargar el módulo, el filtro de lapso se inicializa con el lapso vigente (`Lapso::current()`, que calcula el lapso cuya fecha `finicial/ffinal` contiene la fecha actual).

**RN-04: Calidad textual.**
Se cuenta el número de palabras en el campo `teaching` que tengan **más de 3 letras** (excluyendo caracteres no alfabéticos). El umbral (`activities_avr`) se define por `Pestudio`. La comparación produce:
- `teachingWordsMayorCount() > activities_avr` → indicador success (verde, flecha arriba)
- `teachingWordsMayorCount() == activities_avr` → indicador warning (azul, flecha igual)
- `teachingWordsMayorCount() < activities_avr` → indicador danger (amarillo, flecha abajo)
- `activities_avr is null` → no se muestra indicador

**RN-05: Indicador global de planificación.**
Cuando se selecciona un profesor específico en los filtros, se muestra un alerta con el porcentaje de actividades que superan el promedio de palabras. Umbrales:
- ≥ 50% → success (verde, "Buen desempeño")
- ≥ 25% → warning (amarillo, "Desempeño moderado")
- < 25% → danger (rojo, "Atención")

**RN-06: Aprobación binaria.**
La actividad puede estar en dos estados: `status = 1` (Aprobado) o `status = 0` (En revisión). Se refleja visualmente con color (verde/amarillo) e icono (check/—).

**RN-07: Resumen solo con descripción.**
La vista `resume` solo incluye actividades cuyo campo `description` NO sea nulo. Las actividades sin descripción evaluativa se excluyen del resumen ejecutivo.

**RN-08: Profesores activos.**
La lista de profesores en el filtro solo incluye aquellos con `status_active = true`.

**RN-09: Observaciones del coordinador.**
El campo `observations` (en `Pevaluacion`) se gestiona independientemente de los comentarios de actividades. Lo escribe el coordinador de evaluación, no el jefe de área.

**RN-10: Paginación especial con "Todos".**
Cuando se selecciona `paginate = 9999`, el sistema devuelve todos los registros pero los envuelve en un `LengthAwarePaginator` para mantener la interfaz de paginación consistente.

### 4.2 Flujo de datos completo

```
[Usuario autenticado con rol is_planning]
    │
    ├─(1) GET /app/plannings/activities/index
    │     └─ ActivityController@index()
    │           ├─ initializeUserData() [trait]
    │           │    ├─ Auth::user()
    │           │    ├─ Leader::getPestudioForLeader($userId)
    │           │    └─ Leader::getPeducativosForLeader($userId)
    │           ├─ Profesor::getProfesorForLeaderId($userId)
    │           ├─ Lapso::all()
    │           ├─ Lapso::current()
    │           └─ view('plannings.activities.index', compact(...))
    │                 └─ <livewire:planning.activity.index-component/>
    │                       │
    │                       ├─ mount()
    │                       │    ├─ User::find(Auth::id())
    │                       │    ├─ Leader::getPestudio/Peducativos/Grados/Pensums ForLeader
    │                       │    ├─ Profesor::orderBy()->where('status_active',true)
    │                       │    ├─ Lapso::select('name','id')
    │                       │    └─ Pevaluacion::COLUMN_COMMENTS
    │                       │
    │                       ├─ render()
    │                       │    ├─ array_filter($filters) — elimina nulos/vacíos
    │                       │    ├─ Leader::getPevaluacionesForLeader($leaderId, $filters, true, $paginate)
    │                       │    │    ├─ with('pensum.asignatura.campoConocimientos.areaConocimiento')
    │                       │    │    ├─ with('seccion.grado', 'profesor', 'lapso', 'pensum')
    │                       │    │    ├─ withCount('activities')
    │                       │    │    ├─ whereHas('pensum.pestudio', planning_module = true)
    │                       │    │    ├─ when(seccion_id) / grado_id / lapso_id / pestudio_id / profesor_id
    │                       │    │    ├─ when(status_activities = SI → having activities_count > 0)
    │                       │    │    ├─ when(status_activities = NO → having activities_count = 0)
    │                       │    │    ├─ orderBy('created_at', 'desc')
    │                       │    │    └─ paginate($perPage)
    │                       │    └─ view('livewire.planning.activity.index-component')
    │                       │
    │                       ├─ [INTERACCIÓN] updatedGradoId()
    │                       │    ├─ Grado::find($value)
    │                       │    ├─ Seccion::list_seccion_grado($gradoId)
    │                       │    └─ Leader::getPensumsForLeader($leaderId, $gradoId)
    │                       │
    │                       ├─ [INTERACCIÓN] updatedPestudioId()
    │                       │    ├─ Grado::list_pestudio_grado($pestudioId)
    │                       │    └─ Profesor::list_profesors_pestudio($pestudioId)
    │                       │
    │                       ├─ [ACCIÓN] createObservation($id)
    │                       │    ├─ Pevaluacion::findOrFail($id)
    │                       │    ├─ $this->observations = $pevaluacion->observations
    │                       │    └─ $this->modeObservation = true
    │                       │
    │                       ├─ [ACCIÓN] saveObservation()
    │                       │    ├─ $pevaluacion->observations = $this->observations
    │                       │    ├─ $pevaluacion->save()
    │                       │    └─ reset modes → modeIndex
    │                       │
    │                       ├─ [ACCIÓN] setModeComment($activityId)
    │                       │    ├─ Activity::findOrFail($activityId)
    │                       │    ├─ $this->comments = $activity->comments
    │                       │    ├─ $this->status = $activity->status
    │                       │    └─ $this->modeComments = true
    │                       │
    │                       └─ [ACCIÓN] saveComent()
    │                            ├─ $activity->comments = $this->comments
    │                            ├─ $activity->status = $this->status
    │                            ├─ $activity->save()
    │                            └─ reset modes → modeIndex
    │
    ├─(2) GET /app/plannings/activities/format/{id}
    │     └─ ActivityController@format($id)
    │           ├─ Pevaluacion::findOrFail($id)
    │           ├─ Activity::where('pevaluacion_id', $id)->orderBy('finicial')
    │           ├─ Institucion::orderBy('created_at','DESC')->first()
    │           └─ view('plannings.activities.format', compact(...))
    │                 └─ Renderiza tabla de 9 columnas:
    │                      N° | Fecha | Contenido (topic+thematic+references)
    │                      | Enseñanza | Aprendizaje | Act.Eval.
    │                      | Ind.Logros | ODS/Sistematización | Comentarios [Jef.Área]
    │
    └─(3) GET /app/plannings/activities/resume/{id}
          └─ ActivityController@resume($id)
                ├─ Pevaluacion::findOrFail($id)
                ├─ Activity::whereNotNull('description')->where('pevaluacion_id',$id)->orderBy('finicial')
                ├─ Institucion::orderBy('created_at','DESC')->first()
                └─ view('plannings.activities.resume', compact(...))
                      └─ Renderiza tabla de 6 columnas:
                           N° | Fecha | Contenido (solo references)
                           | Act.Eval. | Ind.Logros | ODS/Sistematización
```

### 4.3 Máquina de estados del IndexComponent

```
┌─────────────┐
│  modeIndex  │ ← estado inicial (true)
│  (tabla)    │
└──────┬──────┘
       │
       │ createObservation($id)
       ├──────────────────────────────> ┌──────────────────┐
       │                                │ modeObservation  │
       │                                │ (textarea obs.)  │
       │                                │ saveObservation()│
       │                                │ ──→ modeIndex    │
       │                                └──────────────────┘
       │
       │ setModeComment($activityId) o CommentComponent::setModeComment()
       ├──────────────────────────────> ┌──────────────────┐
       │                                │ modeComments     │
       │                                │ (comentario +    │
       │                                │  status radio)   │
       │                                │ saveComent()     │
       │                                │ ──→ modeIndex    │
       │                                └──────────────────┘
       │
       │ close()
       └──────────────────────────────> ┌──────────────────┐
                                        │ Todos false      │
                                        │ modeIndex = true │
                                        └──────────────────┘
```

Los modos son **mutuamente excluyentes**: `close()` desactiva todos los modos antes de activar uno nuevo.

---

## 5. Esquemas de Base de Datos

### 5.1 Tabla `activities`

```sql
CREATE TABLE `activities` (
  `id`               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pevaluacion_id`   BIGINT UNSIGNED NOT NULL COMMENT 'Plan de Evaluación',
  `finicial`         DATE NOT NULL COMMENT 'Fecha Inicial',
  `ffinal`           DATE NOT NULL COMMENT 'Fecha Final',
  `topic`            TEXT NULL COMMENT 'Tema generador y Énfasis',
  `thematic`         TEXT NULL COMMENT 'Tejido temático / Tema Indispensable',
  `references`       TEXT NOT NULL COMMENT 'Referentes teórico prácticos y éticos',
  `teaching`         TEXT NOT NULL COMMENT 'Enseñanza/Actividad Globalizada',
  `learning`         TEXT NOT NULL COMMENT 'Aprendizaje',
  `description`      TEXT NULL COMMENT 'Actividad Evaluativa',
  `observations`     TEXT NOT NULL COMMENT 'ODS / Sistematización',
  `comments`         TEXT NULL COMMENT 'Comentarios del Jefe de Área',
  `status`           TINYINT(1) DEFAULT 0 COMMENT 'Aprobación (1=Aprobado, 0=En revisión)',
  `created_at`       TIMESTAMP NULL,
  `updated_at`       TIMESTAMP NULL,
  CONSTRAINT `fk_activities_pevaluacion` FOREIGN KEY (`pevaluacion_id`)
    REFERENCES `pevaluacions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.2 Tabla `pevaluacions`

```sql
CREATE TABLE `pevaluacions` (
  `id`            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `profesor_id`   INT UNSIGNED NOT NULL COMMENT 'Profesor',
  `pensum_id`     BIGINT UNSIGNED NOT NULL COMMENT 'Pensum (Asignatura+Grado)',
  `lapso_id`      INT UNSIGNED NOT NULL COMMENT 'Lapso/Período',
  `seccion_id`    BIGINT UNSIGNED NULL COMMENT 'Sección (added later)',
  `objetivo`      VARCHAR(255) NULL COMMENT 'Objetivo General',
  `description`   VARCHAR(255) NULL COMMENT 'Descripción del plan',
  `observations`  TEXT NULL COMMENT 'Observaciones del Coordinador de Evaluación',
  `category`      VARCHAR(255) NULL,
  `deleted_at`    TIMESTAMP NULL,
  `created_at`    TIMESTAMP NULL,
  `updated_at`    TIMESTAMP NULL,
  CONSTRAINT `fk_pevaluacions_profesor` FOREIGN KEY (`profesor_id`)
    REFERENCES `profesors`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pevaluacions_pensum` FOREIGN KEY (`pensum_id`)
    REFERENCES `pensums`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pevaluacions_lapso` FOREIGN KEY (`lapso_id`)
    REFERENCES `lapsos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.3 Tabla `achievements`

```sql
CREATE TABLE `achievements` (
  `id`                           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `activity_id`                  BIGINT UNSIGNED NOT NULL COMMENT 'Actividad',
  `name`                         VARCHAR(255) NOT NULL COMMENT 'Nombre del indicador',
  `weighting`                    DECIMAL(8,2) NULL COMMENT 'Ponderación',
  `status_quantitative_weighting` TINYINT(1) DEFAULT 0 COMMENT 'Es ponderado (cuantitativo)',
  `created_at`                   TIMESTAMP NULL,
  `updated_at`                   TIMESTAMP NULL,
  CONSTRAINT `fk_achievements_activity` FOREIGN KEY (`activity_id`)
    REFERENCES `activities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.4 Campos relevantes en `pestudios`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | BIGINT UNSIGNED | PK |
| `peducativo_id` | BIGINT UNSIGNED | FK → `peducativos.id` |
| `manager_id` | BIGINT UNSIGNED | FK → `users.id` (coordinador) |
| `name` | VARCHAR(255) | Nombre del plan de estudio |
| `planning_module` | TINYINT(1) DEFAULT 0 | Habilita el módulo de planificación |
| `activities_avr` | INT NULL | Palabras promedio esperadas por actividad |

### 5.5 Tablas relacionadas (FK chain)

| Tabla | PK | FK relevante |
|-------|----|-------------|
| `area_conocimientos` | `id` | `leader_id` → `users.id` |
| `campo_conocimientos` | `id` | `area_conocimiento_id` → `area_conocimientos.id` |
| `asignaturas` | `id` | `campo_conocimiento_id` → `campo_conocimientos.id` |
| `pensums` | `id` | `asignatura_id` → `asignaturas.id`, `grado_id` → `grados.id`, `pestudio_id` → `pestudios.id` |
| `seccions` | `id` | `grado_id` → `grados.id` |
| `profesors` | `id` | `id` referenciado por `pevaluacions.profesor_id` |
| `lapsos` | `id` | Período con `finicial`, `ffinal` |

---

## 6. Modelo de Datos (API REST para exportación)

### 6.1 Endpoints propuestos para backend (Laravel o Node)

#### `GET /api/planning/activities`

Lista paginada de `Pevaluacion` con actividades, filtrada por líder.

**Query params:**
| Parámetro | Tipo | Default | Descripción |
|-----------|------|---------|-------------|
| `leader_id` | int | requerido | ID del líder autenticado |
| `pestudio_id` | int | null | Filtro por plan de estudio |
| `profesor_id` | int | null | Filtro por profesor |
| `grado_id` | int | null | Filtro por grado |
| `seccion_id` | int | null | Filtro por sección |
| `lapso_id` | int | current | Filtro por lapso |
| `status_activities` | enum | null | `SI` (con actividades), `NO` (sin actividades) |
| `per_page` | int | 10 | Elementos por página (usar -1 para todos) |
| `page` | int | 1 | Número de página |

**Response (200):**
```json
{
  "data": [
    {
      "id": 45,
      "profesor": { "id": 12, "fullname": "PÉREZ JUAN", "email": "..." },
      "asignatura": { "id": 8, "name": "MATEMÁTICA", "code": "MT" },
      "grado": { "id": 3, "name": "5TO AÑO", "code": "5A" },
      "seccion": { "id": 15, "name": "A" },
      "lapso": { "id": 2, "name": "II MOMENTO" },
      "activities_count": 4,
      "observations": "Revisar contenidos...",
      "activities": [
        {
          "id": 201,
          "finicial": "2025-02-03",
          "ffinal": "2025-02-10",
          "topic": "Funciones trigonométricas",
          "thematic": "Razones trigonométricas",
          "references": "Textos Santillana 5to",
          "teaching": "Explicación del docente...",
          "learning": "Resolución de ejercicios...",
          "description": "Evaluación escrita",
          "observations": "ODS 4: Educación de calidad",
          "comments": "Ajustar nivel de dificultad",
          "status": true,
          "teaching_words_count": 12,
          "activities_avr": 10,
          "word_quality": "above_average",
          "achievements": [
            { "id": 1, "name": "Identifica funciones", "weighting": 4.0 }
          ]
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 10,
    "total": 42
  },
  "global_indicator": {
    "total_activities": 16,
    "above_average": 11,
    "percentage": 68.8,
    "level": "success",
    "message": "Buen desempeño: la mayoría de las actividades superan el promedio de palabras esperado."
  }
}
```

> **Nota:** `global_indicator` solo se incluye cuando `profesor_id` está presente.

#### `PUT /api/planning/activities/{id}/comment`

Actualiza comentario y estado de una actividad.

**Request body:**
```json
{
  "comments": "Actividad aprobada, buen nivel de detalle.",
  "status": true
}
```

**Response (200):**
```json
{
  "id": 201,
  "comments": "Actividad aprobada, buen nivel de detalle.",
  "status": true,
  "updated_at": "2025-02-15T10:30:00Z"
}
```

#### `PUT /api/planning/pevaluaciones/{id}/observations`

Actualiza observaciones del coordinador en un plan de evaluación.

**Request body:**
```json
{
  "observations": "El plan requiere ajustes en los contenidos del II momento."
}
```

**Response (200):**
```json
{
  "id": 45,
  "observations": "El plan requiere ajustes en los contenidos del II momento.",
  "updated_at": "2025-02-15T10:30:00Z"
}
```

#### `GET /api/planning/pestudios?leader_id={id}`

Lista de planes de estudio disponibles para el líder.

#### `GET /api/planning/profesors?leader_id={id}&pestudio_id={id}`

Lista de profesores filtrados (opcionalmente por plan de estudio).

#### `GET /api/planning/grados?leader_id={id}&pestudio_id={id}`

Lista de grados (opcionalmente filtrados por plan de estudio).

#### `GET /api/planning/secciones?grado_id={id}`

Lista de secciones de un grado.

#### `GET /api/planning/lapsos`

Lista de lapsos disponibles.

#### `GET /api/planning/activities/format/{id}`

Genera el documento de formato (ideal como endpoint que devuelve HTML renderizado o un PDF generado).

#### `GET /api/planning/activities/resume/{id}`

Genera el resumen ejecutivo del plan.

### 6.2 Validaciones del lado del servidor

| Campo | Regla | Código fuente |
|-------|-------|---------------|
| `comments` | `nullable\|string\|max:65535` | `CommentComponent::saveComent()` |
| `status` | `required\|boolean` | `CommentComponent::saveComent()` |
| `observations` | `nullable\|string` | Sin validación explícita en IndexComponent (solo asignación directa) |
| `pevaluacion_id` en rutas | Debe existir en `pevaluacions` | `findOrFail($id)` |
| `activity_id` en comentarios | Debe existir en `activities` | `findOrFail($activitie_id)` |
| `profesor_id` en filtros | `status_active = true` | `setProfesorLists()` |

---

## 7. Lógica de Calidad Textual (Cómputo de Palabras Significativas)

### 7.1 Algoritmo `teachingWordsMayorCount()`

```php
public function teachingWordsMayorCount(int $num = 3): int
{
    if (empty($this->teaching)) return 0;

    // 1. Normalizar: eliminar caracteres no alfabéticos (excepto espacios)
    $texto = preg_replace('/[^\p{L}\s]/u', '', $this->teaching);

    // 2. Separar por espacios (uno o más)
    $palabras = preg_split('/\s+/u', trim($texto), -1, PREG_SPLIT_NO_EMPTY);

    // 3. Contar palabras cuya longitud > $num (default 3)
    return count(array_filter($palabras, fn(string $p) => mb_strlen($p) > $num));
}
```

**Implementación en Node.js/TypeScript:**
```typescript
function teachingWordsMayorCount(teaching: string, minLength: number = 3): number {
    if (!teaching) return 0;
    // Eliminar caracteres no alfabéticos (excepto espacios)
    const normalized = teaching.replace(/[^\p{L}\s]/gu, '');
    // Separar por espacios
    const words = normalized.trim().split(/\s+/u).filter(Boolean);
    // Contar palabras con longitud > minLength
    return words.filter(w => w.length > minLength).length;
}
```

### 7.2 Accessor `activities_avr`

```php
public function getActivitiesAvrAttribute(): ?int
{
    return optional(
        optional(
            optional($this->pevaluacion)->pensum
        )->pestudio
    )->activities_avr;
}
```

**Equivalente JavaScript (asumiendo objeto anidado):**
```typescript
function getActivitiesAvr(activity: any): number | null {
    return activity?.pevaluacion?.pensum?.pestudio?.activities_avr ?? null;
}
```

### 7.3 Lógica de indicador visual (implementación en frontend)

```typescript
type QualityLevel = 'above_average' | 'at_average' | 'below_average' | 'no_data';

function getWordQuality(wordCount: number | null, avr: number | null): {
    level: QualityLevel;
    icon: string;
    badgeClass: string;
    title: string;
} {
    if (avr === null || wordCount === null) {
        return { level: 'no_data', icon: '', badgeClass: '', title: '' };
    }
    if (wordCount < avr) {
        return {
            level: 'below_average',
            icon: 'fa-arrow-down',
            badgeClass: 'badge-warning',
            title: `Palabras (${wordCount}) por debajo del promedio (${avr})`,
        };
    }
    if (wordCount === avr) {
        return {
            level: 'at_average',
            icon: 'fa-minus',
            badgeClass: 'badge-primary',
            title: `Palabras (${wordCount}) igual al promedio (${avr})`,
        };
    }
    return {
        level: 'above_average',
        icon: 'fa-arrow-up',
        badgeClass: 'badge-success',
        title: `Palabras (${wordCount}) por encima del promedio (${avr})`,
    };
}

function getGlobalIndicator(total: number, above: number): {
    percentage: number;
    level: 'success' | 'warning' | 'danger';
    message: string;
} | null {
    if (total === 0) return null;
    const pct = Math.round((above / total) * 1000) / 10;
    if (pct >= 50) {
        return {
            percentage: pct, level: 'success',
            message: 'Buen desempeño: la mayoría de las actividades superan el promedio de palabras esperado.',
        };
    }
    if (pct >= 25) {
        return {
            percentage: pct, level: 'warning',
            message: 'Desempeño moderado: una parte de las actividades alcanza el promedio.',
        };
    }
    return {
        percentage: pct, level: 'danger',
        message: 'Atención: pocas actividades superan el promedio de palabras esperado.',
    };
}
```

---

## 8. Especificación de Componentes (NextJS + Tailwind)

### 8.1 Página principal: `PlanningActivitiesPage`

```
┌─────────────────────────────────────────────────────────────┐
│  Módulo Plan de Actividades                      Diseñado   │
│  Planes Educativos: Media General 5to Año || Media General  │
├─────────────────────────────────────────────────────────────┤
│  [Plan Estudio ▼] [Profesor ▼] [Grado/Año ▼] [Sección ▼]   │
│  [Momento ▼] [Actividades ▼] [10 ▼] [🔄]                   │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────────────────────────────────────────────────┐   │
│  │ 📊 Indicador — Actividades sobre el Promedio        │   │
│  │ 16 actividades, 11 superan promedio (palabras >3)   │   │
│  │ 🟢 Buen desempeño                        [↑ 68.8%]  │   │
│  └──────────────────────────────────────────────────────┘   │
├──────────┬──────────┬──────────┬────────────────────┬───────┤
│ N°       │ Asig/Grad│ Cant.Act │ Actividades        │ Acc.  │
│          │ /Sección │          │                    │       │
├──────────┼──────────┼──────────┼────────────────────┼───────┤
│ 1        │ MATEMÁTI │ 4        │ [1✓][2][3][4]     │ [Obs] │
│          │ CA       │          │ T.Generador/...    │ [PDF] │
│          │ 5TO AÑO  │          │ Comentario [J.Área]:│ [PDF] │
│          │ A [II M] │          │ "Ajustar..."       │       │
│          │          │          │ 🟢 Aprobado  [✏️]  │       │
├──────────┼──────────┼──────────┼────────────────────┼───────┤
│ 2        │ LENGUA   │ 0 🔴    │ No hay actividades │ [Obs] │
│          │ 5TO AÑO  │          │                    │ [PDF] │
│          │ B [II M] │          │                    │       │
├──────────┴──────────┴──────────┴────────────────────┴───────┤
│  ← 1 2 3 ... 5 →                                           │
└─────────────────────────────────────────────────────────────┘
```

### 8.2 Árbol de componentes

```
PlanningActivitiesPage
├── FilterBar
│   ├── SelectField (pestudio_id)
│   ├── SelectField (profesor_id)
│   ├── SelectField (grado_id)       ← onChange → carga secciones y pensums
│   ├── SelectField (seccion_id)
│   ├── SelectField (lapso_id)
│   ├── SelectField (status_activities)
│   ├── SelectField (per_page)
│   └── RefreshButton
├── GlobalIndicator                  ← solo cuando profesor_id está presente
│   ├── PercentageBadge
│   └── QualityMessage
├── PevaluacionTable
│   └── PevaluacionRow (por cada item)
│       ├── SubjectGradeCell
│       ├── ActivityCountCell
│       ├── ActivityTabs
│       │   └── ActivityTab (por cada actividad)
│       │       ├── WordQualityBadge  ← icono + tooltip
│       │       └── CommentDisplay
│       │           ├── StatusBadge (Aprobado/En revisión)
│       │           └── EditButton → CommentModal
│       └── ActionButtons
│           ├── ObservationButton → ObservationModal
│           ├── ResumeButton → nueva pestaña
│           └── FormatButton → nueva pestaña
├── ObservationModal
│   ├── ProfesorInfo
│   ├── TextArea (observations)
│   ├── SaveButton
│   └── CloseButton
├── CommentModal                     ← desde CommentComponent
│   ├── ActivityDetail
│   ├── TextArea (comments)
│   ├── RadioGroup (status: Aprobado/En revisión)
│   ├── SaveButton
│   └── CloseButton
└── Pagination
```

### 8.3 Estados de cada componente

| Componente | Loading | Empty | Error | Success |
|-----------|---------|-------|-------|---------|
| `FilterBar` | Deshabilitar selects | Mostrar opciones disponibles | Badge error en filtro | Normal |
| `GlobalIndicator` | Skeleton | No renderizar | No renderizar | Badge con % |
| `PevaluacionTable` | Skeleton filas | "No hay planes de evaluación para este líder" | "Error al cargar datos" + retry | Tabla normal |
| `ActivityTabs` | Spinner en tab | "No hay actividades registradas" | Badge en tab | Tabs con datos |
| `CommentModal` | Botón guardar deshabilitado + spinner | N/A | Toast error + mantener datos | Toast éxito + cerrar |
| `ObservationModal` | Botón guardar deshabilitado + spinner | N/A | Toast error | Toast éxito + cerrar |
| `Pagination` | Links deshabilitados | N/A | Ocultar | Links funcionales |

---

## 9. Reglas para exportación de PDF/HTML imprimible

El sistema actual genera documentos HTML puro con CSS para impresión (sin librería PDF en servidor). Para replicar:

**Opción A: HTML a PDF vía librería (recomendado para NextJS)**
```typescript
// Usar @react-pdf/renderer o puppeteer, o un servicio como pdfcrowd
import { generatePdf } from '@/lib/pdf';

// Estructura del documento format (9 columnas)
const FormatDocument = ({ pevaluacion, activities, institucion, fecha }) => (
  <div className="pdf-format">
    <header>
      <h1>{institucion.name}</h1>
      <h2>COORD. ACADEMICA - Plan de Actividades</h2>
      <p>PE: {pevaluacion.pestudio.name} - {asignatura.name} {grado.name} {seccion.name} - {lapso.name} - {fecha}</p>
    </header>
    <table className="pdf-table">
      <thead>
        <tr>
          <th>N°</th>
          <th>Fecha</th>
          <th>Contenido</th>
          <th>Enseñanza</th>
          <th>Aprendizaje</th>
          <th>Act.Eval.</th>
          <th>Ind.Logros</th>
          <th>ODS/Sist.</th>
          <th>Comentarios</th>
        </tr>
      </thead>
      <tbody>
        {activities.map((act, i) => (
          <tr key={act.id}>
            <td>{i + 1}</td>
            <td>{act.finicial} al {act.ffinal}</td>
            <td>{/* topic + thematic + references */}</td>
            <td>{act.teaching}</td>
            <td>{act.learning}</td>
            <td>{act.description}</td>
            <td>{act.achievements.map(a => `${a.name} [${a.weighting}]`)}</td>
            <td>{act.observations}</td>
            <td>{act.comments}<br/>{act.status ? 'APROBADO' : 'EN REVISION'}</td>
          </tr>
        ))}
      </tbody>
    </table>
    <footer>
      <p>Elaborado por: {userName} - SAEFL: {fecha}</p>
    </footer>
  </div>
);
```

**Opción B: Endpoint backend que renderiza HTML y lo convierte a PDF**
```php
// Laravel: usar barryvdh/laravel-dompdf (ya existe en el proyecto)
// Node: usar puppeteer o pdfkit
Route::get('/api/planning/activities/format/{id}/pdf', function ($id) {
    $pevaluacion = Pevaluacion::with('activities.achievements')->findOrFail($id);
    $html = view('pdf.planning-format', compact('pevaluacion'))->render();
    $pdf = PDF::loadHTML($html);
    return $pdf->download("plan-actividades-{$id}.pdf");
});
```

**Opción C (NextJS nativo):**
```typescript
// pages/api/planning/activities/format/[id]/pdf.ts
// Usar playwright-core o chromium para server-side PDF generation
// o un servicio externo como print Friendly
```

---

## 10. Plan de Migración: Laravel/Livewire → NextJS + API

### Fase 1: Backend API (backend existente o nuevo)

| Prioridad | Endpoint | Descripción | Depende de |
|-----------|----------|-------------|------------|
| P0 | `GET /api/planning/pestudios` | Lista de planes de estudio del líder | Autenticación |
| P0 | `GET /api/planning/profesors` | Profesores (con filtro opcional por pestudio) | Autenticación |
| P0 | `GET /api/planning/grados` | Grados (con filtro opcional por pestudio) | Pestudio |
| P0 | `GET /api/planning/secciones?grado_id=` | Secciones de un grado | Grado |
| P0 | `GET /api/planning/lapsos` | Lapsos disponibles | — |
| P0 | `GET /api/planning/activities` | Lista paginada con filtros | Todos los anteriores |
| P1 | `PUT /api/planning/activities/{id}/comment` | Actualizar comentario + status | Activity |
| P1 | `PUT /api/planning/pevaluaciones/{id}/observations` | Actualizar observaciones | Pevaluacion |
| P2 | `GET /api/planning/activities/format/{id}` | Documento formato (HTML o JSON) | Pevaluacion |
| P2 | `GET /api/planning/activities/resume/{id}` | Documento resumen (HTML o JSON) | Pevaluacion |

### Fase 2: Frontend NextJS

| Prioridad | Componente | Descripción |
|-----------|------------|-------------|
| P0 | `usePlanningActivities` | Hook personalizado: datos, filtros, paginación, mutaciones |
| P0 | `FilterBar` | Barra de filtros con cascada dropdown |
| P0 | `PevaluacionTable` | Tabla paginada con tabs de actividades |
| P1 | `CommentModal` | Modal de comentario + estado |
| P1 | `ObservationModal` | Modal de observaciones |
| P1 | `GlobalIndicator` | Indicador global de calidad textual |
| P2 | `FormatDocument` | Documento imprimible (9 columnas) |
| P2 | `ResumeDocument` | Documento resumen (6 columnas) |

### Fase 3: Autenticación y autorización

- El middleware `is_planning` debe migrarse a un guard o policy en el backend API
- El rol se determina por `$user->IsPlanning()` en el modelo User actual
- En NextJS: proteger las rutas con `middleware.ts` que verifique el token JWT y el rol

### Fase 4: Pruebas

- Unitarias: algoritmo `teachingWordsMayorCount()`, cálculo de `getGlobalIndicator()`
- Integración: cada endpoint API con casos borde (filtros vacíos, profesor sin actividades, etc.)
- Componentes: Estados vacío, error, loading, éxito
- E2E: Flujo completo: login → filtros → ver tabla → abrir comentario → guardar → ver cambio

---

## 11. Validaciones y casos borde

| Caso | Comportamiento esperado |
|------|------------------------|
| Profesor sin actividades registradas | Tab muestra count 0 en rojo, sin tabs, mensaje "No hay datos de actividades" |
| Plan de estudio sin `planning_module` | No aparece en ningún resultado (filtro invisible) |
| `activities_avr` nulo | Indicador de calidad no se muestra para esa actividad |
| Actividad sin `teaching` | `teachingWordsMayorCount()` retorna 0 |
| Actividad sin `description` | Excluida del resumen, pero visible en formato completo |
| `paginate = 9999` | Devuelve todos los registros en una sola página |
| Lapso sin fechas configuradas | `Lapso::current()` puede retornar null; se muestra filtro sin selección |
| Grado sin secciones | `list_seccion` vacío; dropdown sin opciones |
| Guardar comentario sin cambios | Persiste el valor existente (no hay detección de cambios) |
| Líder sin planes de estudio asignados | Tabla vacía, mensaje "No hay planes de evaluación para este líder" |
| Observación guardada sin texto | Se persiste como string vacío (no hay validación de required) |

---

## 12. Checklist de validación del blueprint original

- [x] Rutas correctas y nombres de ruta validados
- [x] Controlador y métodos confirmados contra código fuente
- [x] Componentes Livewire y sus métodos validados
- [x] Modelos, relaciones y atributos verificados
- [x] Migración `activities` leída y documentada
- [x] Migración `pevaluacions` leída de backup
- [x] Algoritmo `teachingWordsMayorCount()` extraído con precisión
- [x] Filtro `planning_module` identificado y documentado (omisión crítica corregida)
- [x] Máquina de estados de modos documentada
- [x] Lógica de cascada dropdown documentada (updatedTrait)
- [x] Diferencia entre CommentComponent y partial create.blade.php documentada
- [x] Regla de negocio `status_activities` (SI/NO con HAVING count) documentada
- [x] Manejo de `paginate = 9999` documentado
- [x] Observaciones condicionales documentadas
- [x] ENDPOINTS API propuestos para migración
- [x] Algoritmos equivalentes en TypeScript/Node
- [x] Plan de migración por fases
- [x] Casos borde documentados
- [x] Estados de componentes UI (loading/empty/error/success)

---

*Documento generado a partir del análisis del código fuente de SAEFL. Validado contra: `ActivityController.php`, `IndexComponent.php`, `updatedTrait.php`, `CommentComponent.php`, `Activity.php`, `Pevaluacion.php`, `Leader.php`, `Pestudio.php`, `routes/web.php`, las vistas Blade, y la migración `2024_08_29_205213_create_activities_table.php`.*
