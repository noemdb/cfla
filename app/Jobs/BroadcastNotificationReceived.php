<?php

namespace App\Jobs;

use App\Events\NotificationReceived;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Re-emisión diferida del broadcast `NotificationReceived` cuando el push
 * inmediato falló (Reverb caído). Replica el patrón de respaldo de
 * BroadcastLessonScheduled (LmsPublicationService, Opción 9): si Reverb
 * vuelve en 10/60/300 s, el worker re-emite y la campana se actualiza al
 * instante en lugar de esperar el poll del dropdown.
 *
 * La notificación DB ya está persistida en el request original; este job
 * solo re-envía el broadcast con el mismo id de fila y payload.
 */
class BroadcastNotificationReceived implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [10, 60, 300];

    public function __construct(
        public string $notificationId,
        public array $payload,
        public int $userId,
    ) {}

    /**
     * Si Reverb sigue caído, el dispatch lanza y el worker reintenta con
     * backoff (10, 60, 300 s). El poll de la campana cubre mientras tanto.
     */
    public function handle(): void
    {
        NotificationReceived::dispatch($this->notificationId, $this->payload, $this->userId);
    }
}
