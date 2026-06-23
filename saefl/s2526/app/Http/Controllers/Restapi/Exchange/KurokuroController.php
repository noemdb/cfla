<?php

namespace App\Http\Controllers\Restapi\Exchange;

use App\Http\Controllers\Controller;
use App\Models\app\Planpago\ExchangeRate;
use Carbon\Carbon;
// use Illuminate\Http\Request;

use GuzzleHttp\Client;

class KurokuroController extends Controller
{
    public function setExchangeRate()
    {
        if (ExchangeRate::getAmmountExchange() == null) {

            $client = new Client([
                'base_uri' => env('API_EXCHANGE_KURO'), // API_EXCHANGE_KURO="https://bcv-api.deno.dev/v1/exchange"
                'timeout'  => 30.0, //default 2.0 (milisecond)
                'verify' => false,
            ]);

            $response = $client->request('GET');

            if ($response->getStatusCode() == 200) {

                $bodyResponse = json_decode($response->getBody());

                $info_exchange = array_shift($bodyResponse);
                $currency = $info_exchange->currency;
                $exchange = $info_exchange->exchange;
                $date = $info_exchange->date;
                $date = Carbon::parse($date)->setTimezone('America/Caracas');

                $now = Carbon::now();

                $diffInDays = $now->diffInDays($date);

                if ($diffInDays <= 1) {
                    $arr = [
                        'currency_id'=>1, //Bolivares
                        'currency_referential_id'=>1, //Dolar
                        'date'=>$now->format('Y-m-d'),
                        'ammount'=>$exchange,
                        'source'=>'Otros',
                        'user_id'=>1,
                    ];
                    $exchange_rate = ExchangeRate::create($arr);
                    return $exchange_rate;
                }
            }
        }






    }

}
