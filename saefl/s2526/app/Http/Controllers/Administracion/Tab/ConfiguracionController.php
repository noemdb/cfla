<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Configuracion\CreateInstitucionRequest;
use App\Http\Requests\Administracion\Configuracion\UpdateInstitucionRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

use App\User;
use App\Models\sys\Profile;
use App\Models\sys\Rol;

use App\Models\sys\Task;
use App\Models\sys\Alert;
use App\Models\sys\Messege;

use App\Models\app\Institucion;

class ConfiguracionController extends Controller
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

    // public function institucion()
    // {
    //     $institucion = Institucion::findOrFail(session('institucion_id'));

        // $institucion = Institucion::OrderBy('created_at', 'desc')->first();

        // dd($institucion);

    //     return view('administracion.configuraciones.institucion.index',compact('institucion'));
    // }

    // public function InstitucionUpdate(UpdateInstitucionRequest $request, $id)
    // {
        // dd($id);
        // $institucion = Institucion::findOrFail($id);
        // $r = \Request::All();
        //dd($r);
        // $institucion->fill($request->all());
        // $institucion->save();
        // $messenge = trans('db_oper_result.update_ok');
        // Session::flash('operp_ok',$messenge);
        // Session::flash('class_oper','success');
        // return redirect()->route('administracion.configuraciones.institucion.');
    //     return view('administracion.configuraciones.institucion.index',compact('institucion'));
    // }
}
