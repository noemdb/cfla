# Flujo Completo de una Actividad / Lección LMS

**Blueprint de Dominio**
_Última revisión:_ 2026-07-31
_Estado:_ Verificación de aseveraciones contra el código (regla absoluta)

---

## Tabla de Contenidos

1. [Visión General](#1-visión-general)
2. [Cadena Completa de Modelos](#2-cadena-completa-de-modelos)
3. [Fase 1: Asignación de Carga Académica (Módulo Planning)](#3-fase-1-asignacin-de-carga-acadmica-mdulo-planning)
4. [Fase 2: Registro de Actividades por el Profesor y Aprobación](#4-fase-2-registro-de-actividades-por-el-profesor-y-aprobacin)
5. [Fase 3: Preparación LMS](#5-fase-3-preparación-lms)
6. [Fase 4: Programación y Publicación](#6-fase-4-programación-y-publicación)
7. [Fase 5: Scoping del Estudiante](#7-fase-5-scoping-del-estudiante)
8. [Fase 6: Visibilidad en Consultas](#8-fase-6-visibilidad-en-consultas)
9. [Fase 7: Consumo por el Estudiante](#9-fase-7-consumo-por-el-estudiante)
10. [Fase 8: Expiración y Despublicación](#10-fase-8-expiracin-y-despublicacin)
11. [Diagrama de Flujo Completo](#11-diagrama-de-flujo-completo)
12. [Matriz de Visibilidad (Quién ve qué)](#12-matriz-de-visibilidad-quin-ve-qu)
13. [Referencia de Queries Clave](#13-referencia-de-queries-clave)

---

## 1. Visión General

Este documento traza el ciclo de vida completo de una **actividad de planificación** (evaluativa) desde que el **módulo de Planificación** (planning) asigna la carga académica (creando el `Pevaluacion`), pasando por el **registro de actividades por el profesor**, hasta que es visible y consumible por un estudiante en el módulo LMS.

El flujo involucra **5 roles** (Planning, Jefe de Área, Coordinación, Profesor, Estudiante — más Admin como bypass) y **8 fases** secuenciales, con múltiples gates de visibilidad que determinan qué ve cada quién.

```
Carga Académica → Registro Actividades → Aprobación → Preparación LMS → Programación/Publicación → Scoping → Consulta → Consumo → Expiración
```

**Resumen de roles reales (middleware):**

| Rol técnico | Campo `users` | Middleware | Nota |
|-------------|---------------|------------|------|
| Planning (Sub Dirección) | `is_planner` | `IsPlanner` = `is_admin \|\| is_planner \|\| is_diagnostic` | Crea cargas, aprueba actividades, monitorea LMS |
| Jefe de Área | `is_leadership` | `IsLeadership` = `is_leadership` (solo) | Publica lecciones programadas de su asignatura |
| Coordinación | `is_coordinacion` | `IsCoordinacion` = `is_admin \|\| is_coordinacion` | Monitoreo (no publica) |
| Profesor | `is_profesor` | `IsProfesor` = `is_profesor \|\| is_admin` | Registra actividades, prepara LMS |
| Estudiante | `is_student` | `IsStudent` (admin bypass) | Consume contenido |
| Admin | `is_admin` | bypass en casi todos | Todo |

> **Aclaración de nomenclatura:** El documento histórico hablaba de "Sub Dirección" y "Jefe de Área/Coordinador" como actores únicos de publicación. En el código real la publicación efectiva de lecciones la ejercen **Planning** (`is_planner`) vía `LmsMonitor`, **Jefe de Área** (`is_leadership`) vía `ActivityOverview` (`/activities`) y `LessonMonitor` (`/lessons`), y **Admin**. Coordinación (`is_coordinacion`) **no publica** LMS.

---

## 2. Cadena Completa de Modelos

### Jerarquía estructural

```
User (rol: is_admin | is_planner | is_leadership | is_coordinacion | is_profesor | is_student)
 │
 ├── Profesor (profesor_id)
 │
 ├── Pevaluacion (profesor_id, seccion_id, pensum_id, lapso_id, grupo_estable_id)
 │    │
 │    ├── Seccion (seccion_id ← student scope target)
 │    │    ├── Grado (grado_id)
 │    │    │    ├── Pestudio (pestudio_id, planning_module)
 │    │    │    └── Pensum (grado_id, asignatura_id)
 │    │    │         └── Pevaluacion (…)
 │    │    │              └── Activity (…)
 │    │    └── Inscripcion (seccion_id, estudiant_id)
 │    │         └── Estudiant (user_id → User.is_student = true)
 │    │
 │    ├── Pensum (asignatura_id)
 │    │    └── Asignatura (name, code)
 │    │
 │    ├── Lapso (name, finicial, ffinal, date_preclosing, time_preclosing)
 │    │
 │    └── Activities (hasMany)
 │         │
 │         ├── LmsActivityPublication (hasOne)
 │         │    ├── status: DRAFT | SCHEDULED | PUBLISHED | ARCHIVED
 │         │    ├── publish_at, unpublish_at, published_at
 │         │    └── allow_comments, allow_downloads, published_by
 │         │
 │         ├── LmsActivitySection (hasMany, ordenado por sort_order)
 │         │    ├── title, description, is_visible
 │         │    └── LmsActivityContent (hasMany, ordenado por sort_order)
 │         │         ├── type: TEXT | VIDEO | AUDIO | IMAGE | PRESENTATION | HTML | EMBED | FILE_PREVIEW
 │         │         ├── body, title, media_id, is_visible, is_required
 │         │         └── Media (LmsMediaLibrary — hasMany normal, NO polimórfica)
 │         │
 │         ├── LmsActivityResource (hasMany, ordenado por sort_order)
 │         │    ├── display_name, description, is_visible
 │         │    ├── section_id (opcional), media_id → LmsMediaLibrary
 │         │    └── download_count, uploaded_by
 │         │
 │         ├── LmsActivityLink (hasMany, ordenado por sort_order)
 │         │    ├── url, title, is_visible, link_type
 │         │
 │         ├── LmsHtmlEmbed (hasMany, ordenado por sort_order)
 │         │    ├── html_content, title, is_visible, section_id, added_by
 │         │
 │         ├── LmsActivityLog (hasMany)
 │         │    ├── event: VIEW | COMPLETE | RESOURCE_DOWNLOAD | PUBLISH | UNPUBLISH | SCHEDULE | EDIT
 │         │    ├── user_id, created_at, ip_address
 │         │    └── context_id, context_type (columnas; NO hay relación morphTo definida)
 │         │
 │         ├── LmsActivityProgress (modelo independiente, sin relación en Activity)
 │         │    ├── activity_id + student_id (users.id) — clave compuesta de acceso
 │         │    ├── status: IN_PROGRESS | COMPLETED
 │         │    ├── completion_pct, time_spent_secs
 │         │    └── first_access_at, last_access_at, completed_at
 │         │
 │         └── ActivityComment (hasMany, SoftDeletes)
 │              ├── body, user_id
 │              ├── is_approved, approved_at, approved_by
 │              └── rejected_at, rejected_by, rejected_reason
 │
 └── Achievement (hasMany, indicadores de la actividad)
```

> **Correcciones verificadas:**
> - `LmsActivityPublication` expone `studentVisibility()` que devuelve `'hidden'` / `'preview'` / `'full'` según la relación de `now()` con `publish_at` (ver Fase 6). El modelo **nunca** persiste `publish_at` nulo desde el servicio (ver Fase 4a).
> - `LmsMediaLibrary` **NO es polimórfica**: las relaciones `contents()` / `resources()` son `hasMany` planas (no `morphMany`). La columna de discriminación es `provider` ('LOCAL' = archivo local) vía `isLocal()`, no `media_type`.
> - `Activity` **NO tiene relación `lmsProgress`**. El progreso se lee/escribe consultando `LmsActivityProgress` directamente por `activity_id` + `student_id`.
> - `LmsActivityLog` sí tiene las columnas `context_id`/`context_type` y `ip_address`, pero el modelo no define una relación `morphTo`; son columnas informativas para asociar el log a un recurso concreto (p. ej. `RESOURCE_DOWNLOAD` de un `LmsActivityResource`).
> - Los contenidos tienen `is_required` (obligatorio/requerido), no solo `is_visible`.
> - `LmsActivityResource` y `LmsHtmlEmbed` tienen `section_id` (opcional) y `uploaded_by`/`added_by`.

---

## 3. Fase 1: Asignación de Carga Académica (Módulo Planning)

### 3.1 Creación del Pevaluacion

**Planning** (el "Sub Dirección" histórico; acceso `IsPlanner` = `is_admin` o `is_planner` o `is_diagnostic`) asigna la **carga académica** de cada profesor, lo que crea automáticamente un registro en `pevaluacions` (Plan de Evaluación). Componente: `app/Livewire/Planning/Pevaluacion/IndexComponent.php`. Este registro agrupa actividades por:

- **Sección** (`seccion_id`): víncula a un grupo-aula específico
- **Pensum / Asignatura** (`pensum_id`): define qué materia cubre
- **Lapso** (`lapso_id`): período académico (1er/2do/3er momento)
- **Profesor** (`profesor_id`): docente responsable
- **Grupo Estable** (`grupo_estable_id`): opcional, para grupos especiales

El `Pevaluacion` es el **ancla de scoping**: el estudiante solo ve actividades cuyo `Pevaluacion.seccion_id` coincide con la sección de su inscripción.

### 3.2 Detalles verificados del componente Planning

- **Selects en cascada:** `pestudio → grado → (sección + pensum) → profesor`. Solo muestra `Pestudio.planning_module = true` y `status_active = true`.
- **Unicidad compuesta:** `save()` valida que no exista otra carga con el mismo `(lapso_id, seccion_id, pensum_id)`. Si existe → error "Carga Académica Duplicada".
- **Lapso cerrado:** `edit()` bloquea si `Pevaluacion::is_lapso_closed` (verifica fechas del lapso).
- **Borrado protegido:** `destroy()` impide eliminar una carga que tenga actividades (`withCount('activities')` > 0 → "Elimínelas primero").
- **Nota:** el `IndexComponent` usa `withPlanningModule()` (scope que restringe a pestudios con `planning_module=true`).

> **Importante:** El profesor **no crea** el `Pevaluacion`. Este es creado por Planning cuando se le asigna una carga académica al profesor. El profesor trabaja **dentro** de Pevaluacions ya existentes.

---

## 4. Fase 2: Registro de Actividades por el Profesor y Aprobación

### 4.1 Creación de Actividades

El **Profesor** registra sus **Activities** (actividades de planificación) dentro del `Pevaluacion` que Planning le asignó. Componente: `app/Livewire/Profesor/Activity/IndexComponent.php` (`mount($id)` recibe el `pevaluacion_id`). Cada actividad contiene:

| Campo | Descripción | Uso en LMS |
|-------|-------------|-----------|
| `topic` | Tema generador y énfasis | Título visible en listados |
| `thematic` | Tejido temático | Búsqueda |
| `description` | Actividad evaluativa | Preview en listados, contenido |
| `teaching` | Enseñanza / Actividad Globalizada | Estructura INICIO·DESARROLLO·CIERRE |
| `learning` | Aprendizaje | Contenido textual |
| `observations` | ODS / Sistematización | — |
| `status` | Aprobado (1) o En revisión (0) | **Gate 1: solo status=true es visible** |
| `finicial` | Fecha inicio | Línea de tiempo |
| `ffinal` | Fecha fin | Deadlines, expiración |
| `comments` | Comentario de la Jefatura de Área | — (solo Planning lo escribe) |

**Detalles verificados del registro:**

- **`ActivityForm` NO incluye el campo `status`.** El profesor no aprueba su propia actividad desde el formulario; la actividad se crea sin `status` explícito (default `false`/null).
- El campo `teaching` se compone en el form de 3 segmentos (`teachingStart`, `teachingContent`, `teachingEnd`) que se concatenan como `INICIO: … DESARROLLO: … CIERRE: …` (`buildTeaching()`). El modelo `Activity` ofrece `hasTeachingStructure()` y `getTeachingSections()` para descomponerlos.
- **Bloqueo por precierre del lapso:** `mount()` calcula `enable_edit` comparando ahora contra `Lapso.date_preclosing + Lapso.time_preclosing`.
- El profesor puede adjuntar **Achievements** (indicadores) y usar **clonación entre secciones** del mismo grado, copia desde `s2526` (período anterior) y mejora con IA (`ActivityImprovementService`).
- `emptyActivities()` elimina en cascada: `achievements`, `lmsLogs`, `lmsPublication`, `lmsSections`, `lmsResources`, `lmsLinks`, `lmsHtmlEmbeds`.

### 4.2 Aprobación de la Actividad (por Planning, no por el Profesor)

La actividad debe tener `status = true` (Aprobado). Este campo es binario:

- **`status = true`** → La actividad está aprobada y puede continuar al flujo LMS.
- **`status = false`** → La actividad está en revisión. Los estudiantes **nunca** la ven, independientemente del estado de publicación LMS.

**Gate de visibilidad #1:** `Activity.status = true`

**Quién aprueba (verificado):** El **módulo Planning** (`app/Livewire/Planning/Activities/IndexComponent.php`) aprueba la actividad vía el método `saveComent()`:

```php
public function saveComent()
{
    $this->validate([
        'comments' => 'nullable|string|max:65535',
        'status'   => 'required|boolean',
    ]);

    $this->activity->comments = $this->comments;   // Comentario del Jefe de Área
    $this->activity->status   = $this->status;     // 0 = En revisión, 1 = Aprobado
    $this->activity->save();
    // ...
}
```

`Activity::COLUMN_COMMENTS` lo confirma: `'comments' => 'Comentarios del Jefe de Área'` y `'status' => 'Aprobación (1=Aprobado, 0=En revisión)'`.

### 4.3 Matriz de roles real (Fases 1-2)

| Rol | Crea Pevaluacion | Registra Activities | Aprueba Activity (status) | Programa LMS | Publica LMS |
|-----|:---:|:---:|:---:|:---:|:---:|
| **Planning** (`is_planner`/admin/diagnostic) | ✅ (carga académica) | ❌ | ✅ (vía `saveComent`) | ✅ | ✅ (vía `LmsMonitor`) |
| **Jefe de Área** (`is_leadership`) | ❌ | ❌ | ❌ (solo visualiza) | ❌ | ✅ (vía `ActivityOverview` / `LessonMonitor`, solo SCHEDULED) |
| **Coordinación** (`is_coordinacion`) | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Profesor** (`is_profesor`) | ❌ | ✅ (dentro de su carga) | ❌ | ✅ (vía LessonWizard) | ⚠️ vía `saveAndPublish` (ver Fase 4) |
| **Admin** (`is_admin`) | ✅ (bypass) | ✅ | ✅ | ✅ | ✅ |

---

## 5. Fase 3: Preparación LMS

Una vez la actividad está aprobada (`status=true`), el **Profesor** (o Admin) ingresa al **LessonWizard** (`app/Livewire/Profesor/Lms/LessonWizard.php`) para preparar el contenido LMS. Ruta: `/profesor/lms/activity/lesson/new` dentro del grupo con middleware `IsProfesor` (`is_profesor || is_admin`).

**Hechos verificados del wizard:**
- Detecta y conserva diagramas **Mermaid** en el contenido HTML (las vistas lo renderizan con `mermaidEmbed()`; el modelo `LmsHtmlEmbed` expone `is_mermaid`).
- Límite de **10 MB** para recursos subidos.
- Permite **exportar/importar** contenido entre secciones del mismo grado.
- `confirmPublish()` discrimina por rol: planner → `PUBLISH`, profesor → `SCHEDULE` (ver Fase 4).

### 5.1 Secciones (LmsActivitySection)

La actividad puede tener múltiples secciones, cada una con:

- `title`: nombre de la sección (ej. "Inicio", "Desarrollo", "Cierre")
- `description`: texto descriptivo
- `is_visible`: booleano — **Gate 2a**
- `sort_order`: orden de visualización

### 5.2 Contenidos (LmsActivityContent)

Cada sección contiene contenidos de diversos tipos:

| Tipo | Descripción | Requiere Media |
|------|-------------|---------------|
| `TEXT` | Texto enriquecido (body) | No |
| `IMAGE` | Imagen subida a LmsMediaLibrary | Sí |
| `VIDEO` | Video subido | Sí |
| `AUDIO` | Audio subido | Sí |
| `PRESENTATION` | Presentación/PDF | Sí |
| `HTML` | HTML incrustado | No |
| `EMBED` | Embed externo (YouTube, etc.) | No |
| `FILE_PREVIEW` | Vista previa de archivo | Sí |

Cada contenido tiene `is_visible` booleano — **Gate 2b**.

### 5.3 Recursos (LmsActivityResource)

Recursos descargables asociados a la actividad (o a una sección específica):

- `display_name`: nombre visible
- `description`: descripción opcional
- `media_id`: archivo en LmsMediaLibrary
- `is_visible`: booleano — **Gate 2c**
- `download_count`: contador de descargas (incrementa en cada download)

### 5.4 Enlaces (LmsActivityLink)

Enlaces web asociados:

- `url`: destino
- `title`: título visible
- `is_visible`: booleano — **Gate 2d**

### 5.5 HTML Embeds (LmsHtmlEmbed)

Contenido HTML personalizado:

- `html_content`: HTML crudo (con soporte Mermaid.js para diagramas)
- `title`: título
- `is_visible`: booleano — **Gate 2e**
- `section_id`: opcional, asociación a una sección
- `added_by`: autor del embed
- Constante `RENDER_CONDITIONS = ['ALWAYS']`

### 5.6 Configuración de Publicación

Antes de publicar, se configuran:

| Parámetro | Campo en LmsActivityPublication | Efecto |
|-----------|--------------------------------|--------|
| Publicación inmediata | `publish_at = now()`, `status = PUBLISHED` | Visible **completa** al instante |
| Publicación programada | `publish_at = future_date`, `status = SCHEDULED` | Visible en **vista previa** (solo 1ª sección) hasta la fecha; luego se activa sola |
| Publicación con fecha pasada | `publish_at = past/now`, `status = PUBLISHED` | Visible **completa** |
| Expiración | `unpublish_at = future_date` | Se oculta automáticamente |
| Comentarios | `allow_comments` | Habilita/deshabilita comentarios |
| Descargas | `allow_downloads` | Habilita/deshabilita descarga de recursos |

> **⚠️ Invariante:** `publish_at` **nunca queda nulo**. Los botones "Publicar ahora" (LmsMonitor, Coordinación `LessonList`, Leadership `ActivityOverview`) abren un modal con `datetime-local` (fecha opcional; vacía → `now()`) y el servicio `LmsPublicationService::publish()` aplica el mismo default. Un registro PUBLISHED/SCHEDULED con `publish_at` nulo se considera **oculto** (`studentVisibility() = 'hidden'`).
>
> **Nota:** `LessonMonitor` (`/app/leadership/lessons`) usa el **mismo patrón** que `ActivityOverview`: modal con `datetime-local` pre-cargado con `publish_at`, guardia de `Activity.status = true` y `publish_at` nunca nulo. Todas las vías de publicación cumplen el invariante.

---

## 6. Fase 4: Programación y Publicación

Esta fase tiene **dos caminos según el rol**: el profesor (no planner) **programa** (envía a revisión) y Planning/Jefatura/Admin **publican**. **⚠️ Discrepancia verificada:** el flujo `saveAndPublish()` del LessonWizard permite a un profesor publicar directamente (sin pasar por revisión), aunque `confirmPublish()` sí discrimina por rol. Detalles abajo.

### 6.1 Fase 4a: Programación por el Profesor

El profesor ejecuta "Guardar y Programar" en el LessonWizard. Internamente se invoca `LmsPublicationService::publish()` (verificado exacto):

```php
public function publish(Activity $activity, array $data, int $publisherId): LmsActivityPublication
{
    $publishAt = $data['publish_at'] ?? now();
    if (! $publishAt instanceof \Carbon\CarbonInterface) {
        $publishAt = $publishAt ? \Carbon\Carbon::parse($publishAt) : now();
    }
    $isFuture = $publishAt->gt(now());
    $pub = LmsActivityPublication::updateOrCreate(
        ['activity_id' => $activity->id],
        [
            'published_by'    => $publisherId,
            'status'          => $isFuture ? 'SCHEDULED' : 'PUBLISHED',
            'publish_at'      => $publishAt,                 // nunca nulo (default now())
            'unpublish_at'    => $data['unpublish_at'] ?? null,
            'published_at'    => $isFuture ? null : now(),
            'allow_comments'  => $data['allow_comments'] ?? true,
            'allow_downloads' => $data['allow_downloads'] ?? true,
            'notes'           => $data['notes'] ?? null,
        ]
    );

    LmsActivityLog::record($activity->id, $publisherId, 'PUBLISH');  // ← siempre registra PUBLISH
    return $pub;
}
```

**Diferencias clave con la versión previa (verificadas):**
- `publish_at` **nunca queda nulo**: si no llega en `$data`, se normaliza a `now()` (acepta tanto `CarbonInterface` como strings de inputs `datetime-local`).
- El `status` ya **no** depende de que exista `publish_at` (antes: "si hay fecha → SCHEDULED"), sino de la relación con `now()`: fecha **futura → SCHEDULED** (vista previa hasta activar), fecha **ahora/pasada → PUBLISHED** (visible completa).
- `published_at` se escribe solo cuando no es futuro.

**Nota:** `publish()` registra siempre `PUBLISH`. Además, el LessonWizard registra un segundo evento discriminado por rol (línea 4288):

```php
// Si es Planner/Admin → PUBLISH. Si es Profesor → SCHEDULE
LmsActivityLog::record($activityId, auth()->id(),
    $this->isCurrentUserPlanner() ? 'PUBLISH' : 'SCHEDULE');

// Si el usuario es profesor (no planner), notificar a planning
if (! $this->isCurrentUserPlanner()) {
    $this->notifyPlanningScheduled($activityId);
}
```

**Flujo cuando el profesor programa:**

1. El profesor llena el LessonWizard (secciones, contenidos, recursos, fecha programada)
2. Presiona "Guardar y Programar"
3. El sistema registra el evento `SCHEDULE` en `LmsActivityLog` (además del `PUBLISH` que registra `publish()`)
4. Se envía una **notificación** a todos los usuarios con `is_planner = true` o `is_admin = true`
5. La lección queda en estado `SCHEDULED` (si `publish_at` es futuro → vista previa para estudiantes) o `PUBLISHED` (si `publish_at` es ahora/pasado → visible completa). `publish_at` nunca queda nulo (el servicio usa `now()` por defecto)

> **⚠️ Bypass verificado:** `saveAndPublish()` **no** discrimina por rol. Un profesor no-planner puede publicar directamente una lección en estado `PUBLISHED` (confirmado por el test de caracterización `LessonWizardCharacterizationTest::test_saveAndPublish_publica_leccion`). Es decir, el gate de "profesor solo programa" se aplica en `confirmPublish()` pero NO en `saveAndPublish()`.

**Notificación a Planificación:**

```php
private function notifyPlanningScheduled(int $activityId): void
{
    $planners = User::query()
        ->where('is_planner', true)
        ->orWhere('is_admin', true)
        ->get();

    Notification::send($planners, new LessonScheduledForApproval(
        activityId: $activityId,
        teacherName: auth()->user()->fullName ?? 'Profesor',
        activityTitle: $activity->topic ?? 'Lección',
        scheduledAt: $scheduledDate,
    ));
}
```

### 6.2 Fase 4b: Publicación

Hay **tres vías reales** de publicar una lección:

| Vía | Componente | Rol | Condición |
|-----|-----------|-----|-----------|
| **Monitor LMS** | `app/Livewire/Planning/Lms/LmsMonitor.php` | Planning (`is_planner`/admin/diagnostic) | `publish()` / `unpublish()` / `setDraft()`, acciones masivas, modal de programación, modal de ajustes (registra `EDIT`) |
| **LessonWizard** | `app/Livewire/Profesor/Lms/LessonWizard.php` | Planner/Admin → `PUBLISH`; Profesor → `SCHEDULE` | `confirmPublish()`; pero `saveAndPublish()` no discrimina |
| **ActivityOverview (Jefatura, `/activities`)** | `app/Livewire/Leadership/ActivityOverview.php` | Jefe de Área (`is_leadership` solo) | Botón "Publicar" **activo solo si `Activity.status = true`** (Gate 1); modal con `datetime-local` **pre-cargado con `publish_at`** (o `now()` si no hay); verifica `assertCanAccessAsignatura()` (LeadershipService); actualiza a `PUBLISHED` + `publish_at` (nunca nulo; vacío → `now()`) + `published_at` condicional |
| **LessonMonitor (Jefatura, `/lessons`)** | `app/Livewire/Leadership/LessonMonitor.php` | Jefe de Área (`is_leadership` solo) | Igual que `ActivityOverview`: botón "Publicar" **activo solo si `Activity.status = true`**; modal con `datetime-local` **pre-cargado con `publish_at`** (o `now()`); verifica `assertCanAccessAsignatura()`; actualiza a `PUBLISHED` + `publish_at` (nunca nulo; vacío → `now()`) + `published_at` condicional |

**Evento registrado:** `LmsActivityLog.event = 'PUBLISH'` (en las vías que llaman `LmsPublicationService::publish()` o lo replican).

### 6.3 Estados de Publicación

| Estado | Quién lo asigna | ¿Visible para estudiantes? |
|--------|----------------|---------------------------|
| `DRAFT` | Factory/default; lección nunca publicada | ❌ |
| `SCHEDULED` | Profesor al programar, o cualquier vía con `publish_at` futuro (`publish()`) | 🟡 **vista previa** (solo 1ª sección + adjuntos) hasta que `activateScheduled()` la pase a `PUBLISHED` |
| `PUBLISHED` | `publish()` (cualquier vía), `saveAndPublish()`, `activateScheduled()`, `ActivityOverview` / `LessonMonitor` (Jefatura) | ✅ **completa** si `publish_at <= now()`; 🟡 vista previa si `publish_at` futuro; ❌ si `publish_at` nulo |
| `ARCHIVED` | `unpublish()` / `setDraft()` (Planning) | ❌ |

> **Nota:** la aseveración histórica "PUBLISHED solo lo asignan Jefe de Área/Coordinador/Sub Dirección" es **incorrecta**: un profesor puede publicar vía `saveAndPublish()` y un planner vía `LmsMonitor`. Coordinación (`is_coordinacion`) no publica.

### 6.4 Activación de Programadas

La transición `SCHEDULED → PUBLISHED` la ejecuta `LmsPublicationService::activateScheduled()`, invocado por el comando `lms:publish-scheduled`, agendado en el Kernel de consola **cada 5 minutos**:

```php
public function activateScheduled(): int
{
    return LmsActivityPublication::where('status', 'SCHEDULED')
        ->where('publish_at', '<=', now())
        ->update(['status' => 'PUBLISHED', 'published_at' => now()]);
}
```

**⚠️ Corrección verificada:** `activateScheduled()` **NO registra ningún `LmsActivityLog`** en la transición (la aseveración previa "Evento registrado: PUBLISH en la transición" es falsa). El `LmsActivityLog::record(..., 'PUBLISH')` solo ocurre dentro de `LmsPublicationService::publish()` y en el LessonWizard.

**Importante (visibilidad en vista previa):** mientras la lección está `SCHEDULED` (antes de que `activateScheduled()` la active), **sí es visible para los estudiantes** como vista previa (`now() < publish_at` → `studentVisibility() = 'preview'`). Es el único estado pre-activación que aparece en listados, con badge "Vista previa" y limitado a la 1ª sección.

---

## 7. Fase 5: Scoping del Estudiante

### 7.1 Chain de Resolución

Cuando un estudiante se autentica y accede al módulo LMS, el sistema resuelve su sección así:

```
User.is_student = true
  → Estudiant (WHERE user_id = {userId})
    → Inscripcion (WHERE estudiant_id = {estudiantId}) [hasOne]
      → Seccion (WHERE id = {inscripcion.seccion_id})
        → seccion_id → usado para scoping de Pevaluacions
```

### 7.2 StudentScopeService

El `StudentScopeService` (en `app/Services/Estudiant/StudentScopeService.php`) centraliza toda la lógica de scoping. **Verificado contra el código real** (métodos firmados):

```php
$service = app(StudentScopeService::class, ['user' => Auth::user()]);

// IDs de secciones del estudiante — Colección con 1 SOLO id (una inscripción)
$seccionIds = $service->getSeccionIds();  // Collection de 1 id

// Scope para Pevaluacions visibles — devuelve whereRaw('1 = 0') si no hay sección
$service->scopePevaluacions($query);  // → whereIn('seccion_id', $seccionIds)

// Scope para Activities con publicación visible
// ⚠️ NO filtra Activity.status — eso se hace aparte (ver Fase 6)
$service->scopeActivities($query);  // → whereHas pevaluacion(seccion) + whereHas lmsPublication(visibleNow)

// Scope para Recursos visibles
$service->scopeResources($query);   // → where('is_visible', true) + whereHas activity.pevaluacion(seccion)

// Verificación a nivel de instancia (ActivityView)
$service->isActivityVisible($activity);  // → status && lmsPublication?->isVisibleToStudents() && pevaluacion && seccionIds->contains(seccion_id)
```

**Métodos reales del servicio (verificados):** `getEstudiant()`, `getInscripcion()`, `getSeccionIds()`, `getGradoIds()`, `getGradoId()`, `scopePevaluacions()`, `scopeActivities()`, `isActivityVisible()`, `scopeResources()`, `getPensumIds()`, `getPensumsWithAsignatura()`, `getInscripcionData()`.

**Casos borde manejados (memoización y vacíos):**
- Sin `User.estudiant` asociado → `getSeccionIds()` devuelve `collect()` (vacío)
- Sin `Inscripcion` activa → colecciones vacías
- Sin `Seccion` → colecciones vacías
- Cuando la colección está vacía, `scopePevaluacions`/`scopeActivities`/`scopeResources` devuelven `whereRaw('1 = 0')` (query sin resultados)
- `getSeccionIds()`/`getGradoIds()` se memoizan (solo se consultan una vez)

### 7.3 Estudiante sin inscripción

Si un usuario tiene `is_student = true` pero no tiene un `Estudiant` vinculado, o su `Estudiant` no tiene `Inscripcion`, el dashboard se muestra vacío:

```php
if ($seccionIds->isEmpty()) {
    // Retorna colecciones vacías → empty state en la UI
}
```

---

## 8. Fase 6: Visibilidad en Consultas

### 8.1 Gates de Visibilidad — Resumen

Para que una actividad sea visible a un estudiante, TODOS estos gates deben pasar:

```
Gate 1: Activity.status = true (aprobada — por Planning, no por el profesor)
Gate 2: Los contenidos LMS tienen is_visible = true (individualmente)
Gate 3: LmsActivityPublication.status IN ('PUBLISHED', 'SCHEDULED')
Gate 4: publish_at no nulo; now() < publish_at → VISTA PREVIA (solo 1ª sección);
        now() >= publish_at → COMPLETO
Gate 5: unpublish_at >= now (o null)
Gate 6: Pevaluacion.seccion_id IN (secciones del estudiante)
```

> **Nota de verificación:** el Gate 4 quedó **restaurado e implementado** como lógica de 3 niveles (`hidden` / `preview` / `full`) en `studentVisibility()`. `publish_at` nulo se considera oculto (invariante: el servicio nunca persiste nulo).

### 8.2 visibleNow — El scope clave

El scope `visibleNow()` en `LmsActivityPublication` condensa los gates 3, 4 y 5. **Código real verificado** (tras la restauración del Gate 4):

```php
public function scopeVisibleNow($query)
{
    return $query->whereIn('status', ['PUBLISHED', 'SCHEDULED'])   // Gate 3
        ->whereNotNull('publish_at')                               // Gate 4: nulo = oculto
        ->where(fn($q) => $q->whereNull('unpublish_at')->orWhere('unpublish_at', '>=', now())); // Gate 5
}
```

La visibilidad a nivel de instancia se resuelve con `studentVisibility()` (3 niveles) más sus helpers:

```php
public function studentVisibility(): string
{
    if (! in_array($this->status, ['PUBLISHED', 'SCHEDULED'], true)) return 'hidden'; // Gate 3
    if ($this->publish_at === null) return 'hidden';                                  // Gate 4
    if ($this->unpublish_at && now()->gt($this->unpublish_at)) return 'hidden';       // Gate 5
    return now()->lt($this->publish_at) ? 'preview' : 'full';   // Gate 4: preview vs full
}

public function isVisibleToStudents(): bool      { return $this->studentVisibility() !== 'hidden'; }
public function isPreviewToStudents(): bool      { return $this->studentVisibility() === 'preview'; }
public function isFullVisibleToStudents(): bool  { return $this->studentVisibility() === 'full'; }
```

> **Gates 3-5 se condesan así:** `visibleNow()` (lista) y `studentVisibility()` (instancia) cubren `PUBLISHED`/`SCHEDULED`, `publish_at` no nulo, y `unpublish_at` no vencido. La distinción `preview`/`full` la da solo la comparación `now()` vs `publish_at`.

### 8.3 Query Completa en StudentHome (Dashboard)

Tomando `StudentHome.render()` como ejemplo canónico:

```php
// 1. Obtener IDs de actividades publicadas visibles ahora
$publishedActivityIds = LmsActivityPublication::visibleNow()->pluck('activity_id');

// 2. Escoped por sección del estudiante (Gate 6)
$visibleActivityIds = Activity::whereIn('id', $publishedActivityIds)
    ->where('status', true)  // Gate 1
    ->whereHas('pevaluacion', fn($q) =>
        $q->whereIn('seccion_id', $seccionIds)  // Gate 6
    )->pluck('id');

// 3. Usar $visibleActivityIds para todas las consultas del dashboard
// (stats, continue learning, deadlines, subject distribution)
```

### 8.4 Anidación de visibilidad en ActivityView

Cuando el estudiante entra a una actividad, cada subcomponente se filtra por `is_visible`:

```php
// Secciones visibles
$this->sections = $activity->lmsSections()
    ->where('is_visible', true)
    ->with(['visibleContents.media'])  // visibleContents filtra is_visible=true
    ->get();

// Recursos visibles
$this->resources = $activity->lmsResources()
    ->where('is_visible', true)
    ->with('media')
    ->get();

// Enlaces visibles
$this->links = $activity->lmsLinks()
    ->where('is_visible', true)
    ->get();

// Comentarios aprobados
$this->comments = ActivityComment::forActivity($activity->id)
    ->approved()  // is_approved = true
    ->orderBy('created_at', 'desc')
    ->get();
```

**Límite de vista previa (verificado en `ActivityView::mount()`):** si la publicación está en vista previa (`now() < publish_at`), la actividad se reduce a la **1ª sección** (`lmsSections()` está ordenada por `sort_order`) y a los adjuntos vinculados a esa sección (`section_id` = id de la 1ª sección). Los adjuntos globales (sin `section_id`) y el resto de secciones quedan ocultos:

```php
$this->isPreview = $activity->lmsPublication?->isPreviewToStudents() ?? false;
// ...
if ($this->isPreview) {
    $firstSection    = $this->sections->first();
    $firstSectionId  = $firstSection?->id;
    $this->sections  = $firstSection ? collect([$firstSection]) : collect();
    $this->resources = $this->resources->filter(fn($r) => $r->section_id === $firstSectionId);
    $this->links     = $this->links->filter(fn($l) => $l->section_id === $firstSectionId);
    $this->htmlEmbeds = $this->htmlEmbeds->filter(fn($e) => $e->section_id === $firstSectionId);
}
```

> El botón "Marcar como completada" queda deshabilitado en vista previa (solo se permite cuando `status === 'PUBLISHED'` y no está completada). La vista muestra un banner ámbar con la fecha de publicación programada.

---

## 9. Fase 7: Consumo por el Estudiante

### 9.1 Rutas del estudiante

Grupo `Route::prefix('app/estudiante')->middleware(['auth', 'isStudent'])` (verificado en `routes/web.php:352`). Middleware `IsStudent`: admin bypass, resto debe ser `is_student`:

```
/app/estudiante/home          → Dashboard de progreso (StudentHome)
/app/estudiante/perfil        → Perfil del estudiante (Profile)
/app/estudiante/academica     → Información académica (AcademicInfo)
/app/estudiante/lecciones     → Catálogo con filtros (LessonList)
/app/estudiante/recursos      → Recursos con modal preview (ResourceList)
/app/estudiante/activity/{activity}      → Vista detalle de actividad (ActivityView)
/app/estudiante/resource/{resource}/download → Descarga (ResourceDownloadController)
```

> **Corrección:** el blueprint previo omitía `/perfil` y `/academica` y no incluía el parámetro `{activity}`/`{resource}` (route-model binding) ni la ruta de descarga.

### 9.2 Interacciones del Estudiante

Todas las interacciones quedan registradas en `LmsActivityLog` y el progreso se trackea en `LmsActivityProgress`:

| Acción | Evento | ¿Dónde ocurre? |
|--------|--------|---------------|
| Visualizar actividad | `VIEW` | `ActivityView.mount()` |
| Marcar como completada | `COMPLETE` | `ActivityView.markComplete()` |
| Descargar recurso | `RESOURCE_DOWNLOAD` | `ResourceDownloadController.download()` |

**Estructura del log (verificada, `app/Models/app/Academy/Lms/LmsActivityLog.php`):**

```php
LmsActivityLog::record(
    activityId: $activity->id,
    userId: auth()->id(),
    event: 'VIEW',          // | 'COMPLETE' | 'RESOURCE_DOWNLOAD'
    contextId: $resource->id ?? null,       // opcional, para RESOURCE_DOWNLOAD
    contextType: LmsActivityResource::class  // opcional, clase del contexto
);
```

El modelo tiene `$timestamps = false` (no usa created_at/updated_at automáticos), escribe `created_at` manualmente e incluye `ip_address` (`request()->ip()`).

**Modelo LmsActivityProgress (verificado):**

Registra el progreso individual de cada estudiante por actividad. Es un modelo **independiente** (tabla `lms_activity_progress`), sin relación directa desde `Activity`. Se crea/actualiza en `ActivityView`:

| Método | Acción |
|--------|--------|
| `mount()` | `firstOrCreate(activity_id, student_id)` con `status=IN_PROGRESS`, `completion_pct=0`, `first_access_at=now` si no existe |
| `mount()` | `update(['last_access_at' => now()])` si ya existía |
| `markComplete()` | `LmsActivityLog::record(..., 'COMPLETE')` + `updateOrCreate` con `status=COMPLETED`, `completion_pct=100`, `completed_at=now` |
| `completed` (flag) | `mount()` calcula `completed=true` si existe registro `COMPLETED` o log `COMPLETE` del estudiante |

Campos reales: `activity_id`, `student_id` (users.id), `status`, `completion_pct` (decimal:2), `time_spent_secs`, `first_access_at`, `last_access_at`, `completed_at`.

### 9.3 Diagramas Mermaid con Pantalla Completa

Los diagramas Mermaid en `activity-view.blade.php` se renderizan con el componente Alpine `mermaidEmbed()` (definido en `resources/js/lms-student-preview.js`) que incluye (verificado):

- **Toolbar con zoom** (hover para revelar): zoom in/out (`_stepZoom`), porcentaje (`zoom-pct`), ajustar al ancho (`_fitToWidth`), restablecer (`_resetTransform`)
- **Botón de pantalla completa** usando Fullscreen API (`_toggleFullscreen` → `requestFullscreen`/`exitFullscreen`)
- **Drag & pan** (mousedown/mousemove/mouseup con `_startDrag`/`_onDrag`/`_stopDrag`)
- **Zoom táctil** en dispositivos móviles (pinch-to-zoom: `_onTouchStart`/`_onTouchMove`/`_onTouchEnd`)
- **Ctrl+scroll** para zoom (`_onWheel`), rango 0.25×–6×
- **Soporte dark mode** en el toolbar (detecta `.bg-slate-800/900`)

El toolbar se oculta por defecto (`opacity-0`) y aparece al hacer hover; si hay zoom/pan activo queda visible.

### 9.4 Comentarios

Los estudiantes pueden comentar en actividades. Los comentarios pasan por un flujo de moderación (modelo `ActivityComment`, `SoftDeletes`):

```
Estudiante escribe comentario (ActivityView.saveComment)
  → is_approved = false (pendiente de moderación)
  → Profesor/Admin ve comentarios pendientes (CommentModeration, tabs pending/approved/rejected)
  → Aprueba (is_approved=true, approved_at, approved_by) o Rechaza (rejected_at, rejected_by, rejected_reason)
  → Estudiante ve solo comentarios aprobados (scopeApproved + forActivity + orderBy created_at desc)
```

Scopes del modelo (verificados): `pending` (is_approved=false + rejected_at null + no borrado), `approved` (is_approved=true + rejected_at null), `rejected` (rejected_at not null), `forActivity(id)`. Métodos `approve($userId)` / `reject($userId, ?reason)`.

### 9.5 Segunda capa de verificación en ActivityView

Además del scoping de ruta (middleware `IsStudent` + grupo de rutas), el `ActivityView` hace una verificación adicional vía `StudentScopeService::isActivityVisible()`. **Corrección verificada:** la aseveración previa decía `$this->accessDenied = true; return;` — el código real usa **`abort(404)`**:

```php
public function mount(Activity $activity): void
{
    $this->initializeHasStudentScope();  // Inicializa StudentScopeService

    if (!$this->studentService->isActivityVisible($activity)) {
        abort(404);   // ← abort(404), NO accessDenied
    }
    // ...
}
```

`isActivityVisible()` verifica (código real, `StudentScopeService`):
```php
return $activity->status                                              // Gate 1
    && $activity->lmsPublication?->isVisibleToStudents()       // Gates 3-5 (oculto/preview/full)
    && $activity->pevaluacion                                  // Existe
    && $seccionIds->contains($activity->pevaluacion->seccion_id); // Gate 6
```

> **Ojo:** `isActivityVisible()` devuelve `true` tanto para vista previa como para completo (ambos pasan `isVisibleToStudents()`). La distinción de qué se muestra (1ª sección vs. completo) la aplica luego `ActivityView::mount()` según `isPreviewToStudents()`.

Esto previene acceso directo por URL a actividades no publicadas, expiradas, en revisión, o de otra sección (responde HTTP 404).

---

## 10. Fase 8: Expiración y Despublicación

### 10.1 Expiración Automática

Cuando `unpublish_at` es una fecha pasada:

```php
// La actividad ya NO pasa el scope visibleNow()
$q->where(fn($q) => $q->whereNull('unpublish_at')->orWhere('unpublish_at', '>=', now()));
//                                          ↑ esto falla si la fecha ya pasó
```

La actividad desaparece automáticamente de todos los listados del estudiante.

> **Nota:** el Gate 4 (`publish_at`) está implementado (ver 8.1-8.2). Las lecciones programadas con `publish_at` futuro son visibles **como vista previa** (1ª sección), no como contenido completo: no hay regresión de "contenido visible antes de tiempo".

### 10.2 Despublicación Manual

Planning (o quien llama al servicio) puede despublicar explícitamente (verificado, `LmsPublicationService::unpublish()`):

```php
$service->unpublish($activity, $userId);
// LmsActivityPublication.status → 'ARCHIVED'
// LmsActivityLog.event → 'UNPUBLISH'
```

Además `LmsMonitor` ofrece `setDraft()` (estado `DRAFT`).

### 10.3 Efectos de la expiración

| Aspecto | Comportamiento |
|---------|---------------|
| Listados (Home, Lecciones) | ❌ No aparece |
| Link directo (ActivityView) | ❌ abort(404) vía `isActivityVisible()` |
| Comentarios existentes | ❌ Siguen visibles solo si ya fueron aprobados (en BD, no en UI) |
| Recursos descargados | ✅ El archivo sigue en storage, el link de descarga fallará |
| Logs de interacción | ✅ Se conservan en LmsActivityLog |

**Verificación del controlador de descarga (`ResourceDownloadController::download()`):** verifica `isVisibleToStudents()` (404 si no), `allow_downloads` (403 si deshabilitado), `is_visible` (404), que la media sea local (`isLocal()`), registra `RESOURCE_DOWNLOAD` con `context_id`/`context_type` y llama `incrementDownload()`. **No** valida el scoping por sección (un estudiante con el URL directo de un recurso de otra sección podría descargarlo si el activity está publicado).

---

## 11. Diagrama de Flujo Completo

```
PLANNING              PROFESOR                JEFATURA/PLANNING         ESTUDIANTE
══════════            ════════════            ═══════════════════       ═══════════════

  Asignar carga
  → Pevaluacion
  (seccion_id,
   pensum_id, etc.)
       │
       │         Registrar Activities
       ├───────>│  (topic, teaching,
                 │   description, finicial,
                 │   ffinal)  — status NO lo toca el profesor
                 └────────┬──────────┘
                          │
     Aprobar status=true  │   ← Gate 1 (Planning: saveComent)
     ────────────────────>│
                 ┌────────┴──────────┐
                 │  Preparar LMS      │
                 │  Secciones,        │
                 │  Contenidos,       │
                 │  Recursos/Enlaces  │
                 │  is_visible=true   │ ← Gates 2a-2e
                 └────────┬──────────┘
                          │
                  Programar (SCHEDULE)
                  + Notificar a Planning
                 ┌────────┴──────────┐
                 │  Pendiente de      │
                 │  aprobación        │
                 └────────┬──────────┘
                          │
                          │      Revisar y Publicar
                          ├─────>│  status IN ('PUBLISHED','SCHEDULED') ← Gate 3
                                 │  (LmsMonitor / ActivityOverview / LessonMonitor / saveAndPublish)
                                 │  now() vs publish_at  ← Gate 4 (preview/full)
                                 └────────┬──────────┘
                                          │            ┌─ Autenticarse ───┐
                                          │            │  is_student       │
                                          │            │  → /estudiante    │
                                          │            └────────┬────────┘
                                          │                     │
                                          │             ┌─ Scoping ───────┐
                                          │             │  User→Estudiant │
                                          │             │  →Inscripcion   │
                                          │             │  →Seccion       │
                                          │             │  ↓ seccion_id   │
                                          │             └────────┬───────┘
                                          │                      │
                                          ▼                      ▼
                                     ┌──────────────────────────────────┐
                                     │  ¿Actividad visible?             │
                                     │  1. status = true                │
                                     │  2. PUBLISHED/SCHEDULED &        │
                                     │     fechas ok (preview si futura)│
                                     │  3. seccion_id coincide          │
                                     └──────────────────────────────────┘
                                          │              │
                                          NO             SÍ
                                          │              │
                                          ▼              ▼
                                   No visible    ┌──────────────────┐
                                                  │ Home (dashboard) │
                                                  │ Lecciones (grid) │
                                                  │ ActivityView     │
                                                  └────────┬─────────┘
                                                           │
                                              ┌─ Consumo ──┴────────┐
                                              │  VIEW → log         │
                                              │  COMPLETE → log     │
                                              │  Comentar → moderac.│
                                              │  Descargar → log    │
                                              └─────────────────────┘
                                                           │
                                              ┌─ Expira ───┘
                                              │  unpublish_at pasado
                                              │  o ARCHIVED manual
                                              ▼
                                       No visible (oculto)
```

---

## 12. Matriz de Visibilidad (Quién ve qué)

### 12.1 Por estado de la actividad

| Activity.status | Publication.status | ¿Estudiante lo ve? | ¿Profesor lo ve?¹ | ¿Planning/Jefatura? | ¿Admin lo ve? |
|:---:|:---:|:---:|:---:|:---:|:---:|
| false | — | ❌ | ✅ | ✅ | ✅ |
| true | DRAFT | ❌ | ✅ | ✅ | ✅ |
| true | SCHEDULED (publish_at futuro) | 🟡 **vista previa** (1ª sección) | ✅ | ✅ | ✅ |
| true | PUBLISHED (publish_at futuro) | 🟡 **vista previa** (1ª sección) | ✅ | ✅ | ✅ |
| true | PUBLISHED (publish_at pasado/now) | ✅ **completa** | ✅ | ✅ | ✅ |
| true | PUBLISHED (publish_at nulo) | ❌ (oculto) | ✅ | ✅ | ✅ |
| true | PUBLISHED (expirado) | ❌ | ✅ | ✅ | ✅ |
| true | ARCHIVED | ❌ | ✅ | ✅ | ✅ |

> ¹ El profesor ve las actividades de sus Pevaluacions (su carga). Planning/Jefatura ven todo su alcance. **Verificado:** en la vista previa (futuro) el estudiante ve la lección con badge "Vista previa", solo la 1ª sección y sus adjuntos vinculados.

### 12.2 Por sección del estudiante

| Sección del Estudiante | Pevaluacion.seccion_id | ¿Visible? |
|:---:|:---:|:---:|
| A | A | ✅ |
| A | B | ❌ |
| — (sin inscripción) | cualquiera | ❌ (colecciones vacías) |

### 12.3 Por visibilidad de contenidos

| is_visible | ¿Visible en ActivityView? |
|:---:|:---:|
| true | ✅ Se muestra |
| false | ❌ Oculto (no se consulta) |

---

## 13. Referencia de Queries Clave

### 13.1 Scope para listados de actividades visibles

```sql
-- Todos los gates aplicados (código real verificado)
SELECT a.* FROM activities a
JOIN pevaluacions p ON p.id = a.pevaluacion_id
JOIN lms_activity_publications pub ON pub.activity_id = a.id
WHERE a.status = 1                                                  -- Gate 1
  AND pub.status IN ('PUBLISHED', 'SCHEDULED')                      -- Gate 3
  AND pub.publish_at IS NOT NULL                                    -- Gate 4: nulo = oculto
  AND (pub.unpublish_at IS NULL OR pub.unpublish_at >= NOW())        -- Gate 5
  AND p.seccion_id IN (                                              -- Gate 6
      SELECT s.id FROM seccions s
      JOIN inscripcions i ON i.seccion_id = s.id
      JOIN estudiants e ON e.id = i.estudiant_id
      WHERE e.user_id = {userId}
  );
-- Nota: la distinción preview (1ª sección) vs full se decide en PHP
-- comparando NOW() con pub.publish_at (studentVisibility()).
```

### 13.2 Scope para dashboard (StudentHome)

```php
// PHP equivalente usando el servicio de scoping (verificado en StudentHome.render())
$publishedIds = LmsActivityPublication::visibleNow()->pluck('activity_id');

$visibleIds = Activity::where('status', true)
    ->whereIn('id', $publishedIds)
    ->whereHas('pevaluacion', fn($q) =>
        $q->whereIn('seccion_id', $service->getSeccionIds())
    )->pluck('id');

// Stats
$total      = $visibleIds->count();
$completed  = LmsActivityLog::where('user_id', authId)
                ->whereIn('activity_id', $visibleIds)
                ->where('event', 'COMPLETE')
                ->pluck('activity_id')->unique()->count();
```

### 13.3 Verificación en ActivityView

```php
// Código REAL: verificación completa con StudentScopeService::isActivityVisible()
// (incluye Gate 6 de sección), NO solo isVisibleToStudents()
if (!$this->studentService->isActivityVisible($activity)) {
    abort(404);
}
```

### 13.4 Transición de programadas a publicadas

```sql
-- Lo que ejecuta activateScheduled() periódicamente (cada 5 min, comando lms:publish-scheduled)
UPDATE lms_activity_publications
SET status = 'PUBLISHED', published_at = NOW()
WHERE status = 'SCHEDULED'
  AND publish_at <= NOW();
```

---

## Referencias

| Archivo | Propósito |
|---------|-----------|
| `app/Models/app/Academy/Activity.php` | Modelo de actividad, relaciones con LMS, COLUMN_COMMENTS |
| `app/Models/app/Academy/Pevaluacion.php` | Plan de evaluación, ancla de scoping, SoftDeletes |
| `app/Models/app/Academy/Lms/LmsActivityPublication.php` | Publicación, `studentVisibility()` (hidden/preview/full), scope `visibleNow` |
| `app/Models/app/Academy/Lms/LmsActivitySection.php` | Sección de actividad LMS (contents/visibleContents) |
| `app/Models/app/Academy/Lms/LmsActivityContent.php` | Contenido de sección (8 tipos + is_required) |
| `app/Models/app/Academy/Lms/LmsActivityResource.php` | Recurso descargable (incrementDownload) |
| `app/Models/app/Academy/Lms/LmsActivityLink.php` | Enlace (link_type) |
| `app/Models/app/Academy/Lms/LmsHtmlEmbed.php` | Embed HTML (render_condition) |
| `app/Models/app/Academy/Lms/LmsActivityLog.php` | Log de interacciones (record) |
| `app/Models/app/Academy/Lms/LmsActivityProgress.php` | Progreso individual por estudiante |
| `app/Models/app/Academy/Lms/ActivityComment.php` | Comentarios con moderación |
| `app/Models/app/Learner/Estudiant.php` | Estudiante, inscripción, sección |
| `app/Models/app/Academy/Inscripcion.php` | Inscripción (estudiant → sección) |
| `app/Services/Estudiant/StudentScopeService.php` | Scoping centralizado por inscripción |
| `app/Services/Lms/LmsPublicationService.php` | Publicación (publish_at nunca nulo; futuro → SCHEDULED), despublicación, activateScheduled |
| `app/Livewire/Planning/Pevaluacion/IndexComponent.php` | Fase 1: creación de carga académica |
| `app/Livewire/Planning/Activities/IndexComponent.php` | Fase 2: aprobación de actividades (saveComent) |
| `app/Livewire/Profesor/Activity/IndexComponent.php` | Fase 2: registro de actividades por el profesor |
| `app/Livewire/Profesor/Lms/LessonWizard.php` | Fase 3-4: preparación y publicación LMS |
| `app/Livewire/Planning/Lms/LmsMonitor.php` | Fase 4b: monitor de publicación (Planning) |
| `app/Livewire/Leadership/ActivityOverview.php` | Fase 4b: publicación por Jefatura con modal de fecha (solo SCHEDULED y actividad aprobada) |
| `app/Livewire/Leadership/LessonMonitor.php` | Fase 4b: publicación por Jefatura con modal de fecha (solo SCHEDULED y actividad aprobada) |
| `app/Livewire/Student/Lms/StudentHome.php` | Dashboard de progreso (consumo) |
| `app/Livewire/Student/Lms/LessonList.php` | Catálogo de lecciones (consumo) |
| `app/Livewire/Student/Lms/ActivityView.php` | Vista de actividad (consumo) |
| `app/Livewire/Student/Lms/ResourceList.php` | Recursos con modal preview (consumo) |
| `app/Http/Controllers/Lms/ResourceDownloadController.php` | Descarga de recursos |
| `app/Console/Commands/PublishScheduledLmsContent.php` | Comando lms:publish-scheduled |
| `app/Console/Kernel.php` | Agenda lms:publish-scheduled cada 5 min |
| `resources/js/lms-student-preview.js` | Componente Alpine mermaidEmbed |
