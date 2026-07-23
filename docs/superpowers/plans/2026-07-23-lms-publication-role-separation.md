# Publicación LMS: Separación de Roles (Profesor programa / Planning publica)

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** El profesor solo puede programar lecciones (SCHEDULED), el rol Planning/Admin publica (PUBLISHED) desde el monitor. Al programar, se envía notificación database a los usuarios planning.

**Architecture:** Modificaciones localizadas en LessonWizard (3 métodos + vista), nueva notificación database estándar de Laravel, badge visual en el monitor de Planning. Sin cambios en LmsPublicationService ni rutas.

**Tech Stack:** Laravel 10, Livewire 3, Tailwind CSS, MySQL

## Global Constraints

- Usar `php8.2` para todos los comandos artisan
- El idioma de la UI es español (`es`)
- Las notificaciones usan la tabla `notifications` estándar de Laravel (UUID primario)
- El getter `User::isPlanner` ya existe y devuelve `is_admin || is_planner`
- El trait `Notifiable` ya está presente en `User`
- Todas las clases PHP deben declarar `strict_types=1` implícito (seguir convención del proyecto)

---

### Task 1: Migración — tabla `notifications`

**Files:**
- Create: `database/migrations/2026_07_23_000001_create_notifications_table.php`

**Interfaces:**
- Produces: Migración que crea la tabla `notifications` estándar de Laravel (UUID primario, `notifiable_id` + `notifiable_type` polimórfica, `data` JSON, `read_at` timestamp)

- [ ] **Step 1: Create the migration file**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
```

- [ ] **Step 2: Run migration**

```bash
php8.2 artisan migrate
```

Expected output: `Migration created: notifications`

- [ ] **Step 3: Commit**

```bash
git add database/migrations/2026_07_23_000001_create_notifications_table.php
git commit -m "feat: add notifications table migration"
```

---

### Task 2: Clase de Notificación — `LessonScheduledForApproval`

**Files:**
- Create: `app/Notifications/LessonScheduledForApproval.php`

**Interfaces:**
- Consumes: `User::isPlanner` (getter exists), `route('app.planning.lms.monitor')` (route exists)
- Produces: `LessonScheduledForApproval` notification class usable via `Notification::send($users, new LessonScheduledForApproval(...))`
- Constructor params: `int $activityId`, `string $teacherName`, `string $activityTitle`, `string $scheduledAt`
- Via channels: `['database']`
- `toDatabase()` returns array with keys: `activity_id`, `type`, `teacher_name`, `activity_title`, `scheduled_at`, `message`, `url`

- [ ] **Step 1: Create the notification class**

```php
<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LessonScheduledForApproval extends Notification
{
    use Queueable;

    public function __construct(
        private int $activityId,
        private string $teacherName,
        private string $activityTitle,
        private string $scheduledAt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'activity_id'    => $this->activityId,
            'type'           => 'lesson_scheduled',
            'teacher_name'   => $this->teacherName,
            'activity_title' => $this->activityTitle,
            'scheduled_at'   => $this->scheduledAt,
            'message'        => "{$this->teacherName} ha programado la lección «{$this->activityTitle}» para aprobación de Planificación.",
            'url'            => route('app.planning.lms.monitor', ['filterStatus' => 'SCHEDULED']),
        ];
    }
}
```

- [ ] **Step 2: Commit**

```bash
git add app/Notifications/LessonScheduledForApproval.php
git commit -m "feat: add LessonScheduledForApproval notification"
```

---

### Task 3: LessonWizard PHP — lógica de roles

**Files:**
- Modify: `app/Livewire/Profesor/Lms/LessonWizard.php`
  - Add `use App\Models\User;` (verify exists)
  - Add `use Illuminate\Support\Facades\Notification;`
  - Add `use App\Notifications\LessonScheduledForApproval;`
  - Add method `isCurrentUserPlanner(): bool`
  - Modify method `confirmPublish(): void`
  - Add method `notifyPlanningScheduled(int $activityId): void`

**Interfaces:**
- Consumes: `auth()->user()->isPlanner` (getter), `LessonScheduledForApproval`, `User::where('is_planner', true)->orWhere('is_admin', true)->get()`
- Produces: Modified `confirmPublish()` with role-aware branching, `notifyPlanningScheduled()` called after teacher schedules

- [ ] **Step 1: Add imports at the top of the file**

Add these use statements after existing imports (after line ~21, after `use App\Services\Lms\LmsPublicationService;`):

```php
use App\Models\User;
use App\Notifications\LessonScheduledForApproval;
use Illuminate\Support\Facades\Notification;
```

- [ ] **Step 2: Add the `isCurrentUserPlanner()` method**

Add this method somewhere before `confirmPublish()` (around line 3415):

```php
public function isCurrentUserPlanner(): bool
{
    return auth()->user()->isPlanner;
}
```

- [ ] **Step 3: Modify `confirmPublish()`**

Replace the current method (lines 3417-3424):

```php
public function confirmPublish(): void
{
    // Planners/admins pueden publicar directamente (comportamiento actual)
    if ($this->isCurrentUserPlanner()) {
        if (blank($this->publishAt)) {
            $this->showPublishConfirm = true;
        } else {
            $this->saveAndPublish();
        }
        return;
    }

    // Profesores: solo pueden programar (requiere fecha)
    if (blank($this->publishAt)) {
        $this->notification()->warning(
            'Fecha requerida',
            'Debes establecer una fecha de programación. La lección será revisada y publicada por Planificación.'
        );
        return;
    }

    $this->saveAndPublish();
}
```

- [ ] **Step 4: Add the `notifyPlanningScheduled()` method**

Add this method after `saveAndPublish()` (after line ~3695):

```php
private function notifyPlanningScheduled(int $activityId): void
{
    $activity = \App\Models\app\Academy\Activity::find($activityId);
    if (!$activity) {
        return;
    }

    $planners = User::query()
        ->where('is_planner', true)
        ->orWhere('is_admin', true)
        ->get();

    $scheduledDate = $this->publishAt
        ? \Carbon\Carbon::parse($this->publishAt)->format('d/m/Y H:i')
        : '—';

    Notification::send($planners, new LessonScheduledForApproval(
        activityId: $activityId,
        teacherName: auth()->user()->fullName ?? 'Profesor',
        activityTitle: $activity->topic ?? 'Lección',
        scheduledAt: $scheduledDate,
    ));

    \App\Models\app\Academy\Lms\LmsActivityLog::record($activityId, auth()->id(), 'SCHEDULE');
}
```

- [ ] **Step 5: Call `notifyPlanningScheduled()` at the end of `saveAndPublish()`**

Add this right before `$this->dispatch('lesson-saved');` (around line 3694), after the log record:

```php
// Si el usuario es profesor (no planner), notificar a planning
if (!$this->isCurrentUserPlanner()) {
    $this->notifyPlanningScheduled($activityId);
}
```

- [ ] **Step 6: Verify no syntax errors**

```bash
php8.2 artisan tinker --execute="echo 'LessonWizard imports OK';"
php8.2 -l app/Livewire/Profesor/Lms/LessonWizard.php
```

Expected: `No syntax errors detected`

- [ ] **Step 7: Commit**

```bash
git add app/Livewire/Profesor/Lms/LessonWizard.php
git commit -m "feat: add role-based publish logic in LessonWizard"
```

---

### Task 4: LessonWizard Blade — botón condicional + textos

**Files:**
- Modify: `resources/views/livewire/profesor/lms/lesson-wizard.blade.php`
  - Replace el botón "Publicar lección" con versión condicional por rol
  - Actualizar el texto informativo en el panel de estados publicado/programado

- [ ] **Step 1: Replace the "Publicar lección" button with role-aware version**

Find the current button block (around line 3700-3707):

```html
                            <button wire:click="confirmPublish"
                                    wire:loading.attr="disabled"
                                    class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 disabled:bg-gray-200 dark:disabled:bg-slate-700 disabled:text-gray-400 dark:disabled:text-slate-500 text-white text-sm font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                                <svg wire:loading wire:target="confirmPublish" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Publicar lección
                            </button>
