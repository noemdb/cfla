# Plan Integral — Notificación en Tiempo Real de "Lección Programada" (SCHEDULED) vía Laravel Reverb

> **Staff Engineer Plan** · Proyecto CFLA · Módulo LMS
> **Fecha**: 2026-08-12 · **Estado**: ✅ Implementado y verificado · **Última actualización**: 2026-08-13 (monitor en vivo + fixes)

---

## 1. Objetivo

Cuando un **profesor programa una lección** (estado `SCHEDULED`), los usuarios responsables
(**Planificación**, **Coordinación**, **Leadership**, **Admin**) deben recibir la notificación
**en tiempo real** (WebSocket), actualizando el contador/badge de lecciones pendientes en su
navbar **sin recargar la página**.

El envío en diferido (canal `database`) ya existía; este plan añade la capa **push** en vivo.

---

## 2. Arquitectura

```
┌─────────────┐    dispatch     ┌──────────────────────┐   ShouldBroadcastNow   ┌───────────┐
│ LessonWizard│ ──────────────▶ │ LessonScheduledEvent │ ─────────────────────▶ │  Reverb   │
│ (Profesor)  │                  │ app/Events/Lms/      │  (canales privados)    │  :8090    │
└─────────────┘                  └──────────────────────┘                        └─────┬─────┘
                                                                                        │ WS
                                                         ┌──────────────────────────────┘
                                                         ▼
                                               ┌──────────────────────┐
                                               │   Echo (browser)     │
                                               │  private.App.Models. │
                                               │  User.{id}           │
                                               └──────────┬───────────┘
                                     ┌────────────────────┼─────────────────────┐
                                     ▼                    ▼                     ▼
                       ┌──────────────────────┐ ┌────────────────────┐ ┌──────────────────────┐
                       │ LessonPendingCount   │ │  Livewire.dispatch │ │ MonitorStats (widget)│
                       │ (badge navbar)       │ │  'lesson-scheduled'│ │ (stats del monitor:  │
                       │ ← refresca contador  │ │  (bootstrap.js)    │ │  card "Programadas"  │
                       └──────────────────────┘ └────────────────────┘ │  ← en vivo + poll)   │
                                                                        └──────────────────────┘
```

**Flujo completo**:

1. Profesor `confirmPublish()` con fecha → `LmsPublicationService::publish()` → estado `SCHEDULED`.
2. `LmsPublicationService::notifyScheduled($activity, $publisherId, $publishAt)` (dentro de `publish()` cuando `!$authorized`):
   - Envía `LessonScheduledForApproval` (canal `database`) a los responsables (`is_planner | is_admin | is_leadership | is_coordinacion`).
   - Dispara `LessonScheduled::dispatch(...)` (evento `ShouldBroadcastNow`).
   - Registra `LmsActivityLog::record('SCHEDULE')`.
3. `LessonScheduled::broadcastOn()` → **1 canal privado por responsable**: `private-App.Models.User.{id}`.
4. Reverb entrega el evento al socket del navegador del responsable.
5. `bootstrap.js` escucha `.lesson.scheduled` → `Livewire.dispatch('lesson-scheduled')` (con payload).
6. Componente `LessonPendingCount` refresca su contador (`SCHEDULED` count) → badge se actualiza al instante. Su listener Echo propio (`refreshCountFromEcho`) además **muestra el toast WireUI** con link al monitor (dedup por usuario+lección).
7. Widget `MonitorStats` (listener Echo propio `echo-private:App.Models.User.{id},.lesson.scheduled`) recalcula sus stats → la tarjeta **"Programadas"** del monitor se actualiza en vivo (fallback `wire:poll.{{ config }}ms` si Reverb cae).
8. `LmsMonitor` escucha el mismo broadcast (`refreshFromRealtimeEvent`) → el **listado** se re-renderiza con la lección nueva.

---

## 3. Archivos creados/modificados

