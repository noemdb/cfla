<?php

namespace App\Http\Controllers\Api\Bot;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\ExchangeRate;
use App\Models\app\Bot\Bmain;
use App\Models\app\Bot\Bmessege;
use App\Models\app\Bot\Boption;

class AutoresponderController extends Controller
{
    public $bmain, $bmain_id, $json_message, $message, $area;

    public function __construct()
    {
        $this->area = "ADMINISTRACION";
        $this->bmain = Bmain::where('area', $this->area)->where('status_active', 'true')->orderBy('created_at', 'desc')->first();
        $this->bmain_id = ($this->bmain) ? $this->bmain->id : null;
    }

    public function main(Request $request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $this->json_message = null;

        $data = json_decode(json_encode($request->all()), FALSE); //dd($data);

        if (
            !empty($data->query) &&
            !empty($data->appPackageName) &&
            !empty($data->messengerPackageName) &&
            !empty($data->query->sender) &&
            !empty($data->query->message)
        ) {
            $this->message = $data->query->message;

            // $bmain = Bmain::where('status_active','true')->orderBy('created_at','desc')->first(); //dd($bmain);
            if ($this->bmain_id) {
                if ($this->message > 0 && $this->message < 9) {
                    $boption = Boption::where('bmain_id', $this->bmain_id)->where('key', $this->message)->first(); //dd($boption);
                    if ($boption) {
                        // Opción 6: Tasa de cambio BCV — respuesta dinámica en vez de texto estático
                        if ((int) $boption->key === 6) {
                            $this->json_message = $this->getExchangeRateMessage();
                        } else {
                            $this->json_message = $boption->text;
                        }
                    } else {
                        $this->json_message = 'Opción no encontrada';
                    }
                } else {
                    $this->json_message = $this->getMenu();
                }
            }

            $this->saveMessage($data);

            // set response code - 200 success
            http_response_code(200);

            // send one or multiple replies to AutoResponder
            return json_encode(array("replies" => array(
                array("message" => $this->json_message)
            )));

            // or


        } else {
            http_response_code(400);
            return json_encode(array("replies" => array(
                array("message" => "Error ❌"),
                array("message" => "JSON data is incomplete. Was the request sent by AutoResponder?")
            )));
        }
    }

    /**
     * Genera el mensaje con la tasa de cambio BCV del día.
     * Se usa desde main() para la opción dinámica y desde sendExchangeRate().
     */
    protected function getExchangeRateMessage(): string
    {
        try {
            $exchange_rate = ExchangeRate::whereDate('date', Carbon::now())->first();
            if ($exchange_rate) {
                return "*Tasa de Cambio BCV*  [" . Carbon::now()->format('d-m-y') . "]" .
                    "\n ```Bs. " . f_float($exchange_rate->ammount) . "```";
            }
            return "\n - No hay tasa de cambio oficial registrada" .
                "\n - Las tasas de cambio se registran de lunes a viernes";
        } catch (\Exception $e) {
            return "\n - No hay tasa de cambio oficial registrada:-";
        }
    }

