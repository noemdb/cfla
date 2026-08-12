# Plan Integral — Notificación en Tiempo Real de "Lección Programada" (SCHEDULED) vía Laravel Reverb

> **Staff Engineer Plan** · Proyecto CFLA · Módulo LMS
> **Fecha**: 2026-08-12 · **Estado**: ✅ Implementado y verificado

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
                                                         │ Livewire.dispatch('lesson-scheduled')
                                                         ▼
                                              ┌──────────────────────┐
                                              │ LessonPendingCount   │  ← refresca contador
                                              │ (navbar / monitor)   │
                                              └──────────────────────┘
```

**Flujo completo**:

1. Profesor `confirmPublish()` con fecha → `LmsPublicationService::publish()` → estado `SCHEDULED`.
2. `notifyPlanningScheduled($activityId)`:
   - Envía `LessonScheduledForApproval` (canal `database`) a los responsables.
   - Dispara `LessonScheduled::dispatch(...)` (evento `ShouldBroadcastNow`).
3. `LessonScheduled::broadcastOn()` → **1 canal privado por responsable**: `private-App.Models.User.{id}`.
4. Reverb entrega el evento al socket del navegador del responsable.
5. `bootstrap.js` escucha `.lesson.scheduled` → `Livewire.dispatch('lesson-scheduled')`.
6. Componente `LessonPendingCount` refresca su contador (`SCHEDULED` count) → badge se actualiza al instante.

---

## 3. Archivos creados/modificados

| Tipo | Archivo | Detalle |
|---|---|---|
| ➕ | `app/Events/Lms/LessonScheduled.php` | Evento `ShouldBroadcastNow` con canal privado por destinatario |
| ✏️ | `app/Livewire/Profesor/Lms/LessonWizard.php` | `use App\Events\Lms\LessonScheduled;` + `LessonScheduled::dispatch(...)` en `notifyPlanningScheduled()` |
| ➕ | `app/Livewire/Planning/Lms/LessonPendingCount.php` | Componente contador de lecciones `SCHEDULED`, con listener Echo privado |
| ➕ | `resources/views/livewire/planning/lms/lesson-pending-count.blade.php` | Badge ámbar con conteo + `wire:poll.5s` como fallback |
| ✏️ | `resources/views/components/navbars/planning-items.blade.php` | `<livewire:planning.lms.lesson-pending-count />` junto a "Contenido LMS" |
| ✏️ | `resources/views/layouts/dashboard.blade.php` | `data-reverb="enabled"` + `data-user-id="{{ auth()->id() }}"` en `<html>` |
| ✏️ | `resources/js/bootstrap.js` | Suscripción `Echo.private('App.Models.User.{id}')` → `Livewire.dispatch('lesson-scheduled')` |
| ✏️ | `tests/Feature/Livewire/Profesor/Lms/LessonWizardCharacterizationTest.php` | Assert `Event::assertDispatched(LessonScheduled::class)` |

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

### 4.2 Disparo en `LessonWizard::notifyPlanningScheduled()`

```php
LessonScheduled::dispatch(
    $activity,
    $planners->all(),                       // mismos destinatarios que la notificación DB
    auth()->user()->fullName ?? 'Profesor',
    $scheduledDate,
);
```

### 4.3 Componente contador `LessonPendingCount`

- Cuenta `Activity::whereHas('lmsPublication', fn($q) => $q->where('status','SCHEDULED'))->count()`.
- Listener **Echo privado**: `'echo-private:App.Models.User.'.auth()->id() => 'refreshCountFromEcho'`.
- Fallback: `wire:poll.5s` en la vista (si WebSocket cae, el badge se actualiza igual en ≤5s).
- Escucha además eventos Livewire `lesson-scheduled`, `lesson-published`, `lesson-approved`.

### 4.4 Frontend (bootstrap.js)

```js
const userId = document.documentElement.dataset.userId;
if (userId) {
    Echo.private(`App.Models.User.${userId}`)
        .listen('.lesson.scheduled', () => {
            Livewire.dispatch('lesson-scheduled');
        });
}
```

`data-reverb="enabled"` + `data-user-id` van en `<html>` del layout `dashboard`.

---

## 5. Infraestructura (Reverb)

- **Config**: `BROADCAST_DRIVER=reverb`, `REVERB_HOST=localhost`, `REVERB_PORT=8090` (`.env`).
- **Servidor**: `php artisan reverb:start --host=127.0.0.1 --port=8090`
- **Supervisor**: `/etc/supervisor/conf.d/` (`[program:cfla-reverb]` definido, `autorestart=true`).
- **Auth de canal**: `routes/channels.php` → `App.Models.User.{id}` exige `$user->id === $id`.

> ⚠️ **Importante**: Reverb DEBE estar corriendo para el push en vivo. El `wire:poll.5s`
> garantiza que el contador nunca quede obsoleto aunque Reverb esté caído.

---

## 6. Verificación

### 6.1 Tests (automáticos)

```bash
php8.2 artisan test tests/Feature/Livewire/Profesor/Lms/LessonWizardCharacterizationTest.php
# → 69 passed (170 assertions)
```

Test clave: `confirm_publish_profesor_con_fecha_programa_y_notifica_responsables`
- `Notification::fake()` + `Event::fake()`
- Assert: lección queda `SCHEDULED`, `published_at` nulo
- `Notification::assertSentTo([...], LessonScheduledForApproval::class)`
- `Event::assertDispatched(LessonScheduled::class, fn($e) => destinatarios + activity correctos)`

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
| Reverb caído → no llega push | `wire:poll.5s` como fallback (máx. 5s de desfase) |
| Conexiones no autorizadas al canal privado | Auth `App.Models.User.{id}` en `channels.php` |
| Muchos destinatarios → muchos canales | Un canal privado por user (patrón estándar de Laravel); N pequeño (planners/admins) |
| Evento con modelos grandes en `broadcastWith` | Solo se serializan datos escalares + `activity_id` |
| `ShouldBroadcastNow` sin cola | Correcto para eventos pequeños e inmediatos |

---

## 8. Próximos pasos (opcional)

- [ ] Añadir **badge también en navbars** de Coordinación y Leadership (`director-items`, `coordinacion-items`) si se desea el mismo contador.
- [ ] Toast en vivo (WireUI `notification()->success`) al recibir el evento, con link al monitor.
- [ ] Marcar la notificación DB como `read` al hacer clic en el enlace.
- [ ] Añadir test de `LessonPendingCount` (render + refreshCount + listener Echo).

---

## 9. Changelog

| Fecha | Cambio |
|---|---|
| 2026-08-12 | Creación del plan e implementación completa (evento, dispatch, contador, navbar, Echo, tests, Reverb up) |
