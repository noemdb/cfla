<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\RegistroTitulo;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Jenssegers\Date\Date;

class RegistroTituloController extends Controller
{

    public function hoja_registro($registro_titulo_id)
    {
        $registro_titulo = RegistroTitulo::findOrFail($registro_titulo_id);
        $pestudio = $registro_titulo->pestudio;
        $titulos_seccions = $registro_titulo->getTitulosOrderCI();
        // $fecha   = (!empty($registro_titulo->fecha_egreso)) ? Date::createFromFormat('F Y', $registro_titulo->fecha_egreso): 'JULIO 2020';
        $fecha_egreso = Date::createFromDate($registro_titulo->fecha_egreso)->format('F Y');
        $fecha = Date::now()->format('d F Y');

        // $pescolar_ffinal = Session::get('pescolar_ffinal');
        // $fecha_remision = Date::createFromDate($pescolar_ffinal);

        $fecha_remision = now()->format('Y-m-d');
        $pestudio = $registro_titulo->pestudio;
        if ($pestudio) $fecha_remision = (isset($pestudio->fecha_promocion)) ?  $pestudio->fecha_promocion : $fecha_remision ;
        $fecha_remision = Date::createFromDate($fecha_remision);

        $estudiants = $registro_titulo->estudiants;

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1'); //REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3'); //JEFE DE CONTROL DE ESTUDIO

        $orientacion = 'portrait'; //portrait,landscape
        $paper  = 'legal'; // legal, letter

        $view =  View::make('administracion.registro_titulos.pdf.hoja_registro.'.$pestudio->code,
        compact('estudiants','registro_titulo','titulos_seccions','pestudio','institucion','autoridad1','autoridad2','fecha','fecha_egreso','fecha_remision'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper,$orientacion); //landscape,
        // return $pdf->stream('Hoja Registro de Título');
        return $view;
    }

    public function constanciaPromocion($estudiant_id)
    {
        $fecha = Date::now()->format('d F Y');
        $estudiant = Estudiant::findOrFail($estudiant_id);
        $pestudio = $estudiant->pestudio;
        $grado = $estudiant->grado;
        $pestudio_next = $estudiant->getPestudioNext($pestudio->id); //dd($pgrado);
        $grado_next = $estudiant->getGradoNext($grado->id); //dd($pgrado);

        // $pescolar_ffinal = Session::get('pescolar_ffinal');
        // $fecha_remision = Date::createFromDate($pescolar_ffinal);

        $fecha_remision = now()->format('Y-m-d');
        if ($pestudio) $fecha_remision = ($pestudio->fecha_promocion) ?  $pestudio->fecha_promocion : $fecha_remision ;
        $fecha_remision = Date::createFromDate($fecha_remision);


        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1'); //REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3'); //JEFE DE CONTROL DE ESTUDIO

        $orientacion = 'portrait'; //portrait,landscape
        $paper  = 'letter'; // legal, letter

        $view =  View::make('administracion.registro_titulos.pdf.promocion.'.$pestudio->code,
        compact('estudiant','pestudio','pestudio_next','grado','grado_next','fecha','fecha_remision','institucion','autoridad1','autoridad2','fecha'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper,$orientacion); //landscape,
        // return $pdf->stream('Constancia de Promoción');
        return $view;
    }

    public function constanciaPromocionLote($registro_titulo_id)
    {

        $fecha = Date::now()->format('d F Y');
        $registro_titulo = RegistroTitulo::findOrFail($registro_titulo_id);
        $pestudio = $registro_titulo->pestudio;
        $grado = $registro_titulo->grado;
        $titulos_seccions = $registro_titulo->getTitulosOrderCI();

        // $pescolar_ffinal = Session::get('pescolar_ffinal');
        // $fecha_remision = Date::createFromDate($pescolar_ffinal);

        $fecha_remision = now()->format('Y-m-d');
        if ($pestudio) $fecha_remision = ($pestudio->fecha_promocion) ?  $pestudio->fecha_promocion : $fecha_remision ;
        $fecha_remision = Date::createFromDate($fecha_remision);

        $estudiants = $grado->estudiants;

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1'); //REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3'); //JEFE DE CONTROL DE ESTUDIO

        $orientacion = 'portrait'; //portrait,landscape
        $paper  = 'letter'; // legal, letter

        $view =  View::make('administracion.registro_titulos.pdf.promocion.lote.'.$pestudio->code,
        compact('estudiants','pestudio','grado','fecha','institucion','autoridad1','autoridad2','fecha','fecha_remision'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper,$orientacion); //landscape,
        // return $pdf->stream('Constancia de Promoción');
        return $view;
    }

}
