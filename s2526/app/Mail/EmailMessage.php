<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// class EmailMessage extends Mailable implements ShouldQueue
class EmailMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $message)
    {
        $this->name = $name;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from("noemdb@gmail.com")
                ->view('email')
                ->subject('Notificación de Cobro')
                ->with([
                        'nameEmail' => $this->name,
                        'messageEmail' => $this->message,
                    ]);
    }
}
