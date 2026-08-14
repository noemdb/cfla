# Plan Staff Engineer — Mejora del Sistema de Notificación en Tiempo Real (Laravel Reverb)

> **Proyecto**: CFLA · **Módulo**: LMS (Lección programada → aprobación de Planificación)
> **Estado**: En ejecución (ver §2.5) · **Fecha**: 2026-08-12 · **Última actualización**: 2026-08-13
> **Alcance**: Hallazgos de auditoría + Opciones 1–11 (pack completo de mejora sobre la base implementada en `ea498ac5` y `9ff71beb`)

---

## 0. Estado actual (baseline **auditado** contra el código)

> ⚠️ Esta sección corrige la tabla original: se verificó cada afirmación contra los archivos reales.
> Varias afirmaciones del v1 no se sostienen (marcadas con **✏️**).

| Componente | Estado real verificado | Notas |
|---|---|---|
| `app/Events/Lms/LessonScheduled.php` | `ShouldBroadcastNow`; `broadcastOn()` → 1 `PrivateChannel('App.Models.User.{id}')` por destinatario; `broadcastAs() = lesson.scheduled`; payload con `type/activity_id/teacher_name/lesson_title/scheduled_at/message/url` | Correcto. |
| `app/Livewire/Profesor/Lms/LessonWizard.php` (`notifyPlanningScheduled`, ≈L4835) | **Único emisor** del evento en todo el código. Query de destinatarios global sin scope de grado: `where('is_planner' OR 'is_admin' OR 'is_leadership' OR 'is_coordinacion')` | ✏️ El v1 asumía emisión generalizada; en realidad solo el flujo del profesor emite. |
| `app/Livewire/Planning/Lms/LessonPendingCount.php` | Contador **global** de `SCHEDULED`. Duplicación `$listeners` (L12–16) + `getListeners()` (L18–26). Escucha `lesson-scheduled`, `lesson-published`, `lesson-approved` | ✏️ `lesson-published`/`lesson-approved` **nunca se emiten** (dead code). |
| `resources/views/livewire/planning/lms/lesson-pending-count.blade.php` | Badge ámbar solo si `$count > 0`; `wire:poll.5s` como fallback | Correcto. |
| `resources/js/bootstrap.js` | `Echo.private(App.Models.User.{id})` → `.listen('.lesson.scheduled')` → `Livewire.dispatch('lesson-scheduled')` | Correcto. |
| Layouts (`data-reverb`/`data-user-id`) | **Solo** `resources/views/layouts/dashboard.blade.php` (L2–5) tiene ambos atributos. | ✏️ El v1 afirmaba "dashboard, planning, coordinacion, director, profesors"; esos layouts no existen como tales — son navbars dentro de `dashboard`. |
| Navbars con badge | **Solo** `resources/views/components/navbars/planning-items.blade.php:177` (desktop). | ✏️ Faltan: `planning-items-mobile`, `coordinacion-items`, `director-items`, `admin-items`. |
| `routes/channels.php` | `App.Models.User.{id}` → `(int)$user->id === (int)$id` | Correcto. |
| Emisores de broadcast | Solo `LessonWizard`. `LmsMonitor::publish()` (L395) y `saveSchedule()` (L461) cambian a `SCHEDULED/PUBLISHED` **sin** emitir broadcast. | ✏️ Hueco funcional: planificador programa → badge de otros responsables solo se actualiza por poll (≤5s), no en vivo. |
| Reverb | Corriendo en `:8090`; supervisor configurado | Correcto. |
| Tests | 466 passed; assert de `LessonScheduled` en `LessonWizardCharacterizationTest`. **Sin test de `LessonPendingCount` ni de broadcast desde el monitor.** | ✏️ Cobertura incompleta. |

### 0.1 Hallazgos críticos (base del plan)

| # | Hallazgo | Impacto | Dónde se resuelve |
|---|---|---|---|
| **H1** | **Emisión descentralizada**: el dispatch vive dentro de `notifyPlanningScheduled()` del wizard. Otros flujos que producen `SCHEDULED` (monitor `publish()` con fecha, `saveSchedule()`, bulk) no notifican. | Alto — notificaciones en vivo inconsistentes según el camino usado | **Opción 11** (centralizar en `LmsPublicationService`) |
| **H2** | **Crash-risk de `ShouldBroadcastNow`**: si Reverb está caído, el broadcaster lanza excepción síncrona y **rompe `saveStep2` del profesor** aunque la notificación DB ya se haya guardado. | Alto — el push puede romper el request | **Opción 9** (cola/guard) |
| **H3** | **Contador global sin scope de rol/grado**: todos los responsables ven todas las `SCHEDULED`, aunque coordinación solo cubre ciertos grados (existe `CoordinacionScopeService`). | Medio — ruido y conteo engañoso | **Opción 2** |
| **H4** | **Cobertura UI incompleta**: badge solo en desktop de planning; coordinación/leadership/admin no lo ven; navbar móvil sin badge. | Medio — los aprobadores fuera de planning no reciben señal visual | **Opciones 3 y 4** |
| **H5** | **Código muerto/duplicado**: `lesson-published`/`lesson-approved` nunca emitidos; `$listeners` duplicado con `getListeners()`. | Bajo — deuda técnica, confusión en mantenimiento | **Opción 5** (limpieza previa) |

---

## 1. Objetivos (hallazgos + opciones 1–11)

