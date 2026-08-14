<?php

namespace App\Jobs;

use App\Events\Lms\LessonScheduled;
use App\Models\app\Academy\Activity;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BroadcastLessonScheduled implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [10, 60, 300];

    /**
     * @param  User[]  $recipients  Usuarios responsables (planner/admin/leadership/coordinación).
     */
    public function __construct(
        public Activity $activity,
        public array $recipients,
        public string $teacherName,
        public string $scheduledFor,
        public ?int $eventId = null,
    ) {}

    /**
     * Re-emite el broadcast a Reverb. Si Reverb sigue caído, el evento lanza
     * una excepción y el worker reintenta con backoff (10, 60, 300 s).
     * La notificación DB ya está persistida y el poll (5 s) cubre el badge.
     */
    public function handle(): void
    {
        LessonScheduled::dispatch($this->activity, $this->recipients, $this->teacherName, $this->scheduledFor, $this->eventId);
    }
}
