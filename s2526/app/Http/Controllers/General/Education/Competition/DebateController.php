<?php

namespace App\Http\Controllers\General\Education\Competition;

use App\Http\Controllers\Controller;
use App\Models\app\Educational\Debate;
use App\Models\app\Educational\DebateCompetition;
use App\Models\app\Institucion;
use App\Models\app\Institucion\Autoridad;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

class DebateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_profesor']);
    }

    public function index($tokena,$tokenb)
    {
        $competition = DebateCompetition::where('token',$tokena)->orderBy('created_at','desc')->first(); //dd($competition);
        $debate = Debate::where('token',$tokenb)->orderBy('created_at','desc')->first(); //dd($competition,$debate);
        if (empty($competition) || empty($debate)) abort(403, 'No tiene acceso');
        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('COORDINADOR DE EVALUACIÓN'); //dd($autoridad2);
        $toDay = Date::now();
        return view('general.educationals.competitions.debates.index',compact('competition','debate','institucion','autoridad1','autoridad2','toDay'));
    }

    public function interactive($token)
    {
        $user = User::findOrFail(Auth::id());
        $competition = DebateCompetition::where('token',$token)->where('user_id',$user->id)->orderBy('created_at','desc')->first();
        if (empty($competition)) abort(403, 'No tiene acceso');

        $institucion = Institucion::OrderBy('created_at','DESC')->first();
        $autoridad1 = Autoridad::getAuthority('DIRECTORA');
        $autoridad2 = Autoridad::getAuthority('COORDINADOR DE EVALUACIÓN'); //dd($autoridad2);
        $toDay = Date::now();
        return view('general.educationals.competitions.interactive.index',compact('competition','institucion','autoridad1','autoridad2','toDay'));
        // /home/nuser/code/s2425/resources/views/general/educationals/interactive/index.blade.php
    }
}
