<?php

namespace App\Http\Controllers\Evaluacion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Learning\Lesson;
use App\Models\app\Pescolar\Pestudio;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
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

    public function index(Request $request)
    {
        $pestudios = $this->pestudios;
        $lessons = Lesson::getForManagerId($this->user->id); //dd($lessons);       
        return view('evaluacions.leaders.lessons.index', compact('pestudios','lessons'));
    }
}
