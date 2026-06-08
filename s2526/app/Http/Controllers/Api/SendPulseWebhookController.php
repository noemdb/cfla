<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ResendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SendPulseWebhookController extends Controller
{
    /**
     * Maneja los webhooks de SendPulse
     */
    public function handle(Request $request)
    {
        try {
            // Validar la firma del webhook
            if (!$this->validateWebhookSignature($request)) {
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Validar los datos del webhook
            $validator = Validator::make($request->all(), [
                'event' => 'required|string',
                'email' => 'required|email',
                'message_id' => 'required|string',
                'timestamp' => 'required|integer',
                'status' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Procesar el evento
            $this->processWebhookEvent($request->all());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error en webhook de SendPulse', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Valida la firma del webhook
     */
    protected function validateWebhookSignature(Request $request): bool
    {
        $signature = $request->header('X-SendPulse-Signature');
        $payload = $request->getContent();
        $secret = config('services.sendpulse.webhook_secret');

        if (!$signature || !$secret) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Procesa el evento del webhook
     */
    protected function processWebhookEvent(array $data): void
    {
        $event = $data['event'];
        $email = $data['email'];
        $messageId = $data['message_id'];
        $status = $data['status'];

        // Buscar el email en la base de datos
        $resendEmail = ResendEmail::where('resend_id', $messageId)
            ->orWhere('to', $email)
            ->first();

        if (!$resendEmail) {
            Log::warning('Email no encontrado en la base de datos', [
                'message_id' => $messageId,
                'email' => $email
            ]);
            return;
        }

        // Actualizar el estado según el evento
        $newStatus = $this->mapEventToStatus($event, $status);
        if ($newStatus) {
            $resendEmail->status = $newStatus;
            $resendEmail->save();

            Log::info('Estado de email actualizado', [
                'email_id' => $resendEmail->id,
                'old_status' => $resendEmail->getOriginal('status'),
                'new_status' => $newStatus,
                'event' => $event
            ]);
        }
    }

    /**
     * Mapea el evento de SendPulse a un estado en la base de datos
     */
    protected function mapEventToStatus(string $event, string $status): ?string
    {
        return match ($event) {
            'delivered' => 'delivered',
            'opened' => 'opened',
            'clicked' => 'clicked',
            'bounced' => 'bounced',
            'complained' => 'complained',
            'unsubscribed' => 'unsubscribed',
            'rejected' => 'rejected',
            default => null
        };
    }
}