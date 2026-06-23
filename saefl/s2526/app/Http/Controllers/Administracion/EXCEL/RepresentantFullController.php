<?php

namespace App\Http\Controllers\Administracion\EXCEL;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RepresentantFulltExport;

class RepresentantFullController extends Controller
{
    public function representantFull(){
        $fecha = Carbon::now()->format('d-m-Y h.m.s');
        return Excel::download(new RepresentantFulltExport, 'Representantes_'.$fecha.'.xlsx');
    }
}
