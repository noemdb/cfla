<?php

namespace App\Http\Controllers\Administracion\EXCEL;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EstudiantExport;

class EstudiantController extends Controller
{
    public function list_saldo_dw_excel(){
        $fecha = Carbon::now()->format('d-m-Y h.m.s');
        return Excel::download(new EstudiantExport, 'SaldosXEstudiantes_'.$fecha.'.xlsx');
    }
    // public function list_saldo_dw_excel(){

    //     return Excel::download(new EstudiantExport, 'Estudiant_Saldos.xlsx');
    // }
}
