<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Pestudio;

class PensumNotaController extends Controller
{
    public function listado(Request $request){

        $pestudios = Pestudio::all()->where('status_active','true');

        $institucion = Institucion::OrderBy('created_at','DESC')->first();

        $orientacion = 'portrait'; //landscape
        $paper  = 'lettet';

        $view = \View::make('administracion.configuraciones.pensums.template.pdf', compact('pestudios','institucion'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('libros_estudiants');
    }
}
