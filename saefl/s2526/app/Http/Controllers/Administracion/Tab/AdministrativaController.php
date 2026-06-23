<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Helpers\Convertidor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

//validation request
// use App\Http\Requests\Administracion\CreateUserRequest;
// use App\Http\Requests\Administracion\UpdateUserRequest;

use App\Models\app\Pescolar;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Descuento;
use App\Models\app\Estudiante\PlanBenefico;
use App\Models\app\Estudiante\Retiro;
use App\Models\app\Estudiante\Tinscripcion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Prosecucion;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Planpago;
use App\User;

class AdministrativaController extends Controller
{

    public function asistente(Request $request)
    {
        return view('administracion.administrativas.asistente');
    }

    public function unregistered(Request $request)
    {
        $search = $request->search ?? null;
        $status_prosecucion = $request->status_prosecucion ?? null;
        $prosecucion_seccion_id = $request->prosecucion_seccion_id ?? null;

        $estudiants = collect();

        if (count($request->all()) > 0) {
            $estudiants = Estudiant::select('estudiants.*');

            if ($prosecucion_seccion_id || $status_prosecucion === 'SI') {
                // Subconsulta para obtener estudiantes con administrativas que SÍ afectan la inscripción
                $estudiants = $estudiants
                    ->join('prosecucions', 'estudiants.id', '=', 'prosecucions.estudiant_id')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('administrativas')
                            ->join('planpagos', 'administrativas.planpago_id', '=', 'planpagos.id')
                            ->whereColumn('administrativas.estudiant_id', 'estudiants.id')
                            ->where('planpagos.status_inscription_affects', true); // Solo si afecta inscripción
                    });

                if ($prosecucion_seccion_id) {
                    $estudiants = $estudiants->where('prosecucions.seccion_id', $prosecucion_seccion_id);
                }
            }

            // ✅ Filtro de búsqueda: name o lastname
            if ($search) {
                $estudiants = $estudiants->where(function ($query) use ($search) {
                    $query->where('estudiants.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('estudiants.lastname', 'LIKE', '%' . $search . '%');
                });
            }

            $estudiants = $estudiants->get();
        }

        $list_prosecucion = Prosecucion::list_prosecucion();