| Tipo | Archivo | Detalle |
|---|---|---|
| ➕ | `app/Events/Lms/LessonScheduled.php` | Evento `ShouldBroadcastNow` con canal privado por destinatario |
| ✏️ | `app/Services/Lms/LmsPublicationService.php` | **Emisor único** (`Opción 11`): `publish()` con `!$authorized` → `notifyScheduled()` = `LessonScheduledForApproval` (DB) + `LessonScheduled::dispatch` (broadcast) + `LmsActivityLog::record('SCHEDULE')` |
| ✏️ | `app/Livewire/Profesor/Lms/LessonWizard.php` | `saveAndPublish()` delega en `LmsPublicationService::publish()`. `notifyPlanningScheduled()` (emisión duplicada) **eliminado** — el servicio es el único punto de emisión |
| ➕ | `app/Livewire/Planning/Lms/LessonPendingCount.php` | Componente contador de lecciones `SCHEDULED`, con listener Echo privado + **toast WireUI** (dedup por usuario+lección) |
| ➕ | `resources/views/livewire/planning/lms/lesson-pending-count.blade.php` | Badge ámbar clicable (→ monitor filtrado) + `wire:poll.{{ config('broadcasting.poll_interval') }}ms` como fallback |
| ✏️ | `resources/views/components/navbars/planning-items.blade.php` | `<livewire:planning.lms.lesson-pending-count />` junto a "Contenido LMS" |
| ✏️ | `resources/views/components/navbars/*-items(-mobile).blade.php` | Badge en las **8 navbars**: planning, coordinación, director, admin (+ variantes móvil) |
| ✏️ | `app/Livewire/Planning/Lms/MonitorStats.php` | **Widget de stats del monitor** (Total, Publicadas, Programadas…): listener Echo privado `.lesson.scheduled` → `refreshStatsFromEcho()` + `wire:poll.{{ config('broadcasting.poll_interval') }}ms` como fallback |
| ✏️ | `resources/views/livewire/planning/lms/monitor-stats.blade.php` | Grid de tarjetas de stats del monitor (la tarjeta **"Programadas" se actualiza en vivo**) |
| ✏️ | `resources/views/livewire/planning/lms/monitor.blade.php` | Embebe `<livewire:planning.lms.monitor-stats />` (los stats ya no se calculan en `LmsMonitor`) |
| ✏️ | `app/Livewire/Planning/Lms/LmsMonitor.php` | **Listado en vivo** (Opción 6): listener `lesson-scheduled` + `echo-private:...lesson.scheduled` → `refreshFromRealtimeEvent()` (re-render) |
| ✏️ | `config/broadcasting.php` | `poll_interval` = `REVERB_POLL_INTERVAL` (default 5000ms) — cadencia de `wire:poll` en badge y stats |
| ✏️ | `resources/views/layouts/dashboard.blade.php` | `data-reverb="enabled"` + `data-user-id="{{ auth()->id() }}"` en `<html>` |
| ✏️ | `resources/js/bootstrap.js` | Suscripción `Echo.private('App.Models.User.{id}')` → `Livewire.dispatch('lesson-scheduled')` |
| ✏️ | `tests/Feature/Livewire/Profesor/Lms/LessonWizardCharacterizationTest.php` | Assert `Event::assertDispatched(LessonScheduled::class)` + no-duplicación (times=1) |
| ➕ | `tests/Feature/Planning/Lms/LmsPublicationServiceTest.php` | Opción 11: 1 broadcast + 1 notificación DB por `SCHEDULED`; autorizado no emite; no acumula |
| ➕ | `tests/Feature/Planning/Lms/LmsMonitorRealtimeTest.php` | Opción 6: listener de refresh + re-render del listado |
| ➕ | `tests/Feature/Planning/Lms/MonitorStatsTest.php` | Stats en vivo: render + counts + refresh |
| ➕ | `tests/Feature/Livewire/Planning/Lms/LessonPendingCountTest.php` | Badge: render + listener Echo + dedup del toast |

---

## 4. Detalle de implementación

### 4.1 Evento `LessonScheduled`

```php
class LessonScheduled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Activity $activity,
        public array $recipients,      // User[] (planner/admin/leadership/coordinación)
        public string $teacherName,
        public string $scheduledFor,
    ) {}

    public function broadcastOn(): array
    {
        return collect($this->recipients)
            ->map(fn (User $user) => new PrivateChannel('App.Models.User.'.$user->id))
            ->values()->all();
    }

    public function broadcastAs(): string { return 'lesson.scheduled'; }

    public function broadcastWith(): array
    {
        $title = $this->activity->topic ?? 'Lección';
        return [
            'type'         => 'lesson_scheduled',
            'activity_id'  => $this->activity->id,
            'teacher_name' => $this->teacherName,
            'lesson_title' => $title,
            'scheduled_at' => $this->scheduledFor,
            'message'      => "{$this->teacherName} ha programado la lección «{$title}» para aprobación de Planificación.",
            'url'          => route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
        ];
    }
}
```

