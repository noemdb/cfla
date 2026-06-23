<?php

namespace App\Console\Commands;

use App\Services\SendEmailRotationService;
use App\Models\app\SenderMailer\SendMailLogs;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

/*
# Prueba básica con rotación automática
php artisan email:test-rotation --email=noemdb@gmail.com

# Probar un servicio específico
php artisan email:test-rotation --email=noemdb@gmail.com --service=brevo

# Prueba en lote (5 emails)
php artisan email:test-rotation --email=noemdb@gmail.com --batch=5

# Ver estadísticas de servicios
php artisan email:test-rotation --stats

# Ver reporte detallado de límites
php artisan email:test-rotation --limits

# Reintentar emails fallidos
php artisan email:test-rotation --retry-failed

# Verificar estado de un email específico
php artisan email:test-rotation --check-status=123

# Programar verificaciones de estado
php artisan email:test-rotation --schedule-checks

# Preview de programación en lote
php artisan email:test-rotation --preview-batch=10

# Modo verbose para más detalles
php artisan email:test-rotation --email=noemdb@gmail.com -v
*/

class TestEmailRotationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-rotation 
                            {--email= : Email de destino para la prueba}
                            {--service= : Servicio específico a probar (brevo, mailjet, sendpulse, resend)}
                            {--batch= : Número de emails para prueba en lote}
                            {--stats : Mostrar estadísticas de servicios}
                            {--limits : Mostrar reporte de límites}
                            {--retry-failed : Reintentar emails fallidos}
                            {--check-status= : Verificar estado de un email por ID}
                            {--schedule-checks : Programar verificaciones de estado}
                            {--preview-batch= : Preview de programación en lote}
                            {--whatsapp= : Probar notificación WhatsApp}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba y gestiona el servicio de rotación de emails';

    protected $rotationService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->rotationService = app(SendEmailRotationService::class);

        $this->info('🚀 Iniciando pruebas del SendEmailRotationService');
        $this->newLine();

        // Verificar qué acción realizar
        if ($this->option('stats')) {
            return $this->showStats();
        }

        if ($this->option('limits')) {
            return $this->showLimitsReport();
        }

        if ($this->option('retry-failed')) {
            return $this->retryFailedEmails();
        }

        if ($this->option('check-status')) {
            return $this->checkEmailStatus();
        }

        if ($this->option('schedule-checks')) {
            return $this->scheduleStatusChecks();
        }

        if ($this->option('preview-batch')) {
            return $this->previewBatchSchedule();
        }

        if ($this->option('whatsapp')) {
            return $this->testWhatsApp();
        }

        if ($this->option('batch')) {
            return $this->testBatchEmails();
        }

        if ($this->option('service')) {
            return $this->testSpecificService();
        }

        // Prueba básica por defecto
        return $this->testBasicRotation();
    }

    /**
     * Prueba básica de rotación
     */
    protected function testBasicRotation(): int
    {
        $email = $this->option('email') ?: $this->ask('Ingresa el email de destino para la prueba');

        if (!$this->validateEmail($email)) {
            return 1;
        }

        $this->info("📧 Probando envío básico con rotación a: {$email}");

        $emailData = $this->generateTestEmailData($email);
        
        $result = $this->rotationService->queueRotationEmail($emailData);

        $this->displayResult($result);

        if ($result['success']) {
            $this->info("✅ Email programado exitosamente");
            $this->table(['Campo', 'Valor'], [
                ['Servicio', $result['service']],
                ['Programado para', $result['scheduled_at']],
                ['Mail Log ID', $result['mail_log_id']]
            ]);
        }

        return 0;
    }

    /**
     * Prueba un servicio específico
     */
    protected function testSpecificService(): int
    {
        $service = $this->option('service');
        $email = $this->option('email') ?: $this->ask('Ingresa el email de destino');

        if (!$this->validateEmail($email)) {
            return 1;
        }

        if (!in_array($service, ['brevo', 'mailjet', 'sendpulse', 'resend'])) {
            $this->error("❌ Servicio inválido. Usa: brevo, mailjet, sendpulse, resend");
            return 1;
        }

        $this->info("🎯 Probando servicio específico: {$service}");

        $emailData = $this->generateTestEmailData($email);
        
        $result = $this->rotationService->sendEmailWithService($service, $emailData);

        $this->displayResult($result);

        return $result['success'] ? 0 : 1;
    }

    /**
     * Prueba envío en lote
     */
    protected function testBatchEmails(): int
    {
        $batchSize = (int) $this->option('batch');
        $baseEmail = $this->option('email') ?: $this->ask('Ingresa el email base para las pruebas');

        if (!$this->validateEmail($baseEmail)) {
            return 1;
        }

        if ($batchSize < 1 || $batchSize > 50) {
            $this->error("❌ El tamaño del lote debe estar entre 1 y 50");
            return 1;
        }

        $this->info("📦 Probando envío en lote de {$batchSize} emails");

        // Generar emails de prueba
        $emailsData = [];
        for ($i = 1; $i <= $batchSize; $i++) {
            $testEmail = str_replace('@', "+test{$i}@", $baseEmail);
            $emailsData[] = $this->generateTestEmailData($testEmail, "Prueba Lote #{$i}");
        }

        $result = $this->rotationService->batchCollectionSendSchedule($emailsData, 1);

        $this->displayResult($result);

        if ($result['success']) {
            $this->info("✅ Lote programado exitosamente");
            $this->info("📊 Total de emails: {$result['total_emails']}");
            
            // Mostrar algunos resultados
            $this->table(['#', 'Email', 'Servicio', 'Programado para'], 
                collect($result['results'])->take(5)->map(function($item) {
                    return [
                        $item['index'] + 1,
                        $item['email'],
                        $item['result']['service'] ?? 'N/A',
                        $item['scheduled_at']
                    ];
                })->toArray()
            );

            if (count($result['results']) > 5) {
                $this->info("... y " . (count($result['results']) - 5) . " más");
            }
        }

        return 0;
    }

    /**
     * Muestra estadísticas de servicios
     */
    protected function showStats(): int
    {
        $this->info("📊 Estadísticas de Servicios de Email");
        $this->newLine();

        $stats = $this->rotationService->getServiceStats();

        $tableData = [];
        foreach ($stats as $service => $data) {
            $tableData[] = [
                ucfirst($service),
                $data['enabled'] ? '✅' : '❌',
                $data['daily_count'] . '/' . $data['daily_limit'],
                $data['remaining_quota'],
                $data['success_rate'] . '%',
                $data['successful'],
                $data['failed'],
                $data['delay_seconds'] . 's'
            ];
        }

        $this->table([
            'Servicio', 'Habilitado', 'Uso Diario', 'Cuota Restante', 
            'Tasa Éxito', 'Exitosos', 'Fallidos', 'Delay'
        ], $tableData);

        return 0;
    }

    /**
     * Muestra reporte de límites
     */
    protected function showLimitsReport(): int
    {
        $this->info("📋 Reporte Detallado de Límites y Disponibilidad");
        $this->newLine();

        $report = $this->rotationService->getServiceLimitsReport();

        foreach ($report['services'] as $service => $data) {
            $this->info("🔧 {$data['service_name']}");
            
            $status = match($data['status']) {
                'available' => '🟢 Disponible',
                'cooling_down' => '🟡 En espera',
                'quota_exceeded' => '🔴 Cuota agotada',
                'disabled' => '⚫ Deshabilitado',
                default => '❓ Desconocido'
            };

            $this->line("   Estado: {$status}");
            $this->line("   Cuota: {$data['daily_count']}/{$data['daily_limit']} ({$data['quota_percentage']}%)");
            $this->line("   Disponible en: {$data['minutes_until_available']} minutos");
            $this->line("   Último envío: " . ($data['last_sent_at'] ? Carbon::parse($data['last_sent_at'])->diffForHumans() : 'Nunca'));
            $this->newLine();
        }

        // Resumen
        $summary = $report['summary'];
        $this->info("📈 Resumen General:");
        $this->line("   Servicios habilitados: {$summary['enabled_services']}/{$summary['total_services']}");
        $this->line("   Servicios disponibles: {$summary['available_services']}");
        $this->line("   Cuota total: {$summary['used_quota']}/{$summary['total_daily_quota']} ({$summary['quota_usage_percentage']}%)");

        return 0;
    }

    /**
     * Reintenta emails fallidos
     */
    protected function retryFailedEmails(): int
    {
        $this->info("🔄 Reintentando emails fallidos...");

        $result = $this->rotationService->apiRetryFailedEmails();

        $this->displayResult($result);

        if ($result['success']) {
            $this->info("✅ Proceso de reintentos completado");
            $this->info("📊 Total de emails fallidos: {$result['total_failed']}");
            
            if (!empty($result['retry_results'])) {
                $this->table(['Mail Log ID', 'Email', 'Resultado'], 
                    collect($result['retry_results'])->map(function($item) {
                        return [
                            $item['mail_log_id'],
                            $item['email'],
                            $item['result']['success'] ? '✅ Programado' : '❌ ' . $item['result']['message']
                        ];
                    })->toArray()
                );
            }
        }

        return 0;
    }

    /**
     * Verifica el estado de un email
     */
    protected function checkEmailStatus(): int
    {
        $mailLogId = (int) $this->option('check-status');

        if ($mailLogId <= 0) {
            $this->error("❌ ID de mail log inválido");
            return 1;
        }

        $this->info("🔍 Verificando estado del email ID: {$mailLogId}");

        $result = $this->rotationService->checkEmailStatus($mailLogId);

        $this->displayResult($result);

        if ($result['success']) {
            $this->table(['Campo', 'Valor'], [
                ['Estado Actual', $result['current_status']],
                ['Estado Actualizado', $result['updated_status'] ?: 'Sin cambios'],
                ['Fue Actualizado', $result['was_updated'] ? 'Sí' : 'No']
            ]);
        }

        return 0;
    }

    /**
     * Programa verificaciones de estado
     */
    protected function scheduleStatusChecks(): int
    {
        $this->info("⏰ Programando verificaciones de estado...");

        $result = $this->rotationService->scheduleStatusChecks();

        $this->displayResult($result);

        if ($result['success']) {
            $this->info("✅ {$result['scheduled_checks']} verificaciones programadas");
        }

        return 0;
    }

    /**
     * Preview de programación en lote
     */
    protected function previewBatchSchedule(): int
    {
        $batchSize = (int) $this->option('preview-batch');
        $baseEmail = $this->option('email') ?: 'test@example.com';

        if ($batchSize < 1 || $batchSize > 20) {
            $this->error("❌ El tamaño del preview debe estar entre 1 y 20");
            return 1;
        }

        $this->info("👀 Preview de programación en lote ({$batchSize} emails)");

        // Generar emails de prueba
        $emailsData = [];
        for ($i = 1; $i <= $batchSize; $i++) {
            $emailsData[] = ['to' => "test{$i}@example.com"];
        }

        $result = $this->rotationService->apiPreviewSchedule($emailsData);

        if ($result['success']) {
            $this->table(['#', 'Email', 'Servicio', 'Programado para', 'Delay (min)'], 
                collect($result['preview'])->map(function($item) {
                    return [
                        $item['index'] + 1,
                        $item['email'],
                        $item['service'],
                        $item['scheduled_at'] ? Carbon::parse($item['scheduled_at'])->format('H:i:s') : 'N/A',
                        $item['delay_minutes']
                    ];
                })->toArray()
            );

            $this->info("⏱️  Tiempo estimado de finalización: " . Carbon::parse($result['estimated_completion'])->format('H:i:s'));
        }

        return 0;
    }

    /**
     * Prueba notificación WhatsApp
     */
    protected function testWhatsApp(): int
    {
        $phone = $this->option('whatsapp');
        
        if (!$phone) {
            $phone = $this->ask('Ingresa el número de teléfono (con código de país)');
        }

        $this->info("📱 Probando notificación WhatsApp a: {$phone}");

        $whatsappData = [
            'phone' => $phone,
            'message' => 'Prueba de notificación WhatsApp desde ' . config('app.name'),
            'template' => 'test_notification'
        ];

        $result = $this->rotationService->queueWhatsAppNotification($whatsappData);

        $this->displayResult($result);

        return $result['success'] ? 0 : 1;
    }

    /**
     * Genera datos de email de prueba
     */
    protected function generateTestEmailData(string $email, string $subject = null): array
    {
        return [
            'to' => $email,
            'to_name' => 'Usuario de Prueba',
            'subject' => $subject ?: 'Prueba de Rotación de Emails - ' . now()->format('Y-m-d H:i:s'),
            'html' => $this->generateTestHtmlContent(),
            'text' => 'Este es un email de prueba del sistema de rotación de emails.',
            'message_type' => 'test',
            'collection_political_id' => null,
            'representant_ci' => null
        ];
    }

    /**
     * Genera contenido HTML de prueba
     */
    protected function generateTestHtmlContent(): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Prueba de Email</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h1 style="color: #2c3e50;">🧪 Prueba de Sistema de Rotación</h1>
                <p>Este es un email de prueba del <strong>SendEmailRotationService</strong>.</p>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <h3>Detalles de la Prueba:</h3>
                    <ul>
                        <li><strong>Fecha:</strong> ' . now()->format('d/m/Y H:i:s') . '</li>
                        <li><strong>Sistema:</strong> ' . config('app.name') . '</li>
                        <li><strong>Entorno:</strong> ' . config('app.env') . '</li>
                    </ul>
                </div>
                <p>Si recibes este email, significa que el sistema de rotación está funcionando correctamente.</p>
                <hr style="margin: 30px 0;">
                <p style="font-size: 12px; color: #666;">
                    Este es un email automático de prueba. No es necesario responder.
                </p>
            </div>
        </body>
        </html>';
    }

    /**
     * Valida un email
     */
    protected function validateEmail(string $email): bool
    {
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            $this->error("❌ Email inválido: {$email}");
            return false;
        }

        return true;
    }

    /**
     * Muestra el resultado de una operación
     */
    protected function displayResult(array $result): void
    {
        if ($result['success']) {
            $this->info("✅ " . $result['message']);
        } else {
            $this->error("❌ " . $result['message']);
        }

        if (isset($result['data']) && $this->output->isVerbose()) {
            $this->line("📋 Datos adicionales:");
            $this->line(json_encode($result['data'], JSON_PRETTY_PRINT));
        }
    }
}