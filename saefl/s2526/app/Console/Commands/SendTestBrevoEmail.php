<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\View;
use App\Services\BrevoService;

class SendTestBrevoEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brevo:test-email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un correo de prueba usando Brevo Service con una vista Blade';

    /**
     * Execute the console command.
     *
     * @param  \App\Services\BrevoService  $brevo
     * @return int
     */
    public function handle(BrevoService $brevo)
    {
        $toEmail = $this->argument('email');

        // Datos que pasamos a la vista
        $data = [
            'name' => 'Usuario de Prueba',
            'message' => 'Este es un correo de prueba enviado desde Laravel usando Brevo.',
            'url' => url('/')
        ];

        // Renderizamos la vista Blade como HTML
        $htmlContent = View::make('email.test.brevo', $data)->render();

        // Enviar correo
        $result = $brevo->sendEmail(
            to: [ ['email' => $toEmail] ],
            subject: 'Correo de prueba desde Brevo',
            htmlContent: $htmlContent
        );

        if (isset($result['error'])) {
            $this->error("Error al enviar el correo: " . $result['error']);
            return 1;
        }

        $this->info("Correo enviado exitosamente. Respuesta:");
        $this->line(json_encode($result, JSON_PRETTY_PRINT));
        return 0;
    }
}