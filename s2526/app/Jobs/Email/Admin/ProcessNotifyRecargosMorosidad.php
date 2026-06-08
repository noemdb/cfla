<?php

namespace App\Jobs\Email\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SendPulseService;
use App\Mail\Queue\RecargosMorosidad\EmailForQueuing; // Crearemos esta clase

class ProcessNotifyRecargosMorosidad implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dataEmail;
    public $tries = 5;
    public $timeout = 20;
    public $backoff = 3;

    public function __construct($dataEmail)
    {
        $this->dataEmail = $dataEmail;
    }

    public function handle(SendPulseService $sendPulseService)
    {
        $email = new EmailForQueuing($this->dataEmail);
        $htmlContent = $email->render();

        $response = $sendPulseService->send(
            $this->dataEmail['address'],
            $this->dataEmail['subject'],
            $htmlContent,
            null,
            false
        );

        if (!$response['success']) {
            throw new \Exception('Error sending email: ' . $response['message']);
        }
    }
}