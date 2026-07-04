<?php

namespace App\Http\Controllers\Administracion\EXCEL;

use App\Exports\PagoAdelantadoExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Planpago\Cuentaxpagar;
use Maatwebsite\Excel\Facades\Excel;

class PagoAdelantadoController extends Controller
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function export_list(PagoAdelantadoExport $export, Request $request)
    {
        $cuentaxpagar_id    = (!empty($request->cuentaxpagar_id)) ? $request->cuentaxpagar_id : null  ;
        $cuentaxpagar = Cuentaxpagar::findOrFail($cuentaxpagar_id);
        $name = 'registros_de_pagos_adelantados_'.$cuentaxpagar->name.'.xlsx';
        return Excel::download($export, $name);
    }
}
