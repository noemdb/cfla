<?php

namespace App\Http\Controllers\Administracion\EXCEL;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BoletinExport;

use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Grado;

class BoletinController extends Controller
{
    // public function list_dw_excel()
    // {
    //     $fecha = Carbon::now()->format('d-m-Y h.m.s');
    //     return Excel::download(new BoletinExport, 'Notas_'.$fecha.'.xlsx');
    // }

    public function list_dw_excel(BoletinExport $export)
    {
        $request = (!empty($export->request)) ? $export->request : null ;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id : null ;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;

        // dd($request->request->lapso_id);

        $lapso = Lapso::find($lapso_id );
        $grado = Grado::find($grado_id );
        $lapso_name = ($lapso) ? $lapso->name : null ;
        $grado_name = ($grado) ? $grado->name : null ;

        $fecha = Carbon::now()->format('d-m-Y h.m.s');
        return Excel::download($export, 'Notas_'.$lapso_name.' - '.$grado_name.'_'.$fecha.'.xlsx');
    }
}
