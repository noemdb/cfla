<?php

namespace App\Livewire\Landing;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ContactComponent extends Component
{
    public $email;
    public $subject;
    public $message;
    public $success = false;

    protected $rules = [
        'email' => 'required|email',
        'subject' => 'required|min:3',
        'message' => 'required|min:10',
    ];

    public function send()
    {
        $this->validate();

        try {
            // Construct email body
            $html = "
                <h2>Nuevo Mensaje de Contacto desde EDUSYS</h2>
                <p><strong>De:</strong> {$this->email}</p>
                <p><strong>Asunto:</strong> {$this->subject}</p>
                <p><strong>Mensaje:</strong></p>
                <p>{$this->message}</p>
            ";

            // Send email using Resend API (similar to Payment/IndexComponent)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('RESEND_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post(env('RESEND_URL', 'https://api.resend.com/emails'), [
                'from' => env('RESEND_FROM_NAME', 'EDUSYS') . ' <' . env('RESEND_FROM', 'onboarding@resend.dev') . '>',
                'to' => $this->email,
                'bcc' => [env('MAIL_CC_ADDRESS')],
                'subject' => 'Contacto Web: ' . $this->subject,
                'html' => $html,
            ]);

            if ($response->successful()) {
                $this->success = true;
                $this->reset(['email', 'subject', 'message']);
            } else {
                $this->addError('general', 'Hubo un problema al enviar el mensaje. Por favor intenta más tarde. ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->addError('general', 'Error de conexión: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.landing.contact-component');
    }
}
