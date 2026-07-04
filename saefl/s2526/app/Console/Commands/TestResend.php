<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ResendEmailService;
use Illuminate\Support\Facades\Log;

class TestResend extends Command
{
    protected $signature = 'test:resend {email?}';
    protected $description = 'Prueba el servicio de Resend';

    public function handle()
    {
        try {
            $this->info('Iniciando prueba de Resend...');

            // Obtener el email de prueba o usar uno por defecto
            $testEmail = $this->argument('email') ?? 'noemdb@gmail.com';

            // Crear instancia del servicio
            $resendService = app(ResendEmailService::class);

            // Verificar la configuración
            $this->info('Verificando configuración de Resend...');
            $this->info('URL configurada: ' . config('services.resend.url'));
            $this->info('From configurado: ' . config('services.resend.from'));

            // Preparar datos del email de prueba
            $subject = 'Test Resend - ' . now()->format('Y-m-d H:i:s');
            $htmlContent = '
                <html>
                    <body>
                        <h1>Test de Resend</h1>
                        <p>Este es un email de prueba enviado el ' . now()->format('Y-m-d H:i:s') . '</p>
                        <p>Si recibes este email, significa que la configuración de Resend está funcionando correctamente.</p>
                    </body>
                </html>
            ';

            // Intentar enviar el email
            $this->info('Intentando enviar email de prueba...');
            $result = $resendService->send(
                $testEmail,
                $subject,
                $htmlContent,
                null,
                false
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
            Log::error('Error en prueba de Resend', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
