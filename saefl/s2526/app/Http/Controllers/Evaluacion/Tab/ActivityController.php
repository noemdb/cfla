<?php

namespace App\Http\Controllers\Evaluacion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pestudio;
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
    public $user,$autoridad,$list_comment_autoridad,$pestudios;

    public function __construct()
    {
        $this->middleware(['auth','is_evaluacion', function ($request, $next) {
            $user = User::find(Auth::id());
            $this->user = $user;
            $this->autoridad = Autoridad::where('user_id',$user->id)->first();
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            $this->pestudios = Pestudio::getPestudios($user->id); //dd($this->pestudios);
            return $next($request);
        }]);
    }

    public function index(Request $request)
    {
        $pestudios = $this->pestudios;
        /*******************request****************************/
        $profesor_id = (!empty($request->profesor_id)) ? $request->profesor_id : null ;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;

        /*******************query****************************/

        $pevaluacions = Pevaluacion::select('pevaluacions.*')
            ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
            ->OrderBy('created_at','desc');

        $pevaluacions = ($profesor_id) ? $pevaluacions->where('pevaluacions.profesor_id',$profesor_id) : $pevaluacions ;
        $pevaluacions = ($grado_id) ? $pevaluacions->where('pensums.grado_id',$grado_id) : $pevaluacions ;
        $pevaluacions = ($seccion_id) ? $pevaluacions->where('pevaluacions.seccion_id',$seccion_id) : $pevaluacions ;
        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;
        $pevaluacions = $pevaluacions->get(); // dd($pevaluacions);

        /*******************list****************************/
        $list_grado = Grado::list_pestudio_grado_manage($this->user->id);
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $autoridad = $this->autoridad;

        return view('evaluacions.activities.index',compact('pestudios','autoridad','pevaluacions','profesor_id','grado_id','list_grado','seccion_id','list_seccion','lapso_id','list_lapso'));
    }

    public function format($id)
    {
        /*******************query****************************/
        $pevaluacion = Pevaluacion::findOrFail($id);
        $activities = Activity::where('pevaluacion_id',$id)->orderBy('finicial','asc')->get();
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('evaluacions.activities.format',compact('pevaluacion','activities','institucion','fecha'));
    }

    public function resume($id)
    {
        /*******************query****************************/
        $pevaluacion = Pevaluacion::findOrFail($id);
        $activities = Activity::where('pevaluacion_id',$id)->whereNotNull('description')->orderBy('finicial','asc')->get();
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('evaluacions.activities.resume',compact('pevaluacion','activities','institucion','fecha'));
    }

    public function formatForGrado($grado_id, $seccion_id, Request $request)
    {
        /*******************query****************************/
        $grado = Grado::findOrFail($grado_id);
        $seccion = Seccion::findOrFail($seccion_id);
        $pensums = $grado->pensums;
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $fecha = Carbon::now()->format('d-m-Y h:m A');
        return view('evaluacions.activities.formatGrado',compact('grado','seccion','pensums','institucion','fecha'));
    }
}
