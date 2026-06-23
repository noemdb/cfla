<?php

namespace App\Http\Controllers\Meta;

use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\ExchangeRate;
use App\Services\FacebookTokenService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

trait webHookDefaultTrait
{

    private function getAdministrativeInfo($messageBody)
    {
        // Responder con un mensaje específico para 7 u 8 dígitos
        $representant = Representant::where('ci_representant', $messageBody)->first(); //dd($representant);
        $message = null;

        if ($representant) {

            $ammount_expire_bill = $representant->total_exchange_ammount_expire_bill;

            $exchange_rate = ExchangeRate::whereDate('date', Carbon::now())->first();
            $ammountBs = ($exchange_rate) ? ($ammount_expire_bill * $exchange_rate->ammount) : null;
            $ammountBs = ($ammountBs) ? round($ammountBs, 2) . ' (Según Tasa BCV del día)' : '[Sin Tasa BCV]';

            $message =
                "🔔 *Información Administrativa* " .
                "\n👤 *Nombre*: " . $representant->name .
                "\n🆔 *CI*: " . $representant->ci_representant .
                "\n💵 *Deuda Vencida*: USD" . round($ammount_expire_bill, 2);
            if ($exchange_rate) {
                $ammountBs = $ammount_expire_bill * $exchange_rate->ammount;
                $ammountBs = round($ammountBs, 2); // Aseguramos que la cantidad tiene dos decimales
                $ammountBs = number_format($ammountBs, 2, ',', '.'); // Formato moneda con separadores
                $ammountBs = $ammountBs . ' (Según Tasa BCV del día)';
                $message .= "\n *Deuda Vencida Bs*: " . $ammountBs;
            }

            // $exchange_expire_bill = $representant->exchange_expire_bill_pendientes;
            $exchange_expire_bill = $representant->getExchangeExpireBillPendientesDecimal();
            if (round($ammount_expire_bill, 2) > 0) {
                if ($exchange_expire_bill->isNotEmpty()) {
                    $list = null;
                    foreach ($exchange_expire_bill as $expire_bill) {
                        if (round($expire_bill['ammount'], 2) > 0) {
                            $list .= "\n  -. 📌 " . $expire_bill['expire_bill_name'] . ": ```$" . round($expire_bill['ammount'], 2) . '```';
                        }
                    }
                    $message .= "\n📅 *Cuotas vencidas*:" . $list;
                }
            }

            // $unexpire_bill = $representant->exchange_unexpire_bill_pendientes->first();
            $unexpire_bills = $representant->exchange_unexpire_bill_pendientes->take(2);
            if ($unexpire_bills->count()) {
                $message .= "\n------------------------ \n";
                $message .= "📅 *Cuota(s) por vencer*:";
                foreach ($unexpire_bills as $unexpire_bill) {
                    if ($unexpire_bill) {
                        if (!empty($unexpire_bill['ammount'])) {
                            $ammount = $unexpire_bill['ammount'];
                            // $message .= "------------------------\n ";
                            $message .= "\n   -. " . $unexpire_bill['expire_bill_name'] . " - $```" . round($ammount, 2) . "``` | *" . f_date($unexpire_bill['date_expiration']) . "*";
                        }
                    }
                }
            }

            $status_blacklist = $representant->status_blacklist;
            if ($status_blacklist) {
                $bad_exchange_ammount_expire_bill = $representant->bad_exchange_ammount_expire_bill;
                if ($bad_exchange_ammount_expire_bill > 0) {
                    $message .= "\n------------------------";
                    $message .= "\n❌ *Este representante incumplió con el compromiso de pago en las fechas correspondientes en otro período escolar*:";
                    $message .= "\n💵 Deuda de períodos anteriores: ``` $" . round($bad_exchange_ammount_expire_bill, 2) . "```";
                    $message .= "\n------------------------";
                }
            }

            $message .= "\n------------------------";
            $message .= "\n⏳ Los reportes de pago se hacen efectivo en 2 o 3 días hábiles, mientras se realizan las conciliaciones bancarias respectiva.";
            // $message .= "\n------------------------";
            // $message .= "\n------------------------";
            // $message .= "\n💡 En los pagos realizados a través del *TPV Botón de Pago CFLA*, la verificación, concialición y registro es automático.";
            // $message .= "\n------------------------";
            $message .= "\n------------------------";
            $message .= "\n📧 Correo Electrónico registrado: " . $representant->email;
            $message .= "\n------------------------";

            try {
                $exchange_rate = ExchangeRate::whereDate('date', Carbon::now())->first();
                if ($exchange_rate) {
                    $message .= "\n *Tasa BCV* " . Carbon::now()->format('d-m-y') . ":\n   Bs. ```" . round($exchange_rate->ammount, 2) . "```";
                } else {
                    $message .= "\n -No hay Tasa de Cambio Oficial registrada-";
                }
            } catch (\Exception $e) {
                $message .= "\n -No hay tasa de cambio oficial registrada:-";
            }

            $message .= "\n------------------------";
            $message .= "\n *Últimos Pagos Registrados:*";
            $registropagos = $representant->getRegistroPagosForRepresentanId();
            $registropagos = ($registropagos->isNotEmpty()) ? $registropagos->take(3) : $registropagos;
            $i = 0;
            foreach ($registropagos as $registropago) {
                $i++;
                // $message .= "\n📌 *Id*: ".$registropago->id;
                $message .= "\n   📆 *Fecha*: " . $registropago->created_at->format('d-m-Y') ?? null;
                $message .= "\n   #️⃣ *Cuota*: " . $registropago->cuentaxpagar->name ?? '';
                $message .= "\n   💵 *Monto*: " . ' USD ```' . f_float($registropago->total_exchange_pagos_ammount) . '```';
                $message .= "\n";
            }
        } else {
            $message = '❌ *Cédula no registrada*';
        }

        return $message;
    }