| # | Opción | Categoría | Atiende |
|---|---|---|---|
| 0 | **Higiene**: eliminar dead code (`lesson-published/approved`) y duplicación `$listeners`/`getListeners()` | Calidad | H5 |
| 1 | **Toast + sonido** en el navegador del responsable | UX | — |
| 2 | **Contador por rol y por scope** (planner/leadership/coordinación ven solo lo suyo + scope de coordinación) | Corrección funcional | H3 |
| 3 | **Código cadencias + cobertura**: poll 5s → 2s configurable + badge en navbars móvil/coordinación/director/admin | Rendimiento / UX | H4 |
| 4 | **Badge/clic → monitor filtrado** (el payload ya trae la URL) | UX | — |
| 5 | **Marcar como leída** con persistencia (`user_lesson_reads`) | Estado real | — |
| 6 | **Vista en vivo del monitor** (los ítems se actualizan sin recargar) | Producto | — |
| 7 | **Multi-tenant / escala** (N canales, Redis, agrupación por equipo) | Arquitectura | — |
| 8 | **Presencia** (quién está online en el monitor) | Colaboración | — |
| 9 | **Cola diferida + reintentos** (push con backoff / reconexión) | Confiabilidad | H2 |
| 10 | **Auditoría y métricas** de despachos en tiempo real | Observabilidad | — |
| 11 | **Emisión centralizada y segura** en `LmsPublicationService::publish()` | Arquitectura / corrección | H1 |

---

## 2. Arquitectura objetivo

```
                    ┌────────────────────────────────────────────────────────────────────┐
                    │                          Laravel App                              │
┌───────────────┐   │  ┌─────────────────────────────┐   LmsPublicationService           │
│ LessonWizard  │──▶│  │  LmsPublicationService::   │──── publish(): status              │
│  (Profesor)   │   │  │  publish()  [EMISOR ÚNICO]  │      + notify*() / broadcast      │
└───────────────┘   │  └──────────────┬──────────────┘                                   │
┌───────────────┐   │                 │ status = SCHEDULED | PUBLISHED                    │
│ LmsMonitor    │──▶│                 ├──────────────────────────┐                        │
│ (Planner/… )  │   │                 ▼                          ▼                        │
└───────────────┘   │      ┌──────────────────────┐   ┌─────────────────────────┐        │
                    │      │ NotificaciónDB       │   │ LessonScheduled         │        │
                    │      │ LessonScheduledFor   │   │ (ShouldBroadcastNow ⤺   │        │
                    │      │ Approval             │   │  guard de conexión)     │        │
                    │      └──────────────────────┘   └───────────┬─────────────┘        │
                    │                                            │                       │
                    │                       ┌────────────────────▼─────────────┐          │
                    │                       │ Reverb (WSS) :8090 (+ Redis)     │          │
                    │                       └──────────────────┬──────────────┘          │
                    │                 canales ┌─────────────────┴────────────┐            │
                    │                         ▼                              ▼            │
                    │          ┌──────────────────────────┐      ┌────────────────────┐  │
                    │          │ private.App.Models.      │      │ presence.lms.monitor│  │
                    │          │ User.{id} (toast+badge)  │      │ (quién está online) │  │
                    │          └──────────────────────────┘      └────────────────────┘  │
                    │                         │                                        │
                    │                         ▼ Livewire.dispatch('lesson-scheduled')    │
                    │          ┌──────────────────────────┐                             │
                    │          │ LessonPendingCount       │ (badge + toast + read)      │
                    │          │  + navbars móvil/coord/director/admin                  │
                    │          └──────────────────────────┘                             │
                    └────────── CONFIG: BROADCAST_DRIVER=reverb · REVERB_* · REDIS_CLIENT=predis
```

---

## 2.5 Estado de implementación (ejecución real)

> Estado **vivo**: esta tabla se actualiza a medida que se implementan las opciones.
> Referencia de marcado: ✅ implementado · 🟡 parcial · ⬜ pendiente · 🔧 corregido.

