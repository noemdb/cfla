<?php

namespace App\Services\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityPublication;

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
                'published_by'    => $publisherId,
                'status'          => $status,
                'publish_at'      => $publishAt,
                'unpublish_at'    => $data['unpublish_at'] ?? null,
                'published_at'    => $authorized ? now() : null,
                'allow_comments'  => $data['allow_comments'] ?? true,
                'allow_downloads' => $data['allow_downloads'] ?? true,
                'notes'           => $data['notes'] ?? null,
            ]
        );

        LmsActivityLog::record($activity->id, $publisherId, $authorized ? 'PUBLISH' : 'SCHEDULE');

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
}
