# Spec integral — Dropdown de notificaciones (últimas notificaciones + Reverb) y página "Ver todas"

> Estado: **Draft — pendiente de aprobación**
> Fecha: 2026-08-14
> Autor: sesión de desarrollo SAEFL
> Relacionado: `blueprint/lms/plan-mejora-notificacion-reverb.md`, `blueprint/lms/notificacion-tiempo-real-leccion-programada.md`, `blueprint/lms/proximos-pasos-recomendados.md`

---

## 1. Objetivo

Mostrar al usuario autenticado, desde un **icono de campana en el navbar** (compartido por todos los roles), las **últimas notificaciones** que tiene en la tabla `notifications` (base de datos), **actualizadas en tiempo real** vía Laravel Reverb, con un **botón "Ver todas las notificaciones"** que abre una página paginada con el histórico completo y acciones de lectura.

Hoy el sistema **ya emite** notificaciones de base de datos (`LessonScheduledForApproval` → tabla `notifications`) y eventos broadcast (`lesson.scheduled` → Reverb), pero **no existe ninguna UI** para consumirlas: la campana solo existe como badge de contador (`LessonPendingCount`) en algunos navbars y sin listado.

## 2. Contexto actual (AS-IS)

### 2.1 Persistencia
- Tabla `notifications` (Laravel estándar): `id` uuid, `type`, `notifiable_id`/`notifiable_type` (morph), `data` (text/JSON), `read_at` nullable, timestamps.
  - Migración en `database/migrations/sql/lms/2026_07_23_000001_create_notifications_table.php`.
  - **Hallazgo (deuda técnica):** la migración vive en `database/migrations/sql/lms/`, **fuera de la ruta auto-cargada** por Laravel (`database/migrations`). En la BD actual la tabla **existe** (creada vía dump `lms_all_tables.sql`), pero un `artisan migrate` desde cero **no la crearía**. Ver §10.
- `App\Models\User` usa `HasApiTokens, HasFactory, Notifiable` → dispone de `notifications()`, `unreadNotifications()`, `markAsRead()`, `notifications()->delete()`, etc.

### 2.2 Único emisor de notificaciones DB hoy
`App\Services\Lms\LmsPublicationService::notifyScheduled()` (L238):
```php
Notification::send($recipients, new LessonScheduledForApproval(
    activityId: $activity->id,
    teacherName: ...,
    activityTitle: $activity->topic ?? 'Lección',
    scheduledAt: $scheduledFor,
));
```
`LessonScheduledForApproval::toDatabase()` guarda `data`:
```php
[
  'activity_id'    => $activity->id,
  'type'           => 'lesson_scheduled',
  'teacher_name'   => ...,
  'activity_title' => ...,
  'scheduled_at'   => ...,
  'message'        => "{teacher} ha programado la lección «{title}» para aprobación de Planificación.",
  'url'            => route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
]
```

### 2.3 Broadcast en tiempo real (Reverb)
- `App\Events\Lms\LessonScheduled` (`ShouldBroadcastNow`): canales privados `App.Models.User.{id}` por destinatario, `broadcastAs('lesson.scheduled')`, payload `{ activity, teacher_name, scheduled_for, event_id }`.
- `resources/js/bootstrap.js` inicializa Echo solo si `document.documentElement.dataset.reverb === 'enabled'` y escucha `.lesson.scheduled` → `Livewire.dispatch('lesson-scheduled', e)`.
- Layouts con `data-reverb="enabled"` + `data-user-id`: `layouts/dashboard.blade.php` y los 4 layouts de rol (`planning`, `coordinacion`, `director`, `profesors`).

### 2.4 Navbar compartido
- `resources/views/components/role-navbar.blade.php` es usado por los 5 layouts autenticados (`dashboard`, `planning/app`, `coordinacion/app`, `director/app`, `profesors/app`). Sección derecha (L27–44): perfil (username + role_label), logout, `<x-theme-toggle />`, hamburguesa.
- Los navbars de items (`planning-items`, `coordinacion-items`, etc.) ya incluyen `<livewire:planning.lms.lesson-pending-count />` (badge de contador).

### 2.5 Estado real hoy
- El **único** origen de notificaciones DB es `lesson_scheduled`. No hay página "ver todas", no hay dropdown, no hay evento broadcast genérico de notificación DB.

