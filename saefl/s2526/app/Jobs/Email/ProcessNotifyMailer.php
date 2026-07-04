<?php

namespace App\Jobs\Email;

use App\Mail\Queue\Mailer\EmailForQueuingJobSend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessNotifyMailer implements ShouldQueue
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
    public function handle()
    {
        $email = new EmailForQueuingJobSend($this->dataEmail);
        //Mail::to($this->dataEmail['address'])->cc($this->dataEmail['mailCCAddress'])->send($email);
        Mail::to($this->dataEmail['address'])->send($email);
    }
}
