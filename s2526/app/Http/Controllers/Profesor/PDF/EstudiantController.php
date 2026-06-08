<?php

namespace App\Http\Controllers\Profesor\PDF;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Profesor;

class EstudiantController extends Controller
{
    //student_records

    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function enrollments($id){
        $estudiant = Estudiant::findOrFail($id);
        $enrollment = Enrollment::where('ci_estudiant',$estudiant->ci_estudiant)->first();
        $profesor = $this->profesor;

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO

        // profesors.estudiants.pdf.enrollment

        $view =  View::make('profesors.estudiants.pdf.enrollment', compact('estudiant','enrollment','institucion','autoridad1','autoridad2','profesor'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true,'isHtml5ParserEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('letter','portrait'); //landscape, // legal, letter
        // return $pdf->stream('Renovación de Matrícula');
        // return $view;
        $name_file = 'Planilla del Estudiante';
        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
    }

}
