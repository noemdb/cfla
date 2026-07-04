<?php

namespace App\Http\Controllers\Profesor\PDF;

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
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class BoletinController extends Controller
{

    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id', Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function boletin($id)
    {
        $orientacion = 'landscape';
        $paper  = 'letter';

        $estudiant = Estudiant::findOrFail($id);
        $lapsos = Lapso::all();
        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make('profesors.boletins.boletin.pdf', compact('estudiant', 'lapsos', 'institucion', 'autoridad1', 'autoridad2'))->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72, 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        $name_file = 'Boletin';
        if (env('APP_ENV') == "local") return $view;
        else return $pdf->stream($name_file);
    }

    public function sabana($pevaluacion_id)
    {
        $orientacion = 'landscape';
        $paper  = 'lettet';
        $baremo = new Baremo();
        $profesor = Profesor::where('user_id', Auth::user()->id)->first();
        $pevaluacion = Pevaluacion::findOrFail($pevaluacion_id);

        $lapsos = Lapso::all();

        $pensum = $pevaluacion->pensum;
        $grado = $pensum->grado;
        $seccion = $pevaluacion->seccion;
        $lapso = $pevaluacion->lapso;

        $pestudio = $grado->pestudio;
        //$baremos = $pestudio->baremos;
        $baremos = $pestudio->getBaremos($lapso->id ?? null);

        $evaluacions = Evaluacion::orderby('id')
            ->where('pevaluacion_id', $pevaluacion_id)
            ->get();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make(
            'profesors.boletins.pdf.sabana',
            compact(
                'profesor',
                'pevaluacion',
                'lapsos',
                'pensum',
                'grado',
                'seccion',
                'lapso',
                'pestudio',
                'pestudio',
                'baremo',
                'baremos',
                'evaluacions',
                'institucion',
                'autoridad1',
                'autoridad2'
            )
        )
            ->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72, 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        $name_file = 'Acta Discusión de Notas - ' . $grado->name . ' - ' . $seccion->name;
        if (env('APP_ENV') == "local") return $view;
        else return $pdf->stream($name_file);
    }

    public function sabana_single($pensum_id, $seccion_id)
    {

        $profesor = $this->profesor;
        $baremo = new Baremo();
        $pensum = $profesor->pensums->where('id', $pensum_id)->first();
        $pensum_id = ($pensum) ? $pensum->id : null;

        $seccions = $profesor->seccions->where('id', $seccion_id)->first();
        $seccion_id = ($seccions) ? $seccions->id : null;

        $orientacion = 'landscape'; // landscape , portrait
        $paper  = 'legal'; // legal, letter
        $fecha = Carbon::now()->format('d-m-Y');

        $lapsos = Lapso::all();

        $pensum = Pensum::findOrFail($pensum_id);
        $pensum_id = $pensum->id;
        $grado = $pensum->grado;
        $grado_id = $grado->id;
        $seccion = Seccion::findOrFail($seccion_id);
        $seccion_id = $seccion->id;

        $estudiants = $seccion->estudiants_in;

        $pestudio = $grado->pestudio;
        $baremos = $pestudio->baremos;
        //$baremos = $pestudio->getBaremos($lapso->id ?? null);

        $pensums = Pensum::where('id', $pensum_id)->get();

        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  View::make(
            'profesors.boletins.pdf.sabana_single',
            compact('profesor', 'estudiants', 'baremo', 'grado_id', 'grado', 'seccion_id', 'seccion', 'lapsos', 'pensums', 'pestudio', 'baremos', 'institucion', 'autoridad1', 'autoridad2', 'fecha')
        )
            ->render();
        $pdf = App::make('dompdf.wrapper')->setOptions(['dpi' => 72, 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $pdf->loadHTML($view)->setPaper($paper, $orientacion);

        $name_file = 'Resumen final del rendimiento estudiantíl - ' . $grado->name . ' - ' . $seccion->name;
        if (env('APP_ENV') == "local") return $view;
        else return $pdf->stream($name_file);
    }
}
