<?php

namespace App\Http\Controllers\Leader\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Activity;
use App\Models\app\Profesor\Pevaluacion;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public $user,$autoridad,$list_comment_autoridad,$area_active;
    
    public function __construct()
    {
        $this->middleware(['auth','is_leader', function ($request, $next) {
            $this->user = User::find(Auth::id());
            $this->autoridad = Autoridad::where('user_id',Auth::id())->first(); //dd(Auth::id(),$this->autoridad);
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            $this->area_active = "CONOCIMIENTO";
            return $next($request);
        }]);
    }

    public function index(Request $request)
    {
        $user=$this->user;
        $autoridad=$this->autoridad;
        $list_comment_autoridad=$this->list_comment_autoridad; 
        $area_active=$this->area_active; //dd($area_active);

        /*******************request****************************/
        // $profesor = $this->profesor;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;

        /*******************query****************************/
        $pevaluacions =
        Pevaluacion::select('pevaluacions.*')
        ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
        ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
        ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
        ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

        ->where('area_conocimientos.leader_id',$user->id)
        ->wherenull('pensums.deleted_at')
        ->wherenull('pevaluacions.deleted_at')
        ->groupBy('asignaturas.id');

        /*******************if()?****************************/
        $pevaluacions = ($grado_id) ? $pevaluacions->where('pensums.grado_id',$grado_id) : $pevaluacions ;
        $pevaluacions = ($seccion_id) ? $pevaluacions->where('pevaluacions.seccion_id',$seccion_id) : $pevaluacions ;
        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;

        /*******************get collections****************************/
        $pevaluacions = $pevaluacions->get(); //dd($pevaluacions);

        /*******************list****************************/
        // $list_grado = Profesor::list_grado($profesor->id);
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();        
        $area_conocimientos = AreaConocimiento::where('leader_id',$user->id)->get();

        $compact = [
            'pevaluacions',
            'user','autoridad','list_comment_autoridad','area_conocimientos','lapsos','lapso_active','area_active'
        ];

        return view('leaders.activities.index',compact($compact));
    }

    public function format($id)
    {
        /*******************query****************************/
        $pevaluacion = Pevaluacion::findOrFail($id);
        //$activities = Activity::where('pevaluacion_id',$id)->get();
        $activities = Activity::where('pevaluacion_id',$id)->orderBy('finicial','asc')->get();
        $institucion = Institucion::OrderBy('created_at','DESC')->first();        
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('leaders.activities.format',compact('pevaluacion','activities','institucion','fecha'));
    }

    public function resume($id)
    {
        /*******************query****************************/
        $pevaluacion = Pevaluacion::findOrFail($id); //dd($pevaluacion);
        //$activities = Activity::where('pevaluacion_id',$id)->whereNotNull('description')->get();
        $activities = Activity::where('pevaluacion_id',$id)->whereNotNull('description')->orderBy('finicial','asc')->get();
        $institucion = Institucion::OrderBy('created_at','DESC')->first();        
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('leaders.activities.resume',compact('pevaluacion','activities','institucion','fecha'));
    }
}
