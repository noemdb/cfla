<?php

namespace App\Observers;

use App\Models\app\Academy\Activity;
use App\Notifications\ActivityApprovedNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityObserver
{
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
