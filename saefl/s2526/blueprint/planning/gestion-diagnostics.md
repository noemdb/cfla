# Gestión de Diagnóstico Educativo (Diagnostics) — Documento Técnico Completo

> **Versión:** 1.0 — Validada contra código fuente real (Laravel 8.x / Livewire 2.5)
> **Módulo:** `plannings.diagnostics` — Panel de seguimiento y análisis de diagnóstico pedagógico.
> **Propósito:** Replicación en NextJS + API REST (Laravel o Node).
> **Arquitectura fuente:** Server-rendered (Blade) + Livewire con tabs, modales, integración multi-IA.

---

## 1. Introducción

El módulo **Diagnóstico Educativo** (Diagnostics) dentro del módulo de Planificación (`is_planning`) proporciona un **panel de jefatura** para el seguimiento, análisis y generación de informes del diagnóstico pedagógico institucional. Es el módulo más complejo del sistema de planificación, con **5 tabs** que cubren:

- **Dashboard** — Métricas globales, gráficos de precisión, sesiones recientes, progreso por pensum
- **Preguntas** — Exploración y filtrado del banco de preguntas del instrumento diagnóstico
- **Sesiones** — Sesiones por estudiante, generación de informes vía IA (DeepSeek/Qwen/Gemini/OpenRouter)
- **Diagnósticos** — CRUD de tipos/ciclos de diagnóstico (`DiagMain`)
- **Secciones** — Informes consolidados por sección con análisis pedagógico agregado

El componente Livewire es **compartido** por los módulos de Planificación, Académicos y Evaluación (`livewire:evaluacion.diagnostic.index-component`), con 4 traits de integración de IA intercambiables.

---

## 2. Arquitectura del Dominio

### 2.1 Cadena de dependencias

```
DiagMain (ciclo de diagnóstico)
  ├── DiagQuestion (preguntas del instrumento)
  │     ├── DiagOption (opciones de respuesta)
  │     ├── pensum_id → Pensum (asignatura + grado)
  │     ├── competency_id → DiagCompetency → DiagReferent (marco curricular)
  │     └── indicator_id → DiagIndicator (indicador de logro)
  │
  ├── DiagSession (sesión por estudiante)
  │     ├── estudiant_id → Estudiant (estudiante)
  │     ├── pensum_id → Pensum
  │     ├── lapso_id → Lapso
  │     └── DiagAnswer (respuestas del estudiante)
  │           ├── question_id → DiagQuestion
  │           └── option_id → DiagOption
  │
  ├── DiagReport (informe generado)
  │     ├── DiagResult (resultado global)
  │     ├── DiagReportPensum (resultado por área/asignatura)
  │     ├── DiagReportIndicatorResult (gap analysis: esperado vs observado)
  │     ├── DiagReportAiDraft (borrador generado por IA)
  │     ├── DiagRecommendation (recomendaciones pedagógicas)
  │     └── DiagReportAuditLog (auditoría de cambios)
  │
  ├── DiagReferent (marco referencial curricular)
  │     ├── DiagCompetency (competencias)
  │     │     └── DiagIndicator (indicadores de logro)
  │     └── pestudio_id → Pestudio
  │
  ├── lapso_id → Lapso
  └── pestudio_id → Pestudio

SectionDiagnosticReport (informe consolidado de sección)
  ├── SectionGlobalResult (resultados globales)
  ├── SectionAreaResult (resultados por área)
  │     └── SectionAreaInsight (fortalezas/debilidades)
  ├── SectionProfile (perfil pedagógico de la sección)
  ├── SectionContrast (brechas y materias críticas)
  └── SectionRecommendation (recomendaciones)
```

### 2.2 Árbol de archivos del módulo

```
routes/
  app/tab/plannings/diagnostics.php                ← 1 ruta GET

app/
  Http/
    Controllers/Planning/Tab/
      DiagnosticController.php                     ← 1 método: index
      UserDataInitializer.php                      ← trait compartido
    Livewire/Evaluacion/Diagnostic/
      IndexComponent.php                           ← ~3000 líneas, el corazón del módulo
      DeepSeekReportTrait.php                      ← dsGenerateReport($payload)
      QwenReportTrait.php                          ← qwGenerateReport($payload)
      GeminiReportTrait.php                        ← gmGenerateReport($payload)
      OpenRouterReportTrait.php                    ← orGenerateReport($payload)
    Livewire/Diagnostic/
      DiagReportIndex.php                          ← listado de informes
      DiagReportView.php                           ← visualización/firma de informes
      AuditLogViewer.php                           ← visor de auditoría

  Models/app/Instrument/
    DiagMain.php                                   ← ciclo de diagnóstico
    DiagQuestion.php                               ← preguntas del instrumento
    DiagOption.php                                 ← opciones de respuesta
    DiagSession.php                                ← sesión por estudiante
    DiagAnswer.php                                 ← respuestas
    DiagReport.php                                 ← informe generado
    DiagResult.php                                 ← resultado global del informe
    DiagReportPensum.php                           ← resultado por pensum
    DiagReportIndicatorResult.php                  ← gap indicators
    DiagReportAiDraft.php                          ← borrador IA
    DiagReportAuditLog.php                         ← auditoría
    DiagRecommendation.php                         ← recomendaciones
    DiagReferent.php                               ← marco curricular referencial
    DiagCompetency.php                             ← competencias
    DiagIndicator.php                              ← indicadores de logro

  Models/app/Instrument/Section/ (reports agregados)
    SectionDiagnosticReport.php                    ← informe de sección
    SectionGlobalResult.php                        ← resultado global sección
    SectionAreaResult.php                          ← resultado por área sección
    SectionAreaInsight.php                         ← insights de área
    SectionProfile.php                             ← perfil sección
    SectionContrast.php                            ← contraste sección
    SectionRecommendation.php                      ← recomendación sección

  Services/Diagnostic/
    AiReportService.php                            ← generación de drafts IA
    DiagReportComparisonService.php                ← comparación entre lapsos
    DiagContrastService.php                        ← cálculo de gaps (esperado vs observado)
    Section/
      SectionDiagnosticAggregatorService.php       ← agregación de informes individuales
      SectionReportBuilder.php                     ← persistencia de informe agregado
      PedagogicalPatternDetector.php               ← detección de patrones pedagógicos

  Console/Commands/
    GenerateDiagnosticReports.php                  ← generación batch por consola
    Diagnostic/SyncPrecisionCommand.php            ← sincronización de precisión
    Diagnostic/GenerateSectionDiagnosticReportCommand.php ← informe de sección batch

  Jobs/Diagnostic/
    GenerateSectionDiagnosticReportJob.php         ← job queueable para informes de sección

resources/views/
  plannings/diagnostics/index.blade.php            ← layout del módulo planning
  livewire/evaluacion/diagnostic/
    index-component.blade.php                      ← componente principal con 5 tabs
    partials/
      dashboard.blade.php                          ← tarjetas + gráficos + sesiones recientes
      questions.blade.php                          ← listado y filtro de preguntas
      sessions.blade.php                           ← sesiones + informe IA por estudiante
      diagmains.blade.php                          ← CRUD de ciclos de diagnóstico
      sections.blade.php                           ← informes consolidados por sección
      ai-report-modal.blade.php                    ← modal de informe IA
      section-report-modal.blade.php               ← modal de informe de sección
```

