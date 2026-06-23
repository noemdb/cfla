<?php

namespace App\Http\Controllers\Administracion\PDF;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Planpago\Recibo;
use Illuminate\Http\Request;

class ReciboController extends Controller
{
    public function recibo($id){
        $orientacion = 'portrait';
        $paper  = 'lettet';
        $recibo = Recibo::findOrFail($id);

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR

        $compact = ['recibo','institucion','autoridad1','autoridad2'];

        $view =  \View::make('administracion.receibts.recibos.pdf.recibo', compact($compact))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        return $pdf->stream('Recibo de Pago Representante');
        // return $view;
    }
}
