<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\MetodoPago;
use Illuminate\Support\Facades\DB;

class IngresoController extends Controller
{
    public function movimeintos(Request $request)
    {

        $orientacion = 'portrait';
        $paper  = 'lettet';

        $banco_id = (!empty($request->banco_id)) ? $request->banco_id : null ;
        $finicial = (!empty($request->finicial)) ? $request->finicial :null ;
        $ffinal = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $ci = (!empty($request->ci)) ? $request->ci : null ;
        $number_i_pay = (!empty($request->number_i_pay)) ? $request->number_i_pay : null ;

        $institucion = Institucion::OrderBy('created_at','DESC')->first();

        $ingresos = collect();
        $metodos = collect();

        if (count($request->all())>0) {

            $ $ingresos = Ingreso::OrderBy('created_at','desc')
            // ->withTrashed()
            ->select('ingresos.*')
            ->join('estudiants', 'estudiants.id', '=', 'ingresos.estudiant_id')
            ->join('representants', 'representants.id', '=', 'ingresos.representant_id')

            // ->where('ingresos.number_i_pay', 'NOT LIKE', "%BORRADO%")
            ;

            $ingresos = (isset($finicial)) ? $ingresos->wheredate('ingresos.date_transaction','>=',$finicial) : $ingresos;
            $ingresos = (isset($ffinal)) ? $ingresos->wheredate('ingresos.date_transaction','<=',$ffinal) : $ingresos;
            $ingresos = (isset($banco_id)) ? $ingresos->where('ingresos.banco_id',$banco_id) : $ingresos;

            if ($ci) {
                $ingresos = $ingresos->where( function($query) use ($ci) {
                    $query->where('estudiants.ci_estudiant', 'like', "%".$ci."%")
                          ->orWhere('representants.ci_representant', 'like', "%".$ci."%");
                });
            }

            $ingresos = (isset($number_i_pay)) ? $ingresos->where('ingresos.number_i_pay', 'like', "%".$number_i_pay."%") : $ingresos;

            $metodos = clone $ingresos;
            $metodos = $metodos
                ->select('metodo_pagos.id as id','metodo_pagos.name as name')
                ->selectRaw('sum(ingresos.ingreso_ammount) as total')
                ->selectRaw('count(ingresos.id) as count')
                ->groupby('metodo_pagos.id')
                ->get();

            $ingresos = $ingresos
                ->selectRaw('sum(ingresos.ingreso_ammount) as ingreso_ammount_total')
                ->groupby('ingresos.number_i_pay')
                ->get();

        }

        // dd($ingresos,$metodos);

        $view =  \View::make('administracion.ingresos.pdf.movimeintos',
        compact('institucion','ingresos','metodos','banco_id','finicial','ffinal','ci','number_i_pay'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('libros_movimientos');
        // return view('administracion.configuraciones.banco.libro.pdf', compact('ingresos','metodos','banco','institucion','finicial','ffinal'));
    }
}
