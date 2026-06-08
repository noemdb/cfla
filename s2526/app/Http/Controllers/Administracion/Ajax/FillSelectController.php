<?php

namespace App\Http\Controllers\Administracion\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Estudiant;

class FillSelectController extends Controller
{
    public function gradoByseccion($id)
    {
        //dd($id);
        return Seccion::where('grado_id','=',$id)->where('status_active','true')->get();
    }

    public function gradoBypensum($id)
    {
        return Pensum::select('pensums.id as id','asignaturas.id as asignatura_id')
            ->selectRaw("CONCAT('[',asignaturas.code,'] ',asignaturas.name) as name")
            ->join('asignaturas','asignaturas.id','=','pensums.asignatura_id')
            ->where('grado_id','=',$id)
            ->orderBy('asignaturas.order')
            ->get();

    }

    public function studiantBytype($type)
    {
        return Estudiant::select('name','id', DB::raw("CONCAT(ci_estudiant,' - ',name,' ',lastname) as fullname"))->orderby('fullname')->get();

    }

    public function cuentaByconcepto($type)
    {

        $list_cuentaxpagar= DB::table('cuentaxpagars')
            ->select('cuentaxpagars.id',DB::raw('concat(planpagos.name, " || ",cuentaxpagars.name) as name'))
            ->join('planpagos', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')
            ->orderby('planpagos.name','asc')
            ->where('cuentaxpagars.type',$type)
            ->where('cuentaxpagars.status_active','true')
            ->wherenull('cuentaxpagars.deleted_at')
            ->orderBy('cuentaxpagars.created_at','desc')
            ->get()
            // ->pluck('full_name', 'id')
            ;

        return $list_cuentaxpagar;

    }
}
