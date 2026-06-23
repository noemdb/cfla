<?php

namespace App\Http\Controllers\Administracion\EXCEL;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbonoExport;

class AbonoController extends Controller
{
    public function list_abono_dw_excel(AbonoExport $export)
    {
        $fecha = Carbon::now()->format('d-m-Y h.m.s');
        return Excel::download($export, 'Representante_Abonos_'.$fecha.'.xlsx');
    }
}
