<?php

namespace App\Http\Controllers\Administracion\Chart;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiante\Ingreso;

class IngresosController extends Controller
{
    public function IngresosXDay(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos'; //dd($range);

        if ($range=='Todos') {
            $finicial = Carbon::now()->subYear(10)->startOfMonth();
            $ffinal = Carbon::now()->addYear(10);
        }else{
            $finicial = Carbon::now()->subDays($range);
            $ffinal = Carbon::now()->endOfMonth(); //dd($finicial,$ffinal);
        }

        $data = Ingreso::select(DB::raw('sum(exchange_ammount) as sum_ingreso_ammount'),
            // DB::raw('DAY(ingresos.date_transaction) as day'),
            DB::raw("DATE_FORMAT(ingresos.date_transaction, '%m-%d') as date"))
            ->Where('ingresos.date_transaction', '>=', $finicial)
            ->Where('ingresos.date_transaction', '<=', $ffinal)
            ->groupby('date')
            ->orderBy('date', 'asc')
            ->get();
        // dd($data);

        //INI m-d
        $label_month = array();
        $labels = $data->pluck('date');
        foreach ($labels as $key => $value) {
            $dateObj   = Date::createFromFormat('m-d', $value);
            $str_date = $dateObj->format('j').' '.ucfirst($dateObj->format('M'));
            $label_month[] = $str_date;
        }

        $values = $data->pluck('sum_ingreso_ammount');

        foreach ($values as $k => $v) {
            $values[$k] = round($v,2);
        }
        //FIN m-d

        // dd($labels, $label_month, $values,$finicial,$ffinal);

        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Núm. Oper.",
                    "backgroundColor"=>"rgba(128, 160, 128,0.2)",
                    "borderColor"=>"rgba(128, 160, 128,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function IngresosXDayDate(Request $request)
    {
        $range = $request->input('range');
        $date = Carbon::createFromFormat('Y-m-d', $range); //dd($range,$date);

        $finicial = $date->copy()->subDays(7);
        $ffinal = $date->copy()->addDays(10); //dd($range,$date,$finicial,$ffinal);

       $data = Ingreso::select(
            DB::raw('sum(exchange_ammount) as sum_ingreso_ammount'),
            DB::raw('count(id) as value'),
            // DB::raw('MONTH(date_transaction) as month'),
            DB::raw("DATE_FORMAT(date_transaction, '%m-%d') as date")
        )
            ->Where('date_transaction', '>=', $finicial)
            ->Where('date_transaction', '<=', $ffinal)
            // ->wherenull('deleted_at')
            ->groupby('date')
            ->orderBy('date_transaction', 'asc');
        $data = $data->get(); //dd($data);

        //INI nombre de los meses en español
        $label_month = array();
        $labels = $data->pluck('value','date');
        foreach ($labels as $date => $value) {
            $dateObj   = Date::createFromFormat('m-d', $date);
            $str_date = $dateObj->format('j').' '.ucfirst($dateObj->format('M'));
            $label_month[] = $str_date;
        }

        $values = $data->pluck('sum_ingreso_ammount');
        foreach ($values as $k => $v) {
            $values[$k] = round($v,2);
        }
        //FIN nombre de los meses en español

        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Monto ingresado",
                    "backgroundColor"=>"rgba(92, 160, 92,0.2)",
                    "borderColor"=>"rgba(92, 160, 92,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}
