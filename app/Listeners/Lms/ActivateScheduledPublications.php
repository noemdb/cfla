<?php

namespace App\Listeners\Lms;

use App\Events\Lms\ScheduledPublicationsReady;
use Illuminate\Support\Facades\Log;

class ActivateScheduledPublications
{
    public function handle(ScheduledPublicationsReady $event): void
    {
        Log::info('Publicaciones LMS activadas automaticamente', [
            'count' => $event->publicationsActivated,
        ]);
    }
}
