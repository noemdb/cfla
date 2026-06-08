<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
// /home/nuser/code/s2021/app/Http/Requests/Administracion/Planpago/CreateConceptoPagoRequest.php
use App\Http\Requests\Administracion\Planpago\CreateConceptoPagoRequest;
use App\Http\Requests\Administracion\Planpago\UpdateConceptoPagoRequest;

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
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\DB;

class ConceptoPagoController extends Controller
{

    public function __construct()
    {
    // $this->middleware(['auth','is_admon']);
    $this->middleware(['auth','is_admon']);
    }

    public function crud()
    {
        $concepto_pagos = ConceptoPago::select('concepto_pagos.*')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->where('cuentaxpagars.type','GENERAL')
            ->get();
        $cuentaxpagars = Cuentaxpagar::all()->where('type','GENERAL');
        $planpagos = Planpago::all();
        return view('administracion.configuraciones.concepto_pagos.crud',compact('planpagos','cuentaxpagars','concepto_pagos'));
    }

    public function index(Request $request)
    {
        $planpago_id = (!empty($request->planpago_id)) ? $request->planpago_id : null ;
        $type = (!empty($request->type)) ? $request->type : null ;
        $finicial = (!empty($request->finicial)) ? $request->finicial : null;
        $ffinal = (!empty($request->ffinal)) ? $request->ffinal : null ;

        $planpagos = Planpago::where('status_active','true')->get();

        $concepto_pagos = ConceptoPago::select('concepto_pagos.*')
            ->join('cuentaxpagars', 'cuentaxpagars.id', '=', 'concepto_pagos.cuentaxpagar_id')
            ->OrderBy('concepto_pagos.created_at','desc')
            ->where('concepto_pagos.status_active','true')
            ->where('cuentaxpagars.status_active','true');

        $concepto_pagos = (isset($planpago_id)) ? $concepto_pagos->where('cuentaxpagars.planpago_id',$planpago_id) : $concepto_pagos;
        $concepto_pagos = (isset($type)) ? $concepto_pagos->where('cuentaxpagars.type',$type) : $concepto_pagos;
        $concepto_pagos = (isset($finicial)) ? $concepto_pagos->wheredate('cuentaxpagars.date_expiration','>=',$finicial) : $concepto_pagos;
        $concepto_pagos = (isset($ffinal)) ? $concepto_pagos->wheredate('cuentaxpagars.date_expiration','<=',$ffinal) : $concepto_pagos;

        $concepto_pagos = $concepto_pagos->get();

        $list_planpago= Planpago::select('name', 'id')
            ->orderby('name','asc')
            ->where('status_active','true')
            ->pluck('name', 'id');

        return view('administracion.configuraciones.concepto_pagos.index',
            compact('planpagos','concepto_pagos','list_planpago','planpago_id','type','finicial','ffinal'));
    }

    public function create_concept($id)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($id);

        $list_nom_concepto_pago = NomConceptoPago::select('name', 'id')
            ->orderby('name','asc')
            ->pluck('name', 'id');
        $list_comment = ConceptoPago::COLUMN_COMMENTS;

        return view('administracion.configuraciones.concepto_pagos.create.concept',compact('list_nom_concepto_pago','list_comment','cuentaxpagar'));
    }


    public function create()
    {
        // $list_cuentaxpagar= DB::table('cuentaxpagars')
        //     ->select('cuentaxpagars.*',DB::raw('concat(planpagos.name, " || ",cuentaxpagars.name) as full_name'))
        //     ->join('planpagos', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')
        //     ->orderby('planpagos.name','asc')
        //     ->where('cuentaxpagars.type','GENERAL')
        //     ->where('cuentaxpagars.status_active','true')
        //     ->pluck('full_name', 'id');
        $list_cuentaxpagar = collect();
        $list_nom_concepto_pago = NomConceptoPago::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_comment = ConceptoPago::COLUMN_COMMENTS;
        $cuentaxpagars = Cuentaxpagar::all()->where('type','GENERAL')->sortByDesc('created_at');

        return view('administracion.configuraciones.concepto_pagos.create',compact('cuentaxpagars','list_cuentaxpagar','list_nom_concepto_pago','list_comment'));
    }

    public function store(CreateConceptoPagoRequest $request)
    {
        $concepto_pago = ConceptoPago::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.concepto_pagos.index');
    }

    public function store_from_cuentaxpagar(CreateConceptoPagoRequest $request)
    {
        $id = $request->all()['cuentaxpagar_id'];

        $conceptopago = ConceptoPago::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);

        return redirect()->route('administracion.configuraciones.cuentaxpagars.index');
    }

    public function show(ConceptoPago $conceptoPago)
    {
        //
    }

    public function edit($id)
    {
        $conceptopago = ConceptoPago::findOrFail($id);

        $cuentaxpagars = Cuentaxpagar::all()->where('type','GENERAL');
        $planpagos = Planpago::all();

        $list_comment = ConceptoPago::COLUMN_COMMENTS;

        $list_nom_concepto_pago = NomConceptoPago::select('name', 'id')
            ->orderby('name','asc')
            ->pluck('name', 'id');

        return view('administracion.configuraciones.concepto_pagos.edit',compact('conceptopago','cuentaxpagars','planpagos','list_nom_concepto_pago','list_comment'));
    }

    public function update(UpdateConceptoPagoRequest $request, $id)
    {
        $concepto_pagos = ConceptoPago::findOrFail($id);
        //dd($request->all());
        $concepto_pagos->fill($request->all());
        $concepto_pagos->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        return redirect()->route('administracion.configuraciones.concepto_pagos.index');
    }

    public function destroy($id, Request $request)
    {
        $conceptopago = ConceptoPago::findOrFail($id);

        if ($conceptopago->status_delete) {
            $conceptopago->delete();
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
        $messenge = trans('db_oper_result.user_destroy_not_ok');
        Session::flash('no_operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.concepto_pagos.index');
    }
}
