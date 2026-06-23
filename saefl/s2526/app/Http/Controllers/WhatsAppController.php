<?php

namespace App\Http\Controllers;

use App\Models\app\Enrollment\Catchment;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Integration\MetaResponse;
use App\Models\app\Planpago\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

use App\Services\FacebookTokenService;
use App\Services\SendWhatsappMessageService;

class WhatsAppController extends Controller
{
    //////////////////////////INI META//////////////////////////

    public function uploadImage(FacebookTokenService $tokenService, Request $request)
    { // send-whatsapp/meta/upload/image
        $accessToken = $tokenService->getAccessToken(); //dd($url,$accessToken);
        $mediaId = env('FACEBOOK_IMAGE_ADMON_ID');

        $phone_id = env('FACEBOOK_NUM_ID');
        $response = Http::withToken($accessToken)
            ->attach('file', file_get_contents(public_path() . '/images/brand/saefl/bgBlankCFLA.jpg'), 'image.jpg')
            ->post('https://graph.facebook.com/v23.0/' . $phone_id . '/media', [
                'messaging_product' => 'whatsapp',
                'type' => 'image',
            ]); //dd($response->json());
        $mediaId = $response->json()['id']; // Guarda este ID para usarlo en la plantilla
        dd($mediaId);
    }

    public function metaCustom(FacebookTokenService $tokenService, Request $request)
    { // /send-whatsapp/meta/custom/{ident}/{phone}
        $ident = $request->ident;
        $phone = $request->phone;

        $phonePattern = '/^\d{11,12}$/';
        $valid = preg_match($phonePattern, $phone);

        if ($valid) {
            $representant = Representant::where('ci_representant', $ident)->first();
            if (! $representant) abort('CI no encontrada', 404);

            $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill, 2);
            if ($exchange_ammount_expire_bill <= 0) abort('Representante SOLVENTE', 500);

            $exchange_rate = ExchangeRate::whereDate('date', Carbon::now())->first();
            $now = Carbon::now()->format('d-m-Y');
            $ammountBs = ($exchange_rate) ? ($exchange_ammount_expire_bill * $exchange_rate->ammount) : null;
            $ammountBs = ($ammountBs) ? 'Bs ' . round($ammountBs, 2) . ' (Según Tasa BCV ' . $now . ')' : '[Sin Tasa BCV]';

            $to = ($phone) ? $phone : '584145752242';
            $name = $representant->name; // Valor para {{1}}
            $amount = $ammountBs . ' - USD ' . $exchange_ammount_expire_bill; // Valor para {{2}}

            $url = env('FACEBOOK_URL'); // URL de la API de Facebook Graph
            $accessToken = $tokenService->getAccessToken();

            // Datos del mensaje
            $data = [
                "messaging_product" => "whatsapp",
                "to" => $to, // Número de destino 584145752242 / 584121560804
                "type" => "text",
                "text" => [
                    "body" => "*Notificaciones SAEFL*\nEstimado/a representante " . $name . ", le recordamos que tiene pendiente el pago por servicio de escolaridad correspondiente a su representando *[" . $amount . "]*.\n\nAgradecemos su atención y apoyo.\n\nEn nuestro portal web tiene los métodos de pago aceptados https://uefrayluisamigosf.com.\n\nAtentamente, \n\n*Dirección de Administración CE.CFLA*"
                ]
            ];
            // Enviar solicitud POST
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $data);

