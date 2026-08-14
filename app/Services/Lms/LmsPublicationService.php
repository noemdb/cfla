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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification;

class LmsPublicationService
{
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
     * Notifica a los responsables cuando una lección queda SCHEDULED.
     * Emite broadcast (Reverb) + notificación DB.
     * Crash-guard (Opción 9): ShouldBroadcastNow lanza excepción síncrona si Reverb
     * está caído; envolvemos el dispatch para no romper saveStep2 del profesor.
     * La notificación DB y el log ya están persistidos; el poll (5s) cubre el badge.
     */
    protected function notifyScheduled(Activity $activity, int $publisherId, ?string $scheduledAt = null): void
    {
        $recipients = $this->getRecipients($activity);

        $scheduledFor = $scheduledAt
            ? \Carbon\Carbon::parse($scheduledAt)->format('d/m/Y H:i')
            : '—';

        // Notificación en base de datos (siempre persistida)
        Notification::send($recipients, new LessonScheduledForApproval(
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
            BroadcastLessonScheduled::dispatch(
                $activity,
                $recipients->all(),
                \App\Models\User::find($publisherId)?->fullName ?? 'Profesor',
                $scheduledFor,
            );
        }
    }
}
