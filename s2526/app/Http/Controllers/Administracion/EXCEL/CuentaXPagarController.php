<?php

namespace App\Http\Controllers\Administracion\EXCEL;

use App\Exports\CuentaXPagarExport;
use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class CuentaXPagarController extends Controller
{
    public function representants_cuentaxpagars_pendeintes(CuentaXPagarExport $export)
    {
        $fecha = Carbon::now()->format('d-m-Y h.m.s');
        return Excel::download($export, 'ConceptosPendienteXRepresentantes_'.$fecha.'.xlsx');
    }
}
