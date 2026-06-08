<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResendWebhookController extends Controller
{
    /**
     * Mapeo de eventos de Resend a estados de nuestra aplicación
     */
    protected const STATUS_MAP = [
        'email.sent' => 'sent',
        'email.delivered' => 'delivered',
        'email.opened' => 'opened',
        'email.clicked' => 'clicked',
        'email.bounced' => 'bounced',
        'email.complained' => 'complained',
        'email.delivered_delayed' => 'delivered_delayed',
        'email.delivery_delayed' => 'delivery_delayed'
    ];

    /**
     * Maneja los webhooks de Resend
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request)
    {
        try {
            $payload = $request->getContent();
            $data = json_decode($payload, true);

            if (!$data) {
                throw new \Exception('Payload inválido');
            }

            $this->logWebhook('Webhook recibido', ['payload' => $data]);

            // Verificar la firma del webhook
            if (!$this->verifyWebhookSignature($request, $payload)) {
                $this->logWebhook('Firma inválida', ['payload' => $data], 'warning');
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Validar que el evento sea uno que conocemos
            if (!isset($data['type']) || !array_key_exists($data['type'], self::STATUS_MAP)) {
                $this->logWebhook('Tipo de evento desconocido', ['type' => $data['type']], 'warning');
                return response()->json(['error' => 'Unknown event type'], 400);
            }

            $email = ResendEmail::where('resend_id', $data['id'])->first();

            if (!$email) {
                $this->logWebhook('Email no encontrado', ['resend_id' => $data['id']], 'warning');
                return response()->json(['error' => 'Email not found'], 404);
            }

            // Obtener el estado mapeado
            $status = self::STATUS_MAP[$data['type']];
            $timestamp = $data['created_at'] ?? now();

            // Actualizar el estado del email
            $email->updateStatus($status, $timestamp);

            // Agregar el evento al historial
            $email->addEvent([
                'type' => $data['type'],
                'status' => $status,
                'timestamp' => $timestamp,
                'data' => $data
            ]);

            $this->logWebhook('Estado actualizado exitosamente', [
                'email_id' => $email->id,
                'resend_id' => $email->resend_id,
                'old_status' => $email->getOriginal('status'),
                'new_status' => $status
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            $this->logWebhook('Error procesando webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ], 'error');

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Verifica la firma del webhook
     *
     * @param Request $request
     * @param string $payload
     * @return bool
     */
    private function verifyWebhookSignature(Request $request, string $payload): bool
    {
        $secret = config('services.resend.webhook_secret');
        $signatureHeader = $request->header('Resend-Signature');

        if (!$signatureHeader || !$secret) {
            $this->logWebhook('Falta firma o secreto', [
                'has_signature' => !empty($signatureHeader),
                'has_secret' => !empty($secret)
            ], 'warning');
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signatureHeader);
    }

    /**
     * Registra información del webhook en el log
     *
     * @param string $message
     * @param array $context
     * @param string $level
     * @return void
     */
    private function logWebhook(string $message, array $context = [], string $level = 'info'): void
    {
        $logMessage = 'Resend Webhook: ' . $message;

        switch ($level) {
            case 'error':
                Log::error($logMessage, $context);
                break;
            case 'warning':
                Log::warning($logMessage, $context);
                break;
            default:
                Log::info($logMessage, $context);
        }
    }
}
