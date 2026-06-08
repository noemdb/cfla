<?php

namespace App\Mail\Queue\Poll;

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
        return $this->from(env('MAIL_FROM_ADDRESS', 'hello@example.com'), $this->email['subject'].' - SAEFL - Notificaciones')
                ->view($this->email['view'])
                ->subject($this->email['subject'])
                ->with([
                    'subject' => $this->email['subject'],
                    'poll_main' => $this->email['poll_main'],
                    'poll_questions' => $this->email['poll_questions'],
                    'poll_token' => $this->email['poll_token'],
                    'attendee' => $this->email['attendee'],
                    'institucion' => $this->email['institucion'],
                    'director' => $this->email['director'],
                    'toDate' => $this->email['toDate'],
                ]);
    }
}