---

## 3. Validación contra código fuente

### 3.1 Hallazgos clave

| # | Tópico | Detalle |
|---|--------|---------|
| 1 | **Componente compartido** | Planning NO tiene componente Livewire propio. El `DiagnosticController` renderiza `academicos.diagnostics.index` que embebe `<livewire:evaluacion.diagnostic.index-component />` — el mismo componente usado por Académicos y Evaluación. |
| 2 | **4 proveedores de IA intercambiables** | El componente usa 4 traits (`DeepSeekReportTrait`, `QwenReportTrait`, `GeminiReportTrait`, `OpenRouterReportTrait`), cada uno con un método que llama al servicio respectivo. Se seleccionan via `$selected_ai_service`. |
| 3 | **`$filterGradoId`** no filtra auto `$list_secciones` en `mount()` | Solo se cargan secciones cuando el usuario cambia el grado via `updatedFilterGradoId()`. No hay precarga inicial. |
| 4 | **Scope por líder ausente** | El componente NO filtra por `leader_id`. Muestra datos globales de todos los pensums activos sin restricción por área de conocimiento del líder. |
| 5 | **`getPensumProgress()` tiene lógica incompleta** | El filtro `$this->filterSeccionId` está declarado pero tiene un bloque de comentarios extenso explicando que la consulta JOIN es compleja y el filtro no se implementa realmente (líneas 497-521). |
| 6 | **`generateAIReport()` es monolítico** | El método reportado como "2000+ líneas" construye manualmente un payload JSON masivo con datos de institución, estudiante, sesiones, áreas, resultados, contraste curricular y referentes, todo en un solo método. |
| 7 | **9 modelos para informes** | `DiagReport` + `DiagResult` + `DiagReportPensum` + `DiagReportIndicatorResult` + `DiagReportAiDraft` + `DiagReportAuditLog` + `DiagRecommendation` — cada informe requiere 7 tablas relacionadas. |
| 8 | **`status_active_diagnostic` en pensums** | Los pensums tienen un flag booleano `status_active_diagnostic` que controla si participan en el módulo de diagnóstico. Se usa en `totalActiveDiagnosticPensums`. |
| 9 | **Múltiples mecanismos de paginación** | El componente usa 4 páginas independientes: `pensumPage`, `questionsPage`, `sessionsPage` y `sectionsPage`. Cada tab resetea las páginas de los otros tabs. |
| 10 | **Auditoría completa** | `DiagReportAuditLog` registra cada acción sobre informes con `user_id`, `action`, `details`, `ip_address`, `user_agent`. |
| 11 | **Cascada incompleta** | `filterGradoId` actualiza `list_secciones` pero no hay un `updatedSeccionId` que reseteo las páginas — `updatedFilterSeccionId` sí existe. |

### 3.2 Validación de rutas

| Ruta | Método | Controlador | Middleware | Archivo |
|------|--------|-------------|------------|---------|
| `GET /app/plannings/diagnostics/index` | `index()` | `Planning\Tab\DiagnosticController` | `auth`, `is_planning` | `routes/app/tab/plannings/diagnostics.php` |

Registro en `routes/web.php`:
```php
Route::group(['prefix' => 'app', 'middleware' => ['auth'], 'namespace' => 'Planning'], function () {
    Route::group(['prefix' => 'plannings', 'middleware' => ['is_planning']], function () {
        require (__DIR__ . '/app/plannings.php');  // → requiere tab/plannings/diagnostics.php
    });
});
```

---

## 4. Lógica de Negocio — Especificación Completa

### 4.1 Reglas de negocio

**RN-01: Diagnóstico por ciclo (DiagMain).**
Cada `DiagMain` define un ciclo de diagnóstico: nombre, descripción, token de acceso, lapso, pestudio y referente curricular asociado. El flag `active` lo habilita/deshabilita.

