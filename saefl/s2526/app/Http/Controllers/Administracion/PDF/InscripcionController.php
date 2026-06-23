<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Session;

use App\Models\app\Pescolar;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Tinscripcion;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use Carbon\Carbon;
use Jenssegers\Date\Date;

class InscripcionController extends Controller
{
    public function prosecucionLote($grado_id,$seccion_id)
    {
        // $estudiant = Estudiant::findOrFail($estudiant_id);
        $grado = Grado::findOrFail($grado_id);
        $seccion = Seccion::findOrFail($seccion_id);
        $estudiants = $seccion->estudiants_in; //dd($estudiants);
        $pestudio = $grado->pestudio;//dd($pestudio);

        // $pescolar_ffinal = Session::get('pescolar_ffinal');
        // $fecha_remision = Date::createFromDate($pescolar_ffinal);

        $fecha_remision = now()->format('Y-m-d');
        if ($pestudio) $fecha_remision = ($pestudio->fecha_certificacion) ?  $pestudio->fecha_certificacion : $fecha_remision ; //dd($fecha_expedicion);
        $fecha_remision = Date::createFromDate($fecha_remision);

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO

        $view =  \View::make('administracion.inscripciones.constancia.prosecucion.lotes.'.$pestudio->code,
        compact('estudiants','pestudio','grado','institucion','autoridad1','autoridad2','fecha_remision'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('lettet','portrait'); //landscape,
        // return $pdf->stream('Constancia de Prosecución');
        return $view;
    }
    public function prosecucion($estudiant_id)
    {
        $estudiant = Estudiant::findOrFail($estudiant_id);
        $grado = $estudiant->grado;
        $pgrado = $estudiant->getGrado($grado->id + 1); //dd($pgrado);
        $pestudio = $estudiant->pestudio;//dd($pestudio);

        // $pescolar_ffinal = Session::get('pescolar_ffinal');
        // $fecha_remision = Date::createFromDate($pescolar_ffinal);

        $fecha_remision = now()->format('Y-m-d');
        if ($pestudio) $fecha_remision = ($pestudio->fecha_prosecucion) ?  Date::createFromDate($pestudio->fecha_prosecucion) : $fecha_remision;

        $pestudio_next = $estudiant->getPestudioNext($pestudio->id); //dd($pgrado);
        $grado_next = $estudiant->getGradoNext($grado->id); //dd($pgrado);

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO

        $view =  \View::make('administracion.inscripciones.constancia.prosecucion.'.$pestudio->code,
        compact('estudiant','pestudio','pestudio_next','grado','grado_next','institucion','autoridad1','autoridad2','pgrado','fecha_remision'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('lettet','portrait'); //landscape,
        // return $pdf->stream('Constancia de Prosecución');
        return $view;
    }

    public function book(Request $request){
        $orientacion = 'portrait';
        $paper  = 'lettet';
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $pescolar_id = ($request->get('pescolar_id')) ? $request->get('pescolar_id'): Session::get('pescolar_id');
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get();
        $grados = Grado::Orderby('id','asc')->where('status_active','true')->get();
        $seccions = Seccion::Orderby('id','asc')->get();
        $tinscripcions = Tinscripcion::Orderby('id','asc')->get();
        $std_ciaca_siadm = Inscripcion::std_ciaca_siadm();
        $std_siaca_ciadm = Administrativa::std_siaca_ciadm();

        $view =  \View::make('administracion.inscripciones.book.pdf', compact('std_ciaca_siadm','std_siaca_ciadm','pescolar_id','autoridad1','autoridad2','institucion','pestudios','grados','seccions','tinscripcions'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('book');
        // return view('administracion.inscripciones.list.pdf',compact('pestudios','order','grado_id '));
    }

    public function constanciapdf($id){

        $estudiant = Estudiant::findOrFail($id);
        $inscripcion = $estudiant->inscripcion;
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO

        $view =  \View::make('administracion.inscripciones.constancia.pdf.inscripcion', compact('inscripcion','estudiant','institucion','autoridad1','autoridad2'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('lettet','portrait'); //landscape,
        return $pdf->stream('Constancia de Inscripción');
    }

    public function cestudiopdf($id){
        $estudiant = Estudiant::findOrFail($id);
        $inscripcion = $estudiant->inscripcion;
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO

        $view =  \View::make('administracion.inscripciones.constancia.pdf.estudio', compact('inscripcion','institucion','autoridad1','autoridad2'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('lettet','portrait'); //landscape,
        return $pdf->stream('Constancia de Estudio');
        // return $view;
    }

    public function listpdf(Request $request){
        $order = $request->order;
        $orientacion = $request->orientacion;
        $paper = $request->paper;
        $grado_id  = $request->grado_id ;
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        // dd($order);
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get();
        $view =  \View::make('administracion.inscripciones.list.pdf', compact('pestudios','order','grado_id','institucion'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // return $pdf->stream('inscripciones');
        // return view('administracion.inscripciones.list.pdf',compact('pestudios','order','grado_id '));

        $name_file = 'Listado inscripciones - Nóminas Estudiantíles';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
        
    }
    public function matricula_inicial(Request $request){
        $order = $request->order;
        $pestudio_id = $request->pestudio_id;
        $grado_id = $request->grado_id;
        $orientacion = $request->orientacion;
        $paper = $request->paper;

        $grado = Grado::find($grado_id);
        $seccions = ($grado) ? $grado->seccion : collect();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get();

        //dd($institucion,$pestudios,$order,$pestudio_id,$grado_id,$paper);

        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//

        $view =  \View::make('administracion.inscripciones.matricula.pdf.inicial', compact('pestudios','order','grado_id','grado','seccions','institucion','autoridad1','autoridad2'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // return $pdf->stream('Matrícula Inicial');
        // return $view;

        $name_file = 'Matrícula Inicial';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
    }


}
