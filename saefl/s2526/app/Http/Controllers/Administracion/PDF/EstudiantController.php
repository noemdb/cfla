<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;

class EstudiantController extends Controller
{
    public function listado(Request $request){

        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null ;

        $grado = Grado::where('id',$grado_id)->first();
        $seccion = Seccion::where('id',$seccion_id)->first();

        $estudiants =
            Estudiant::select('estudiants.*')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->whereNull('inscripcions.deleted_at')
                ->whereNull('seccions.deleted_at')
                ->whereNull('grados.deleted_at')
                ->where('estudiants.status_active','true')
                ->groupby('estudiants.id');
                // ->get();

        $estudiants = (isset($grado_id)) ? $estudiants->where('grados.id',$grado_id) : $estudiants;
        $estudiants = (isset($grado_id) && isset($seccion_id)) ? $estudiants->where('seccions.id',$seccion_id) : $estudiants;

        $estudiants = $estudiants->get();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();

        // dd($estudiants);

        $orientacion = 'portrait'; //landscape
        $paper  = 'lettet';

        $view =  \View::make('administracion.estudiants.libro.pdf', compact('estudiants','institucion','grado','seccion'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('libros_estudiants');
        // return view('administracion.estudiants.libro.pdf',compact('estudiants','institucion','grado','seccion'));
    }

    public function listado_bck(Request $request){

        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null ;

        $grado = Grado::where('id',$grado_id)->first();
        $seccion = Seccion::where('id',$seccion_id)->first();

        $orientacion = 'portrait'; //landscape
        $paper  = 'lettet';

        $representants =
            Representant::select('representants.*')
                ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->where('representants.status_active','true')
                ->whereNull('estudiants.deleted_at')
                ->whereNull('inscripcions.deleted_at')
                ->where('estudiants.status_active','true')
                ->groupby('representants.id')
                ->Orderby('representants.name');

        $representants = ($grado_id) ? $representants->where('grados.id',$grado_id) : $representants ;
        $representants = (isset($grado_id) && isset($seccion_id)) ? $representants->where('seccions.id',$seccion_id) : $representants;

        $representants = $representants->get();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();

        // $view =  \View::make('administracion.representants.libro.pdf', compact('representants','institucion','estudiants'))->render();
        $view =  \View::make('administracion.representants.libro.pdf', compact('representants','institucion','grado','seccion'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('libros_representantes');
        // return view('administracion.administrativas.list.pdf',compact('pestudios','order'));
    }

    public function carta_bconducta($estudiant_id)
    {
        $estudiant = Estudiant::findOrFail($estudiant_id);
        $pestudio = $estudiant->pestudio;

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('1');//REPRESENTATE LEGAL
        $autoridad2 = Autoridad::getTipoAuthority('3');//JEFE DE CONTROL DE ESTUDIO

        $view =  \View::make('administracion.estudiants.pdf.carta_bconducta.'.$pestudio->code,
        compact('estudiant','pestudio','institucion','autoridad1','autoridad2'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper('lettet','portrait'); //landscape,
        return $pdf->stream('Constancia de Buena Conducta');
    }
}
