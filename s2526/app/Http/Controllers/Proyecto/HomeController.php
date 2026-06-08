<?php

namespace App\Http\Controllers\Proyecto;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public $user,$autoridad,$list_comment_autoridad;
    
    public function __construct()
    {
        $this->middleware(['auth','is_proyecto', function ($request, $next) {
            $this->user = User::find(Auth::id());
            $this->autoridad = Autoridad::where('user_id',Auth::id())->first();
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            return $next($request);
        }]);
    }

    public function home()
    {
        $user=$this->user;
        $autoridad=$this->autoridad;
        $list_comment_autoridad=$this->list_comment_autoridad;        
        return view('proyectos.home',compact('user','autoridad','list_comment_autoridad'));
    }
}