**RN-02: Preguntas por pensum.**
Cada pregunta (`DiagQuestion`) pertenece a un `pensum` (asignatura+grado). Soporta 3 tipos: `multiple` (selección), `open` (abierta), `scale` (escala). Tiene `difficulty` y `weighing` para ponderación.

**RN-03: Sesión por estudiante por pensum.**
Cada estudiante puede tener una sesión (`DiagSession`) por pensum dentro de un ciclo diagnóstico. La sesión tiene `iniciado_at`, `completado_at`, `progreso` y `activo`.

**RN-04: Precisión de estudiante.**
`DiagAnswer::calculateStudentPrecision()` calcula el porcentaje de respuestas correctas (donde `option.valor = 1`), con filtro opcional por diag_main_id. Se usa en `getStudentAccuracyStats()` del dashboard.

**RN-05: Informe generado por IA con 4 proveedores.**
El informe (`DiagReport`) se genera mediante IA seleccionable: DeepSeek, Qwen, Gemini u OpenRouter. El payload incluye: datos de institución, estudiante, sesiones, áreas evaluadas, resultados globales, contraste curricular y referentes.

**RN-06: Draft de IA no es el informe final.**
`DiagReportAiDraft` almacena el output crudo de la IA (proveedor, modelo, prompts usados, texto generado). El draft puede ser revisado antes de considerarlo "informe final".

**RN-07: Gap analysis por indicador.**
`DiagContrastService::calculateContrast()` compara el nivel esperado (del `DiagIndicator.expected_level`) con el nivel observado (de las respuestas), calculando `gap_value` y `gap_label` (Suficiente=1, Desarrollado=2, Avanzado=3, Sobresaliente=4).

**RN-08: Informes de sección agregados.**
`SectionDiagnosticAggregatorService::aggregate()` toma todos los informes individuales de estudiantes de una sección y los consolida en un `SectionDiagnosticReport` con resultados globales, por área, perfil, contraste y recomendaciones.

**RN-09: Pensum debe tener flag diagnóstico activo.**
Solo los pensums con `status_active_diagnostic = true` cuentan para `totalActiveDiagnosticPensums`. El flag se controla via modales de activación/desactivación en el tab de preguntas.

**RN-10: Filtro por rango de fechas.**
`$dateRange` (default 365 días) filtra `getResponseStats()` y `getRecentSessions()`. Aplica a respuestas con `completado_at >= now()->subDays(dateRange)`.

### 4.2 Flujo de datos completo

```
[Usuario autenticado con rol is_planning]
    │
    ├─(1) GET /app/plannings/diagnostics/index
    │     └─ DiagnosticController@index()
    │           ├─ $this->initializeUserData()
    │           ├─ Profesor::getProfesorForLeaderId($user->id)
    │           ├─ Lapso::all()
    │           ├─ Lapso::current()
    │           └─ view('academicos.diagnostics.index')  ← vista COMPARTIDA
    │                 └─ @livewire('evaluacion.diagnostic.index-component')
    │
    └─ [COMPONENTE] IndexComponent (Livewire) — ~3000 líneas
          │
          ├─ mount()
          │    ├─ $list_pensums = Pensum::list_pestudio_pensum()
          │    ├─ $list_grados = Grado::list_pestudio_grado()
          │    └─ $activeTab = 'dashboard' (default)
          │
          ├─ render()
          │    ├─ generalStats        → getGeneralStats()
          │    ├─ questionsByDiff     → getQuestionsByDifficulty()
          │    ├─ questionsByType     → getQuestionsByType()
          │    ├─ pensumProgress      → getPensumProgress() (paginated: pensumPage)
          │    ├─ recentSessions      → getRecentSessions() (limit 15)
          │    ├─ responseStats       → getResponseStats() (date filtered)
          │    ├─ questions           → getFilteredQuestions() (paginated: questionsPage)
          │    ├─ sessionsPaginated   → getSessionsPaginated() (paginated: sessionsPage)
          │    ├─ sectionsPaginated   → getSectionsPaginated() (paginated: sectionsPage)
          │    └─ view('livewire.evaluacion.diagnostic.index-component', ...)
          │
          ├─ TABS
          │    ├─ setActiveTab('dashboard')  → resetea páginas
          │    ├─ setActiveTab('questions')
          │    ├─ setActiveTab('sessions')
          │    ├─ setActiveTab('diagmains')
          │    └─ setActiveTab('sections')
          │
          ├─ CRUD DiagMain
          │    ├─ saveDiagMain()        → crear/actualizar
          │    ├─ editDiagMain()        → cargar en formulario
          │    └─ deleteDiagMain()      → eliminar
          │
          ├─ Pensum Activation
          │    ├─ showPensumQuestions()       → modal preguntas del pensum
          │    ├─ confirmActivatePensum()     → activar flag diagnóstico
          │    ├─ confirmDeactivatePensum()   → desactivar flag diagnóstico
          │    └─ processBulkDeactivation()   → desactivación masiva
          │
          ├─ Sessions & Reports
          │    ├─ showPensumQuestions()
          │    ├─ showSessionDetails()
          │    ├─ showSessionAnswers()
          │    ├─ showStudentDetails()
          │    ├─ showStudentSessions()
          │    ├─ generateAIReport()     → ~2000+ líneas, construye payload + llama IA
          │    ├─ saveReportToDatabase() → persiste informe generado
          │    ├─ deleteAIReport()
          │    └─ viewAIReport()
          │
          └─ Section Reports
               ├─ generateSectionReport()
               └─ viewSectionReport()
```

