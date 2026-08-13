# Plan Staff Engineer — Mejora del Sistema de Notificación en Tiempo Real (Laravel Reverb)

> **Proyecto**: CFLA · **Módulo**: LMS (Lección programada → aprobación de Planificación)
> **Estado**: Propuesto · **Fecha**: 2026-08-12
> **Alcance**: Opciones 1–10 (pack completo de mejora sobre la base ya implementada en `ea498ac5` y `9ff71beb`)

---

## 0. Estado actual (baseline)

| Componente | Descripción |
|---|---|
| `app/Events/Lms/LessonScheduled.php` | Evento `ShouldBroadcastNow`; `broadcastOn()` → 1 canal privado por destinatario (`App.Models.User.{id}`); `broadcastAs() = lesson.scheduled`; payload con `type/activity_id/teacher_name/lesson_title/scheduled_at/message/url` |
| `app/Livewire/Planning/Lms/LessonPendingCount.php` | Contador SCHEDULED; `getListeners()` con `echo-private:App.Models.User.{auth()->id()}` → `refreshCountFromEcho()`; `$listeners` con eventos Livewire |
| `resources/views/livewire/planning/lms/lesson-pending-count.blade.php` | Badge ámbar + `wire:poll.5s` (fallback) |
| `resources/js/bootstrap.js` | `Echo.private(...).listen('.lesson.scheduled', ...)` → `Livewire.dispatch('lesson-scheduled')` |
| Layouts | `data-reverb="enabled"` + `data-user-id` en dashboard, planning, coordinacion, director, profesors |
| Reverb | Corriendo en `:8090`; auth en `routes/channels.php` (`App.Models.User.{id}` → `$user->id === $id`) |
| Tests | 466 passed; assert del dispatch de `LessonScheduled` en characterization test |

**Flujo actual**: Profesor confirma con fecha → `LmsPublicationService::publish()` (SCHEDULED) → `notifyPlanningScheduled()` → (a) notificación DB `LessonScheduledForApproval` a responsables, (b) `LessonScheduled::dispatch()` → Reverb → canal privado del responsable → `bootstrap.js` → `Livewire.dispatch` → `LessonPendingCount::refreshCount`.

---

## 1. Objetivos (opciones 1–10)

| # | Opción | Categoría |
|---|---|---|
| 1 | **Toast + sonido** en el navegador del responsable | UX |
| 2 | **Contador por rol** (planner/leadership/coordinación ven solo lo suyo) | Corrección funcional |
| 3 | **Código cadencias**: poll 5s → 2s configurable + SSE/`connect` | Rendimiento |
| 4 | **Badge/clic → monitor filtrado** (el payload ya trae la URL) | UX |
| 5 | **Marcar como leída** con persistencia (`user_lesson_reads`) | Estado real |
| 6 | **Vista en vivo** del monitor (los ítems se actualizan sin recargar) | Producto |
| 7 | **Multi-tenant / escala** (N canales, Redis, agrupación por equipo) | Arquitectura |
| 8 | **Presencia** (quién está online en el monitor) | Colaboración |
| 9 | **Cola diferida + reintentos** (push con backoff / reconexión) | Confiabilidad |
| 10 | **Auditoría y métricas** de despachos en tiempo real | Observabilidad |

---

## 2. Arquitectura objetivo

