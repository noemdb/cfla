<?php

namespace App\Jobs\Queue\Meta;

use App\Http\Controllers\Integration\WhatsAppApiController;
use App\Services\FacebookTokenService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWhatsAppMessageFlyerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ident;
    protected $phone;
    protected $template;
    public $tries = 5;
    public $timeout = 20;
    public $backoff = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($ident, $phone, $template)
    {
        $this->ident = $ident;
        $this->phone = $phone;
        $this->template = $template;
    }

    /**
     * Execute the job.
     */
    public function handle(FacebookTokenService $tokenService)
    {
        // Usa el controlador para enviar el mensaje
        $whatsappController = new WhatsAppApiController();
        $whatsappController->sendMessageFlyer($tokenService, $this->ident, $this->phone, $this->template);
    }
}