```

Replace with:

```html
                            @php $isPlanner = auth()->user()->isPlanner; @endphp

                            @if($isPlanner)
                                {{-- Planner/Admin: botón "Publicar lección" (comportamiento actual) --}}
                                <button wire:click="confirmPublish"
                                        wire:loading.attr="disabled"
                                        class="w-full py-2 bg-emerald-600 hover:bg-emerald-500 disabled:bg-gray-200 dark:disabled:bg-slate-700 disabled:text-gray-400 dark:disabled:text-slate-500 text-white text-sm font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                                    <svg wire:loading wire:target="confirmPublish" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="confirmPublish">Publicar lección</span>
                                    <span wire:loading wire:target="confirmPublish">Publicando…</span>
                                </button>
                            @else
                                {{-- Profesor: botón "Programar lección" --}}
                                <button wire:click="confirmPublish"
                                        wire:loading.attr="disabled"
                                        @if(blank($publishAt)) disabled @endif
                                        class="w-full py-2 @if(blank($publishAt)) bg-gray-300 dark:bg-slate-700 text-gray-500 dark:text-slate-500 cursor-not-allowed @else bg-amber-600 hover:bg-amber-500 text-white @endif disabled:bg-gray-200 dark:disabled:bg-slate-700 disabled:text-gray-400 dark:disabled:text-slate-500 text-sm font-bold rounded-lg transition-all duration-200 flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 @if(!blank($publishAt)) text-amber-200 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span wire:loading.remove wire:target="confirmPublish">
                                        @if(blank($publishAt))
                                            Programar lección
                                        @else
                                            Programar lección
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="confirmPublish">Programando…</span>
                                </button>
                                @if(blank($publishAt))
                                    <p class="text-[10px] text-amber-400 text-center -mt-1">
                                        Establece una fecha de programación primero
                                    </p>
                                @endif
                            @endif
