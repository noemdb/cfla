<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Planpago\CreateCuentaxpagarRequest;
use App\Http\Requests\Administracion\Planpago\UpdateCuentaxpagarRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

use App\Models\app\Pescolar;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\CreditoAFavor;
use App\Models\app\Planpago;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\RegistroPago;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\ConceptoCancelado;
use App\Models\app\Planpago\CreditoAplicado;
use App\Models\app\Estudiante\Descuento;
use App\Models\app\Planpago\DescuentoAplicado;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\NomConceptoPago;

use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\Tinscripcion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\DB;

class CuentaXPagarController extends Controller
{
    /**
* Create a new controller instance.
*
* @return void
*/
public function __construct()
{
    $this->middleware(['auth','is_admon']);
}

public function late_payment()
{
    $pestudios = Pestudio::active()->get(); //dd($pestudios);
    return view('administracion.configuraciones.cuentaxpagars.late_payment',compact('pestudios'));
}

/**
* Display a listing of the resource.
*
* @return \Illuminate\Http\Response
*/
public function index(Request $request)
{
    $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id : null ;
    $type = (!empty($request->type)) ? $request->type : null ;
    $finicial = (!empty($request->finicial)) ? $request->finicial : null;
    $ffinal = (!empty($request->ffinal)) ? $request->ffinal : null ;

    $planpagos = Planpago::where('status_active','true')->get();

    $cuentaxpagars = Cuentaxpagar::where('status_active','true')->OrderBy('created_at','desc');

    $cuentaxpagars = (isset($planpago_id)) ? $cuentaxpagars->where('planpago_id',$planpago_id) : $cuentaxpagars;
    $cuentaxpagars = (isset($type)) ? $cuentaxpagars->where('type',$type) : $cuentaxpagars;
    $cuentaxpagars = (isset($finicial)) ? $cuentaxpagars->wheredate('date_expiration','>=',$finicial) : $cuentaxpagars;
    $cuentaxpagars = (isset($ffinal)) ? $cuentaxpagars->wheredate('date_expiration','<=',$ffinal) : $cuentaxpagars;

    $cuentaxpagars = $cuentaxpagars->get();

    $list_planpago= Planpago::select('name', 'id')
        ->orderby('name','asc')
        ->where('status_active','true')
        ->pluck('name', 'id');

    return view('administracion.configuraciones.cuentaxpagars.index',
        compact('planpagos','cuentaxpagars','list_planpago','planpago_id','type','finicial','ffinal'));
}

public function crud()
{
    $cuentaxpagars = Cuentaxpagar::all()->where('type','GENERAL');
    $planpagos = Planpago::all();
    return view('administracion.configuraciones.cuentaxpagars.crud',compact('planpagos','cuentaxpagars'));
}

/**
* Show the form for creating a new resource.
*
* @return \Illuminate\Http\Response
*/
public function create(Request $request)
{
    $help_estudiant = (!empty($request->help_estudiant)) ? $request->help_estudiant : null ;

    $list_planpago= Planpago::select('name', 'id')->orderby('name','asc')->where('status_active','true')->pluck('name', 'id');
    $list_comment = Cuentaxpagar::COLUMN_COMMENTS;

    // $list_estudiant = collect();
    // $list_estudiant = Estudiant::list_pestudio_grado();
    $list_estudiant = Estudiant::list_active();

    return view('administracion.configuraciones.cuentaxpagars.create',compact('list_planpago','list_comment','list_estudiant','help_estudiant'));
}

/**
* Store a newly created resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @return \Illuminate\Http\Response
*/
public function store(CreateCuentaxpagarRequest $request)
{
    $planpagos = Planpago::all();
    $cuentaxpagar = Cuentaxpagar::create($request->all());
    Session::flash('operp_ok','Registro guardado exitosamente');

    return redirect()->route('administracion.configuraciones.cuentaxpagars.index',compact('planpagos'));
}

/**
* Display the specified resource.
*
* @param  \App\Models\app\Planpago\Cuentaxpagar  $cuentaXPagar
* @return \Illuminate\Http\Response
*/
public function show(Cuentaxpagar $cuentaXPagar)
{
//
}

/**
* Show the form for editing the specified resource.
*
* @param  \App\Models\app\Planpago\Cuentaxpagar  $cuentaXPagar
* @return \Illuminate\Http\Response
*/
public function edit($id,Request $request)
{
    $cuentaxpagar = Cuentaxpagar::findOrFail($id);
    $list_planpago= Planpago::select('name', 'id')
    ->orderby('name','asc')
    ->pluck('name', 'id');
    $list_comment = Cuentaxpagar::COLUMN_COMMENTS;

    $help_estudiant = (!empty($request->help_estudiant)) ? $request->help_estudiant : null ;
    // $list_estudiant = Estudiant::list_pestudio_grado();
    $list_estudiant = Estudiant::list();

    return view('administracion.configuraciones.cuentaxpagars.edit',compact('cuentaxpagar','list_estudiant','help_estudiant','list_planpago','list_comment'));
}

/**
* Update the specified resource in storage.
*
* @param  \Illuminate\Http\Request  $request
* @param  \App\Models\app\Planpago\Cuentaxpagar  $cuentaXPagar
* @return \Illuminate\Http\Response
*/
// public function update(Request $request, Cuentaxpagar $cuentaXPagar)
public function update(UpdateCuentaxpagarRequest $request, $id)
{
    //dd($request, $id);
    $cuentaxpagar = Cuentaxpagar::findOrFail($id);
    $planpagos = Planpago::all();
    $cuentaxpagar->fill($request->all()); //dd($cuentaxpagar);
    $cuentaxpagar->save();
    $messenge = trans('db_oper_result.update_ok');
    Session::flash('operp_ok',$messenge);
    Session::flash('class_oper','success');
    return redirect()->route('administracion.configuraciones.cuentaxpagars.index',compact('planpagos'));
}

/**
* Remove the specified resource from storage.
*
* @param  \App\Models\app\Planpago\Cuentaxpagar  $cuentaXPagar
* @return \Illuminate\Http\Response
*/
    public function destroy($id, Request $request)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($id);