> ⚠️ **Nota**: `broadcastWith()` usa variable `$title` porque PHP no permite `??` dentro de
> interpolación de strings `"{$this->x ?? 'y'}"`.

### 4.2 Disparo en `LmsPublicationService::notifyScheduled()` (emisor único)

```php
// Dentro de publish() cuando !$authorized (queda SCHEDULED)
Notification::send($recipients, new LessonScheduledForApproval(
    activityId: $activity->id,
    teacherName: $teacherName,
    activityTitle: $activity->topic ?? 'Lección',
    scheduledAt: $scheduledFor,
));

LessonScheduled::dispatch($activity, $recipients->all(), $teacherName, $scheduledFor);
LmsActivityLog::record($activity->id, $publisherId, 'SCHEDULE');
```

### 4.3 Componente contador `LessonPendingCount`

- Cuenta `Activity::whereHas('lmsPublication', fn($q) => $q->where('status','SCHEDULED'))->count()`.
- Listener **Echo privado** (con evento): `'echo-private:App.Models.User.{id},.lesson.scheduled' => 'refreshCountFromEcho'`.
  > ⚠️ El evento es obligatorio: sin `,.lesson.scheduled`, Livewire llama `listen(undefined)` y lanza TypeError (`charAt`) que rompía el navbar.
- Escucha además `lesson-scheduled` (dispatch de `bootstrap.js`) → `refreshCount` (evita el no-op silencioso de `$refreshCount`).
- **Toast en vivo (Opción 1)**: `refreshCountFromEcho` muestra `wireui:notification` (WireUI, descripción con link al monitor). Dedup por usuario + `activity_id` en session → con N badges en la página solo aparece 1 toast. El evento intermedio `lesson-scheduled-toast` se eliminó.
- Fallback: `wire:poll.{{ config('broadcasting.poll_interval') }}ms` en la vista (si WebSocket cae, el badge se actualiza igual en ≤ el intervalo configurado, default 5s).

### 4.4 Frontend (bootstrap.js)

```js
const userId = document.documentElement.dataset.userId;
if (userId) {
    Echo.private(`App.Models.User.${userId}`)
        .listen('.lesson.scheduled', (e) => {
            Livewire.dispatch('lesson-scheduled', e);  // e = payload completo del evento
        });
}
```

`data-reverb="enabled"` + `data-user-id` van en `<html>` del layout `dashboard`.

### 4.5 Vista en vivo del monitor (Opción 6)

`LmsMonitor` escucha el broadcast (listener Echo privado + `lesson-scheduled`) y re-renderiza el listado con los datos frescos:

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
    // Sin-op: Livewire re-renderiza tras el listener y la query corre fresca.
}
```

Los stats viven en el widget `MonitorStats` (componente hijo con su propio listener + poll).

---

## 5. Infraestructura (Reverb)

- **Config**: `BROADCAST_DRIVER=reverb`, `REVERB_HOST=localhost`, `REVERB_PORT=8090` (`.env`).
- **Servidor**: `php artisan reverb:start --host=127.0.0.1 --port=8090`
- **Supervisor**: `/etc/supervisor/conf.d/` (`[program:cfla-reverb]` definido, `autorestart=true`).
- **Auth de canal**: `routes/channels.php` → `App.Models.User.{id}` exige `$user->id === $id`.

> ⚠️ **Importante**: Reverb DEBE estar corriendo para el push en vivo. El `wire:poll.{{ config('broadcasting.poll_interval') }}ms` (default 5s)
> garantiza que el contador nunca quede obsoleto aunque Reverb esté caído.

---

## 6. Verificación

### 6.1 Tests (automáticos)

```bash
php8.2 artisan test tests/Feature/Livewire/Profesor/Lms/LessonWizardCharacterizationTest.php
# → 69 passed (170 assertions)