| Opción | Estado | Qué se implementó | Archivos |
|---|---|---|---|
| **11 — Emisión centralizada** | ✅ | `LmsPublicationService::publish()` notifica (DB + broadcast `LessonScheduled` + log `SCHEDULE`) cuando `!$authorized` (quedó `SCHEDULED`). El wizard delega en el servicio. `LessonWizard::notifyPlanningScheduled()` (dead code) **eliminado** junto con sus imports huérfanos (`LessonScheduled`, `LessonScheduledForApproval`, `User`, `Notification`). | `app/Services/Lms/LmsPublicationService.php`, `app/Livewire/Profesor/Lms/LessonWizard.php` |
| **0 — Higiene (dead code)** | ✅ | `LessonPendingCount` sin `lesson-published`/`lesson-approved` (nunca se emiten) y sin propiedad `$listeners` duplicada (solo `getListeners()`). | `app/Livewire/Planning/Lms/LessonPendingCount.php` |
| **🔧 Fix crash Echo** | ✅ | La clave del listener pasó a `echo-private:App.Models.User.{id},.lesson.scheduled` (sin evento, Livewire llama `listen(undefined)` → TypeError `charAt` y rompía el navbar). | `LessonPendingCount.php` |
| **🔧 Fix listener `$refreshCount`** | ✅ | `'lesson-scheduled' => '$refreshCount'` era **no-op silencioso** (`wrap()`→null por método inexistente). Ahora mapea a `refreshCount`. | `LessonPendingCount.php` |
| **4 — Badge clicable** | ✅ | Badge = `<a>` → `route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED'])`. | `resources/views/livewire/planning/lms/lesson-pending-count.blade.php` |
| **3 — Cobertura navbars + poll** | ✅ | Badge en las 8 navbars: `planning-items`, `planning-items-mobile`, `coordinacion-items`, `coordinacion-items-mobile`, `director-items`, `director-items-mobile`, `admin-items`, `admin-items-mobile`. Poll configurable vía `REVERB_POLL_INTERVAL` (default 5000ms, `config/broadcasting.php::poll_interval`). | `resources/views/components/navbars/*`, `config/broadcasting.php`, `lesson-pending-count.blade.php`, `monitor-stats.blade.php` |
| **1 — Toast en vivo** | ✅ | El badge (`LessonPendingCount`) muestra el toast WireUI al recibir el broadcast (`wireui:notification`) con link al monitor. **Dedup por usuario+lección** (session): si hay varios badges en la página, solo se muestra 1 toast. El evento intermedio `lesson-scheduled-toast` se eliminó (dead code). | `app/Livewire/Planning/Lms/LessonPendingCount.php` |
| **6 — Vista en vivo del monitor** | ✅ | **Listado + stats en vivo**: `LmsMonitor` ahora escucha `lesson-scheduled` y `echo-private:...lesson.scheduled` → `refreshFromRealtimeEvent()` (re-renderiza el listado). El widget `MonitorStats` sigue con su listener + poll. `LmsMonitor` ya no calcula stats (`getStats()` eliminado); la vista embebe `<livewire:planning.lms.monitor-stats />`. | `app/Livewire/Planning/Lms/LmsMonitor.php`, `MonitorStats.php`, `monitor.blade.php`, `monitor-stats.blade.php` |
| **🔧 Fix botones flotantes wizard** | ✅ | Condición de visibilidad pasó de `request()->query('activity_id')` a `$mode === 'wizard' && $selectedActivityId` (visible en todos los steps; antes fallaba al entrar al wizard desde la lista vía Livewire sin query param). | `resources/views/livewire/profesor/lms/lesson-wizard.blade.php` |
| **2 — Contador por rol/scope** | ✅ | **Badge** (`LessonPendingCount::refreshCount`): SCHEDULED no-leídas + scope por rol — Admin/Planner/Director globales; Coordinación solo su scope de peducativos (`CoordinacionScopeService`); Leadership solo sus áreas (`LeadershipService`); usuarios sin rol responsable → 0. **Emisor (H3)**: `LmsPublicationService::getRecipients(Activity)` scopes destinatarios — admin/planner/director siempre; leadership solo si la asignatura de la lección está en sus áreas; coordinación solo si el pestudio está en su scope (`pevaluacionIsInScope`). Se corrigió además un bug latente: el log `SCHEDULE` se guardaba vacío porque el ENUM `lms_activity_logs.event` no lo incluía (migración `add_schedule_event`). Tests: `LessonPendingScopeTest` (5), tests de scope en `LmsPublicationServiceTest`, `LessonWizardCharacterizationTest` actualizado (leader+coordinator con scope). | `LessonPendingCount.php`, `LmsPublicationService.php`, `database/migrations/2026_08_13_000002_add_schedule_event_to_lms_activity_logs.php`, `tests/Feature/Planning/Lms/LessonPendingScopeTest.php`, `LmsPublicationServiceTest.php`, `LessonWizardCharacterizationTest.php` |
| **5 — Marcar como leída** | ✅ | Migración `user_lesson_reads` (unique `user_id`+`activity_id`, FKs a users/activities, `read_at`), modelo `UserLessonRead` (+relaciones `User::lessonReads()`, `Activity::lessonReads()`). `LessonPendingCount::refreshCount()` cuenta solo **SCHEDULED no leídas** (`whereDoesntHave('lessonReads')`); nueva acción `markAsRead(array $activityIds)` batch + idempotente (insert `diff` contra existentes). `LmsMonitor::mount()` llama `markScheduledAsRead()` → al abrir el monitor las `SCHEDULED` actuales quedan leídas para ese usuario (las nuevas en vivo siguen contando). Tests `LessonReadTest` (6): migración+modelo, refreshCount solo no-leídas, batch idempotente, privacidad por usuario, marcar al abrir monitor, reapertura idempotente. **Nota**: `users.id` es `int(10) unsigned` → la FK usa `unsignedInteger` (no `foreignId`, que sería bigint y fallaría). | `database/migrations/2026_08_13_000001_create_user_lesson_reads_table.php`, `app/Models/UserLessonRead.php`, `app/Models/User.php`, `app/Models/app/Academy/Activity.php`, `LessonPendingCount.php`, `LmsMonitor.php`, `tests/Feature/Planning/Lms/LessonReadTest.php` |
| **7 — Multi-tenant/Redis** | ⬜ | Pendiente. | — |
| **8 — Presencia** | ⬜ | Pendiente. | — |
| **9 — Cola + crash-guard** | ✅ | **Crash-guard completo (estrategia híbrida)**: push inmediato `ShouldBroadcastNow` con try/catch (no rompe `saveStep2`; notificación DB persistida + poll 5s cubren) + **job de respaldo** `BroadcastLessonScheduled` (`tries=3`, `backoff=[10,60,300]`) que se encola solo si el push falla (Reverb caído) y re-emite cuando Reverb vuelve. Supervisor: `cfla-queue` (`queue:work database --tries=3 --backoff=10`). Tests: `BroadcastLessonScheduledJobTest` (2) + `reverb_caido_no_rompe_request_y_encola_job_respaldo` (dispatcher que lanza). | `app/Jobs/BroadcastLessonScheduled.php`, `LmsPublicationService.php`, `supervisor-reverb.conf`, `tests/Feature/Planning/Lms/BroadcastLessonScheduledJobTest.php`, `LmsPublicationServiceTest.php` |
| **10 — Auditoría/métricas** | ✅ | **Auditoría en el punto central**: migración `broadcast_events` (morphs subject, actor, recipient_ids JSON, channel_count, driver, delivered) + modelo `BroadcastEvent` + service `BroadcastAudit::log()` (fila + línea JSON a `storage/logs/broadcast.log` vía canal Monolog `broadcast`) invocado en `notifyScheduled`. **ACK**: payload de `LessonScheduled` incluye `event_id`; `bootstrap.js` hace `POST /api/broadcast/ack` (auth:sanctum + throttle 30/min, idempotente). **Métricas**: comando `broadcast:stats` (eventos/hora, ratio delivered/total, por evento, destinatarios promedio). Tests: `BroadcastAuditTest` (7). | `database/migrations/2026_08_13_000003_create_broadcast_events_table.php`, `app/Models/app/Academy/Lms/BroadcastEvent.php`, `app/Services/Lms/BroadcastAudit.php`, `app/Console/Commands/DispatchStatsCommand.php`, `routes/api.php`, `resources/js/bootstrap.js`, `LessonScheduled.php` (+event_id), `config/logging.php` (+canal broadcast), `LmsPublicationService.php` |

