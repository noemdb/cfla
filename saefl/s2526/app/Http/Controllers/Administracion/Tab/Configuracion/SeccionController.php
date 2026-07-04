<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Modelos
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;

use Illuminate\Support\Facades\Session;

class SeccionController extends Controller
{
    public function __construct()
    {
    $this->middleware(['auth','is_control']);
    }
    public function index()
    {
        $seccions = Seccion::all();
        $list_comment = Seccion::COLUMN_COMMENTS;
        // $pestudios = Pestudio::active('true')->with('grados')->orderBy('id', 'asc')->get(); //dd($pestudios);
        // $list_grado = array();
        // foreach ($pestudios as $pestudio) {
        //     $list_grado[$pestudio->name] = $pestudio->grados->pluck('name', 'id');
        // }
        $list_grado = Grado::list_pestudio_grado();
        $list_grado = Grado::list_pestudio_grado_all();

        return view('administracion.configuraciones.seccions.index',compact('seccions','list_grado','list_comment'));
    }

    public function create()
    {
        $list_comment = Seccion::COLUMN_COMMENTS;
        // $pestudios = Pestudio::active('true')->with('grados')->orderBy('id', 'asc')->get();
        // foreach ($pestudios as $pestudio) {
        //     $list_grado[$pestudio->name] = $pestudio->grados->pluck('name', 'id');
        // }
        $list_grado = Grado::list_pestudio_grado();
        return view('administracion.configuraciones.seccions.create',compact('list_grado','list_comment'));
    }
    public function store(Request $request)
    {
        $seccion = Seccion::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.seccions.index');
    }
    public function edit($id)
    {
        $seccion = Seccion::findOrFail($id);
        $list_comment = Seccion::COLUMN_COMMENTS;
        // $pestudios = Pestudio::active('true')->with('grados')->orderBy('id', 'asc')->get();
        // foreach ($pestudios as $pestudio) {
        //     $list_grado[$pestudio->name] = $pestudio->grados->pluck('name', 'id');
        // }
        $list_grado = Grado::list_pestudio_grado();
        return view('administracion.configuraciones.seccions.edit',compact('seccion','list_comment','list_grado'));
    }
    public function update(Request $request, $id)
    {
        $seccion = Seccion::findOrFail($id);
        $seccion->fill($request->all());
        $seccion->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.seccions.index',$id);
    }

    public function destroy($id, Request $request)
    {
        $grado = Seccion::findOrFail($id);
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
        return redirect()->route('administracion.configuraciones.seccions.index');
    }
}
