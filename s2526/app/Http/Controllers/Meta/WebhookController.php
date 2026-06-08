<?php

namespace App\Http\Controllers\Meta;

use App\Http\Controllers\Controller;
use App\Models\app\Meta\WebhookLog;
use App\Models\app\Meta\WebhookMessage;
use App\Models\app\Pescolar\Lapso;
use App\Services\FacebookTokenService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\QwenService;

class WebhookController extends Controller
{
    use webHookSendPDFTrait;
    use webHookDefaultTrait;
    use GeminiChatHookTrait;
    use QwenChatHookTrait;
    use validateOptionsTrait;

    public $context;

    // Verificación del webhook
    public function verifyWebhook(Request $request)
    {
        $verifyToken = env("FACEBOOK_MY_VERIFY_TOKEN", "my_verify_token");

        $eventName = $request->input('object', 'unknown_event');
        WebhookLog::create([
            'event_name' => $eventName,
            'payload' => json_encode($request, JSON_PRETTY_PRINT),
        ]);

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === $verifyToken) {
            return response($challenge, 200);
        }

        // Registro para depuración
        Log::warning("Solicitud GET no válida al webhook", [
            'hub_mode' => $mode,
            'hub_verify_token' => $token,
            'ip' => request()->ip(),
            'full_url' => request()->fullUrl()
        ]);