    public function sendInfoDebs(Request $request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $json_message = null;
        $br = '\n';

        $data = json_decode(json_encode($request->all()), FALSE); //dd($data);

        if (!empty($data->query)) {

            if (!empty($data->query->message)) {

                $message = $data->query->message;

                $representant = Representant::where('ci_representant', $message)->first();

                if ($representant) {

                    // $ammount_expire_bill = $representant->total_exchange_ammount_expire_bill;
                    $ammount_expire_bill = $representant->exchange_ammount_expire_bill;

                    $total_credito_exchange = $representant->total_credito_exchange;
                    $total_abono_exchange = $representant->total_abono_exchange;
                    $saldo_a_favor_exchange = $total_credito_exchange + $total_abono_exchange;

                    //$ammountBs = ($exchange_rate) ? ($ammount_expire_bill * $exchange_rate->ammount) : null;
                    //$ammountBs = (round($ammount_expire_bill, 2) >= 0.01) ? $ammountBs : 0;
                    //$ammountBs = ($ammountBs) ? round($ammountBs, 2) . ' (Según Tasa BCV del día)' : '[Sin Tasa BCV]';

                    $json_message =
                        "*Información Administrativa* " .
                        "\n *Nombre*: " . $representant->name .
                        "\n *CI*: " . $representant->ci_representant .
                        "\n *Deuda Vencida*: $" . round($ammount_expire_bill, 2);
                    $exchange_rate = ExchangeRate::whereDate('date', Carbon::now())->first();
                    if ($exchange_rate) {
                        $ammountBs = ($exchange_rate) ? ($ammount_expire_bill * $exchange_rate->ammount) : 0;
                        $ammountBs = (round($ammount_expire_bill, 2) >= 0.01) ? $ammountBs : 0;
                        $ammountBs = round($ammountBs, 2) . ' (Según Tasa BCV del día...)';
                        $json_message .= "\n *Deuda Vencida Bs*: " . $ammountBs;
                    }

                    // $exchange_expire_bill = $representant->exchange_expire_bill_pendientes;
                    $exchange_expire_bill = $representant->getExchangeExpireBillPendientesDecimal();
                    if (round($ammount_expire_bill, 2) > 0) {
                        if ($exchange_expire_bill->isNotEmpty()) {
                            $list = null;
                            foreach ($exchange_expire_bill as $expire_bill) {
                                if (round($expire_bill['ammount'], 2) > 0) {
                                    $list .= "\n  -. " . $expire_bill['expire_bill_name'] . ": ```$" . round($expire_bill['ammount'], 2) . '```';
                                }
                            }
                            $json_message .= "\n *Cuotas vencidas*:" . $list;
                        }
                    }

                    // $unexpire_bill = $representant->exchange_unexpire_bill_pendientes->first();
                    $unexpire_bills = $representant->exchange_unexpire_bill_pendientes->take(2);
                    if ($unexpire_bills->count()) {
                        $json_message .= "\n------------------------ \n";
                        $json_message .= "*Cuota(s) por vencer*:";
                        foreach ($unexpire_bills as $unexpire_bill) {
                            if ($unexpire_bill) {
                                if (!empty($unexpire_bill['ammount'])) {
                                    $ammount = $unexpire_bill['ammount'];
                                    // $json_message .= "------------------------\n ";
                                    $json_message .= "\n   -. " . $unexpire_bill['expire_bill_name'] . " - $```" . round($ammount, 2) . "``` | *" . f_date($unexpire_bill['date_expiration']) . "*";
                                }
                            }
                        }
                    }

                    if ($saldo_a_favor_exchange > 0) {
                        $json_message .= "\n------------------------ \n";
                        $json_message .= "*Saldo a favor disponibles*: $" . round($saldo_a_favor_exchange, 2);
                        $json_message .= "\n------------------------ \n";
                    }

                    $registropagos = $representant->getRegistroPagosForRepresentanId();
                    if ($registropagos->isNotEmpty()) {
                        $json_message .= "\n *Últimos Pagos Registrados:*";
                        $registropagos = ($registropagos->isNotEmpty()) ? $registropagos->take(3) : $registropagos;
                        $i = 0;
                        foreach ($registropagos as $registropago) {
                            $i++;
                            $json_message .= "\n📌 *N.F.*: " . $registropago->correlative;
                            $json_message .= "\n   📆 *Fecha*: " . $registropago->created_at->format('d-m-Y') ?? null;
                            $json_message .= "\n   #️⃣ *Cuota*: " . $registropago->cuentaxpagar->name ?? '';
                            $json_message .= "\n   💵 *Monto*: " . ' USD ```' . f_float($registropago->total_exchange_pagos_ammount) . '```';
                            $json_message .= "\n";
                        }
                        $json_message .= "\n------------------------";
                    }

                    $status_blacklist = $representant->status_blacklist;
                    if ($status_blacklist) {
                        $bad_exchange_ammount_expire_bill = $representant->bad_exchange_ammount_expire_bill;
                        if ($bad_exchange_ammount_expire_bill > 0) {
                            $json_message .= "\n------------------------";
                            $json_message .= "\n*Este representante incumplió con el compromiso de pago en las fechas correspondientes en otro período escolar*:";
                            $json_message .= "\nDeuda de períodos anteriores: ``` $" . round($bad_exchange_ammount_expire_bill, 2) . "```";
                            $json_message .= "\n------------------------";
                        }
                    }

                    // $json_message .= "\n------------------------";
                    $json_message .= "\n Los reportes de pago se hacen efectivo en 2 o 3 días hábiles, mientras se realizan las conciliaciones bancarias respectiva.";
                    $json_message .= "\n------------------------";

                    // $json_message .= "\n------------------------";
                    // $json_message .= "\n En los pagos realizados a través del *TPV Botón de Pago CFLA*, la verificación, concialición y registro es automático.";
                    // $json_message .= "\n------------------------";

                    // $json_message .= "\n------------------------";
                    $json_message .= "\n Correo Electrónico registrado: " . $representant->email;
                    $json_message .= "\n------------------------";

                    try {
                        $exchange_rate = ExchangeRate::whereDate('date', Carbon::now())->first();
                        if ($exchange_rate) {
                            $json_message .= "\n *Tasa BCV* " . Carbon::now()->format('d-m-y') . ":\n   Bs. ```" . round($exchange_rate->ammount, 2) . "```";
                        } else {
                            $json_message .= "\n -No hay Tasa de Cambio Oficial registrada-";
                        }
                    } catch (\Exception $e) {
                        $json_message .= "\n -No hay tasa de cambio oficial registrada:-";
                    }
                } else {
                    $json_message = '*Cédula no registrada*';
                }

                $this->saveMessage($data);

                http_response_code(200);

                return json_encode(array("replies" => array(
                    array("message" => $json_message)
                )));
            }
        } else {

            http_response_code(400);

            return json_encode(array("replies" => array(
                array("message" => "Error ❌"),
                array("message" => "JSON data is incomplete. Was the request sent by AutoResponder?")
            )));
        }
    }

