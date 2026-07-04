<?php

namespace App\Http\Controllers\Administracion\Ajax\Configuraciones;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Planpago;
use App\Models\app\Planpago\ConceptoPago;
use App\Models\app\Planpago\Cuentaxpagar;
use App\Models\app\Planpago\NomConceptoPago;

class CuentaXPagarController extends Controller
{
    public function show($id, Request $request)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($id);
        $modal_id = 'modal_cuentaxpagar_'.$cuentaxpagar->id;

        $list_planpago= Planpago::select('name', 'id')
            ->orderby('name','asc')
            ->pluck('name', 'id');
        $list_comment = Cuentaxpagar::COLUMN_COMMENTS;

        $help_estudiant = (!empty($request->help_estudiant)) ? $request->help_estudiant : null ;
        $list_estudiant = Estudiant::list_pestudio_grado();

        if($request->ajax()){
            return view('administracion.configuraciones.cuentaxpagars.show.modal', compact('cuentaxpagar','list_estudiant','help_estudiant','modal_id','list_planpago','list_comment'));
        }
    }
    public function edit($id, Request $request)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($id);
        $modal_id = 'modal_cuentaxpagar_'.$cuentaxpagar->id;

        $list_planpago= Planpago::select('name', 'id')
            ->orderby('name','asc')
            ->pluck('name', 'id');
        $list_comment = Cuentaxpagar::COLUMN_COMMENTS;

        $help_estudiant = (!empty($request->help_estudiant)) ? $request->help_estudiant : null ;
        // $list_estudiant = Estudiant::list_pestudio_grado();
        $list_estudiant = Estudiant::list();

        if($request->ajax()){
            return view('administracion.configuraciones.cuentaxpagars.modal.edit', compact('cuentaxpagar','help_estudiant','list_estudiant','modal_id','list_planpago','list_comment'));
        }
    }
    public function asignar($id, Request $request)
    {
        $cuentaxpagar = Cuentaxpagar::findOrFail($id);
        $modal_id = 'modal_cuentaxpagar_'.$cuentaxpagar->id;
        $list_cuentaxpagar = collect();

        $list_nom_concepto_pago = NomConceptoPago::select('name', 'id')
            ->orderby('name','asc')
            ->pluck('name', 'id');
        $list_comment = ConceptoPago::COLUMN_COMMENTS;

        $help_estudiant = (!empty($request->help_estudiant)) ? $request->help_estudiant : null ;
        $list_estudiant = Estudiant::list_pestudio_grado();

        if($request->ajax()){
            return view('administracion.configuraciones.cuentaxpagars.modal.asignar_concepto', compact('cuentaxpagar','list_cuentaxpagar','list_estudiant','help_estudiant','modal_id','list_comment','list_nom_concepto_pago'));
        }
    }
}