## 3. Hallazgos (oportunidades identificadas en el análisis)

| # | Hallazgo | Impacto | Decisión |
|---|----------|---------|----------|
| N1 | No hay UI para consumir las notificaciones DB ya existentes | Usuarios no ven las lecciones programadas salvo por el badge y el toast | Construir dropdown + página "Ver todas" (este spec) |
| N2 | No existe evento broadcast genérico al crear una notificación DB | El dropdown no puede actualizarse en tiempo real de forma desacoplada | Crear `NotificationReceived` (`App\Events\NotificationReceived`) emitido por un emisor central |
| N3 | `toDatabase()->url` apunta siempre a `app.planning.lms.monitor` (middleware `isPlanner`), pero los destinatarios incluyen `is_director`, `is_leadership`, `is_coordinacion` (getRecipients L117–162) | Un director/líder/coordinador que pulse la notificación recibiría 403 | Resolver el destino **según rol** con un resolver (`NotificationTargetResolver`); el `url` almacenado queda como fallback/canónico para planners |
| N4 | Migración de `notifications` fuera de la ruta estándar de Laravel | Fresh `migrate` no la crea | Mover a `database/migrations/` (deuda técnica, ver §10) |
| N5 | Raza de consistencia: `ShouldBroadcastNow` dispara antes del commit de la transacción que persiste la notificación | El refresh por broadcast puede no ver la fila recién creada | Payload del evento con datos para **inserción optimista** + reconciliación con BD al abrir el dropdown / poll |
| N6 | No hay caché del conteo de no leídas del dropdown (solo existe `PENDING_COUNT_CACHE_PREFIX` del badge de lecciones) | Consulta N+1 por render si se computa sin caché | Reutilizar patrón de caché por usuario + invalidación en el emisor central |

## 4. Diseño (TO-BE)

### 4.1 Arquitectura de referencia

```
notifyScheduled() y futuros emisores
        │
        ▼
NotificationService::notifyUsers($recipients, $notification)   ← punto central (N2)
        │                                    │
        ├─ Notification::send(...)           └─ dispatch(NotificationReceived(...))  por destinatario
        │                                                          │ (ShouldBroadcastNow, crash-guard)
        ▼                                                            ▼
tabla notifications (data: type, message, activity_id, ...)     Reverb: App.Models.User.{id}  .notification.received
        │                                                          │
        ▼                                                            ▼
┌──────────────────────────────┐       listener Echo (Livewire)   ┌───────────────────────────────┐
│ NotificationsIndex           │                                   │ NotificationBell (dropdown)   │
│ página app.notifications     │  ◄── "Ver todas" ─────────────────┤  • últimas N (BD)             │
│ paginada, tabs, marcar leída │                                   │  • inserción optimista (N5)   │
└──────────────────────────────┘                                   │  • badge no-leídas (caché)    │
        ▲                                                          │  • "Ver todas" → index        │
        └── NotificationTargetResolver: url según rol (N3)         └───────────────────────────────┘
```

### 4.2 Evento broadcast `NotificationReceived` (N2)

Nuevo `app/Events/NotificationReceived.php`:
- `implements ShouldBroadcastNow` (mismo patrón crash-guard que `LessonScheduled`).
- Constructor público: `string $notificationId`, `array $payload` (trozos de `data`: `type`, `message`, `url`/`route_name`, `activity_id`, `created_at`), `int $userId`.
- `broadcastOn(): PrivateChannel('App.Models.User.'.$this->userId)`.
- `broadcastAs(): 'notification.received'`.
- `broadcastWith(): ['id' => ..., 'data' => $this->payload, 'read_at' => null]` → suficiente para inserción optimista (N5).

### 4.3 Emisor central `NotificationService` (N2)

