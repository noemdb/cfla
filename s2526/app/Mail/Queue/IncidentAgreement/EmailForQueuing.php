<?php

namespace App\Mail\Queue\IncidentAgreement;

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
                'representant' => $this->email['representant'],
                'incident' => $this->email['incident'],
                'incident_agreements' => $this->email['incident_agreements'],
                'institucion' => $this->email['institucion'],
                'autoridad1' => $this->email['autoridad1'],
                'autoridad2' => $this->email['autoridad2'],
                'autoridad3' => $this->email['autoridad3'],
                'toDate' => $this->email['toDate'],
            ]);
    }
}
