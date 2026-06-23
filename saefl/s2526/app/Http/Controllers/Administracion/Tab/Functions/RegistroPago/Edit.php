<?php

namespace App\Http\Controllers\Administracion\Tab\Functions\RegistroPago;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Http\Requests\Administracion\Planpago\UpdateRegistroPagoRequest;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Ingreso;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Institucion\Banco;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\MetodoPago;
use App\Models\app\Planpago\Pago;
use App\Models\app\Planpago\RegistroPago;


trait Edit {
    public function edit($id)
    {

        $registropago       = RegistroPago::findOrFail($id);

        $estudiant          = $registropago->estudiant;

        $cuentaxpagar       = $registropago->cuentaxpagar;

        $pago            = Pago::where('registro_pago_id',$registropago->id)->first();

        $ingreso        = $pago->ingreso;

        $cuentaxpagar_list  = Cuentaxpagar::select('name', 'id')->where('id',$cuentaxpagar->id)->pluck('name', 'id');

        $method_pay_list    = MetodoPago::select('name','id')->orderby('name','asc')->pluck('name', 'id');

        $banco_list         = Banco::banco_list();

        $conceptos_pagados = $cuentaxpagar->ConceptosPagados($estudiant->id);

        $conceptos_x_pagar = $cuentaxpagar->ConceptosXPagar($estudiant->id);

        return view('administracion.registropagos.edit',compact('registropago','ingreso','estudiant','cuentaxpagar','cuentaxpagar_list','method_pay_list','banco_list','conceptos_pagados','conceptos_x_pagar'));

    }

    public function update(UpdateRegistroPagoRequest $request, $id)
    {
        $registropago       = RegistroPago::findOrFail($id);

        $ingreso            = Ingreso::where('registro_pago_id',$registropago->id);

        $estudiant          = $registropago->estudiant;

        dd($request->all(),$registropago,$ingreso->get());

        $ingreso->update([
            'method_pay_id' => $request->method_pay_id,
            'banco_id' => $request->banco_id,
            'number_i_pay' =>$request->number_i_pay,
            'ingreso_observations' => $request->ingreso_observations
            ]);

        Session::flash('operp_ok','Registros actualizados exitosamente');

        $search = $estudiant->ci_estudiant;

        return redirect()->route('administracion.registropagos.index',compact('search'));
    }

}
