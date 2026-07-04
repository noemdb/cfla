<?php

namespace App\Http\Controllers\Administracion\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Abono;

class FillDataTableController extends Controller
{
    public function DataTableAbonos()
    {
        
        // $abonos = Abono::select('')->toArray();
        $abonos = Estudiant::active('true')->select('estudiants.name as Estudiante','estudiants.ci_estudiant as Cédula')
                ->join('inscripcions','inscripcions.estudiant_id','=','estudiants.id')->get()->toArray();

        $results = array(
        "sEcho" => 1,
        "iTotalRecords" => count($abonos),
        "iTotalDisplayRecords" => count($abonos),
        "aaData"=>$abonos);
                
        return json_encode($results);         
        
    }
}
