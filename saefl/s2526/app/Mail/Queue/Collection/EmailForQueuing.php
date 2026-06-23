<?php

namespace App\Mail\Queue\Collection;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Jenssegers\Date\Date;

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
                ->view('email.collections.messege')
                // ->subject('Notificación - SAEFL - Administración')
                ->subject($this->email['subject'])
                // ->subject($this->email['toDate'])
                ->with([
                    'subject' => $this->email['subject'],
                    'representant' => $this->email['representant'],
                    'coll_message' => $this->email['coll_message'],
                    'institucion' => $this->email['institucion'],
                    'autoridad1' => $this->email['autoridad1'],
                    'autoridad2' => $this->email['autoridad2'],
                    'toDate' => $this->email['toDate'],
                    'lastDate' => $this->email['lastDate'],
                ]);
    }
}
