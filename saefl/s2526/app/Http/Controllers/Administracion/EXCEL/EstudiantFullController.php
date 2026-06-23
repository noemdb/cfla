<?php

namespace App\Http\Controllers\Administracion\EXCEL;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EstudiantFulltExport;

class EstudiantFullController extends Controller
{
    public function estudiantFull(){
        $fecha = Carbon::now()->format('d-m-Y h.m.s');
        return Excel::download(new EstudiantFulltExport, 'Estudiantes_'.$fecha.'.xlsx');
    }
}