**Tests añadidos** (verificación de la implementación):
- `tests/Feature/Planning/Lms/MonitorStatsTest.php` — render + counts, formato del listener Echo, `refreshStats` recalcula tras nueva lección `SCHEDULED`.
- `tests/Feature/Livewire/Planning/Lms/LessonPendingCountTest.php` — render, listener Echo con evento, dedup del toast (1 toast por lección aunque haya N badges).
- `tests/Feature/Planning/Lms/LmsMonitorRealtimeTest.php` — listener de refresh en vivo + re-render del listado con la lección nueva.
- `tests/Feature/Planning/Lms/LmsPublicationServiceTest.php` — Opción 11 (no-duplicación): 1 broadcast + 1 notificación DB por `SCHEDULED`; publicación autorizada no emite; reprogramar no acumula.

**Changelog de ejecución**

| Fecha | Cambio |
|---|---|
| 2026-08-12 | Auditoría del baseline (v2) + definición de Opciones 0–11. |
| 2026-08-13 | Fix crash `charAt` (listener Echo sin evento) + fix `$refreshCount` no-op. Wizard: botones flotantes en todos los steps. Opción 6 parcial: widget `MonitorStats` en vivo + fallback poll; `LmsMonitor` sin `getStats()`. Tests `MonitorStatsTest` y `LessonPendingCountTest`. |
| 2026-08-13 | Opción 11 completa: eliminado `LessonWizard::notifyPlanningScheduled()` (dead code) + imports huérfanos. Test `LessonWizardCharacterizationTest` verifica que la notificación sigue viniendo del servicio (69 passed). |
| 2026-08-13 | Opción 1 (toast WireUI + dedup), Opción 3 completa (8 navbars + poll configurable `REVERB_POLL_INTERVAL`), Opción 6 completa (listado del monitor en vivo). Tests: `LmsPublicationServiceTest`, `LmsMonitorRealtimeTest`, dedup del toast. |
| 2026-08-13 | Opción 5 (marcar como leída): migración `user_lesson_reads` + modelo + relaciones, `refreshCount` = no-leídas, `markAsRead` batch/idempotente, monitor marca al abrir. Tests `LessonReadTest` (6). |
| 2026-08-13 | Opción 2 (contador por rol/scope): badge scoped (Admin/Planner/Director globales; Coordinación por peducativos; Leadership por áreas) + emisor H3 scopes destinatarios por rol. Fix bug latente: ENUM `lms_activity_logs.event` sin `SCHEDULE` (log vacío) + log duplicado en `notifyScheduled`. Tests `LessonPendingScopeTest` (5) + scope en `LmsPublicationServiceTest` + wizard actualizado. |
| 2026-08-13 | Opción 9 (cola + crash-guard completo): job `BroadcastLessonScheduled` (`tries=3`, `backoff=[10,60,300]`) encolado cuando el push inmediato falla (Reverb caído); re-emite al volver. Supervisor `cfla-queue`. Tests `BroadcastLessonScheduledJobTest` (2) + `reverb_caido_no_rompe_request_y_encola_job_respaldo`. |
| 2026-08-13 | Opción 10 (auditoría + métricas): tabla `broadcast_events` + `BroadcastEvent` + `BroadcastAudit::log()` en el emisor central; ACK idempotente `POST /api/broadcast/ack` (payload `event_id` + `bootstrap.js`); comando `broadcast:stats`; canal log `broadcast` JSON. Tests `BroadcastAuditTest` (7). |

---

## 3. Desglose por opción

### Opción 0 — Higiene (dead code + duplicación)
**Archivos**: `LessonPendingCount.php`, `bootstrap.js`.

- Eliminar del `$listeners`/`getListeners()` los eventos que nunca se emiten (`lesson-published`, `lesson-approved`). Si en el futuro se emiten, se añaden entonces (YAGNI).
- Eliminar la propiedad `$listeners` redundante y dejar **solo** `getListeners()` (o al revés — no ambos).
- Verificar en `bootstrap.js` que solo se despache `lesson-scheduled` (actual: correcto).

### Opción 1 — Toast + sonido ✅ implementado (sin sonido)
**Archivos**: `bootstrap.js`, `LessonPendingCount.php`, `.blade.php`, nuevos assets.

> **Estado**: toast WireUI implementado y deduplicado (ver §2.5 y §4.3 del doc de implementación). **Sonido pendiente.**

- Al recibir `.lesson.scheduled`, además de refrescar el contador, disparar un toast accesible:
  ```js
  Echo.private(`App.Models.User.${userId}`).listen('.lesson.scheduled', (e) => {
      Livewire.dispatch('lesson-scheduled', { message: e.message, url: e.url });
      // beep opcional (AudioContext, sin volumen antes de interacción para cumplir autoplay policies)
  });
  ```
