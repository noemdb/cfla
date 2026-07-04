<?php

namespace App\Jobs;

use App\Services\SendWhatsappMessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsappNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $to;
    protected string $template;
    protected array  $params;
    protected ?string $mediaId;

    public function __construct(string $to, string $template, array $params, ?string $mediaId = null)
    {
        $this->to       = $to;
        $this->template = $template;
        $this->params   = $params;
        $this->mediaId  = $mediaId;
    }

    public function handle(SendWhatsappMessageService $service): void
    {
        // simplemente llamamos al service para hacer el envío sin habilitar el queue (false)
        $service->sendDynamicTemplate(
            $this->to,
            $this->template,
            $this->params,
            $this->mediaId,
            'es_ES',
            false
        );
    }
}
