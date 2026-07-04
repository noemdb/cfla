<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\Administracion\Configuracion\CreateGrupoEstableRequest;
use App\Http\Requests\Administracion\Configuracion\UpdateGrupoEstableRequest;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Pescolar\Asignatura;
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Escala;

class GrupoEstableController extends Controller
{
    public function index(Request $request)
    {
        $grupo_estables = GrupoEstable::all()->sortByDesc('created_at');
        $list_comment = GrupoEstable::COLUMN_COMMENTS;
        return view('administracion.configuraciones.grupo_estables.index',compact('grupo_estables','list_comment'));
    }

    public function create()
    {
        $list_comment = GrupoEstable::COLUMN_COMMENTS;
        return view('administracion.configuraciones.grupo_estables.create',compact('list_comment'));
    }
    public function store(CreateGrupoEstableRequest $request)
    {
        $grupo_estables = GrupoEstable::create($request->all());
        Session::flash('operp_ok','Registro guardado exitosamente');
        return redirect()->route('administracion.configuraciones.grupo_estables.create');
    }

    public function edit($id)
    {
        $grupo_estable = GrupoEstable::findOrFail($id);
        $list_escala = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_pestudio = Pestudio::active('true')->select('name', 'id',DB::raw("CONCAT(code,' - ',name) as fname"))->orderby('fname','asc')->pluck('fname', 'id');
        $list_comment = GrupoEstable::COLUMN_COMMENTS;
        return view('administracion.configuraciones.grupo_estables.edit',compact('grupo_estable','list_escala','list_pestudio','list_comment'));
    }
    public function update(UpdateGrupoEstableRequest $request, $id)
    {
        $grupo_estable = GrupoEstable::findOrFail($id);
        $grupo_estable->fill($request->all()); //dd($grupo_estable);
        $grupo_estable->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.grupo_estables.edit',$id);
    }

    public function destroy($id, Request $request)
    {
        $grupo_estable = GrupoEstable::findOrFail($id);
        $grupo_estable->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.grupo_estables.index');
    }
}