```
                    ┌─────────────────────────────────────────────────────────┐
                    │                      Laravel App                        │
┌───────────────┐   │  ┌────────────┐   dispatch   ┌──────────────────────┐   │
│  LessonWizard │──▶│  │LessonWizard│ ───────────▶ │  LessonScheduled     │   │
│  (Profesor)   │   │  │(notify...) │               │  (ShouldBroadcastNow)│   │
└───────────────┘   │  └────────────┘               └──────────┬───────────┘   │
                    │                                         │                │
                    │                      ┌──────────────────┴──────────┐     │
                    │                      ▼                            ▼     │
                    │            ┌──────────────────┐        ┌──────────────────┐
                    │            │   NotificaciónDB │        │  Reverb  (WSS)   │
                    │            │  LessonScheduled │        │  :8090           │
                    │            │  ForApproval     │        │  + Redis cluster │
                    │            └──────────────────┘        └────────┬─────────┘
                    │                                                 │ canales
                    │                    ┌────────────────────────────┴──────────┐
                    │                    ▼                                       ▼
                    │        ┌──────────────────────┐              ┌──────────────────────┐
                    │        │ private.User.{id}     │              │  presence.monitor     │
                    │        │ (toast + badge)       │              │  (quién está online)  │
                    │        └──────────────────────┘              └──────────────────────┘
                    │                    │
                    │                    ▼
                    │        ┌──────────────────────┐
                    │        │ bootstrap.js          │  Livewire.dispatch
                    │        │ Echo.private.listen   │ ──────────────▶  LessonPendingCount
                    │        └──────────────────────┘                  (badge + toast + read)
                    └────────── CONFIGURACIÓN: BROADCAST_DRIVER=reverb
                                REVERB_* (app key), REDIS_CLIENT=predis, CACHE_DRIVER=redis
```

---

## 3. Desglose por opción

### Opción 1 — Toast + sonido
**Archivos**: `bootstrap.js`, `LessonPendingCount.php`, `.blade.php`, nuevos assets.

- Al recibir `.lesson.scheduled`, además de refrescar el contador, disparar un toast accesible:
  ```js
  Echo.private(`App.Models.User.${userId}`).listen('.lesson.scheduled', (e) => {
      Livewire.dispatch('lesson-scheduled', { message: e.message, url: e.url });
      // beep opcional (AudioContext, sin volumen antes de interacción para cumplir autoplay policies)
  });
  ```
- En `LessonPendingCount`:
  ```php
  public function onLessonScheduled(array $payload): void
  {
      $this->refreshCount();
      if (! session()->has('muted')) {
          $this->dispatch('toast', message: $payload['message'] ?? 'Nueva lección programada', url: $payload['url'] ?? '');
      }
  }
  ```
- Toast: usar **WireUI** (`wireui:notification`) que ya está en el proyecto (se ve `wireui/assets/scripts` en el layout) — cero dependencias nuevas.
- Sonido: asset `.mp3` corto + `Audio` API; respetar preferencia (`localStorage.muted`), y un botón para silenciar (persistido).

### Opción 2 — Contador por rol
**Archivos**: `LessonPendingCount.php`, `.blade.php`, query de responsables.

- Hoy `refreshCount()` cuenta **todas** las SCHEDULED sin distinguir rol del que mira. Corrección:
  ```php
  public function refreshCount(): void
  {
      $this->count = Activity::query()
          ->whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))
          ->whereHas('lmsActivityMostRecentPublication', fn ($q) => $q->where('status', 'SCHEDULED'))
          ->where(function ($q) {
              // 1. profesores de la actividad Y (para planner/admin/leadership)
              // 2. filtro por identidad del que mira (is_planner, is_leadership, is_coordinacion)
          })
          ->count();
  }
  ```
- El **payload del evento** ya incluye `recipients` → podemos propagar `roles` para que el cliente filtre.
- Decisión explícita de negocio (documentar): ¿un ítem SCHEDULED es "pendiente" solo para el rol que lo va a aprobar? (planning aprueba, leadership aprueba, coordinación revisa). Se definirá con Producto en la fase 0 del plan.

### Opción 3 — Cadencia configurable (poll 5s → 2s, SSE)
**Archivos**: `.blade.php` (el `wire:poll.5s`), `config/broadcasting.php`.

- Parámetro por entorno: `.env` → `REVERB_POLL_INTERVAL=2` (default 5).
  ```blade
  <div wire:poll.{{ config('broadcasting.reverb_poll_interval', 5) }}s>...</div>
  ```
