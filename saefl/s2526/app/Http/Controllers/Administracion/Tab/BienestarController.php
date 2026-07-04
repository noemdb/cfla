<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Bienestar\StudentRecord;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Planpago;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Foreach_;

class BienestarController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_control']);
    }
    public function index()
    {
        return view('administracion.bienestars.index');
    }

    public function batch(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id:null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id:null;
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id:null;
        $status_active     = (!empty($request->status_active)) ? $request->status_active: null;
        $formally = (!empty($request->formally)) ? $request->formally:null;

        $estudiants = collect(New Estudiant);

        if (count($request->all())>0) {

            $estudiants =
                Estudiant::select('estudiants.*')
                    ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                    ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                    ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                    ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                    ->whereNull('inscripcions.deleted_at')
                    ->whereNull('seccions.deleted_at')
                    ->whereNull('grados.deleted_at')
                    ->groupby('estudiants.id');

            $estudiants = (isset($grado_id)) ? $estudiants->where('grados.id',$grado_id) : $estudiants;
            $estudiants = (isset($seccion_id) && isset($seccion_id)) ? $estudiants->where('seccions.id',$seccion_id) : $estudiants;
            $estudiants = (isset($planpago_id) && isset($planpago_id)) ? $estudiants->where('administrativas.planpago_id',$planpago_id) : $estudiants;

            $estudiants = ($status_active) ? $estudiants->where('estudiants.status_active',$status_active) : $estudiants ;

            $estudiants = ($formally=='SI') ? $estudiants->whereNotNull('administrativas.id')->whereNotNull('inscripcions.id') : $estudiants ;
            $estudiants = ($formally=='NO') ? $estudiants->where( function($query) { $query->where('inscripcions.id', null)->orWhere('administrativas.id',null);}) : $estudiants ;

            $estudiants = $estudiants->get();

            foreach ($estudiants as $estudiant) {
                $student_record = StudentRecord::where('estudiant_id',$estudiant->id)->first();
                if (empty($student_record)) {
                    $student_record = New StudentRecord;
                    $student_record->estudiant_id = $estudiant->id;
                    $student_record->save();
                }
            }
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();
        $planpago_list = Planpago::select('name', 'id')->where('status_active','true')->orderby('id','asc')->pluck('name', 'id');
        $active_list = ['true'=>'Activo','false'=>'Desactivo'];
        $formaly_list = ['SI'=>'SI','NO'=>'NO'];

        $compact = [
            'estudiants',
            'grado_id','seccion_id','planpago_id','status_active','formally',
            'list_grado','list_seccion','planpago_list','active_list','formaly_list'
        ];

        return view('administracion.bienestars.batch',compact($compact));
    }
}
