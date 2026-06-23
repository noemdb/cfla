<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Institucion;
use App\Models\app\Estudiante\CreditoAFavor;

class CreditoAFavorController extends Controller
{
    public function libro(Request $request){

        $orientacion = 'portrait';
        $paper  = 'lettet';
        $creditoafavors = collect();
        $total_credito = null;
        $institucion = Institucion::OrderBy('created_at','DESC')->first();

        $finicial           = (!empty($request->finicial)) ? $request->finicial : null ;
        $ffinal             = (!empty($request->ffinal)) ? $request->ffinal : null ;
        $ci                 = (!empty($request->ci)) ? $request->ci : null  ;
        $state              = (!empty($request->state)) ? $request->state : null  ;

        if (count($request->all())>0) {

            $creditoafavors = CreditoAFavor::withTrashed()
            ->select('credito_a_favors.*')
            ->selectRaw('sum(credito_a_favors.credito_ammount) as total_credito_ammount')
            ->join('estudiants', 'estudiants.id', '=', 'credito_a_favors.estudiant_id')
            ->join('representants', 'representants.id', '=', 'credito_a_favors.representant_id')
            ->groupby('representants.id')
            // ->groupby('credito_a_favors.id')
            ->OrderBy('created_at','desc');

            $creditoafavors = (isset($finicial)) ? $creditoafavors->wheredate('credito_a_favors.created_at','>=',$finicial) : $creditoafavors;
            $creditoafavors = (isset($ffinal)) ? $creditoafavors->wheredate('credito_a_favors.created_at','<=',$ffinal) : $creditoafavors;

            if ($ci) {
                $creditoafavors = $creditoafavors->where('estudiants.ci_estudiant', 'like', "%".$ci."%");
                $creditoafavors = $creditoafavors->Orwhere('representants.ci_representant', 'like', "%".$ci."%");
            }

            if ($state=='APLICADO') {
                $creditoafavors = $creditoafavors
                    ->join('registro_pagos', 'registro_pagos.id', '=', 'credito_a_favors.registro_pago_id')
                    ->join('credito_aplicados', 'credito_a_favors.id', '=', 'credito_aplicados.credito_a_favor_id')
                    ->whereNull('credito_aplicados.deleted_at')
                    ->whereNull('registro_pagos.deleted_at')
                    ->whereNotNull('credito_a_favors.deleted_at')
                    ;
            }
            if ($state=='NO APLICADO') { $creditoafavors = $creditoafavors->whereNull('credito_a_favors.deleted_at'); }

            $creditoafavors = $creditoafavors->get();

            $total_credito = $creditoafavors->sum('credito_ammount');

        }

        $view =  \View::make('administracion.creditoafavors.libro.pdf', compact('creditoafavors','institucion','finicial','ffinal','total_credito','state'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('libro_creditoafavors');
        // return view('administracion.configuraciones.banco.libro.pdf', compact('ingresos','metodos','banco','institucion','finicial','ffinal'));
    }
}
