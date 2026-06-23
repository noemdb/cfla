<?php

namespace App\Jobs\Email;

use App\Services\MailjetService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailjetEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailData;

    public function __construct(array $emailData)
    {
        $this->emailData = $emailData;
    }

    public function handle()
    {
        $mailjet = app(MailjetService::class);

        $mailjet->sendEmail([
            'To' => [
                [
                    'Email' => $this->emailData['to'],
                    'Name' => $this->emailData['name'],
                ]
            ],
            'Subject' => $this->emailData['subject'],
            'TextPart' => $this->emailData['text'] ?? '',
            'HTMLPart' => $this->emailData['html'],
        ]);
    }
}
