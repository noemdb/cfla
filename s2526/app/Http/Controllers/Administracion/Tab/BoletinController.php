<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\BoletinAjuste;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\Models\app\Profesor\Pevaluacion\Ecualitativa;

use App\Http\Controllers\Administracion\Tab\Functions\Boletins\Crud;
use App\Http\Controllers\Administracion\Tab\Functions\Boletins\Carga;

class BoletinController extends Controller
{
    use Crud;
    use Carga;

    public function edit($id)
    { // 'estudiant_id','evaluacion_id','nota','ajuste','user_id','observations'
        $boletin = Boletin::findOrFail($id);
        $evaluacion = $boletin->evaluacion;
        $pevaluacion = $boletin->pevaluacion;

        $list_evaluacion = Evaluacion::list_evaluacion($pevaluacion->id); //dd($list_evaluacion);
        $list_comment = Boletin::COLUMN_COMMENTS;
        return view('administracion.boletins.edit', compact('boletin', 'evaluacion', 'pevaluacion', 'list_evaluacion', 'list_comment'));
    }

    public function update(Request $request, $id)
    {
        $boletin = Boletin::findOrFail($id);

        $boletin->fill($request->all());

        $boletin->save();

        $messenge = trans('db_oper_result.update_ok');

        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "grupo_estable" => $request->grupo_estable,
            ]);
        }

        Session::flash('operp_ok', $messenge);

        Session::flash('class_oper', 'success');

        return redirect()->route('administracion.boletins.edit', $id);
    }

    public function resumen_final(Request $request)
    {
        /*******************request****************************/
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;

        /*******************inicializaciones****************************/
        $seccions = Seccion::active('true')->OrderBy('created_at','desc');

        /*******************query****************************/
        $seccions = ($grado_id) ? $seccions->where('grado_id', $grado_id) : $seccions;
        $seccions = ($seccion_id) ? $seccions->where('id', $seccion_id) : $seccions;

        $seccions = $seccions->get();
        $seccions = $seccions->sortByDesc(function ($value, $key) {
            return (!empty($value->grado->pestudio->code)) ? $value->grado->pestudio->code : null;
        });

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : collect();

        return view(
            'administracion.boletins.resumen_final',
            compact('seccions', 'grado_id', 'list_grado', 'seccion_id', 'list_seccion')
        );
    }

    public function index(Request $request)
    {
        /*******************request****************************/
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id : null;
        $pensums_id = (!empty($request->pensums_id)) ? $request->pensums_id : null;
        $profesor_id = (!empty($request->profesor_id)) ? $request->profesor_id : null;

        /*******************inicializaciones****************************/
        $pevaluacions = collect();

        /*******************query****************************/
        if (count($request->all()) > 0) {
            $pevaluacions = Pevaluacion::select('pevaluacions.*')
                ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
                ->OrderBy('created_at', 'desc');

            /*******************if()?****************************/
            $pevaluacions = ($grado_id) ? $pevaluacions->where('pensums.grado_id', $grado_id) : $pevaluacions;
            $pevaluacions = ($seccion_id) ? $pevaluacions->where('pevaluacions.seccion_id', $seccion_id) : $pevaluacions;
            $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id', $lapso_id) : $pevaluacions;
            $pevaluacions = ($profesor_id) ? $pevaluacions->where('pevaluacions.profesor_id', $profesor_id) : $pevaluacions;
            $pevaluacions = ($pensums_id) ? $pevaluacions->where('pensums.id', $pensums_id) : $pevaluacions;

            /*******************get collections****************************/
            $pevaluacions = $pevaluacions->get();
        }

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : collect();
        $list_lapso = Lapso::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');
        $list_profesor = Profesor::list_profesors();
        $list_pensum    = Pensum::list_pestudio_pensum($grado_id);

        return view(
            'administracion.boletins.index',
            compact('pevaluacions', 'grado_id', 'list_grado', 'seccion_id', 'list_seccion', 'lapso_id', 'list_lapso', 'list_profesor', 'profesor_id', 'list_pensum', 'pensums_id')
        );
    }

    public function carga(Request $request)
    {
        $validatedData = $request->validate([
            'pevaluacion_id' => 'required|exists:pevaluacions,id'
        ]);

        $pevaluacion_id = (!empty($request->pevaluacion_id)) ? $request->pevaluacion_id : null;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : 1;
        $grado = Grado::findOrFail($grado_id);
        $boletin = new Boletin;
        $list_comment = Ecualitativa::COLUMN_COMMENTS;

        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : 1;
        $seccion = Seccion::findOrFail($seccion_id);

        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id : 1;
        $lapso = Lapso::findOrFail($lapso_id);

        // $pensum_id = (!empty($request->pensum_id)) ? $request->pensum_id:$grado->pensums->first()->id; ecualitativa
        if (empty($request->pensum_id)) {
            $pensum_id = (!empty($grado->pensums->first())) ? $grado->pensums->first()->id : null;
        } else {
            $pensum_id = $request->pensum_id;
        }

        $pensums = $grado->pensums;

        $pensum = Pensum::where('id', $pensum_id)->first();

        $estudiants = $seccion->estudiants_in;
        $list_grado = Grado::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');
        $list_seccion = $grado->seccions()->pluck('name', 'id');

        $list_pensum =
            Pensum::select('pensums.id as id', 'asignaturas.name as name')
            ->join('asignaturas', 'pensums.asignatura_id', '=', 'asignaturas.id')
            ->where('pensums.grado_id', $grado_id)
            ->pluck('name', 'id');

        $list_lapso = Lapso::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');
        $list_lapso->put(null, 'Final');

        // $pevaluacion_id = (!empty($request->pevaluacion_id)) ? $pevaluacion_id : null ;

        $pevaluacion = Pevaluacion::where('id', $pevaluacion_id)->first(); //dd($pevaluacion->escala);

        $escala = ($pevaluacion && $pevaluacion->escala) ? $pevaluacion->escala : null;
        $minimo = ($escala) ? $escala->minimo : 0;
        $maximo = ($escala) ? $escala->maximo : 20;
        // $minimo = $pevaluacion->escala->minimo;
        // $maximo = $pevaluacion->escala->maximo;
        $list_nota['-1'] = null;
        $list_nota[0] = 'I';
        for ($i = $minimo; $i <= $maximo; $i++) {
            $list_nota[$i] = $i;
        }

        return view(
            'administracion.boletins.carga',
            compact(
                'pevaluacion',
                'pevaluacion_id',
                'estudiants',
                'pensum',
                'pensums',
                'pensum_id',
                'list_pensum',
                'grado',
                'grado_id',
                'list_grado',
                'seccion',
                'seccion_id',
                'list_seccion',
                'lapso',
                'lapso_id',
                'list_lapso',
                'boletin',
                'list_nota',
                'list_comment'
            )
        );
    }

    public function store(Request $request)
    {
        //dd($request);
        $nota_arr = (is_array($request->nota)) ? $request->nota : array();
        foreach ($nota_arr as $estudiant_id => $evaluacions) {

            foreach ($evaluacions as $evaluacion_id => $nota) {

                if (isset($nota) && !(is_null($nota)) && $nota <> ''  && $nota <> null) {

                    $boletin = Boletin::where('estudiant_id', $estudiant_id)->where('evaluacion_id', $evaluacion_id)->first();

                    if ($nota == -1) {
                        if ($boletin) {
                            $boletin->delete();
                        }
                    } else {
                        $arr = [
                            'estudiant_id' => $estudiant_id,
                            'evaluacion_id' => $evaluacion_id,
                            'nota' => $nota
                        ];

                        if ($boletin) {
                            $boletin->fill($arr);
                            $boletin->save();
                        } else {
                            $create = Boletin::create($arr);
                        }
                    }
                }
            }
        }

        $messenge = trans('db_oper_result.oper_ok');
        $operation = 'create';
        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
            ]);
        }

        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok', $messenge);

        return redirect()->route('administracion.boletins.index');
    }

    public function store_ajustes(Request $request)
    {

        $pevaluacion_id = $request->pevaluacion_id;
        $estudiant_id = $request->estudiant_id;

        $arr_dat = [
            'pevaluacion_id' => $pevaluacion_id,
            'estudiant_id' => $estudiant_id,
            'ajuste' => $request->ajuste,
            'user_id' => Auth::User()->id,
        ];

        $obj = BoletinAjuste::where('estudiant_id', $estudiant_id)->where('pevaluacion_id', $pevaluacion_id);

        if ($obj->count() > 0) {
            $update = $obj->update($arr_dat);
        } else {
            $create = BoletinAjuste::create($arr_dat);
        }

        $estudiant = Estudiant::findOrFail($estudiant_id);

        $nota = $estudiant->getNotaPevaluacion($pevaluacion_id, 0);
        $ajuste = $estudiant->getAjuste($pevaluacion_id);

        $messenge = trans('db_oper_result.oper_ok');
        $operation = 'create';
        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
                "nota" => $nota,
                "ajuste" => $ajuste,
            ]);
        }
    }

    public function destroy($id, Request $request)
    {
        $boletin = Boletin::findOrFail($id);
        $boletin->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation = 'delete';
        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
            ]);
        }
    }
    public function destroyAjuste($id, Request $request)
    {
        $boletin = BoletinAjuste::findOrFail($id);
        $boletin->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation = 'delete';
        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
            ]);
        }
    }
}
