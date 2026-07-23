# Separación de Roles en Publicación de Lecciones LMS

> **Fecha:** 2026-07-23
> **Contexto:** El profesor solo programa (establece fecha → SCHEDULED), el rol Planning publica (PUBLISHED) desde el monitor, con notificaciones database.

---

## 1. Resumen del cambio

| Rol | Qué puede hacer | Estado resultante | Acceso al botón "Publicar ahora" |
|-----|----------------|-------------------|----------------------------------|
| **Profesor** (`is_profesor`) | Programar (establecer fecha) | `SCHEDULED` | ❌ No disponible sin fecha |
| **Planning/Admin** (`is_planner`/`is_admin`) | Publicar inmediatamente desde el wizard | `PUBLISHED` | ✅ Mantiene comportamiento actual |

---

## 2. LessonWizard PHP (`app/Livewire/Profesor/Lms/LessonWizard.php`)

### 2.1 Método `confirmPublish()` — nueva lógica

```
confirmPublish()
├── ¿Usuario es Planner/Admin?
│   └── Sí → comportamiento actual
│       ├── publishAt vacío → showPublishConfirm = true (modal "sin fecha")
│       └── publishAt con fecha → saveAndPublish()
│
└── No (es Profesor) →
    ├── publishAt vacío → mostrar error: "Debes establecer una fecha de programación"
    │   (no se abre el modal, se muestra notificación warning)
    └── publishAt con fecha → saveAndPublish() + notifyPlanningScheduled()
```

### 2.2 Nuevo método `notifyPlanningScheduled(int $activityId)`

```php
private function notifyPlanningScheduled(int $activityId): void
{
    $activity = Activity::find($activityId);
    $planners = User::query()
        ->where('is_planner', true)
        ->orWhere('is_admin', true)
        ->get();

    $scheduledDate = $this->publishAt
        ? Carbon::parse($this->publishAt)->format('d/m/Y H:i')
        : '—';

    Notification::send($planners, new LessonScheduledForApproval(
        activityId: $activityId,
        teacherName: auth()->user()->fullName,
        activityTitle: $activity->topic ?? 'Lección',
        scheduledAt: $scheduledDate,
    ));

    // También registrar en el log de auditoría
    LmsActivityLog::record($activityId, auth()->id(), 'SCHEDULE');
}
```

### 2.3 Nuevo helper `isCurrentUserPlanner()`

```php
public function isCurrentUserPlanner(): bool
{
    return auth()->user()->isPlanner; // getter: is_admin || is_planner
}
```

### 2.4 Propiedades

No se requieren nuevas propiedades. Las existentes son suficientes:
- `$publishAt` (string | null) — indica si el profesor programó una fecha
- `$allowDownloads` (bool) — se mantiene igual

---

## 3. LessonWizard Blade (`resources/views/.../lesson-wizard.blade.php`)

### 3.1 Botón "Publicar lección" — condicional por rol

**Profesor + publishAt vacío:**
```html
<button disabled
        title="Establece una fecha de programación primero"
        class="... disabled:bg-gray-200 dark:disabled:bg-slate-700 disabled:text-gray-400 dark:disabled:text-slate-500 ...">
    Programar lección
</button>
```

**Profesor + publishAt con fecha:**
```html
<button wire:click="confirmPublish"
        class="... bg-amber-600 hover:bg-amber-500 ...">
    <svg>⏰</svg>
    Programar lección
</button>
```

**Planner/Admin (cualquier publishAt):**
```html
<button wire:click="confirmPublish"
        class="... bg-emerald-600 hover:bg-emerald-500 ...">
    <svg>✓</svg>
    Publicar lección
</button>
```

### 3.2 Modal `showPublishConfirm` — condicional

El modal de confirmación "Sin fecha de publicación" solo se muestra si el usuario es Planner/Admin.
Para profesores, si `publishAt` está vacío y hacen clic, se muestra una notificación warning en lugar del modal.

### 3.3 Panel de estados — texto contextual

Cuando el profesor programa, añadir al final del mensaje informativo:
> *"Al programar, la lección quedará pendiente de aprobación por Planificación."*

---

## 4. Notificación: Migración

Nueva migración para la tabla `notifications` de Laravel (estándar):

```php
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('type');
    $table->morphs('notifiable');
    $table->text('data');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();
});
```

---

## 5. Notificación: Clase `LessonScheduledForApproval`

**Archivo:** `app/Notifications/LessonScheduledForApproval.php`

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

---

## 6. LmsMonitor — badge "Nueva" + filtro mejorado

### 6.1 Blade del monitor (`resources/views/livewire/planning/lms/monitor.blade.php`)

En la celda de estado, cuando `pubStatus === 'SCHEDULED'`:
- Si la publicación se creó hace menos de 48h → badge `🆕 Nueva` junto al badge "Programado"
- El badge usa estilo `bg-sky-500/15 text-sky-400 border border-sky-500/25`

### 6.2 LmsMonitor PHP

Sin cambios en la clase PHP. El cálculo "nueva" se hace en el blade comparando `lmsPublication->created_at` con `now()->subHours(48)`.

---

## 7. User model (`app/Models/User.php`)

Ya tiene:
- `Notifiable` trait ✓
- `is_planner` en `$fillable` y `$casts` ✓
- Getter `getIsPlannerAttribute()` que incluye `is_admin` ✓

No requiere cambios.

---

## 8. Lo que NO cambia

| Archivo | Razón |
|---------|-------|
| `app/Services/Lms/LmsPublicationService.php` | Ya soporta SCHEDULED / PUBLISHED según `publish_at` |
| `app/Livewire/Planning/Lms/LmsMonitor.php` | Ya tiene `publish()`, `openSchedule()`, filtros |
| `routes/web.php` | Sin cambios de rutas |
| `app/Models/User.php` | Ya tiene todo lo necesario |
| `app/Models/app/Academy/Lms/LmsActivityLog.php` | Solo se usa para registrar evento SCHEDULE |
| Botón flotante "Guardar" del wizard | Se mantiene igual (guarda sin publicar) |
| Modal de schedule en el monitor | Ya existe y funciona |

---

## 9. Resumen de archivos afectados

| Archivo | Tipo de cambio |
|---------|---------------|
| `app/Livewire/Profesor/Lms/LessonWizard.php` | Modificar 1 método, añadir 2 métodos |
| `resources/views/livewire/profesor/lms/lesson-wizard.blade.php` | Botón condicional, textos |
| `app/Notifications/LessonScheduledForApproval.php` | **NUEVO** |
| `database/migrations/YYYY_MM_DD_HHMMSS_create_notifications_table.php` | **NUEVO** |
| `resources/views/livewire/planning/lms/monitor.blade.php` | Badge "Nueva" |
