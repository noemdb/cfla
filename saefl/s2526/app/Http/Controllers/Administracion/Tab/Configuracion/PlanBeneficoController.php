<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

//validation request
use App\Http\Requests\Administracion\Configuracion\CreatePlanBeneficorRequest;
use App\Http\Requests\Administracion\Configuracion\UpdatePlanBeneficoRequest;
// use App\Http\Requests\Administracion\UpdateUserRequest;

use App\Models\app\Pescolar;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Descuento;
use App\Models\app\Estudiante\PlanBenefico;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;

class PlanBeneficoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['is_admon']);
    }

    public function edit($id)
    {
        $plan_benefico = PlanBenefico::findOrFail($id);

        $list_descuentos = Descuento::select('descuento_name', 'id',DB::raw("CONCAT(descuento_name,' - ',descuento_ammount,'%') as fullname"))->orderby('descuento_name','asc')->pluck('fullname', 'id');

        $descuentos = Descuento::all();

        return view('administracion.configuraciones.plan_beneficos.edit',compact('plan_benefico','descuentos','list_descuentos'));
    }
    public function update(UpdatePlanBeneficoRequest $request, $id)
    {
        $plan_benefico = PlanBenefico::findOrFail($id);
        $pescolar = Pescolar::first();
        $arr = [
            'estudiant_id'=>$request->estudiant_id,
            'descuento_id'=>$request->descuento_id,
            'name'=>$request->name,
            'status_active'=>'true',
            'ffinal'=>$request->ffinal,
            'created_at'=>$request->created_at,
        ];
        $plan_benefico->update($arr);
        // $plan_benefico->fill($request->all());
        // $plan_benefico->save();

        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.plan_beneficos.edit',$id);
    }

    public function destroy($id, Request $request)
    {
        $plan_benefico = PlanBenefico::findOrFail($id);

        $plan_benefico->fill(['ffinal'=>Carbon::now()]);
        $plan_benefico->save();

        $plan_benefico->delete();
        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation
            ]);
        }

        Session::flash('operp_ok',$messenge.' -> ('.$plan_benefico->estudiant->name.')');
        $plan_beneficos = PlanBenefico::all();
        return view('administracion.configuraciones.plan_beneficos.crud',compact('plan_beneficos'));
    }

    public function crud(Request $request)
    {
        $plan_beneficos = PlanBenefico::all();

        // foreach ($plan_beneficos as $item) {
        //     $arr = [
        //         'estudiant_id'=>$item->estudiant_id,
        //         'descuento_id'=>$item->descuento_id,
        //         'name'=>$item->name,
        //         'status_active'=>'true',
        //         'ffinal'=>'2025-08-31',
        //         'created_at'=>'2025-08-01'
        //     ];    //dd($arr);
        //     $plan_benefico = PlanBenefico::create($arr);
        // }

        $plan_beneficos = PlanBenefico::all();

        return view('administracion.configuraciones.plan_beneficos.crud',compact('plan_beneficos'));
    }

    public function index(Request $request)
    {

        //$plan_beneficos = PlanBenefico::where('name','like','%PRONTO PAGO%')->get(); dd($plan_beneficos);
        if ($request->get('search')) {

            $search = $request->get('search');
            $arr_get = [ 'search'=>$search ];

            $estudiants = Estudiant::name($arr_get)
            ->OrderBy('id', 'asc')
            ->get();

            return view('administracion.configuraciones.plan_beneficos.index',compact('estudiants','search'));
        }
        else{
            $search = '';
            return view('administracion.configuraciones.plan_beneficos.index',compact('search'));
        }
    }
    public function create($id)
    {
        $estudiant = Estudiant::findOrFail($id);

        $descuentos = Descuento::all();

        $list_descuentos = Descuento::select('descuento_name', 'id',DB::raw("CONCAT(descuento_name,' - ',descuento_ammount,'%') as fullname"))
            ->orderby('descuento_name','asc')
            ->pluck('fullname', 'id');

        return view('administracion.configuraciones.plan_beneficos.create',compact('estudiant','descuentos','list_descuentos'));
    }

    public function store(CreatePlanBeneficorRequest $request)
    {
        $estudiant = Estudiant::findOrFail($request->estudiant_id);

        $pescolar = Pescolar::first();

        $search = $estudiant->ci_estudiant;

        $arr = [
            'estudiant_id'=>$request->estudiant_id,
            'descuento_id'=>$request->descuento_id,
            'name'=>$request->name,
            'status_active'=>'true',
            'ffinal'=>$request->ffinal,
            'created_at'=>$request->created_at,
        ];

        $plan_benefico = PlanBenefico::create($arr);

        Session::flash('operp_ok','Registro guardado exitosamente');

        return redirect()->route('administracion.configuraciones.plan_beneficos.index',compact('search'));

        // return view('administracion.configuraciones.plan_beneficos.index',compact('id'));

    }
}
