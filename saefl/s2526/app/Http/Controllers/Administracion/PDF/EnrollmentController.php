<?php

namespace App\Http\Controllers\Administracion\PDF;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function payroll($id){
        $enrollment = Enrollment::findOrFail($id);
        $estudiant = Estudiant::where('ci_estudiant',$enrollment->ci_estudiant)->first();
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO

        $view =  \View::make('administracion.enrollments.pdf.payroll', compact('estudiant','enrollment','institucion','autoridad1','autoridad2'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('letter','portrait'); //landscape, // legal, letter
        // return $pdf->stream('Renovación de Matrícula');
        // return $view;
        $name_file = 'Solicitud de Matrícula';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);

        // return $view;
    }

    public function formatos($grado_id,$seccion_id){

        $grado = Grado::findOrFail($grado_id);
        $grado_id = $grado->id;
        $seccion = Seccion::findOrFail($seccion_id);
        $seccion_id = $seccion->id;

        $estudiants = $seccion->estudiants_in;

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO

        $view =  \View::make('administracion.enrollments.pdf.formatos', compact('estudiants','grado','grado','institucion','autoridad1','autoridad2'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('legal','portrait'); //landscape,lettet  // legal, letter
        //return $pdf->stream('Renovación de Matrícula');
        // return $view;

        $name_file = 'Solicitud de Matrícula';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
    }

    public function simple()
    {
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO

        $grado_next = new Grado;
        $grado_next->id = 100;
        $estudiant = new Estudiant;
        $enrollment = new Enrollment;

        $view =  \View::make('administracion.enrollments.pdf.simple', compact('institucion','autoridad1','autoridad2','grado_next','estudiant','enrollment'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('legal','portrait'); //landscape, // legal, letter
        //return $pdf->stream('Solicitud de Matrícula');
        // return $view;
        $name_file = 'Solicitud de Matrícula';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
    }

    public function individual($id)
    {
        $estudiant = Estudiant::findOrFail($id);
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO

        $view =  \View::make('administracion.enrollments.pdf.individual', compact('institucion','autoridad1','autoridad2','estudiant','enrollment'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('legal','portrait'); //landscape, // legal, letter
        //return $pdf->stream('Solicitud de Matrícula');
        $name_file = 'Solicitud de Matrícula';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
        // return $view;
    }
}
