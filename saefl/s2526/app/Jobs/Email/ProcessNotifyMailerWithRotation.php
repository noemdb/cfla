<?php

namespace App\Jobs\Email;

use App\Services\SendEmailRotationService;
use App\Models\app\SenderMailer\SendMailLogs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessNotifyMailerWithRotation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $emailData;
    public $serviceName;
    public $mailLogId;
    public $tries = 3;
    public $timeout = 60;
    public $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(array $emailData, string $serviceName, int $mailLogId)
    {
        $this->emailData = $emailData;
        $this->serviceName = $serviceName;
        $this->mailLogId = $mailLogId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $emailRotationService = app(SendEmailRotationService::class);

            // Actualizar el log como "enviando"
            $mailLog = SendMailLogs::find($this->mailLogId);
            if ($mailLog) {
                $mailLog->updateStatus('sending');
                $mailLog->addEvent([
                    'type' => 'job_started',
                    'service' => $this->serviceName,
                    'started_at' => now()->toISOString()
                ]);
            }

            // Enviar el email usando el servicio específico
            $result = $emailRotationService->sendEmailWithService($this->serviceName, $this->emailData);

            if ($result['success']) {
                // Actualizar el log como exitoso
                if ($mailLog) {
                    $mailLog->updateStatus('sent');
                    $mailLog->sent_at = now();
                    $mailLog->response_data = $result;
                    $mailLog->save();

                    $mailLog->addEvent([
                        'type' => 'sent_successfully',
                        'service' => $this->serviceName,
                        'sent_at' => now()->toISOString(),
                        'response' => $result
                    ]);
                }

                Log::info("Email enviado exitosamente", [
                    'service' => $this->serviceName,
                    'to' => $this->emailData['to'],
                    'mail_log_id' => $this->mailLogId
                ]);
            } else {
                // Marcar como fallido
                if ($mailLog) {
                    $mailLog->updateStatus('failed');
                    $mailLog->error_message = $result['message'] ?? 'Error desconocido';
                    $mailLog->save();

                    $mailLog->addEvent([
                        'type' => 'send_failed',
                        'service' => $this->serviceName,
                        'failed_at' => now()->toISOString(),
                        'error' => $result['message'] ?? 'Error desconocido'
                    ]);
                }

                throw new \Exception($result['message'] ?? 'Error al enviar email');
            }

        } catch (\Exception $e) {
            Log::error("Error en ProcessNotifyMailerWithRotation", [
                'service' => $this->serviceName,
                'email_data' => $this->emailData,
                'mail_log_id' => $this->mailLogId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Actualizar el log con el error
            $mailLog = SendMailLogs::find($this->mailLogId);
            if ($mailLog) {
                $mailLog->updateStatus('failed');
                $mailLog->error_message = $e->getMessage();
                $mailLog->save();

                $mailLog->addEvent([
                    'type' => 'job_failed',
                    'service' => $this->serviceName,
                    'failed_at' => now()->toISOString(),
                    'error' => $e->getMessage(),
                    'attempt' => $this->attempts()
                ]);
            }

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error("Job ProcessNotifyMailerWithRotation falló definitivamente", [
            'service' => $this->serviceName,
            'email_data' => $this->emailData,
            'mail_log_id' => $this->mailLogId,
            'error' => $exception->getMessage()
        ]);

        // Marcar como fallido definitivamente
        $mailLog = SendMailLogs::find($this->mailLogId);
        if ($mailLog) {
            $mailLog->updateStatus('failed_permanently');
            $mailLog->error_message = $exception->getMessage();
            $mailLog->save();

            $mailLog->addEvent([
                'type' => 'job_failed_permanently',
                'service' => $this->serviceName,
                'failed_at' => now()->toISOString(),
                'error' => $exception->getMessage(),
                'total_attempts' => $this->attempts()
            ]);
        }
    }
}
