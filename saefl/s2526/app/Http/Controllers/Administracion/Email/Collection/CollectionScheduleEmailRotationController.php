<?php

namespace App\Http\Controllers\Administracion\Email\Collection;

use App\Http\Controllers\Controller;
use App\Jobs\Email\Collection\ProcessNotifyCollectRotation;
use Illuminate\Http\Request;

use App\Jobs\Queue\Meta\SendWhatsAppMessageJob;
use App\Models\app\Cobranzas\CollPolitical;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\SenderMailer\SendMailLogs;
use App\Services\BrevoService;
use App\Services\MailjetService;
use App\Services\SendPulseService;
use App\Services\ResendEmailService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Jenssegers\Date\Date;

class CollectionScheduleEmailRotationController extends Controller
{
    public $view = 'email.collections.messege'; // Vista para el correo electrónico
    public $services = ['brevo', 'jetmail', 'sendpulse', 'resend']; // Servicios de correos transaccionales
    
    // Límites específicos por servicio
    public $serviceLimits = [
        'brevo' => 300,      // Límite para Brevo
        'jetmail' => 250,    // Límite para Mailjet
        'sendpulse' => 200,  // Límite para SendPulse
        'resend' => 100,     // Límite para Resend (plan gratuito más limitado)
    ];

    // Delays específicos por servicio (en segundos)
    public $serviceDelays = [
        'brevo' => 80,       // 80 segundos entre emails de Brevo
        'jetmail' => 70,     // 70 segundos entre emails de Mailjet
        'sendpulse' => 100,  // 100 segundos entre emails de SendPulse
        'resend' => 90,      // 90 segundos entre emails de Resend
    ];
    
    protected $currentServiceIndex = 0; // Índice del servicio actual
    protected $serviceInstances = []; // Instancias de los servicios
    protected $serviceCounts = []; // Contadores por servicio
    protected $serviceLastUsed = []; // Último tiempo usado por servicio
    protected $availableServices = []; // Servicios disponibles (que no han alcanzado el límite)

    public function __construct()
    {
        // Inicializar las instancias de los servicios
        $this->serviceInstances = [
            'brevo' => app(BrevoService::class),
            'jetmail' => app(MailjetService::class),
            'sendpulse' => app(SendPulseService::class),
            'resend' => app(ResendEmailService::class),
        ];

        // Inicializar contadores y tiempos
        $this->initializeServiceCounters();
    }

    /**
     * Inicializa los contadores y tiempos por servicio
     */
    protected function initializeServiceCounters()
    {
        $today = Carbon::today();
        
        foreach ($this->services as $service) {
            // Cargar conteo actual del día
            $this->serviceCounts[$service] = SendMailLogs::where('service_provider', $service)
                ->whereDate('created_at', $today)
                ->count();
            
            // Inicializar último tiempo usado
            $this->serviceLastUsed[$service] = Carbon::now();
        }

        // Inicializar servicios disponibles
        $this->updateAvailableServices();
    }

    /**
     * Actualiza la lista de servicios disponibles (que no han alcanzado el límite)
     */
    protected function updateAvailableServices()
    {
        $this->availableServices = [];
        
        foreach ($this->services as $service) {
            if ($this->serviceCounts[$service] < $this->serviceLimits[$service]) {
                $this->availableServices[] = $service;
            }
        }

        // Si no hay servicios disponibles, resetear todos (para el siguiente día)
        if (empty($this->availableServices)) {
            $this->availableServices = $this->services;
            Log::warning("Todos los servicios han alcanzado su límite diario");
        }
    }

    /**
     * Obtiene el siguiente servicio disponible de forma rotatoria
     */
    protected function getNextAvailableService(): string
    {
        if (empty($this->availableServices)) {
            $this->updateAvailableServices();
        }

        // Rotación circular entre servicios disponibles
        $service = $this->availableServices[$this->currentServiceIndex % count($this->availableServices)];
        $this->currentServiceIndex++;

        return $service;
    }

    /**
     * Calcula el próximo tiempo disponible para un servicio específico
     */
    protected function getNextAvailableTime(string $service, Carbon $baseTime): Carbon
    {
        $serviceDelay = $this->serviceDelays[$service];
        $lastUsed = $this->serviceLastUsed[$service];
        $nextAvailable = $lastUsed->copy()->addSeconds($serviceDelay);

        // Si el tiempo base es mayor que el próximo disponible, usar el tiempo base
        if ($baseTime->greaterThan($nextAvailable)) {
            return $baseTime;
        }

        return $nextAvailable;
    }

