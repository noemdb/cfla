<?php

namespace App\Http\Controllers\Inicial\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Lapso;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EifinalpController extends Controller
{
    public $user,$autoridad,$list_comment_autoridad;
    
    public function __construct()
    {
        $this->middleware(['auth','is_inicial', function ($request, $next) {
            $this->user = User::find(Auth::id());
            $this->autoridad = Autoridad::where('user_id',Auth::id())->first(); //dd(Auth::id(),$this->autoridad);
            $this->list_comment_autoridad = Autoridad::COLUMN_COMMENTS;
            return $next($request);
        }]);
    }

    public function home()
    {
        $user=$this->user; //dd($user);
        $autoridad=$this->autoridad;
        $list_comment_autoridad=$this->list_comment_autoridad;  
        
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();
        
        return view('inicials.eifinalks.index',compact('user','autoridad','list_comment_autoridad','lapsos','lapso_active'));
    }
}

/*
eiplanningwks
eiplanningbwks
eiprojectks
eispecialks
eievaluationks
eifinalks
*/