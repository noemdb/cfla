<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SendPulseService;
use Illuminate\Support\Facades\Log;

class TestSendPulse extends Command
{
    protected $signature = 'test:sendpulse {email?} {--queue}';
    protected $description = 'Prueba el servicio de SendPulse';

    public function handle()
    {
        try {
            $this->info('Iniciando prueba de SendPulse...');

            // Obtener el email de prueba o usar uno por defecto
            $testEmail = $this->argument('email') ?? 'noemdb@gmail.com';
            $useQueue = $this->option('queue');

            if ($useQueue) {
                $this->info('Modo Queue activado.');
            }

            // Crear instancia del servicio
            $sendPulseService = app(SendPulseService::class);

            // Verificar la conexión
            $this->info('Verificando conexión con SendPulse...');
            $addressBooks = $sendPulseService->getAddressBooks();
            $this->info('Conexión exitosa: ' . json_encode($addressBooks));

            // Preparar datos del email de prueba
            $subject = 'Test SendPulse - ' . now()->format('Y-m-d H:i:s');
            $htmlContent = '
                <html>
                    <body>
                        <h1>Test de SendPulse</h1>
                        <p>Este es un email de prueba enviado el ' . now()->format('Y-m-d H:i:s') . '</p>
                        <p>Si recibes este email, significa que la configuración de SendPulse está funcionando correctamente.</p>
                    </body>
                </html>
            ';

            // Intentar enviar el email
            $this->info('Intentando enviar email de prueba...');
            $result = $sendPulseService->send(
                $testEmail,
                $subject,
                $htmlContent,
                null,
                $useQueue
            );

            // Mostrar resultado
            $this->info('Resultado del envío: ' . json_encode($result, JSON_PRETTY_PRINT));

            if ($result['success']) {
                $this->info('✅ Email enviado exitosamente');
            } else {
                $this->error('❌ Error al enviar el email: ' . $result['message']);
            }
        } catch (\Exception $e) {
            $this->error('❌ Error en la prueba: ' . $e->getMessage());
            Log::error('Error en prueba de SendPulse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
