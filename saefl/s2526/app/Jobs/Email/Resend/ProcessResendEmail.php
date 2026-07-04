<?php

namespace App\Jobs\Email\Resend;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SendPulseService;

class ProcessResendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dataEmail;
    public $tries = 5;
    public $timeout = 20;
    public $backoff = 3;

    /**
     * Create a new job instance.
     *
     * @param array $dataEmail
     * @return void
     */
    public function __construct(array $dataEmail)
    {
        $this->dataEmail = $dataEmail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SendPulseService $sendPulseService)
    {
        $html = $this->dataEmail['html'] ?? null;
        if (!$html && isset($this->dataEmail['view'])) {
            $html = view($this->dataEmail['view'], $this->dataEmail)->render();
        }

        $sendPulseService->send(
            $this->dataEmail['address'],
            $this->dataEmail['subject'],
            $html,
            null,
            false, // es necesario que sea false
            $this->dataEmail['mailCCAddress'] ?? $this->dataEmail['cc'] ?? null,
            $this->dataEmail['bcc'] ?? null
        );
    }
}

// $resendService->send(
//     $this->dataEmail['address'],
//     $this->dataEmail['subject'],
//     $this->dataEmail['html'],
//     null,
//     true,
//     $this->dataEmail['cc'] ?? null,
//     $this->dataEmail['bcc'] ?? null
// );