- **Estrategia**: Reverb es el medio primario; el `poll` es **fallback de resiliencia**. Reducir a 2s solo si `BROADCAST_DRIVER=reverb` está activo; en local sin Reverb, mantener 5s.
- **Migración futura a SSE/`connect`**: Livewire 3 soporta `wire:connect` (Laravel 11+). Mantener el componente compatible para que el cambio sea de 1 línea en la vista.

### Opción 4 — Badge clicable → monitor filtrado
**Archivos**: `.blade.php` del badge.

- El payload ya trae `url` (route `app.planning.lms.monitor` con `filterStatus=SCHEDULED`). El badge se vuelve un `<a>`:
  ```blade
  <a href="{{ $count > 0 ? route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']) : '#' }}"
     class="relative inline-flex ...">
      <x-icon name="book-open" class="..." />
      <span class="badge badge-amber">{{ $count }}</span>
  </a>
  ```
- Si `count === 0`, mostrar el mismo badge pero deshabilitado (`aria-disabled`), evitando navegación inútil.
- También el **toast** lleva el enlace "Ver" → monitor.

### Opción 5 — Marcar como leída (persistencia)
**Archivos nuevos**: migración, `app/Models/UserLessonRead.php`, servicio; **modificados**: `LessonPendingCount`, js.

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
- `refreshCount()` pasa a ser **"no leídas"**:
  ```php
  public function refreshCount(): void
  {
      $this->count = Activity::query()
          ->whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'))
          ->whereDoesntHave('lessonReads', fn ($q) => $q->where('user_id', auth()->id()))
          ->count();
  }
  ```
- Acción **marcar leída**: cuando el responsable abre el monitor o hace clic en el toast/badge → `markAsRead($activityIds)` (UPDATE batch por `user_id`).
- Evento `LessonScheduled` → `broadcastWith()` NO debe incluir datos sensibles; el marcado de leído es client-side vía Livewire.

### Opción 6 — Vista en vivo del monitor (items se actualizan sin recargar)
**Archivos**: `LmsMonitor.php` (o `LearningMonitor`), su `.blade.php`, `bootstrap.js`.

- Añadir listener de broadcast en el **monitor** mismo (no solo en el badge):
  ```php
  protected function getListeners(): array
  {
      return [
          'echo-private:App.Models.User.'.auth()->id() => 'onLiveLessonEvent',
      ];
  }

  public function onLiveLessonEvent(array $payload): void
  {
      // refresca la lista SIN resetar la paginación/filtros activos
      $this->refreshRowsOnly();
  }
  ```
- Refrescar solo el cuerpo de la tabla (no el layout completo) para preservar scroll/filtros.
- Considerar **inertia** no aplica aquí (Livewire 3), pero sí: usar `Livewire.dispatch('lms-monitor-refresh')` en el listener JS global.

### Opción 7 — Multi-tenant/escala (N canales, Redis)
**Archivos**: `LessonScheduled.php` (broadcastOn), `config/broadcasting.php`, `config/database.php` (redis), `composer.json`.

- Hoy: 1 canal privado por user. Para N canales: se emiten todos en el mismo `broadcastOn()` (ya lo hace). Mejora: **agrupar por tenant/equipo**:
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
- Verificar `composer require predis/predis` (si no está) y que el `config/database.php` tenga conexión `redis` con `host/port` correctos.
- Prueba de carga (número de canales/usuarios) — ver opción 10.

### Opción 8 — Presencia (quién está online en el monitor)
**Archivos**: `LessonScheduled.php` o nuevo `app/Events/Lms/MonitorPresence.php`, `routes/channels.php`, monitor JS/`.blade.php`.

- Usar `PresenceChannel` para el monitor de planificación:
  ```php
  // routes/channels.php
  Broadcast::channel('presence.lms.monitor', function ($user) {
      return ['name' => $user->fullName, 'avatar' => $user->avatar_url] ?? null;
  });
  Broadcast::channel('presence.lms.monitor.{team}', ...); // si multi-equipo (opción 7)
  ```
