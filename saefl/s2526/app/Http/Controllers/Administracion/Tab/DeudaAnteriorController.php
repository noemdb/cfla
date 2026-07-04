<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// use App\Http\Requests\Administracion\Estudiant\CreateRepresentantRequest;
// use App\Http\Requests\Administracion\Estudiant\UpdateRepresentantRequest;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\ConceptoPago;

class DeudaAnteriorController extends Controller
{
    public function crud(Request $request)
    {
        $cuentaxpagars =
            Cuentaxpagar::OrderBy('id')
                ->Where('cuentaxpagars.type','INDIVIDUAL')
                ->Where('cuentaxpagars.enable_late_payment',false)
                ->WhereNull('cuentaxpagars.quota_original_id')
                // ->groupBy('estudiant_id')
                ->get();

        //dd($cuentaxpagars);

        return view('administracion.deudas_anterior.crud',compact('cuentaxpagars'));
    }
    public function edit($id)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($id);
        $conceptopagos = $cuentaxpagar->conceptopagos;

        return view('administracion.deudas_anterior.edit',compact('cuentaxpagar','conceptopagos'));
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $conceptopago = ConceptoPago::findOrFail($id);
        $cuentaxpagar = $conceptopago->cuentaxpagar;
        $conceptopagos = $cuentaxpagar->conceptopagos;
        $conceptopago->fill($request->all());
        $conceptopago->save();
        Session::flash('operp_ok','Registro actualizado exitosamente');
        Session::flash('class_oper','success');
        // return view('administracion.deudas_anterior.edit',compact('cuentaxpagar','conceptopagos'));
        return redirect()->route('administracion.deudas_anterior.edit',compact('cuentaxpagar','conceptopagos'));
    }
}
