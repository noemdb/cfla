<?php

namespace App\Http\Controllers\Admin\Fix\DB;

use App\Http\Controllers\Admin\Fix\DB\Functions\ConceptoPago\FixConceptoPago;
use App\Http\Controllers\Admin\Fix\DB\Functions\Control\FixEvaluacion;
use App\Http\Controllers\Admin\Fix\DB\Functions\Control\FixProfesor;
use App\Http\Controllers\Admin\Fix\DB\Functions\DB\FixDB;
use App\Http\Controllers\Admin\Fix\DB\Functions\Ingreso\IngresoFraccion;
use App\Http\Controllers\Admin\Fix\DB\Functions\RegistroPagoCombinado\FixRegistroPago;
// use App\Http\Controllers\Admin\Fix\DB\Api\RegistroPagoCombinado\ApiFixRegistroPago;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\Cuentaxpagar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    //Functions trait
    use FixRegistroPago;
    // use FixRegistroPago;
    use FixConceptoPago;
    use FixEvaluacion;
    use FixProfesor;
    use FixDB;
    use IngresoFraccion;

    public function fixJobsQueueMsn()
    {
        $datas = collect();
        $jobs = DB::table('jobs')->select('jobs.*')->get();
        foreach ($jobs as $job) {
            $pos = strpos($job->payload, 'del mes en curso');
            if ($pos !== false) {
                $fixText = str_replace("del mes en curso", "de cada mes", $job->payload);
                $affected = DB::table('jobs')->where('id', $job->id)->update(['payload' => $fixText]);
                $datas->push($job);
            }
        }
        return $datas;
    }

    public function fixDAA()
    {
        $estudiants = DB::table('estudiants')
        ->select('estudiants.*','administrativas.planpago_id as administrativa_planpago_id')
        ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
        ->get();

        $datas_join = collect();
        $datas_leftjoin = collect();

        foreach ($estudiants as $estudiant) {
            $cuentaxpagars = Cuentaxpagar::where('cuentaxpagars.type','INDIVIDUAL')->where('cuentaxpagars.estudiant_id',$estudiant->id)->get();
            if ($cuentaxpagars->isNotEmpty()) {
                foreach ($cuentaxpagars as $cuentaxpagar) {
                    $cuentaxpagar->update(['planpago_id'=>$estudiant->administrativa_planpago_id]);
                    $datas_join->push($estudiant->ci_estudiant);
                }
            }
        }

        $estudiants = DB::table('estudiants')
        ->select('estudiants.*','administrativas.planpago_id as administrativa_planpago_id')
        ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
        ->whereNull('administrativas.id')
        ->get();

        foreach ($estudiants as $estudiant) {
            $planpago_id = 1;
            $cuentaxpagars = Cuentaxpagar::where('cuentaxpagars.type','INDIVIDUAL')->where('cuentaxpagars.estudiant_id',$estudiant->id)->get();
            if ($cuentaxpagars->isNotEmpty()) {
                foreach ($cuentaxpagars as $cuentaxpagar) {
                    $cuentaxpagar->update(['planpago_id'=>$planpago_id]);
                    $administrativa = Administrativa::where('estudiant_id',$estudiant->id)->first();
                    if ($administrativa) {
                        $administrativa->fill(['planpago_id'=>$planpago_id]);
                        $administrativa->save();
                    }
                    else {
                        $create = Administrativa::create([
                            'estudiant_id' => $estudiant->id,
                            'planpago_id' => $planpago_id,
                            'user_id' => Auth::user()->id
                        ]);
                    }
                    $datas_leftjoin->push($estudiant->ci_estudiant);
                }
            }
        }

        dd($datas_join,$datas_leftjoin);
    }

    public function fix_town_state_estudiant()
    {
        $municipios =
        DB::table('estudiants')
            ->select('estudiants.id','estudiants.ci_estudiant','estudiants.town_hall_birth','estudiants.state_birth')
            ->selectRaw('count(town_hall_birth) as count_town_hall_birth')
            ->GroupBy('town_hall_birth')
            ->OrderBy('count_town_hall_birth','desc')
            ->WhereNotNull('town_hall_birth')
            ->Where('state_birth','YARACUY')
            ->get()
            ;

        // dd($municipios);

        $arr = ['state_birth'=>'LARA','town_hall_birth'=>'IRIBARREN'];
        Estudiant::Where('town_hall_birth', 'IRIBARREN')->update($arr);

        $arr = ['state_birth'=>'LARA','town_hall_birth'=>'BARQUISIMETO'];
        Estudiant::Where('town_hall_birth', 'BARQUISIMETO')->update($arr);

        $arr = ['state_birth'=>'MIRANDA','town_hall_birth'=>'CHACAO'];
        Estudiant::Where('town_hall_birth', 'MIRANDA')->update($arr);

        $arr = ['state_birth'=>'MIRANDA','town_hall_birth'=>'CHACAO'];
        Estudiant::Where('town_hall_birth', 'CHACAO')->update($arr);

        $arr = ['state_birth'=>'MIRANDA','town_hall_birth'=>'ZAMORA'];
        Estudiant::Where('town_hall_birth', 'ZAMORA')->update($arr);

        $arr = ['state_birth'=>'MARACAIBO','town_hall_birth'=>'GIRARDOT'];
        Estudiant::Where('town_hall_birth', 'GIRARDOT')->update($arr);

        $arr = ['state_birth'=>'ZULIA','town_hall_birth'=>'MARACAIBO'];
        Estudiant::Where('town_hall_birth', 'MARACAIBO')->update($arr);

    }

}
