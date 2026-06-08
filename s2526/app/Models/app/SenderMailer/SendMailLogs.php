<?php

namespace App\Models\app\SenderMailer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendMailLogs extends Model
{
    use HasFactory;

    protected $table = 'send_mail_logs';

    protected $fillable = [
        'resend_id',
        'service_provider',
        'from',
        'to',
        'subject',
        'html',
        'text',
        'cc',
        'bcc',
        'status',
        'events',
        'sent_at',
        'delivered_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'complained_at',
        'unsubscribed_at',
        'rejected_at',
        'error_message',
        'retry_count',
        'collection_political_id',
        'representant_ci',
        'message_type',
        'scheduled_at',
        'response_data'
    ];

    protected $casts = [
        'cc' => 'array',
        'bcc' => 'array',
        'events' => 'array',
        'response_data' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
        'complained_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'rejected_at' => 'datetime',
        'scheduled_at' => 'datetime'
    ];

    /**
     * Actualiza el estado del email y el timestamp correspondiente
     */
    public function updateStatus($status, $timestamp = null)
    {
        $this->status = $status;
        $timestamp = $timestamp ?? now();

        switch ($status) {
            case 'sent':
                $this->sent_at = $timestamp;
                break;
            case 'delivered':
                $this->delivered_at = $timestamp;
                break;
            case 'opened':
                $this->opened_at = $timestamp;
                break;
            case 'clicked':
                $this->clicked_at = $timestamp;
                break;
            case 'bounced':
                $this->bounced_at = $timestamp;
                break;
            case 'complained':
                $this->complained_at = $timestamp;
                break;
            case 'unsubscribed':
                $this->unsubscribed_at = $timestamp;
                break;
            case 'rejected':
                $this->rejected_at = $timestamp;
                break;
        }

        $this->save();
    }

    /**
     * Agrega un evento al historial de eventos
     */
    public function addEvent($event)
    {
        $events = $this->events ?? [];
        $event['timestamp'] = $event['timestamp'] ?? now()->toISOString();
        $events[] = $event;
        $this->events = $events;
        $this->save();
    }

    /**
     * Incrementa el contador de reintentos
     */
    public function incrementRetryCount()
    {
        $this->retry_count = ($this->retry_count ?? 0) + 1;
        $this->save();
    }

    /**
     * Establece un mensaje de error
     */
    public function setError($errorMessage)
    {
        $this->error_message = $errorMessage;
        $this->status = 'failed';
        $this->save();
    }

    /**
     * Guarda la respuesta del proveedor de email
     */
    public function saveResponse($responseData)
    {
        $this->response_data = $responseData;
        $this->save();
    }

    /**
     * Verifica si el email fue enviado exitosamente
     */
    public function wasSuccessful(): bool
    {
        return in_array($this->status, ['sent', 'delivered', 'opened', 'clicked']);
    }

    /**
     * Verifica si el email falló
     */
    public function hasFailed(): bool
    {
        return in_array($this->status, ['failed', 'bounced', 'rejected']);
    }

    /**
     * Verifica si el email está pendiente
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'scheduled']);
    }

    /**
     * Obtiene el tiempo transcurrido desde el envío
     */
    public function getTimeSinceSent()
    {
        if (!$this->sent_at) {
            return null;
        }
        return $this->sent_at->diffForHumans();
    }

    /**
     * Obtiene estadísticas del email
     */
    public function getStats(): array
    {
        return [
            'status' => $this->status,
            'service_provider' => $this->service_provider,
            'sent_at' => $this->sent_at?->toISOString(),
            'delivered_at' => $this->delivered_at?->toISOString(),
            'opened_at' => $this->opened_at?->toISOString(),
            'clicked_at' => $this->clicked_at?->toISOString(),
            'retry_count' => $this->retry_count ?? 0,
            'has_error' => !empty($this->error_message),
            'time_since_sent' => $this->getTimeSinceSent()
        ];
    }

    /**
     * Scope para filtrar por proveedor de servicio
     */
    public function scopeByProvider($query, $provider)
    {
        return $query->where('service_provider', $provider);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para emails enviados en un rango de fechas
     */
    public function scopeSentBetween($query, $startDate, $endDate)
    {
        return $query->whereBetween('sent_at', [$startDate, $endDate]);
    }

    /**
     * Scope para emails exitosos
     */
    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', ['sent', 'delivered', 'opened', 'clicked']);
    }

    /**
     * Scope para emails fallidos
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'bounced', 'rejected']);
    }

    /**
     * Scope para emails pendientes
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'scheduled']);
    }

    /**
     * Relación con CollPolitical (si existe)
     */
    public function collPolitical()
    {
        return $this->belongsTo(\App\Models\app\Cobranzas\CollPolitical::class, 'collection_political_id');
    }

    /**
     * Método estático para crear un registro de email desde el controller
     */
    public static function createFromController($dataEmail, $service, $response = null)
    {
        return self::create([
            'resend_id' => $response['id'] ?? uniqid($service . '_'),
            'service_provider' => $service,
            'from' => config("services.{$service}.from") ?? config("services.{$service}.default_sender.email"),
            'to' => $dataEmail['address'],
            'subject' => $dataEmail['subject'],
            'html' => view($dataEmail['view'], $dataEmail)->render(),
            'text' => strip_tags(view($dataEmail['view'], $dataEmail)->render()),
            'cc' => !empty($dataEmail['mailCCAddress']) ? [$dataEmail['mailCCAddress']] : null,
            'bcc' => null,
            'status' => 'pending',
            'collection_political_id' => $dataEmail['collection_political_id'] ?? null,
            'representant_ci' => $dataEmail['representant']->ci_representant ?? null,
            'message_type' => 'collection_notice',
            'scheduled_at' => now(),
            'response_data' => $response,
            'retry_count' => 0
        ]);
    }

    /**
     * Obtiene estadísticas por proveedor
     */
    public static function getProviderStats($dateFrom = null, $dateTo = null)
    {
        $query = self::query();
        
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        }

        return $query->selectRaw('
            service_provider,
            COUNT(*) as total,
            SUM(CASE WHEN status IN ("sent", "delivered", "opened", "clicked") THEN 1 ELSE 0 END) as successful,
            SUM(CASE WHEN status IN ("failed", "bounced", "rejected") THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN status IN ("pending", "scheduled") THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = "opened" THEN 1 ELSE 0 END) as opened,
            SUM(CASE WHEN status = "clicked" THEN 1 ELSE 0 END) as clicked,
            AVG(retry_count) as avg_retries
        ')
        ->groupBy('service_provider')
        ->get();
    }

    /**
     * Obtiene emails que necesitan actualización de estado
     */
    public static function getNeedingStatusUpdate($minutes = 60)
    {
        return self::where('status', 'sent')
            ->where('sent_at', '>', now()->subDays(7)) // Solo emails de los últimos 7 días
            ->where('sent_at', '<', now()->subMinutes($minutes)) // Que fueron enviados hace más de X minutos
            ->whereNull('delivered_at') // Que no tienen confirmación de entrega
            ->get();
    }
}
