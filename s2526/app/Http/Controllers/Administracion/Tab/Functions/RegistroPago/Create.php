<?php

namespace App\Http\Controllers\Administracion\Tab\Functions\RegistroPago;

use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\MetodoPago;

trait Create {



    public function create_representant_exchange(Request $request)
    {
        $id = $request->id;

        $fecha = Carbon::now()->format('Y-m-d');

        $representant       = Representant::findOrFail($id); //dd($representant);

        $estudiants     = Estudiant::where('representant_id',$representant->id)->active('true')->get();

        $method_pay_list    = MetodoPago::list_metodo_pago();

        $banco_list         = Banco::banco_list();

        $representant_id = $representant->id;
        $help_representante = $representant->ci_representant;
        $list_representant = Representant::list_representant();
        $list_divisas = ['1'=>'1','2'=>'2','5'=>'5','10'=>'10','20'=>'20','50'=>'50','100'=>'100'];


        return view('administracion.registropagos.create_representant_exchange',
        compact('representant','estudiants','method_pay_list','banco_list','representant_id','help_representante','list_representant','list_divisas','fecha'));
    }

    public function parcial_create(Request $request)
    {
        $estudiant       = Estudiant::findOrFail($request->id);

        $method_pay_list    = MetodoPago::select('name','id')->orderby('name','asc')->pluck('name', 'id');

        $banco_list         = Banco::banco_list();

        return view('administracion.registropagos.create_parcial',compact('estudiant','method_pay_list','banco_list'));
    }

    public function representant_create(Request $request)
    {
        $id = $request->id;

        $representant       = Representant::findOrFail($id);

        $estudiants     = Estudiant::where('representant_id',$representant->id)->active('true')->get();

        $method_pay_list    = MetodoPago::list_metodo_pago();

        $banco_list         = Banco::banco_list();

        // $banco_list         = Banco::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return view('administracion.registropagos.create_representant',compact('representant','estudiants','method_pay_list','banco_list'));
    }

    public function create($id,$ctaid)
    {
        $estudiant          = Estudiant::findOrFail($id);

        $estudiant_list     = Estudiant::select('id','name')
            // ->where('status_active','true')
            ->active('true')
            ->pluck('name', 'id');

            // dd($estudiant_list);

        $cuentaxpagar       = Cuentaxpagar::findOrFail($ctaid);

        $cuentaxpagar_list  =
        Cuentaxpagar::select('cuentaxpagars.name as name', 'cuentaxpagars.id as id')
            ->join('administrativas', 'administrativas.planpago_id', '=', 'cuentaxpagars.planpago_id')
            ->Where('administrativas.estudiant_id',$estudiant->id)
            ->Where('cuentaxpagars.type','GENERAL')
            ->orWhere('cuentaxpagars.estudiant_id',$estudiant->id)
            ->pluck('name', 'id');

        $method_pay_list    = MetodoPago::select('name','id')->orderby('name','asc')->pluck('name', 'id');

        $banco_list         = Banco::banco_list();

        if (isset($ctaid)) {

            $conceptos_pagados = $cuentaxpagar->ConceptosPagados($estudiant->id);

            $conceptos_x_pagar = $cuentaxpagar->ConceptosXPagar($estudiant->id);

            return view('administracion.registropagos.create',compact('estudiant','estudiant_list','cuentaxpagar','cuentaxpagar_list','method_pay_list','banco_list','conceptos_pagados','conceptos_x_pagar'));

        } else {
            return view('administracion.registropagos.create',compact('estudiant','estudiant_list','cuentaxpagar','cuentaxpagar_list','method_pay_list','banco_list'));
        }

    }

}