- En `LessonPendingCount`:
  ```php
  public function refreshCountFromEcho(array $payload = []): void
  {
      $this->refreshCount();
      if (! empty($payload)) {
          $this->showScheduledToast($payload);   // wireui:notification + dedup por usuario+lección
      }
  }
  ```
- Toast: usar **WireUI** (`wireui:notification`) que ya está en el proyecto — cero dependencias nuevas. ✅ hecho.
- Sonido: asset `.mp3` corto + `Audio` API; respetar preferencia (`localStorage.muted`) y un botón para silenciar (persistido). ⬜ pendiente.

### Opción 2 — Contador por rol y por scope
**Archivos**: `LessonPendingCount.php`, `.blade.php`, query del emisor (Opción 11).

- Hoy `refreshCount()` cuenta **todas** las `SCHEDULED` sin distinguir rol ni scope. Corrección (a dos niveles):
  ```php
  public function refreshCount(): void
  {
      $user = auth()->user();

      $this->count = Activity::query()
          ->whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))
          ->where(function ($q) use ($user) {
              // A. Rol de aprobación: quién aprueba qué (decisión Fase 0)
              if ($user->is_planner) { $q->where(...); }        // planning aprueba
              elseif ($user->is_leadership) { $q->where(...); } // leadership aprueba
              elseif ($user->is_coordinacion) { $q->where(...); } // coordinación revisa
          })
          // B. Scope de coordinación: reusar CoordinacionScopeService
          ->when($user->is_coordinacion && ! $user->is_admin,
              fn ($q) => app(CoordinacionScopeService::class)->scopeActivities($q))
          ->count();
  }
  ```
- El **payload del evento** ya incluye `recipients` → propagar `roles` para que el cliente filtre el toast.
- **H3**: la query del emisor (Opción 11) también debe scoper los destinatarios por grado/coordinación, no mandar la notificación a todos los admin/planner/leadership/coordinación sin importar el contenido.
- Decisión de negocio (Fase 0): ¿un ítem `SCHEDULED` es "pendiente" solo para el rol que lo aprueba? Se define con Producto.

### Opción 3 — Cadencia configurable + cobertura de navbars ✅ implementado
**Archivos**: `.blade.php` (badge), `config/broadcasting.php`, `planning-items-mobile`, `coordinacion-items`, `director-items`, `admin-items`.

> **Estado**: `REVERB_POLL_INTERVAL` en `config/broadcasting.php::poll_interval` (default 5000ms) + badge en las **8 navbars** (planning/coordinación/director/admin + móvil). Ver §2.5.

- Parámetro por entorno: `.env` → `REVERB_POLL_INTERVAL=2000` (default 5000ms).
  ```blade
  <div wire:poll.{{ config('broadcasting.poll_interval', 5000) }}ms>...</div>
  ```
- **Estrategia**: Reverb es el medio primario; el `poll` es **fallback de resiliencia**. Reducir a 2s solo si `BROADCAST_DRIVER=reverb` está activo; en local sin Reverb, mantener 5s.
- **H4 — cobertura** ✅: `<livewire:planning.lms.lesson-pending-count />` replicado en las 8 navbars.
- Como `data-reverb`/`data-user-id` ya viven en `layouts/dashboard.blade.php`, todos los navbars que renderiza ese layout heredan el atributo sin cambios adicionales.
- **Migración futura a SSE/`wire:connect`** (Laravel 11+): mantener el componente compatible para que el cambio sea de 1 línea en la vista.

### Opción 4 — Badge clicable → monitor filtrado
**Archivos**: `.blade.php` del badge (desktop y móvil).

- El payload ya trae `url` (route `app.planning.lms.monitor` con `filterStatus=SCHEDULED`). El badge se vuelve un `<a>`:
  ```blade
  <a href="{{ $count > 0 ? route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']) : '#' }}"
     class="relative inline-flex ..." {{ $count === 0 ? 'aria-disabled="true" tabindex="-1"' : '' }}>
      <x-icon name="book-open" class="..." />
      <span class="badge badge-amber">{{ $count }}</span>
  </a>
  ```
- Si `count === 0`, mostrar el mismo badge pero deshabilitado (`aria-disabled`), evitando navegación inútil.
- También el **toast** lleva el enlace "Ver" → monitor.

### Opción 5 — Marcar como leída (persistencia)
**Archivos nuevos**: migración, `app/Models/UserLessonRead.php`, servicio; **modificados**: `LessonPendingCount`, `LmsMonitor`, js.

- Migración:
  ```php
  Schema::create('user_lesson_reads', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
      $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
      $table->timestamp('read_at')->useCurrent();
      $table->unique(['user_id', 'activity_id']);
  });
  ```
- Modelo `UserLessonRead` + relación en `User`: `lessonReads()`.
- `refreshCount()` pasa a ser **"no leídas"** (combinado con el scope de la Opción 2):
  ```php
  public function refreshCount(): void
  {
      $this->count = Activity::query()
          ->whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))
          ->whereDoesntHave('lessonReads', fn ($q) => $q->where('user_id', auth()->id()))
          ->when($scope, fn ($q) => $scope($q))   // scope de rol/coordinación (Opción 2)
          ->count();
  }
  ```
- Acción **marcar leída**: cuando el responsable abre el monitor o hace clic en el toast/badge → `markAsRead($activityIds)` (UPDATE batch por `user_id`).
- `LessonScheduled` → `broadcastWith()` NO debe incluir datos sensibles; el marcado de leído es client-side vía Livewire.
- **H5**: de paso, limpiar los listeners muertos aquí.

### Opción 6 — Vista en vivo del monitor (items se actualizan sin recargar) ✅ implementado
**Archivos**: `LmsMonitor.php`, su `.blade.php`, `bootstrap.js`.