Nuevo `app/Services/NotificationService.php`:
```php
public function notifyUsers(iterable $recipients, Notification $notification): void
{
    $before = now();
    foreach ($recipients as $recipient) {
        Cache::forget(self::UNREAD_PREFIX.$recipient->id);   // badge dropdown (N6)
    }
    Notification::send($recipients, $notification);          // persiste filas

    // Broadcast optimista por destinatario (crash-guard, nunca rompe el request)
    try {
        foreach ($recipients as $recipient) {
            $id = $recipient->notifications()->orderByDesc('created_at')->first()?->id
                   ?? Str::uuid(); // fallback si no se pudo leer
            NotificationReceived::dispatch(
                notificationId: $id,
                payload: $notification->toDatabase($recipient) + ['created_at' => $before->toIso8601String()],
                userId: $recipient->id,
            );
        }
    } catch (\Throwable $e) {
        Log::warning('NotificationReceived falló (Reverb caído), cubre poll', ['error' => $e->getMessage()]);
    }
}
```
- Refactor: `LmsPublicationService::notifyScheduled()` delega en `NotificationService::notifyUsers($recipients, new LessonScheduledForApproval(...))` (mantiene `getRecipients`, invalidación de `PENDING_COUNT_CACHE_PREFIX` y auditoría `BroadcastAudit`/`LessonScheduled` intactos).
- **Regla de oro**: toda notificación DB futura se emite por `NotificationService` para heredar el broadcast + invalidación de caché automáticamente.

### 4.4 Componente `NotificationBell` (dropdown)

Nuevo `app/Livewire/App/Notifications/NotificationBell.php` (render parcial, anónimo o con vista propia):

**Estado:**
- `public array $notifications = []` (últimas **5–8**, item: `id, type, message, url, created_at, read_at, icon/color`).
- `public int $unreadCount = 0` (caché por usuario, ver §4.6).
- `public bool $realtimeEnabled = false` (inyectado desde el layout `data-reverb`).
- `public bool $reconciledAt` (timestamp del último sync con BD).

**Listeners:**
```php
protected function getListeners(): array
{
    $userId = auth()->id();
    return [
        'echo-private:App.Models.User.'.$userId.',.notification.received' => 'onNotificationReceived',
        'notification-received'    => 'onNotificationReceived',   // fallback JS (bootstrap.js)
        'notification-read'        => 'reconcile',
    ];
}
```

**Métodos:**
- `mount()`: `$this->realtimeEnabled = request()->... ?? (bool)($this->reverbEnabledFromLayout())`; carga inicial con `reconcile()`.
- `onNotificationReceived($payload)`: **inserción optimista** (N5) — si `id` ya existe en `$this->notifications`, solo `reconcile()` (o no-op); si no, `array_unshift` el item + `$this->unreadCount++` (truncar a MAX_RECENT) e invalidar caché. No hace consulta DB.
- `reconcile()`: relee de BD las últimas N + cuenta no-leídas; actualiza `$this->notifications` y `$this->unreadCount` (fuente de verdad). Se llama en `mount`, al abrir el dropdown (`@click` → `$wire.reconcile()`), en el `wire:poll` de fallback y tras marcar leídas.
- `markAllAsRead()`: `auth()->user()->unreadNotifications()->update(['read_at' => now()])` + invalidar caché + `reconcile()`. (Optimización futura: usar `getQuery()` para update masivo sin cargar modelos.)
- `markAsRead($id)`: marca una; invalida caché; `reconcile()`.
- `open()`: `$this->reconcile()` antes de abrir (garantiza consistencia).

**Vista** (inline en `role-navbar` o blade parcial):
- Botón campana con badge `{{ $unreadCount }}` (oculto si 0), Alpine `@click="open = !open; $wire.open()"`, `@click.outside="open = false"`.
- Panel dropdown (posicionado a la derecha, `x-transition`, `z-[60]` para superar el `z-50` del header sticky):
  - Encabezado "Notificaciones" + acción "Marcar todas como leídas".
  - Lista de items: icono/color por `type` (mapeo), `message`, tiempo relativo (`diffForHumans`), indicador de no leída (punto).
  - Clic en item → `markAsRead($id)` + `window.location = $url` (url resuelta por rol, §4.7).
  - Estado vacío: "No tienes notificaciones".
  - Pie: **botón "Ver todas las notificaciones"** → `route('app.notifications.index')`.
- Fallback poll (si `! realtimeEnabled`): `wire:poll.30s="reconcile"`.

### 4.5 Página "Ver todas" — `NotificationsIndex`

