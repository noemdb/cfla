<?php

namespace App\Http\Controllers\Api\Bot;

use App\Http\Controllers\Controller;
use App\Models\app\Planpago\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    public function info(Request $request)
    {
        // required headers
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        $json_message = null;
        $data = json_decode(json_encode($request->all()), FALSE); //dd($data);

        // if(!empty($data->query) && !empty($data->appPackageName) && !empty($data->messengerPackageName) ){
        if(!empty($data->query) && !empty($data->appPackageName) && !empty($data->messengerPackageName) && !empty($data->query->sender) && !empty($data->query->message) ){

            $exchange_rate = ExchangeRate::whereDate('date',Carbon::now())->first() ; //dd($exchange_rate);

            if ($exchange_rate) {
                $json_message =
                    "*Tasa de Cambio BCV*  [".Carbon::now()->format('d-m-y')."]".
                    "\n ```Bs. ".f_float($exchange_rate->ammount)."```";
            } else {
                $json_message = "\n - No hay tasa de cambio oficial registrada -";
            }

            http_response_code(200);

            return json_encode(array("replies" => array(
                array("message" => $json_message)
            )));

        }

        else {

            http_response_code(400);

            return json_encode(array("replies" => array(
                array("message" => "Error ❌"),
                array("message" => "JSON data is incomplete. Was the request sent by AutoResponder?")
            )));
        }
    }
}