- En el monitor: `Echo.join(...).here(users => ...).joining(user => ...).leaving(user => ...)` → renderiza avatares online en la cabecera.
- Cerrar sesión del socket al cerrar la pestaña (autolimpieza Reverb por timeout 30s por defecto).

### Opción 9 — Cola diferida + reintentos (backoff/reconexión)
**Archivos**: `app/Jobs/BroadcastLessonScheduled.php`, `LessonScheduled.php` (cambiar a `ShouldBroadcast` + `$queue`), `config/queue.php`.

- En vez de `ShouldBroadcastNow` (síncrono), opción **híbrida**:
  - **Push inmediato** (hoy): `ShouldBroadcastNow` → para el toast instantáneo.
  - **Persistencia**: la notificación DB ya es el "deferred" (estado asíncrono garantizado en BD).
  - **Job de reintento opcional**:
    ```php
    class BroadcastLessonScheduled implements ShouldQueue {
        public $tries = 3;
        public $backoff = [10, 60, 300]; // segundos
        public function handle(LessonScheduled $event) { /* re-emit a Reverb */ }
    }
    ```
- Cuando un responsable **se conecta** (entra al dashboard), el componente `LessonPendingCount::mount()` ya hace `refreshCount()` → la cola DB le muestra lo pendiente aunque se perdiera el push.
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
      $table->boolean('delivered')->default(false); // actualizado por el cliente? (ver nota)
      $table->timestamps();
  });
  ```
- En `LessonScheduled::broadcastOn()` o en el dispatch: registrar 1 fila por evento despachado (service `BroadcastAudit::log(...)`).
- Métricas: `php artisan broadcast:stats` (o endpoint admin) → número de eventos/hora, destinos, ratio `delivered/total`.
- **Nota de diseño**: marcar `delivered=true` requiere roundtrip del cliente (ACK: el firmware de `bootstrap.js` envía `POST /api/broadcast/ack {event_id}`) — aceptable para staff tooling.
- Log estructurado a `storage/logs/broadcast.log` (JSON lines) con Monolog channel dedicated.

---

## 4. Plan de ejecución por fases

### Fase 0 — Decisión de negocio (1h)
- Definir con producto: ¿contador por rol (opción 2) = aprobación solo del rol que aprueba, o todos los responsables ven todas las SCHEDULED?
- Definir umbrales de `poll` (opción 3) y política de "leída" (opción 5): ¿leída = abrió el monitor o hizo clic?

### Fase 1 — Quick wins UX (medio día) — opciones 1, 3, 4
- Toast WireUI + sonido con mute persistente.
- Badge clicable con `$count > 0` guard.
- `wire:poll.{{ config(...) }}` + doc fallback.

### Fase 2 — Estado de lectura (1 día) — opciones 2, 5
- Migración `user_lesson_reads` + modelo + relación.
- `refreshCount()` → no-leídas.
- Acción `markAsRead()` en monitor + badge.
- Ajustar query por rol (decisión Fase 0).

### Fase 3 — Monitor en vivo + presencia (1 día) — opciones 6, 8
- Listeners en `LmsMonitor` (refresh parcial).
- `PresenceChannel` + avatares online.
- Limpieza de sockets.

### Fase 4 — Arquitectura (1–2 días) — opciones 7, 9
- Redis (predis/phpredis) + Reverb multi-node.
- Job `BroadcastLessonScheduled` con backoff + queue worker.
- Validar `ShouldBroadcastNow` ↔ híbrido.

### Fase 5 — Observabilidad (medio día) — opción 10
- Migración `broadcast_events` + `BroadcastAudit` service.
- ACK del cliente + stats endpoint.
- Channel de log dedicado.

---

## 5. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
|---|---|---|
| Contador por rol rompe expectativa actual | Alto | Decisión de negocio en Fase 0; feature flag `LMS_ROLE_BADGE` |
| `presence` canales expuestos | Medio | Solo `PresenceChannel` con auth por rol; nunca datos sensibles |
| Redis no disponible en hosting | Alto | Fallback a `database`/`sync` queue; Reverb single-node |
| ACK de entrega (opción 10) añade roundtrip | Bajo | Endpoint idempotente, rate-limited, `delivered` eventual |
| `wire:poll` cada 2s con N usuarios | Medio | Poll solo cuando Reverb no llegó; config por entorno |
| Cambiar `ShouldBroadcastNow` → `ShouldBroadcast` (cola) | Medio | Híbrido: push inmediato + job de respaldo, nunca perder el evento |
| Multi-tenancy no es requisito real hoy | Medio | Envolver en feature flag `REVERB_TEAM_CHANNELS`; default off |

---

## 6. Testing

- **Unit**: query de `refreshCount` por rol (con y sin `user_lesson_reads`), `markAsRead` batch, `BroadcastAudit::log`.
- **Feature** (Livewire): badge clicable respeta `count===0`; listener echo dispara `refreshCount`; toast al recibir payload; presencia auth.
- **Broadcast**: extender characterization test con `Event::fake()` y `assertDispatched` por destinatario múltiple (ya existe para 3 responsables).
- **E2E manual**: 2 navegadores (profesor + planner) — programar desde uno, ver toast+badge en el otro sin recargar; abrir monitor → marcar leída → badge a 0; desconectar Reverb → `poll` asume en ≤ 5s.

---

## 7. Entregables (resumen de artefactos)

| Tipo | Artefacto |
|---|---|
| Migración | `user_lesson_reads` |
| Migración | `broadcast_events` |
| Modelo | `UserLessonRead` (+ relación en `User`) |
| Servicio | `BroadcastAudit` |
| Job | `BroadcastLessonScheduled` (+ backoff) |
| Evento | `LessonScheduled` (enriquecido: roles, team, ACK) |
| Evento nuevo | `MonitorPresence` (presence) |
| Livewire | `LessonPendingCount` (rol + leídas + toast) |
| Livewire | `LmsMonitor` (refresh en vivo + presencia) |
| JS | `bootstrap.js` (ACK, sonido, presencia `Echo.join`) |
| Config | `broadcasting.php`, `database.php` (redis), `.env` |
| Comando | `broadcast:stats` |
| Tests | caracterización + unit + feature |
| Docs | este blueprint + actualizar `notificacion-tiempo-real-leccion-programada.md` |

---

## 8. Prioridad sugerida (matriz esfuerzo/valor)

| # | Opción | Esfuerzo | Valor | Orden |
|---|---|---|---|---|
| 4 | Badge clicable | XS | M | 1 |
| 1 | Toast + sonido | S | M | 2 |
| 3 | Poll configurable | XS | S | 3 |
| 5 | Marcar leída | M | A | 4 |
| 2 | Contador por rol | M | A | 5 |
| 6 | Monitor en vivo | M | A | 6 |
| 8 | Presencia | S | M | 7 |
| 9 | Cola diferida/backoff | M | A | 8 |
| 7 | Redis multi-node | L | M | 9 |
| 10 | Auditoría + stats | M | M | 10 |

**Orden sugerido**: 4→1→3→5→2→6→8→9→7→10, agrupado en fases 1–5.

---

## 9. Decisiones abiertas (para Producto/Tech Lead)

1. **Contador por rol** (opción 2): ¿solo el rol que aprueba ve su SCHEDULED, o todos los responsables ven todas?
2. **Leída** (opción 5): ¿abrir el monitor marca todo como leído, o solo clic explícito en el badge/toast?
3. **Multi-tenant** (opción 7): ¿existen equipos/tenants reales hoy, o es hipotético? (influencia en `team.` channels)
4. **ACK de entrega** (opción 10): ¿se requiere exactitud de "entregado" o basta con logs server-side?
5. **Redis**: ¿está disponible en el hosting de producción o hay que aprovisionar?

---

> **Siguiente paso**: aprobar Fase 0 (decisiones de negocio) y arrancar Fase 1. Yo puedo implementar cualquiera de las fases completas (código + migraciones + tests + docs) cuando digas cuál priorizas.