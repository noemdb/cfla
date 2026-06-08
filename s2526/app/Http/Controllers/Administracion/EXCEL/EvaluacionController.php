<?php

namespace App\Http\Controllers\Administracion\EXCEL;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EvaluacionExport;

class EvaluacionController extends Controller
{
    public function list_dw_excel()
    {
        $fecha = Carbon::now()->format('d-m-Y h.m.s');
        return Excel::download(new EvaluacionExport, 'Evaluaciones_'.$fecha.'.xlsx');
    }
}