    /**
     * Actualiza el último tiempo usado para un servicio
     */
    protected function updateServiceLastUsed(string $service, Carbon $time)
    {
        $this->serviceLastUsed[$service] = $time;
        $this->serviceCounts[$service]++;
        
        // Actualizar servicios disponibles si se alcanzó el límite
        if ($this->serviceCounts[$service] >= $this->serviceLimits[$service]) {
            $this->updateAvailableServices();
        }
    }

    public function batchCollectionSendSchedule($id, $number = null, $status_whatsapp = false, $status_email = false)
    {
        $coll_political = CollPolitical::findOrFail($id);
        $coll_messages = $coll_political->coll_messeges;
        $representants = $coll_political->representants;
        
        $baseTime = Carbon::now();
        $sendMails = collect();
        $emailCount = 0;
        
        Log::info("Iniciando envío masivo con rotación alternada", [
            'collection_political_id' => $id,
            'total_representants' => $representants->count(),
            'total_messages' => $coll_messages->where('status', 'true')->count(),
            'available_services' => $this->availableServices,
            'service_limits' => $this->serviceLimits,
            'service_delays' => $this->serviceDelays,
            'current_counts' => $this->serviceCounts
        ]);
        
        foreach ($representants as $representant) {
            foreach ($coll_messages as $coll_message) {
                if ($coll_message->status == 'true') {
                    $subject = $number . ' ' . $coll_message->title;
                    
                    // Obtener el siguiente servicio disponible
                    $selectedService = $this->getNextAvailableService();
                    
                    // Calcular el tiempo de envío para este servicio
                    $scheduledTime = $this->getNextAvailableTime($selectedService, $baseTime);
                    
                    $emailSent = $this->processRepresentantNotification(
                        $representant, 
                        $coll_message, 
                        $scheduledTime, 
                        $subject, 
                        $status_whatsapp, 
                        $status_email,
                        $coll_political->id,
                        $selectedService
                    );
                    
                    if ($emailSent) {
                        $sendMails->push($emailSent);
                        $emailCount++;
                        
                        // Actualizar el último tiempo usado para este servicio
                        $this->updateServiceLastUsed($selectedService, $scheduledTime);
                        
                        // Actualizar el tiempo base para el próximo email
                        $baseTime = $scheduledTime->copy()->addSeconds(10); // 10 segundos mínimos entre cualquier email
                    }
                    
                    // Procesar WhatsApp si está activado
                    if ($status_whatsapp && $representant->status_whatsapp_verify) {
                        $this->queueWhatsAppNotification($representant, $scheduledTime);
                    }
                }
            }
        }
        
        Log::info("Envío masivo completado", [
            'total_emails_scheduled' => $sendMails->count(),
            'final_service_counts' => $this->serviceCounts,
            'last_scheduled_time' => $baseTime->toISOString()
        ]);
        
        return $sendMails;
    }

    protected function processRepresentantNotification($representant, $coll_message, $scheduledTime, $subject, $status_whatsapp, $status_email, $collPoliticalId, $selectedService)
    {
        $except = [/* tu lista de CIs excluidos */];
        
        if (in_array($representant->ci_representant, $except)) {
            return null;
        }

        $emailRecord = null;

        // Procesar email si está activado y es válido
        if ($status_email && validate_email($representant->email)) {
            // Configuración común
            $institucion = Institucion::latest()->first();
            $autoridad1 = Autoridad::getTipoAuthority('2'); // director
            $autoridad2 = Autoridad::getTipoAuthority('4'); // administrador
            $toDate = Date::now()->format('d F Y');
            $lastDate = Date::now()->lastOfMonth()->format('d F Y');

            $emailRecord = $this->queueRotationEmail(
                $representant,
                $coll_message,
                $subject,
                $institucion,
                $autoridad1,
                $autoridad2,
                $toDate,
                $lastDate,
                $scheduledTime,
                $collPoliticalId,
                $selectedService
            );
        }

        return $emailRecord;
    }

