<?php

namespace App\Services;

use App\Models\app\SenderMailer\SendMailLogs;
use App\Services\BrevoService;
use App\Services\MailjetService;
use App\Services\SendPulseService;
use App\Services\ResendEmailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use App\Jobs\Email\SendEmailWithRotationJob;
use App\Jobs\WhatsApp\SendWhatsAppNotificationJob;

class SendEmailRotationService
{
    protected $services = [];
    protected $serviceConfigs = [];

    public function __construct()
    {
        $this->initializeServices();
        $this->loadServiceConfigs();
    }

    /**
     * Inicializa los servicios de email disponibles
     */
    protected function initializeServices()
    {
        $this->services = [
            'brevo' => app(BrevoService::class),
            'mailjet' => app(MailjetService::class),
            'sendpulse' => app(SendPulseService::class),
            'resend' => app(ResendEmailService::class),
        ];
    }

    /**
     * Carga la configuración de límites y delays para cada servicio
     */
    protected function loadServiceConfigs()
    {
        $this->serviceConfigs = [
            'brevo' => [
                'daily_limit' => config('services.brevo.daily_limit', 300),
                'delay_seconds' => config('services.brevo.delay_seconds', 60),
                'enabled' => config('services.brevo.enabled', true),
            ],
            'mailjet' => [
                'daily_limit' => config('services.mailjet.daily_limit', 200),
                'delay_seconds' => config('services.mailjet.delay_seconds', 45),
                'enabled' => config('services.mailjet.enabled', true),
            ],
            'sendpulse' => [
                'daily_limit' => config('services.sendpulse.daily_limit', 500),
                'delay_seconds' => config('services.sendpulse.delay_seconds', 30),
                'enabled' => config('services.sendpulse.enabled', true),
            ],
            'resend' => [
                'daily_limit' => config('services.resend.daily_limit', 100),
                'delay_seconds' => config('services.resend.delay_seconds', 90),
                'enabled' => config('services.resend.enabled', true),
            ],
        ];
    }

