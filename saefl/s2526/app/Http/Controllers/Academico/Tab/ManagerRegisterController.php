<?php

namespace App\Http\Controllers\Academico\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pestudio;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerRegisterController extends Controller
{
    public $user,$director,$list_comment;

    public function __construct()
    {
        $this->middleware(['auth','is_academico', function ($request, $next) {
            $this->director = Autoridad::where('user_id',Auth::user()->id)->first();
            $this->user = User::find(Auth::id());
            $this->list_comment = Autoridad::COLUMN_COMMENTS;
            return $next($request);
        }]);
    }
    
    public function index(Request $request)
    {
        $director = $this->director;
        $user = $this->user;

        $pestudios = Pestudio::Orderby('id','asc')->where('status_active','true')->get();
        $lapso = Lapso::current();

        $compact = ['user','director','pestudios','lapso'];
        return view('academicos.manager_registers.index',compact($compact));
    }
}
