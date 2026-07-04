<?php

namespace App\Http\Controllers\Api\Bot;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RepresentantController extends Controller
{
    public function info(Request $request)
    {
        //dd($request->all());
        // required headers
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        $json_message = null;
        $br = '\n';

        $data = json_decode(json_encode($request->all()), FALSE); //dd($data);

        // make sure json data is not incomplete
        if( !empty($data->query) && !empty($data->appPackageName) && !empty($data->messengerPackageName) && !empty($data->query->sender) && !empty($data->query->message) ) {

            $appPackageName = $data->appPackageName;
            $messengerPackageName = $data->messengerPackageName;
            $sender = $data->query->sender;
            $message = $data->query->message;
            $isGroup = $data->query->isGroup;
            $ruleId = $data->query->ruleId;

            $representant = Representant::where('ci_representant',$message)->first();

            if ($representant) {

                $ammount_expire_bill = $representant->total_exchange_ammount_expire_bill;

                $json_message =
                    "*Información Administrativa* ".
                    "\n *Nombre:* ".$representant->name.
                    "\n *CI:* ". $representant->ci_representant.
                    "\n *Deuda Vencida:* $".f_float($representant->total_exchange_ammount_expire_bill).
                    "\n *Índice de Morosidad:* ".$representant->late_index.'%'
                    ;

                $exchange_expire_bill = $representant->exchange_expire_bill_pendientes;
                if ($ammount_expire_bill > 0 ) {
                    if ($exchange_expire_bill->isNotEmpty()) {
                        $list = null;
                        foreach ($exchange_expire_bill as $expire_bill) {
                            $list .= "   -. ".$expire_bill['expire_bill_name']. ": $".$expire_bill['ammount'] . "\n";
                        }
                        $json_message .= "\n *Cuotas Pendientes:* \n".$list;
                    }
                }

                try {
                    $exchange_rate = ExchangeRate::whereDate('date',Carbon::now())->first() ;
                    if ($exchange_rate) {
                        $json_message .= "\n------------------------";
                        $json_message .= "\n *TDC BCV* ".Carbon::now()->format('d-m-y').":\n   Bs. ```".f_float($exchange_rate->ammount)."```";
                    } else {
                        $json_message .= "\n -No hay Tasa de Cambio Oficial registrada-";
                    }

                } catch (\Exception $e) {
                    $json_message .= "\n -No hay Tasa de Cambio Oficial registrada:-";
                }

            } else {
                $json_message = '*Cédula no registrada*';
            }

            // set response code - 200 success
            http_response_code(200);

            // send one or multiple replies to AutoResponder
            return json_encode(array("replies" => array(
                array("message" => $json_message)
            )));

            // or this instead for no reply:
            // echo json_encode(array("replies" => array()));
        }

        // tell the user json data is incomplete
        else{

            // set response code - 400 bad request
            http_response_code(400);

            // send error
            return json_encode(array("replies" => array(
                array("message" => "Error ❌"),
                array("message" => "JSON data is incomplete. Was the request sent by AutoResponder?")
            )));
        }
    }
}
