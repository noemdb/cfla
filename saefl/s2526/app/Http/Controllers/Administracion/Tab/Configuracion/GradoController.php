<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Modelos
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Grado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GradoController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth','is_control']);
    }

    public function index()
    {
        $grados =Grado::all();
        $list_comment = Grado::COLUMN_COMMENTS;
        // $list_pestudio = Pestudio::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_pestudio = Pestudio::select(DB::raw('concat(pestudios.code, " || ",pestudios.name ) as code_name'),'name', 'id')
            ->active('true')
            ->orderby('name','asc')
            ->pluck('code_name', 'id');
        return view('administracion.configuraciones.grados.index',compact('grados','list_comment','list_pestudio'));
    }

    public function create()
    {
        $list_comment = Grado::COLUMN_COMMENTS;
        $list_pestudio = Pestudio::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        return view('administracion.configuraciones.grados.create',compact('list_pestudio','list_comment'));
    }

    public function store(Request $request)
    {
        $grado = Grado::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.grados.index');
    }

    public function edit($id)
    {
        $grado = Grado::findOrFail($id);
        $list_comment = Grado::COLUMN_COMMENTS;
        $list_pestudio = Pestudio::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        return view('administracion.configuraciones.grados.edit',compact('grado','list_comment','list_pestudio'));
    }
    public function update(Request $request, $id)
    {
        $grado = Grado::findOrFail($id);
        $grado->fill($request->all());
        $grado->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.grados.index',$id);
    }

    public function destroy($id, Request $request)
    {
        $grado = Grado::findOrFail($id);
        $messenge = trans('db_oper_result.destroy_not_ok');
        if ($grado->status_delete) {
            $grado->delete();
            $messenge = trans('db_oper_result.delete_ok');
            $operation= 'delete';
            if($request->ajax()){
                return response()->json([
                    "messenge"=>$messenge,
                    "operation"=>$operation,
                ]);
            }
        }
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.grados.index');
    }
}
