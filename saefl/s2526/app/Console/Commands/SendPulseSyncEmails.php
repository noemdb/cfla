<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SendPulseService;
use App\Models\ResendEmail;
use Carbon\Carbon;

class SendPulseSyncEmails extends Command
{
    protected $signature = 'sendpulse:sync-emails {--days=1 : Número de días hacia atrás para sincronizar}';
    protected $description = 'Sincroniza el estado de los emails enviados a través de SendPulse';

    protected $sendPulseService;

    public function __construct(SendPulseService $sendPulseService)
    {
        parent::__construct();
        $this->sendPulseService = $sendPulseService;
    }

    public function handle()
    {
        $days = $this->option('days');
        $this->info("Iniciando sincronización de emails de los últimos {$days} días...");

        try {
            // Obtener emails directamente de la API de SendPulse
            $this->info("Obteniendo emails de SendPulse...");

            $fromDate = Carbon::now()->subDays($days)->format('Y-m-d');
            $toDate = Carbon::now()->format('Y-m-d');

            $this->info("Período de búsqueda: {$fromDate} hasta {$toDate}");

            $result = $this->sendPulseService->getEmailsList(
                fromDate: $fromDate,
                toDate: $toDate
            );

            $this->info("Respuesta recibida de SendPulse: " . json_encode($result));

            if (!isset($result['data']) || empty($result['data'])) {
                $this->warn('No se encontraron emails en SendPulse para el período especificado.');
                $this->info('Verificando la respuesta completa: ' . json_encode($result));
                return 0;
            }

            $this->info("Encontrados " . count($result['data']) . " emails en SendPulse.");
            $this->newLine();

            // Mostrar tabla de emails encontrados
            $headers = ['ID', 'Destinatario', 'Asunto', 'Estado', 'Fecha'];
            $rows = [];

            foreach ($result['data'] as $email) {
                $rows[] = [
                    $email['id'] ?? 'N/A',
                    $email['recipient'] ?? 'N/A',
                    $email['subject'] ?? 'N/A',
                    $email['status'] ?? 'N/A',
                    isset($email['date']) ? Carbon::parse($email['date'])->format('Y-m-d H:i:s') : 'N/A'
                ];
            }

            $this->table($headers, $rows);
            $this->newLine();

            // Preguntar si desea continuar con la sincronización
            if (!$this->confirm('¿Desea continuar con la sincronización de estos emails?')) {
                $this->info('Operación cancelada por el usuario.');
                return 0;
            }

            $this->info("Iniciando sincronización...");
            $bar = $this->output->createProgressBar(count($result['data']));
            $bar->start();

            foreach ($result['data'] as $emailData) {
                // Buscar o crear el registro local
                $email = ResendEmail::firstOrNew(['resend_id' => $emailData['id']]);

                if (!$email->exists) {
                    // Si es un nuevo email, actualizar los datos básicos
                    $email->fill([
                        'from' => $emailData['sender'] ?? config('services.sendpulse.from'),
                        'to' => $emailData['recipient'] ?? '',
                        'subject' => $emailData['subject'] ?? '',
                        'status' => $emailData['status'] ?? 'pending',
                        'sent_at' => isset($emailData['date']) ? Carbon::parse($emailData['date']) : null
                    ]);
                    $email->save();
                } else {
                    // Si ya existe, solo actualizar el estado
                    $email->updateStatus($emailData['status'] ?? 'pending');
                }

                $this->sendPulseService->logError('Email sincronizado', [
                    'email_id' => $email->id,
                    'resend_id' => $email->resend_id,
                    'status' => $emailData['status'] ?? 'pending'
                ]);

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('Sincronización completada exitosamente.');
        } catch (\Exception $e) {
            $this->error("Error durante la sincronización: {$e->getMessage()}");
            $this->sendPulseService->logError('Error en sincronización', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        return 0;
    }
}
