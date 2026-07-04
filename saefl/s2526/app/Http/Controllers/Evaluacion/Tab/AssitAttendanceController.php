<?php

namespace App\Http\Controllers\Evaluacion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Assistcontrol\AssitAttendance;
use App\Models\app\Assistcontrol\AssitSchedule;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Pestudio;
use App\Models\sys\Cargo;
use App\Models\sys\Rol;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssitAttendanceController extends Controller
{
    public $user,$autoridad,$list_comment_autoridad,$pestudios;

    public function __construct()
    {
        $this->middleware(['auth','is_evaluacion', function ($request, $next) {
            $user = User::find(Auth::id());
            $this->user = $user;
            $this->autoridad = Autoridad::where('user_id',$user->id)->first();
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            $this->pestudios = Pestudio::where('manager_id',$user->id)->Orderby('id','asc')->where('status_active','true')->get();
            return $next($request);
        }]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pestudios = $this->pestudios; //dd($pestudios);

        $finicial = (!empty($request->finicial)) ? $request->finicial:null;
        $ffinal = (!empty($request->ffinal)) ? $request->ffinal:null;
        $area = (!empty($request->area)) ? $request->area:null;
        $assit_schedule_id = (!empty($request->assit_schedule_id)) ? $request->assit_schedule_id:null;
        $cargo_id = (!empty($request->cargo_id)) ? $request->cargo_id:null;

        $dates = ($finicial && $ffinal) ? date_range($finicial,$ffinal) : collect(); //dd($dates);
        $user = new User;

        $list_comment = AssitAttendance::COLUMN_COMMENTS;
        $list_area = Rol::list_area();
        $list_cargos = Cargo::list_cargos();
        $list_assit_schedule = AssitSchedule::list_assit_schedule();

        $compact = ['pestudios','user','dates','finicial','ffinal','cargo_id','assit_schedule_id','list_assit_schedule','area','list_area','list_cargos','list_comment'];

        return view('evaluacions.assistcontrols.index', compact($compact));
    }
}
