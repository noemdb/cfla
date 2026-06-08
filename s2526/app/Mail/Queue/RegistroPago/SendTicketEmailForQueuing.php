<?php

namespace App\Mail\Queue\RegistroPago;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendTicketEmailForQueuing extends Mailable
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
        // $this->email['attach'] = base64_encode(base64_decode($this->email['attach']));
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
                ->attachData($this->email['attach'], 'reciboDePago.pdf')
                ->with([
                    'subject' => $this->email['subject'],
                    'registro_pago_combinado' => $this->email['registro_pago_combinado'],
                    'representant' => $this->email['representant'],
                    'institucion' => $this->email['institucion'],
                    'autoridad1' => $this->email['autoridad1'],
                    'autoridad2' => $this->email['autoridad2'],
                    'toDate' => $this->email['toDate'],
                    'lastDate' => $this->email['lastDate'],
                ]);
    }
}
