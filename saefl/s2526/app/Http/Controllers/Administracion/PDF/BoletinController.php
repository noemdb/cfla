<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Jenssegers\Date\Date;

class BoletinController extends Controller
{
    public function positions($grado_id,$seccion_id,$lapso_id)
    {
        $orientacion = 'portrait'; //portrait,landscape
        $paper  = 'letter'; // legal, letter
        $baremo = new Baremo();
        $fecha = Carbon::now()->format('d-m-Y h:m A');

        $lapsos = Lapso::all();

        $grado = Grado::findOrFail($grado_id); $grado_id = $grado->id;
        $seccion = Seccion::findOrFail($seccion_id); $seccion_id = $seccion->id;
        $lapso = Lapso::find($lapso_id) ; $lapso_id = ($lapso) ? $lapso->id:null ;

        $estudiants = $seccion->getEstudiantPosicionPromedioLapso($lapso_id);

        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);

        $pensums = Pensum::where('grado_id',$grado_id)->get();

        $pevaluacion = Pevaluacion::orderby('id')->where('lapso_id',$lapso_id)->where('seccion_id',$seccion_id)->first();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make('administracion.boletins.pdf.positions',
        compact('estudiants','grado_id','grado',
            'seccion_id','seccion','lapso','lapso_id','pensums',
            'pestudio','baremos','pevaluacion','institucion',
            'autoridad1','autoridad2','fecha'))
        ->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // return $pdf->stream('Posicines de los Estudiantes según su PA');
        // return view('administracion.boletins.pdf.sabana', compact('grado_id','grado','seccion_id','seccion','lapso','lapso_id','pensums','pestudio','baremos','pevaluacion','institucion','autoridad1','autoridad2'));
        $name_file = 'Posicines de los Estudiantes según su PA';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
    }

    public function lote_corte($grado_id,$seccion_id,$lapso_id){

        $orientacion = 'portrait';
        $paper  = 'lettet';
        $baremo = new Baremo();

        $grado = Grado::findOrFail($grado_id);
        $seccion = Seccion::findOrFail($seccion_id);
        $lapso = Lapso::findOrFail($lapso_id);

        $lapsos = Lapso::all();
        $estudiants = $seccion->estudiants_in;
        $pensums = $grado->pensums->sortBy(function ($value, $key) {return (!empty($value->asignatura->order)) ? $value->asignatura->order:null;});

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);

        $view =  View::make('administracion.boletins.pdf.lotes.corte.'.$pestudio->code,
            compact('estudiants','baremos','baremo','pestudio','pensums','grado','seccion','lapsos','lapso','lapso_id','institucion','autoridad1','autoridad2'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // return $pdf->stream('Boletin');
        // return view('administracion.inscripciones.constancia.pdf', compact('inscripcion','institucion','autoridad1','autoridad2'));
        $name_file = 'Informe de Notas';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
    }

    public function corte($estudiant_id,$lapso_id){
        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $baremo = new Baremo();

        $estudiant = Estudiant::findOrFail($estudiant_id);
        $lapso = Lapso::findOrFail($lapso_id);
        $date = Carbon::now()->subMonth()->format('Y-m-d');

        $seccion = $estudiant->inscripcion->seccion;
        $grado = $seccion->grado;
        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);
        $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first();
        $pensums = $grado->pensums->sortBy(function ($value, $key) {return (!empty($value->asignatura->order)) ? $value->asignatura->order:null;});

        $lapsos = Lapso::all();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $compact = ['estudiant','seccion','grado','pestudio','baremos','baremo','profesor_guia','pensums','lapsos','lapso','lapso_id','institucion','autoridad1','autoridad2']; //dd(compact($compact));

        $view =  View::make('administracion.boletins.pdf.corte.'.$pestudio->code,compact($compact))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        $name_file = 'Corte de Notas';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
        // return $view;
    }

    public function sabanafull($grado_id,$seccion_id){

        $orientacion = 'landscape';
        $paper  = 'legal'; // legal, letter
        $fecha = Carbon::now()->format('d-m-Y');
        $baremo = new Baremo();

        $lapsos = Lapso::all();

        $grado = Grado::findOrFail($grado_id); $grado_id = $grado->id;
        $seccion = Seccion::findOrFail($seccion_id); $seccion_id = $seccion->id;

        $estudiants = $seccion->estudiants_in;

        $pestudio = $grado->pestudio;
        $baremos = $pestudio->baremos;
        //$baremos = $pestudio->getBaremos($lapso->id ?? null);

        $pensums = Pensum::where('grado_id',$grado_id)->get();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make('administracion.boletins.pdf.sabanafull',
        compact('estudiants','grado_id','grado','seccion_id','seccion','lapsos','pensums','pestudio','baremos','baremo','institucion','autoridad1','autoridad2','fecha'))
        ->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]);
        $pdf->loadHTML($view)->setPaper($paper, $orientacion);

        return $pdf->stream('Acta Discusión de Notas');

        // return view('administracion.boletins.pdf.sabanafull', compact('estudiants','grado_id','grado','seccion_id','seccion','lapsos','pensums','pestudio','baremos','institucion','autoridad1','autoridad2','fecha'));
    }

    public function lote_boletin($grado_id,$seccion_id,$lapso_id){

        $orientacion = 'portrait';
        $paper  = 'lettet';
        $baremo = new Baremo();

        // $seccion_id = (!empty($seccion_id)) ? $seccion_id :null ;
        // $lapso_id = (!empty($lapso_id)) ? $lapso_id :null ;

        $grado = Grado::findOrFail($grado_id);
        $seccion = Seccion::findOrFail($seccion_id);
        $lapso = Lapso::findOrFail($lapso_id);

        $lapsos = Lapso::all();
        $estudiants = $seccion->estudiants_in;
        $pensums = $grado->pensums->sortBy(function ($value, $key) {return (!empty($value->asignatura->order)) ? $value->asignatura->order:null;});

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);

        $view =  View::make('administracion.boletins.pdf.lotes.'.$pestudio->code,
            compact('estudiants','baremos','baremo','pestudio','pensums','grado','seccion','lapsos','lapso_id','lapso','institucion','autoridad1','autoridad2'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('Boletin');
        // return view('administracion.inscripciones.constancia.pdf', compact('inscripcion','institucion','autoridad1','autoridad2'));
    }

    public function sabana_profesor($pevaluacion_id){
        $orientacion = 'landscape';
        $paper  = 'legal';
        $baremo = new Baremo();
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        $profesor = Profesor::where('user_id',Auth::user()->id)->first();
        $pevaluacion = Pevaluacion::findOrFail($pevaluacion_id);

        $lapsos = Lapso::all();

        $pensum = $pevaluacion->pensum;
        $grado = $pensum->grado;
        $pensums = $grado->pensums;
        $seccion = $pevaluacion->seccion;
        $lapso = $pevaluacion->lapso;

        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);

        $evaluacions = Evaluacion::orderby('id')
            ->where('pevaluacion_id',$pevaluacion_id)
            ->get();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make('administracion.boletins.pdf.sabana_profesor',
        compact('profesor','pevaluacion','lapsos','pensum','pensums','grado','seccion','lapso','pestudio','pestudio','baremos',
        'evaluacions','institucion','autoridad1','autoridad2','fecha'))
        ->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('Acta Discusión de Notas');
        // return $view;
    }

    public function boletin($estudiant_id,$lapso_id){
        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $baremo = new Baremo();

        $estudiant = Estudiant::findOrFail($estudiant_id);
        $lapso = Lapso::findOrFail($lapso_id);

        $seccion = $estudiant->inscripcion->seccion;
        $grado = $seccion->grado;
        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);
        $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first();
        $pensums = $grado->pensums->sortBy(function ($value, $key) {return (!empty($value->asignatura->order)) ? $value->asignatura->order:null;});
        $pensums_subareas = $grado->pensums_subareas;
        // $paper = ($pestudio) ? $pestudio->paper : $paper ;

        $lapsos = Lapso::all();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make('administracion.boletins.pdf.'.$pestudio->code,
        compact('estudiant','seccion','grado','pestudio','baremos','baremo','profesor_guia','pensums','lapsos','lapso','lapso_id','institucion','autoridad1','autoridad2'))
        ->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // return $pdf->stream('Boletin');
        // return $view;
        $name_file = 'Informe de Notas';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
        // return $view;
    }

    public function sabana($grado_id,$seccion_id,$lapso_id)
    {
        $orientacion = 'landscape';
        $paper  = 'legal'; // legal, letter
        $baremo = new Baremo();
        $fecha = Carbon::now()->format('d-m-Y h:m A');

        $lapsos = Lapso::all();

        $grado = Grado::findOrFail($grado_id); $grado_id = $grado->id;
        $seccion = Seccion::findOrFail($seccion_id); $seccion_id = $seccion->id;
        $lapso = Lapso::find($lapso_id) ; $lapso_id = ($lapso) ? $lapso->id:null ;

        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);

        $pensums = Pensum::where('grado_id',$grado_id)->get();

        $pevaluacion = Pevaluacion::orderby('id')->where('lapso_id',$lapso_id)->where('seccion_id',$seccion_id)->first();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make('administracion.boletins.pdf.sabana',
        compact('grado_id','grado','seccion_id','seccion','lapso','lapso_id','pensums','pestudio','baremos','pevaluacion','institucion','autoridad1','autoridad2','fecha'))
        ->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('Acta Discusión de Notas');
        // return $view;
    }

    public function lote_sabana($pestudio_id,$lapso_id, Request $request)
    {

        $orientacion = 'landscape'; // landscape, portrait
        $paper  = 'legal'; // legal, letter

        $fecha = Carbon::now()->format('d-m-Y h:i A');
        $institucion = Institucion::OrderBy('created_at','DESC')->first();

        $pestudio = Pestudio::findOrFail($pestudio_id);
        $lapso = Lapso::findOrFail($lapso_id);
        $grados = $pestudio->grados;

        $view =  View::make('administracion.boletins.pdf.lotes.sabana', compact('pestudio','grados','lapso','pestudio_id','lapso_id','institucion','fecha'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('Actas Discusión de Notas - Lotes');
    }

    public function sabana_simple($grado_id,$seccion_id,$lapso_id,$pensum_id)
    {
        $orientacion = 'landscape';
        $paper  = 'lettet';
        $baremo = new Baremo();
        $profesor = Profesor::where('user_id',Auth::user()->id)->first();
        // $pevaluacion = Pevaluacion::findOrFail($pevaluacion_id);
        $pevaluacion = Pevaluacion::where('lapso_id',$lapso_id)->where('seccion_id',$seccion_id)->where('pensum_id',$pensum_id)->first();

        $lapsos = Lapso::all();

        $pensum = $pevaluacion->pensum;
        $grado = $pensum->grado;
        $seccion = $pevaluacion->seccion;
        $lapso = $pevaluacion->lapso;

        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);

        $evaluacions = Evaluacion::orderby('id')->where('pevaluacion_id',$pevaluacion->id)->get();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make('administracion.boletins.pdf.sabana_simple',
        compact('profesor','pevaluacion','lapsos','pensum','grado','seccion','lapso','pestudio','pestudio','baremo','baremos',
        'evaluacions','institucion','autoridad1','autoridad2'))
        ->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('Acta Discusión de Notas');
        // return $view;
    }

    public function resumen_final($seccion_id)
    {
        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'legal'; //legal, lettet
        $baremo = new Baremo();
        $seccion = Seccion::findOrFail($seccion_id); $seccion_id = $seccion->id;
        // $fecha = Date::now()->format('d-m-Y'); Session::get('pescolar_ffinal')
        $fecha_final = Session::get('pescolar_ffinal');
        $fecha = Carbon::createFromDate($fecha_final)->format('d-m-Y');
        $fecha_remision = Date::createFromDate($fecha_final);
        // $fecha = Date::now()->format('l j F Y');
        $ano = Date::now()->format('Y');

        $fecha_remision = now()->format('Y-m-d');
        $pestudio = $seccion->pestudio;
        if ($pestudio) $fecha_remision = ($pestudio->fecha_prosecucion) ?  Date::createFromDate($pestudio->fecha_prosecucion) : $fecha_remision;
        $fecha = $fecha_remision;

        $grado = $seccion->grado;
        // $pensums = $grado->pensums;
        $pensums = $grado->pensums->sortBy(function ($value, $key) {return (!empty($value->asignatura->order)) ? $value->asignatura->order:null;}); //dd($pensums);
        $pestudio = $grado->pestudio;
        $baremos = ($pestudio) ? $pestudio->baremos->sortBy('literal') : collect() ;
        $profesor_guia = $grado->profesor_guias->where('seccion_id',$seccion->id)->first();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make('administracion.boletins.pdf.resumen_final.'.$pestudio->code,
        compact('pestudio','grado','pensums','seccion','baremos','baremo','profesor_guia','fecha','fecha_remision','ano','institucion','autoridad1','autoridad2'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        $name_file = 'Resumen final del rendimiento estudiantíl - '.$grado->name.' - '.$seccion->name;
        // if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
        return $view;
    }
}