```

- [ ] **Step 2: Update the contextual message in the status panel**

Find the message paragraph inside the "Estados de publicación" amber box (around line 3658-3666):

```html
                                {{-- Mensaje contextual según acción --}}
                                <p class="text-[11px] text-amber-400/70 leading-relaxed">
                                    @if(blank($publishAt))
                                        Al publicar <strong class="text-amber-300">sin fecha</strong>, la lección será <strong class="text-emerald-400">visible para los estudiantes</strong> inmediatamente después de guardar.
                                    @else
                                        Al programar, la lección se publicará <strong class="text-cyan-400">automáticamente el {{ \Carbon\Carbon::parse($publishAt)->format('d/m/Y H:i') }}</strong>. Hasta entonces solo tú puedes verla.
                                    @endif
                                    Si aún no está lista, usa el botón flotante <strong class="text-blue-400">Guardar</strong> para mantenerla en borrador.
                                </p>
```

Replace with:

```html
                                {{-- Mensaje contextual según acción --}}
                                <p class="text-[11px] text-amber-400/70 leading-relaxed">
                                    @auth
                                        @if(auth()->user()->isPlanner)
                                            {{-- Mensaje para Planner/Admin --}}
                                            @if(blank($publishAt))
                                                Al publicar <strong class="text-amber-300">sin fecha</strong>, la lección será <strong class="text-emerald-400">visible para los estudiantes</strong> inmediatamente.
                                            @else
                                                Al publicar, la lección será <strong class="text-emerald-400">visible para los estudiantes</strong> y la programación planificada se aplicará el <strong class="text-cyan-400">{{ \Carbon\Carbon::parse($publishAt)->format('d/m/Y H:i') }}</strong>.
                                            @endif
                                        @else
                                            {{-- Mensaje para Profesor --}}
                                            @if(blank($publishAt))
                                                Al programar con una fecha, la lección quedará <strong class="text-amber-300">pendiente de aprobación</strong> por Planificación.
                                            @else
                                                La lección se programará para el <strong class="text-cyan-400">{{ \Carbon\Carbon::parse($publishAt)->format('d/m/Y H:i') }}</strong> y quedará <strong class="text-amber-300">pendiente de aprobación</strong> por Planificación.
                                            @endif
                                        @endif
                                    @endauth
                                    Si aún no está lista, usa el botón flotante <strong class="text-blue-400">Guardar</strong> para mantenerla en borrador.
                                </p>
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/livewire/profesor/lms/lesson-wizard.blade.php
git commit -m "feat: conditional publish button by role in lesson wizard"
```

---

### Task 5: LmsMonitor Blade — badge "Nueva" para lecciones programadas recientemente

**Files:**
- Modify: `resources/views/livewire/planning/lms/monitor.blade.php`
  - Add `🆕 Nueva` badge next to "Programado" status when publication was created within the last 48 hours

- [ ] **Step 1: Add the "Nueva" badge to the SCHEDULED status span**

Find the status span for `SCHEDULED` (around line 225):

```html
                            @if($pubStatus === 'SCHEDULED' && $pub->lmsPublication?->publish_at)
                                <span class="block text-[10px] text-amber-600/70 dark:text-amber-500/70 mt-0.5">
                                    {{ $pub->lmsPublication->publish_at->format('d/m/Y H:i') }}
                                </span>
                            @endif
```

Replace with:

```html
                            @if($pubStatus === 'SCHEDULED' && $pub->lmsPublication?->publish_at)
                                <span class="block text-[10px] text-amber-600/70 dark:text-amber-500/70 mt-0.5">
                                    {{ $pub->lmsPublication->publish_at->format('d/m/Y H:i') }}
                                    @if($pub->lmsPublication->created_at && $pub->lmsPublication->created_at->gt(now()->subHours(48)))
                                        <span class="inline-block ml-1 px-1.5 py-0.5 rounded text-[9px] font-bold bg-sky-500/15 text-sky-400 border border-sky-500/25">🆕 Nueva</span>
                                    @endif
                                </span>
                            @endif
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/livewire/planning/lms/monitor.blade.php
git commit -m "feat: add 'Nueva' badge for recently scheduled lessons in monitor"
```

---

### Task 6: Verificación final

**Files:** No file changes — only verification commands.

- [ ] **Step 1: Run all tests to ensure nothing broke**

```bash
php8.2 artisan test --stop-on-failure
```

Expected: All tests pass (no failures)

- [ ] **Step 2: Verify syntax of all modified files**

```bash
php8.2 -l app/Livewire/Profesor/Lms/LessonWizard.php
php8.2 -l app/Notifications/LessonScheduledForApproval.php
```

Expected: `No syntax errors detected`

- [ ] **Step 3: Verify migration ran**

```bash
php8.2 artisan migrate:status | grep notifications
```

Expected: `[OK] notifications` with a `Y` in the Ran column

- [ ] **Step 4: Final commit with plan and spec**

```bash
git add docs/superpowers/plans/2026-07-23-lms-publication-role-separation.md
git commit -m "docs: add implementation plan for LMS role separation"
```