        return response('Forbidden', 403);
    }


    // Manejo de mensajes entrantes
    public function handleWebhook(Request $request)
    {
        try {
            // Obtén el payload completo
            $data = $request->all(); //dd($data);
            $status_send = false;

            $eventName = $request->input('object', 'unknown_event');
            WebhookLog::create([
                'event_name' => $eventName,
                'payload' => json_encode($data, JSON_PRETTY_PRINT),
            ]);

            // Verifica si el evento pertenece al objeto esperado
            if (isset($data['object']) && $data['object'] === 'whatsapp_business_account') { //dd($data['object']);
                foreach ($data['entry'] as $entry) {
                    foreach ($entry['changes'] as $change) {

                        $value = $change['value'] ?? [];

                        // 👇 Ignorar si es un mensaje de status (no contiene mensajes reales)
                        if (!isset($value['messages']) || empty($value['messages'])) {
                            continue; // simplemente ignora este cambio
                        }

                        // 👇 Resto de tu lógica se mantiene
                        $metadata = $value['metadata'] ?? [];
                        $phoneNumberId = $metadata['phone_number_id'] ?? null;
                        $displayPhoneNumber = $metadata['display_phone_number'] ?? null;

                        // Procesa mensajes
                        if (!empty($value['messages'])) {
                            foreach ($value['messages'] as $message) {
                                $from = $message['from'] ?? null;
                                $type = $message['type'] ?? 'text';
                                $body = $this->extractMessageContent($message);
                                if (isset($from) && isset($body) && $status_send == false) {

                                    $profile_name = $value['contacts'][0]['profile']['name'] ?? null;
                                    $messageData = [
                                        'from' => $from,
                                        'to' => $displayPhoneNumber,
                                        'wa_id' => $value['contacts'][0]['wa_id'] ?? null,
                                        'profile_name' => $profile_name ?? null,
                                        'message_id' => $message['id'] ?? null,
                                        'body' => $body,
                                        'type' => $type,
                                        'timestamp' => isset($message['timestamp']) ? date('Y-m-d H:i:s', $message['timestamp']) : null,
                                        'messaging_product' => $value['messaging_product'] ?? 'whatsapp',
                                        'phone_number_id' => $phoneNumberId,
                                        'metadata' => json_encode($metadata),
                                    ];

                                    WebhookMessage::updateOrCreate(
                                        ['message_id' => $messageData['message_id']], // Evita duplicados
                                        $messageData
                                    );

                                    $text = "Hola soy SAEFL Bot";
                                    if ($type == "text") {
                                        // Verificamos si el mensaje es un número entre 1 y 9
                                        if (preg_match('/^[1-9]$/i', $body) && $status_send == false) {
                                            $text = $this->sendSpecificResponse($body);
                                        }
                                        // Verificamos si el mensaje tiene entre 7 y 8 dígitos
                                        elseif (preg_match('/^\d{7,8}$/i', $body)) {
                                            $text = $this->getAdministrativeInfo($body);
                                        } elseif (preg_match('/^i\d{8,20}$/i', $body) && $status_send == false) {
                                            $text = $this->sendFileInscription($from, $body);
                                            //$text = "La opción seleccionada está en mantenimiento";
                                            $status_send = true;
                                        } elseif (preg_match('/^c\d{8,20}$/i', $body) && $status_send == false) {
                                            $text = $this->sendFileConstancia($from, $body);
                                            //$text = "La opción seleccionada está en mantenimiento";

                                            $status_send = true;
                                        } elseif (preg_match('/^s\d{8,20}$/i', $body) && $status_send == false) {
                                            $text = $this->sendFileSolvencia($from, $body);
                                            //$text = "La opción seleccionada está en mantenimiento";
                                            $status_send = true;
                                        } elseif (preg_match('/^a\d{8,20}$/i', $body) && $status_send == false) {
                                            $text = $this->sendFileConstanciaAdministrativa($from, $body);
                                            //$text = "La opción seleccionada está en mantenimiento";
                                            $status_send = true;
                                        } elseif (preg_match('/^n\d{8,20}$/i', $body) && $status_send == false) {

                                            $text = $this->sendFileBoletin($from, $body);
                                            //$text = "La opción seleccionada está en mantenimiento";
                                            $status_send = true;
                                        } elseif (preg_match('/^men[uú]$/i', $body)) {
                                            $text = $this->getDefaultMenuResponse();
                                        } else {
                                            // Responder con el menú de opciones por defecto
                                            $default = 'Gracias por contactarnos. 😊, notamos algo inusual ❌, indícame nuevamente tu requerimiento. ' .
                                                '/nTe recuerdo que puedes consultar las opciones disponibles en nuestro menú, o responderme con el número o la letra correspondiente a la opción que deseas.' .
                                                '/nPuedes enviar la palabra 🇲 🇪 🇳 🇺 para ver todas las opciones.';

                                            $text = $this->generateQwenResponse($body, $from); //dd($text);
                                        }
                                        // Enviar el mensaje de respuesta
                                        $this->sendMessage($from, $text);
                                    }

                                    $messageData = [
                                        'from' => 'SAEFL',
                                        'to' => $from,
                                        'wa_id' => 'wa_id.' . $value['contacts'][0]['wa_id'] ?? null,
                                        'profile_name' => "SAEFL Bot",
                                        'message_id' => 'message_id.' . $message['id'] ?? null,
                                        'body' => $text,
                                        'type' => 'text',
                                        'timestamp' => isset($message['timestamp']) ? date('Y-m-d H:i:s', $message['timestamp']) : null,
                                        'messaging_product' => $value['messaging_product'] ?? 'whatsapp',
                                        'phone_number_id' => "SAEFLIdPhone",
                                        'metadata' => json_encode($metadata),
                                    ];
                                    WebhookMessage::updateOrCreate(
                                        ['message_id' => $messageData['message_id']], // Evita duplicados
                                        $messageData
                                    );
                                }
                            }
                        }
                    }
                }
            }

            // Respuesta estándar
            return response('EVENT_RECEIVED', 200);
        } catch (Exception $e) {
            Log::error('Error procesando el webhook: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    ////////////////////////////////////////////////////////////////////////
    private function getDefaultMenuResponse()
    {
        // Mensaje de menú por defecto
        return "*Gracias por escribirnos.* 😃 \n\n"
            . "Por favor, elige una de las siguientes opciones: \n\n"
            . "------------------------\n"
            . "1️⃣ : *Tasa de cambio BCV del día* 📈 \n"
            . "------------------------\n"
            . "2️⃣ : *Enlace para reportar pagos* 📄 \n"
            // ."------------------------\n"
            // . "3️⃣ : *Enlace para el botón de pago* 💳 \n"
            . "------------------------\n"
            . "*Número de cédula de identidad* del representante para ver *información administrativa* detallada \n ej: ```14567234``` \n"
            . "------------------------\n"
            . "🇮 seguida del *número de cédula de identidad* del estudiante para obtener una *Constancia de Inscripción Académica* \n ej: ```i31256734``` \n"
            . "------------------------\n"
            . "🇨 seguida del *número de cédula de identidad* del estudiante para obtener una *Constancia de Estudios* \n ej: ```c33967131``` \n"
            // ."------------------------\n"
            // . "🇦 seguida del *número de cédula de identidad* del estudiante para obtener una *Constancia de Inscripción Administrativa* \n ej: ```a33967131``` \n"
            . "------------------------\n"
            . "🇸 seguida del *número de cédula de identidad* del estudiante para obtener una *Solvencia Administrativa* \n ej: ```s33967131``` \n"
            . "------------------------\n"
            . "🇳 seguida del *número de cédula de identidad* del estudiante para obtener un *Informe de notas* \n ej: ```n33967131``` \n"
            . "------------------------\n"
            . "🇲 🇪 🇳 🇺 para ver el menú de opciones \n ej: ```menu``` \n"
            . "------------------------\n"
            // . "🇷 seguida del *número de cédula de identidad* del estudiante para obtener una *Informe de notas de momento actual* \n ej: ```r33967131``` \n"
            // . "*4*: *Horarios de atención* ⏰\n"
            // . "*5*: *Consultas sobre facturación* 💳\n"
            // . "*6*: *Sugerencias y comentarios* 💬\n"
            // . "*7*: *Consulta sobre cuentas* 🏦\n"
            // . "*8*: *Servicios adicionales* 🛠️\n"
            // . "*9*: *Contactar con un agente* 🤖"
        ;
    }

    //Funcional
    public function sendMessage($phone, $text)
    {
        $phonePattern = '/^\d{11,12}$/';
        if (preg_match($phonePattern, $phone)) {
            // URL de la API de Facebook Graph
            $url = env('FACEBOOK_URL'); //dd($url,$phone,$text);
            $tokenService = new FacebookTokenService;
            $accessToken = $tokenService->getAccessToken(); //dd($url,$accessToken);

            // Datos del mensaje
            $data = [
                "messaging_product" => "whatsapp",
                "to" => $phone,
                "type" => "text",
                "text" => [
                    "body" => $text
                ]
            ];
            // Enviar solicitud POST
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $data);

            // Manejar la respuesta
            if ($response->successful()) {
                WebhookLog::create([
                    'event_name' => 'successful',
                    'payload' => json_encode($response, JSON_PRETTY_PRINT),
                ]);
                // Log::info("Mensaje enviado a {$phone}: {$text}");
            } else {
                WebhookLog::create([
                    'event_name' => 'fail',
                    'payload' => json_encode($response, JSON_PRETTY_PRINT),
                ]);
                // Log::error("Error al enviar el mensaje: {$response->body()}");
            }
        } else {
            Log::error("Error al enviar el mensaje: {$phone} no cumple con el patron de validación");
        }
    }

    private function senderFileMessage($phone, $pdfPath)
    {
        $url = env('FACEBOOK_URL');
        $tokenService = new FacebookTokenService;
        $accessToken = $tokenService->getAccessToken();
        $response = Http::withToken($accessToken)->post($url, [
            'messaging_product' => 'whatsapp',
            'recipient_type'    => 'individual',
            'to'                => $phone,
            'type'              => 'document',
            'document'          => [
                'link' => $pdfPath,
                'filename' => basename($pdfPath), // Nombre del archivo
            ],
        ]);

        // Verifica la respuesta
        if ($response->failed()) {
            $this->sendMessage($phone, '❌ *Se detectaron errores. Por favor, contacte al administrador del sistema.*');
        }
    }

    public function sendMessageTemplateGeneral($phone, $text)
    {
        $phonePattern = '/^\d{11,12}$/';
        if (preg_match($phonePattern, $phone)) {
            // URL de la API de Facebook Graph
            $url = env('FACEBOOK_URL'); //dd($url,$phone,$text);
            $tokenService = new FacebookTokenService;
            $accessToken = $tokenService->getAccessToken(); //dd($url,$accessToken);

            // Datos del mensaje
            $data = [
                "messaging_product" => "whatsapp",
                "to" => $phone,
                "type" => "text",
                "text" => [
                    "body" => $text
                ]
            ];
            // Enviar solicitud POST
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $data);

            // Manejar la respuesta
            if ($response->successful()) {
                WebhookLog::create([
                    'event_name' => 'successful',
                    'payload' => json_encode($response, JSON_PRETTY_PRINT),
                ]);
                // Log::info("Mensaje enviado a {$phone}: {$text}");
            } else {
                WebhookLog::create([
                    'event_name' => 'fail',
                    'payload' => json_encode($response, JSON_PRETTY_PRINT),
                ]);
                // Log::error("Error al enviar el mensaje: {$response->body()}");
            }
        } else {
            Log::error("Error al enviar el mensaje: {$phone} no cumple con el patron de validación");
        }
    }
}
