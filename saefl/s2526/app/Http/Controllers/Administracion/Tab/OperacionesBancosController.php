<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\User;
use App\Models\sys\Profile;
use App\Models\sys\Rol;

use App\Models\sys\Task;
use App\Models\sys\Alert;
use App\Models\sys\Messege;

class OperacionesBancosController extends Controller
{
    public function dashboard()
    {
        $tasks = Task::OrderBy('created_at', 'desc')->limit(5)->get();
        $alerts = Alert::OrderBy('created_at', 'desc')->limit(5)->get();
        $messeges = Messege::OrderBy('created_at', 'desc')->limit(5)->get();
        $users = User::OrderBy('created_at', 'desc')->limit(5)->get();
        $profiles = Profile::OrderBy('created_at', 'desc')->limit(5)->get();
        $rols = Rol::OrderBy('created_at', 'desc')->limit(5)->get();

        //dd($tasks,$alerts,$messeges,$users,$profiles,$rols);

        return view('administracion.operaciones_bancos.home',compact('tasks','alerts','users','profiles','rols'));
    }

    public function index()
    {
        $users = User::OrderBy('created_at', 'desc')->limit(5)->get();
        //dd($tasks,$alerts,$messeges,$users,$profiles,$rols);

        return view('administracion.operaciones_bancos.index',compact('users'));
    }
}
