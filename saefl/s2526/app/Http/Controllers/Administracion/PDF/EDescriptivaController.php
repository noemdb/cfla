<?php

namespace App\Http\Controllers\Administracion\PDF;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion\Edescriptiva;
use Jenssegers\Date\Date;

class EDescriptivaController extends Controller
{
    public function edescriptiva($estudiant_id)
    {
        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet

        // $fecha = Date::now()->format(' j F Y');
        $date = Date::now();
        $fecha = 'a los '.$date->format('j').' días del mes de '.$date->format('F').' del año '.$date->format('Y');

        $estudiant = Estudiant::findOrFail($estudiant_id);
        $pestudio = $estudiant->pestudio;
        $fecha_remision = ($pestudio) ? $pestudio->fecha_descriptivo : now()->format('Y-m-d') ;
        $fecha_remision = Date::createFromDate($fecha_remision);

        $edescriptivas = $estudiant->edescriptivas;

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  \View::make('administracion.edescriptivas.pdf.edescriptiva', compact('estudiant','edescriptivas','institucion','autoridad1','autoridad2','fecha','fecha_remision'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // return $pdf->stream('Boletin');
        return $view;
    }

    public function lote_edescriptiva($grado_id,$seccion_id,$lapso_id)
    {
        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet

        $date = Date::now();
        $fecha = 'a los '.$date->format('j').' días del mes de '.$date->format('F').' del año '.$date->format('Y');

        // $pescolar_ffinal = Session::get('pescolar_ffinal');
        // $fecha_remision = Date::createFromDate($pescolar_ffinal);
        // $fecha_remision = 'a los '.$fecha_remision->format('j').' días del mes de '.$fecha_remision->format('F').' del año '.$fecha_remision->format('Y');

        $seccion = Seccion::FindOrFail($seccion_id);
        $pestudio = $seccion->pestudio;
        $fecha_remision = ($pestudio) ? $pestudio->fecha_descriptivo : now()->format('Y-m-d') ;
        $fecha_remision = Date::createFromDate($fecha_remision);

        $estudiants = $seccion->estudiants_in;

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  \View::make('administracion.edescriptivas.pdf.lotes.edescriptiva', compact('estudiants','lapso_id','institucion','autoridad1','autoridad2','fecha','fecha_remision'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // return $pdf->stream('edescriptivas');
        return $view;
    }
}
