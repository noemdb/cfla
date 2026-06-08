<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Seccion;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;

class BoletinRevisionController extends Controller
{
    public function resumen_revision(Request $request)
    {
        $seccion_id = $request?->seccion_id;
        $type = $request?->type;

        $orientacion = 'portrait'; //'portrait', 'landscape'
        $paper  = 'legal'; //legal, lettet
        $baremo = new Baremo();
        $fecha = Date::now()->format('d-m-Y');
        $ano = Date::now()->format('Y');

        $seccion = Seccion::findOrFail($seccion_id); $seccion_id = $seccion->id;
        $pestudio = $seccion->pestudio;
        $fecha_remision = ($pestudio) ? $pestudio->remision_resumen_final : $fecha ;
        $fecha_remision = ($fecha_remision) ? $fecha_remision : $fecha ;
        // $fecha_remision = Session::get('pescolar_ffinal');
        $fecha_remision = Date::createFromDate($fecha_remision);

        $estudiants = Estudiant::select('estudiants.*','boletin_revisions.pensum_id as pensum_id')
                ->join('boletin_revisions', 'estudiants.id', '=', 'boletin_revisions.estudiant_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                ->OrderBy('estudiants.ci_estudiant', 'asc')
                ->where('seccions.id',$seccion_id)
                ->whereNull('boletin_revisions.deleted_at')
                ->whereNull('inscripcions.deleted_at')
                ->whereNull('seccions.deleted_at')
                ->whereNull('grados.deleted_at')
                ->orderBy('boletin_revisions.created_at','desc')
                ->groupBy('estudiants.id')
                ->get();

        $grado = $seccion->grado;
        $pensums = $grado->pensums->sortBy(function ($value, $key) {return (!empty($value->asignatura->order)) ? $value->asignatura->order:null;});
        $pestudio = $grado->pestudio;

        $estudiants_clone = clone $estudiants;
        $estudiant_ids = clone $estudiants_clone->pluck('id');
        $pensums_revision = Pensum::select('pensums.*','seccions.id as seccions_id')
        ->join('boletin_revisions', 'pensums.id', '=', 'boletin_revisions.pensum_id')
        ->join('grados', 'grados.id', '=', 'pensums.grado_id')
        ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
        ->where('seccions.id',$seccion_id)
        ->whereIn('boletin_revisions.estudiant_id',$estudiant_ids)
        ->groupBy('pensums.id')
        ->get(); //dd($pensums_revision);

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('JEFE DE CONTROL DE ESTUDIO');

        $view =  \View::make('administracion.boletin_revisions.pdf.resumen_revision.'.$pestudio->code,
        compact('estudiants','pestudio','grado','pensums','pensums_revision','seccion','type','baremo','fecha','fecha_remision','ano','institucion','autoridad1','autoridad2'))
        ->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // return $pdf->stream('Resumen final del rendimiento estudiantíl - '.$grado->name.' - '.$seccion->name);
        return $view;
    }
}
