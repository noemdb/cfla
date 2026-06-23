<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\RegistroTitulo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;

class TituloController extends Controller
{
    public function carta_culminacion($estudiant_id,$registro_titulo_id)
    {
        $estudiant = Estudiant::findOrFail($estudiant_id);
        $registro_titulo = RegistroTitulo::findOrFail($registro_titulo_id);
        $titulo = $estudiant->getTitulo($registro_titulo_id);
        $pestudio = $estudiant->grado->pestudio;

        $pescolar_ffinal = Session::get('pescolar_ffinal');
        $fecha_remision = Date::createFromDate($pescolar_ffinal);

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO

        $view =  \View::make('administracion.registro_titulos.pdf.carta_culminacion.'.$pestudio->code,
        compact('estudiant','registro_titulo','titulo','pestudio','institucion','autoridad1','autoridad2','fecha_remision'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('lettet','portrait'); //landscape,
        return $pdf->stream('Constancia de Inscripción');
    }
}
