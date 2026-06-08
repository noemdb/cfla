<?php

namespace App\Jobs\Email\Catchment;

use Illuminate\Bus\Queueable;
use App\Mail\Queue\Catchment\EmailForQueuingAttendee;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Mail;

class ProcessNotifyAttendee implements ShouldQueue
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
    public function handle()
    {
        $email = new EmailForQueuingAttendee($this->dataEmail); //dd($email);
        Mail::to($this->dataEmail['address'])->cc($this->dataEmail['mailCCAddress'])->send($email);
    }
}
