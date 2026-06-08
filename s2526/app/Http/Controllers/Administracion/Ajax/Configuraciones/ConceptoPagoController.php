<?php

namespace App\Http\Controllers\Administracion\Ajax\Configuraciones;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Planpago;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\NomConceptoPago;
use Illuminate\Support\Facades\DB;

class ConceptoPagoController extends Controller
{
    public function show($id, Request $request)
    {
        $concepto_pago = ConceptoPago::findOrFail($id);
        $modal_id = 'modal_concepto_pago_'.$concepto_pago->id;
        $list_comment = ConceptoPago::COLUMN_COMMENTS;

        $cuentaxpagars = Cuentaxpagar::all()->where('type','GENERAL');

        $planpagos = Planpago::all();

        $list_comment = ConceptoPago::COLUMN_COMMENTS;

        $list_nom_concepto_pago = NomConceptoPago::select('name', 'id')
            ->orderby('name','asc')
            ->pluck('name', 'id');

        if($request->ajax()){
            return view('administracion.configuraciones.concepto_pagos.show.modal',
                compact('concepto_pago','modal_id','cuentaxpagars','planpagos','list_comment','list_nom_concepto_pago'));
        }
    }
    public function edit($id, Request $request)
    {
        $concepto_pago = ConceptoPago::findOrFail($id);
        $modal_id = 'modal_concepto_pago_'.$concepto_pago->id;
        $list_comment = ConceptoPago::COLUMN_COMMENTS;

        $cuentaxpagars = Cuentaxpagar::all()->where('type','GENERAL');

        $planpagos = Planpago::all();

        $list_nom_concepto_pago = NomConceptoPago::select('name', 'id')
            ->orderby('name','asc')
            ->pluck('name', 'id');
        $list_cuentaxpagar= DB::table('cuentaxpagars')
            ->select('cuentaxpagars.*',DB::raw('concat(planpagos.name, " || ",cuentaxpagars.name) as full_name'))
            ->join('planpagos', 'planpagos.id', '=', 'cuentaxpagars.planpago_id')
            ->orderby('planpagos.name','asc')
            ->where('cuentaxpagars.type','GENERAL')
            ->where('cuentaxpagars.status_active','true')
            ->pluck('full_name', 'id');

        if($request->ajax()){
            return view('administracion.configuraciones.concepto_pagos.modal.edit',
                compact('concepto_pago','modal_id','cuentaxpagars','list_cuentaxpagar','planpagos','list_comment','list_nom_concepto_pago'));
        }
    }
}
