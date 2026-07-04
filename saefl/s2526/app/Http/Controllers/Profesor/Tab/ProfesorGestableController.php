<?php

namespace App\Http\Controllers\Profesor\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\ProfesorGestable;
use Illuminate\Support\Facades\Auth;

class ProfesorGestableController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $profesor = $this->profesor;
        /*******************request************************************************************/
        $pevaluacion_id = (!empty($request->pevaluacion_id)) ? $request->pevaluacion_id: null;
        $grupo_estable_id = (!empty($request->grupo_estable_id)) ? $request->grupo_estable_id: null;

        /*******************inicializaciones***************************************************/
        $pevaluacions = collect();
        $selected = ($pevaluacion_id) ? Pevaluacion::find($pevaluacion_id) : null ;
        $grupo_estable = ($grupo_estable_id) ? GrupoEstable::find($grupo_estable_id) : null ;
        $profesor_gestable = ($grupo_estable_id && $pevaluacion_id) ? ProfesorGestable::where('profesor_id',$profesor->id)->where('grupo_estable_id',$grupo_estable_id)->where('pevaluacion_id',$pevaluacion_id)->first() : null ;
        $modeSetUp = ($selected) ? true : false ;
        $evaluacions = ($selected) ? $selected->evaluacions : collect() ;
        $estudiants = ($profesor_gestable) ? $profesor_gestable->estudiants : collect() ;
        // $evaluacion_gestables = ($selected) ? $selected->getEvaluacionGestables($profesor->id) : collect() ;

        /*******************query builder***************************************************/
        $pevaluacions = Pevaluacion::select('pevaluacions.*')
        ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
        ->join('asignaturas','asignaturas.id','=','pensums.asignatura_id')
        ->join('profesor_gestables', 'pevaluacions.id', '=', 'profesor_gestables.pevaluacion_id')
        ->join('profesors','profesors.id','=','profesor_gestables.profesor_id')
        ->where('asignaturas.enable_grupo_estable','true')
        ->where('profesors.id',$profesor->id)
        ->OrderBy('created_at','desc')
        ->groupBy('pevaluacions.id');

        /*******************get collections****************************/
        $pevaluacions = $pevaluacions->get();


        $minimo = ($selected) ? $selected->escala->minimo : null;
        $maximo = ($selected) ? $selected->escala->maximo : null;
        $list_nota['-1'] = null; $list_nota[0] = 'I';
        for ($i=$minimo; $i <= $maximo ; $i++) { $list_nota[$i] = $i;}

        $compact = [
            'profesor','grupo_estable','profesor_gestable',
            'pevaluacions','estudiants',
            'modeSetUp','selected','evaluacions',
            'pevaluacion_id','grupo_estable_id',
            'list_nota',
        ];

        return view('profesors.profesor_gestables.index', compact($compact));
    }
}
