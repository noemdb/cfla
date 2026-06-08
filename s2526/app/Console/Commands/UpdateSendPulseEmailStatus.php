<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ResendEmail;
use App\Services\SendPulseService;
use Illuminate\Support\Facades\Log;

class UpdateSendPulseEmailStatus extends Command
{
    protected $signature = 'sendpulse:update-status';
    protected $description = 'Actualiza el estado de los emails enviados por SendPulse';

    public function handle()
    {
        try {
            $this->info('Iniciando actualización de estados de emails...');

            // Obtener emails pendientes o en estado 'sent'
            $emails = ResendEmail::where('status', 'sent')
                ->orWhere('status', 'pending')
                ->where('created_at', '>=', now()->subDays(7)) // Solo emails de los últimos 7 días
                ->get();

            if ($emails->isEmpty()) {
                $this->info('No hay emails pendientes de actualización');
                return;
            }

            $this->info('Encontrados ' . $emails->count() . ' emails para actualizar');

            // Crear instancia del servicio
            $sendPulseService = app(SendPulseService::class);

            foreach ($emails as $email) {
                try {
                    // Obtener el estado actual del email
                    $status = $sendPulseService->getEmailStatus($email->resend_id);

                    if ($status && $status !== $email->status) {
                        $email->status = $status;
                        $email->save();

                        $this->info("Email {$email->id} actualizado a estado: {$status}");
                    }
                } catch (\Exception $e) {
                    $this->error("Error al actualizar email {$email->id}: " . $e->getMessage());
                    Log::error('Error al actualizar estado de email', [
                        'email_id' => $email->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->info('Proceso de actualización completado');
        } catch (\Exception $e) {
            $this->error('Error en el proceso de actualización: ' . $e->getMessage());
            Log::error('Error en actualización de estados de emails', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
