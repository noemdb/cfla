<?php

namespace App\Http\Controllers\Inicial\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Inicial\Eiplanningbwk;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Profesor;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EiplanningbwkController extends Controller
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

    public function index()
    {
        $user=$this->user; //dd($user);
        $autoridad=$this->autoridad;
        $list_comment_autoridad=$this->list_comment_autoridad;  
        
        $lapsos = Lapso::all();
        $lapso_active = Lapso::current();
        
        return view('inicials.eiplanningbwks.index',compact('user','autoridad','list_comment_autoridad','lapsos','lapso_active'));
    }

    public function format($id, Request $request)
    {
        // dd($request->all());
        $eiplanningbwk = Eiplanningbwk::findOrFail($id); //dd($eiplanningbwk->eiplanningbwsummaries);
        $profesor = Profesor::where('user_id',Auth::user()->id)->first();
        $institucion = Institucion::OrderBy('created_at','DESC')->first(); 
        $fecha = Carbon::now()->format('d-m-Y h:m A');

        return view('livewire.inicial.formats.eiplanningbwk.index',compact('profesor','eiplanningbwk','institucion','fecha'));
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