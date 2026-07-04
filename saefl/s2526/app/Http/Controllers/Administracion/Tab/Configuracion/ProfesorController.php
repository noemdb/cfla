<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\Administracion\Configuracion\CreateProfesorRequest;
use App\Http\Requests\Administracion\Configuracion\UpdateProfesorRequest;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Pescolar\Profesor;
use App\Models\app\Estudiante\TypeCi;
use App\Models\app\Pescolar\Lapso;
use App\User;

class ProfesorController extends Controller
{
            /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','is_control']);
    }

    public function index(Request $request)
    {
        $is_gestable            = (!empty($request->is_gestable)) ? $request->is_gestable : null  ; 
        $search                   = (!empty($request->search)) ? $request->search : null  ; 

        // $profesors = Profesor::all()->sortByDesc('created_at');
        $profesors = Profesor::select('profesors.*')->orderBy('profesors.created_at');

        $profesors = ($search) ? $profesors->where('profesors.name','like','%'.$search.'%')->orWhere('profesors.lastname','like','%'.$search.'%') : $profesors ;

        if ($is_gestable) {
            $profesors = $profesors->join('profesor_gestables','profesors.id','=','profesor_gestables.profesor_id')
            ->whereNotNull('profesor_gestables.profesor_id');
        }

        $profesors = $profesors->groupBy('profesors.id')->get();

        $lapsos = Lapso::all();
        $list_comment = Profesor::COLUMN_COMMENTS;
        return view('administracion.configuraciones.profesors.index',compact('profesors','search','is_gestable','lapsos','list_comment'));
    }

    public function create()
    {
        $profesors = Profesor::all()->sortByDesc('created_at');
        $profesor = new Profesor;
        $list_comment = Profesor::COLUMN_COMMENTS;
        $user_list = User::orderby('users.username','asc')->pluck('users.username', 'users.id');
        return view('administracion.configuraciones.profesors.create',compact('profesors','profesor','list_comment','user_list'));
    }

    public function store(CreateProfesorRequest $request)
    {
        $profesor = Profesor::create($request->all());

        $create_user = ($profesor) ? $profesor->create_user : null;

        Session::flash('operp_ok','Registro guardado exitosamente');
        return redirect()->route('administracion.configuraciones.profesors.index');
    }

    public function edit($id)
    {
        $profesors = Profesor::all()->sortByDesc('created_at');
        $profesor = Profesor::findOrFail($id);
        $list_comment = Profesor::COLUMN_COMMENTS;
        $user_list = User::orderby('users.username','asc')->pluck('users.username', 'users.id');
        return view('administracion.configuraciones.profesors.edit',compact('profesors','profesor','list_comment','user_list'));
    }

    public function update(UpdateProfesorRequest $request, $id)
    {
        $profesor = Profesor::findOrFail($id);
        $profesor->fill($request->all());
        $profesor->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.profesors.edit',$id);
    }

    public function destroy($id, Request $request)
    {
        $profesor = Profesor::findOrFail($id);
        $profesor->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.profesors.index');
    }
}