Nuevo `app/Livewire/App/Notifications/NotificationsIndex.php` (full-page):
- `mount()` con filtro por query string (`tab` = `all|unread|read`).
- `public array $tabs = ['all' => 'Todas', 'unread' => 'No leídas', 'read' => 'Leídas'];`
- `public int $perPage = 15;` (paginación simple con `SimplePaginator` o paginación Livewire).
- Query: `auth()->user()->notifications()` ordenado por `created_at` desc, filtrado por `read_at` según tab.
- `markAsRead($id)` / `markAllAsRead()` con invalidación de caché.
- `targetUrl($notification)`: delega en `NotificationTargetResolver` (§4.7); fallback `data->url`.
- Render: `@livewire` en layout `layouts/app` (o el de cada rol) vía ruta.

**Ruta** (N3/alcance): accesible para **todo rol autenticado**:
```php
Route::prefix('app')->name('app.')->group(function () {
    Route::middleware(['auth'])->group(function () {
        Route::prefix('notificaciones')->name('notifications.')->group(function () {
            Route::get('/', \App\Livewire\App\Notifications\NotificationsIndex::class)
                ->name('index');   // → app.notifications.index
        });
    });
});
```

### 4.6 Caché del conteo (N6)

- Constante `NotificationService::UNREAD_PREFIX = 'user_unread_notifications_'`.
- `unreadCountFor(int $userId): int` → `Cache::remember(UNREAD_PREFIX.$userId, now()->addSeconds(self::cacheTtlSeconds()), fn () => User::find($userId)?->unreadNotifications()->count() ?? 0)`.
- TTL de cache: reutilizar la convención del módulo LMS (poll interval de 5s); se puede centralizar el valor en el servicio (p. ej. `cacheTtlSeconds()`).
- **Invalidación**: en `notifyUsers()` (por destinatario), en `markAsRead()`, `markAllAsRead()` y en el `onNotificationReceived` optimista.
- El badge de `LessonPendingCount` **no cambia** (es otro contador, de lecciones SCHEDULED no leídas).

### 4.7 Resolución de destino según rol (N3)

Nuevo `app/Services/NotificationTargetResolver.php`:
```php
public function resolveFor(User $user, array $data): string
{
    return match (true) {
        $user->is_admin || $user->is_planner  => route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
        $user->is_coordinacion                => route('app.coordinacion.lessons'),
        $user->is_leadership                  => route('app.leadership.lessons'),
        $user->is_director                    => route('app.director.lessons'),
        default                               => $data['url'] ?? '/',
    };
}
```
- Usado por el dropdown y por la página index al construir `targetUrl()`.
- `data['url']` almacenado queda como canónico para planners y como fallback genérico.
- **Futuro**: si el monitor pasa a ser accesible por más roles (autorización de scope), este resolver se simplifica o se elimina.

### 4.8 Integración en navbar (`role-navbar.blade.php`)

- En la sección derecha (junto a `x-theme-toggle`, L27–44), antes del perfil:
  ```blade
  @auth
      <livewire:app.notifications.notification-bell />
  @endauth
  ```
- Como `role-navbar` es compartido por los 5 layouts, la campana aparece en **todos los roles** (dashboard, planning, coordinación, dirección, profesor) sin tocar cada navbar de items.
- **Móvil**: la campana permanece en la barra superior (es un componente autónomo, `sm+` y `lg-`); el panel dropdown se posiciona absoluto respecto al botón. Adicionalmente, si se requiere, un item de campana en el menú móvil que enlace a `app.notifications.index` (fuera de alcance inicial, ver §11).
- Los navbars de items NO se modifican.

### 4.9 Hook JS (opcional, fallback de bootstrap.js)

`resources/js/bootstrap.js` ya despacha `lesson-scheduled` de forma global. Para el dropdown se usa el listener Echo **dentro del componente Livewire** (`echo-private:...notification.received`), por lo que **no es imprescindible** tocar `bootstrap.js`. Si se desea además un toast global, se añade:
```js
.listen('.notification.received', (e) => Livewire.dispatch('notification-received', e));
```
Se deja como decisión de implementación (mínima: no tocar `bootstrap.js`).

## 5. Opciones consideradas

