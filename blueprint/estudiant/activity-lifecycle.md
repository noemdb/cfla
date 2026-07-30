# Flujo Completo de una Actividad / Lección LMS

**Blueprint de Dominio**
_Última revisión:_ 2026-07-30

---

## Tabla de Contenidos

1. [Visión General](#1-visión-general)
2. [Cadena Completa de Modelos](#2-cadena-completa-de-modelos)
3. [Fase 1: Asignación de Carga Académica (Sub Dirección)](#3-fase-1-asignacin-de-carga-acadmica-sub-direccin)
4. [Fase 2: Registro de Actividades por el Profesor](#4-fase-2-registro-de-actividades-por-el-profesor)
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

Este documento traza el ciclo de vida completo de una **actividad de planificación** (evaluativa) desde que **Sub Dirección asigna la carga académica** (creando el `Pevaluacion`), pasando por el **registro de actividades por el profesor**, hasta que es visible y consumible por un estudiante en el módulo LMS.

El flujo involucra **4 roles** (Sub Dirección, Jefe de Área/Coordinador, Profesor, Estudiante) y **8 fases** secuenciales, con múltiples gates de visibilidad que determinan qué ve cada quién.

```
Carga Académica → Registro Actividades → Preparación LMS → Programación (Profesor) → Publicación (Jefe Área/Coord.) → Scoping → Consulta → Consumo → Expiración
```

---

## 2. Cadena Completa de Modelos

### Jerarquía estructural

```
User (rol: planner | profesor | admin)
 │
 ├── Profesor (profesor_id)
 │
 ├── Pevaluacion (profesor_id, seccion_id, pensum_id, lapso_id)
 │    │
 │    ├── Seccion (seccion_id ← student scope target)
 │    │    ├── Grado (grado_id)
 │    │    │    ├── Pestudio (pestudio_id)
 │    │    │    └── Pensum (grado_id, asignatura_id)
 │    │    │         └── Pevaluacion (…)
 │    │    │              └── Activity (…)
 │    │    └── Inscripcion (seccion_id, estudiant_id)
 │    │         └── Estudiant (user_id → User.is_student = true)
 │    │
 │    ├── Pensum (asignatura_id)
 │    │    └── Asignatura (name, code)
 │    │
 │    ├── Lapso (name, finicial, ffinal)
 │    │
 │    └── Activities (hasMany)
 │         │
 │         ├── LmsActivityPublication (hasOne)
 │         │    ├── status: DRAFT | SCHEDULED | PUBLISHED | ARCHIVED
 │         │    ├── publish_at, unpublish_at
 │         │    └── allow_comments, allow_downloads
 │         │
 │         ├── LmsActivitySection (hasMany, ordenado)
 │         │    ├── title, description, is_visible
 │         │    └── LmsActivityContent (hasMany, ordenado)
 │         │         ├── type: TEXT | VIDEO | AUDIO | IMAGE | PRESENTATION | HTML | EMBED | FILE_PREVIEW
 │         │         ├── body, title, media_id, is_visible
 │         │         └── Media (LmsMediaLibrary, polimórfica)
 │         │
 │         ├── LmsActivityResource (hasMany, ordenado)
 │         │    ├── display_name, description, is_visible
 │         │    ├── media_id → LmsMediaLibrary
 │         │    └── download_count
 │         │
 │         ├── LmsActivityLink (hasMany, ordenado)
 │         │    ├── url, title, is_visible
 │         │
 │         ├── LmsHtmlEmbed (hasMany, ordenado)
 │         │    ├── html_content, title, is_visible
 │         │
 │         ├── LmsActivityLog (hasMany)
 │         │    ├── event: VIEW | COMPLETE | RESOURCE_DOWNLOAD | PUBLISH | UNPUBLISH | SCHEDULE | EDIT
 │         │    ├── user_id, created_at
 │         │    └── context_id, context_type (polimórfico)
 │         │
 │         ├── LmsActivityProgress (hasMany)
 │         │    ├── status: IN_PROGRESS | COMPLETED
 │         │    ├── student_id (users.id), completion_pct, first_access_at, last_access_at, completed_at
 │         │    └── Registro de progreso individual por estudiante
 │         │
 │         └── ActivityComment (hasMany, SoftDeletes)
 │              ├── body, user_id
 │              ├── is_approved, approved_at, approved_by
 │              └── rejected_at, rejected_by, rejected_reason
```

### Relación con el estudiante

```
User (auth)
 │
 └── Estudiant (user_id)
      │
      ├── Inscripcion (estudiant_id)
      │    └── Seccion (seccion_id = pevaluacion.seccion_id) ← SCOPE KEY
      │
      └── Administrativa (estudiant_id) — datos administrativos
```

---

## 3. Fase 1: Asignación de Carga Académica (Sub Dirección)

### 3.1 Creación del Pevaluacion por Sub Dirección

**Sub Dirección** (personal administrativo / planificación académica) asigna la **carga académica** de cada profesor, lo que crea automáticamente un registro en `pevaluacions` (Plan de Evaluación). Este registro agrupa actividades por:

- **Sección** (`seccion_id`): víncula a un grupo-aula específico
- **Pensum / Asignatura** (`pensum_id`): define qué materia cubre
- **Lapso** (`lapso_id`): período académico (1er/2do/3er momento)
- **Profesor** (`profesor_id`): docente responsable
- **Grupo Estable** (`grupo_estable_id`): opcional, para grupos especiales

El `Pevaluacion` es el **ancla de scoping**: el estudiante solo ve actividades cuyo `Pevaluacion.seccion_id` coincide con la sección de su inscripción.

> **Importante:** El profesor **no crea** el `Pevaluacion`. Este es creado por Sub Dirección cuando se le asigna una carga académica al profesor. El profesor trabaja **dentro** de Pevaluacions ya existentes.

---

## 4. Fase 2: Registro de Actividades por el Profesor

### 4.1 Creación de Actividades

El **Profesor** registra sus **Activities** (actividades de planificación) dentro del `Pevaluacion` que Sub Dirección le asignó. Cada actividad contiene:

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

### 4.2 Aprobación de la Actividad

La actividad debe tener `status = true` (Aprobado). Este campo es binario:

- **`status = true`** → La actividad está aprobada y puede continuar al flujo LMS.
- **`status = false`** → La actividad está en revisión. Los estudiantes **nunca** la ven, independientemente del estado de publicación LMS.

**Gate de visibilidad #1:** `Activity.status = true`

### 4.3 Rol del Profesor vs Sub Dirección

| Rol | Crea Pevaluacion | Registra Activities | Aprueba Activity | Programa LMS | Publica LMS |
|-----|:---:|:---:|:---:|:---:|:---:|
| **Sub Dirección** | ✅ (carga académica) | ❌ | ❌ | ❌ | ✅ |
| **Jefe de Área / Coordinador** | ❌ | ❌ | ❌ | ❌ | ✅ |
| **Profesor** | ❌ | ✅ (dentro de su carga) | ✅ (status) | ✅ (vía LessonWizard) | ❌ |
| **Admin** | ✅ (bypass) | ✅ | ✅ | ✅ | ✅ |

---

## 5. Fase 3: Preparación LMS

Una vez la actividad está aprobada, el Profesor/Planificador ingresa al **LessonWizard** (`app/Livewire/Profesor/Lms/LessonWizard.php`) para preparar el contenido LMS.

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

### 5.6 Configuración de Publicación

Antes de publicar, se configuran:

| Parámetro | Campo en LmsActivityPublication | Efecto |
|-----------|--------------------------------|--------|
| Publicación inmediata | `publish_at = null`, `status = PUBLISHED` | Visible al instante |
| Publicación programada | `publish_at = future_date`, `status = SCHEDULED` | Se activa automáticamente |
| Expiración | `unpublish_at = future_date` | Se oculta automáticamente |
| Comentarios | `allow_comments` | Habilita/deshabilita comentarios |
| Descargas | `allow_downloads` | Habilita/deshabilita descarga de recursos |

---

## 6. Fase 4: Programación y Publicación

Esta fase tiene **dos actores distintos**: el profesor solo puede **programar** (enviar a revisión), mientras que la **publicación** efectiva es exclusiva de Jefe de Área, Coordinador o Sub Dirección.

### 6.1 Fase 4a: Programación por el Profesor

El profesor ejecuta "Guardar y Programar" en el LessonWizard. Internamente se invoca `LmsPublicationService::publish()`:

```php
public function publish(Activity $activity, array $data, int $publisherId): LmsActivityPublication
{
    $pub = LmsActivityPublication::updateOrCreate(
        ['activity_id' => $activity->id],
        [
            'published_by'    => $publisherId,
            'status'          => isset($data['publish_at']) ? 'SCHEDULED' : 'PUBLISHED',
            'publish_at'      => $data['publish_at'] ?? null,
            'unpublish_at'    => $data['unpublish_at'] ?? null,
            'published_at'    => isset($data['publish_at']) ? null : now(),
            'allow_comments'  => $data['allow_comments'] ?? true,
            'allow_downloads' => $data['allow_downloads'] ?? true,
            'notes'           => $data['notes'] ?? null,
        ]
    );

    LmsActivityLog::record($activity->id, $publisherId, 'PUBLISH');
    return $pub;
}
```

**Pero el LessonWizard discrimina por rol** (línea 4288):

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
3. El sistema registra el evento `SCHEDULE` en `LmsActivityLog`
4. Se envía una **notificación** a todos los usuarios con `is_planner = true` o `is_admin = true`
5. La lección queda en estado `SCHEDULED` (si tiene `publish_at`) o `PUBLISHED` (si no tiene fecha, queda visible inmediatamente — aunque el rol de profesor no debería poder publicar sin fecha según la lógica de negocio)

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

### 6.2 Fase 4b: Publicación por Jefe de Área / Coordinador / Sub Dirección

Los usuarios con rol **Jefe de Área (`is_leadership`)**, **Coordinador/Planificador (`is_planner`)** o **Sub Dirección/Admin (`is_admin`)** pueden revisar y **publicar** oficialmente la lección. Esto puede ocurrir:

- **Desde el Monitor de LMS** (`app/Livewire/Planning/Lms/LmsMonitor.php`): Un planner revisa las lecciones programadas pendientes y las publica.
- **Desde el LessonWizard**: Si el usuario autenticado es planner/admin, el evento registrado es `PUBLISH` (no `SCHEDULE`).

**Evento registrado:** `LmsActivityLog.event = 'PUBLISH'`

### 6.3 Estados de Publicación

| Estado | Quién lo asigna | ¿Visible para estudiantes? |
|--------|----------------|---------------------------|
| `DRAFT` | — (nunca publicado) | ❌ |
| `SCHEDULED` | Profesor (al programar) o Planner (si fijó fecha futura) | ❌ (hasta `publish_at`) |
| `PUBLISHED` | **Solo Jefe de Área / Coordinador / Sub Dirección** | ✅ (si cumple fechas) |
| `ARCHIVED` | Planner/Admin (despublicación manual) | ❌ |

### 6.3 Activación de Programadas

Un comando/scheduled task ejecuta periódicamente:

```php
$pub->activateScheduled(); // status SCHEDULED → PUBLISHED cuando publish_at <= now
```

**Evento registrado:** `LmsActivityLog.event = 'PUBLISH'` (en la transición)

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

El `StudentScopeService` (en `app/Services/Estudiant/StudentScopeService.php`) centraliza toda la lógica de scoping:

```php
$service = app(StudentScopeService::class, ['user' => Auth::user()]);

// Obtener IDs de secciones del estudiante
$seccionIds = $service->getSeccionIds();  // Collection de 1+ IDs

// Scope para Pevaluacions visibles
$service->scopePevaluacions($query)
  ->whereIn('seccion_id', $seccionIds);

// Scope para Activities con publicación visible
$service->scopeActivities($query)
  ->whereHas('pevaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds))
  ->whereHas('lmsPublication', fn($q) => $q->visibleNow());

// Scope para Recursos visibles
$service->scopeResources($query)
  ->where('is_visible', true)
  ->whereHas('activity.pevaluacion', fn($q) => $q->whereIn('seccion_id', $seccionIds));
```

**Casos borde manejados:**
- Sin `User.estudiant` asociado → colecciones vacías (no hay datos)
- Sin `Inscripcion` activa → colecciones vacías
- Sin `Seccion` → colecciones vacías

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
Gate 1: Activity.status = true (aprobada por el profesor)
Gate 2: Los contenidos LMS tienen is_visible = true (individualmente)
Gate 3: LmsActivityPublication.status = 'PUBLISHED'
Gate 4: publish_at <= now (o null)
Gate 5: unpublish_at >= now (o null)
Gate 6: Pevaluacion.seccion_id IN (secciones del estudiante)
```

### 8.2 visibleNow — El scope clave

El scope `visibleNow()` en `LmsActivityPublication` condensa los gates 3, 4 y 5:

```php
public function scopeVisibleNow($query)
{
    return $query->where('status', 'PUBLISHED')
        ->where(fn($q) => $q->whereNull('publish_at')->orWhere('publish_at', '<=', now()))
        ->where(fn($q) => $q->whereNull('unpublish_at')->orWhere('unpublish_at', '>=', now()));
}
```

Equivalente a nivel de instancia:

```php
public function isVisibleToStudents(): bool
{
    if ($this->status !== 'PUBLISHED')                     // Gate 3
        return false;
    if ($this->publish_at && now()->lt($this->publish_at))  // Gate 4
        return false;
    if ($this->unpublish_at && now()->gt($this->unpublish_at)) // Gate 5
        return false;
    return true;
}
```

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

---

## 9. Fase 7: Consumo por el Estudiante

### 9.1 Rutas del estudiante

```
/app/estudiante/home         → Dashboard de progreso (StudentHome)
/app/estudiante/lecciones    → Catálogo con filtros (LessonList)
/app/estudiante/activity/{id} → Vista detalle de actividad (ActivityView)
/app/estudiante/recursos     → Recursos con modal preview (ResourceList)
```

### 9.2 Interacciones del Estudiante

Todas las interacciones quedan registradas en `LmsActivityLog` y el progreso se trackea en `LmsActivityProgress`:

| Acción | Evento | ¿Dónde ocurre? |
|--------|--------|---------------|
| Visualizar actividad | `VIEW` | `ActivityView.mount()` |
| Marcar como completada | `COMPLETE` | `ActivityView.markComplete()` |
| Descargar recurso | `RESOURCE_DOWNLOAD` | `ResourceDownloadController.download()` |

**Estructura del log:**

```php
LmsActivityLog::record(
    activityId: $activity->id,
    userId: auth()->id(),
    event: 'VIEW',          // | 'COMPLETE' | 'RESOURCE_DOWNLOAD'
    contextId: $resource->id ?? null,       // opcional, para RESOURCE_DOWNLOAD
    contextType: LmsActivityResource::class  // opcional, clase del contexto
);
```

**Modelo LmsActivityProgress:**

Registra el progreso individual de cada estudiante por actividad. Se crea/actualiza en `ActivityView`:

| Método | Acción |
|--------|--------|
| `mount()` | `firstOrCreate` con `status=IN_PROGRESS` si no existe |
| `mount()` | `update(['last_access_at' => now()])` si ya existe |
| `markComplete()` | `updateOrCreate` con `status=COMPLETED`, `completion_pct=100` |

### 9.3 Diagramas Mermaid con Pantalla Completa

Los diagramas Mermaid en `activity-view.blade.php` se renderizan con el componente Alpine `mermaidEmbed()` que incluye:

- **Toolbar con zoom** (hover para revelar): zoom in/out, porcentaje, ajustar al ancho
- **Botón de pantalla completa** (`⛶`) usando Fullscreen API
- **Drag & pan** para diagramas ampliados
- **Zoom táctil** en dispositivos móviles (pinch-to-zoom)
- **Soporte dark mode** en el toolbar

El toolbar se oculta por defecto y aparece al hacer hover sobre el diagrama. En fullscreen, el toolbar queda fijo arriba a la derecha y siempre visible.

### 9.3 Comentarios

Los estudiantes pueden comentar en actividades. Los comentarios pasan por un flujo de moderación:

```
Estudiante escribe comentario
  → is_approved = false (pendiente de moderación)
  → Profesor/Admin ve comentario pendiente
  → Aprueba (is_approved = true) o Rechaza (rejected_at)
  → Estudiante ve solo comentarios aprobados (scopeApproved)
```

### 9.4 Segunda capa de verificación en ActivityView

Además del scoping de ruta (middleware `IsStudent` + grupo de rutas), el `ActivityView` hace una verificación adicional vía `StudentScopeService::isActivityVisible()` que chequea 3 gates:

```php
public function mount(Activity $activity): void
{
    $this->initializeHasStudentScope();  // Inicializa StudentScopeService

    if (!$this->studentService->isActivityVisible($activity)) {
        $this->accessDenied = true;
        return;
    }
    // ...
}
```

`isActivityVisible()` verifica:
```php
$activity->status                                              // Gate 1
    && $activity->lmsPublication?->isVisibleToStudents()       // Gates 3-5
    && $activity->pevaluacion                                  // Existe
    && $seccionIds->contains($activity->pevaluacion->seccion_id); // Gate 6
```

Esto previene acceso directo por URL a actividades no publicadas, expiradas, en revisión, o de otra sección.

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

### 10.2 Despublicación Manual

El profesor/planificador puede despublicar explícitamente:

```php
$service->unpublish($activity, $userId);
// LmsActivityPublication.status → 'ARCHIVED'
// LmsActivityLog.event → 'UNPUBLISH'
```

### 10.3 Efectos de la expiración

| Aspecto | Comportamiento |
|---------|---------------|
| Listados (Home, Lecciones) | ❌ No aparece |
| Link directo (ActivityView) | ❌ abort(404) |
| Comentarios existentes | ❌ Siguen visibles solo si ya fueron aprobados (en BD, no en UI) |
| Recursos descargados | ✅ El archivo sigue en storage, el link de descarga fallará |
| Logs de interacción | ✅ Se conservan en LmsActivityLog |

---

## 11. Diagrama de Flujo Completo

```
SUB DIRECCIÓN          PROFESOR                JEFE ÁREA / COORD.       ESTUDIANTE
══════════════         ════════════            ═══════════════════       ═══════════════

  Asignar carga
  → Pevaluacion
  (seccion_id,
   pensum_id, etc.)
       │
       │         Registrar Activities
       ├───────>│  (topic, teaching,
                 │   description, finicial,
                 │   ffinal, status=false)
                 └────────┬──────────┘
                          │
                  Aprobar │ status=true  ← Gate 1
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
                          ├─────>│  status = 'PUBLISHED' ← Gate 3
                                 │  publish_at <= now    ← Gate 4
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
                                     │  2. PUBLISHED & fechas ok        │
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

| Activity.status | Publication.status | ¿Estudiante lo ve? | ¿Profesor lo ve? | ¿Admin lo ve? |
|:---:|:---:|:---:|:---:|:---:|
| false | — | ❌ | ✅ | ✅ |
| true | DRAFT | ❌ | ✅ | ✅ |
| true | SCHEDULED (futuro) | ❌ | ✅ | ✅ |
| true | PUBLISHED (vigente) | ✅ | ✅ | ✅ |
| true | PUBLISHED (expirado) | ❌ | ✅ | ✅ |
| true | ARCHIVED | ❌ | ✅ | ✅ |

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
-- Todos los gates aplicados
SELECT a.* FROM activities a
JOIN pevaluacions p ON p.id = a.pevaluacion_id
JOIN lms_activity_publications pub ON pub.activity_id = a.id
WHERE a.status = 1                                                  -- Gate 1
  AND pub.status = 'PUBLISHED'                                      -- Gate 3
  AND (pub.publish_at IS NULL OR pub.publish_at <= NOW())            -- Gate 4
  AND (pub.unpublish_at IS NULL OR pub.unpublish_at >= NOW())        -- Gate 5
  AND p.seccion_id IN (                                              -- Gate 6
      SELECT s.id FROM seccions s
      JOIN inscripcions i ON i.seccion_id = s.id
      JOIN estudiants e ON e.id = i.estudiant_id
      WHERE e.user_id = {userId}
  );
```

### 13.2 Scope para dashboard (StudentHome)

```php
// PHP equivalente usando el servicio de scoping
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
// Doble verificación: ruta + instancia
abort_unless($activity->lmsPublication?->isVisibleToStudents(), 404);
```

### 13.4 Transición de programadas a publicadas

```sql
-- Lo que ejecuta activateScheduled() periódicamente
UPDATE lms_activity_publications
SET status = 'PUBLISHED', published_at = NOW()
WHERE status = 'SCHEDULED'
  AND publish_at <= NOW();
```

---

## Referencias

| Archivo | Propósito |
|---------|-----------|
| `app/Models/app/Academy/Activity.php` | Modelo de actividad, relaciones con LMS |
| `app/Models/app/Academy/Pevaluacion.php` | Plan de evaluación, ancla de scoping |
| `app/Models/app/Academy/Lms/LmsActivityPublication.php` | Publicación, visibilidad, scope visibleNow |
| `app/Models/app/Academy/Lms/LmsActivitySection.php` | Sección de actividad LMS |
| `app/Models/app/Academy/Lms/LmsActivityContent.php` | Contenido de sección (8 tipos) |
| `app/Models/app/Academy/Lms/LmsActivityResource.php` | Recurso descargable |
| `app/Models/app/Academy/Lms/LmsActivityLog.php` | Log de interacciones |
| `app/Models/app/Academy/Lms/ActivityComment.php` | Comentarios con moderación |
| `app/Models/app/Learner/Estudiant.php` | Estudiante, inscripción, sección |
| `app/Models/app/Academy/Inscripcion.php` | Inscripción (estudiant → sección) |
| `app/Services/Estudiant/StudentScopeService.php` | Scoping centralizado por inscripción |
| `app/Services/Lms/LmsPublicationService.php` | Servicio de publicación/despublicación |
| `app/Livewire/Student/Lms/StudentHome.php` | Dashboard de progreso (consumo) |
| `app/Livewire/Student/Lms/LessonList.php` | Catálogo de lecciones (consumo) |
| `app/Livewire/Student/Lms/ActivityView.php` | Vista de actividad (consumo) |
| `app/Http/Controllers/Lms/ResourceDownloadController.php` | Descarga de recursos |
