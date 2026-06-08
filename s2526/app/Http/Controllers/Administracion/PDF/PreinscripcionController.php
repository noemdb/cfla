<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Tinscripcion;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\Session;

class PreinscripcionController extends Controller
{
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

        $view =  \View::make('administracion.preinscripcions.book.pdf', compact('std_ciaca_siadm','std_siaca_ciadm','pescolar_id','autoridad1','autoridad2','institucion','pestudios','grados','seccions','tinscripcions'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('Libro de Preinscripciones');
        // return view('administracion.preinscripcions.list.pdf',compact('pestudios','order','grado_id '));
    }
}
