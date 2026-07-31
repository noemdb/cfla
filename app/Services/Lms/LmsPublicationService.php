<?php

namespace App\Services\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityPublication;

class LmsPublicationService
{
    public function publish(Activity $activity, array $data, int $publisherId): LmsActivityPublication
    {
        // publish_at nunca queda nulo: si no llega fecha, se usa now()
        // (publicación inmediata). Una fecha futura queda como SCHEDULED
        // (visible en vista previa para los estudiantes) y se activa sola
        // cuando llega la fecha vía lms:publish-scheduled.
        // El input llega como string (datetime-local) o puede faltar.
        $publishAt = $data['publish_at'] ?? now();
        if (! $publishAt instanceof \Carbon\CarbonInterface) {
            $publishAt = $publishAt ? \Carbon\Carbon::parse($publishAt) : now();
        }

        $isFuture = $publishAt->gt(now());

        $pub = LmsActivityPublication::updateOrCreate(
            ['activity_id' => $activity->id],
            [
                'published_by'    => $publisherId,
                'status'          => $isFuture ? 'SCHEDULED' : 'PUBLISHED',
                'publish_at'      => $publishAt,
                'unpublish_at'    => $data['unpublish_at'] ?? null,
                'published_at'    => $isFuture ? null : now(),
                'allow_comments'  => $data['allow_comments'] ?? true,
                'allow_downloads' => $data['allow_downloads'] ?? true,
                'notes'           => $data['notes'] ?? null,
            ]
        );

        LmsActivityLog::record($activity->id, $publisherId, 'PUBLISH');

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

    public function activateScheduled(): int
    {
        return LmsActivityPublication::where('status', 'SCHEDULED')
            ->where('publish_at', '<=', now())
            ->update(['status' => 'PUBLISHED', 'published_at' => now()]);
    }
}
