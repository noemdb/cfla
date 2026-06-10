<?php

namespace App\Console\Commands;

use App\Events\Lms\ScheduledPublicationsReady;
use App\Services\Lms\LmsPublicationService;
use Illuminate\Console\Command;

class PublishScheduledLmsContent extends Command
{
    protected $signature = 'lms:publish-scheduled';
    protected $description = 'Activa publicaciones LMS programadas cuya fecha ya llegó';

    public function handle(LmsPublicationService $service): int
    {
        $count = $service->activateScheduled();

        if ($count > 0) {
            $this->info("{$count} publicación(es) activada(s).");
            ScheduledPublicationsReady::dispatch($count);
        } else {
            $this->info('No hay publicaciones pendientes.');
        }

        return Command::SUCCESS;
    }
}
