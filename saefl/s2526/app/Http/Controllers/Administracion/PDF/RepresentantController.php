<?php

namespace App\Http\Controllers\Administracion\PDF;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiant;
use App\Models\app\Institucion;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Planpago\RegistroPagoCombinado;
use Barryvdh\DomPDF\PDF;

class RepresentantController extends Controller
{
    public function reciboMail($id){
        $orientacion = 'landscape'; //landscape,portrait
        $paper  = 'lettet';
        $registro_pago_combinado = RegistroPagoCombinado::findOrFail($id);

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR

        $compact = ['registro_pago_combinado','institucion','autoridad1','autoridad2'];

        // $pdf = PDF::loadView('administracion.representants.recibo.mailNotify', $compact);

        // $view =  \View::make('administracion.representants.recibo.pdf', compact($compact))->render();
        // $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        // $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        // $name_file = 'Recibo de Pago Representante';
        // return $pdf->stream($name_file);

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('administracion.representants.recibo.mailNotify', compact($compact));
        return $pdf;

        // return PDF::loadView('invoices.pdf5', compact($compact))->stream();
    }

    public function recibo($id){
        $orientacion = 'landscape'; //landscape,portrait
        $paper  = 'lettet';
        $registro_pago_combinado = RegistroPagoCombinado::findOrFail($id);

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getTipoAuthority('2');//director
        $autoridad2 = Autoridad::getTipoAuthority('4');//ADMINISTRADOR

        $compact = ['registro_pago_combinado','institucion','autoridad1','autoridad2'];

        $view =  \View::make('administracion.representants.recibo.pdf', compact($compact))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape

        $name_file = 'Recibo de Pago Representante';

        if (env('APP_ENV')=="local") return $view; else return $pdf->stream($name_file);
    }

    public function libro(Request $request){

        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null ;
        $formally = (!empty($request->formally)) ? $request->formally : null ;

        $grado = Grado::where('id',$grado_id)->first();
        $seccion = Seccion::where('id',$seccion_id)->first();

        $orientacion = 'portrait'; //landscape
        $paper  = 'lettet';

        $representants =
            Representant::select('representants.*')
                // ->selectRaw('count(estudiants.id) as count_estudiant')
                ->join('estudiants', 'representants.id', '=', 'estudiants.representant_id')
                ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                ->where('representants.status_active','true')
                ->where('estudiants.status_active','true')
                // ->whereRAW('count(estudiants.id) > 0')
                ->groupby('representants.id')
                ;

        $representants = (isset($grado_id)) ? $representants->where('grados.id',$grado_id) : $representants;
        $representants = (isset($grado_id) && isset($seccion_id)) ? $representants->where('seccions.id',$seccion_id) : $representants;
        // $representants = (isset($planpago_id) && isset($planpago_id)) ? $representants->where('administrativas.planpago_id',$planpago_id) : $representants;

        $representants = ($formally=='SI') ? $representants->whereNotNull('administrativas.id')->whereNotNull('inscripcions.id')->where('seccions.status_active','true') : $representants ;
        $representants = ($formally=='NO') ? $representants->where( function($query) { $query->where('inscripcions.id', null)->orWhere('administrativas.id',null);}) : $representants ;

        $representants = $representants->get();

        $institucion = Institucion::OrderBy('created_at','DESC')->first();

        // $view =  \View::make('administracion.representants.libro.pdf', compact('representants','institucion','estudiants'))->render();
        $view =  \View::make('administracion.representants.libro.pdf', compact('representants','institucion','grado','seccion'))->render();
        $pdf = \App::make('dompdf.wrapper')->setOptions(['dpi' => 72,'isHtml5ParserEnabled'=>true,'isRemoteEnabled'=>true]); //150,95
        $pdf->loadHTML($view)->setPaper($paper, $orientacion); //landscape
        return $pdf->stream('libros_representantes');
        // return $view;
        // return view('administracion.administrativas.list.pdf',compact('pestudios','order'));
    }

}
