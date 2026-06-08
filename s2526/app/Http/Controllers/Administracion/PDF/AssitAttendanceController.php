<?php

namespace App\Http\Controllers\Administracion\PDF;

use App\Http\Controllers\Controller;
use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\sys\Cargo;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssitAttendanceController extends Controller
{
    public function formato(Request $request){

        $finicial = (!empty($request->finicial)) ? Carbon::parse($request->finicial):null;
        $ffinal = (!empty($request->ffinal)) ? Carbon::parse($request->ffinal):null;
        $area = (!empty($request->area)) ? $request->area:null;
        $cargo_id = (!empty($request->cargo_id)) ? $request->cargo_id:null;
        $assit_schedule_id = (!empty($request->assit_schedule_id)) ? $request->assit_schedule_id:null;

        $dates = ($finicial && $ffinal) ? date_range($finicial,$ffinal) : collect(); //dd($dates);
        $user = new User();
        $cargo = Cargo::find($cargo_id);
        $assit_schedule = AssitSchedule::find($assit_schedule_id);

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('ADMINISTRADOR');
        $autoridad2 = Autoridad::getAuthority('DIRECTOR DE ADMINISTRACIÓN');

        $compact = ['cargo','cargo_id','assit_schedule','finicial','ffinal','assit_schedule_id','dates','user','institucion','autoridad1','autoridad2'];
        $view =  \View::make('administracion.asisst_controls.assit_attendances.pdf.formato',compact($compact))->render();

        $orientacion = 'landscape'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet
        $dpi  = 72; //legal, lettet
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => $dpi,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // $name_file = 'formato_de_asistencia_'.$finicial->format('Y-m-d').'_'.$ffinal->format('Y-m-d');

        $finicial_name = ($cargo) ? $cargo->name : null ;
        $cargo_name = ($cargo) ? $cargo->name : null ;
        $name_file = 'formato_de_asistencia_'.$finicial->format('d-m-Y').'_'.$ffinal->format('d-m-Y').'_'.$cargo_name.'.pdf';

        // return $pdf->stream($name_file);
        return $view;
    }
}
