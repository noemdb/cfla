<?php

namespace App\Observers;

use App\Models\app\Academy\Activity;
use App\Models\User;
use App\Notifications\ActivityApprovedNotification;
use App\Notifications\ActivityCreatedNotification;
use App\Services\ActivityAudienceResolver;
use App\Services\Lms\BroadcastAudit;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityObserver
{
    /**
     * Ventana anti-spam (ítem 7): si el destinatario ya tiene una
     * `activity_created` sin leer para la misma pevaluacion dentro de esta
     * ventana, se omite la nueva (un aviso por pevaluacion/ventana basta en
     * picos de registro).
     */
    public const DEDUPE_HOURS = NotificationService::DEDUPE_HOURS;

    /**
     * Notifica al registrar una actividad nueva, sea cual sea la vía de
     * creación (wizard del profesor, clonado, módulo planning, etc.):
     * jefatura del área (leader_id activo y con rol), administración y
     * planificación (alcance global) y coordinación en cuyo ámbito cae la
     * pevaluacion. Los conjuntos se unen sin exigir rol "puro" para que los
     * usuarios multi-rol también reciban el aviso, y sin duplicados. El
     * creador queda fuera salvo que tenga un rol de supervisión: si registra
     * la actividad como profesor pero además es planificador, necesita el
     * aviso en esa capacidad. Sin emisión en contextos sin usuario
     * autenticado (seeders, consola, factories).
     */
    public function created(Activity $activity): void
    {
        $creatorId = Auth::id();

        if ($creatorId === null) {
            return;
        }

        try {
            $activity->loadMissing(
                'pevaluacion.pensum.asignatura',
                'pevaluacion.pensum.grado',
                'pevaluacion.seccion'
            );
        } catch (\Throwable $e) {
            Log::warning('ActivityObserver: no se pudo cargar relaciones para notificación de creación', [
                'activity_id' => $activity->id,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $pevaluacion = $activity->pevaluacion;

        // Destinatarios: jefatura del área, administración y planificación
        // (alcance global) y coordinación en cuyo ámbito cae la pevaluacion.
        //
        // La resolución vive en `ActivityAudienceResolver` porque la comparten
        // los observers de `Activity` y de `LmsActivityPublication`. Los
        // conjuntos se unen sin exigir rol "puro" (para que los usuarios
        // multi-rol también reciban el aviso) y el autor queda fuera salvo que
        // tenga un rol de supervisión.
        //
        // Aquí NO se avisa al profesor: es el autor del registro.
        $users = app(ActivityAudienceResolver::class)->forActivity(
            $activity,
            actor: Auth::user(),
            includeProfessor: false
        );

        if ($users->isEmpty()) {
            return;
        }

        // Anti-spam: si el destinatario ya tiene un `activity_created` sin leer
        // para la misma pevaluacion dentro de la ventana, se omite el nuevo
        // (un aviso por pevaluacion/ventana basta en picos de registro).
        $subject = $pevaluacion ? ['pevaluacion_id' => (int) $pevaluacion->id] : [];
        $users = app(NotificationService::class)
            ->filterByDedupe($users, 'activity_created', $subject, self::DEDUPE_HOURS);

        if ($users->isEmpty()) {
            return;
        }

        $asignatura = $pevaluacion?->pensum?->asignatura;
        $grado = $pevaluacion?->pensum?->grado;
        $seccion = $pevaluacion?->seccion;

        $message = 'Se registró una nueva actividad en '.($asignatura?->name ?? 'la asignatura')
            .($grado?->name ? ' · '.$grado->name : '')
            .($seccion?->name ? ' · '.$seccion->name : '').'.';

        try {
            // URL neutra (ítem 8): el destino por rol lo decide
            // NotificationTargetResolver al hacer clic.
            //
            // La huella de idempotencia es la ACTIVIDAD concreta, no la
            // pevaluacion: así un job reintentado no duplica este aviso, pero
            // registrar otra actividad en la misma pevaluacion sí avisa (eso
            // lo gobierna el anti-spam de no leídas, de ventana 24h).
            app(NotificationService::class)->notifyUsers(
                $users,
                new ActivityCreatedNotification(
                    type: 'activity_created',
                    message: $message,
                    url: route('app.notifications.index'),
                    activityId: (int) $activity->id,
                    pevaluacionId: $pevaluacion ? (int) $pevaluacion->id : null,
                    asignaturaName: $asignatura?->name,
                    gradoName: $grado?->name,
                    seccionName: $seccion?->name,
                ),
                ['activity_id' => (int) $activity->id]
            );
        } catch (\Throwable $e) {
            Log::warning('ActivityObserver: fallo al notificar creación de actividad', [
                'activity_id' => $activity->id,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        // Auditoría del evento (ítem 9, patrón Opción 10): no rompe nada.
        try {
            app(BroadcastAudit::class)->log(
                event: 'activity.created',
                subject: $activity,
                actorUserId: (int) $creatorId,
                recipientIds: $users->pluck('id')->all(),
            );
        } catch (\Throwable $e) {
            Log::warning('ActivityObserver: fallo al auditar creación de actividad', [
                'activity_id' => $activity->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notifica al profesor cuando su actividad es aprobada (status: 0/null → 1)
     * por un usuario con rol is_planner, is_coordinacion o is_leadership.
     */
    public function updated(Activity $activity): void
    {
        // Solo cuando status cambia a aprobado
        if (! $activity->wasChanged('status')) {
            return;
        }

        $original = $activity->getOriginal('status');
        $wasApproved = (bool) $original;
        $isApproved = (bool) $activity->status;

        // 0/null -> 1
        if ($wasApproved || ! $isApproved) {
            return;
        }

        $approver = Auth::user();

        // Si no hay usuario autenticado, no notificar (ej: seeders, tinker)
        if (! $approver) {
            return;
        }

        // Solo si el aprobador tiene rol planificador / coordinación / jefatura
        $hasRole = (bool) ($approver->is_planner ?? false)
            || (bool) ($approver->is_coordinacion ?? false)
            || (bool) ($approver->is_leadership ?? false)
            || (bool) ($approver->is_admin ?? false);

        if (! $hasRole) {
            return;
        }

        // Resolver profesor -> user
        try {
            $activity->loadMissing('pevaluacion.profesor.user', 'pevaluacion.pensum.asignatura');
        } catch (\Throwable $e) {
            Log::warning('ActivityObserver: no se pudo cargar relaciones para notificación de aprobación', [
                'activity_id' => $activity->id,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $profesor = $activity->pevaluacion?->profesor;
        $profesorUser = $profesor?->user;

        if (! $profesorUser) {
            return;
        }

        // No auto-notificar si el aprobador es el propio profesor (aunque por roles no debería pasar)
        if ((int) $approver->id === (int) $profesorUser->id) {
            return;
        }

        // Solo notificar a usuarios activos y con rol profesor (o equivalente)
        // Si el profesor no es usuario profesor activo, igual notificar pero filtrar por is_active
        if (($profesorUser->is_active ?? 'enable') !== 'enable') {
            return;
        }

        $topic = $activity->topic ?: 'actividad';
        $asignaturaName = $activity->pevaluacion?->pensum?->asignatura?->name;

        // Resolver nombre y rol del aprobador para el mensaje
        $approverName = $approver->full_name ?? $approver->username ?? 'Coordinación';
        $approverRole = $this->resolveApproverRoleLabel($approver);

        $message = 'Tu actividad "'.\Str::limit($topic, 60).'"'
            .($asignaturaName ? ' de '.$asignaturaName : '')
            .' fue aprobada'.($approverRole ? ' por '.$approverRole : '').'.';

        // URL para el profesor: listado de actividades donde puede ver la aprobación
        // app.profesors.activities.index muestra el selector de pevaluacion
        $url = route('app.profesors.activities.index');

        try {
            app(NotificationService::class)->notifyUsers(
                [$profesorUser],
                new ActivityApprovedNotification(
                    type: 'activity_approved',
                    message: $message,
                    url: $url,
                    activityId: (int) $activity->id,
                    topic: $topic,
                    asignaturaName: $asignaturaName,
                    approverName: $approverName,
                    approverRole: $approverRole,
                )
            );
        } catch (\Throwable $e) {
            Log::warning('ActivityObserver: fallo al notificar aprobación de actividad al profesor', [
                'activity_id' => $activity->id,
                'profesor_user_id' => $profesorUser->id,
                'approver_id' => $approver->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function resolveApproverRoleLabel($user): ?string
    {
        // Priorizar el rol aprobador explícito
        if (! empty($user->is_leadership)) {
            return 'Jefe de Área';
        }
        if (! empty($user->is_coordinacion)) {
            return 'Coordinación';
        }
        if (! empty($user->is_planner)) {
            return 'Planificación';
        }
        if (! empty($user->is_admin)) {
            return 'Administración';
        }

        return null;
    }
}