    protected function queueRotationEmail($representant, $coll_message, $subject, $institucion, $autoridad1, $autoridad2, $toDate, $lastDate, $scheduledTime, $collPoliticalId, $selectedService)
    {
        try {
            // Preparar datos del email para crear el registro
            $dataEmail = [
                'view' => $this->view,
                'subject' => $subject,
                'address' => $representant->email,
                'mailCCAddress' => env('MAIL_CC_ADDRESS_ADMON'),
                'representant' => $representant,
                'coll_message' => $coll_message,
                'institucion' => $institucion,
                'autoridad1' => $autoridad1,
                'autoridad2' => $autoridad2,
                'toDate' => $toDate,
                'lastDate' => $lastDate,
                'collection_political_id' => $collPoliticalId
            ];

            // Crear el registro usando el método del modelo
            $emailRecord = SendMailLogs::createFromController($dataEmail, $selectedService);
            
            // Actualizar campos específicos para la rotación
            $emailRecord->update([
                'scheduled_at' => $scheduledTime,
                'status' => 'scheduled'
            ]);

            // Agregar evento de programación
            $emailRecord->addEvent([
                'type' => 'scheduled',
                'service' => $selectedService,
                'scheduled_for' => $scheduledTime->toISOString(),
                'delay_used' => $this->serviceDelays[$selectedService],
                'message' => "Email programado para envío con delay de {$this->serviceDelays[$selectedService]} segundos"
            ]);

            // Preparar datos para el job
            $jobData = array_merge($dataEmail, [
                'service' => $selectedService,
                'send_mail_log_id' => $emailRecord->id
            ]);

            // Usar el Job genérico para rotación de servicios
            ProcessNotifyCollectRotation::dispatch($jobData)->delay($scheduledTime);

            Log::info("Email programado con servicio alternado", [
                'send_mail_log_id' => $emailRecord->id,
                'service' => $selectedService,
                'to' => $representant->email,
                'scheduled_for' => $scheduledTime->toISOString(),
                'delay_used' => $this->serviceDelays[$selectedService],
                'service_count' => $this->serviceCounts[$selectedService]
            ]);

            return $emailRecord;

        } catch (Exception $e) {
            Log::error("Error al programar email", [
                'error' => $e->getMessage(),
                'service' => $selectedService,
                'to' => $representant->email,
                'representant_ci' => $representant->ci_representant,
                'scheduled_time' => $scheduledTime->toISOString()
            ]);
            return null;
        }
    }    

    protected function queueWhatsAppNotification($representant, $time)
    {
        $whatsapp = $representant->whatsapp;
        $phonePattern = '/^\d{11,12}$/';
        
        if (preg_match($phonePattern, $whatsapp)) {
            SendWhatsAppMessageJob::dispatch(
                $representant->ci_representant,
                $whatsapp,
                'notice_collection'
            )->delay($time);
        }
    }

    /**
     * Obtiene la dirección FROM del servicio especificado
     */
    protected function getServiceFromAddress(string $service): string
    {
        return match($service) {
            'brevo' => config('services.brevo.default_sender.email'),
            'jetmail' => config('services.mailjet.default_from_email'),
            'sendpulse' => config('services.sendpulse.from'),
            'resend' => config('services.resend.from'),
            default => config('mail.from.address')
        };
    }

