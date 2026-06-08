<?php

namespace App\Http\Controllers\Administracion\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;


use App\Models\app\Planpago\ExchangeRate;

class ExchangeRateController extends Controller
{
    public function movimientocambiario(Request $request)
    {

        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $banco_id = (!empty($request->banco_id)) ? $request->banco_id:null;

        if ($range=='Todos') {
            $finicial = Carbon::now()->subYear(10);
            $ffinal = Carbon::now()->addYear(10);
        }else{
            $finicial = Carbon::now()->subDays($range);
            $ffinal = Carbon::now();
        }

        // $range = Carbon::now()->subMonth($months);

        $data = ExchangeRate::select(
                'exchange_rates.ammount as ammount',
                'exchange_rates.date',
                DB::raw('DAY(exchange_rates.date) as day'),
                DB::raw("DATE_FORMAT(exchange_rates.date, '%d-%m') as date_md")
            )
            ->Where('date', '>=', $finicial)
            ->Where('date', '<=', $ffinal)
            ->groupby('exchange_rates.date')
            ->orderBy('date', 'asc')
            ->get();

        //INI nombre de los meses en español
        $labels = $data->pluck('date_md');
        foreach ($labels as $key => $value) {
            $dateObj   = Date::createFromFormat('d-m', $value);
            $label_month[] = $dateObj->format('d').'-'.ucfirst($dateObj->format('M'));
        }
        $values = $data->pluck('ammount');
        //FIN nombre de los meses en español

        foreach ($values as $k => $v) {
            $values[$k] = round($v,2);
        }

        //dd($labels, $label_month, $values);

        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Monto Cambiario",
                    "backgroundColor"=>"rgba(192, 57, 43,0.2)",
                    "borderColor"=>"rgba(192, 57, 43,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}
