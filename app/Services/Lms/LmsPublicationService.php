<?php

namespace App\Services\Lms;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Lms\LmsActivityLog;
use App\Models\app\Academy\Lms\LmsActivityPublication;

class LmsPublicationService
{
    public function publish(Activity $activity, array $data, int $publisherId): LmsActivityPublication
    {
        $pub = LmsActivityPublication::updateOrCreate(
            ['activity_id' => $activity->id],
            [
                'published_by'    => $publisherId,
                'status'          => isset($data['publish_at']) ? 'SCHEDULED' : 'PUBLISHED',
                'publish_at'      => $data['publish_at'] ?? null,
                'unpublish_at'    => $data['unpublish_at'] ?? null,
                'published_at'    => isset($data['publish_at']) ? null : now(),
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
