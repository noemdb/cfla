<?php

namespace App\Observers;

use App\Models\app\Academy\Lms\LmsActivityPublication;
use App\Models\User;
use App\Notifications\LmsActivityPublicationNotification;
use App\Services\ActivityAudienceResolver;
use App\Services\Lms\BroadcastAudit;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Avisos del ciclo de publicación de una lección, con el mismo destinatario y
 * el mismo flujo que `ActivityObserver` (jefatura del área, administración y
 * planificación, coordinación en ámbito y el profesor de la pevaluacion).
 *
 * Solo se observan DOS eventos, por diseño:
 *
 * 1. La publicación queda en `status = PUBLISHED`, tanto si se crea ya
 *    publicada (primera publicación de la lección, que `LmsPublicationService`
 *    hace con `updateOrCreate`) como si pasa de DRAFT/SCHEDULED a PUBLISHED.
 *    Es el momento en que la lección se hace visible para el alumnado, así que
 *    es cuando al profesor y a los responsables les interesa enterarse. Los
 *    estados intermedios (DRAFT, SCHEDULED) no avisan: cuando un profesor
 *    PROGRAMA una lección, `LmsPublicationService` ya notifica por su cuenta
 *    (`LessonScheduledForApproval`).
 * 2. `deleted` cuando se elimina el registro de publicación.
 *
 * Sin usuario autenticado (seeders, tinker, factories) no se emite nada.
 */
class LmsActivityPublicationObserver
{
    /**
     * Ventana anti-spam: si el destinatario ya tiene sin leer un aviso de este
     * tipo sobre la misma pevaluacion dentro de la ventana, se omite el nuevo.
     */
    public const DEDUPE_HOURS = NotificationService::DEDUPE_HOURS;

    /**
     * Alta directa en PUBLISHED: es la primera publicación de la lección, así
     * que también avisa (mismo evento que la transición).
     */
    public function created(LmsActivityPublication $publication): void
    {
        if ($publication->status !== 'PUBLISHED') {
            return;
        }

        $this->notify($publication, 'publicó', 'lms_activity_published', 'lms.publication.published');
    }

    public function updated(LmsActivityPublication $publication): void
    {
        if (! $publication->wasChanged('status')) {
            return;
        }

        // Solo la transición a PUBLISHED (no un update que ya venía publicado).
        if ($publication->status !== 'PUBLISHED' || $publication->getOriginal('status') === 'PUBLISHED') {
            return;
        }

        $this->notify($publication, 'publicó', 'lms_activity_published', 'lms.publication.published');
    }

    public function deleted(LmsActivityPublication $publication): void
    {
        $this->notify($publication, 'eliminó la publicación de', 'lms_publication_deleted', 'lms.publication.deleted');
    }

    private function notify(LmsActivityPublication $publication, string $action, string $type, string $auditEvent): void
    {
        $actor = Auth::user();

        // Sin usuario autenticado no se emite (seeders, consola, factories).
        if (! $actor) {
            return;
        }

        $activity = $publication->activity;

        if (! $activity) {
            return;
        }

        try {
            $activity->loadMissing(
                'pevaluacion.pensum.asignatura',
                'pevaluacion.pensum.grado',
                'pevaluacion.seccion',
                'pevaluacion.profesor'
            );
        } catch (\Throwable $e) {
            Log::warning('LmsActivityPublicationObserver: no se pudieron cargar las relaciones', [
                'publication_id' => $publication->id,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $pevaluacion = $activity->pevaluacion;

        // Mismo público que el aviso de actividad (más el profesor, que aquí
        // no es el autor: quien publica es un responsable).
        $users = app(ActivityAudienceResolver::class)->forActivity($activity, actor: $actor);

        if ($users->isEmpty()) {
            return;
        }

        $subject = $pevaluacion ? ['pevaluacion_id' => (int) $pevaluacion->id] : [];
        $users = app(NotificationService::class)
            ->filterByDedupe($users, $type, $subject, self::DEDUPE_HOURS);

        if ($users->isEmpty()) {
            return;
        }

        $asignatura = $pevaluacion?->pensum?->asignatura;
        $grado = $pevaluacion?->pensum?->grado;
        $seccion = $pevaluacion?->seccion;

        $context = collect([
            $asignatura?->name ?? 'la asignatura',
            $grado?->name,
            $seccion?->name,
        ])->filter()->implode(' · ');

        $topic = $activity->topic ? ' «'.$activity->topic.'»' : '';
        $message = $type === 'lms_activity_published'
            ? 'Se publicó la lección'.$topic.' en '.$context.' por '.$this->actorLabel($actor).'.'
            : 'Se eliminó la publicación de la lección'.$topic.' en '.$context.' por '.$this->actorLabel($actor).'.';

        try {
            app(NotificationService::class)->notifyUsers(
                $users,
                new LmsActivityPublicationNotification(
                    type: $type,
                    message: $message,
                    // URL neutra: el destino por rol lo decide
                    // NotificationTargetResolver (previsualización, editor o
                    // listado según el rol del destinatario).
                    url: route('app.notifications.index'),
                    publicationId: (int) $publication->id,
                    activityId: (int) $activity->id,
                    pevaluacionId: $pevaluacion ? (int) $pevaluacion->id : null,
                    asignaturaName: $asignatura?->name,
                    gradoName: $grado?->name,
                    seccionName: $seccion?->name,
                    actorName: $actor->username,
                    actorRole: $this->actorLabel($actor),
                    publishedAt: $publication->published_at?->toIso8601String(),
                    action: $action,
                ),
                // Huella de idempotencia: ESTA publicación + esta acción. Un
                // reintento no duplica el aviso; publicar otra lección, sí.
                ['publication_id' => (int) $publication->id, 'action' => $type]
            );
        } catch (\Throwable $e) {
            Log::warning('LmsActivityPublicationObserver: fallo al notificar', [
                'publication_id' => $publication->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        try {
            app(BroadcastAudit::class)->log(
                event: $auditEvent,
                subject: $publication,
                actorUserId: (int) $actor->id,
                recipientIds: $users->pluck('id')->all(),
            );
        } catch (\Throwable $e) {
            Log::warning('LmsActivityPublicationObserver: fallo al auditar', [
                'publication_id' => $publication->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function actorLabel(User $user): string
    {
        foreach ([
            'is_admin' => 'Administración',
            'is_planner' => 'Planificación',
            'is_coordinacion' => 'Coordinación',
            'is_leadership' => 'Jefatura de Área',
            'is_director' => 'Dirección',
        ] as $flag => $label) {
            if (! empty($user->getAttributes()[$flag])) {
                return $label;
            }
        }

        return $user->username ?? 'el profesor';
    }
}