        if ($cuentaxpagar->status_delete) {
            $cuentaxpagar->delete();
            $messenge = trans('db_oper_result.delete_ok');
            $operation= 'delete';
            if($request->ajax()){
                return response()->json([
                    "messenge"=>$messenge,
                    "operation"=>$operation,
                ]);
            }
            Session::flash('operp_ok',$messenge);
        }
        return redirect()->route('administracion.configuraciones.cuentaxpagars.index');
    }

    public function account_bad(Request $request)
    {
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id : null ;
        $type = (!empty($request->type)) ? $request->type : null ;
        $finicial = (!empty($request->finicial)) ? $request->finicial : null;
        $ffinal = (!empty($request->ffinal)) ? $request->ffinal : null ;

        $planpagos = Planpago::where('status_active','true')->get();

        $cuentaxpagars = Cuentaxpagar::where('status_bad','true')->OrderBy('created_at','desc');

        $cuentaxpagars = (isset($planpago_id)) ? $cuentaxpagars->where('planpago_id',$planpago_id) : $cuentaxpagars;
        $cuentaxpagars = (isset($type)) ? $cuentaxpagars->where('type',$type) : $cuentaxpagars;
        $cuentaxpagars = (isset($finicial)) ? $cuentaxpagars->wheredate('date_expiration','>=',$finicial) : $cuentaxpagars;
        $cuentaxpagars = (isset($ffinal)) ? $cuentaxpagars->wheredate('date_expiration','<=',$ffinal) : $cuentaxpagars;

        $cuentaxpagars = $cuentaxpagars->get();

        $list_planpago= Planpago::select('name', 'id')
            ->orderby('name','asc')
            ->where('status_active','true')
            ->pluck('name', 'id');

        return view('administracion.configuraciones.cuentaxpagars.account_bad',
            compact('planpagos','cuentaxpagars','list_planpago','planpago_id','type','finicial','ffinal'));
    }
}
