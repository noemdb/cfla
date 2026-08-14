<?php

namespace App\Services\Lms;

use App\Events\Lms\LessonScheduled;
use App\Jobs\BroadcastLessonScheduled;
use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\User;
use App\Notifications\LessonScheduledForApproval;
use App\Services\Leadership\LeadershipService;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class LmsPublicationService
{
    /**
     * Prefijo de caché del contador de SCHEDULED pendientes por usuario
     * (badge de navbar). Se invalida por destinatario al notificar, para que
     * el badge reaccione al instante cuando llega el broadcast.
     */
    public const PENDING_COUNT_CACHE_PREFIX = 'lms_pending_count_';

    /**
     * Clave de caché del grid de stats del monitor (MonitorStats). Se invalida
     * en el punto central (publish/unpublish) y expira con el poll interval.
     */
    public const MONITOR_STATS_CACHE_KEY = 'lms_monitor_stats';

    /**
     * TTL de caché (segundos) alineado con la cadencia de `wire:poll`
     * (config('broadcasting.poll_interval'), default 5000ms).
     */
    public static function cacheTtlSeconds(): int
    {
        return max(1, (int) ceil((int) config('broadcasting.poll_interval', 5000) / 1000));
    }

    /**
     * Publica o programa la lección de una actividad.
     *
     * Solo una publicación autorizada (Jefe de Área, Coordinación o
     * Planificación) marca la lección como PUBLISHED. Si `publish_at` es
     * futura, el estudiante la ve en VISTA PREVIA (solo la 1ª sección) vía
     * `studentVisibility()`, y pasa a visible completa cuando llega la fecha.
     * No existe auto-publicación por cron: la acción manual de un responsable
     * es la que publica.
     *
     * Una publicación NO autorizada (profesor) queda SCHEDULED y no se activa
     * sola: debe publicarla un responsable (Jefe de Área, Coordinación o
     * Planificación).
     */
    public function publish(Activity $activity, array $data, int $publisherId, bool $authorized = false): LmsActivityPublication
    {
        // publish_at nunca queda nulo: si no llega fecha, se usa now()
        // (publicación inmediata). El input llega como string (datetime-local)
        // o puede faltar.
        $publishAt = $data['publish_at'] ?? now();
        if (! $publishAt instanceof \Carbon\CarbonInterface) {
            $publishAt = $publishAt ? \Carbon\Carbon::parse($publishAt) : now();
        }

        $status = $authorized ? 'PUBLISHED' : 'SCHEDULED';

        $pub = LmsActivityPublication::updateOrCreate(
            ['activity_id' => $activity->id],
            [
                'published_by' => $publisherId,
                'status' => $status,
                'publish_at' => $publishAt,
                'unpublish_at' => $data['unpublish_at'] ?? null,
                'published_at' => $authorized ? now() : null,
                'allow_comments' => $data['allow_comments'] ?? true,
                'allow_downloads' => $data['allow_downloads'] ?? true,
                'notes' => $data['notes'] ?? null,
            ]
        );

        LmsActivityLog::record($activity->id, $publisherId, $authorized ? 'PUBLISH' : 'SCHEDULE');

        // Los stats del monitor (PUBLISHED/SCHEDULED/DRAFT/…) cambiaron:
        // invalidar la caché del widget (Opción 5).
        Cache::forget(self::MONITOR_STATS_CACHE_KEY);

        // Si es SCHEDULED (no autorizado), notificar a los responsables
        if (! $authorized) {
            $this->notifyScheduled($activity, $publisherId, $publishAt);
        }

        return $pub;
    }

    public function unpublish(Activity $activity, int $userId): void
    {
        $pub = LmsActivityPublication::where('activity_id', $activity->id)->first();
        if ($pub) {
            $pub->update(['status' => 'ARCHIVED']);
            LmsActivityLog::record($activity->id, $userId, 'UNPUBLISH');

            // Los stats del monitor cambiaron (SCHEDULED/PUBLISHED → ARCHIVED).
            Cache::forget(self::MONITOR_STATS_CACHE_KEY);
        }
    }

    /**
     * Obtiene los usuarios responsables (planners, admins, directors, leadership,
     * coordinación) que deben enterarse de la lección programada.
     *
     * Opción 2 (H3): los destinatarios se scopean por rol:
     * - Admin / Planner / Director: visión global (sin restricción de scope).
     * - Leadership: solo si la asignatura de la lección está en sus áreas asignadas.
     * - Coordinación: solo si el pestudio de la lección está en su scope de peducativos.
     */
    protected function getRecipients(Activity $activity): Collection
    {
        // Roles globales: ven todas las SCHEDULED sin restricción de scope.
        $global = User::query()
            ->where('is_admin', true)
            ->orWhere('is_planner', true)
            ->orWhere('is_director', true)
            ->get();

        $asignaturaId = $activity->pevaluacion?->pensum?->asignatura_id;

        // Leadership: solo si la asignatura de la lección está en sus áreas.
        $leadership = User::query()
            ->where('is_leadership', true)
            ->where('is_admin', false)
            ->where('is_planner', false)
            ->where('is_director', false)
            ->get()
            ->filter(function (User $u) use ($asignaturaId) {
                if (! $asignaturaId) {
                    return false;
                }

                return app(LeadershipService::class, ['user' => $u])
                    ->getAssignedAsignaturaIds()
                    ->contains($asignaturaId);
            });

        // Coordinación: solo si el pestudio de la lección está en su scope.
        $coordinacion = User::query()
            ->where('is_coordinacion', true)
            ->where('is_admin', false)
            ->where('is_planner', false)
            ->where('is_director', false)
            ->get()
            ->filter(function (User $u) use ($activity) {
                return app(CoordinacionScopeService::class, ['user' => $u])
                    ->pevaluacionIsInScope($activity->pevaluacion_id);
            });

        return $global
            ->concat($leadership)
            ->concat($coordinacion)
            ->unique('id')
            ->values();
    }

    /**
     * Query de actividades SCHEDULED visibles para un usuario según su rol y
     * scope (blueprint Opción 2). Reutilizada por el badge (LessonPendingCount)
     * y por el marcado de lectura al abrir el monitor (LmsMonitor), para que
     * ambos cuenten/marquen exactamente el mismo conjunto de lecciones.
     */
    public function scopedScheduledQuery(User $user): Builder
    {
        $query = Activity::query()
            ->whereHas('lmsPublication', fn ($q) => $q->where('status', 'SCHEDULED'));

        $this->applyRoleScope($query, $user);

        return $query;
    }

    /**
     * Restringe la query al scope del rol del usuario:
     * - Admin / Planner / Director: visión global (sin restricción).
     * - Coordinación: solo sus peducativos (CoordinacionScopeService).
     * - Leadership: solo sus áreas asignadas (LeadershipService).
     * - Usuario sin rol responsable: no ve nada (1 = 0).
     */
    private function applyRoleScope(Builder $query, User $user): void
    {
        // is_planner/is_director son accessors que ya incluyen a los admins.
        if ($user->is_planner || $user->is_director) {
            return;
        }

        $query->where(function ($q) use ($user) {
            $scoped = false;

            if ($user->isCoordinacion()) {
                $pestudioIds = app(CoordinacionScopeService::class, ['user' => $user])->getPestudioIds();
                $q->orWhereHas('pevaluacion.pensum', fn ($sq) => $sq->whereIn('pestudio_id', $pestudioIds));
                $scoped = true;
            }

            if ($user->isLeadership()) {
                $asignaturaIds = app(LeadershipService::class, ['user' => $user])->getAssignedAsignaturaIds();
                $q->orWhereHas('pevaluacion.pensum', fn ($sq) => $sq->whereIn('asignatura_id', $asignaturaIds));
                $scoped = true;
            }

            if (! $scoped) {
                $q->whereRaw('1 = 0');
            }
        });
    }

    /**
     * Notifica a los responsables cuando una lección queda SCHEDULED.
     * Emite broadcast (Reverb) + notificación DB.
     * La notificación DB se delega en NotificationService, que además emite el
     * broadcast optimista `NotificationReceived` para el dropdown del navbar
     * (blueprint/notifications) e invalida la caché de no-leídas (N6).
     * Crash-guard (Opción 9): ShouldBroadcastNow lanza excepción síncrona si Reverb
     * está caído; envolvemos el dispatch para no romper saveStep2 del profesor.
     * La notificación DB y el log ya están persistidos; el poll (5s) cubre el badge.
     */
    protected function notifyScheduled(Activity $activity, int $publisherId, ?string $scheduledAt = null): void
    {
        $recipients = $this->getRecipients($activity);

        // El contador del badge (SCHEDULED no-leídas) cambió para los
        // destinatarios: invalidar la caché por usuario en el mismo request
        // para que el refresh disparado por el broadcast lea datos frescos.
        foreach ($recipients as $recipient) {
            Cache::forget(self::PENDING_COUNT_CACHE_PREFIX.$recipient->id);
        }

        $scheduledFor = $scheduledAt
            ? \Carbon\Carbon::parse($scheduledAt)->format('d/m/Y H:i')
            : '—';

        // Notificación en base de datos (siempre persistida) + broadcast
        // optimista por destinatario (NotificationReceived). El punto central
        // (NotificationService) invalida la caché de no-leídas del dropdown.
        app(NotificationService::class)->notifyUsers($recipients, new LessonScheduledForApproval(
            activityId: $activity->id,
            teacherName: \App\Models\User::find($publisherId)?->fullName ?? 'Profesor',
            activityTitle: $activity->topic ?? 'Lección',
            scheduledAt: $scheduledFor,
        ));

        // Notificación en tiempo real (Laravel Reverb) — crash-guard (Opción 9)
        // Auditoría (Opción 10): una fila por evento emitido en el punto central.
        $audit = app(BroadcastAudit::class)->log(
            event: 'lesson.scheduled',
            subject: $activity,
            actorUserId: $publisherId,
            recipientIds: $recipients->pluck('id')->all(),
        );
        try {
            LessonScheduled::dispatch(
                $activity,
                $recipients->all(),
                \App\Models\User::find($publisherId)?->fullName ?? 'Profesor',
                $scheduledFor,
                $audit->id,
            );
        } catch (\Throwable $e) {
            // Log para observabilidad, no rompemos el request del profesor.
            \Illuminate\Support\Facades\Log::warning('Broadcast LessonScheduled falló (Reverb caído), cubre poll fallback', [
                'activity_id' => $activity->id,
                'error' => $e->getMessage(),
            ]);

            // Job de respaldo con reintentos/backoff: si Reverb vuelve en
            // 10/60/300 s, el worker re-emite el broadcast. La notificación DB
            // ya está persistida y el poll (5 s) cubre el badge mientras tanto.
            // Se propaga el event_id para que la re-emisión conserve el ACK de
            // auditoría (Opción 10) de la fila broadcast_events original.
            BroadcastLessonScheduled::dispatch(
                $activity,
                $recipients->all(),
                \App\Models\User::find($publisherId)?->fullName ?? 'Profesor',
                $scheduledFor,
                $audit->id,
            );
        }
    }
}
