<?php

namespace App\Jobs\Email;

use App\Models\app\SenderMailer\SendMailLogs;
use App\Services\SendEmailRotationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

class SendEmailWithRotationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emailData;
    protected $serviceName;
    protected $mailLogId;
    protected $retryCount;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 2;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     *
     * @return array
     */
    public function backoff()
    {
        return [30, 60, 120]; // 30 segundos, 1 minuto, 2 minutos
    }

    /**
     * Create a new job instance.
     *
     * @param array $emailData
     * @param string $serviceName
     * @param int $mailLogId
     * @param int $retryCount
     */
    public function __construct(array $emailData, string $serviceName, int $mailLogId, int $retryCount = 0)
    {
        $this->emailData = $emailData;
        $this->serviceName = $serviceName;
        $this->mailLogId = $mailLogId;
        $this->retryCount = $retryCount;

        // Configurar la queue específica para emails
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Obtener el registro del email
            $mailLog = SendMailLogs::find($this->mailLogId);

            if (!$mailLog) {
                Log::error('SendEmailWithRotationJob: Mail log no encontrado', [
                    'mail_log_id' => $this->mailLogId,
                    'service' => $this->serviceName
                ]);
                return;
            }

            // Verificar si el email ya fue enviado exitosamente
            if ($mailLog->wasSuccessful()) {
                Log::info('SendEmailWithRotationJob: Email ya fue enviado exitosamente', [
                    'mail_log_id' => $this->mailLogId,
                    'status' => $mailLog->status
                ]);
                return;
            }

            // Actualizar estado a "processing"
            $mailLog->updateStatus('processing');
            $mailLog->addEvent([
                'type' => 'job_started',
                'service' => $this->serviceName,
                'attempt' => $this->attempts(),
                'retry_count' => $this->retryCount
            ]);

            // Obtener el servicio de rotación
            $rotationService = app(SendEmailRotationService::class);

            // Verificar si el servicio está disponible
            if (!$this->isServiceAvailable($rotationService)) {
                $this->handleServiceUnavailable($mailLog, $rotationService);
                return;
            }

            // Enviar el email
            $result = $rotationService->sendEmailWithService($this->serviceName, $this->emailData);

            if ($result['success'] ?? false) {
                $this->handleSuccessfulSend($mailLog, $result);
            } else {
                $this->handleFailedSend($mailLog, $result, $rotationService);
            }

        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Verifica si el servicio está disponible
     */
    protected function isServiceAvailable(SendEmailRotationService $rotationService): bool
    {
        $stats = $rotationService->getServiceStats();
        $serviceStats = $stats[$this->serviceName] ?? null;

        if (!$serviceStats) {
            Log::warning('SendEmailWithRotationJob: Estadísticas del servicio no disponibles', [
                'service' => $this->serviceName,
                'mail_log_id' => $this->mailLogId
            ]);
            return false;
        }

        // Verificar si el servicio está habilitado
        if (!$serviceStats['enabled']) {
            Log::warning('SendEmailWithRotationJob: Servicio deshabilitado', [
                'service' => $this->serviceName,
                'mail_log_id' => $this->mailLogId
            ]);
            return false;
        }

        // Verificar cuota disponible
        if ($serviceStats['remaining_quota'] <= 0) {
            Log::warning('SendEmailWithRotationJob: Cuota del servicio agotada', [
                'service' => $this->serviceName,
                'daily_count' => $serviceStats['daily_count'],
                'daily_limit' => $serviceStats['daily_limit'],
                'mail_log_id' => $this->mailLogId
            ]);
            return false;
        }

        return true;
    }

    /**
     * Maneja cuando el servicio no está disponible
     */
    protected function handleServiceUnavailable(SendMailLogs $mailLog, SendEmailRotationService $rotationService)
    {
        // Intentar obtener un servicio alternativo
        $alternativeService = $rotationService->getNextAvailableService();

        if ($alternativeService && $alternativeService !== $this->serviceName) {
            Log::info('SendEmailWithRotationJob: Cambiando a servicio alternativo', [
                'original_service' => $this->serviceName,
                'alternative_service' => $alternativeService,
                'mail_log_id' => $this->mailLogId
            ]);

            // Actualizar el servicio en el mail log
            $mailLog->service_provider = $alternativeService;
            $mailLog->save();

            $mailLog->addEvent([
                'type' => 'service_changed',
                'original_service' => $this->serviceName,
                'new_service' => $alternativeService,
                'reason' => 'original_service_unavailable'
            ]);

            // Reprogramar con el nuevo servicio
            $nextAvailableTime = $rotationService->getNextAvailableTime($alternativeService);
            $newJob = new self($this->emailData, $alternativeService, $this->mailLogId, $this->retryCount);

            if ($nextAvailableTime->isFuture()) {
                $newJob->delay($nextAvailableTime);
            }

            dispatch($newJob);

        } else {
            // No hay servicios alternativos disponibles
            $mailLog->updateStatus('failed');
            $mailLog->setError('No hay servicios de email disponibles');

            $mailLog->addEvent([
                'type' => 'no_services_available',
                'attempted_service' => $this->serviceName,
                'retry_count' => $this->retryCount
            ]);

            Log::error('SendEmailWithRotationJob: No hay servicios disponibles', [
                'service' => $this->serviceName,
                'mail_log_id' => $this->mailLogId,
                'retry_count' => $this->retryCount
            ]);
        }
    }

    /**
     * Maneja el envío exitoso
     */
    protected function handleSuccessfulSend(SendMailLogs $mailLog, array $result)
    {
        $mailLog->updateStatus('sent');
        $mailLog->sent_at = now();

        // Guardar la respuesta del proveedor
        if (isset($result['data'])) {
            $mailLog->saveResponse($result['data']);

            // Actualizar el resend_id si viene en la respuesta
            if (isset($result['data']['id'])) {
                $mailLog->resend_id = $result['data']['id'];
            } elseif (isset($result['data']['messageId'])) {
                $mailLog->resend_id = $result['data']['messageId'];
            }
        }

        $mailLog->save();

        $mailLog->addEvent([
            'type' => 'sent_successfully',
            'service' => $this->serviceName,
            'attempt' => $this->attempts(),
            'retry_count' => $this->retryCount,
            'response_data' => $result['data'] ?? null
        ]);

        Log::info('SendEmailWithRotationJob: Email enviado exitosamente', [
            'service' => $this->serviceName,
            'mail_log_id' => $this->mailLogId,
            'to' => $this->emailData['to'],
            'subject' => $this->emailData['subject'],
            'attempt' => $this->attempts()
        ]);

        // Programar verificaciones de estado si el servicio lo soporta
        $this->scheduleStatusChecks($mailLog);
    }

    /**
     * Maneja el envío fallido
     */
    protected function handleFailedSend(SendMailLogs $mailLog, array $result, SendEmailRotationService $rotationService)
    {
        $errorMessage = $result['message'] ?? 'Error desconocido al enviar email';

        $mailLog->addEvent([
            'type' => 'send_failed',
            'service' => $this->serviceName,
            'attempt' => $this->attempts(),
            'retry_count' => $this->retryCount,
            'error_message' => $errorMessage,
            'response_data' => $result['data'] ?? null
        ]);

        Log::warning('SendEmailWithRotationJob: Fallo al enviar email', [
            'service' => $this->serviceName,
            'mail_log_id' => $this->mailLogId,
            'error' => $errorMessage,
            'attempt' => $this->attempts(),
            'retry_count' => $this->retryCount
        ]);

        // Verificar si debemos reintentar
        $maxRetries = config('services.email_rotation.max_retries', 3);

        if ($this->retryCount < $maxRetries) {
            $this->scheduleRetry($mailLog, $rotationService, $errorMessage);
        } else {
            // Máximo de reintentos alcanzado
            $mailLog->updateStatus('failed');
            $mailLog->setError($errorMessage);

            $mailLog->addEvent([
                'type' => 'max_retries_reached',
                'service' => $this->serviceName,
                'final_error' => $errorMessage,
                'total_retries' => $this->retryCount
            ]);

            Log::error('SendEmailWithRotationJob: Máximo de reintentos alcanzado', [
                'service' => $this->serviceName,
                'mail_log_id' => $this->mailLogId,
                'total_retries' => $this->retryCount,
                'final_error' => $errorMessage
            ]);
        }
    }

    /**
     * Programa un reintento con posible cambio de servicio
     */
    protected function scheduleRetry(SendMailLogs $mailLog, SendEmailRotationService $rotationService, string $errorMessage)
    {
        $newRetryCount = $this->retryCount + 1;
        $mailLog->incrementRetryCount();

        // Intentar obtener un servicio diferente para el reintento
        $retryService = $rotationService->getNextAvailableService();

        if (!$retryService) {
            // Si no hay servicios disponibles, usar el mismo servicio pero con delay mayor
            $retryService = $this->serviceName;
        }

        // Calcular el delay para el reintento
        $retryDelayMinutes = config('services.email_rotation.retry_delay_minutes', 15);
        $delayMinutes = $retryDelayMinutes * $newRetryCount; // Delay exponencial
        $retryTime = now()->addMinutes($delayMinutes);

        // Actualizar el servicio si cambió
        if ($retryService !== $this->serviceName) {
            $mailLog->service_provider = $retryService;
            $mailLog->save();
        }

        $mailLog->updateStatus('retry_scheduled');
        $mailLog->addEvent([
            'type' => 'retry_scheduled',
            'original_service' => $this->serviceName,
            'retry_service' => $retryService,
            'retry_count' => $newRetryCount,
            'retry_at' => $retryTime->toISOString(),
            'delay_minutes' => $delayMinutes,
            'reason' => $errorMessage
        ]);

        // Programar el reintento
        $retryJob = new self($this->emailData, $retryService, $this->mailLogId, $newRetryCount);
        $retryJob->delay($retryTime);
        dispatch($retryJob);

        Log::info('SendEmailWithRotationJob: Reintento programado', [
            'original_service' => $this->serviceName,
            'retry_service' => $retryService,
            'mail_log_id' => $this->mailLogId,
            'retry_count' => $newRetryCount,
            'retry_at' => $retryTime->toISOString(),
            'delay_minutes' => $delayMinutes
        ]);
    }

    /**
     * Programa verificaciones de estado del email
     */
    protected function scheduleStatusChecks(SendMailLogs $mailLog)
    {
        if (!config('services.email_rotation.status_check_enabled', true)) {
            return;
        }

        $intervals = config('services.email_rotation.status_check_intervals', [1, 5, 15]);

        foreach ($intervals as $minutes) {
            dispatch(function () use ($mailLog) {
                $rotationService = app(SendEmailRotationService::class);
                $rotationService->checkEmailStatus($mailLog->id);
            })->delay(now()->addMinutes($minutes));
        }
    }

    /**
     * Maneja excepciones no controladas
     */
    protected function handleException(Exception $e)
    {
        Log::error('SendEmailWithRotationJob: Excepción no controlada', [
            'service' => $this->serviceName,
            'mail_log_id' => $this->mailLogId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'attempt' => $this->attempts()
        ]);

        // Actualizar el mail log si es posible
        try {
            $mailLog = SendMailLogs::find($this->mailLogId);
            if ($mailLog) {
                $mailLog->setError('Excepción en job: ' . $e->getMessage());
                $mailLog->addEvent([
                    'type' => 'job_exception',
                    'service' => $this->serviceName,
                    'error' => $e->getMessage(),
                    'attempt' => $this->attempts()
                ]);
            }
        } catch (Exception $logException) {
            Log::error('SendEmailWithRotationJob: Error al actualizar mail log', [
                'original_error' => $e->getMessage(),
                'log_error' => $logException->getMessage()
            ]);
        }

        // Re-lanzar la excepción para que Laravel maneje los reintentos
        throw $e;
    }

    /**
     * Handle a job failure.
     *
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        Log::error('SendEmailWithRotationJob: Job falló definitivamente', [
            'service' => $this->serviceName,
            'mail_log_id' => $this->mailLogId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
            'retry_count' => $this->retryCount
        ]);

        // Actualizar el estado final del email
        try {
            $mailLog = SendMailLogs::find($this->mailLogId);
            if ($mailLog) {
                $mailLog->updateStatus('failed');
                $mailLog->setError('Job falló: ' . $exception->getMessage());
                $mailLog->addEvent([
                    'type' => 'job_failed_permanently',
                    'service' => $this->serviceName,
                    'error' => $exception->getMessage(),
                    'total_attempts' => $this->attempts(),
                    'retry_count' => $this->retryCount
                ]);
            }
        } catch (Exception $e) {
            Log::error('SendEmailWithRotationJob: Error al marcar como fallido', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags()
    {
        return [
            'email',
            'rotation',
            $this->serviceName,
            'mail_log:' . $this->mailLogId
        ];
    }
}
