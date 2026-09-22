<?php

namespace App\Console\Commands;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\AreaConocimiento;
use App\Models\User;
use App\Notifications\PendingApprovalReminderNotification;
use App\Notifications\ScheduledLessonsReminderNotification;
use App\Notifications\StaleActivitiesReminderNotification;
use App\Services\Lms\CoordinacionScopeService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

/**
 * Recordatorios diarios de planificación (programado a las 06:00, ver
 * Console\Kernel). Agrupa cinco reglas, cada una como un resumen por usuario
 * y rol (nunca una notificación por actividad):
 *
 *  1. Jefe de Área (is_leadership, scoped a sus áreas): actividades sin
 *     aprobar (status=false/null) con finicial = hoy+3.
 *  2. Coordinación (is_coordinacion, scoped a sus peducativos): mismas
 *     actividades sin aprobar con finicial = hoy+3.
 *  3. Planificación (is_planner, global): mismas actividades sin aprobar con
 *     finicial = hoy+3.
 *  4. Jefe de Área: 5 o más lecciones programadas (publicación LMS SCHEDULED)
 *     con publish_at = hoy+3.
 *  5. Jefe de Área: más de 5 días desde la última actividad registrada/
 *     actualizada (updated_at) en sus áreas.
 *
 * Cadena de asociación con el Jefe de Área:
 *   Activity → Pevaluacion → Pensum → Asignatura → CampoConocimiento →
 *   AreaConocimiento.leader_id.
 *
 * Toda notificación sale por NotificationService (regla de oro del blueprint/
 * notifications): persiste en `notifications` y emite el broadcast Reverb
 * `NotificationReceived` para la campana del navbar. Sin canal mail.
 *
 * Uso:
 *   php8.2 artisan planning:daily-reminders
 *   php8.2 artisan planning:daily-reminders --date=2026-09-24   # simula el objetivo
 *   php8.2 artisan planning:daily-reminders --dry-run           # no persiste
 */
class PlanningDailyReminders extends Command
{
    /** Días de antelación de la fecha objetivo (hoy + 3). */
    public const LEAD_DAYS = 3;

    /** Mínimo de lecciones programadas para la alerta de carga (regla 4). */
    public const MIN_SCHEDULED_LESSONS = 5;

    /** Días de inactividad que disparan la alerta de área (regla 5). */
    public const STALE_DAYS = 5;

    protected $signature = 'planning:daily-reminders
        {--date= : Fecha objetivo (Y-m-d) de las reglas "en 3 días" (default: hoy + 3)}
        {--dry-run : Calcula y muestra los envíos sin persistir notificaciones}';

    protected $description = 'Envía los recordatorios diarios de planificación (aprobaciones, lecciones programadas y áreas inactivas)';

    public function handle(): int
    {
        $target = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : now()->startOfDay()->addDays(self::LEAD_DAYS);

        $dry = (bool) $this->option('dry-run');

        $this->info('Recordatorios de planificación — objetivo: '.$target->toDateString().($dry ? ' [dry-run]' : ''));

        $sent = 0;
        $sent += $this->notifyLeadershipPendingApproval($target, $dry);
        $sent += $this->notifyCoordinacionPendingApproval($target, $dry);
        $sent += $this->notifyPlannerPendingApproval($target, $dry);
        $sent += $this->notifyLeadershipScheduledLessons($target, $dry);
        $sent += $this->notifyLeadershipStale($dry);

        $this->info("Total de notificaciones emitidas: {$sent}");

        return self::SUCCESS;
    }