> **Estado**: implementado — listado y stats se refrescan en vivo (ver §2.5 y §4.5 del doc de implementación). La paginación/filtros se conservan (el listener re-renderiza sin resetear).

- Añadir listener de broadcast en el **monitor** mismo (no solo en el badge) ✅:
  ```php
  protected function getListeners(): array
  {
      return [
          'lesson-scheduled' => 'refreshFromRealtimeEvent',
          'echo-private:App.Models.User.'.auth()->id().',.lesson.scheduled' => 'refreshFromRealtimeEvent',
      ];
  }

  public function refreshFromRealtimeEvent(): void
  {
      // Sin-op: Livewire re-renderiza el listado con datos frescos, preservando filtros.
  }
  ```
- Refrescar solo el cuerpo de la tabla (no el layout completo) para preservar scroll/filtros. ✅ (el re-render de Livewire conserva el estado del componente).
- Usar `Livewire.dispatch('lms-monitor-refresh')` en el listener JS global para sincronizar ambos componentes. *(Simplificado: `LmsMonitor` escucha el broadcast directamente; el widget `MonitorStats` lo escucha por su cuenta.)*

### Opción 7 — Multi-tenant/escala (N canales, Redis)
**Archivos**: `LessonScheduled.php` (broadcastOn), `config/broadcasting.php`, `config/database.php` (redis), `composer.json`.

- Hoy: 1 canal privado por user. Mejora: **agrupar por tenant/equipo**:
  ```php
  public function broadcastOn(): array
  {
      return collect($this->recipients)
          ->map(fn (User $u) => new PrivateChannel('App.Models.User.'.$u->id))
          ->concat([new PrivateChannel('team.'.($this->teamId ?? 'default'))])
          ->values()->all();
  }
  ```
- **Redis**: Reverb escala horizontalmente con Redis (adapter). Configurar `REVERB_REDIS_CONNECTION`, `REDIS_CLIENT=predis` (PHP puro, sin extensión) o `phpredis`.
- Verificar `composer require predis/predis` (si no está) y conexión `redis` en `config/database.php`.
- Envolver en feature flag `REVERB_TEAM_CHANNELS` (default off) — ver Riesgos.

### Opción 8 — Presencia (quién está online en el monitor)
**Archivos**: `app/Events/Lms/MonitorPresence.php`, `routes/channels.php`, monitor JS/`.blade.php`.

- Usar `PresenceChannel` para el monitor de planificación:
  ```php
  // routes/channels.php
  Broadcast::channel('presence.lms.monitor', function ($user) {
      return ['name' => $user->fullName, 'avatar' => $user->avatar_url] ?? null;
  });
  Broadcast::channel('presence.lms.monitor.{team}', ...); // si multi-equipo (Opción 7)
  ```
- En el monitor: `Echo.join(...).here(users => ...).joining(user => ...).leaving(user => ...)` → renderiza avatares online en la cabecera.
- Autolimpieza Reverb por timeout (30s por defecto) al cerrar la pestaña.

### Opción 9 — Cola diferida + reintentos y **crash-guard**
**Archivos**: `app/Jobs/BroadcastLessonScheduled.php`, `LessonScheduled.php` (cambiar a `ShouldBroadcast` + `$queue`), `config/queue.php`, emisor (Opción 11).

- **H2 (crash-risk)**: `ShouldBroadcastNow` lanza excepción síncrona si Reverb está caído y puede romper `saveStep2`. Estrategia híbrida:
  - **Primario inmediato con guard**: mantener `ShouldBroadcastNow` pero envolver el dispatch en un try/catch (o un helper `Broadcast::safe()`):
    ```php
    try {
        LessonScheduled::dispatch($activity, $recipients, $name, $date);
    } catch (\Throwable $e) {
        report($e);   // el poll de 5s cubre el badge; la notificación DB ya está persistida
    }
    ```
  - **Alternativa robusta**: migrar a `ShouldBroadcast` (cola) + job de respaldo con reintentos:
    ```php
    class BroadcastLessonScheduled implements ShouldQueue {
        public $tries = 3;
        public $backoff = [10, 60, 300]; // segundos
        public function handle(LessonScheduled $event) { /* re-emit a Reverb */ }
    }
    ```
  - **Persistencia**: la notificación DB es el "deferred" (estado asíncrono garantizado en BD) y `LessonPendingCount::mount()` hace `refreshCount()` al entrar → el pendiente nunca se pierde aunque falle el push.
- Redis como driver de cola: verificar `QUEUE_CONNECTION=redis` + `php artisan queue:work` bajo supervisor.

### Opción 10 — Auditoría + métricas en tiempo real
**Archivos nuevos**: migración `broadcast_events_log`, `app/Console/Commands/DispatchStatsCommand.php`; **modificados**: `LessonScheduled.php` (hook de logging), `routes/web.php` (dashboard admin), `config/broadcasting.php`.

- Tabla de auditoría:
  ```php
  Schema::create('broadcast_events', function (Blueprint $table) {
      $table->id();
      $table->string('event')->index();            // lesson.scheduled
      $table->nullableMorphs('subject');           // Activity
      $table->foreignId('actor_user_id')->nullable(); // profesor que disparó
      $table->json('recipient_ids');               // [11, 2597]
      $table->integer('channel_count');
      $table->string('driver');                    // reverb|database
      $table->boolean('delivered')->default(false); // actualizado por el cliente (ACK)
      $table->timestamps();
  });
  ```
- Registrar 1 fila por evento en el punto central de emisión (Opción 11), vía service `BroadcastAudit::log(...)`.
- Métricas: `php artisan broadcast:stats` (o endpoint admin) → eventos/hora, destinos, ratio `delivered/total`.
- **ACK**: el firmware de `bootstrap.js` envía `POST /api/broadcast/ack {event_id}` — endpoint idempotente y rate-limited; `delivered` eventual.
- Log estructurado a `storage/logs/broadcast.log` (JSON lines) con channel Monolog dedicado.

