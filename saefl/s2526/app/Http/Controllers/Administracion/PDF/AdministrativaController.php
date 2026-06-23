<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Session;

use App\Models\app\Pescolar;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Tinscripcion;

class AdministrativaController extends Controller
{
    public function book(Request $request){
        $orientacion = 'portrait';
        $paper  = 'lettet';
        // $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        // $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO
        $institucion = Institucion::OrderBy('created_at','DESC')->first();

        $pescolar_id = ($request->get('pescolar_id')) ? $request->get('pescolar_id'): Session::get('pescolar_id');
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get(); 
        $grados = Grado::Orderby('id','asc')->where('status_active','true')->get(); 
        $seccions = Seccion::Orderby('id','asc')->get(); 
        $tinscripcions = Tinscripcion::Orderby('id','asc')->get();
        $std_siaca_ciadm = Administrativa::std_siaca_ciadm();
        $std_ciaca_siadm = Administrativa::std_ciaca_siadm();       
        
        $view =  \View::make('administracion.administrativas.book.pdf', compact('std_siaca_ciadm','std_ciaca_siadm','pescolar_id','institucion','pestudios','grados','seccions','tinscripcions'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('book');
        $name_file = 'book';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
    }

    public function solvencia_pdf($id){
        $estudiant = Estudiant::findOrFail($id);
        $administrativa = $estudiant->administrativa;
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('2');//ADMINISTRADOR
        $orientacion = 'portrait';
        $paper  = 'lettet';

        $view =  \View::make('administracion.administrativas.solvencia.pdf', compact('administrativa','estudiant','institucion','autoridad1','autoridad2'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]);
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('Solvencia Administrativa');
        // return view('administracion.administrativas.solvencia.pdf', compact('administrativa','estudiant','institucion','autoridad1','autoridad2'));
    }
    
    public function constancia_pdf($id){
        $estudiant = Estudiant::findOrFail($id);
        $administrativa = $estudiant->administrativa;
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('2');//ADMINISTRADOR
        $orientacion = 'portrait';
        $paper  = 'lettet';

        $view =  \View::make('administracion.administrativas.constancia.pdf', compact('administrativa','estudiant','institucion','autoridad1','autoridad2'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]);
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('Constacia de Inscripción Administrativa');
        // return view('administracion.administrativas.solvencia.pdf', compact('administrativa','estudiant','institucion','autoridad1','autoridad2'));
    }
    
    public function listpdf(Request $request){
        $order = $request->order;
        $orientacion = $request->orientacion;
        $paper = $request->paper;
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        // dd($order);
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get(); 
        $view =  \View::make('administracion.administrativas.list.pdf', compact('pestudios','order','institucion'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('inscripciones');
        // return view('administracion.administrativas.list.pdf',compact('pestudios','order'));
    }
}
