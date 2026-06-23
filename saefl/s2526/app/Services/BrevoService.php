<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class BrevoService
{
    protected $client;
    protected $apiKey;
    protected $defaultSender;

    public function __construct()
    {
        $this->apiKey = config('services.brevo.api_key'); 
        $this->defaultSender = config('services.brevo.default_sender');

        $this->client = new Client([
            'base_uri' => 'https://api.brevo.com/v3/', 
            'headers' => [
                'accept' => 'application/json',
                'content-type' => 'application/json',
                'api-key' => $this->apiKey, // <-- debe ser exactamente "api-key"
            ],
        ]);
    }

    /**
     * Envía un correo transaccional
     *
     * @param array $to [ ['email' => 'user@example.com', 'name' => 'Nombre'] ]
     * @param string $subject
     * @param string $htmlContent
     * @param string|null $textContent
     * @param array|null $cc
     * @param array|null $bcc
     * @param array|null $attachments
     * @return array
     */
    public function sendEmail(
        array $to,
        string $subject,
        string $htmlContent,
        ?string $textContent = null,
        ?array $cc = [],
        ?array $bcc = [],
        ?array $attachments = []
    ): array {
        try {
            $payload = [
                'sender' => $this->defaultSender,
                'to' => $to,
                'subject' => $subject,
                'htmlContent' => $htmlContent,
            ];

            if ($textContent) {
                $payload['textContent'] = $textContent;
            }

            if (!empty($cc)) {
                $payload['cc'] = $cc;
            }

            if (!empty($bcc)) {
                $payload['bcc'] = $bcc;
            }

            if (!empty($attachments)) {
                $payload['attachment'] = $attachments;
            }

            $response = $this->client->post('smtp/email', [
                'json' => $payload,
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error("Error sending email via Brevo: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
}