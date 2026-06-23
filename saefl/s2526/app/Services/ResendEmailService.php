<?php

namespace App\Services;

use App\Models\ResendEmail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Jobs\Email\Resend\ProcessResendEmail;
use InvalidArgumentException;

class ResendEmailService
{
    protected $url;
    protected $apiKey;
    protected $from;
    protected const DEFAULT_URL = 'https://api.resend.com/emails';

    public function __construct()
    {
        // Asegurarnos de que la URL nunca sea null
        $this->url = config('services.resend.url') ?: self::DEFAULT_URL;
        $this->apiKey = config('services.resend.api_key');
        $this->from = config('services.resend.from');

        if (!$this->apiKey || !$this->from) {
            $missing = [];
            if (!$this->apiKey) $missing[] = 'RESEND_API_KEY';
            if (!$this->from) $missing[] = 'RESEND_FROM';

            throw new InvalidArgumentException(
                'Configuración de Resend incompleta. Por favor verifica en tu archivo .env: ' . implode(', ', $missing)
            );
        }
    }

    /**
     * Registra errores en el log de manera consistente
     */
    protected function logError(string $action, array $data = []): void
    {
        Log::error('ResendEmailService: ' . $action, $data);
    }

    /**
     * Envía un correo electrónico usando la API de Resend o lo programa en la queue
     *
     * @param string $to
     * @param string $subject
     * @param string $htmlContent
     * @param Carbon|null $delayTime
     * @param bool $queue Si es true, usa la queue
     * @param string|array|null $cc Dirección o array de direcciones de correo para copia
     * @param string|array|null $bcc Dirección o array de direcciones de correo para copia oculta
     * @return array
     */
    public function send(string $to, string $subject, string $htmlContent, ?Carbon $delayTime = null, bool $queue = false, string|array|null $cc = null, string|array|null $bcc = null): array
    {
        if ($queue) {
            // Si se solicita queue, despachar el job
            $dataEmail = [
                'html' => $htmlContent,
                'subject' => $subject,
                'address' => $to,
                'cc' => $cc,
                'bcc' => $bcc,
            ];
            $job = ProcessResendEmail::dispatch($dataEmail);
            if ($delayTime) {
                $job->delay($delayTime);
            }
            return [
                'success' => true,
                'message' => 'Correo programado en la queue',
                'queue' => true,
                'scheduled_time' => $delayTime ? $delayTime->format('Y-m-d H:i:s') : null
            ];
        }

        // Envío inmediato
        try {
            if (!$this->apiKey || !$this->from) {
                throw new InvalidArgumentException('Configuración de Resend incompleta');
            }

            // Asegurarnos de que la URL sea válida
            $url = trim($this->url);
            if (empty($url)) {
                $url = self::DEFAULT_URL;
            }

            $response = Http::withToken($this->apiKey)
                ->post($url, [
                    'from' => $this->from,
                    'to' => $to,
                    'subject' => $subject,
                    'html' => $htmlContent,
                    'cc' => $cc,
                    'bcc' => $bcc,
                ]);

            if ($response->successful()) {
                return $this->handleSuccessfulResponse($response, $to, $subject, $htmlContent, $cc, $bcc, $delayTime);
            }

            $errorMsg = $response->json('error.message') ?? $response->body();
            if (str_contains($errorMsg, 'limit')) {
                $errorMsg = 'Has superado el límite de envíos del plan gratuito de Resend.';
            }

            return [
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $errorMsg,
                'data' => $response->json()
            ];
        } catch (\Exception $e) {
            $this->logError('Excepción al enviar email', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
                'scheduled' => $delayTime ? true : false,
                'scheduled_time' => $delayTime ? $delayTime->format('Y-m-d H:i:s') : null
            ]);
            return [
                'success' => false,
                'message' => 'Error al enviar el correo: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Maneja la respuesta exitosa del envío de correo
     *
     * @param \Illuminate\Http\Client\Response $response
     * @param string $to
     * @param string $subject
     * @param string $htmlContent
     * @param string|array|null $cc
     * @param string|array|null $bcc
     * @param Carbon|null $delayTime
     * @return array
     */
    protected function handleSuccessfulResponse($response, string $to, string $subject, string $htmlContent, $cc, $bcc, ?Carbon $delayTime): array
    {
        $responseData = $response->json();

        // Guardar el email en la base de datos
        $email = ResendEmail::create([
            'resend_id' => $responseData['id'],
            'from' => $this->from,
            'to' => $to,
            'subject' => $subject,
            'html' => $htmlContent,
            'cc' => $cc,
            'bcc' => $bcc,
            'status' => $delayTime ? 'scheduled' : 'sent',
            'sent_at' => $delayTime ? null : now()
        ]);

        // Programar 3 jobs para actualizar el estado del email
        if (!$delayTime) {
            // Primer job: 1 minuto después del envío
            dispatch(function () use ($email) {
                $this->getEmailStatus($email->resend_id);
            })->delay(now()->addMinute());

            // Segundo job: 5 minutos después del envío
            dispatch(function () use ($email) {
                $this->getEmailStatus($email->resend_id);
            })->delay(now()->addMinutes(5));

            // Tercer job: 15 minutos después del envío
            dispatch(function () use ($email) {
                $this->getEmailStatus($email->resend_id);
            })->delay(now()->addMinutes(15));
        }

        return [
            'success' => true,
            'message' => $delayTime
                ? 'Correo programado para envío exitosamente'
                : 'Correo enviado exitosamente',
            'data' => $responseData,
            'scheduled' => $delayTime ? true : false,
            'scheduled_time' => $delayTime ? $delayTime->format('Y-m-d H:i:s') : null
        ];
    }

    /**
     * Envía múltiples correos con retraso incremental
     *
     * @param array $emails Array de correos a enviar [['to' => string, 'subject' => string, 'html' => string]]
     * @param int $initialDelay Segundos de retraso inicial
     * @param int $incrementDelay Segundos de incremento entre cada correo
     * @return array
     */
    public function sendBulkWithDelay(array $emails, int $initialDelay = 60, int $incrementDelay = 40): array
    {
        $results = [];
        $currentDelay = $initialDelay;

        foreach ($emails as $email) {
            $delayTime = Carbon::now()->addSeconds($currentDelay);
            $result = $this->send(
                $email['to'],
                $email['subject'],
                $email['html'],
                $delayTime
            );

            $results[] = [
                'email' => $email['to'],
                'scheduled_time' => $delayTime->format('Y-m-d H:i:s'),
                'result' => $result
            ];

            $currentDelay += $incrementDelay;
        }

        return [
            'success' => true,
            'message' => 'Proceso de envío masivo iniciado',
            'data' => $results
        ];
    }

    /**
     * Obtiene el estado de un email enviado
     *
     * @param string $messageId ID del mensaje
     * @return string|null Estado del email o null si no se puede obtener
     */
    public function getEmailStatus(string $messageId): ?string
    {
        try {
            if (!$this->apiKey) {
                throw new InvalidArgumentException('Configuración de Resend incompleta');
            }

            // Asegurarnos de que la URL sea válida
            $url = trim($this->url);
            if (empty($url)) {
                $url = self::DEFAULT_URL;
            }

            // Obtener el estado del email
            $response = Http::withToken($this->apiKey)
                ->get("{$url}/{$messageId}");

            if (!$response->successful()) {
                $this->logError('Error al obtener estado del email', [
                    'message_id' => $messageId,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return null;
            }

            $data = $response->json();

            // Mapear el estado de Resend a nuestro formato
            return match ($data['status'] ?? '') {
                'delivered' => 'delivered',
                'opened' => 'opened',
                'clicked' => 'clicked',
                'bounced' => 'bounced',
                'complained' => 'complained',
                'unsubscribed' => 'unsubscribed',
                'rejected' => 'rejected',
                'sent' => 'sent',
                'pending' => 'pending',
                default => null
            };
        } catch (\Exception $e) {
            $this->logError('Error al obtener estado del email', [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
