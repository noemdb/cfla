<?php

namespace App\Http\Controllers\Administracion\EXCEL;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegistroPagoExport;

class RegistroPagoController extends Controller
{
    public function list_pagos_dw_excel(RegistroPagoExport $export)
    {
        return Excel::download($export, 'Estudiante_Registro_de_Pago.xlsx');
    }

    public function export_list(RegistroPagoExport $export)
    {
        $fecha = Carbon::now()->format('d-m-Y h.m.s');
        return Excel::download($export, 'PagosRegistradosXRepresentantes_'.$fecha.'.xlsx');
    }
}
