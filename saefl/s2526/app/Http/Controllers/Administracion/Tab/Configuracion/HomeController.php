<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\sys\Profile;
use App\Models\sys\Rol;

use App\Models\sys\Task;
use App\Models\sys\Alert;
use App\Models\sys\Messege;

use App\Models\app\Institucion;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $tasks = Task::OrderBy('created_at', 'desc')->limit(5)->get();
        $alerts = Alert::OrderBy('created_at', 'desc')->limit(5)->get();
        $messeges = Messege::OrderBy('created_at', 'desc')->limit(5)->get();
        $users = User::OrderBy('created_at', 'desc')->limit(5)->get();
        $profiles = Profile::OrderBy('created_at', 'desc')->limit(5)->get();
        $rols = Rol::OrderBy('created_at', 'desc')->limit(5)->get();

        //dd($tasks,$alerts,$messeges,$users,$profiles,$rols);

        return view('administracion.configuraciones.home',compact('tasks','alerts','users','profiles','rols'));
    }
}
