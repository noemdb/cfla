<?php
namespace App\Http\Controllers\Administracion\Tab\Functions\Boletins;

use App\Imports\BoletinsCollectionImport;
use Illuminate\Http\Request;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\BoletinAjuste;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

trait Carga {

    public function cargaXls(Request $request)
    {
        $file = $request->file('file_xls');

        $grado_id = (!empty($request->grado_id)) ? $request->grado_id:null; $grado = Grado::find($grado_id);
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null; $seccion = Seccion::find($seccion_id);
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id:null;
        $pensum_id = (!empty($request->pensum_id)) ? $request->pensum_id:null; $pensum = Pensum::find($pensum_id);
        $evaluacion_id = (!empty($request->evaluacion_id)) ? $request->evaluacion_id:null;
        $notas_new = (!empty($request->notas_new)) ? $request->notas_new:null;

        $file_path = (!empty($request->file_path)) ? $request->file_path:null;
        $minimo = null;
        $maximo = null;

        $notasImport = new BoletinsCollectionImport;

        $notasXlsCol = ($file_path) ? $notasImport->toCollectionFix($file_path):null;

        $estudiants = ($seccion) ? $seccion->estudiants_in : collect();

        $pevaluacion = Pevaluacion::where('lapso_id',$lapso_id)->where('seccion_id',$seccion_id)->where('pensum_id',$pensum_id)->first();

        $pevaluacion_id = ($pevaluacion) ? $pevaluacion->id:'_id_';

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('code_sm', 'id')->orderby('name','asc')->pluck('code_sm', 'id');
        $list_pensum = Pensum::list_pestudio_pensum($grado_id);

        $list_nota = collect();
        if ($pensum) {
            $pevaluacion = $pensum->pevaluacions->first();
            $minimo = $pevaluacion->escala->minimo;
            $maximo = $pevaluacion->escala->maximo;
            $list_nota[0] = 'I';
            for ($i=$minimo; $i <= $maximo ; $i++) { $list_nota[$i] = $i;}
        }

        $evaluacions = Evaluacion::select('evaluacions.*',DB::raw('concat(evaluacions.fecha, " ",evaluacions.description ) as description_full'))
            ->join('pevaluacions','pevaluacions.id','=','evaluacions.pevaluacion_id')
            ->where('pevaluacions.lapso_id',$lapso_id)
            ->where('pevaluacions.seccion_id',$seccion_id)
            ->where('pevaluacions.pensum_id',$pensum_id)
            ->wherenull('pevaluacions.deleted_at')
            ->orderBy('evaluacions.created_at')
            // ->pluck('evaluacions.description', 'evaluacions.id')
            ->get()
            ;
        $list_evaluacions = $evaluacions->pluck('description_full', 'id');


        return view('administracion.boletins.carga_xls',
        compact('estudiants','notasXlsCol','pevaluacion','pevaluacion_id','grado_id','seccion_id','lapso_id','pensum_id','evaluacion_id','list_grado','list_seccion',
        'list_lapso','list_pensum','list_nota','minimo','maximo','evaluacions','list_evaluacions','notas_new'));
    }

    public function store_xls(Request $request)
    {
        $nota_arr = (is_array($request->nota)) ? $request->nota: array();
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id:null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id:null;
        $pensum_id = (!empty($request->pensum_id)) ? $request->pensum_id:null;
        $evaluacion_id = (!empty($request->evaluacion_id)) ? $request->evaluacion_id:null;
        $notas_new = collect();

        if ( !empty(count($nota_arr)) && $grado_id && $seccion_id && $lapso_id && $pensum_id && $evaluacion_id) {

            foreach ($nota_arr as $estudiant_id => $nota) {

                $estudiant  = Estudiant::find($estudiant_id);
                $evaluacion = Evaluacion::find($evaluacion_id);

                if ( $estudiant && $evaluacion && !empty($nota) ) {

                    $arr = [
                        'estudiant_id'=>$estudiant->id,
                        'evaluacion_id'=>$evaluacion->id,
                        'nota'=>$nota
                    ];
                    $notas_new->put($estudiant->id,$arr);

                    $boletin = Boletin::where('estudiant_id',$estudiant_id)->where('evaluacion_id',$evaluacion->id)->first();

                    if ($boletin) {
                        $boletin->fill($arr);
                        $boletin->save();
                    } else {
                        $create = Boletin::create($arr);
                    }

                }

            }

        }

        $messenge = 'Buen trabajo! La carga Fue realizada éxitosamente';
        Session::flash('operp_ok',$messenge);

        return redirect()->route('administracion.boletins.carga.xls');
    }

    public function cargaXlsPost(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id:null; $grado = Grado::find($grado_id);
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null; $seccion = Seccion::find($seccion_id);
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id:null;
        $pensum_id = (!empty($request->pensum_id)) ? $request->pensum_id:null; $pensum = Pensum::find($pensum_id);
        $file_path = $request->file('file_xls')->store('tmp');

        $inputs = [
            'grado_id'=>$grado_id,
            'seccion_id'=>$seccion_id,
            'lapso_id'=>$lapso_id,
            'pensum_id'=>$pensum_id,
            'file_path'=>$file_path,
        ];
        return redirect()->route('administracion.boletins.carga.xls',$inputs);

    }


}
