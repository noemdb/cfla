<?php

namespace App\Mail\Queue\Interrogation;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailForQueuingInterview extends Mailable
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
                    'user' => $this->email['user'],
                    'interview' => $this->email['interview'],
                    'interview_questions' => $this->email['interview_questions'],
                    'institucion' => $this->email['institucion'],
                    'director' => $this->email['director'],
                    'toDate' => $this->email['toDate'],
                ]);
    }
}

/*

mailCCAddress
subject
address
user
interview
interview_questions
institucion
director
toDate
view
*/