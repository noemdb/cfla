<?php

namespace App\Http\Controllers\Bienestar\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Planpago;
use Illuminate\Http\Request;

class EstudiantController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_bienestar']);
    }
    public function index()
    {
        return view('bienestars.estudiants.index');
    }

    public function crud(Request $request)
    {

        $grado_id = (!empty($request->grado_id)) ? $request->grado_id:null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id:null;
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id:null;
        $status_active     = (!empty($request->status_active)) ? $request->status_active: null;
        $formally = (!empty($request->formally)) ? $request->formally:null;
        $status_inscription_affects = (!empty($request->status_inscription_affects)) ? $request->status_inscription_affects:null;
        $seccion_status_active = (!empty($request->seccion_status_active)) ? $request->seccion_status_active:null; //dd($status_active);

        $estudiants = collect([]);  //dd($request->all());

        if (count($request->all())>0) {

            $estudiants =
                Estudiant::select('estudiants.*')
                    ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                    ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                    ->leftjoin('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                    ->groupby('estudiants.id');

            $estudiants = (isset($grado_id)) ? $estudiants->where('grados.id',$grado_id)->where('seccions.status_active','true') : $estudiants;
            $estudiants = (isset($seccion_id) && isset($seccion_id)) ? $estudiants->where('seccions.id',$seccion_id)->where('seccions.status_active','true') : $estudiants;
            $estudiants = (isset($planpago_id) && isset($planpago_id)) ? $estudiants->where('administrativas.planpago_id',$planpago_id) : $estudiants;
            $estudiants = ($status_active) ? $estudiants->where('estudiants.status_active',$status_active) : $estudiants ;

            $estudiants = ($formally=='SI') ? $estudiants->whereNotNull('inscripcions.id')->where('seccions.status_active','true')->where('planpagos.status_inscription_affects','true') : $estudiants ;
            $estudiants = ($formally=='NO') ? $estudiants->whereNull('inscripcions.id') : $estudiants ;

            $estudiants = ($status_inscription_affects) ? $estudiants->where('planpagos.status_inscription_affects',$status_inscription_affects) : $estudiants ;
            $estudiants = ($seccion_status_active) ? $estudiants->where('seccions.status_active',$seccion_status_active) : $estudiants ;

            $estudiants = $estudiants->get();

        
        }

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();
        $planpago_list = Planpago::select('name', 'id')->where('status_active','true')->orderby('id','asc')->pluck('name', 'id');

        return view('bienestars.estudiants.crud',compact('status_inscription_affects','seccion_status_active','estudiants','list_grado','list_seccion','planpago_list','grado_id','seccion_id','planpago_id','status_active','formally'));
    }
}
