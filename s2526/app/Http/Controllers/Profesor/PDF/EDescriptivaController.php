<?php

namespace App\Http\Controllers\Profesor\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

class EDescriptivaController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function edescriptiva($estudiant_id)
    {
        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'lettet'; //legal, lettet

        $date = Date::now();
        $fecha = 'a los '.$date->format('j').' días del mes de '.$date->format('F').' del año '.$date->format('Y');

        $edescriptivas = collect();

        $estudiant = Estudiant::select('estudiants.*')->WidthInscripcion()
            // ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->where('estudiants.id',$estudiant_id)
            ->where('pevaluacions.profesor_id',$this->profesor->id)
            ->wherenull('pevaluacions.deleted_at')
            ->groupBy('estudiants.id')
            ->first()
            ;

        if ($estudiant) {
            $edescriptivas = $estudiant->edescriptivas;
        }

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make('profesors.edescriptivas.pdf.edescriptiva', compact('estudiant','edescriptivas','institucion','autoridad1','autoridad2','fecha'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
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

        $estudiants = collect();

        $seccion = Seccion::select('seccions.*')
            ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('profesor_guias', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->where('seccions.id',$seccion_id)
            ->where('profesor_guias.profesor_id',$this->profesor->id)
            ->wherenull('pevaluacions.deleted_at')
            ->groupBy('seccions.id')
            ->orderBy('profesor_guias.created_at','desc')
            ->first()
            ;
        if ($seccion) {

            $estudiants = $seccion->estudiants_in;
        }

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make('profesors.edescriptivas.pdf.lotes.edescriptiva', compact('estudiants','lapso_id','institucion','autoridad1','autoridad2','fecha'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // return $pdf->stream('edescriptivas');
        return $view;
    }
}
