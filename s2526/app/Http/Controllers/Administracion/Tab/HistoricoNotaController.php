<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\HistoricoNota;
use App\Models\app\HistoricoNota\Hnota;
use App\Models\app\HistoricoNota\Oinstitucion;
use App\Models\app\HistoricoNota\Tevaluacion;
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\SubAsignatura;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class HistoricoNotaController extends Controller
{

    public function storeCarga(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $grado = ($grado_id) ? Grado::findOrFail($grado_id) : collect();
        $seccion = ($seccion_id) ? Seccion::findOrFail($seccion_id) : collect();
        $pestudio = $grado->pestudio;
        $baremo = new Baremo();
        $datas = collect();
        $fecha = Carbon::now()->format('m-Y');
        $user_id = Auth::user()->id;
        $institucion = Institucion::OrderBy('created_at', 'DESC')->first();

        if ($seccion_id) {
            $estudiants = $seccion->estudiants_in;
            $pensums = ($grado_id) ? $grado->pensums : collect();
            foreach ($estudiants as $estudiant) {
                $historico_nota = HistoricoNota::withTrashed()->where('estudiant_id',$estudiant->id)->orderBy('updated_at', 'asc')->first();
                if (empty($historico_nota)) {
                    $arr = ['pestudio_id' => $pestudio->id, 'estudiant_id' => $estudiant->id];
                    $historico_nota = HistoricoNota::create($arr);
                } else {
                    $historico_nota->pestudio_id = $pestudio->id;
                    $historico_nota->deleted_at = null;
                    $historico_nota->save();

                    //eliminar duplicados
                    $duplicates = DB::table('historico_notas')
                        ->select('historico_notas.*')
                        ->where('historico_notas.id', '<>', $historico_nota->id)
                        ->where('estudiant_id', $estudiant->id)
                        ->get();
                    foreach ($duplicates as $item) {
                        DB::table('hnotas')->where('hnotas.historico_nota_id', $item->id)->delete();
                        DB::table('historico_notas')->delete($item->id);
                    }
                }

                foreach ($pensums as $pensum) {

                    $hnota = (isset($historico_nota)) ? $historico_nota->getHNota($pensum->id) : null;
                    if ($hnota) {
                        $hnota->fixNotasUnique();
                    }

                    $hnota_valor = ($hnota) ? $hnota->valor : null;
                    $hnota_literal = ($hnota) ? $hnota->literal : null;

                    $status_revision = $estudiant->getNotaRevisionStatus($pensum->id);
                    $nota_valor = ($status_revision) ? $estudiant->getNotaFinalRevision($pensum->id, 0) : $estudiant->getNotaFinal($pensum->id, 0);
                    $nota_literal = ($status_revision) ? $estudiant->getNotaFinalRevision($pensum->id, 0, true) : $estudiant->getNotaFinal($pensum->id, 0, true);

                    $tevaluacion_id = ($status_revision) ? '3' : '1';

                    $enable_academic_index = $pensum->enable_academic_index;
                    $inscripcion = $estudiant->inscripcion;
                    $grupo_estable = ($inscripcion->grupo_estable) ? $inscripcion->grupo_estable : null;
                    $grupo_estable_id = ($enable_academic_index && $grupo_estable) ? $grupo_estable->id : null;

                    $arr = [
                        'pensum_id' => $pensum->id,
                        'estudiant_id' => $estudiant->id,
                        'historico_nota_id' => $historico_nota->id,
                        'grupo_estable_id' => $grupo_estable_id,
                        'institucion_id' => $institucion->id,
                        'valor' => $nota_valor,
                        'literal' => $nota_literal,
                        'tevaluacion_id' => $tevaluacion_id,
                        'fecha' => $fecha,
                        'user_id' => $user_id,
                        'deleted_at' => null
                    ];
                    if (isset($hnota)) {
                        $hnota->fill($arr);
                        $hnota->save();
                    } else {
                        $hnota = Hnota::create($arr);
                    }
                }
            }
        }
        Session::flash('operp_ok', 'Registro guardado exitosamente');
        return redirect()->route('administracion.historico_notas.carga', compact('grado_id', 'seccion_id'));
    }
    public function carga(Request $request)
    {
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $estudiants = collect();
        if ($seccion_id) {
            $seccion = Seccion::findOrFail($seccion_id);
            $estudiants = $seccion->estudiants_in;
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : collect();

        $grado = ($grado_id) ? Grado::findOrFail($grado_id) : collect();
        $pensums = ($grado_id) ? $grado->pensums : collect();

        return view('administracion.historico_notas.carga', compact('estudiants', 'grado', 'grado_id', 'pensums', 'seccion_id', 'list_grado', 'list_seccion'));
    }

    public function crud(Request $request)
    {
        $grado_id = $request->get("grado_id");
        $seccion_id = $request->get("seccion_id");
        $search = $request->get("search");
        $status = $request->get("status", "active");

        // Inicialmente vacío si no hay búsqueda ni grado
        if (! $grado_id && ! $search) {
            $historico_notas = collect();
        } else {
            $historico_notas = HistoricoNota::select("historico_notas.*")
                ->join("estudiants", "estudiants.id", "=", "historico_notas.estudiant_id");

            if ($status === "all") {
                $historico_notas->withTrashed();
            } elseif ($status === "deleted") {
                $historico_notas->onlyTrashed();
            }

            if ($search) {
                $historico_notas->where(function ($query) use ($search) {
                    $query->where("estudiants.ci_estudiant", "like", "%" . $search . "%")
                        ->orWhere(DB::raw("concat(estudiants.lastname, ' ', estudiants.name)"), "like", "%" . $search . "%");
                });
            }

            if ($seccion_id || $grado_id) {
                $historico_notas->join("inscripcions", "inscripcions.estudiant_id", "=", "estudiants.id")
                    ->whereNull("inscripcions.deleted_at");

                if ($seccion_id) {
                    $historico_notas->where("inscripcions.seccion_id", $seccion_id);
                } else {
                    $historico_notas->join("seccions", "inscripcions.seccion_id", "=", "seccions.id")
                        ->where("seccions.grado_id", $grado_id);
                }
            }

            $historico_notas = $historico_notas->get();
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where("grado_id", $grado_id)->pluck("name", "id") : collect();

        return view("administracion.historico_notas.crud", compact(
            "historico_notas",
            "search",
            "grado_id",
            "seccion_id",
            "list_grado",
            "list_seccion",
            "status"
        ));
    }

    public function show($id)
    {
        $historico_nota = HistoricoNota::findOrFail($id);

        return view('administracion.historico_notas.show', compact('historico_nota'));
    }

    public function index(Request $request)
    {
        $estudiant_id = (!empty($request->estudiant_id)) ? $request->estudiant_id : null;
        $help_estudiant = (!empty($request->help_estudiant)) ? $request->help_estudiant : null;
        $pestudio_id = (!empty($request->pestudio_id)) ? $request->pestudio_id : null;
        // $finicial = (!empty($request->finicial)) ? $request->finicial : Carbon::now()->subYear(4)->format('m-Y');
        $finicial = (!empty($request->finicial)) ? $request->finicial : '07-' . Carbon::now()->subYear(4)->format('Y');

        $estudiant = Estudiant::find($estudiant_id);

        $historico_nota = HistoricoNota::where('estudiant_id', $estudiant_id)->first();

        $historico_nota = ($request->historico_nota_id) ? HistoricoNota::find($request->historico_nota_id) : $historico_nota;

        $pestudio = Pestudio::find($pestudio_id);

        $list_estudiant = Estudiant::list_pestudio_grado();

        $list_pestudio = Pestudio::select('id', 'name')
            ->where('status_active', 'true')
            ->orderby('name', 'asc')
            ->pluck('name', 'id');
        $list_institucion = Oinstitucion::select('id', 'name')
            ->orderby('id', 'asc')
            ->pluck('name', 'id');
        $list_tevaluacion = Tevaluacion::select('id', 'name')
            ->orderby('id', 'asc')
            ->pluck('name', 'id');
        $list_baremo = Baremo::select('id', 'valoracion', DB::raw("CONCAT(valoracion,' - ',description) as fullname"))
            ->where('pestudio_id', $pestudio_id)
            ->orderby('id', 'asc')
            ->pluck('fullname', 'valoracion');
        $list_grupo_estables = GrupoEstable::select('id', 'name', DB::raw("CONCAT(name,' || ',code) as fullname"))
            ->orderby('name', 'asc')
            ->pluck('fullname', 'id');
        $list_nota['-1'] = 'I';
        for ($i = 1; $i <= 20; $i++) {
            $list_nota[$i] = $i;
        }


        return view(
            'administracion.historico_notas.index',
            compact(
                'estudiant',
                'pestudio',
                'historico_nota',
                'estudiant_id',
                'help_estudiant',
                'pestudio_id',
                'finicial',
                'list_estudiant',
                'list_pestudio',
                'list_institucion',
                'list_tevaluacion',
                'list_nota',
                'list_baremo',
                'list_grupo_estables'
            )
        );
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'estudiant_id' => ['required', 'unique:historico_notas'],
        ]);

        // $grupo_estable_arr = $request->grupo_estable_id;
        $grupo_estable_arr = $request->input('grupo_estable_id', []);

        $institucion_arr = $request->institucion;
        $valor_arr = $request->valor;
        $literal_arr = $request->literal;
        $tipo_arr = $request->tipo;
        $fecha_arr = $request->fecha;
        $user_id = Auth::user()->id;

        $estudiant_id = (!empty($request->estudiant_id)) ? $request->estudiant_id : null;
        $help_estudiant = (!empty($request->help_estudiant)) ? $request->help_estudiant : null;
        $pestudio_id = (!empty($request->pestudio_id)) ? $request->pestudio_id : null;
        $finicial = (!empty($request->finicial)) ? $request->finicial : Carbon::now()->subYear(4)->format('m-Y');

        $historico_nota = HistoricoNota::create($request->all());

        //eliminar duplicados
        $duplicates = HistoricoNota::where('estudiant_id', '<>', $estudiant_id)->get();
        foreach ($duplicates as $item) {
            $hnotas = Hnota::where('id', $item->id)->delete();
            $duplicate = HistoricoNota::find($item->id);
            $duplicate = $duplicate->delete();
        }

        $asignatura_id = $request->asignatura_id;
        foreach ($asignatura_id as $k => $v) {

            $valor = (array_key_exists($k, $valor_arr)) ? $valor_arr[$k] : null;
            $literal = (array_key_exists($k, $literal_arr)) ? $literal_arr[$k] : null;

            $grupo_estable_id   = array_key_exists($k, $grupo_estable_arr) ? $grupo_estable_arr[$k] : null;
            $institucion_id     = (array_key_exists($k, $institucion_arr)   && ($valor || $literal)) ? $institucion_arr[$k]  : null;
            $tipo               = (array_key_exists($k, $tipo_arr)          && ($valor || $literal)) ? $tipo_arr[$k]         : null;
            $fecha              = (array_key_exists($k, $fecha_arr)         && ($valor || $literal)) ? $fecha_arr[$k]        : null;

            $arr = [
                'pensum_id' => $k,
                'grupo_estable_id' => $grupo_estable_id,
                'estudiant_id' => $estudiant_id,
                'historico_nota_id' => $historico_nota->id,
                'institucion_id' => $institucion_id,
                'valor' => $valor,
                'literal' => $literal,
                'tevaluacion_id' => $tipo,
                'fecha' => $fecha,
                'user_id' => $user_id
            ];

            $arr_test[] = $arr;
            $hnota = Hnota::create($arr);
        }

        $link_pdf = '<a class="btn btn-outline-danger btn-sm" target="_blank" href="' . route('administracion.historico_notas.certificacion.pdf', $historico_nota->id) . '" role="button"><i class="fas fa-file-pdf fa-1x"></i></a>';
        Session::flash('operp_ok', 'Registro guardado exitosamente. ' . $link_pdf);

        return redirect()->route('administracion.historico_notas.index', compact('estudiant_id', 'pestudio_id', 'finicial', 'help_estudiant'));
    }

    public function edit($id)
    {
        $historico_nota = HistoricoNota::findOrFail($id);

        $estudiant = $historico_nota->estudiant;

        $pestudio = $historico_nota->pestudio;

        $list_estudiant = Estudiant::select('id', DB::raw("CONCAT(ci_estudiant,' - ',name, ' ',lastname) as ci_fullname"))
            ->where('status_active', 'true')
            ->orderby('ci_estudiant', 'asc')
            ->pluck('ci_fullname', 'id');
        $list_pestudio = Pestudio::select('id', 'name')
            ->where('status_active', 'true')
            ->orderby('name', 'asc')
            ->pluck('name', 'id');
        $list_institucion = Oinstitucion::select('id', 'name')
            ->orderby('id', 'asc')
            ->pluck('name', 'id');
        $list_tevaluacion = Tevaluacion::select('id', 'name')
            ->orderby('id', 'asc')
            ->pluck('name', 'id');
        $list_baremo = Baremo::select('id', 'valoracion', DB::raw("CONCAT(valoracion,' - ',description) as fullname"))
            ->where('pestudio_id', $pestudio->id)
            ->orderby('id', 'asc')
            ->pluck('fullname', 'valoracion');
        $list_grupo_estables = GrupoEstable::select('id', 'name', DB::raw("CONCAT(name,' || ',code) as fullname"))
            ->orderby('name', 'asc')
            ->pluck('fullname', 'id');

        return view(
            'administracion.historico_notas.edit',
            compact(
                'historico_nota',
                'pestudio',
                'estudiant',
                'list_estudiant',
                'list_pestudio',
                'list_institucion',
                'list_tevaluacion',
                'list_baremo',
                'list_grupo_estables'
            )
        );
    }
    public function update(Request $request, $id)
    {
        $estudiant_id = $request->estudiant_id;
        $grupo_estable_arr = $request->grupo_estable_id;
        $institucion_arr = $request->institucion;
        $valor_arr = $request->valor;
        $literal_arr = $request->literal;
        $tipo_arr = $request->tipo;
        $fecha_arr = $request->fecha;
        $user_id = Auth::user()->id;

        $historico_nota = HistoricoNota::findOrFail($id);
        $historico_nota->fill($request->all());
        $historico_nota->deleted_at = null;
        $historico_nota->save();

        //eliminar duplicados
        $duplicates = DB::table('historico_notas')
            ->select('historico_notas.*')
            ->where('historico_notas.id', '<>', $historico_nota->id)
            ->where('estudiant_id', $estudiant_id)
            ->get();
        foreach ($duplicates as $item) {
            DB::table('hnotas')->where('hnotas.historico_nota_id', $item->id)->delete();
            DB::table('historico_notas')->delete($item->id);
        }

        $asignatura_id = $request->asignatura_id; //dd($asignatura_id);
        $datas = collect();
        foreach ($asignatura_id as $k => $v) {
            $valor = null;
            $literal = null;
            $grupo_estable_id = null;
            if (is_array($valor_arr))  $valor = (array_key_exists($k, $valor_arr)) ? $valor_arr[$k] : null;
            if (is_array($literal_arr))  $literal = (array_key_exists($k, $literal_arr)) ? $literal_arr[$k] : null;
            if (is_array($grupo_estable_arr))  $grupo_estable_id = (array_key_exists($k, $grupo_estable_arr)) ? $grupo_estable_arr[$k] : null;

            $institucion_id     = (array_key_exists($k, $institucion_arr)) ? $institucion_arr[$k]  : null;
            $tipo               = (array_key_exists($k, $tipo_arr)) ? $tipo_arr[$k]         : null;
            $fecha              = (array_key_exists($k, $fecha_arr)) ? $fecha_arr[$k]        : null;

            // dd($tipo_arr);
            // if ($k == 73) dd($tipo, $tipo_arr, $k);

            $arr = [
                'pensum_id' => $k,
                'grupo_estable_id' => $grupo_estable_id,
                'estudiant_id' => $estudiant_id,
                'historico_nota_id' => $historico_nota->id,
                'institucion_id' => $institucion_id,
                'valor' => $valor,
                'literal' => $literal,
                'tevaluacion_id' => $tipo,
                'fecha' => $fecha,
                'user_id' => $user_id,
                'deleted_at' => null,
            ];

            // if ($k == 73) dd($arr, $tipo, $tipo_arr, $k);

            $arr_test[] = $arr;
            $hnota = Hnota::where('historico_nota_id', $historico_nota->id)->where('pensum_id', $k)->first();

            if ($hnota) {
                $hnota->update($arr);
                $datas->push($arr);
            } else {
                $hnota = Hnota::create($arr);
                $datas->push($arr);
            }
        }

        $historico_nota = HistoricoNota::findOrFail($id);

        $estudiant_id = (!empty($request->estudiant_id)) ? $request->estudiant_id : null;
        $help_estudiant = (!empty($request->help_estudiant)) ? $request->help_estudiant : null;
        $pestudio_id = (!empty($request->pestudio_id)) ? $request->pestudio_id : null;
        $finicial = (!empty($request->finicial)) ? $request->finicial : Carbon::now()->subYear(4)->format('m-Y');

        $link_pdf = '<a class="btn btn-outline-danger btn-sm" target="_blank" href="' . route('administracion.historico_notas.certificacion.pdf', $historico_nota->id) . '" role="button"><i class="fas fa-file-pdf fa-1x"></i></a>';
        Session::flash('operp_ok', 'Registro actualizado exitosamente. ' . $link_pdf);

        return redirect()->route('administracion.historico_notas.index', compact('estudiant_id', 'pestudio_id', 'finicial', 'help_estudiant'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
        $historico_nota = HistoricoNota::findOrFail($id);

        // Soft delete the main record and its related notes
        $historico_nota->hnotas()->delete();
        $historico_nota->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation = 'delete';

        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
            ]);
        }

        return redirect()->back()->with('operp_ok', $messenge);
    }

    /**
     * Restore the specified resource from storage.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restore($id, Request $request)
    {
        $historico_nota = HistoricoNota::withTrashed()->findOrFail($id);

        // Restore the main record and its related notes
        $historico_nota->restore();
        $historico_nota->hnotas()->restore();

        $messenge = "Registro restaurado exitosamente";
        $operation = 'restore';

        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
            ]);
        }

        return redirect()->back()->with('operp_ok', $messenge);
    }
}
