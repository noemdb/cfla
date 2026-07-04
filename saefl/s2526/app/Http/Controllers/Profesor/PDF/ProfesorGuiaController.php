<?php

namespace App\Http\Controllers\Profesor\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class ProfesorGuiaController extends Controller
{

    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id', Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function sabanafull($grado_id, $seccion_id)
    {

        $seccion_guias = $this->profesor->seccion_guias;

        $seccions = $seccion_guias->where('id', $seccion_id)->first();

        $seccion_id = ($seccions) ? $seccions->id : null;

        $orientacion = 'landscape'; //landscape portrait
        $paper  = 'letter'; // legal, letter
        $fecha = Carbon::now()->format('d-m-Y');

        $lapsos = Lapso::all();

        $grado = Grado::findOrFail($grado_id);
        $grado_id = $grado->id;
        $seccion = Seccion::findOrFail($seccion_id);
        $seccion_id = $seccion->id;

        $estudiants = $seccion->estudiants_in;

        $pestudio = $grado->pestudio;
        $baremos = $pestudio->baremos;
        //$baremos = $pestudio->getBaremos($lapso->id ?? null);

        $pensums = Pensum::where('grado_id', $grado_id)->get();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make(
            'profesors.profesor_guias.pdf.sabanafull',
            compact('estudiants', 'grado_id', 'grado', 'seccion_id', 'seccion', 'lapsos', 'pensums', 'pestudio', 'baremos', 'institucion', 'autoridad1', 'autoridad2', 'fecha')
        )
            ->render();

        // $view =  \View::make('profesors.profesor_guias.pdf.sabanafull',
        // compact('estudiants','grado_id','grado','seccion_id','seccion','lapsos','pensums','pestudio','baremos','institucion','autoridad1','autoridad2','fecha'))
        // ->render();

        // $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]);

        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72, 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->loadHTML($view)->setPaper($paper, $orientacion);

        $name_file = 'Planilla Registro de Notas';
        if (env('APP_ENV') == "local") return $view;
        else return $pdf->stream($name_file);
    }
}
