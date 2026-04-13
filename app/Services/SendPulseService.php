<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class SendPulseService
{
    protected ?string $clientId;
    protected ?string $clientSecret;
    protected ?string $fromEmail;
    protected ?string $fromName;
    protected ?string $oauthUrl;

    public function __construct()
    {
        $this->clientId     = trim(config('services.sendpulse.client_id'));
        $this->clientSecret = trim(config('services.sendpulse.client_secret'));
        $this->fromEmail    = trim(config('services.sendpulse.from_email'));
        $this->fromName     = trim(config('services.sendpulse.from_name'));
        $this->oauthUrl     = config('services.sendpulse.oauth_url', 'https://api.sendpulse.com/oauth/access_token');
    }

    /**
     * Obtiene el token de autenticación de SendPulse.
     * Utiliza caché para minimizar peticiones innecesarias.
     *
     * @return string
     * @throws Exception
     */
    protected function getToken(): string
    {
        // Caché del token por 55 minutos (su vigencia máxima es generalmente de 1 hora o 3600 segundos)
        return Cache::remember('sendpulse_access_token', 3300, function () {
            if (empty($this->clientId) || empty($this->clientSecret)) {
                throw new Exception('Configuración de SendPulse incompleta: clientId o clientSecret no definidos.');
            }

            $response = Http::asJson()->acceptJson()->post($this->oauthUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if (!$response->successful()) {
                Log::error('SendPulse Auth Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new Exception('Error obteniendo token de acceso de SendPulse: ' . $response->body());
            }

            return $response->json('access_token');
        });
    }

    /**
     * Envía un email a través del API SMTP de SendPulse.
     *
     * @param string $to Email de destino
     * @param string $subject Asunto del correo
     * @param string $htmlBody Contenido en HTML del correo
     * @param array $cc Arreglo de emails para copia (CC)
     * @param array $bcc Arreglo de emails para copia oculta (BCC)
     * @return bool True si es exitoso
     * @throws Exception
     */
    public function sendEmail(string $to, string $subject, string $htmlBody, array $cc = [], array $bcc = []): bool
    {
        try {
            $token = $this->getToken();

            $emailData = [
                'html' => base64_encode($htmlBody),
                'subject' => $subject,
                'from' => [
                    'name' => $this->fromName,
                    'email' => $this->fromEmail
                ],
                // El arreglo 'to' requiere objetos asociativos con la clave 'email'
                'to' => [
                    ['email' => $to]
                ]
            ];

            // Añadir CC si existe, validando que no esté vacío
            $cc = array_filter($cc);
            if (!empty($cc)) {
                $emailData['cc'] = array_map(function ($email) { return ['email' => $email]; }, $cc);
            }

            // Añadir BCC si existe, validando que no esté vacío
            $bcc = array_filter($bcc);
            if (!empty($bcc)) {
                $emailData['bcc'] = array_map(function ($email) { return ['email' => $email]; }, $bcc);
            }

            // Enviar correo
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ])->post('https://api.sendpulse.com/smtp/emails', [
                'email' => $emailData
            ]);

            $jsonResponse = $response->json();

            // Evaluar errores arrojados en el cuerpo o peticiones incorrectas
            if (!$response->successful() || isset($jsonResponse['is_error'])) {
                Log::error('SendPulse Email Transaction Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $emailData
                ]);
                throw new Exception('Error enviando email via SendPulse: ' . $response->body());
            }

            return true;
        } catch (Exception $e) {
            Log::error('SendPulse Service Exception', [
                'message' => $e->getMessage()
            ]);
            // Re-lanzamos la excepción para el manejo de la interfaz de usuario posterior
            throw $e;
        }
    }
}