### 4.3 Mapa de tabs y funcionalidad

```
┌─────────────────────────────────────────────────────────────────────┐
│  [Dashboard]  [Preguntas]  [Sesiones]  [Diagnósticos]  [Secciones] │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  DASHBOARD:                                                          │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐               │
│  │ Total    │ │Respuestas│ │Sesiones  │ │Precisión │               │
│  │Preguntas │ │Registrad.│ │Completad.│ │Estudiant │               │
│  │   142    │ │  1,234   │ │    89    │ │  73.5%   │               │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘               │
│  ┌────────────────────┐ ┌──────────────────┐                        │
│  │ Sesiones Recientes │ │ Progreso Pensums │                        │
│  │ (lista 15 últimas) │ │ (tabla paginada) │                        │
│  └────────────────────┘ └──────────────────┘                        │
│  ┌────────────────────┐ ┌──────────────────┐                        │
│  │ Gráfico Respuestas │ │ Stats por tipo   │                        │
│  │ Diarias (Chart.js) │ │ y dificultad     │                        │
│  └────────────────────┘ └──────────────────┘                        │
│                                                                      │
│  ──── o ────                                                         │
│                                                                      │
│  PREGUNTAS (tab):                                                    │
│  Filtros: [Pensum ▼] [DiagMain ▼] [Tipo ▼] [Estado ▼] [Profesor ▼] │
│           [Buscar...]                                                 │
│  Tabla: # | Pensum | Pregunta | Tipo | Dificultad | Estado           │
│                                                                      │
│  ──── o ────                                                         │
│                                                                      │
│  SESIONES (tab):                                                     │
│  Filtros: [Grado ▼] [Sección ▼] [Nombre...] [Rango fecha: 365d ▼]   │
│  Tabla: Estudiante | Área | Progreso | Inicio | Fin | Acciones       │
│  Acciones: [Informe IA ▼] [Ver] [Eliminar]                           │
│                                                                      │
│  ──── o ────                                                         │
│                                                                      │
│  DIAGNÓSTICOS (tab):                                                 │
│  [+ Nuevo Diagnóstico]                                               │
│  Tabla: # | Nombre | Token | Estado | Lapso | P.Estudio | Acciones  │
│                                                                      │
│  ──── o ────                                                         │
│                                                                      │
│  SECCIONES (tab):                                                    │
│  Tabla: Sección | Estudiantes | Precisión Prom. | Estado | Informe  │
│  Acciones: [Generar Informe] [Ver]                                   │
│                                                                      │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 5. Esquemas de Base de Datos

### 5.1 Tabla `diag_mains`

```sql
CREATE TABLE `diag_mains` (
  `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `token`       VARCHAR(100) NULL,
  `active`      TINYINT(1) DEFAULT 1,
  `referent_id` BIGINT UNSIGNED NULL,
  `lapso_id`    INT UNSIGNED NULL,
  `pestudio_id` BIGINT UNSIGNED NULL,
  `created_at`  TIMESTAMP NULL,
  `updated_at`  TIMESTAMP NULL,
  FOREIGN KEY (`lapso_id`) REFERENCES `lapsos`(`id`),
  FOREIGN KEY (`pestudio_id`) REFERENCES `pestudios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.2 Tabla `diag_questions`

```sql
CREATE TABLE `diag_questions` (
  `id`            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pensum_id`     INT UNSIGNED NOT NULL,
  `pregunta`      TEXT NOT NULL,
  `tipo_pregunta` VARCHAR(50) NOT NULL,        -- 'multiple' | 'open' | 'scale'
  `orden`         INT DEFAULT 0,
  `difficulty`    VARCHAR(50) DEFAULT 'media', -- 'facil' | 'media' | 'dificil'
  `weighing`      DECIMAL(5,2) DEFAULT 1.00,
  `activo`        TINYINT(1) DEFAULT 1,
  `diag_main_id`  BIGINT UNSIGNED NULL,
  `competency_id` BIGINT UNSIGNED NULL,
  `indicator_id`  BIGINT UNSIGNED NULL,
  `created_at`    TIMESTAMP NULL,
  `updated_at`    TIMESTAMP NULL,
  FOREIGN KEY (`pensum_id`) REFERENCES `pensums`(`id`),
  FOREIGN KEY (`diag_main_id`) REFERENCES `diag_mains`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.3 Tabla `diag_options`

```sql
CREATE TABLE `diag_options` (
  `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `question_id` BIGINT UNSIGNED NOT NULL,
  `opcion`      TEXT NOT NULL,
  `valor`       TINYINT(1) DEFAULT 0,          -- 1 = correcta, 0 = incorrecta
  `orden`       INT DEFAULT 0,
  `created_at`  TIMESTAMP NULL,
  `updated_at`  TIMESTAMP NULL,
  FOREIGN KEY (`question_id`) REFERENCES `diag_questions`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.4 Tabla `diag_sessions`

```sql
CREATE TABLE `diag_sessions` (
  `id`              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `estudiant_id`    BIGINT UNSIGNED NOT NULL,
  `pensum_id`       INT UNSIGNED NOT NULL,
  `iniciado_at`     DATETIME NULL,
  `completado_at`   DATETIME NULL,
  `progreso`        INT DEFAULT 0,
  `total_preguntas` INT DEFAULT 0,
  `activo`          TINYINT(1) DEFAULT 1,
  `diag_main_id`    BIGINT UNSIGNED NULL,
  `lapso_id`        INT UNSIGNED NULL,
  `status`          VARCHAR(50) NULL,
  `created_at`      TIMESTAMP NULL,
  `updated_at`      TIMESTAMP NULL,
  FOREIGN KEY (`estudiant_id`) REFERENCES `estudiants`(`id`),
  FOREIGN KEY (`pensum_id`) REFERENCES `pensums`(`id`),
  FOREIGN KEY (`diag_main_id`) REFERENCES `diag_mains`(`id`),
  FOREIGN KEY (`lapso_id`) REFERENCES `lapsos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.5 Tabla `diag_answers`

```sql
CREATE TABLE `diag_answers` (
  `id`             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `estudiant_id`   BIGINT UNSIGNED NOT NULL,
  `question_id`    BIGINT UNSIGNED NOT NULL,
  `option_id`      BIGINT UNSIGNED NULL,
  `session_id`     BIGINT UNSIGNED NOT NULL,
  `respuesta`      TEXT NULL,
  `valor_numerico` DECIMAL(5,2) NULL,
  `completado_at`  DATETIME NULL,
  `created_at`     TIMESTAMP NULL,
  `updated_at`     TIMESTAMP NULL,
  FOREIGN KEY (`estudiant_id`) REFERENCES `estudiants`(`id`),
  FOREIGN KEY (`question_id`) REFERENCES `diag_questions`(`id`),
  FOREIGN KEY (`option_id`) REFERENCES `diag_options`(`id`),
  FOREIGN KEY (`session_id`) REFERENCES `diag_sessions`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.6 Tablas de informes

#### `diag_reports`
```sql
CREATE TABLE `diag_reports` (
  `id`           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `estudiant_id` BIGINT UNSIGNED NOT NULL,
  `diag_main_id` BIGINT UNSIGNED NULL,
  `referent_id`  BIGINT UNSIGNED NULL,
  `lapso_id`     INT UNSIGNED NULL,
  `status`       VARCHAR(50) DEFAULT 'draft',  -- draft | validated | signed
  `generated_at` DATETIME NULL,
  `validated_at` DATETIME NULL,
  `created_at`   TIMESTAMP NULL,
  `updated_at`   TIMESTAMP NULL,
  FOREIGN KEY (`estudiant_id`) REFERENCES `estudiants`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### `diag_results`
```sql
CREATE TABLE `diag_results` (
  `id`                          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `report_id`                   BIGINT UNSIGNED NOT NULL,
  `total_answered_questions`    INT DEFAULT 0,
  `precision`                   DECIMAL(5,2) DEFAULT 0.00,
  `open_ended_response_level`   VARCHAR(50) NULL,
  `created_at`                  TIMESTAMP NULL,
  `updated_at`                  TIMESTAMP NULL,
  FOREIGN KEY (`report_id`) REFERENCES `diag_reports`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### `diag_report_ai_drafts`
```sql
CREATE TABLE `diag_report_ai_drafts` (
  `id`                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `report_id`           BIGINT UNSIGNED NOT NULL,
  `llm_provider`        VARCHAR(100) NULL,       -- 'qwen' | 'deepseek' | 'gemini' | 'openrouter'
  `llm_model`           VARCHAR(255) NULL,
  `system_prompt_id`    BIGINT UNSIGNED NULL,    -- FK ai_prompts
  `user_prompt_id`      BIGINT UNSIGNED NULL,    -- FK ai_prompts
  `prompt_version_label` VARCHAR(100) NULL,
  `input_hash`          VARCHAR(64) NULL,        -- SHA-256 del payload
  `output_text`         LONGTEXT NULL,
  `status`              VARCHAR(50) DEFAULT 'pending',
  `created_at`          TIMESTAMP NULL,
  `updated_at`          TIMESTAMP NULL,
  FOREIGN KEY (`report_id`) REFERENCES `diag_reports`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### `diag_report_indicator_results`
```sql
CREATE TABLE `diag_report_indicator_results` (
  `id`                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `report_id`         BIGINT UNSIGNED NOT NULL,
  `pensum_id`         INT UNSIGNED NULL,
  `indicator_id`      BIGINT UNSIGNED NULL,
  `expected_level`    VARCHAR(50) NULL,
  `observed_level`    VARCHAR(50) NULL,
  `gap_value`         INT DEFAULT 0,             -- expected_numeric - observed_numeric
  `gap_label`         VARCHAR(100) NULL,          -- 'Sin brecha' | 'Brecha leve' | 'Brecha significativa'
  `teacher_observation` TEXT NULL,
  FOREIGN KEY (`report_id`) REFERENCES `diag_reports`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.7 Tablas de referentes curriculares

```sql
CREATE TABLE `diag_referents` (
  `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `pestudio_id` BIGINT UNSIGNED NULL,
  `name`        VARCHAR(255) NOT NULL,
  `code`        VARCHAR(50) NULL,
  `version`     VARCHAR(50) NULL,
  `description` TEXT NULL,
  `active`      TINYINT(1) DEFAULT 1,
  `created_at`  TIMESTAMP NULL,
  `updated_at`  TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `diag_competencies` (
  `id`          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `referent_id` BIGINT UNSIGNED NOT NULL,
  `pensum_id`   INT UNSIGNED NULL,
  `name`        VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `created_at`  TIMESTAMP NULL,
  `updated_at`  TIMESTAMP NULL,
  FOREIGN KEY (`referent_id`) REFERENCES `diag_referents`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `diag_indicators` (
  `id`             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `competency_id`  BIGINT UNSIGNED NOT NULL,
  `code`           VARCHAR(50) NULL,
  `description`    TEXT NOT NULL,
  `expected_level` VARCHAR(50) NULL,
  `created_at`     TIMESTAMP NULL,
  `updated_at`     TIMESTAMP NULL,
  FOREIGN KEY (`competency_id`) REFERENCES `diag_competencies`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 5.8 Tablas de informes de sección

Las tablas `section_diagnostic_reports`, `section_global_results`, `section_area_results`, `section_area_insights`, `section_profiles`, `section_contrasts` y `section_recommendations` conforman el modelo de informes consolidados por sección. Ver migraciones en `database/migrations/backUps/gamificacion/`.

---

## 6. Modelo de Datos — API REST para exportación

### 6.1 Endpoints propuestos

#### `GET /api/planning/diagnostics/dashboard`

Estadísticas globales del dashboard.

**Query params:** `diag_main_id`, `grado_id`, `seccion_id`, `date_range`

**Response:**
```json
{
  "general_stats": {
    "total_questions": 142,
    "total_sessions": 156,
    "completed_sessions": 89,
    "total_responses": 1234,
    "active_sessions": 12,
    "avg_completion_rate": 57.05,
    "student_accuracy": 73.5,
    "correct_answers": 907,
    "total_answered": 1234
  },
  "questions_by_difficulty": [
    { "difficulty": "facil", "count": 45 },
    { "difficulty": "media", "count": 62 },
    { "difficulty": "dificil", "count": 35 }
  ],
  "questions_by_type": [
    { "type": "multiple", "count": 98 },
    { "type": "open", "count": 32 },
    { "type": "scale", "count": 12 }
  ],
  "pensum_progress": [
    {
      "pensum_id": 89,
      "asignatura": "MATEMÁTICA",
      "grado": "5TO AÑO",
      "total_questions": 15,
      "total_sessions": 28,
      "completed_sessions": 22,
      "completion_percentage": 78.57
    }
  ],
  "recent_sessions": [
    {
      "id": 45,
      "estudiant": "PÉREZ JUAN",
      "pensum": "MATEMÁTICA",
      "progreso": 100,
      "iniciado_at": "2026-05-15T10:30:00Z",
      "completado_at": "2026-05-15T11:15:00Z"
    }
  ],
  "response_stats": {
    "daily_responses": [
      { "date": "2026-06-01", "count": 45 },
      { "date": "2026-06-02", "count": 62 }
    ],
    "responses_by_pensum": [
      { "id": 89, "name": "MATEMÁTICA", "count": 234 }
    ]
  }
}
```

#### `GET /api/planning/diagnostics/questions`

Listado paginado de preguntas con filtros.

**Query params:** `pensum_id`, `diag_main_id`, `tipo_pregunta`, `estado_pregunta`, `profesor_id`, `search`, `page`, `per_page`

#### `GET /api/planning/diagnostics/sessions`

Listado paginado de sesiones de estudiantes.

**Query params:** `grado_id`, `seccion_id`, `student_name`, `diag_main_id`, `date_range`, `page`, `per_page`

#### `GET /api/planning/diagnostics/sessions/{id}`

Detalle de una sesión con respuestas.

#### `POST /api/planning/diagnostics/reports/generate`

Generar informe de diagnóstico vía IA para un estudiante.

**Request:**
```json
{
  "estudiant_id": 45,
  "diag_main_id": 1,
  "ai_service": "qwen",
  "model": "qwen-max"
}
```

**Response (202 Accepted):**
```json
{
  "report_id": 12,
  "status": "processing",
  "message": "Informe en proceso de generación."
}
```

#### `GET /api/planning/diagnostics/reports/{id}`

Obtener informe generado con todos los datos.

**Response:**
```json
{
  "id": 12,
  "estudiant": { "id": 45, "fullname": "PÉREZ JUAN" },
  "diag_main": { "id": 1, "name": "Diagnóstico Inicial 2025-2026" },
  "status": "draft",
  "generated_at": "2026-06-01T10:30:00Z",
  "results": {
    "precision": 73.5,
    "total_answered_questions": 20,
    "open_ended_response_level": "Desarrollado"
  },
  "pensum_results": [
    {
      "pensum_id": 89,
      "asignatura": "MATEMÁTICA",
      "precision": 68.0,
      "total_answered": 10,
      "open_ended_level": "En desarrollo"
    }
  ],
  "indicator_results": [
    {
      "indicator_code": "IND-001",
      "indicator_description": "Resuelve ecuaciones lineales",
      "expected_level": "Avanzado",
      "observed_level": "Desarrollado",
      "gap_value": 1,
      "gap_label": "Brecha leve"
    }
  ],
  "ai_draft": {
    "llm_provider": "qwen",
    "output_text": "...informe generado por IA...",
    "status": "completed"
  }
}
```

#### `POST /api/planning/diagnostics/diag-mains`

CRUD de ciclos de diagnóstico.

#### `GET /api/planning/diagnostics/section-reports`

Informes consolidados de sección.

#### `POST /api/planning/diagnostics/section-reports/generate`

Generar informe consolidado para una sección (job queueable).

---

## 7. Algoritmos Clave

### 7.1 Cálculo de precisión del estudiante

```typescript
interface PrecisionStats {
  accuracy: number;
  correct_answers: number;
  total_answered: number;
}

function calculateStudentPrecision(
  estudiantId: number,
  diagMainId?: number
): PrecisionStats {
  // Obtener respuestas del estudiante
  // Cada respuesta tiene una option_id
  // La opción correcta tiene valor = 1
  // Filtrar opcionalmente por diag_main_id
  
  const query = DiagAnswer.query()
    .where('estudiant_id', estudiantId)
    .join('diag_options', 'diag_answers.option_id', 'diag_options.id')
    .whereNotNull('diag_answers.completado_at');
    
  if (diagMainId) {
    query.join('diag_questions', 'diag_answers.question_id', 'diag_questions.id')
      .where('diag_questions.diag_main_id', diagMainId);
  }
  
  const totalAnswered = query.count();
  const correctAnswers = query.where('diag_options.valor', 1).count();
  
  return {
    accuracy: totalAnswered > 0 
      ? Math.round((correctAnswers / totalAnswered) * 1000) / 10 
      : 0,
    correct_answers: correctAnswers,
    total_answered: totalAnswered,
  };
}
```

### 7.2 Gap analysis (esperado vs observado)

```typescript
const LEVEL_MAP: Record<string, number> = {
  'Inicial': 1,
  'En desarrollo': 2,
  'Desarrollado': 3,
  'Avanzado': 4,
  'Sobresaliente': 5,
};

function calculateGap(expectedLevel: string, observedLevel: string): {
  gapValue: number;
  gapLabel: string;
} {
  const expected = LEVEL_MAP[expectedLevel] ?? 0;
  const observed = LEVEL_MAP[observedLevel] ?? 0;
  const gap = expected - observed;

  let gapLabel: string;
  if (gap <= 0) {
    gapLabel = 'Sin brecha (nivel alcanzado o superado)';
  } else if (gap === 1) {
    gapLabel = 'Brecha leve';
  } else if (gap === 2) {
    gapLabel = 'Brecha moderada';
  } else {
    gapLabel = 'Brecha significativa';
  }

  return { gapValue: gap, gapLabel };
}
```

### 7.3 Construcción del payload para IA

El payload que se envía al LLM es un JSON estructurado con:

```json
{
  "institution": { "name": "U.E. Fray Luis Amigo", "school_year": "2025-2026" },
  "student": { "fullname": "PÉREZ JUAN", "ci": "V-12345678", "grade": "5TO AÑO", "section": "A" },
  "sessions": [
    {
      "area": "MATEMÁTICA",
      "date": "2026-05-15",
      "total_questions": 10,
      "answered": 10,
      "precision_percentage": 68.0,
      "difficulty_distribution": { "facil": 3, "media": 5, "dificil": 2 },
      "incorrect_by_difficulty": { "facil": 0, "media": 2, "dificil": 1 },
      "open_ended_level": "En desarrollo"
    }
  ],
  "global_results": {
    "overall_precision": 73.5,
    "completed_areas": 4,
    "total_areas": 5,
    "highest_area": "CIENCIAS NATURALES",
    "lowest_area": "MATEMÁTICA"
  },
  "curricular_contrast": {
    "indicators": [
      {
        "code": "IND-001",
        "description": "Resuelve ecuaciones lineales",
        "expected": "Avanzado",
        "observed": "Desarrollado",
        "gap": "Brecha leve"
      }
    ],
    "critical_indicators": [
      { "code": "IND-003", "gap": 2 }
    ]
  },
  "referents": { "name": "Currículo Nacional 2025", "version": "1.0" },
  "previous_reports": []
}
```

---

## 8. Especificación de Componentes (NextJS + Tailwind)

### 8.1 Árbol de componentes

```
PlanningDiagnosticsPage
├── DiagnosticFilterBar
│   ├── DiagMainSelect (filtro principal de ciclo diagnóstico)
│   ├── GradoSelect (cascada: → sección)
│   └── SeccionSelect
├── DiagnosticTabs (5 tabs)
│   ├── DashboardTab
│   │   ├── StatsCards (4 tarjetas: preguntas, respuestas, sesiones, precisión)
│   │   ├── RecentSessionsList
│   │   ├── PensumProgressTable (paginated)
│   │   ├── ResponseChart (Chart.js / Recharts: respuestas diarias)
│   │   └── DistributionCharts (por dificultad y tipo)
│   ├── QuestionsTab
│   │   ├── QuestionsFilterBar (pensum, tipo, estado, profesor, búsqueda)
│   │   └── QuestionsTable (paginated)
│   ├── SessionsTab
│   │   ├── SessionsFilterBar (grado, sección, nombre, rango fecha)
│   │   ├── SessionsTable (paginated)
│   │   └── SessionActions (generar informe IA, ver, eliminar)
│   ├── DiagMainsTab
│   │   ├── CreateDiagMainButton
│   │   └── DiagMainsTable (CRUD)
│   └── SectionsTab
│       ├── SectionsTable (paginated)
│       └── SectionActions (generar/ver informe de sección)
├── AiReportModal (modal de informe generado por IA)
│   ├── AiServiceSelector (deepseek/qwen/gemini/openrouter)
│   ├── ReportPreview (texto del informe)
│   └── ReportActions (validar, firmar, descargar)
└── SectionReportModal (modal de informe de sección)
```

### 8.2 Estados de cada componente

| Componente | Loading | Empty | Error | Success |
|-----------|---------|-------|-------|---------|
| `DashboardTab` | Skeleton cards | "Sin datos de diagnóstico" | Toast error | Tarjetas + gráficos |
| `QuestionsTable` | Skeleton filas | "No hay preguntas registradas" | Toast error | Tabla paginada |
| `SessionsTable` | Spinner | "No hay sesiones registradas" | Toast error | Tabla paginada |
| `DiagMainsTable` | Spinner | "No hay diagnósticos creados" | Toast error | Tabla CRUD |
| `SectionsTable` | Spinner | "No hay informes de sección" | Toast error | Tabla paginada |
| `AiReportModal` | Spinner "Generando informe..." | "Seleccione un estudiante" | Toast "Error al generar" | Texto del informe |
| `SectionReportModal` | Spinner | "Seleccione una sección" | Toast error | Datos consolidados |
| `ResponseChart` | Chart skeleton | "Sin datos en el período" | N/A | Chart.js |
| `PensumProgressTable` | Skeleton | "Sin progreso registrado" | N/A | Tabla paginada |

---

## 9. Plan de Migración: Laravel/Livewire → NextJS + API

### Fase 1: Backend API

| Prioridad | Endpoint | Descripción |
|-----------|----------|-------------|
| P0 | `GET /api/planning/diagnostics/dashboard` | Estadísticas globales |
| P0 | `GET /api/planning/diagnostics/questions` | Preguntas paginadas con filtros |
| P0 | `GET /api/planning/diagnostics/sessions` | Sesiones paginadas |
| P0 | `GET /api/planning/diagnostics/sessions/{id}` | Detalle de sesión |
| P0 | `GET /api/planning/diagnostics/filters` | Datos de filtros (pensums, grados, diag_mains, tipos) |
| P1 | `GET /api/planning/diagnostics/reports/{id}` | Informe individual |
| P1 | `POST /api/planning/diagnostics/reports/generate` | Generar informe vía IA |
| P1 | `CRUD /api/planning/diagnostics/diag-mains` | Mantenimiento de ciclos |
| P2 | `GET /api/planning/diagnostics/section-reports` | Informes de sección |
| P2 | `POST /api/planning/diagnostics/section-reports/generate` | Generar informe de sección (job) |

### Fase 2: Frontend NextJS

| Prioridad | Componente | Descripción |
|-----------|------------|-------------|
| P0 | `useDiagnostics` | Hook principal con filtros y tabs |
| P0 | `DiagnosticLayout` | Layout con tabs y filtro global |
| P0 | `DashboardTab` | Stats, charts, progreso |
| P1 | `SessionsTab + AiReportModal` | Sesiones y generación de informes IA |
| P1 | `QuestionsTab` | Exploración de preguntas |
| P2 | `DiagMainsTab` | CRUD de ciclos de diagnóstico |
| P2 | `SectionsTab` | Informes consolidados |

### Fase 3: Integración con IA

| Prioridad | Servicio | Descripción |
|-----------|----------|-------------|
| P0 | QwenService | API Qwen (default, más usado) |
| P0 | DeepSeekService | API DeepSeek |
| P1 | GeminiService | API Gemini |
| P1 | OpenRouterService | API OpenRouter (multi-modelo) |
| P2 | AiPromptManager | Gestión de prompts del sistema/usuario |

### Fase 4: Pruebas

| Tipo | Casos |
|------|-------|
| Unitarias | Cálculo de precisión, gap analysis, construcción de payload IA |
| Integración | Generación de informe → verificar DiagReport + DiagResult + DiagReportAiDraft |
| Integración | CRUD DiagMain con preguntas encadenadas |
| Integración | Informe de sección (agregación de múltiples informes individuales) |
| Componente | Dashboard con datos reales, simulando filtros |
| Componente | Modal de IA: selección de servicio, generación, vista de resultado |
| E2E | Flujo: ver dashboard → filtrar por diagnóstico → ver sesiones → generar informe IA → ver informe |

---

## 10. Dependencias y servicios externos

| Dependencia | Uso |
|-------------|-----|
| Chart.js | Gráficos de respuestas diarias, distribución por tipo/dificultad |
| SweetAlert2 | Alertas de éxito/error, confirmaciones |
| Bootstrap 4 | Layout, tabs, cards, modales, tablas |
| FontAwesome 5 | Iconos |
| DeepSeek API | Generación de informes de diagnóstico |
| Qwen API (DashScope) | Generación de informes (default) |
| Gemini API | Generación de informes |
| OpenRouter API | Generación de informes multi-modelo |
| AiPrompt (modelo) | Almacenamiento de prompts de sistema/usuario versionados |

---

## 11. Edge Cases y validaciones

| Caso | Comportamiento esperado |
|------|------------------------|
| Sin preguntas activas | Dashboard muestra 0 en todas las tarjetas |
| Sin sesiones completadas | `avg_completion_rate` = 0, badges sin datos |
| Estudiante sin respuestas | Precisión 0%, informe IA con datos mínimos |
| Filtro diag_main_id sin resultados | Todas las métricas en 0 |
| Error en llamada IA | `generateAIReport()` loggea error, muestra toast, no bloquea UI |
| Pensum sin flag diagnóstico | No aparece en `totalActiveDiagnosticPensums` |
| Sección sin informes individuales | `generateSectionReport()` falla con validación |
| Múltiples tabs abiertos | Cada tab resetea páginas de los otros tabs |
| Payload IA muy grande | `AiReportService` usa `input_hash` (SHA-256) para evitar duplicados |

---

*Documento generado a partir del análisis del código fuente de SAEFL. Validado contra: `DiagnosticController.php`, `IndexComponent.php` (~3000 líneas), `DiagMain`, `DiagQuestion`, `DiagSession`, `DiagAnswer`, `DiagReport`, `DiagContrastService`, `AiReportService`, migraciones en `backUps/diagnostico/` y `backUps/gamificacion/`, y las vistas Blade del módulo.*
