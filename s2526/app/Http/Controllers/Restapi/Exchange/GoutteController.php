<?php

namespace App\Http\Controllers\Restapi\Exchange;

use App\Http\Controllers\Controller;
use App\Models\app\Planpago\CalendarEvent;
use App\Models\app\Planpago\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;

use Goutte\Client;
use GuzzleHttp\Client as ClientHttp;
use Symfony\Component\HttpClient\HttpClient;

class GoutteController extends Controller
{
    public $url = "https://www.bcv.org.ve";

    public function getExchanRateToday()
    {
        $client = new Client(HttpClient::create(['verify_peer' => false, 'verify_host' => false]));
        $website = $client->request('GET', $this->url);
        $elements = $website->filter('#dolar .centrado')->each(function ($node) {
            return $node->text();
        });
        if (count($elements)) {
            return str_replace(',','.',$elements[0]);
        }
    }
    
    public function setExchangeRateTodateCFLA()
    {
        $ammount = ExchangeRate::getAmmountExchange();
        $exchange_rate = collect();
        if (empty($ammount)) {
            $now = Carbon::now();
            $date = $now->copy()->format('Y-m-d');
            $observations = "Registro automatizado a través de los servicios internos del SAEFL";
            $exchange_ammount = $this->getExchanRateToday();
            if ($exchange_ammount) {
                $dayOfTheWeek = $now->dayOfWeek;
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
                        'source'=>'Goutte',
                        'name'=>'Tarea Programada',
                        'observations'=>$observations,
                        'user_id'=>1,
                    ];
                    $exchange_rate = ExchangeRate::create($arr);
                }
                return $exchange_rate;
            }
        }
    }

}