    /**
     * Regla 1 — Jefe de Área: actividades sin aprobar con finicial = hoy+3 en
     * sus áreas (leader_id de la cadena CampoConocimiento → AreaConocimiento).
     */
    private function notifyLeadershipPendingApproval(Carbon $target, bool $dry): int
    {
        $activities = $this->unapproved()
            ->whereDate('finicial', $target->toDateString())
            ->with(['pevaluacion.pensum.asignatura.areasConocimiento'])
            ->get();

        // leader_id => [activity_id => Activity] (dedupe si el líder tiene dos
        // áreas que incluyen la misma asignatura).
        $byLeader = [];
        foreach ($activities as $activity) {
            $asignatura = $activity->pevaluacion?->pensum?->asignatura;

            foreach (($asignatura?->areasConocimiento ?? collect()) as $area) {
                if ($area->leader_id) {
                    $byLeader[$area->leader_id][$activity->id] = $activity;
                }
            }
        }

        if ($byLeader === []) {
            $this->line('  Jefes de Área (aprobaciones en 3 días): 0');

            return 0;
        }

        $date = $target->format('d/m/Y');
        $sent = 0;

        foreach ($this->activeUsers(array_keys($byLeader)) as $user) {
            $ids = array_map('intval', array_keys($byLeader[$user->id] ?? []));
            $n = count($ids);

            if ($n === 0) {
                continue;
            }

            $this->send($user, new PendingApprovalReminderNotification(
                type: 'pending_approval_leadership',
                message: "Tienes {$n} actividad(es) de tus áreas sin aprobar que inician el {$date} (en ".self::LEAD_DAYS.' días).',
                url: route('app.leadership.activities'),
                count: $n,
                activityIds: $ids,
            ), $dry);

            $sent++;
        }

        $this->line("  Jefes de Área (aprobaciones en 3 días): {$sent}");

        return $sent;
    }

    /**
     * Regla 2 — Coordinación: actividades sin aprobar con finicial = hoy+3 en
     * el scope de peducativos del coordinador.
     */
    private function notifyCoordinacionPendingApproval(Carbon $target, bool $dry): int
    {
        $date = $target->format('d/m/Y');
        $sent = 0;

        $users = User::query()
            ->where('is_coordinacion', true)
            ->where('is_active', 'enable')
            ->get();

        foreach ($users as $user) {
            $pestudioIds = app(CoordinacionScopeService::class, ['user' => $user])->getPestudioIds();

            if ($pestudioIds->isEmpty()) {
                continue;
            }

            $query = $this->unapproved()
                ->whereDate('finicial', $target->toDateString())
                ->whereHas('pevaluacion.pensum', fn ($q) => $q->whereIn('pestudio_id', $pestudioIds));

            $n = (clone $query)->count();

            if ($n === 0) {
                continue;
            }

            $this->send($user, new PendingApprovalReminderNotification(
                type: 'pending_approval_coordinacion',
                message: "Tienes {$n} actividad(es) sin aprobar en tu coordinación que inician el {$date} (en ".self::LEAD_DAYS.' días).',
                url: route('app.coordinacion.activities'),
                count: $n,
                activityIds: $query->pluck('id')->map(fn ($id) => (int) $id)->all(),
            ), $dry);

            $sent++;
        }

        $this->line("  Coordinación (aprobaciones en 3 días): {$sent}");

        return $sent;
    }

    /**
     * Regla 3 — Planificación: todas las actividades sin aprobar con
     * finicial = hoy+3 (visión global).
     */
    private function notifyPlannerPendingApproval(Carbon $target, bool $dry): int
    {
        $query = $this->unapproved()->whereDate('finicial', $target->toDateString());
        $n = (clone $query)->count();

        if ($n === 0) {
            $this->line('  Planificación (aprobaciones en 3 días): 0');

            return 0;
        }

        $ids = $query->pluck('id')->map(fn ($id) => (int) $id)->all();
        $date = $target->format('d/m/Y');
        $sent = 0;

        $users = User::query()
            ->where('is_planner', true)
            ->where('is_active', 'enable')
            ->get();

        foreach ($users as $user) {
            $this->send($user, new PendingApprovalReminderNotification(
                type: 'pending_approval_planner',
                message: "Hay {$n} actividad(es) sin aprobar que inician el {$date} (en ".self::LEAD_DAYS.' días).',
                url: route('app.planning.activities.index'),
                count: $n,
                activityIds: $ids,
            ), $dry);

            $sent++;
        }

        $this->line("  Planificación (aprobaciones en 3 días): {$sent}");

        return $sent;
    }

