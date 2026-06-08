<?php

namespace App\Http\Controllers;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Integration\MetaResponse;
use App\Services\FacebookTokenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppTemplateSendInformeNotasController extends Controller
{
    public function sendInformeNotas(FacebookTokenService $tokenService, $token)
    {
        $status = false;
        $message = null;
        $json = null;
        $estudiant = Estudiant::where('token', $token)->first();
        $representant = $estudiant->representant ?? null;
        $ident = $representant->ci_representant ?? null;
        $phone = $representant->whatsapp ?? null;
        $phone = '584145752242';
        $template = 'informe_notas'; // Nombre exacto de la plantilla
        $phonePattern = '/^\d{11,12}$/';
        $valid = preg_match($phonePattern, $phone); //dd($phone);

        if ($ident && $phone && $valid) {

            $representant = Representant::where('ci_representant', $ident)->first();

            if ($representant) {
                $studentName = $estudiant->fullname ?? 'Estudiante'; // Ajusta según tu relación
                $to = $phone;
                $name = $representant->name;
                $token = $estudiant->token;

                $url = env('FACEBOOK_URL');
                $accessToken = $tokenService->getAccessToken();

                $institucion = Institucion::first();
                $mediaId = ($institucion) ? $institucion->facebook_media_id_admon : env('FACEBOOK_IMAGE_ACADEMIC_ID');

                // Enviar el mensaje a través de la API de WhatsApp
                $response = Http::withToken($accessToken)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, [
                        'messaging_product' => 'whatsapp',
                        'to' => $to,
                        'type' => 'template',
                        'template' => [
                            'name' => $template,
                            'language' => ['code' => 'es_ES'],
                            'components' => [
                                [
                                    'type' => 'header',
                                    'parameters' => [
                                        [
                                            'type' => 'image',
                                            'image' => ['id' => $mediaId],
                                        ],
                                    ],
                                ],
                                [
                                    'type' => 'body',
                                    'parameters' => [
                                        ['type' => 'text', 'text' => $name],         // {{1}} Representante
                                        ['type' => 'text', 'text' => $studentName],  // {{2}} Estudiante
                                    ],
                                ],
                                [
                                    'type' => 'button',
                                    'sub_type' => 'url',
                                    'index' => 0,
                                    'parameters' => [
                                        [
                                            'type' => 'text',
                                            'text' => $token // Se usará en la URL dinámica {{1}}
                                        ]
                                    ]
                                ]
                            ],
                        ],
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $arr = [
                        'message' => 'Notificación enviada con éxito.',
                        'ident' => $ident,
                        'phone' => $phone,
                        'template' => $template,
                        'messaging_product' => $data['messaging_product'],
                        'contact_input' => $data['contacts'][0]['input'],
                        'contact_wa_id' => $data['contacts'][0]['wa_id'],
                        'message_id' => $data['messages'][0]['id'],
                        'message_status' => $data['messages'][0]['message_status'],
                        'json' => $data,
                    ];
                    MetaResponse::createResponse($arr);
                    $status = true;
                    $representant->status_whatsapp_verify = true;
                    $representant->save();
                } else {
                    $status = false;
                    $message .= "Error al enviar la notificación.";
                    $json = $response->json();

                    if (isset($json['error']['code']) && $json['error']['code'] == 131047) {
                        $message .= ' El número no está registrado en WhatsApp: ' . $json['error']['message'];
                    }
                }
            } else {
                $message = "Representante no encontrado con CI: {$ident}";
            }
        } else {
            $message = "Número de teléfono no válido o falta el CI.";
        }

        if (! $status) {
            $arr = [
                'message' => $message,
                'ident' => $ident,
                'phone' => $phone,
                'template' => $template,
                'messaging_product' => null,
                'contact_input' => null,
                'contact_wa_id' => null,
                'message_id' => null,
                'message_status' => null,
                'json' => $json,
            ];
            MetaResponse::createResponse($arr);
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'json' => $json
        ]);
    }
}
