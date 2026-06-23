<?php

namespace App\Mail\Queue\Bot;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailForQueuing extends Mailable
{
    use Queueable, SerializesModels;

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
                ->view('email.bots.messege')
                ->subject($this->email['subject'])
                ->with([
                    'subject' => $this->email['subject'],
                    'estudiant' => $this->email['estudiant'],
                    'message' => $this->email['message'],
                    'toDate' => $this->email['toDate'],
                    'institucion' => $this->email['institucion'],
                    'autoridad1' => $this->email['autoridad1'],
                    'autoridad2' => $this->email['autoridad2'],
                ]);
    }
}