            // Manejar la respuesta
            if ($response->successful()) {
                return response()->json([
                    'message' => 'Notificación enviada con éxito.',
                    'response' => $response->json()
                ]);
            } else {
                return response()->json([
                    'message' => 'Error al enviar la notificación.',
                    'response' => $response->json()
                ], $response->status());
            }
        } else {
            return response()->json([
                'message' => 'Error al enviar la notificación. núemero inválido'
            ]);
        }




        //END sin usar plantillas
    }

    public function production(FacebookTokenService $tokenService, Request $request)
    { // /send-whatsapp/meta/template/custom/production
        $ident = $request->ident;
        $phone = $request->phone;

        $phonePattern = '/^\d{11,12}$/';
        $valid = preg_match($phonePattern, $phone);

        if ($ident && $phone && $valid) {

            $representant = Representant::where('ci_representant', $ident)->first();

            if ($representant) {
                $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill, 2);
                if ($exchange_ammount_expire_bill <= 0) abort('Representante SOLVENTE', 500);

                $exchange_rate = ExchangeRate::whereDate('date', Carbon::now())->first();
                $now = Carbon::now()->format('d-m-Y');
                $ammountBs = ($exchange_rate) ? ($exchange_ammount_expire_bill * $exchange_rate->ammount) : null;
                $ammountBs = ($ammountBs) ? 'Bs ' . round($ammountBs, 2) . ' (Según Tasa BCV ' . $now . ')' : '[Sin Tasa BCV]';

                $to = ($phone) ? $phone : '584145752242';
                $name = substr($representant->name, 0, 10) . '...'; // Valor para {{1}}
                $amount = $ammountBs . ' - USD ' . $exchange_ammount_expire_bill; // Valor para {{2}}

                $url = env('FACEBOOK_URL'); // URL de la API de Facebook Graph
                $accessToken = $tokenService->getAccessToken(); //dd($url,$accessToken);
                $institucion = Institucion::first();
                $mediaId = ($institucion) ? $institucion->facebook_media_id_admon : env('FACEBOOK_IMAGE_ADMON_ID');

                // Estructura de la solicitud
                $response = Http::withToken($accessToken)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])
                    ->post($url, [
                        'messaging_product' => 'whatsapp',
                        'to' => $to,
                        'type' => 'template',
                        'template' => [
                            'name' => 'notifications_admon', // Nombre de la plantilla (payment)
                            'language' => [
                                'code' => 'es_ES',
                            ],
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
                                        [
                                            'type' => 'text',
                                            'text' => $name, // Parámetro {{1}}
                                        ],
                                        [
                                            'type' => 'text',
                                            'text' => $amount, // Parámetro {{2}}
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ]);

                // Manejar la respuesta
                if ($response->successful()) {
                    session()->flash('success', 'OK');
                } else {
                    session()->flash('error', 'Error al enviar la notificación.');
                }
            } else {
                session()->flash('error', 'Error al enviar la notificación. CI no encontrada');
            }
        }

        if ($valid) {
            session()->flash('error', 'Error al enviar la notificación. su número telefonico no es válido');
        }

        return view('whatsapp.production', compact('ident', 'phone'));
    }

    public function metaTemplateCustom(FacebookTokenService $tokenService, Request $request)
    { // /send-whatsapp/meta/template/custom/{ident}/{phone}
        $ident = $request->ident;
        $phone = $request->phone;
        $template = $request->template ?? 'notice_collection'; //notice_collection, notifications_admon, payment

        $representant = Representant::where('ci_representant', $ident)->first();

        if (! $representant) abort('CI no encontrada', 404);

        $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill, 2);
        if ($exchange_ammount_expire_bill <= 0) abort('Representante SOLVENTE', 500);

        $exchange_rate = ExchangeRate::whereDate('date', Carbon::now())->first();
        $now = Carbon::now()->format('d-m-Y');
        $ammountBs = ($exchange_rate) ? ($exchange_ammount_expire_bill * $exchange_rate->ammount) : null;
        $ammountBs = ($ammountBs) ? 'Bs ' . round($ammountBs, 2) . ' (Según Tasa BCV ' . $now . ')' : '[Sin Tasa BCV]';

        $to = ($phone) ? $phone : '584145752242';
        $name = $representant->name; // Valor para {{1}}
        $amount = $ammountBs . ' - USD ' . $exchange_ammount_expire_bill; // Valor para {{2}}

        $url = env('FACEBOOK_URL'); // URL de la API de Facebook Graph
        $accessToken = $tokenService->getAccessToken(); //dd($url,$accessToken);

        $institucion = Institucion::first();
        $mediaId = ($institucion) ? $institucion->facebook_media_id_admon : env('FACEBOOK_IMAGE_ADMON_ID');

        // Estructura de la solicitud

        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => $template, // Nombre de la plantilla (notifications_admon, payment, collections)
                    'language' => [
                        'code' => 'es_ES',
                    ],
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
                                [
                                    'type' => 'text',
                                    'text' => $name, // Parámetro {{1}}
                                ],
                                [
                                    'type' => 'text',
                                    'text' => $amount, // Parámetro {{2}}
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        // Manejar la respuesta
        if ($response->successful()) {
            $data = $response->json(); //dd($data);
            $arr = [
                'message' => 'Notificación enviada con éxito.',
                'ident' => $ident,
                'phone' => $phone,
                'template' => $template, // notifications_admon, collections, payment
                'messaging_product' => $data['messaging_product'],
                'contact_input' => $data['contacts'][0]['input'],
                'contact_wa_id' => $data['contacts'][0]['wa_id'],
                'message_id' => $data['messages'][0]['id'],
                'message_status' => $data['messages'][0]['message_status'],
                'json' => $data,
            ];
            $meta_response = MetaResponse::createResponse($arr);

            return response()->json([
                'message' => 'Notificación enviada con éxito.',
                'response' => $response->json()
            ]);
        } else {
            return response()->json([
                'message' => 'Error al enviar la notificación.',
                'response' => $response->json(),
                'url' => $url,
                'accessToken' => $accessToken,
                'mediaId' => $mediaId,
                'phoneTo' => $to,
                'template' => $template,
            ], $response->status());
        }
    }

    public function meta(FacebookTokenService $tokenService, Request $request)
    { // /send-whatsapp/meta/index
        // URL de la API de Facebook Graph
        $url = env('FACEBOOK_URL');
        // Token de acceso
        $accessToken = $tokenService->getAccessToken(); //dd($url,$accessToken);

        // Datos del mensaje
        $data = [
            "messaging_product" => "whatsapp",
            "to" => "584145752242", // Número de destino 584145752242 / 584121560804
            "type" => "text",
            "text" => [
                "body" => "*Notificaciones SAEFL*\nEstimado/a representante, le recordamos que tiene pendiente el pago por servicio de escolaridad correspondiente a su representando.\n\nEstamos a su disposición para brindarle detalles y coordinar un plan si lo requiere. Contáctenos al 0424-5891682 o frayluisamigoyara@hotmail.com. Agradecemos su atención y apoyo.\n\nEn nuestro portal web tiene los métodos de pago aceptados https://uefrayluisamigosf.com.\n\nAtentamente, \n\n*Dirección de Administración CE.CFLA*"
            ]
        ];
        // Enviar solicitud POST
        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $data);

        // Manejar la respuesta
        if ($response->successful()) {
            return response()->json([
                'message' => 'Notificación enviada con éxito.',
                'response' => $response->json()
            ]);
        } else {
            return response()->json([
                'message' => 'Error al enviar la notificación.',
                'response' => $response->json()
            ], $response->status());
        }
    }

    public function metaTemplateCustomSendFlyer(FacebookTokenService $tokenService, Request $request)
    { // /send-whatsapp/meta/template/custom/{ident}/{phone}/{template}
        $ident = $request->ident;
        $phone = $request->phone; //584121560804
        $template = $request->template;

        $representant = Representant::where('ci_representant', $ident)->first();

        if (! $representant) abort('CI no encontrada', '404');
        if (! $template) abort('Plantilla no encontrada', '404');

        $name = $representant->name; // Valor para {{1}}

        $url = env('FACEBOOK_URL'); // URL de la API de Facebook Graph
        $accessToken = $tokenService->getAccessToken(); //dd($url,$accessToken);
        $institucion = Institucion::first();
        $mediaId = ($institucion) ? $institucion->facebook_media_id_admon : env('FACEBOOK_IMAGE_ADMON_ID');

        $to = ($phone) ? $phone : '584145752242';
        // Estructura de la solicitud
        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => $template, // Nombre de la plantilla
                    'language' => [
                        'code' => 'es_ES',
                    ],
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
                                [
                                    'type' => 'text',
                                    'text' => $name, // Parámetro {{1}}
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        // Manejar la respuesta

        if ($response->successful()) {
            $data = $response->json(); //dd($data);
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
            $meta_response = MetaResponse::createResponse($arr);

            return response()->json([
                'message' => 'Notificación enviada con éxito.',
                'response' => $response->json()
            ]);
        } else {
            return response()->json([
                'message' => 'Error al enviar la notificación [flyer].',
                'response' => $response->json(),
                'url' => $url,
                'accessToken' => $accessToken,
                'mediaId' => $mediaId,
                'phoneTo' => $to,
                'template' => $template,
            ], $response->status());
        }
    }

    //////////////////////////FIN META//////////////////////////

    public function metaTemplateGeneralCustom(FacebookTokenService $tokenService, Request $request)
    {
        // /send-whatsapp/meta/template/custom/{ident}/{phone}
        $ident = $request->ident;
        $phone = $request->phone;
        $template = $request->template ?? 'general'; //presentacion_propuesta, genera, notice_collection, notifications_admon, payment

        $representant = Representant::where('ci_representant', $ident)->first();

        if (! $representant) abort('CI no encontrada', 404);

        $to = ($phone) ? $phone : '584145752242';
        $name = $representant->name; // Valor para {{1}}
        $token = $representant->ci_representant;
        $templateName = 'general';

        $text = implode(' | ', [
            'Quisiéramos recordarle que el proceso de consulta sobre las propuestas de mensualidad para el período escolar 2025-2026 finaliza hoy a las 7:00 p.m.',
            'Para su comodidad, le estamos enviando el enlace de participación por este medio',
            "https://uefrayluisamigosf.com/saefl/general/polls/{$token}",
            'También puede utilizar el enlace que recibió previamente en su correo electrónico.',
            'Si ya ha emitido su voto, por favor, ignore este mensaje.',
            'Para cualquier duda o inconveniente, no dude en contactarnos por correo electrónico a notificaciones@uefrayluisamigosf.com o por WhatsApp al  +584245891682.',
            'Agradecemos de antemano su participación.',
        ]); // Valor para {{2}}

        $url = env('FACEBOOK_URL'); // URL de la API de Facebook Graph
        $accessToken = $tokenService->getAccessToken();

        // Valida los media_id y los vuelve a generar si fueron eliminados.
        $tokenService->validateAndRefreshMediaIds($accessToken);

        $institucion = Institucion::first();

        $mediaId = ($institucion) ? $institucion->facebook_media_id_control : env('FACEBOOK_IMAGE_ACADEMIC_ID');

        // Parámetros dinámicos del body
        $bodyParams = [
            $name, // {{1}}
            $text, // {{2}}
        ];

        try {
            // Enviar mensaje
            $whatsappService = new SendWhatsappMessageService($tokenService);
            $response = $whatsappService->sendDynamicTemplate($to, $templateName, $bodyParams, $mediaId);

            return response()->json([
                'message' => 'Notificación enviada con éxito.',
                'response' => $response
            ]);

            // return json_encode($response);
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }

    public function metaTemplateGeneralCustomCatchment(FacebookTokenService $tokenService, Request $request)
    {
        // /send-whatsapp/meta/template/custom/{ident}/{phone}
        $ident = $request->ident;
        $phone = $request->phone;
        $template = $request->template ?? 'general'; //notice_collection, notifications_admon, payment

        $to = ($phone) ? $phone : '584145752242';
        $text = "Texto de prueba"; // Valor para {{2}}

        $catchment = Catchment::where('representant_ci', $ident)->first();
        if (! $catchment) abort('CI no encontrada', 404);
        $name = '*' . $catchment->representant_name . ' ' . $catchment->representant_lastname . '*'; // Valor para {{1}}

        // $text = "Le recordamos que su cita para el Censo Escolar 2025-2026 en el Colegio Fray Luis Amigó está programada para el ".f_date($catchment->day_appointment)." a las 2:00 PM. Su asistencia es fundamental para continuar con el proceso de inscripción."; // Valor para {{2}}
        $text = "Es un gusto saludarle. Le recordamos que su entrevista está pautada para el " . f_date($catchment->day_appointment) . "a las 2pm. Su presencia es muy importante para nosotros, ya que nos permite fortalecer el trabajo en equipo por la educación de su representado/a. Además, le hacemos un llamado amable a considerar el código de vestimenta descrito en los Acuerdos de Convivencia al asistir al plantel. Esto nos ayuda a mantener un ambiente de respeto y armonía. A continuación, le compartimos algunas indicaciones clave: Para damas: Evitar licras, shorts, minifaldas, strapless o prendas con escotes pronunciados.   Para caballeros: Pantalón formal y camisa/camiseta adecuada (sin tirantes o prendas rotas)."; // Valor para {{2}}

        $url = env('FACEBOOK_URL'); // URL de la API de Facebook Graph
        $accessToken = $tokenService->getAccessToken(); //dd($url,$accessToken);


        $institucion = Institucion::first();
        $mediaId = ($institucion) ? $institucion->facebook_media_id_control : env('FACEBOOK_IMAGE_ACADEMIC_ID');

        // Estructura de la solicitud

        $response = Http::withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => $template, // Nombre de la plantilla (notifications_admon, payment, collections)
                    'language' => [
                        'code' => 'es_ES',
                    ],
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
                                [
                                    'type' => 'text',
                                    'text' => $name, // Parámetro {{1}}
                                ],
                                [
                                    'type' => 'text',
                                    'text' => $text, // Parámetro {{2}}
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

        // Manejar la respuesta
        if ($response->successful()) {
            $data = $response->json(); //dd($data);
            $arr = [
                'message' => 'Notificación enviada con éxito.',
                'ident' => $ident,
                'phone' => $phone,
                'template' => $template, // notifications_admon, collections, payment
                'messaging_product' => $data['messaging_product'],
                'contact_input' => $data['contacts'][0]['input'],
                'contact_wa_id' => $data['contacts'][0]['wa_id'],
                'message_id' => $data['messages'][0]['id'],
                'message_status' => $data['messages'][0]['message_status'],
                'json' => $data,
            ];
            $meta_response = MetaResponse::createResponse($arr);

            return response()->json([
                'message' => 'Notificación enviada con éxito.',
                'response' => $response->json()
            ]);
        } else {
            return response()->json([
                'message' => 'Error al enviar la notificación.',
                'response' => $response->json(),
                'url' => $url,
                'accessToken' => $accessToken,
                'mediaId' => $mediaId,
                'phoneTo' => $to,
                'template' => $template,
            ], $response->status());
        }
    }

    public function validateAndRefreshMediaIds($accessToken)
    {
        $institucion = Institucion::first();
        if (! $institucion) {
            throw new \Exception("No se encontró registro de Institución");
        }

        $phoneId = env('FACEBOOK_NUM_ID');

        // Helper para validar un Media ID
        $isMediaIdValid = function ($mediaId) use ($accessToken) {
            if (! $mediaId) {
                return false;
            }
            $check = Http::withToken($accessToken)
                ->get("https://graph.facebook.com/v23.0/{$mediaId}");
            return $check->successful();
        };

        $needsSave = false;

        // Validar o regenerar Media ID Admon
        if (! $isMediaIdValid($institucion->facebook_media_id_admon)) {
            $response = Http::withToken($accessToken)
                ->attach('file', file_get_contents(public_path('images/brand/saefl/bgBlankCFLAAdmon.jpg')), 'image.jpg')
                ->post("https://graph.facebook.com/v23.0/{$phoneId}/media", [
                    'messaging_product' => 'whatsapp',
                    'type' => 'image',
                ]);
            if ($response->successful()) {
                $institucion->facebook_media_id_admon = $response->json()['id'];
                $needsSave = true;
            } else {
                throw new \Exception("Error subiendo imagen Admon: " . json_encode($response->json()));
            }
        }

        // Validar o regenerar Media ID Control
        if (! $isMediaIdValid($institucion->facebook_media_id_control)) {
            $response = Http::withToken($accessToken)
                ->attach('file', file_get_contents(public_path('images/brand/saefl/bgBlankCFLAControl.jpg')), 'image.jpg')
                ->post("https://graph.facebook.com/v23.0/{$phoneId}/media", [
                    'messaging_product' => 'whatsapp',
                    'type' => 'image',
                ]);
            if ($response->successful()) {
                $institucion->facebook_media_id_control = $response->json()['id'];
                $needsSave = true;
            } else {
                throw new \Exception("Error subiendo imagen Control: " . json_encode($response->json()));
            }
        }

        if ($needsSave) {
            $institucion->save();
        }

        return [
            'facebook_media_id_admon' => $institucion->facebook_media_id_admon,
            'facebook_media_id_control' => $institucion->facebook_media_id_control
        ];
    }
}
