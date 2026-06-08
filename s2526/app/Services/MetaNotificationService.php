<?php

namespace App\Services;

use App\Models\app\Enrollment\Catchment;
use App\Models\app\Institucion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Response;
use Exception;

class MetaNotificationService
{
    /**
     * Send a template message via Meta/Facebook API.
     * 
     * @param string $recipientId Recipient identifier (CI)
     * @param string $phoneNumber Recipient phone number
     * @param FacebookTokenService $tokenService Service to handle Facebook tokens
     * @param string|null $messageText Optional custom message text
     * @param string $templateName Optional template name (defaults to general template)
     * @param array $templateParams Optional template parameters (overrides default parameters)
     * @return bool Success status
     */
    public function sendTemplateMessage(
        string $recipientId,
        string $phoneNumber,
        FacebookTokenService $tokenService,
        ?string $messageText = null,
        string $templateName = 'general',
        array $templateParams = []
    ): bool {
        try {
            // Get the catchment data
            $catchment = $this->getCatchmentData($recipientId);
            
            if (!$catchment) {
                Log::error("Meta notification failed: Catchment not found for CI: {$recipientId}");
                return false;
            }
            
            // Get access token from the token service
            $accessToken = $tokenService->getAccessToken();
            
            if (empty($accessToken)) {
                Log::error('Meta notification failed: No access token available');
                return false;
            }
            
            // Get media ID for header image
            $institucion = Institucion::firstOrFail();
            $mediaId = ($institucion) ? $institucion->facebook_media_id_control : env('FACEBOOK_IMAGE_ACADEMIC_ID');
            
            // Use provided template parameters or generate default ones with the message text
            $bodyParameters = !empty($templateParams) 
                ? $templateParams 
                : $this->getDefaultParameters($catchment, $messageText);
            
            // Prepare the message payload
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $phoneNumber ?: '584145752242', // Fallback number if provided is empty
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => 'es_ES'],
                    'components' => [
                        [
                            'type' => 'header',
                            'parameters' => [['type' => 'image', 'image' => ['id' => $mediaId]]],
                        ],
                        [
                            'type' => 'body',
                            'parameters' => $bodyParameters,
                        ],
                    ],
                ],
            ];
            
            // Send the request
            $response = $this->sendRequest($accessToken, $payload);
            
            // Process the response
            return $this->processResponse($response, $recipientId, $phoneNumber);
            
        } catch (Exception $e) {
            Log::error('Error sending Meta notification: ' . $e->getMessage(), [
                'recipient_id' => $recipientId,
                'phone' => $phoneNumber,
                'exception' => $e
            ]);
            
            return false;
        }
    }
    
    /**
     * Send a census appointment reminder message.
     * This method implements the specific functionality from sendMessegeMetaTemplateGeneral.
     * 
     * @param FacebookTokenService $tokenService
     * @param string $ident Recipient identifier (CI)
     * @param string $phone Recipient phone number
     * @param string|null $messageText Optional custom message text
     * @return array Response data with success status and message
     */
    public function sendCensusAppointmentReminder(
        FacebookTokenService $tokenService,
        string $ident,
        string $phone,
        ?string $messageText = null
    ): array {
        try {
            // Validate identifier and get catchment data
            $catchment = Catchment::where('representant_ci', $ident)->firstOrFail();
    
            // Format recipient name with asterisks for bold formatting in WhatsApp
            $name = '*' . $catchment->representant_name . ' ' . $catchment->representant_lastname . '*';
            
            // Use provided message text or generate default message
            if ($messageText === null) {        
                // Construct the default message
                $messageText = "Nuestros servicios mejoran cada día.";
            }
            
            $name = $this->cleanText($name);
            $messageText = $this->cleanText($messageText);
    
            // Prepare template parameters
            $templateParams = [
                ['type' => 'text', 'text' => $name],
                ['type' => 'text', 'text' => $messageText],
            ];
            
            // Send the template message
            $success = $this->sendTemplateMessage($ident, $phone, $tokenService, $messageText, 'general', $templateParams);
            
            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Notificación enviada con éxito.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al enviar la notificación.'
                ];
            }
            
        } catch (Exception $e) {
            Log::error('Excepción al enviar notificación:', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error interno del servidor.',
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Get catchment data for the given recipient ID.
     *
     * @param string $recipientId
     * @return Catchment|null
     */
    protected function getCatchmentData(string $recipientId): ?Catchment
    {
        return Catchment::where('representant_ci', $recipientId)->first();
    }
    
    /**
     * Get default template parameters based on catchment data and optional message text.
     *
     * @param Catchment $catchment
     * @param string|null $messageText Optional custom message text
     * @return array
     */
    protected function getDefaultParameters(Catchment $catchment, ?string $messageText = null): array
    {
        $name = '*' . $catchment->representant_name . ' ' . $catchment->representant_lastname . '*';
        $messageText = ($messageText === null) ? "Nuestros servicios mejoran cada día." : $messageText ;
        
        $name = $this->cleanText($name);
        $messageText = $this->cleanText($messageText);
        
        return [
            ['type' => 'text', 'text' => $name],
            ['type' => 'text', 'text' => $messageText],
        ];
    }
    
    /**
     * Send the request to the Meta API.
     *
     * @param string $accessToken
     * @param array $payload
     * @return Response
     */
    protected function sendRequest(string $accessToken, array $payload): Response
    {
        // Get API URL from environment
        $url = env('FACEBOOK_URL');
        
        // Send the request
        return Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $payload);
    }
    
    /**
     * Process the API response.
     *
     * @param Response $response
     * @param string $recipientId
     * @param string $phoneNumber
     * @return bool
     */
    protected function processResponse(Response $response, string $recipientId, string $phoneNumber): bool
    {
        if ($response->successful()) {
            Log::info('Notificación enviada con éxito', [
                'recipient_id' => $recipientId,
                'phone' => $phoneNumber,
                'response' => $response->json()
            ]);
            return true;
        } else {
            Log::error('Error al enviar notificación:', [
                'recipient_id' => $recipientId,
                'phone' => $phoneNumber,
                'response' => $response->json()
            ]);
            return false;
        }
    }
    
    /**
     * Clean text by removing line breaks, tabs, and reducing consecutive spaces.
     *
     * @param string $text
     * @return string
     */
    protected function cleanText(string $text): string
    {
        // Eliminar saltos de línea, tabulaciones y reducir espacios consecutivos
        $text = str_replace(["\n", "\t"], ' ', $text);
        $text = preg_replace('/\s{5,}/', '    ', $text);
        return trim($text);
    }
}