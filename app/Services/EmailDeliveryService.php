<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Entrega de emails transaccionales con estrategia en cascada:
 *
 *  1º SendPulse (API SMTP existente en SendPulseService).
 *  2º Resend (API REST directa, config/services.php#resend).
 *
 * Garantías:
 *  - NUNCA lanza excepciones al llamador: ante un fallo total se loguea
 *    (destinatario enmascarado) y se retorna un resultado de fallo para que
 *    la UI muestre un mensaje amigable ("situación inesperada").
 *  - Si SendPulse no está configurado se salta directo a Resend.
 *  - Respeta el modo tester (MAIL_MODE_TESTER=true) redirigiendo el correo
 *    al buzón de pruebas (MAIL_ADDRESS_TESTER) para no enviar a alumnos reales
 *    durante desarrollo.
 *
 * @method array{success: bool, channel: string|null, error: string|null} send(string $to, string $subject, string $htmlBody)
 */
class EmailDeliveryService
{
    public function __construct(
        protected ?SendPulseService $sendPulse = null,
    ) {
        $this->sendPulse ??= new SendPulseService;
    }

    public function send(string $to, string $subject, string $htmlBody): array
    {
        $recipient = $this->resolveRecipient($to);

        $attempts = [
            'sendpulse' => fn () => $this->trySendPulse($recipient, $subject, $htmlBody),
            'resend' => fn () => $this->tryResend($recipient, $subject, $htmlBody),
        ];

        foreach ($attempts as $channel => $attempt) {
            if (! $this->channelAvailable($channel)) {
                continue;
            }

            $result = $attempt();

            if ($result['success']) {
                return $result;
            }

            Log::warning('EmailDeliveryService: falló '.$channel, [
                'to' => $this->mask($recipient),
                'error' => $result['error'],
            ]);
        }

        Log::error('EmailDeliveryService: entrega fallida en todos los canales', [
            'to' => $this->mask($recipient),
            'subject' => $subject,
        ]);

        return ['success' => false, 'channel' => null, 'error' => 'No se pudo enviar el correo por ningún canal.'];
    }

    protected function channelAvailable(string $channel): bool
    {
        return match ($channel) {
            'sendpulse' => (bool) config('services.sendpulse.client_id')
                && (bool) config('services.sendpulse.client_secret'),
            'resend' => (bool) config('services.resend.api_key'),
            default => false,
        };
    }

    protected function trySendPulse(string $to, string $subject, string $htmlBody): array
    {
        try {
            $this->sendPulse->sendEmail($to, $subject, $htmlBody);

            return ['success' => true, 'channel' => 'sendpulse', 'error' => null];
        } catch (\Throwable $e) {
            return ['success' => false, 'channel' => 'sendpulse', 'error' => $e->getMessage()];
        }
    }

    protected function tryResend(string $to, string $subject, string $htmlBody): array
    {
        try {
            $response = Http::withToken(config('services.resend.api_key'))
                ->acceptJson()
                ->post(config('services.resend.url', 'https://api.resend.com/emails'), [
                    'from' => trim(config('services.resend.from') ?: config('mail.from.address')),
                    'to' => [$to],
                    'subject' => $subject,
                    'html' => $htmlBody,
                ]);

            if (! $response->successful()) {
                return ['success' => false, 'channel' => 'resend', 'error' => $response->body()];
            }

            return ['success' => true, 'channel' => 'resend', 'error' => null];
        } catch (\Throwable $e) {
            return ['success' => false, 'channel' => 'resend', 'error' => $e->getMessage()];
        }
    }

    protected function resolveRecipient(string $to): string
    {
        if (config('mail.mode_tester') && config('mail.address_tester')) {
            return (string) config('mail.address_tester');
        }

        return $to;
    }

    protected function mask(string $value): string
    {
        $value = (string) $value;

        if (strlen($value) < 4) {
            return '***';
        }

        return substr($value, 0, 2).str_repeat('*', max(strlen($value) - 4, 1)).substr($value, -2);
    }
}