    /**
     * Programa el envío de email con rotación de servicios
     */
    public function queueRotationEmail(array $emailData, ?Carbon $scheduledAt = null): array
    {
        try {
            $service = $this->getNextAvailableService();

            if (!$service) {
                return [
                    'success' => false,
                    'message' => 'No hay servicios disponibles en este momento',
                    'next_available_time' => $this->getEarliestAvailableTime()
                ];
            }

            $nextAvailableTime = $this->getNextAvailableTime($service);
            $finalScheduledTime = $scheduledAt ?? $nextAvailableTime;

            // Crear registro en SendMailLogs
            $mailLog = $this->createMailLog($emailData, $service, $finalScheduledTime);

            // Programar el job
            $job = SendEmailWithRotationJob::dispatch($emailData, $service, $mailLog->id);

            if ($finalScheduledTime->isFuture()) {
                $job->delay($finalScheduledTime);
            }

            return [
                'success' => true,
                'message' => 'Email programado exitosamente',
                'service' => $service,
                'scheduled_at' => $finalScheduledTime->toISOString(),
                'mail_log_id' => $mailLog->id
            ];

        } catch (\Exception $e) {
            Log::error('Error en queueRotationEmail: ' . $e->getMessage(), [
                'email_data' => $emailData,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error al programar el email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Envía emails usando un servicio específico
     */
    public function sendEmailWithService(string $serviceName, array $emailData): array
    {
        if (!isset($this->services[$serviceName])) {
            return [
                'success' => false,
                'message' => "Servicio {$serviceName} no encontrado"
            ];
        }

        try {
            switch ($serviceName) {
                case 'brevo':
                    return $this->sendWithBrevo($emailData);
                case 'mailjet':
                    return $this->sendWithMailjet($emailData);
                case 'sendpulse':
                    return $this->sendWithSendPulse($emailData);
                case 'resend':
                    return $this->sendWithResend($emailData);
                default:
                    return [
                        'success' => false,
                        'message' => "Método de envío no implementado para {$serviceName}"
                    ];
            }
        } catch (\Exception $e) {
            Log::error("Error enviando con {$serviceName}: " . $e->getMessage(), [
                'email_data' => $emailData,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => "Error en {$serviceName}: " . $e->getMessage()
            ];
        }
    }

    /**
     * Implementación para Brevo
     */
    protected function sendWithBrevo(array $emailData): array
    {
        $to = [['email' => $emailData['to'], 'name' => $emailData['to_name'] ?? '']];
        $cc = !empty($emailData['cc']) ? [['email' => $emailData['cc'], 'name' => '']] : [];
        $bcc = !empty($emailData['bcc']) ? [['email' => $emailData['bcc'], 'name' => '']] : [];

        return $this->services['brevo']->sendEmail(
            $to,
            $emailData['subject'],
            $emailData['html'],
            $emailData['text'] ?? null,
            $cc,
            $bcc,
            $emailData['attachments'] ?? []
        );
    }

    /**
     * Implementación para Mailjet
     */
    protected function sendWithMailjet(array $emailData): array
    {
        $data = [
            'To' => [['Email' => $emailData['to'], 'Name' => $emailData['to_name'] ?? '']],
            'Subject' => $emailData['subject'],
            'HTMLPart' => $emailData['html'],
            'TextPart' => $emailData['text'] ?? strip_tags($emailData['html'])
        ];

        if (!empty($emailData['cc'])) {
            $data['Cc'] = [['Email' => $emailData['cc'], 'Name' => '']];
        }

        if (!empty($emailData['bcc'])) {
            $data['Bcc'] = [['Email' => $emailData['bcc'], 'Name' => '']];
        }

        return $this->services['mailjet']->sendEmail($data);
    }

    /**
     * Implementación para SendPulse
     */
    protected function sendWithSendPulse(array $emailData): array
    {
        return $this->services['sendpulse']->send(
            $emailData['to'],
            $emailData['subject'],
            $emailData['html'],
            null, // delayTime
            false, // queue
            $emailData['cc'] ?? null,
            $emailData['bcc'] ?? null
        );
    }

    /**
     * Implementación para Resend
     */
    protected function sendWithResend(array $emailData): array
    {
        return $this->services['resend']->send(
            $emailData['to'],
            $emailData['subject'],
            $emailData['html'],
            null, // delayTime
            false, // queue
            $emailData['cc'] ?? null,
            $emailData['bcc'] ?? null
        );
    }

    /**
     * Obtiene el siguiente servicio disponible
     */
    public function getNextAvailableService(): ?string
    {
        $availableServices = [];

        foreach ($this->serviceConfigs as $serviceName => $config) {
            if (!$config['enabled']) {
                continue;
            }

            $dailyCount = $this->getDailyEmailCount($serviceName);

            if ($dailyCount < $config['daily_limit']) {
                $lastSentTime = $this->getLastSentTime($serviceName);
                $nextAvailableTime = $lastSentTime ?
                    $lastSentTime->addSeconds($config['delay_seconds']) :
                    now();

                $availableServices[] = [
                    'service' => $serviceName,
                    'available_at' => $nextAvailableTime,
                    'remaining_quota' => $config['daily_limit'] - $dailyCount
                ];
            }
        }

        if (empty($availableServices)) {
            return null;
        }

        // Ordenar por tiempo disponible y luego por cuota restante
        usort($availableServices, function ($a, $b) {
            $timeComparison = $a['available_at']->timestamp <=> $b['available_at']->timestamp;
            return $timeComparison !== 0 ? $timeComparison : $b['remaining_quota'] <=> $a['remaining_quota'];
        });

        return $availableServices[0]['service'];
    }

    /**
     * Calcula el próximo tiempo disponible para un servicio
     */
    public function getNextAvailableTime(string $serviceName): Carbon
    {
        $config = $this->serviceConfigs[$serviceName] ?? null;

        if (!$config) {
            return now()->addMinutes(5);
        }

        $lastSentTime = $this->getLastSentTime($serviceName);

        if (!$lastSentTime) {
            return now();
        }

        $nextTime = $lastSentTime->addSeconds($config['delay_seconds']);

        return $nextTime->isPast() ? now() : $nextTime;
    }

    /**
     * Programa envíos en lote con rotación
     */
    public function batchCollectionSendSchedule(array $emailsData, int $initialDelayMinutes = 5): array
    {
        $results = [];
        $currentDelay = $initialDelayMinutes;

        foreach ($emailsData as $index => $emailData) {
            $scheduledAt = now()->addMinutes($currentDelay);

            $result = $this->queueRotationEmail($emailData, $scheduledAt);

            $results[] = [
                'index' => $index,
                'email' => $emailData['to'],
                'result' => $result,
                'scheduled_at' => $scheduledAt->toISOString()
            ];

            // Incrementar delay para el siguiente email
            $currentDelay += 2; // 2 minutos entre cada email
        }

        return [
            'success' => true,
            'message' => 'Lote de emails programado exitosamente',
            'total_emails' => count($emailsData),
            'results' => $results
        ];
    }

    /**
     * Obtiene estadísticas de uso por servicio
     */
    public function getServiceStats(?Carbon $dateFrom = null, ?Carbon $dateTo = null): array
    {
        $dateFrom = $dateFrom ?? now()->startOfDay();
        $dateTo = $dateTo ?? now()->endOfDay();

        $stats = [];

        foreach (array_keys($this->serviceConfigs) as $serviceName) {
            $dailyCount = SendMailLogs::byProvider($serviceName)
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();

            $successful = SendMailLogs::byProvider($serviceName)
                ->successful()
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();

            $failed = SendMailLogs::byProvider($serviceName)
                ->failed()
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->count();

            $config = $this->serviceConfigs[$serviceName];

            $stats[$serviceName] = [
                'daily_limit' => $config['daily_limit'],
                'daily_count' => $dailyCount,
                'remaining_quota' => max(0, $config['daily_limit'] - $dailyCount),
                'successful' => $successful,
                'failed' => $failed,
                'success_rate' => $dailyCount > 0 ? round(($successful / $dailyCount) * 100, 2) : 0,
                'enabled' => $config['enabled'],
                'delay_seconds' => $config['delay_seconds'],
                'next_available_time' => $this->getNextAvailableTime($serviceName)->toISOString()
            ];
        }

        return $stats;
    }

    /**
     * Genera reporte detallado de límites y delays
     */
    public function getServiceLimitsReport(): array
    {
        $report = [];

        foreach ($this->serviceConfigs as $serviceName => $config) {
            $dailyCount = $this->getDailyEmailCount($serviceName);
            $lastSentTime = $this->getLastSentTime($serviceName);
            $nextAvailableTime = $this->getNextAvailableTime($serviceName);

            $report[$serviceName] = [
                'service_name' => ucfirst($serviceName),
                'enabled' => $config['enabled'],
                'daily_limit' => $config['daily_limit'],
                'daily_count' => $dailyCount,
                'remaining_quota' => max(0, $config['daily_limit'] - $dailyCount),
                'quota_percentage' => $config['daily_limit'] > 0 ?
                    round(($dailyCount / $config['daily_limit']) * 100, 2) : 0,
                'delay_seconds' => $config['delay_seconds'],
                'last_sent_at' => $lastSentTime?->toISOString(),
                'next_available_at' => $nextAvailableTime->toISOString(),
                'is_available_now' => $nextAvailableTime->isPast(),
                'minutes_until_available' => max(0, $nextAvailableTime->diffInMinutes(now())),
                'status' => $this->getServiceStatus($serviceName)
            ];
        }

        return [
            'generated_at' => now()->toISOString(),
            'services' => $report,
            'summary' => $this->getServicesSummary($report)
        ];
    }

    /**
     * Verifica el estado de un email enviado
     */
    public function checkEmailStatus(int $mailLogId): array
    {
        try {
            $mailLog = SendMailLogs::find($mailLogId);

            if (!$mailLog) {
                return [
                    'success' => false,
                    'message' => 'Email log no encontrado'
                ];
            }

            $serviceName = $mailLog->service_provider;
            $messageId = $mailLog->resend_id;

            if (!isset($this->services[$serviceName])) {
                return [
                    'success' => false,
                    'message' => "Servicio {$serviceName} no disponible"
                ];
            }

            // Obtener estado del servicio correspondiente
            $status = null;
            switch ($serviceName) {
                case 'sendpulse':
                    $status = $this->services['sendpulse']->getEmailStatus($messageId);
                    break;
                case 'resend':
                    $status = $this->services['resend']->getEmailStatus($messageId);
                    break;
                // Agregar otros servicios según implementen getEmailStatus
            }

            if ($status && $status !== $mailLog->status) {
                $mailLog->updateStatus($status);
                $mailLog->addEvent([
                    'type' => 'status_updated',
                    'old_status' => $mailLog->status,
                    'new_status' => $status,
                    'checked_at' => now()->toISOString()
                ]);
            }

            return [
                'success' => true,
                'mail_log_id' => $mailLogId,
                'current_status' => $mailLog->status,
                'updated_status' => $status,
                'was_updated' => $status && $status !== $mailLog->status
            ];

        } catch (\Exception $e) {
            Log::error('Error verificando estado del email: ' . $e->getMessage(), [
                'mail_log_id' => $mailLogId,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error al verificar estado: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Programa verificaciones periódicas del estado
     */
    public function scheduleStatusChecks(): array
    {
        $emailsToCheck = SendMailLogs::getNeedingStatusUpdate(60);
        $scheduled = 0;

        foreach ($emailsToCheck as $mailLog) {
            // Programar verificación con delay aleatorio para evitar sobrecarga
            $delay = rand(1, 300); // Entre 1 y 300 segundos

            dispatch(function () use ($mailLog) {
                $this->checkEmailStatus($mailLog->id);
            })->delay(now()->addSeconds($delay));

            $scheduled++;
        }

        return [
            'success' => true,
            'message' => "Programadas {$scheduled} verificaciones de estado",
            'scheduled_checks' => $scheduled
        ];
    }

    /**
     * Obtiene emails fallidos que necesitan reintento
     */
    public function getEmailsNeedingRetry(int $maxRetries = 3): array
    {
        return SendMailLogs::failed()
            ->where('retry_count', '<', $maxRetries)
            ->where('created_at', '>', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($mailLog) {
                return [
                    'id' => $mailLog->id,
                    'service_provider' => $mailLog->service_provider,
                    'to' => $mailLog->to,
                    'subject' => $mailLog->subject,
                    'retry_count' => $mailLog->retry_count,
                    'error_message' => $mailLog->error_message,
                    'failed_at' => $mailLog->updated_at->toISOString()
                ];
            })
            ->toArray();
    }

    /**
     * Reintenta el envío de un email fallido
     */
    public function retryFailedEmail(int $mailLogId): array
    {
        try {
            $mailLog = SendMailLogs::find($mailLogId);

            if (!$mailLog) {
                return [
                    'success' => false,
                    'message' => 'Email log no encontrado'
                ];
            }

            if (!$mailLog->hasFailed()) {
                return [
                    'success' => false,
                    'message' => 'El email no está en estado fallido'
                ];
            }

            // Incrementar contador de reintentos
            $mailLog->incrementRetryCount();

            // Preparar datos del email
            $emailData = [
                'to' => $mailLog->to,
                'subject' => $mailLog->subject,
                'html' => $mailLog->html,
                'text' => $mailLog->text,
                'cc' => $mailLog->cc,
                'bcc' => $mailLog->bcc
            ];

            // Intentar con un servicio diferente si es posible
            $originalService = $mailLog->service_provider;
            $newService = $this->getNextAvailableService();

            if (!$newService) {
                return [
                    'success' => false,
                    'message' => 'No hay servicios disponibles para el reintento'
                ];
            }

            // Actualizar el servicio en el log
            $mailLog->service_provider = $newService;
            $mailLog->status = 'retrying';
            $mailLog->save();

            // Programar el reintento
            $scheduledAt = $this->getNextAvailableTime($newService);
            $job = SendEmailWithRotationJob::dispatch($emailData, $newService, $mailLog->id);

            if ($scheduledAt->isFuture()) {
                $job->delay($scheduledAt);
            }

            $mailLog->addEvent([
                'type' => 'retry_scheduled',
                'original_service' => $originalService,
                'new_service' => $newService,
                'scheduled_at' => $scheduledAt->toISOString(),
                'retry_count' => $mailLog->retry_count
            ]);

            return [
                'success' => true,
                'message' => 'Reintento programado exitosamente',
                'original_service' => $originalService,
                'new_service' => $newService,
                'scheduled_at' => $scheduledAt->toISOString(),
                'retry_count' => $mailLog->retry_count
            ];

        } catch (\Exception $e) {
            Log::error('Error reintentando email: ' . $e->getMessage(), [
                'mail_log_id' => $mailLogId,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error al reintentar: ' . $e->getMessage()
            ];
        }
    }

    // ============ API ENDPOINTS ============

    /**
     * Endpoint para obtener estadísticas vía API
     */
    public function apiGetStats(): array
    {
        return [
            'success' => true,
            'data' => $this->getServiceStats(),
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * Endpoint para preview del cronograma vía API
     */
    public function apiPreviewSchedule(array $emailsData): array
    {
        $preview = [];
        $currentTime = now();

        foreach ($emailsData as $index => $emailData) {
            $service = $this->getNextAvailableService();
            $scheduledAt = $service ? $this->getNextAvailableTime($service) : null;

            $preview[] = [
                'index' => $index,
                'email' => $emailData['to'] ?? 'N/A',
                'service' => $service ?? 'No disponible',
                'scheduled_at' => $scheduledAt?->toISOString(),
                'delay_minutes' => $scheduledAt ? $scheduledAt->diffInMinutes($currentTime) : null
            ];

            // Simular el uso del servicio para el siguiente cálculo
            if ($service && $scheduledAt) {
                $currentTime = $scheduledAt->addSeconds($this->serviceConfigs[$service]['delay_seconds']);
            }
        }

        return [
            'success' => true,
            'preview' => $preview,
            'total_emails' => count($emailsData),
            'estimated_completion' => $currentTime->toISOString()
        ];
    }

    /**
     * Endpoint para reintentar emails fallidos vía API
     */
    public function apiRetryFailedEmails(int $maxRetries = 3): array
    {
        $failedEmails = $this->getEmailsNeedingRetry($maxRetries);
        $retryResults = [];

        foreach ($failedEmails as $failedEmail) {
            $result = $this->retryFailedEmail($failedEmail['id']);
            $retryResults[] = [
                'mail_log_id' => $failedEmail['id'],
                'email' => $failedEmail['to'],
                'result' => $result
            ];
        }

        return [
            'success' => true,
            'message' => 'Proceso de reintentos completado',
            'total_failed' => count($failedEmails),
            'retry_results' => $retryResults
        ];
    }

    // ============ MÉTODOS AUXILIARES ============

    /**
     * Obtiene el conteo diario de emails para un servicio
     */
    protected function getDailyEmailCount(string $serviceName): int
    {
        return SendMailLogs::byProvider($serviceName)
            ->whereDate('created_at', today())
            ->count();
    }

    /**
     * Obtiene la última vez que se envió un email con un servicio
     */
    protected function getLastSentTime(string $serviceName): ?Carbon
    {
        $lastEmail = SendMailLogs::byProvider($serviceName)
            ->whereNotNull('sent_at')
            ->orderBy('sent_at', 'desc')
            ->first();

        return $lastEmail?->sent_at;
    }

    /**
     * Obtiene el tiempo más temprano disponible entre todos los servicios
     */
    protected function getEarliestAvailableTime(): Carbon
    {
        $earliestTime = now()->addDays(1); // Default: mañana

        foreach (array_keys($this->serviceConfigs) as $serviceName) {
            $availableTime = $this->getNextAvailableTime($serviceName);
            if ($availableTime->isBefore($earliestTime)) {
                $earliestTime = $availableTime;
            }
        }

        return $earliestTime;
    }

    /**
     * Crea un registro en SendMailLogs
     */
    protected function createMailLog(array $emailData, string $service, Carbon $scheduledAt): SendMailLogs
    {
        return SendMailLogs::create([
            'resend_id' => uniqid($service . '_'),
            'service_provider' => $service,
            'from' => config("services.{$service}.from") ?? config("services.{$service}.default_sender.email"),
            'to' => $emailData['to'],
            'subject' => $emailData['subject'],
            'html' => $emailData['html'],
            'text' => $emailData['text'] ?? strip_tags($emailData['html']),
            'cc' => $emailData['cc'] ?? null,
            'bcc' => $emailData['bcc'] ?? null,
            'status' => 'scheduled',
            'collection_political_id' => $emailData['collection_political_id'] ?? null,
            'representant_ci' => $emailData['representant_ci'] ?? null,
            'message_type' => $emailData['message_type'] ?? 'transactional',
            'scheduled_at' => $scheduledAt,
            'retry_count' => 0
        ]);
    }

    /**
     * Obtiene el estado de un servicio
     */
    protected function getServiceStatus(string $serviceName): string
    {
        $config = $this->serviceConfigs[$serviceName];

        if (!$config['enabled']) {
            return 'disabled';
        }

        $dailyCount = $this->getDailyEmailCount($serviceName);

        if ($dailyCount >= $config['daily_limit']) {
            return 'quota_exceeded';
        }

        $nextAvailableTime = $this->getNextAvailableTime($serviceName);

        if ($nextAvailableTime->isFuture()) {
            return 'cooling_down';
        }

        return 'available';
    }

    /**
     * Obtiene un resumen de todos los servicios
     */
    protected function getServicesSummary(array $servicesReport): array
    {
        $totalServices = count($servicesReport);
        $enabledServices = collect($servicesReport)->where('enabled', true)->count();
        $availableServices = collect($servicesReport)->where('status', 'available')->count();
        $totalQuota = collect($servicesReport)->sum('daily_limit');
        $usedQuota = collect($servicesReport)->sum('daily_count');

        return [
            'total_services' => $totalServices,
            'enabled_services' => $enabledServices,
            'available_services' => $availableServices,
            'total_daily_quota' => $totalQuota,
            'used_quota' => $usedQuota,
            'remaining_quota' => max(0, $totalQuota - $usedQuota),
            'quota_usage_percentage' => $totalQuota > 0 ? round(($usedQuota / $totalQuota) * 100, 2) : 0
        ];
    }
}
