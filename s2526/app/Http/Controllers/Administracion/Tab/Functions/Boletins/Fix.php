<?php
namespace App\Http\Controllers\Administracion\Tab\Functions\Boletins;

use Illuminate\Http\Request;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\BoletinAjuste;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait Fix {

    public function fix_boletins_sin_evaluacion(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;
        $pensums_id = (!empty($request->pensums_id)) ? $request->pensums_id: null;
        $profesor_id = (!empty($request->profesor_id)) ? $request->profesor_id: null;
        $boletins = collect();

        if (count($request->all())>0) {

            $boletins = Boletin::select('boletins.*')
                ->join('evaluacions','evaluacions.id','=','boletins.evaluacion_id')
                ->join('pevaluacions','pevaluacions.id','=','evaluacions.pevaluacion_id')
                ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
                ->join('grados', 'grados.id', '=', 'pensums.grado_id')
                ->join('seccions', 'grados.id', '=', 'seccions.grado_id')
                ->whereNotNull('evaluacions.deleted_at')
                ->orderby('evaluacions.created_at')
                ->groupby('boletins.id');

                $boletins = ($grado_id) ? $boletins->where('pensums.grado_id',$grado_id) : $boletins ;
                $boletins = ($seccion_id) ? $boletins->where('pevaluacions.seccion_id',$seccion_id) : $boletins ;
                $boletins = ($lapso_id) ? $boletins->where('pevaluacions.lapso_id',$lapso_id) : $boletins ;
                $boletins = ($pensums_id) ? $boletins->where('pensums.id',$pensums_id) : $boletins ;
                $boletins = ($profesor_id) ? $boletins->where('pevaluacions.profesor_id',$profesor_id) : $boletins ;

                $boletins = $boletins->get();

        }

        $result = collect();
        foreach ($boletins as $boletin) {
            $result = $boletin;
            $boletin->delete();
        }

        $messenge = trans('db_oper_result.oper_ok');
        $operation= 'create';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
                "data"=>$result,
            ]);
        }

        return $result;
    }
}