| Opción | Descripción | Veredicto |
|--------|-------------|-----------|
| **A (recomendada)** | Evento genérico `NotificationReceived` + `NotificationService` central + dropdown optimista + index por rol | ✅ Desacoplado, reutilizable para cualquier futura notificación, cubre N2/N5/N6 |
| B | Reutilizar solo `lesson.scheduled` (el dropdown escucha el evento existente y refresca) | ❌ Acopla el dropdown a eventos LMS; no escala a otros tipos; no resuelve N2 de forma genérica |
| C | Solo página index con `wire:poll`, sin dropdown en navbar | ❌ No cumple el requisito de "últimas notificaciones" en el navbar ni "en tiempo real" |
| D | Broadcast de las filas DB mediante observador del modelo `DatabaseNotification` | ⚠️ Interesante pero más frágil (eventos de modelo + morf + riesgo de loops); el emisor central (A) es más explícito y auditado |

## 6. Modelo de datos

- **No requiere migración nueva**: se reutiliza la tabla `notifications` existente.
- Se recomienda añadir índice de apoyo si la BD crece: `morphs` ya indexa `notifiable`; el orden por `created_at` desc por usuario no necesita índice adicional en volúmenes actuales (posible `composite` futuro en `notifiable_id, created_at`).
- Deuda técnica (N4): **mover** `database/migrations/sql/lms/2026_07_23_000001_create_notifications_table.php` a `database/migrations/` y verificar que no se duplique contra `lms_all_tables.sql`. (Ver §10.)

## 7. Seguridad

- Canales privados: Echo `private('App.Models.User.{id}')` — solo el dueño recibe sus eventos; la autorización del canal ya existe (Laravel `AuthorizesRequests` en `BroadcastServiceProvider`, patrón por defecto).
- Componente `NotificationBell` y `NotificationsIndex`: solo `auth()->id()` para consultar sus propias notificaciones (`$user->notifications()`).
- **Escape de `data`**: `message` interpola texto editable (`activity->topic`). En el dropdown y la index usar **escapado** (`{{ $item['message'] }}` en Blade; o `e()` antes de insertar en el array). No usar `x-html` con contenido dinámico. (Lección aprendida del Fix 4 del módulo LMS: WireUI renderiza `description` con `x-html`.)
- No exponer `data` completa en `broadcastWith()` si contuviera datos sensibles; solo campos de presentación.
- CSRF: las acciones Livewire ya están protegidas; no hay rutas POST nuevas.
- La ruta `app.notifications.index` debe comprobar `auth` (sin middleware de rol: es personal).

## 8. Pruebas (Plan de pruebas)

Suites nuevas en `tests/Feature/App/Notifications/`:

1. **`NotificationServiceTest`**
   - `notify_users_persiste_notificaciones_db_y_emite_broadcast_por_destinatario` (mock de `NotificationReceived::dispatch` / `Event::fake`) — usa `DatabaseTransactions` (la tabla `notifications` existe en la BD real; no requiere `RefreshDatabase`).
   - `notify_users_invalida_cache_de_no_leidas_por_destinatario`.
   - `broadcast_fallido_no_rompe_el_request` (crash-guard, `Log::warning`).

2. **`NotificationBellTest`** (`Livewire::test`)
   - `render_muestra_ultimas_notificaciones_y_conteo`.
   - `listener_echo_notification_received_incluye_evento_y_canal_correcto` (Reflection sobre `getListeners`, patrón de `LessonPendingCountTest`).
   - `on_notification_received_inserta_optimista_sin_duplicar` (dos eventos con el mismo id → 1 item; badge +1).
   - `mark_all_as_read_marca_y_actualiza_vista`.
   - `badge_usa_cache_y_se_invalida_al_leer`.
   - `boton_ver_todas_enlaza_a_app_notifications_index`.

3. **`NotificationsIndexTest`**
   - `lista_pagina_ultimas_notificaciones_del_usuario_solo_suyas`.
   - `tabs_filtran_todas_no_leidas_leidas`.
   - `marcar_como_leida_actualiza_filtro`.
   - `usuario_no_ve_notificaciones_de_otros`.

4. **`NotificationTargetResolverTest`**
   - `resolve_para_planner_admin_director_leadership_coordinacion` (matriz de roles → rutas esperadas).

5. **Tests existentes**: seguirán pasando (no se cambia el comportamiento del badge ni de `notifyScheduled`, solo se refactoriza la emisión a `NotificationService`).

