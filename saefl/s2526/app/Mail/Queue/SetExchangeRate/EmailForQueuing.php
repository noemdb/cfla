<?php

namespace App\Mail\Queue\SetExchangeRate;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailForQueuing extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS', 'hello@example.com'),'SAEFL - Notificaciones')
            ->view($this->email['view'])
            ->subject($this->email['subject'])
            ->with([
                'subject' => $this->email['subject'],
                'exchange_rate' => $this->email['exchange_rate'],
                'toDate' => $this->email['toDate'],
            ]);
    }
}
