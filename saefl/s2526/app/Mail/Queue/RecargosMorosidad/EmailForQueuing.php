<?php

namespace App\Mail\Queue\RecargosMorosidad;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailForQueuing extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->view($this->data['view'])
                    ->subject($this->data['subject'])
                    ->with('data', $this->data);
    }

    // Método para renderizar sin enviar (usado por ResendEmailService)
    public function render()
    {
        return view($this->data['view'], ['data' => $this->data])->render();
    }
}