    /**
     * Regla 4 — Jefe de Área: 5 o más lecciones programadas (LMS SCHEDULED)
     * con publish_at = hoy+3 en sus áreas.
     */
    private function notifyLeadershipScheduledLessons(Carbon $target, bool $dry): int
    {
        $activities = Activity::query()
            ->whereHas('lmsPublication', fn ($q) => $q
                ->where('status', 'SCHEDULED')
                ->whereDate('publish_at', $target->toDateString()))
            ->with(['pevaluacion.pensum.asignatura.areasConocimiento'])
            ->get();

        $byLeader = [];
        foreach ($activities as $activity) {
            $asignatura = $activity->pevaluacion?->pensum?->asignatura;

            foreach (($asignatura?->areasConocimiento ?? collect()) as $area) {
                if ($area->leader_id) {
                    $byLeader[$area->leader_id][$activity->id] = $activity;
                }
            }
        }

        $date = $target->format('d/m/Y');
        $sent = 0;

        foreach ($this->activeUsers(array_keys($byLeader)) as $user) {
            $ids = array_map('intval', array_keys($byLeader[$user->id] ?? []));
            $n = count($ids);

            if ($n < self::MIN_SCHEDULED_LESSONS) {
                continue;
            }

            $this->send($user, new ScheduledLessonsReminderNotification(
                message: "Tienes {$n} lecciones programadas de tus áreas para el {$date} (en ".self::LEAD_DAYS.' días).',
                url: route('app.leadership.lessons'),
                count: $n,
                date: $target->toDateString(),
                activityIds: $ids,
            ), $dry);

            $sent++;
        }

        $this->line('  Jefes de Área ('.self::MIN_SCHEDULED_LESSONS.'+ lecciones programadas): '.$sent);

        return $sent;
    }

    /**
     * Regla 5 — Jefe de Área: más de 5 días desde la última actividad
     * registrada/actualizada (updated_at) en sus áreas.
     */
    private function notifyLeadershipStale(bool $dry): int
    {
        $leaderIds = AreaConocimiento::query()
            ->whereNotNull('leader_id')
            ->distinct()
            ->pluck('leader_id');

        $sent = 0;

        foreach ($this->activeUsers($leaderIds->all()) as $user) {
            $areaIds = AreaConocimiento::query()
                ->where('leader_id', $user->id)
                ->pluck('id');

            if ($areaIds->isEmpty()) {
                continue;
            }

            $last = Activity::query()
                ->whereHas('pevaluacion.pensum.asignatura.areasConocimiento', function ($q) use ($areaIds) {
                    $q->whereIn('area_conocimientos.id', $areaIds);
                })
                ->max('updated_at');

            // Sin actividades no hay "última": no se puede contar la inactividad.
            if (! $last) {
                continue;
            }

            $lastAt = Carbon::parse($last);
            $days = (int) floor($lastAt->diffInDays(now(), true));

            if ($days <= self::STALE_DAYS) {
                continue;
            }

            $this->send($user, new StaleActivitiesReminderNotification(
                message: "Han pasado {$days} días desde la última actividad registrada/actualizada en tus áreas.",
                url: route('app.leadership.activities'),
                daysSince: $days,
                lastActivityAt: $lastAt->toIso8601String(),
            ), $dry);

            $sent++;
        }

        $this->line('  Jefes de Área (áreas inactivas > '.self::STALE_DAYS.' días): '.$sent);

        return $sent;
    }

    /**
     * Actividades de planificación sin aprobar (status = false/null).
     */
    private function unapproved(): Builder
    {
        return Activity::query()
            ->where(fn ($q) => $q->where('status', false)->orWhereNull('status'));
    }

    /**
     * Usuarios activos a partir de una lista de IDs (líderes de área).
     *
     * @param  array<int, int|string>  $ids
     * @return Collection<int, User>
     */
    private function activeUsers(array $ids): Collection
    {
        if ($ids === []) {
            return collect();
        }

        return User::query()
            ->whereIn('id', $ids)
            ->where('is_active', 'enable')
            ->get();
    }

    /**
     * Emite la notificación por el punto central (DB + broadcast Reverb) o la
     * muestra en modo simulación.
     */
    private function send(User $user, Notification $notification, bool $dry): void
    {
        if ($dry) {
            $this->line("    [dry-run] #{$user->id} {$user->username}");

            return;
        }

        app(NotificationService::class)->notifyUsers([$user], $notification);
    }
}
