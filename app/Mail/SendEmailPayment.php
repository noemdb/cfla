<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendEmailPayment extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    public $subject;

    public function __construct($data, $subject)
    {
        $this->data = $data;
        $this->subject = $subject;
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'email.payments.messege',
            with: [
                'data' => $this->data,
            ],
        );
    }
    public function attachments(): array
    {
        return [
            // Here, you can attach your file with something like
            // Attachment::fromPath($this->filepath),
        ];
    }
}