### Opción 11 — **Emisión centralizada y segura** (corrige H1)
**Archivos**: `LmsPublicationService.php`, `LessonWizard.php`, `LmsMonitor.php`, `LmsActivityPublication.php` (observers si aplica).

- **Problema**: antes solo `LessonWizard::notifyPlanningScheduled()` emitía. `LmsMonitor::publish()` y `saveSchedule()` cambian a `SCHEDULED/PUBLISHED` y no notifican → badges de otros responsables desactualizados hasta el poll.
- **Solución (✅ implementada)**: la emisión de broadcast + notificación DB ahora vive **dentro** de `LmsPublicationService::publish()`, de modo que todos los callers la heredan:
  ```php
  public function publish(Activity $activity, array $data, int $publisherId, bool $authorized = false): LmsActivityPublication
  {
      // ... lógica actual de status/publication ...
      if (! $authorized) {
          // todos los flujos que dejan SCHEDULED notifican igual
          $this->notifyScheduled($activity, $publisherId, $data['publish_at'] ?? null);
      } else {
          $this->notifyPublished($activity, $publisherId);   // opcional, emite lesson.published si se desea
      }
      return $pub;
  }
  ```
- ~~Refactor de `LessonWizard::notifyPlanningScheduled()`: eliminar la emisión duplicada y delegar en el servicio~~ → **hecho**: el método (dead code) fue eliminado junto con sus imports huérfanos. `LmsActivityLog::record('SCHEDULE')` queda a cargo del servicio.
- **H3 (destinatarios)**: extraer la query de responsables a un método del servicio que aplique scope de coordinación/grado en vez del `where(...) OR ...` global actual. *(Pendiente — Opción 2)*
- Resultado: un solo punto de emisión = una sola política de auditoría (Opción 10), de recipients (Opción 2), y de crash-guard (Opción 9).

---

## 4. Plan de ejecución por fases

### Fase 0 — Decisión de negocio (1h)
- Contador por rol (Opción 2): ¿"pendiente" = solo lo que aprueba el rol que mira? ¿coordinación con scope de grado o global?
- Política de "leída" (Opción 5): ¿abrir el monitor marca todo como leído, o solo clic explícito en badge/toast?
- Alcance de la cobertura UI (Opción 3): ¿badge en coordinación/director/admin o solo planning?

### Fase 1 — Higiene + Quick wins UX (½–1 día) — opciones 0, 1, 3, 4
- Limpieza de dead code y duplicación (Opción 0).
- Toast WireUI + sonido con mute persistente.
- Badge clicable con `$count > 0` guard.
- `wire:poll.{{ config(...) }}` + doc fallback + cobertura de navbars (móvil + coordinación/director/admin).

### Fase 2 — Emisor central + estado de lectura (1–2 días) — opciones 11, 2, 5
- **Opción 11 primero** (centralizar emisión en `LmsPublicationService` — corrige H1 y habilita 2/10).
- Migración `user_lesson_reads` + modelo + relación.
- `refreshCount()` → no-leídas + scope por rol/coordinación.
- Acción `markAsRead()` en monitor + badge.
- Crash-guard del dispatch (parte de Opción 9).

### Fase 3 — Monitor en vivo + presencia (1 día) — opciones 6, 8
- Listeners en `LmsMonitor` (refresh parcial).
- `PresenceChannel` + avatares online.
- Limpieza de sockets.

### Fase 4 — Arquitectura (1–2 días) — opciones 7, 9
- Redis (predis/phpredis) + Reverb multi-node.
- ~~Job `BroadcastLessonScheduled` con backoff + queue worker.~~ ✅ `tries=3`, `backoff=[10,60,300]`, supervisor `cfla-queue`.
- ~~Validar `ShouldBroadcastNow` (guard) ↔ `ShouldBroadcast` (cola).~~ ✅ Híbrido: push inmediato (guard) + job de respaldo; sin migrar a cola primaria.

### Fase 5 — Observabilidad (½ día) — opción 10
- ~~Migración `broadcast_events` + `BroadcastAudit` service (en el emisor central).~~ ✅
- ~~ACK del cliente + stats endpoint.~~ ✅ `POST /api/broadcast/ack` + `broadcast:stats`
- ~~Channel de log dedicado.~~ ✅ canal Monolog `broadcast` → `storage/logs/broadcast.log`

---

## 5. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
|---|---|---|
| **Reverb caído rompe `saveStep2` del profesor (H2)** | Alto | Crash-guard try/catch + notificación DB persistida + poll fallback; opcional migrar a `ShouldBroadcast` (cola) |
| Contador por rol rompe expectativa actual | Alto | Decisión de negocio en Fase 0; feature flag `LMS_ROLE_BADGE` |
| **Centralizar emisión (Opción 11) duplica notificación si no se limpia el wizard** | ~~Medio~~ ✅ resuelto | `notifyPlanningScheduled()` (emisión duplicada) eliminado; verificación por `LessonWizardCharacterizationTest` (69 passed). Pendiente: test explícito de no-duplicación |
| Badge en más navbars → más componentes + `wire:poll` cada Ns | Medio | Poll solo como fallback; un solo componente por página; cadencia configurable |
| `presence` canales expuestos | Medio | Solo `PresenceChannel` con auth por rol; nunca datos sensibles |
| Redis no disponible en hosting | Alto | Fallback a `database`/`sync` queue; Reverb single-node |
| ACK de entrega (Opción 10) añade roundtrip | Bajo | Endpoint idempotente, rate-limited, `delivered` eventual |
| Cambiar `ShouldBroadcastNow` → `ShouldBroadcast` (cola) | Medio | Híbrido: push inmediato (guard) + job de respaldo, nunca perder el evento |
| Multi-tenancy no es requisito real hoy | Medio | Feature flag `REVERB_TEAM_CHANNELS`; default off |

