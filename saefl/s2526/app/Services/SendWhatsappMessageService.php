<?php

namespace App\Services;

use App\Jobs\SendWhatsappNotificationJob;
use Illuminate\Support\Facades\Http;
use App\Services\FacebookTokenService;
use Exception;

class SendWhatsappMessageService
{
    protected FacebookTokenService $facebookTokenService;

    public function __construct(FacebookTokenService $facebookTokenService)
    {
        $this->facebookTokenService = $facebookTokenService;
    }

    /**
     * Envía un mensaje de WhatsApp usando un template con parámetros dinámicos.
     *
     * @param string      $to           Número de destino (E.164)
     * @param string      $templateName Nombre del template (ej: mensaje_generico)
     * @param array       $bodyParams   Array de textos que corresponden a {{1}}, {{2}}, ..., {{N}}
     * @param string|null $mediaId      Id de imagen usada en el header (null si el template no tiene header)
     * @param string      $language     Código de idioma (ej: es_ES)
     *
     * @return array
     * @throws Exception
     */
    public function sendDynamicTemplate(
        string $to,
        string $templateName,
        array $bodyParams,
        ?string $mediaId = null,
        string $language = 'es_ES',
        bool $useQueue = false    // ⬅️ por defecto de síncrono, sin usar jobsQueue
    ): array {

        if ($useQueue) {
            // se encola el Job y retornamos inmediatamente
            SendWhatsappNotificationJob::dispatch($to, $templateName, $bodyParams, $mediaId);
            return ['queued' => true];
        }

        // 1️⃣ Obtener access token válido
        $accessToken = $this->facebookTokenService->getAccessToken();

        // 2️⃣ Convertir los textos a objetos del type 'text'
        $bodyParameterObjects = [];
        foreach ($bodyParams as $text) {
            $bodyParameterObjects[] = [
                'type' => 'text',
                'text' => $text,
            ];
        }

        // 3️⃣ Armar componentes
        $components = [];

        // Header si se envía mediaId
        if ($mediaId) {
            $components[] = [
                'type' => 'header',
                'parameters' => [
                    [
                        'type'  => 'image',
                        'image' => ['id' => $mediaId],
                    ],
                ],
            ];
        }

        // Body
        $components[] = [
            'type'       => 'body',
            'parameters' => $bodyParameterObjects,
        ];

        // 4️⃣ Payload final
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name'     => $templateName,
                'language' => ['code' => $language],
                'components' => $components,
            ],
        ];

        $url = env('FACEBOOK_URL');

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);

        if (! $response->successful()) {
            throw new Exception('Error al enviar el template: ' . $response->body());
        }

        return $response->json();
    }
}
