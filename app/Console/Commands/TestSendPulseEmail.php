<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SendPulseService;
use Exception;

class TestSendPulseEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendpulse:test {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía un email de prueba a través del servicio SendPulse para confirmar conectividad';

    /**
     * Execute the console command.
     */
    public function handle(SendPulseService $sendPulseService)
    {
        $email = $this->argument('email');
        
        $this->info("Iniciando prueba de envío vía SendPulse al destinatario: {$email}");

        $html = "
        <div style='font-family: Arial, sans-serif; font-size: 16px; color: #333;'>
            <h1>Prueba de Integración Exitosa</h1>
            <p>Hola,</p>
            <p>Este es un correo de prueba generado desde el Artisan Command <b>sendpulse:test</b>.</p>
            <p>Si estás recibiendo esto, significa que la integración con la API de SendPulse, incluyendo la generación del token y el envío SMTP, están funcionando correctamente.</p>
            <hr />
            <p><i>Saludos, Sistema SAEFL</i></p>
        </div>";
        $subject = 'Envío de prueba - Integración SendPulse';

        try {
            $this->line('Obteniendo token y enviando correo...');
            
            $result = $sendPulseService->sendEmail(
                to: $email,
                subject: $subject,
                htmlBody: $html
            );

            if ($result) {
                $this->info('✅ ¡Correo de prueba enviado con éxito! Verifica tu bandeja de entrada o la carpeta de spam.');
            }
        } catch (Exception $e) {
            $this->error('❌ Error al enviar el correo de prueba:');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