---

## 6. Testing

- **Unit**: query de `refreshCount` por rol y scope (con y sin `user_lesson_reads`), `markAsRead` batch, `BroadcastAudit::log`, query de destinatarios scoped en el servicio (H3).
- **Feature** (Livewire): badge clicable respeta `count===0`; listener echo dispara `refreshCount`; toast al recibir payload; presencia auth.
- **Broadcast**:
  - Extender el characterization test del wizard con `Event::fake()` y `assertDispatched` multi-destinatario (ya existe para 3 responsables).
  - **Nuevo**: test de que `LmsMonitor::publish()` y `saveSchedule()` emiten `LessonScheduled` (regresión de H1 — antes de la Opción 11 falla).
  - **Nuevo**: test de no-duplicación: programar desde el wizard no emite dos veces tras la centralización.
  - **Nuevo**: test del componente `LessonPendingCount` (render + refreshCount + listener Echo).
- **E2E manual**: 2 navegadores (profesor + planner) — programar desde uno, ver toast+badge en el otro sin recargar; programar desde el **monitor** y ver el badge de otro planner actualizarse en vivo (valida Opción 11); abrir monitor → marcar leída → badge a 0; desconectar Reverb → profesor guarda sin crash y `poll` asume en ≤5s.

---

## 7. Entregables (resumen de artefactos)

| Tipo | Artefacto |
|---|---|
| Migración | `user_lesson_reads` |
| Migración | `broadcast_events` |
| Modelo | `UserLessonRead` (+ relación en `User`) |
| Servicio | `BroadcastAudit` |
| Servicio (refactor) | `LmsPublicationService` (emisión centralizada + recipients scoped) |
| Job | `BroadcastLessonScheduled` (+ backoff) |
| Evento | `LessonScheduled` (enriquecido: roles, team, ACK) |
| Evento nuevo | `MonitorPresence` (presence) |
| Livewire | `LessonPendingCount` (limpieza + rol + leídas + toast) |
| Livewire | `LmsMonitor` (refresh en vivo + presencia) |
| JS | `bootstrap.js` (ACK, sonido, presencia `Echo.join`) |
| Vistas | Badge en navbars móvil + coordinación/director/admin (Opción 3) |
| Config | `broadcasting.php`, `database.php` (redis), `.env` |
| Comando | `broadcast:stats` |
| Tests | caracterización + unit + feature (incl. regresión H1) |
| Docs | este blueprint + actualizar `notificacion-tiempo-real-leccion-programada.md` |

---

## 8. Prioridad sugerida (matriz esfuerzo/valor)

| Orden | Opción | Esfuerzo | Valor | Atiende |
|---|---|---|---|---|
| 1 | 11 — Emisor centralizado | M | **A** | H1 (corrige hueco funcional real) |
| 2 | 0 — Higiene (dead code) | XS | M | H5 |
| 3 | 4 — Badge clicable | XS | M | — |
| 4 | 1 — Toast + sonido | S | M | — |
| 5 | 3 — Poll configurable + cobertura navbars | S | M | H4 |
| 6 | 9 — Crash-guard + cola/backoff | M | **A** | H2 |
| 7 | 5 — Marcar leída | M | **A** | — |
| 8 | 2 — Contador por rol/scope | M | **A** | H3 |
| 9 | 6 — Monitor en vivo | M | A | — |
| 10 | 8 — Presencia | S | M | — |
| 11 | 10 — Auditoría + stats | M | M | — |
| 12 | 7 — Redis multi-node | L | M | — |

**Orden sugerido (fases)**: 11→0→4→1→3→9→5→2→6→8→10→7, agrupado en fases 1–5. La **Opción 11 es la primera**: habilita 2/6/10 y corrige el hueco real (monitor no notifica).

---

## 9. Decisiones abiertas (para Producto/Tech Lead)

1. **Contador por rol** (Opción 2): ¿solo el rol que aprueba ve su `SCHEDULED`, o todos los responsables ven todas? ¿coordinación con scope de grado (vía `CoordinacionScopeService`) o global?
2. **Leída** (Opción 5): ¿abrir el monitor marca todo como leído, o solo clic explícito en el badge/toast?
3. **Cobertura UI** (Opción 3): ¿badge en coordinación/director/admin (+móvil) o solo planning?
4. **Multi-tenant** (Opción 7): ¿existen equipos/tenants reales hoy, o es hipotético? (influye en `team.` channels y en el orden de la fase 4)
5. **ACK de entrega** (Opción 10): ¿se requiere exactitud de "entregado" o basta con logs server-side?
6. **Redis**: ¿está disponible en el hosting de producción o hay que aprovisionar?
7. **Push primario**: ¿mantener `ShouldBroadcastNow` con crash-guard, o migrar a `ShouldBroadcast` en cola como primario?

---

> **Siguiente paso**: continuar la ejecución según §2.5. Ítems pendientes de mayor valor: (a) **Opción 2** (contador por rol/scope de coordinación) — decisión de negocio pendiente; (b) **Opción 9** completar la segunda mitad (migrar `ShouldBroadcastNow` → `ShouldBroadcast` con cola + reintentos/backoff); (c) **Opción 7/8** (escala Redis, presencia); (d) **Opción 10** (auditoría/métricas). Nota: el badge se renderiza una vez por navbar con rol activo (un usuario multi-rol ve N badges); los counts son idénticos (contador global) y el toast está deduplicado por sesión.