        return view('administracion.administrativas.unregistered', compact(
            'estudiants',
            'status_prosecucion',
            'search',
            'list_prosecucion',
            'prosecucion_seccion_id'
        ));
    }

    public function retiro(Request $request)
    {
        $search         = (!empty($request->search)) ? $request->search : null;
        $planpago_id       = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $grado_id       = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id     = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $status_preinscripcion     = (!empty($request->status_preinscripcion)) ? $request->status_preinscripcion : null;
        $status_inscription_affects     = (!empty($request->status_inscription_affects)) ? $request->status_inscription_affects : null;
        $status_active     = (!empty($request->status_active)) ? $request->status_active : null;
        $formally     = (!empty($request->formally)) ? $request->formally : null;

        $estudiants = collect([]);

        if (count($request->all()) > 0) {

            $estudiants = Estudiant::select('estudiants.*')
                ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                //->where('estudiants.status_active','true')
            ;

            if ($search) {
                $search = $request->get('search');
                $arr_get = ['search' => $search];
                $estudiants = $estudiants->name($arr_get);
            }

            $estudiants = ($planpago_id) ? $estudiants->where('administrativas.planpago_id', $planpago_id) : $estudiants;
            $estudiants = ($grado_id) ? $estudiants->where('grados.id', $grado_id) : $estudiants;
            $estudiants = ($seccion_id) ? $estudiants->where('seccions.id', $seccion_id) : $estudiants;

            $estudiants = $estudiants->get();
        }

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : array();
        $list_planpago = Planpago::select('name', 'id')->where('status_active', 'true')->orderby('id', 'asc')->pluck('name', 'id'); //list_planpago

        return view('administracion.administrativas.retiro', compact('estudiants', 'search', 'list_planpago', 'status_preinscripcion', 'formally', 'status_active', 'status_inscription_affects', 'list_grado', 'list_seccion', 'planpago_id', 'grado_id', 'seccion_id'));
    }

    public function edit($id)
    {
        $administrativa = Administrativa::findOrFail($id);
        $list_planpago = Planpago::select('name', 'id')->orderby('name', 'asc')->pluck('name', 'id');
        return view('administracion.administrativas.edit', compact('administrativa', 'list_planpago'));
    }
    public function update(Request $request, $id)
    {
        $administrativa = Administrativa::findOrFail($id);

        $administrativa->fill($request->all());

        $administrativa->save();

        $messenge = trans('db_oper_result.user_update_ok');

        Session::flash('operp_ok', $messenge);

        Session::flash('class_oper', 'success');

        return redirect()->route('administracion.administrativas.edit', $id);
    }

    public function destroy($id, Request $request)
    {
        $administrativa = Administrativa::findOrFail($id);
        $estudiant = Estudiant::findOrFail($administrativa->estudiant_id);
        $administrativa->delete();
        $messenge = trans('db_oper_result.delete_ok');
        $operation = 'delete';
        $div = '
        <a class="btn-inscribir btn btn-info btn-sm" href="' . route('administracion.administrativas.inscribir', $estudiant->id) . '">
            <i class="fa fa-check" aria-hidden="true"></i>
        </a>';

        if ($request->ajax()) {
            return response()->json([
                "messenge" => $messenge,
                "operation" => $operation,
                "div" => $div,
            ]);
        }

        Session::flash('operp_ok', $messenge . ' -> (' . $estudiant->name . ')');
        $estudiants = Estudiant::active('true')
            ->select('estudiants.*')
            ->join('administrativas', 'administrativas.estudiant_id', '=', 'estudiants.id')->get();

        return redirect()->route('administracion.administrativas.crud', compact('estudiants'));
    }

    public function list_view_excel()
    {
        $list_pescolar =
            Pescolar::select('pescolars.*')
            ->orderby('pescolars.name', 'asc')
            ->pluck('name', 'id');
        return view('administracion.administrativas.list.view.excel', compact('list_pescolar'));
    }

    public function crud(Request $request)
    {
        $search         = (!empty($request->search)) ? $request->search : null;
        $planpago_id       = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $grado_id       = (!empty($request->grado_id)) ? $request->grado_id : null;
        $seccion_id     = (!empty($request->seccion_id)) ? $request->seccion_id : null;
        $status_active     = (!empty($request->status_active)) ? $request->status_active : null;
        $status_inscription_affects     = (!empty($request->status_inscription_affects)) ? $request->status_inscription_affects : null;
        $planpago_id     = (!empty($request->planpago_id)) ? $request->planpago_id : null;
        $status_preinscripcion     = (!empty($request->status_preinscripcion)) ? $request->status_preinscripcion : null;
        $formally = (!empty($request->formally)) ? $request->formally : null;

        $estudiants = collect([]);

        if (count($request->all()) > 0) {

            $estudiants = Estudiant::select('estudiants.*')
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')
                ->leftjoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->leftjoin('grados', 'grados.id', '=', 'seccions.grado_id')
                // ->where('estudiants.status_active','true')
                // ->where('planpagos.status_inscription_affects','true')
            ;

            $estudiants = ($formally == 'SI') ? $estudiants->whereNotNull('inscripcions.id')->where('seccions.status_active', 'true')->where('planpagos.status_inscription_affects', 'true') : $estudiants;
            $estudiants = ($formally == 'NO') ? $estudiants->whereNull('inscripcions.id') : $estudiants;

            $estudiants = ($status_active == 'true') ? $estudiants->where('seccions.status_active', 'true') : $estudiants;
            $estudiants = ($status_active == 'false') ? $estudiants->where('seccions.status_active', 'false') : $estudiants;

            $estudiants = ($status_inscription_affects == 'true') ? $estudiants->where('planpagos.status_inscription_affects', 'true') : $estudiants;
            $estudiants = ($status_inscription_affects == 'false') ? $estudiants->where('planpagos.status_inscription_affects', 'false') : $estudiants;

            if ($search) {
                $search = $request->get('search');
                $arr_get = ['search' => $search];
                $estudiants = $estudiants->name($arr_get);
            }

            $estudiants = ($planpago_id) ? $estudiants->where('administrativas.planpago_id', $planpago_id) : $estudiants;
            $estudiants = ($grado_id) ? $estudiants->where('grados.id', $grado_id)->where('seccions.status_active', 'true') : $estudiants;
            $estudiants = ($seccion_id) ? $estudiants->where('seccions.id', $seccion_id)->where('seccions.status_active', 'true') : $estudiants;
            $estudiants = ($status_preinscripcion == 'SI') ? $estudiants->join('preinscripcions', 'estudiants.id', '=', 'preinscripcions.estudiant_id') : $estudiants;

            $estudiants = $estudiants->get(); //dd($planpago_id,$estudiants);
        }

        /*******************list****************************/
        // $pestudios = Pestudio::active('true')->with('grados')->orderBy('id', 'asc')->get();
        // foreach ($pestudios as $pestudio) {
        //     $list_grado[$pestudio->name] = $pestudio->grados->pluck('name', 'id');
        // }
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id', $grado_id)->pluck('name', 'id') : array();
        $list_planpago = Planpago::select('name', 'id')->where('status_active', 'true')->orderby('id', 'asc')->pluck('name', 'id');

        $list_pescolar = Pescolar::select('pescolars.*')->orderby('pescolars.name', 'asc')->pluck('name', 'id');

        return view('administracion.administrativas.crud', compact('list_pescolar', 'estudiants', 'formally', 'search', 'status_preinscripcion', 'list_planpago', 'list_grado', 'list_seccion', 'planpago_id', 'grado_id', 'seccion_id', 'status_active', 'status_inscription_affects'));
    }

    public function listview()
    {
        $list_pescolar =
            Pescolar::select('pescolars.*')
            ->orderby('pescolars.name', 'asc')
            ->pluck('name', 'id');
        return view('administracion.administrativas.list.view', compact('list_pescolar'));
    }

    public function book(Request $request)
    {
        $list_pescolar =
            Pescolar::select('pescolars.*')
            ->orderby('pescolars.name', 'asc')
            ->pluck('name', 'id');

        $pescolar_id = ($request->get('pescolar_id')) ? $request->get('pescolar_id') : Session::get('pescolar_id');

        $pestudios = Pestudio::Orderby('id', 'asc')->where('status_active', 'true')->get();
        $grados = Grado::Orderby('id', 'asc')->where('status_active', 'true')->get();
        $seccions = Seccion::Orderby('id', 'asc')->get();
        $tinscripcions = Tinscripcion::Orderby('id', 'asc')->get();
        $std_siaca_ciadm = Administrativa::std_siaca_ciadm();
        $std_ciaca_siadm = Administrativa::std_ciaca_siadm();

        return view('administracion.administrativas.book', compact('std_siaca_ciadm', 'std_ciaca_siadm', 'pescolar_id', 'list_pescolar', 'pestudios', 'grados', 'seccions', 'tinscripcions'));
    }

    public function asignar(Request $request)
    {

        $search = (!empty($request->search)) ? $request->search : null;
        $prosecucion_seccion_id = (!empty($request->prosecucion_seccion_id)) ? $request->prosecucion_seccion_id : null;
        $status_preinscripcion = (!empty($request->status_preinscripcion)) ? $request->status_preinscripcion : null;

        $estudiants = collect([]);

        if (count($request->all()) > 0) {

            $estudiants = Estudiant::select('estudiants.*')
                ->leftjoin('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->leftjoin('prosecucions', 'estudiants.id', '=', 'prosecucions.estudiant_id')
                ->active()
                ->where('estudiants.status_active', 'true')
                ->orderBy('estudiants.ci_estudiant')
                ->groupBy('estudiants.id');

            if ($search) {
                $search = $request->get('search');
                $arr_get = ['search' => $search];
                $estudiants = $estudiants->name($arr_get);
            }

            $estudiants = ($prosecucion_seccion_id) ? $estudiants->where('prosecucions.seccion_id', $prosecucion_seccion_id) : $estudiants;
            $estudiants = ($status_preinscripcion == 'SI') ? $estudiants->join('preinscripcions', 'estudiants.id', '=', 'preinscripcions.estudiant_id') : $estudiants;
            $estudiants = ($status_preinscripcion == 'NO') ? $estudiants->leftjoin('preinscripcions', 'estudiants.id', '=', 'preinscripcions.estudiant_id')->whereNull('preinscripcions.id') : $estudiants;

            $estudiants = $estudiants->get();
        }

        $list_grado = Grado::list_pestudio_grado();
        $planpago_list = Planpago::visible()->orderBy('name')->pluck('name', 'id');
        $list_descuentos = Descuento::descuentos_list();

        $list_prosecucion = Prosecucion::list_prosecucion(); //dd($list_prosecucion);

        $compact_arr = [
            'estudiants',
            'planpago_list',
            'list_descuentos',
            'list_prosecucion',
            'search',
            'prosecucion_seccion_id',
            // 'status_enable',
            'status_preinscripcion'
        ];

        return view('administracion.administrativas.asignar', compact($compact_arr));
    }

    public function asignarStore(Request $request)
    {
        $search = (!empty($request->search)) ? $request->search : null;
        $prosecucion_seccion_id = (!empty($request->prosecucion_seccion_id)) ? $request->prosecucion_seccion_id : null;
        $status_preinscripcion     = (!empty($request->status_preinscripcion)) ? $request->status_preinscripcion : null;
        $count = null;
        $planpago_arr = (!empty($request->planpago_arr)) ? $request->planpago_arr : null;
        $descuentos_arr = (!empty($request->descuentos_arr)) ? $request->descuentos_arr : null;

        $datas = collect();

        if ($planpago_arr) {
            if (is_array($planpago_arr)) {
                $count = 0;
                foreach ($planpago_arr as $estudiant_id => $planpago_id) {
                    $datas->put($estudiant_id, $planpago_id);
                    if ($planpago_id && $estudiant_id) {
                        $estudiant = Estudiant::findOrFail($estudiant_id);
                        $planpago = Planpago::findOrFail($planpago_id);

                        $administrativa = Administrativa::where('estudiant_id', $estudiant->id)->first();

                        if ($administrativa) {
                            $administrativa->fill(['planpago_id' => $planpago->id]);
                            $administrativa->save();
                            $count++;
                        } else {
                            $create = Administrativa::create([
                                'estudiant_id' => $estudiant_id,
                                'planpago_id' => $planpago_id,
                                'user_id' => Auth::user()->id
                            ]);
                            $count++;
                        }

                        if (is_array($descuentos_arr)) {
                            if (array_key_exists($estudiant_id, $descuentos_arr)) {
                                $descuento_id = $descuentos_arr[$estudiant_id];
                                $descuento = Descuento::find($descuento_id);
                                if ($descuento) {
                                    $year = Carbon::now()->year;
                                    $arr = [
                                        'estudiant_id' => $estudiant_id,
                                        'descuento_id' => $descuento_id,
                                        'name' => $descuento->descuento_name,
                                        'status_active' => 'true',
                                        'ffinal' => $year . '-08-30',
                                        'created_at' => $year . '-08-01'
                                    ];
                                    $plan_benefico = PlanBenefico::where('estudiant_id', $estudiant_id)->first();
                                    if ($plan_benefico) {
                                        $plan_benefico->fill($arr);
                                        $plan_benefico->save();
                                    } else {
                                        $plan_benefico = PlanBenefico::create($arr);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $count_sentence = Convertidor::numToSentence($count);

        $messege = ($count > 1) ? 'Inscripciones guardadas correctamente!!. ' . $count_sentence . ' (' . $count . ') Registros procesados' : 'Inscripción guardada correctamente!!. ' . $count_sentence . ' (' . $count . ') Registro procesado';

        Session::flash('operp_ok', $messege);

        $inputsArr = ['search' => $search, 'prosecucion_seccion_id' => $prosecucion_seccion_id, 'status_preinscripcion' => $status_preinscripcion];


        return redirect()->route('administracion.administrativas.asignar', $inputsArr);
    }

    public function inscribir($id, Request $request)
    {
        // dd($id, $request->all());

        $planpago_id = $request->planpago_id;

        $administrativa = Administrativa::where('estudiant_id', $id)->first();

        if (!$administrativa) {
            $administrativa = Administrativa::create([
                'estudiant_id' => $id,
                'planpago_id' => $planpago_id,
                'user_id' => Auth::user()->id
            ]);

            $retiro = Retiro::where('estudiant_id', $id)->first();
            if ($retiro) {
                $retiro->delete();
            }
        }

        $title = 'Inscrito el: ' . $administrativa->created_at . ' - Plan de pago: ' . $administrativa->planpago->name;

        if ($request->ajax()) {
            return response()->json([
                "messenge" => 'Estudiante con inscripción administrativa registrada exitosamente',
                "operation" => 'operp_ok',
                "div" => '
                    <a title="' . $title . '" class="btn btn-secondary btn-sm disabled" href="#">
                        <i class="fa fa-check" aria-hidden="true"></i>
                    </a>',
            ]);
        }
    }

    public function set_plan(Request $request)
    {
        $planpago_id = $request->all()['planpago_id'];
        $id = $planpago_id;

        $arr = $request->all()['arr_planpago'];
        foreach ($arr as $k => $v) {
            $estudiant = Estudiant::where('id', $k);
            if ($v == "true") {
                $estudiant->update(['planpago_id' => $planpago_id]);
                $administrativa = Administrativa::where('estudiant_id', $k)->first();
                if (!$administrativa) {
                    $create = Administrativa::create([
                        'estudiant_id' => $k,
                        'planpago_id' => $planpago_id,
                        'user_id' => Auth::user()->id
                    ]);
                }
            } else {
                $estudiant->update(['planpago_id' => '1']);
            }
            unset($estudiant);
        }

        Session::flash('operp_ok', 'Registro guardado exitosamente, Plan de Pago asignado correctamente');
        $estudiants = Estudiant::where('status_active', 'true')->get();
        $planpago = Planpago::findOrFail($planpago_id);
        $planpago_list = Planpago::select('name', 'id')->where('status_active', 'true')->orderby('id', 'asc')->pluck('name', 'id');
        return redirect()->route('administracion.administrativas.asignar', compact('id', 'estudiants', 'planpago_list', 'planpago'));
    }
}
