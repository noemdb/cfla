<?php

namespace App\Http\Controllers\Leader\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Learning\Lesson;
use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Pescolar\Lapso;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningController extends Controller
{
    public $user,$autoridad,$list_comment_autoridad;
    
    public function __construct()
    {
        $this->middleware(['auth','is_leader', function ($request, $next) {
            $this->user = User::find(Auth::id());
            $this->autoridad = Autoridad::where('user_id',Auth::id())->first(); //dd(Auth::id(),$this->autoridad);
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            return $next($request);
        }]);
    }

    public function index()
    {
        $user=$this->user;
        $autoridad=$this->autoridad;
        $list_comment_autoridad=$this->list_comment_autoridad;  
        
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();

        $lessons = Lesson::all();
        
        $area_conocimientos = AreaConocimiento::where('leader_id',$user->id)->get();
        return view('leaders.learnings.index',compact('user','autoridad','list_comment_autoridad','area_conocimientos','lapsos','lapso_active','lessons'));
    }
}
