<?php

namespace App\Jobs\Email\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\SendPulseService;
use App\Mail\Queue\SetExchangeRate\EmailForQueuing; // app/Mail/Queue/SetExchangeRate/EmailForQueuing.php

class ProcessNotifySetExchangeRate implements ShouldQueue
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
        $this->dataEmail = $dataEmail;
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
