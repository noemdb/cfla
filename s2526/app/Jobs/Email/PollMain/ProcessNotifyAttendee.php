<?php

namespace App\Jobs\Email\PollMain;

use App\Mail\Queue\PollMain\EmailForQueuingSend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;
use App\Services\SendPulseService;


class ProcessNotifyAttendee implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dataEmail;
    public $tries = 5;
    // public $timeout = 20;
    public $backoff = 3;
    public $failOnTimeout = false;
    public $timeout = 120000;

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
        $email = new EmailForQueuingSend($this->dataEmail); 
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