    /**
     * Envía email usando el servicio especificado y actualiza el registro
     */
    public function sendEmailWithService($service, $dataEmail)
    {
        $sendMailLog = null;
        
        try {
            // Buscar el registro de email si se proporcionó el ID
            if (isset($dataEmail['send_mail_log_id'])) {
                $sendMailLog = SendMailLogs::find($dataEmail['send_mail_log_id']);
            }

            $serviceInstance = $this->serviceInstances[$service] ?? null;
            
            if (!$serviceInstance) {
                throw new \Exception("Servicio de email '{$service}' no encontrado");
            }

            $htmlContent = view($dataEmail['view'], $dataEmail)->render();
            $textContent = strip_tags($htmlContent);

            // Actualizar el registro con el contenido HTML
            if ($sendMailLog) {
                $sendMailLog->html = $htmlContent;
                $sendMailLog->text = $textContent;
                $sendMailLog->status = 'sending';
                $sendMailLog->save();

                $sendMailLog->addEvent([
                    'type' => 'sending',
                    'service' => $service,
                    'delay_used' => $this->serviceDelays[$service],
                    'message' => "Iniciando envío de email con delay de {$this->serviceDelays[$service]} segundos"
                ]);
            }

            $result = match($service) {
                'brevo' => $this->sendWithBrevo($serviceInstance, $dataEmail, $htmlContent),
                'jetmail' => $this->sendWithMailjet($serviceInstance, $dataEmail, $htmlContent),
                'sendpulse' => $this->sendWithSendPulse($serviceInstance, $dataEmail, $htmlContent),
                'resend' => $this->sendWithResend($serviceInstance, $dataEmail, $htmlContent),
                default => throw new Exception("Servicio '{$service}' no implementado")
            };

            // Actualizar el registro según el resultado
            if ($sendMailLog) {
                if ($result['success']) {
                    $sendMailLog->updateStatus('sent');
                    $sendMailLog->saveResponse($result['data']);
                    
                    // Actualizar el resend_id con el ID real del proveedor si está disponible
                    if (isset($result['data']['id'])) {
                        $sendMailLog->resend_id = $result['data']['id'];
                        $sendMailLog->save();
                    }

                    $sendMailLog->addEvent([
                        'type' => 'sent',
                        'service' => $service,
                        'message' => 'Email enviado exitosamente',
                        'provider_id' => $result['data']['id'] ?? null
                    ]);

                    // Programar jobs para verificar el estado del email
                    $this->scheduleStatusChecks($sendMailLog);

                } else {
                    $sendMailLog->setError($result['message']);
                    $sendMailLog->addEvent([
                        'type' => 'failed',
                        'service' => $service,
                        'message' => $result['message']
                    ]);
                }
            }

            return $result;

        } catch (Exception $e) {
            Log::error("Error enviando email con servicio '{$service}': " . $e->getMessage(), [
                'service' => $service,
                'email' => $dataEmail['address'] ?? 'unknown',
                'send_mail_log_id' => $dataEmail['send_mail_log_id'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Actualizar el registro con el error
            if ($sendMailLog) {
                $sendMailLog->setError($e->getMessage());
                $sendMailLog->incrementRetryCount();
                $sendMailLog->addEvent([
                    'type' => 'error',
                    'service' => $service,
                    'message' => $e->getMessage()
                ]);
            }
            
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'service' => $service
            ];
        }
    }

    /**
     * Programa jobs para verificar el estado del email
     */
    protected function scheduleStatusChecks(SendMailLogs $sendMailLog)
    {
        // Verificar estado después de 2 minutos
        dispatch(function () use ($sendMailLog) {
            $this->checkEmailStatus($sendMailLog);
        })->delay(now()->addMinutes(2));

        // Verificar estado después de 10 minutos
        dispatch(function () use ($sendMailLog) {
            $this->checkEmailStatus($sendMailLog);
        })->delay(now()->addMinutes(10));

        // Verificar estado después de 30 minutos
        dispatch(function () use ($sendMailLog) {
            $this->checkEmailStatus($sendMailLog);
        })->delay(now()->addMinutes(30));
    }

    /**
     * Verifica el estado de un email específico
     */
    public function checkEmailStatus(SendMailLogs $sendMailLog)
    {
        try {
            $service = $this->serviceInstances[$sendMailLog->service_provider] ?? null;
            
            if (!$service || !method_exists($service, 'getEmailStatus')) {
                return;
            }

            $status = $service->getEmailStatus($sendMailLog->resend_id);
            
            if ($status && $status !== $sendMailLog->status) {
                $oldStatus = $sendMailLog->status;
                $sendMailLog->updateStatus($status);
                
                $sendMailLog->addEvent([
                    'type' => 'status_updated',
                    'old_status' => $oldStatus,
                    'new_status' => $status,
                    'message' => "Estado actualizado de {$oldStatus} a {$status}"
                ]);

                Log::info("Estado de email actualizado", [
                    'send_mail_log_id' => $sendMailLog->id,
                    'old_status' => $oldStatus,
                    'new_status' => $status,
                    'service' => $sendMailLog->service_provider
                ]);
            }

        } catch (Exception $e) {
            Log::error("Error verificando estado de email", [
                'send_mail_log_id' => $sendMailLog->id,
                'service' => $sendMailLog->service_provider,
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function sendWithBrevo($service, $dataEmail, $htmlContent)
    {
        $to = [
            [
                'email' => $dataEmail['address'],
                'name' => $dataEmail['representant']->full_name ?? ''
            ]
        ];

        $cc = [];
        if (!empty($dataEmail['mailCCAddress'])) {
            $cc[] = [
                'email' => $dataEmail['mailCCAddress'],
                'name' => 'Administración'
            ];
        }

        $response = $service->sendEmail(
            $to,
            $dataEmail['subject'],
            $htmlContent,
            strip_tags($htmlContent),
            $cc
        );

        return [
            'success' => !isset($response['error']),
            'data' => $response,
            'service' => 'brevo'
        ];
    }

    protected function sendWithMailjet($service, $dataEmail, $htmlContent)
    {
        $mailData = [
            'To' => [
                [
                    'Email' => $dataEmail['address'],
                    'Name' => $dataEmail['representant']->full_name ?? ''
                ]
            ],
            'Subject' => $dataEmail['subject'],
            'HTMLPart' => $htmlContent,
            'TextPart' => strip_tags($htmlContent)
        ];

        if (!empty($dataEmail['mailCCAddress'])) {
            $mailData['Cc'] = [
                [
                    'Email' => $dataEmail['mailCCAddress'],
                    'Name' => 'Administración'
                ]
            ];
        }

        $response = $service->sendEmail($mailData);

        return [
            'success' => $response['success'],
            'data' => $response,
            'service' => 'jetmail'
        ];
    }

    protected function sendWithSendPulse($service, $dataEmail, $htmlContent)
    {
        $cc = !empty($dataEmail['mailCCAddress']) ? $dataEmail['mailCCAddress'] : null;

        $response = $service->send(
            $dataEmail['address'],
            $dataEmail['subject'],
            $htmlContent,
            null, // delayTime
            false, // queue
            $cc
        );

        return [
            'success' => $response['success'],
            'data' => $response,
            'service' => 'sendpulse'
        ];
    }

    protected function sendWithResend($service, $dataEmail, $htmlContent)
    {
        $cc = !empty($dataEmail['mailCCAddress']) ? $dataEmail['mailCCAddress'] : null;

        $response = $service->send(
            $dataEmail['address'],
            $dataEmail['subject'],
            $htmlContent,
            null, // delayTime
            false, // queue
            $cc
        );

        return [
            'success' => $response['success'],
            'data' => $response,
            'service' => 'resend'
        ];
    }

    /**
     * Método para obtener estadísticas de uso por servicio
     */
    public function getServiceStats()
    {
        $today = Carbon::today();
        $providerStats = SendMailLogs::getProviderStats($today, $today->copy()->endOfDay());
        
        return [
            'rotation_mode' => 'alternating',
            'available_services' => $this->availableServices,
            'service_limits' => $this->serviceLimits,
            'service_delays' => $this->serviceDelays,
            'service_counts' => $this->serviceCounts,
            'service_last_used' => array_map(function($time) {
                return $time->toISOString();
            }, $this->serviceLastUsed),
            'provider_stats' => $providerStats,
            'daily_totals' => [
                'total_sent' => SendMailLogs::whereDate('created_at', $today)->count(),
                'successful' => SendMailLogs::successful()->whereDate('created_at', $today)->count(),
                'failed' => SendMailLogs::failed()->whereDate('created_at', $today)->count(),
                'pending' => SendMailLogs::pending()->whereDate('created_at', $today)->count()
            ]
        ];
    }

    /**
     * Método para obtener un reporte detallado de límites y delays
     */
    public function getServiceLimitsReport()
    {
        $today = Carbon::today();
        $report = [];
        
        foreach ($this->services as $service) {
            $todayCount = $this->serviceCounts[$service];
            $remaining = $this->serviceLimits[$service] - $todayCount;
            $nextAvailable = $this->getNextAvailableTime($service, Carbon::now());
            
            $report[$service] = [
                'limit' => $this->serviceLimits[$service],
                'delay' => $this->serviceDelays[$service],
                'used_today' => $todayCount,
                'remaining' => $remaining,
                'percentage_used' => round(($todayCount / $this->serviceLimits[$service]) * 100, 2),
                'is_available' => in_array($service, $this->availableServices),
                'last_used' => $this->serviceLastUsed[$service]->toISOString(),
                'next_available' => $nextAvailable->toISOString(),
                'seconds_until_available' => max(0, $nextAvailable->diffInSeconds(Carbon::now()))
            ];
        }
        
        return $report;
    }

    /**
     * Obtiene emails que necesitan reintento
     */
    public function getEmailsNeedingRetry()
    {
        return SendMailLogs::where('status', 'failed')
            ->where('retry_count', '<', 3)
            ->where('created_at', '>', now()->subDays(1))
            ->get();
    }

    /**
     * Reintenta el envío de un email fallido
     */
    public function retryFailedEmail(SendMailLogs $sendMailLog)
    {
        if ($sendMailLog->retry_count >= 3) {
            Log::warning("Email ha excedido el máximo de reintentos", [
                'send_mail_log_id' => $sendMailLog->id
            ]);
            return false;
        }

        try {
            $sendMailLog->incrementRetryCount();
            $sendMailLog->status = 'retrying';
            $sendMailLog->save();

            $sendMailLog->addEvent([
                'type' => 'retry',
                'attempt' => $sendMailLog->retry_count,
                'message' => "Reintentando envío (intento {$sendMailLog->retry_count})"
            ]);

            // Recrear los datos del email para el reintento
            $dataEmail = [
                'view' => $this->view,
                'subject' => $sendMailLog->subject,
                'address' => $sendMailLog->to,
                'mailCCAddress' => $sendMailLog->cc[0] ?? null,
                'send_mail_log_id' => $sendMailLog->id
            ];

            // Intentar envío inmediato
            $result = $this->sendEmailWithService($sendMailLog->service_provider, $dataEmail);

            return $result['success'];

        } catch (Exception $e) {
            Log::error("Error en reintento de email", [
                'send_mail_log_id' => $sendMailLog->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Simula el cronograma de envío para preview
     */
    public function previewSendingSchedule($id, $number = null, $limit = 10)
    {
        $coll_political = CollPolitical::findOrFail($id);
        $coll_messages = $coll_political->coll_messeges->where('status', 'true');
        $representants = $coll_political->representants->take($limit);
        
        $baseTime = Carbon::now();
        $schedule = [];
        $emailCount = 0;
        
        // Reinicializar para la simulación
        $this->initializeServiceCounters();
        
        foreach ($representants as $representant) {
            foreach ($coll_messages as $coll_message) {
                if (validate_email($representant->email)) {
                    $selectedService = $this->getNextAvailableService();
                    $scheduledTime = $this->getNextAvailableTime($selectedService, $baseTime);
                    
                    $schedule[] = [
                        'email_number' => ++$emailCount,
                        'service' => $selectedService,
                        'delay' => $this->serviceDelays[$selectedService],
                        'to' => $representant->email,
                        'subject' => $number . ' ' . $coll_message->title,
                        'scheduled_time' => $scheduledTime->toISOString(),
                        'time_from_now' => $scheduledTime->diffForHumans(),
                        'service_count_after' => $this->serviceCounts[$selectedService] + 1
                    ];
                    
                    // Simular la actualización
                    $this->updateServiceLastUsed($selectedService, $scheduledTime);
                    $baseTime = $scheduledTime->copy()->addSeconds(10);
                }
            }
        }
        
        return [
            'total_emails' => count($schedule),
            'schedule' => $schedule,
            'final_service_counts' => $this->serviceCounts,
            'estimated_completion' => end($schedule)['scheduled_time'] ?? null
        ];
    }

    /**
     * Endpoint para obtener estadísticas via API
     */
    public function apiGetStats(Request $request)
    {
        try {
            $stats = $this->getServiceStats();
            $limitsReport = $this->getServiceLimitsReport();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'service_stats' => $stats,
                    'limits_report' => $limitsReport,
                    'timestamp' => now()->toISOString()
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint para preview del cronograma
     */
    public function apiPreviewSchedule(Request $request, $id)
    {
        try {
            $limit = $request->get('limit', 10);
            $number = $request->get('number', '');
            
            $preview = $this->previewSendingSchedule($id, $number, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $preview
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint para reintentar emails fallidos
     */
    public function apiRetryFailedEmails(Request $request)
    {
        try {
            $failedEmails = $this->getEmailsNeedingRetry();
            $results = [];
            
            foreach ($failedEmails as $sendMailLog) {
                $success = $this->retryFailedEmail($sendMailLog);
                $results[] = [
                    'send_mail_log_id' => $sendMailLog->id,
                    'to' => $sendMailLog->to,
                    'success' => $success
                ];
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_retried' => count($results),
                    'results' => $results
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}