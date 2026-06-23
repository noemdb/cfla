<?php

//https://github.com/Caprice7894/tasa-cambio-bcv

namespace App\Http\Controllers\Restapi\Exchange;
// app/Http/Controllers/Restapi/Exchange/CapriceController.php

use App\Http\Controllers\Controller;
use App\Models\app\Planpago\CalendarEvent;
use Illuminate\Http\Request;

use App\Models\app\Planpago\ExchangeRate;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;

class CapriceController extends Controller
{

    public function setExchangeRateTodateCFLA()
    {
        $ammount = ExchangeRate::getAmmountExchange(); // dd($ammount);
        if (empty($ammount)) {

            $now = Carbon::now(); //dd($today);
            $date = $now->copy()->format('Y-m-d'); //dd($today);
            $observations = "Registro automatizado a través de los servicios internos del SAEFL";

            $exchange_ammount = $this->getExchanRateToday();  //dd($exchange_ammount);

            if ($exchange_ammount) {

                $dayOfTheWeek = $now->dayOfWeek; //dd($dayOfTheWeek);
                $dateBack = $now->copy()->subDay();

                if ($dayOfTheWeek < 1 || $dayOfTheWeek > 5) {// verifica sabados y domingos
                    $observations .= ", TDC tomada del ".$dateBack." - SAB,DOM";
                    $exchange_ammount = ExchangeRate::getAmmountExchangeNear($dateBack);
                } else {
                    $calendar_event = CalendarEvent::whereDate('date',$date)->first();
                    if ($calendar_event) { // verifica eventos y días feriados registrados
                        $observations .= ", TDC tomada del ".$dateBack." - Día feriado [".$date."] - Calendario de Eventos.";
                        $exchange_ammount = ExchangeRate::getAmmountExchangeNear($dateBack);
                    }
                }

                if ($exchange_ammount) {
                    $arr = [
                        'currency_id'=>1, //Bolivares
                        'currency_referential_id'=>1, //Dolar
                        'date'=>$date,
                        'ammount'=>round($exchange_ammount,2),
                        'source'=>'Caprice',
                        'name'=>'Tarea Programada',
                        'observations'=>$observations,
                        'user_id'=>1,
                    ];
                    $exchange_rate = ExchangeRate::create($arr);
                }

                // $exchange_rate = New ExchangeRate;
                // $exchange_rate->fill($arr);
                // $exchange_rate->id = 1000000;

                return $exchange_rate;
            }
        }
    }

    public function setExchangeRateTodate()
    {
        $ammount = ExchangeRate::getAmmountExchange(); //dd($ammount);
        if (empty($ammount)) {
            $today = Carbon::now()->format('Y-m-d'); //dd($today);
            $exchange_ammount = $this->getExchanRateToday();  //dd($exchange_ammount);
            if ($exchange_ammount) {
                $arr = [
                    'currency_id'=>1, //Bolivares
                    'currency_referential_id'=>1, //Dolar
                    'date'=>$today,
                    'ammount'=>round($exchange_ammount,2),
                    'source'=>'Caprice',
                    'name'=>'Tarea Programada',
                    'observations'=>'Registro automatizado a travéz de los servicios internos del SAEFL',
                    'user_id'=>1,
                ];
                $exchange_rate = ExchangeRate::create($arr);

                // $exchange_rate = New ExchangeRate;
                // $exchange_rate->fill($arr);
                // $exchange_rate->id = 1000000;

                return $exchange_rate;
            }
        }
    }

    public function getExchanRateToday()
    {
        $client = new Client([
            'base_uri' => env('API_EXCHANGE_CAPRICE'), // API_EXCHANGE_CAPRICE="https://tasa-dolar.fly.dev/"
            'timeout'  => 30.0, //default 2.0 (milisecond)
            'verify' => false,
        ]);
        try {
            $response = $client->request('GET');
            if ($response->getStatusCode() == 200) {
                $bodyResponse = json_decode($response->getBody());
                if ($bodyResponse->divisas) {
                    if ($bodyResponse->divisas->USD) {
                        return $bodyResponse->divisas->USD;
                    }
                }
            }
        } catch (Exception $e) {
            $error = $e; //dd($error);
        }
    }

}
