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

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ident;
    protected $phone;
    protected $template; // default
    protected $general; // default
    public $tries = 5;
    public $timeout = 20;
    public $backoff = 3;

    /**
     * Create a new job instance.
     */
    public function __construct($ident, $phone, $template = 'notice_collection', $general = null)
    {
        $this->ident = $ident;
        $this->phone = $phone;
        $this->template = $template;
        $this->general = $general;
    }

    /**
     * Execute the job.
     */
    public function handle(FacebookTokenService $tokenService)
    {
        // Usa el controlador para enviar el mensaje
        $whatsappController = new WhatsAppApiController();
        switch ($this->template) {
            case 'notice_collection':
                $whatsappController->sendMessage($tokenService, $this->ident, $this->phone, $this->template);
                break;
            case 'notication_academic':
                $whatsappController->sendMessageAcademic($tokenService, $this->ident, $this->phone, $this->template);
                break;
            case 'general':
                $whatsappController->sendMessageGeneral($tokenService, $this->ident, $this->phone, $this->template, $this->general);
                break;
        }

        /*
        [
            'general'=>'Mensaje General',
            'notice_collection'=>'Notificación de Cobro',
            'notication_academic'=>'Notificación de Académica',
            'notification_agree'=>'Notificación de Acuerdo',
            'coexistence_notifications'=>'Notificación de Convivencia',
        ];
        */
    }
}