**Comandos:**
```bash
/usr/bin/php8.2 artisan test tests/Feature/App/Notifications/
/usr/bin/php8.2 artisan test tests/Feature/Livewire/Planning/Lms/ tests/Feature/Livewire/Profesor/Lms/LessonWizardCharacterizationTest.php
./vendor/bin/pint app/Services/NotificationService.php app/Services/NotificationTargetResolver.php app/Events/NotificationReceived.php app/Livewire/App/Notifications/ resources/views/components/role-navbar.blade.php
```

## 9. Riesgos y consideraciones

- **Raza broadcast/commit (N5)**: mitigada con inserción optimista + reconciliación. Riesgo residual: si Reverb llega antes del commit y el usuario abre el dropdown instantáneamente, `reconcile()` podría no ver la fila aún → el item optimista ya está mostrado; en el peor caso, el poll/refresh siguiente lo alinea.
- **Regresión en `notifyScheduled`**: el refactor a `NotificationService` debe preservar invalidación de `PENDING_COUNT_CACHE_PREFIX`, `BroadcastAudit` y `LessonScheduled`. Cubierto por la suite LMS existente.
- **Rendimiento**: `markAllAsRead()` con `update` masivo (sin cargar modelos) para no saturar.
- **Multi-layout**: la campana en `role-navbar` cubre los 5 layouts; verificar que el dropdown no quede cortado por `overflow` del header (usar `z-[60]` y posicionamiento absoluto relativo al botón).
- **Dark mode**: respetar el patrón de clases del proyecto (bg-white/dark:bg-gray-900).

## 10. Deuda técnica detectada (pendiente, fuera de este entregable)

- **N4**: mover la migración de `notifications` a `database/migrations/` y reconciliar con `database/migrations/sql/lms/lms_all_tables.sql`. Afecta a entornos fresh y a `RefreshDatabase`. (No bloquea este spec porque la tabla ya existe en la BD de trabajo y los tests usan `DatabaseTransactions`.)

## 11. Fuera de alcance (este entregable)

- Edición de perfil de usuario (spec aparte en `blueprint/profile`).
- Toast global al recibir notificación (decisión de implementación, §4.9).
- Campana duplicada en el menú móvil.
- Notificaciones de otros orígenes (votación, diagnóstico, pagos) — el sistema acepta cualquiera vía `NotificationService`, la UI las muestra genéricamente por `type` + `message`.
- Acceso multi-rol al monitor LMS (se resuelve por URL por rol, no abriendo el monitor).

## 12. Orden de implementación sugerido

1. `App\Events\NotificationReceived` + `App\Services\NotificationService` (con `UNREAD_PREFIX`, `unreadCountFor`, `notifyUsers`).
2. Refactor de `LmsPublicationService::notifyScheduled()` para delegar en `NotificationService` + correr suite LMS (verde).
3. `App\Services\NotificationTargetResolver` (+ test).
4. Ruta `app.notifications.index` + `App\Livewire\App\Notifications\NotificationsIndex` (+ tests).
5. `App\Livewire\App\Notifications\NotificationBell` + inserción en `role-navbar.blade.php` (+ tests).
6. Pint + suite completa.
7. (Opcional) hook en `bootstrap.js` para toast global.

## 13. Archivos involucrados

| Acción | Archivo |
|--------|---------|
| Crear | `app/Events/NotificationReceived.php` |
| Crear | `app/Services/NotificationService.php` |
| Crear | `app/Services/NotificationTargetResolver.php` |
| Crear | `app/Livewire/App/Notifications/NotificationBell.php` + vista |
| Crear | `app/Livewire/App/Notifications/NotificationsIndex.php` + vista |
| Modificar | `app/Services/Lms/LmsPublicationService.php` (delegar en NotificationService) |
| Modificar | `routes/web.php` (grupo `app.notifications.*`) |
| Modificar | `resources/views/components/role-navbar.blade.php` (insertar `<livewire:...notification-bell />`) |
| Modificar (opcional) | `resources/js/bootstrap.js` (hook `notification-received`) |
| Crear | `tests/Feature/App/Notifications/*` |
| Deuda (futura) | `database/migrations/sql/lms/2026_07_23_000001_create_notifications_table.php` → `database/migrations/` |

---

*Fin del spec.*