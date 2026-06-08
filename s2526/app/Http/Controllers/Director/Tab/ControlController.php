<?php

namespace App\Http\Controllers\Director\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;

class ControlController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_director']);
    }
    public function performance()
    {
        $list_comment = Autoridad::COLUMN_COMMENTS;
        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get();
        $indicadores = Pestudio::getIndicadores(); //dd($indicadores);
        $lapsos = Lapso::all();
        $grados = Grado::all();
        $area_conocimientos = AreaConocimiento::all();
        $profesors = Profesor::asignado('true')->get(); //dd($profesors);

        return view('directors.performances.index',compact('pestudios','grados','area_conocimientos','profesors','list_comment','indicadores'));
    }
}
