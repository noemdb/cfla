<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Jenssegers\Date\Date;

// class SendNotifyCollectPolitical extends Mailable implements ShouldQueue
class SendNotifyCollectPolitical extends Mailable
{
    use Queueable, SerializesModels;

    public $representant;
    public $message;
    public $autoridad1;
    public $autoridad2;
    public $toDate;
    public $lastDate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($representant, $coll_message, $institucion, $autoridad1, $autoridad2)
    {
        $this->representant = $representant;
        $this->coll_message = $coll_message;
        $this->institucion = $institucion;
        $this->autoridad1 = $autoridad1;
        $this->autoridad2 = $autoridad2;
        $this->toDate = Date::now()->format('d F Y');
        $this->lastDate = Date::now()->lastOfMonth()->format('d F Y');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("soporte.saefl@gmail.com")
                ->view('email.collections.messege')
                ->subject('Notificación de Cobro - Deuda vencida')
                ->with([
                    'representant' => $this->representant,
                    'coll_message' => $this->coll_message,
                    'institucion' => $this->institucion,
                    'autoridad1' => $this->autoridad1,
                    'autoridad2' => $this->autoridad2,
                    'toDate' => $this->toDate,
                    'lastDate' => $this->lastDate,
                ]);
    }
}
