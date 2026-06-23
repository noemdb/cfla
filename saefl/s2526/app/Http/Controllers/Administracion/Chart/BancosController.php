<?php

namespace App\Http\Controllers\Administracion\Chart;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;


use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Pescolar;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\RegistroPago;

class BancosController extends Controller
{

    public function IngresoXdayMonth(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $label_month = Array();

        $pescolar = Pescolar::OrderBy('created_at','DESC')->first();
        $date_init = Carbon::createFromFormat('Y-m-d',$pescolar->finicial);

        if ($range=='Todos') {
            $finicial = Carbon::now()->subYear(10);
            $ffinal = Carbon::now()->addYear(10);
        }else{
            $finicial = $date_init->clone()->addMonths($range)->firstOfMonth();
            $ffinal = $date_init->clone()->addMonths($range)->endOfMonth();
        }

        $data = Ingreso::select(
                DB::raw('sum(exchange_ammount) as sum_ingreso_ammount'),
                DB::raw('count(id) as value'),
                DB::raw('DAY(date_transaction) as day'),
                DB::raw("DATE_FORMAT(date_transaction, '%m-%d') as date")
            )
            ->Where('date_transaction', '>=', $finicial)
            ->Where('date_transaction', '<=', $ffinal)

            ->wherenull('deleted_at')
            // ->groupby('day')
            ->groupby('date_transaction')
            ->orderBy('date_transaction', 'asc');
            // ->orderBy('month', 'asc');

        if (isset($banco_id)) {
            $data = $data->where('ingresos.banco_id',$banco_id);
        }
        $data = $data->get();

        //INI nombre de los meses en español
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

        //dd($labels, $label_month, $values);
        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Monto ingresado",
                    "backgroundColor"=>"rgba(150, 90, 92,0.2)",
                    "borderColor"=>"rgba(150, 90, 92,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function IngresoXdia(Request $request)
    {

        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $banco_id = (!empty($request->banco_id)) ? $request->banco_id:null;
        $label_month = Array();

        if ($range=='Todos') {
            $finicial = Carbon::now()->subYear(10);
            $ffinal = Carbon::now()->addYear(10);
        }else{
            $finicial = Carbon::now()->subDays($range);
            $ffinal = Carbon::now();
        }

        // $range = Carbon::now()->subMonth($months);

        $data = Ingreso::select(
                DB::raw('sum(exchange_ammount) as sum_ingreso_ammount'),
                DB::raw('count(id) as value'),
                // DB::raw('DAY(date_transaction) as day'),
                // DB::raw("DATE_FORMAT(date_transaction, '%m-%d') as date"),
                DB::raw('DAY(date_transaction) as day'),
                DB::raw("DATE_FORMAT(date_transaction, '%m-%d') as date")
            )
            ->Where('date_transaction', '>=', $finicial)
            ->Where('date_transaction', '<=', $ffinal)

            ->wherenull('deleted_at')
            // ->groupby('day')
            ->groupby('date_transaction')
            ->orderBy('date_transaction', 'asc');
            // ->orderBy('month', 'asc');

        if (isset($banco_id)) {
            $data = $data->where('ingresos.banco_id',$banco_id);
        }
        $data = $data->get();

        //dd($usersmonth);

        //INI nombre de los meses en español
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

        //dd($labels, $label_month, $values);

        $ChartDataSQL = [
            'labels'=>$label_month,
            'datasets'=>[
                [
                    "label"=>"Monto ingresado",
                    "backgroundColor"=>"rgba(150, 90, 92,0.2)",
                    "borderColor"=>"rgba(150, 90, 92,1)",
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }

    public function deuda_representate_concepto(Request $request)
    {

        $cuentaxpagar_name = ($request->input('range')!=null) ? $request->input('range') : null;

        /* INDIVIDUAL - PERIODOS ANTERIORES */

        $concepto_pago_individual = ConceptoPago::list_type('INDIVIDUAL');

        if (!empty($concepto_pago_individual->count())) {

            $labels[] = 'DAA';

            $count_concepto_pago_individual = $concepto_pago_individual->count();
            $concepto_cancelados_individual = ConceptoCancelado::list_type('INDIVIDUAL');
            $count_concepto_cancelados_individual = $concepto_cancelados_individual->count();
            $values[] = ($concepto_cancelados_individual) ? round ( 100 * ( 1 - ( $count_concepto_cancelados_individual / $count_concepto_pago_individual )) , 2 ) : null ;

        }

        /* DEUDAS GENERALES */

        $datas = ConceptoCancelado::list_type('GENERAL');

        if ($cuentaxpagar_name) {
            $datas = $datas->where('cuentaxpagars.name',$cuentaxpagar_name);
        }

        $datas = $datas->get();

        // dd($datas);

        foreach ($datas as $data) {

            $concepto_pagos = ConceptoPago::where('cuentaxpagar_id',$data->cuentaxpagar_id)->get();

            $fecha = Carbon::createFromDate($data->date_expiration)->endOfMonth()->format('Y-m-d');
            $estudiants = Estudiant::select('estudiants.*')->active('true')->WidthInscripcion($fecha)->get();

            $tota_concepto_pagos = $estudiants->count() * $concepto_pagos->count();

            $value = ( !empty($tota_concepto_pagos) ) ? (100 * ( ($tota_concepto_pagos - $data->count_concepto_cancelados ) / $tota_concepto_pagos ) ) : null;

            $value = ( $value < 0 ) ? 0 : $value ;

            $arr[$data->cuentaxpagar_name] = [
                'count_concepto_cancelados'=>$data->count_concepto_cancelados,
                'estudiants_count'=>$estudiants->count(),
                'concepto_pagos_count'=>$concepto_pagos->count(),
                'tota_concepto_pagos'=>$tota_concepto_pagos,
                'porcentage'=>round( $value, 2)
            ];

            $labels[] = $data->cuentaxpagar_name;
            $values[] = round( $value, 2);
        }

        for ($i=0; $i < (count($labels) + 1) ; $i++) {
            $rgb = rand(0,255).', '.rand(0,255).', '.rand(0,255);
            $backgroundColor[] = 'rgba('.$rgb.', 0.6)';
            $borderColor[] = 'rgba('.$rgb.', 1)';
        }


        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"% Representantes deudores",
                    "backgroundColor"=>$backgroundColor,
                    "borderColor"=>$borderColor,
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        // dd($datas,$arr);

        // $ChartDataSQL = [
        //     'labels'=>$labels,
        //     'datasets'=>[
        //         [
        //             "label"=>"% Representantes deudores",
        //             "backgroundColor"=>"rgba(100, 200, 150,0.2)",
        //             "borderColor"=>"rgba(100, 200, 150,1)",
        //             "borderWidth"=>2,
        //             "data"=>$values
        //         ]
        //     ]
        // ];

        return json_encode($ChartDataSQL);
    }

    public function IngresoXMonth(Request $request)
    {

        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $banco_id = (!empty($request->banco_id)) ? $request->banco_id:null;
        $label_month = Array();

        if ($range=='Todos') {
            $finicial = Carbon::now()->subYear(10);
            $ffinal = Carbon::now()->addYear(10);
        }else{
            $finicial = Carbon::now()->subMonth($range);
            $ffinal = Carbon::now();
        }

        // $range = Carbon::now()->subMonth($months);

        $data = Ingreso::select(DB::raw('sum(exchange_ammount) as sum_ingreso_ammount'),DB::raw('count(id) as value'),DB::raw('MONTH(date_transaction) as month'))
            ->Where('date_transaction', '>=', $finicial)
            ->Where('date_transaction', '<=', $ffinal)
            ->wherenull('deleted_at')
            ->groupby('month')
            ->orderBy('date_transaction', 'asc');
            // ->orderBy('month', 'asc');

        if (isset($banco_id)) {
            $data = $data->where('ingresos.banco_id',$banco_id);
        }
        $data = $data->get();

        //dd($usersmonth);

        //INI nombre de los meses en español
        $labels = $data->pluck('month');
        foreach ($labels as $key => $value) {
            $dateObj   = Date::createFromFormat('!m', $value);
            $label_month[] = ucfirst($dateObj->format('F'));
        }
        $values = $data->pluck('sum_ingreso_ammount');

        foreach ($values as $k => $v) {
            $values[$k] = round($v,2);
        }
        //FIN nombre de los meses en español

        //dd($labels, $label_month, $values);

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

    public function IngresoXMetodo(Request $request)
    {
        $range = ($request->input('range')!=null) ? $request->input('range') : 'Todos';
        $limit = ($request->input('limit')!=null) ? $request->input('limit') : 8;
        $banco_id = (!empty($request->banco_id)) ? $request->banco_id:null;
        $colors = Array();

        if($range=='Todos'){
            $finicial = Carbon::now()->subYear(1000)->format('Y-m-d');
            $ffinal = Carbon::now()->addYear(1000)->format('Y-m-d');
        }else{
            $finicial = Carbon::now()->subMonth($range)->format('Y-m-d');
            $ffinal = Carbon::now()->format('Y-m-d');
        }

        $ingresos = Ingreso::getMPCodIdSuCo($finicial,$ffinal,$limit,$banco_id); //dd($ingresos);

        $labels = $ingresos->pluck('code');
        $values = $ingresos->pluck('sum_exchange_ammount');

        foreach ($values as $k => $v) {
            $values[$k] = round($v,2);
        }

        for ($i=0; $i < count($labels) ; $i++) {
            $colors[] = 'rgba('.rand(0,255).', '.rand(0,255).', '.rand(0,255).', 1)';
        }

        $ChartDataSQL = [
            'labels'=>$labels,
            'datasets'=>[
                [
                    "label"=>"Monto",
                    "backgroundColor"=>$colors,
                    "borderColor"=>$colors,
                    "borderWidth"=>2,
                    "data"=>$values
                ]
            ]
        ];

        return json_encode($ChartDataSQL);
    }
}