    private function sendSpecificResponse($digit)
    {
        $message = $responseMessages[$digit] ?? "Opción no válida.";
        switch ($digit) {
            case '1':
                $ammount = ExchangeRate::getAmmountExchange(); //dd($ammount);
                $message = "*Tasa de Cambio BCV* [" . Carbon::now()->format('d-m-y') . "]" . "\n ```Bs. " . f_float($ammount) . "```";
                break;
            case '2':
                $message = "*https://uefrayluisamigosf.com/reporte*";
                break;
            case '3':
                $message = "*https://uefrayluisamigosf.com/pago*";
                break;
            default:
                $message = "*Opción no válida.* ❌";
                break;
        }
        return $message;
    }    

    /**
     * Extrae el contenido de un mensaje según su tipo.
     *
     * @param array $message
     * @return mixed|null
     */
    private function extractMessageContent(array $message)
    {
        $type = $message['type'] ?? null;

        switch ($type) {
            case 'text':
                return $message['text']['body'] ?? null;

            case 'image':
                return json_encode([
                    'media_id' => $message['image']['id'] ?? null,
                    'caption' => $message['image']['caption'] ?? null
                ]);

            case 'document':
                return json_encode([
                    'media_id' => $message['document']['id'] ?? null,
                    'filename' => $message['document']['filename'] ?? null
                ]);

            case 'audio':
                return json_encode([
                    'media_id' => $message['audio']['id'] ?? null
                ]);

            case 'video':
                return json_encode([
                    'media_id' => $message['video']['id'] ?? null,
                    'caption' => $message['video']['caption'] ?? null
                ]);

            default:
                return null;
        }
    }

    // Función recursiva para buscar claves específicas
    public function search_key($array, $key_to_search)
    {
        foreach ($array as $key => $value) {
            if ($key == $key_to_search) {
                return $value;  // Retorna el primer valor encontrado
            }

            if (is_array($value)) {
                $result = $this->search_key($value, $key_to_search);
                if ($result !== null) {
                    return $result;  // Retorna el valor encontrado en la búsqueda recursiva
                }
            }
        }

        return null;  // Retorna null si no se encuentra la clave
    }

    //funcion de prueba
    public function sendMessageSingleGet(Request $request)
    {        
        $phone = $request->phone;
        $text = $request->text;

        $phonePattern = '/^\d{11,12}$/';

        if (preg_match($phonePattern, $phone)) {
            // URL de la API de Facebook Graph
            $url = env('FACEBOOK_URL'); //dd($url,$phone,$text);
            $tokenService = New FacebookTokenService;   
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
                dd("Mensaje enviado a {$phone}: {$text}");
            } else {
                dd("Error al enviar el mensaje: {$response->body()}");
            }
        } else {
            dd("Error al enviar el mensaje: {$phone} no cumple con el patron de validación");
        }        
    }
}
