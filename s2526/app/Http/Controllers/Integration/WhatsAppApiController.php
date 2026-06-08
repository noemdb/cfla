<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Jobs\Queue\Meta\SendWhatsAppMessageFlyerJob;
use App\Jobs\Queue\Meta\SendWhatsAppMessageJob;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Integration\MetaResponse;
use App\Models\app\Planpago\ExchangeRate;
use App\Services\FacebookTokenService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class WhatsAppApiController extends Controller
{

    //notice_collection
    public function sendMessage(FacebookTokenService $tokenService,$ident,$phone,$template)
    {
        $status = false;
        $message = null;
        $json = null;
        $template = ($template) ? $template : 'notice_collection';
        $phonePattern = '/^\d{11,12}$/';
        $valid = preg_match($phonePattern, $phone);
        if ($ident && $phone && $valid) {
            $representant = Representant::where('ci_representant',$ident)->first();
            if ($representant) {
                $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill,2);
                $exchange_rate = ExchangeRate::whereDate('date',Carbon::now())->first() ;
                $now = Carbon::now()->format('d-m-Y');
                $ammountBs = ($exchange_rate) ? ($exchange_ammount_expire_bill * $exchange_rate->ammount) : null ;
                $ammountBs = ($ammountBs) ? 'Bs '.round($ammountBs,2).' (Según Tasa BCV '.$now.')': '[Sin Tasa BCV]';
                $to = ($phone) ? $phone : '584145752242' ;
                $name = substr($representant->name, 0, 10) . '...'; // Valor para {{1}}
                $amount = $ammountBs.' - USD '.$exchange_ammount_expire_bill; // Valor para {{2}}                
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
                        'name' => $template, // Nombre de la plantilla (payment)
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
                    $data = $response->json();//dd($data);
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
                    $status = true;    
                    $representant->status_whatsapp_verify = true;
                    $representant->save();
                } else {
                    $status = false;                    
                    $message .= "Error al enviar la notificación.";
                    $json = $response->json();
                }

                if ($response->failed()) {
                    $error = $response->json();                    
                    // Verifica si el error es relacionado con un número no registrado
                    if ($error['error']['code'] == 131047) {                        
                        $message .= 'Error al enviar el mensaje, el número no está registrado en WhatsApp.: ' . $error['error']['message'];        
                    }
                }
            } else {
                $status = false;             
                $message .= "Error al enviar la notificación. CI no encontrada";
                $json = null;
            }         
        } 

        if (! $valid ) {
            $message .= "Error al enviar la notificación. su número telefonico no es válido";
            $status = false;
            $json = null;            
        }

        if (! $status ) {
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
            $meta_response = MetaResponse::createResponse($arr);
        }
        return $status;
    }

    //notication_academic
    public function sendMessageAcademic(FacebookTokenService $tokenService,$ident,$phone,$template)
    {
        $status = false;
        $message = null;
        $json = null;
        $template = ($template) ? $template : 'notication_academic';
        $phonePattern = '/^\d{11,12}$/';
        $valid = preg_match($phonePattern, $phone);
        if ($ident && $phone && $valid) {
            $representant = Representant::where('ci_representant',$ident)->first();
            if ($representant) {
                $exchange_ammount_expire_bill = round($representant->exchange_ammount_expire_bill,2);
                $exchange_rate = ExchangeRate::whereDate('date',Carbon::now())->first() ;
                $now = Carbon::now()->format('d-m-Y');
                $ammountBs = ($exchange_rate) ? ($exchange_ammount_expire_bill * $exchange_rate->ammount) : null ;
                $ammountBs = ($ammountBs) ? 'Bs '.round($ammountBs,2).' (Según Tasa BCV '.$now.')': '[Sin Tasa BCV]';
                $to = ($phone) ? $phone : '584145752242' ;
                $name = $representant->name; // Valor para {{1}}
                $atte = 'Lic. Escarlet López'; // Valor para {{2}}                
                $url = env('FACEBOOK_URL'); // URL de la API de Facebook Graph        
                $accessToken = $tokenService->getAccessToken();
                
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
                        'name' => $template, // Nombre de la plantilla (notication_academic)
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
                                        'text' => $atte, // Parámetro {{2}}
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);
                // Manejar la respuesta
                if ($response->successful()) {
                    $data = $response->json();//dd($data);
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
                    $status = true;    
                    $representant->status_whatsapp_verify = true;
                    $representant->save();
                } else {
                    $status = false;                    
                    $message .= "Error al enviar la notificación.";
                    $json = $response->json();
                }

                if ($response->failed()) {
                    $error = $response->json();                    
                    // Verifica si el error es relacionado con un número no registrado
                    if ($error['error']['code'] == 131047) {                        
                        $message .= 'Error al enviar el mensaje, el número no está registrado en WhatsApp.: ' . $error['error']['message'];        
                    }
                }
            } else {
                $status = false;             
                $message .= "Error al enviar la notificación. CI no encontrada";
                $json = null;
            }         
        } 

        if (! $valid ) {
            $message .= "Error al enviar la notificación. su número telefonico no es válido";
            $status = false;
            $json = null;            
        }

        if (! $status ) {
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
            $meta_response = MetaResponse::createResponse($arr);
        }
        return $status;
    }    

    //template general
    public function sendMessageGeneral(FacebookTokenService $tokenService,$ident,$phone,$template,$general)
    {
        $status = false;
        $message = null;
        $json = null;
        $template = ($template) ? $template : 'notifications_agreement';
        $phonePattern = '/^\d{11,12}$/';
        $valid = preg_match($phonePattern, $phone);
        if ($ident && $phone && $valid) {
            $representant = Representant::where('ci_representant',$ident)->first();
            if ($representant) {
                $to = ($phone) ? $phone : '584145752242' ;
                // $to = '584145752242';
                $representant_name = $representant->name; // Valor para {{1}}
                $estudiant = $representant->estudiant_agree;
                $estudiant_name = ($estudiant) ? $estudiant->fullname.'*[ suspendido el uso del teléfono ]*' : null; // Valor para {{2}}                
                $url = env('FACEBOOK_URL'); // URL de la API de Facebook Graph        
                $accessToken = $tokenService->getAccessToken();
                
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
                        'name' => $template, // Nombre de la plantilla (notication_academic)
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
                                        'text' => $representant_name, // Parámetro {{1}}
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => $general, // Parámetro {{2}}
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);
                // Manejar la respuesta
                if ($response->successful()) {
                    $data = $response->json();//dd($data);
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
                    $status = true;    
                    $representant->status_whatsapp_verify = true;
                    $representant->save();
                } else {
                    $status = false;                    
                    $message .= "Error al enviar la notificación.";
                    $json = $response->json();
                }

                if ($response->failed()) {
                    $error = $response->json();                    
                    // Verifica si el error es relacionado con un número no registrado
                    if ($error['error']['code'] == 131047) {                        
                        $message .= 'Error al enviar el mensaje, el número no está registrado en WhatsApp.: ' . $error['error']['message'];        
                    }
                }

            } else {
                $status = false;             
                $message .= "Error al enviar la notificación. CI no encontrada";
                $json = null;
            }         
        } 

        if (! $valid ) {
            $message .= "Error al enviar la notificación. su número telefonico no es válido";
            $status = false;
            $json = null;            
        }

        if (! $status ) {
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
            $meta_response = MetaResponse::createResponse($arr);
        }
        return $status;
    }

    public function sendMessageAgree(FacebookTokenService $tokenService,$ident,$phone,$template)
    {
        $status = false;
        $message = null;
        $json = null;
        $template = ($template) ? $template : 'notifications_agreement';
        $phonePattern = '/^\d{11,12}$/';
        $valid = preg_match($phonePattern, $phone);
        if ($ident && $phone && $valid) {
            $representant = Representant::where('ci_representant',$ident)->first();
            if ($representant) {
                $to = ($phone) ? $phone : '584145752242' ;
                // $to = '584145752242';
                $representant_name = $representant->name; // Valor para {{1}}
                $estudiant = $representant->estudiant_agree;
                $estudiant_name = ($estudiant) ? $estudiant->fullname.'*[ suspendido el uso del teléfono ]*' : null; // Valor para {{2}}                
                $url = env('FACEBOOK_URL'); // URL de la API de Facebook Graph        
                $accessToken = $tokenService->getAccessToken();
                
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
                        'name' => $template, // Nombre de la plantilla (notication_academic)
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
                                        'text' => $representant_name, // Parámetro {{1}}
                                    ],
                                    [
                                        'type' => 'text',
                                        'text' => $estudiant_name, // Parámetro {{2}}
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);
                // Manejar la respuesta
                if ($response->successful()) {
                    $data = $response->json();//dd($data);
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
                    $status = true;    
                    $representant->status_whatsapp_verify = true;
                    $representant->save();
                } else {
                    $status = false;                    
                    $message .= "Error al enviar la notificación.";
                    $json = $response->json();
                }

                if ($response->failed()) {
                    $error = $response->json();                    
                    // Verifica si el error es relacionado con un número no registrado
                    if ($error['error']['code'] == 131047) {                        
                        $message .= 'Error al enviar el mensaje, el número no está registrado en WhatsApp.: ' . $error['error']['message'];        
                    }
                }

            } else {
                $status = false;             
                $message .= "Error al enviar la notificación. CI no encontrada";
                $json = null;
            }         
        } 

        if (! $valid ) {
            $message .= "Error al enviar la notificación. su número telefonico no es válido";
            $status = false;
            $json = null;            
        }

        if (! $status ) {
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
            $meta_response = MetaResponse::createResponse($arr);
        }
        return $status;
    }

    public function sendMessageFlyer(FacebookTokenService $tokenService,$ident,$phone,$template)
    {
        $status = false;
        $message = null;
        $json = null;
        $phonePattern = '/^\d{11,12}$/';
        $valid = preg_match($phonePattern, $phone);
        if ($ident && $phone && $valid) {
            $representant = Representant::where('ci_representant',$ident)->first();
            if ($representant) {
                $to = ($phone) ? $phone : '584145752242' ;
                $name = $representant->name; // Valor para {{1}}                  

                $url = env('FACEBOOK_URL'); // URL de la API de Facebook Graph        
                $accessToken = $tokenService->getAccessToken(); //dd($url,$accessToken);
                $mediaId = env('FACEBOOK_IMAGE_ADMON_ID'); 

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
                        'name' => $template, // Nombre de la plantilla (payment)
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
                                    ]
                                ],
                            ],
                        ],
                    ],
                ]);
                // Manejar la respuesta
                if ($response->successful()) {
                    $data = $response->json();//dd($data);
                    $arr = [
                        'message' => 'Notificación enviada con éxito.',
                        'ident' => $ident,
                        'phone' => $phone,
                        'template' => 'notifications_admon',
                        'messaging_product' => $data['messaging_product'],
                        'contact_input' => $data['contacts'][0]['input'],
                        'contact_wa_id' => $data['contacts'][0]['wa_id'],
                        'message_id' => $data['messages'][0]['id'],
                        'message_status' => $data['messages'][0]['message_status'],
                        'json' => $data,
                    ];
                    $meta_response = MetaResponse::createResponse($arr);
                    $status = true;    
                    $representant->status_whatsapp_verify = true;
                    $representant->save();
                } else {
                    $status = false;                    
                    $message .= "Error al enviar la notificación.";
                    $json = $response->json();
                }

                if ($response->failed()) {
                    $error = $response->json();                    
                    // Verifica si el error es relacionado con un número no registrado
                    if ($error['error']['code'] == 131047) {                        
                        $message .= 'Error al enviar el mensaje, el número no está registrado en WhatsApp.: ' . $error['error']['message'];        
                    }
                }
            } else {
                $status = false;             
                $message .= "Error al enviar la notificación. CI no encontrada";
                $json = null;
            }         
        } 

        if (! $valid ) {
            $message .= "Error al enviar la notificación. su número telefonico no es válido";
            $status = false;
            $json = null;            
        }

        if (! $status ) {
            $arr = [
                'message' => $message,
                'ident' => $ident,
                'phone' => $phone,
                'template' => 'notifications_admon',
                'messaging_product' => null,
                'contact_input' => null,
                'contact_wa_id' => null,
                'message_id' => null,
                'message_status' => null,
                'json' => $json,
            ];
            $meta_response = MetaResponse::createResponse($arr);
        }
        return $status;
    }

    public function sendMessageFlyerToJob(FacebookTokenService $tokenService)
    {
        $datas = collect();
        $timeWhatsApp = Carbon::now()->addDay()->setTime(7, 0, 0);
        $representants = Representant::representantFormaly();
        foreach ($representants as $representant) {
            $ident = $representant->ci_representant;
            $phone = $representant->whatsapp;
            $ident = $representant->ci_representant;            
            $template = "send_flyer";
            // $phone = "584145752242";
            // $timeWhatsApp = Carbon::now();
            //$state = $this->sendMessageFlyer($tokenService,$ident,$phone,$template);
            SendWhatsAppMessageFlyerJob::dispatch($ident, $phone, $template)->delay($timeWhatsApp);
            $timeWhatsApp = $timeWhatsApp->addSeconds(80);
            $datas->push($representant);
        }

        dd($datas);
    }
}
