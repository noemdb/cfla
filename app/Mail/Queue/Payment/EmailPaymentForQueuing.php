<?php

namespace App\Mail\Queue\Payment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailPaymentForQueuing extends Mailable
{
    use Queueable, SerializesModels;

    public $mailData;

    /**
     * Create a new message instance.
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    // public function build()
    // {
    //     return $this->from(env('MAIL_CC_ADDRESS', 'hello@example.com'),'SAEFL - Notificaciones')
    //             ->view('email.payments.messege')
    //             ->subject($this->mailData['subject'])
    //             ->with([
    //                 'subject' => $this->mailData['subject'],
    //                 'representant' => $this->mailData['representant'],
    //                 'inputs' => $this->mailData['inputs'],
    //                 'institucion' => $this->mailData['institucion'],
    //                 'autoridad1' => $this->mailData['autoridad1'],
    //                 'autoridad2' => $this->mailData['autoridad2'],
    //                 'toDate' => $this->mailData['toDate'],
    //             ]);
    // }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Email Payment For Queuing',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.payments.messege',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
