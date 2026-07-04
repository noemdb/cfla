<?php

namespace App\Http\Controllers\Administracion\EXCEL;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RepresentantExport;

class RepresentantController extends Controller
{
    public function list_saldo_dw_excel()
    {
        $fecha = Carbon::now()->format('d-m-Y h.m.s');
        return Excel::download(new RepresentantExport, 'SaldosxRepresentantes_'.$fecha.'.xlsx');
    }
}