# Suites de los componentes nuevos (badge, stats, listado, servicio)
php8.2 artisan test tests/Feature/Livewire/Planning/Lms/ tests/Feature/Planning/Lms/
# → incluye LessonPendingCountTest, MonitorStatsTest, LmsMonitorRealtimeTest, LmsPublicationServiceTest
```

Test clave: `confirm_publish_profesor_con_fecha_programa_y_notifica_responsables`
- `Notification::fake()` + `Event::fake()`
- Assert: lección queda `SCHEDULED`, `published_at` nulo
- `Notification::assertSentTo([...], LessonScheduledForApproval::class)`
- `Event::assertDispatched(LessonScheduled::class, fn($e) => destinatarios + activity correctos)`
- Opción 11 (no-duplicación): `Event::assertDispatchedTimes(LessonScheduled::class, 1)` + 1 notificación por destinatario

### 6.2 Prueba manual / Tinker

```php
// Canales generados
(new LessonScheduled($activity, [$user], 'Prof. X', '15/08/2026 10:00'))->broadcastOn();
// → ["private-App.Models.User.1"]

// Disparo real
event(new LessonScheduled($activity, [$user], 'Prof. X', '15/08/2026 10:00'));
// Reverb recibe y entrega al socket autenticado del usuario
```

### 6.3 Prueba de canal (cliente WS nativo)

```js
// Sin auth → Reverb responde: {"code":4009,"message":"Connection is unauthorized"} (esperado/seguro)
```

El canal privado **rechaza conexiones no autenticadas** — correcto por diseño.

---

## 7. Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| Reverb caído → no llega push | `wire:poll.{{ config('broadcasting.poll_interval') }}ms` como fallback (default 5s de desfase) |
| Conexiones no autorizadas al canal privado | Auth `App.Models.User.{id}` en `channels.php` |
| Muchos destinatarios → muchos canales | Un canal privado por user (patrón estándar de Laravel); N pequeño (planners/admins) |
| Evento con modelos grandes en `broadcastWith` | Solo se serializan datos escalares + `activity_id` |
| `ShouldBroadcastNow` sin cola | Correcto para eventos pequeños e inmediatos (ver Opción 9 del plan de mejora si escala) |
| Usuario multi-rol ve N badges (1 por navbar) | Counts idénticos (contador global); toast deduplicado por usuario+lección en session |

---

## 8. Próximos pasos (opcional)

- [x] **Badge también en navbars** de Coordinación, Leadership y Admin (`director-items(-mobile)`, `coordinacion-items(-mobile)`, `admin-items(-mobile)`) — 8 navbars en total.
- [x] **Toast en vivo** al recibir el evento, con link al monitor, deduplicado por sesión (Opción 1).
- [x] **Refresh en vivo del listado** del monitor (Opción 6 completa): `LmsMonitor::refreshFromRealtimeEvent()`.
- [x] **Poll configurable** vía `REVERB_POLL_INTERVAL` (Opción 3).
- [x] **Test de no-duplicación** de la emisión (Opción 11): `LmsPublicationServiceTest`.
- [ ] Marcar la notificación DB como `read` al hacer clic en el enlace (Opción 5).
- [ ] Contador por rol y scope de coordinación (Opción 2) — decisión de negocio.
- [ ] Cola + crash-guard para `ShouldBroadcastNow` (Opción 9).
- [x] Test de `LessonPendingCount` (render + listener Echo) y de `MonitorStats` (render + counts + refresh).

---

## 9. Changelog

| Fecha | Cambio |
|---|---|
| 2026-08-12 | Creación del plan e implementación completa (evento, dispatch, contador, navbar, Echo, tests, Reverb up) |
| 2026-08-13 | **Monitor en vivo**: nuevo widget `MonitorStats` (listener Echo `.lesson.scheduled` + `wire:poll.5s`) que actualiza la tarjeta "Programadas" del monitor sin recargar; `LmsMonitor` deja de calcular stats (`getStats()` eliminado). Fix del listener Echo (`,.lesson.scheduled`) y del no-op `$refreshCount`. Badge clicable → monitor filtrado. Cobertura de badge en navbars de coordinación/director (+móvil). |
| 2026-08-13 | **Emisión centralizada consolidada**: eliminado `LessonWizard::notifyPlanningScheduled()` (dead code, emisión duplicada) y sus imports huérfanos; `LmsPublicationService::notifyScheduled()` queda como único punto de emisión. Verificado por `LessonWizardCharacterizationTest` (69 passed). |
| 2026-08-13 | **Opciones 1, 3 y 6 completas**: toast WireUI con link al monitor (dedup por sesión); badge en las 8 navbars; poll configurable `REVERB_POLL_INTERVAL`; listado del monitor en vivo (`refreshFromRealtimeEvent`). Tests: `LmsPublicationServiceTest`, `LmsMonitorRealtimeTest`, dedup del toast. |
