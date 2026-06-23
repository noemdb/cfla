<?php

namespace App\Jobs\Email;

use App\Mail\Queue\Poll\EmailForQueuing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Services\SendPulseService;



class ProcessNotifyPoll implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dataEmail;
    public $tries = 5;
    public $timeout = 20;
    public $backoff = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataEmail)
    {
        $this->dataEmail = $dataEmail; //dd($this->dataEmail);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SendPulseService $sendPulseService)
    {
        $email = new EmailForQueuing($this->dataEmail); 
        $htmlContent = $email->render();

        $sendPulseService->send(
            $this->dataEmail['address'],
            $this->dataEmail['subject'],
            $htmlContent,
            null,
            false,
            $this->dataEmail['mailCCAddress'] ?? null
        );
    }

}
