<?php

namespace App\Mail\Queue\Catchment;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailForQueuingAttendee extends Mailable
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
        return $this->from(env('MAIL_FROM_ADDRESS', 'hello@example.com'), $this->email['subject'] . ' - SAEFL - Notificaciones')
            ->view($this->email['view'])
            ->subject($this->email['subject'])
            ->with([
                'subject' => $this->email['subject'],
                'catchment' => $this->email['catchment'],
                'interview' => $this->email['interview'],
                'list_comment' => $this->email['list_comment'],
                'institucion' => $this->email['institucion'],
                'director' => $this->email['director'],
                'autoridad' => $this->email['autoridad'],
                'toDate' => $this->email['toDate'],
            ]);
    }
}
