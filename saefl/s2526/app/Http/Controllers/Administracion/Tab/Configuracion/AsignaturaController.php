<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\Administracion\Configuracion\CreateAsignaturaRequest;
use App\Http\Requests\Administracion\Configuracion\UpdateAsignaturaRequest;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Pescolar\Asignatura;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Escala;

class AsignaturaController extends Controller
{
    public function index(Request $request)
    {
        $asignaturas = Asignatura::select('asignaturas.*')->orderBy('created_at','DESC')->get();
        $list_comment = Asignatura::COLUMN_COMMENTS;
        return view('administracion.configuraciones.asignaturas.index',compact('asignaturas','list_comment'));
    }

    public function create()
    {
        $asignatura = new Asignatura;
        $list_escala = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_pestudio = Pestudio::active('true')->select('name', 'id',DB::raw("CONCAT(code,' - ',name) as fname"))->orderby('fname','asc')->pluck('fname', 'id');
        $list_comment = Asignatura::COLUMN_COMMENTS;
        return view('administracion.configuraciones.asignaturas.create',compact('asignatura','list_escala','list_pestudio','list_comment'));
    }
    public function store(CreateAsignaturaRequest $request)
    {
        $asignatura = Asignatura::create($request->all());
        Session::flash('operp_ok','Registro guardado exitosamente');
        return redirect()->route('administracion.configuraciones.asignaturas.index');
    }

    public function edit($id)
    {
        $asignatura = Asignatura::findOrFail($id);
        $list_escala = Escala::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_pestudio = Pestudio::active('true')->select('name', 'id',DB::raw("CONCAT(code,' - ',name) as fname"))->orderby('fname','asc')->pluck('fname', 'id');
        $list_comment = Asignatura::COLUMN_COMMENTS;
        return view('administracion.configuraciones.asignaturas.edit',compact('asignatura','list_escala','list_pestudio','list_comment'));
    }
    public function update(UpdateAsignaturaRequest $request, $id)
    {
        $asignatura = Asignatura::findOrFail($id);
        $asignatura->fill($request->all());
        $asignatura->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.asignaturas.index');
    }

    public function destroy($id, Request $request)
    {
        $asignatura = Asignatura::findOrFail($id);
        $asignatura->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.asignaturas.index');
    }
}