    public function sendExchangeRate(Request $request)
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $data = json_decode(json_encode($request->all()), FALSE);

        if (!empty($data->query) && !empty($data->appPackageName) && !empty($data->messengerPackageName) && !empty($data->query->sender) && !empty($data->query->message)) {
            $json_message = $this->getExchangeRateMessage();

            $this->saveMessage($data);

            http_response_code(200);
            return json_encode(array("replies" => array(
                array("message" => $json_message)
            )));
        } else {
            http_response_code(400);
            return json_encode(array("replies" => array(
                array("message" => "Error ❌"),
                array("message" => "JSON data is incomplete. Was the request sent by AutoResponder?")
            )));
        }
    }

    public function getMenu()
    {
        if ($this->bmain) {
            $boptions = Boption::where('bmain_id', $this->bmain->id)->get(); //dd($this->bmain->id,$boptions);
            $message = $this->bmain->description . "\n \n";
            foreach ($boptions->sortBy('key') as $boption) {
                $message .= $boption->description . "\n";
            }
            return $message;
        }
    }

    public function saveMessage($data)
    {
        $arr = [
            'bmain_id' => $this->bmain_id,
            'app_package_name' => $data->appPackageName,
            'messenger_package_name' => $data->messengerPackageName,
            'sender' => $data->query->sender,
            'message' => $data->query->message,
            'is_group' => (empty($data->isGroup)) ? null : $data->isGroup,
            'rule_id' => (empty($data->rule_id)) ? null : $data->rule_id,
        ];
        $messege = Bmessege::create($arr);
    }
}
