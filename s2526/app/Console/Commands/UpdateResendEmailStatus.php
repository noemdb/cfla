<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ResendEmail;
use App\Services\ResendEmailService;
use Illuminate\Support\Facades\Log;

class UpdateResendEmailStatus extends Command
{
    protected $signature = 'resend:update-status';
    protected $description = 'Actualiza el estado de los emails enviados por Resend';

    public function handle()
    {
        try {
            $this->info('Iniciando actualización de estados de emails de Resend...');

            // Obtener emails pendientes o en estado 'sent'
            $emails = ResendEmail::where('status', 'sent')
                ->orWhere('status', 'pending')
                ->where('created_at', '>=', now()->subDays(7)) // Solo emails de los últimos 7 días
                ->where('resend_id', 'like', 're_%') // Filtrar solo emails de Resend
                ->get();

            if ($emails->isEmpty()) {
                $this->info('No hay emails de Resend pendientes de actualización');
                return;
            }

            $this->info('Encontrados ' . $emails->count() . ' emails de Resend para actualizar');

            // Crear instancia del servicio
            $resendService = app(ResendEmailService::class);

            foreach ($emails as $email) {
                try {
                    // Obtener el estado actual del email
                    $status = $resendService->getEmailStatus($email->resend_id);

                    if ($status && $status !== $email->status) {
                        $email->status = $status;
                        $email->save();

                        $this->info("Email {$email->id} actualizado a estado: {$status}");
                    }
                } catch (\Exception $e) {
                    $this->error("Error al actualizar email {$email->id}: " . $e->getMessage());
                    Log::error('Error al actualizar estado de email de Resend', [
                        'email_id' => $email->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $this->info('Proceso de actualización de Resend completado');
        } catch (\Exception $e) {
            $this->error('Error en el proceso de actualización de Resend: ' . $e->getMessage());
            Log::error('Error en actualización de estados de emails de Resend', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
