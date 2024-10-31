<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailJobPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mail_to, $template;

    public function __construct($mail_to, $template)
    {
        $this->mail_to = $mail_to;
        $this->template = $template;
    }
    public function handle()
    {
        try {
            Mail::to($this->mail_to)->send($this->template);
        } catch (\Exception $e) {
            Log::error('Mail Sending Failed | ' . $e->getMessage());
        }
    }
}
