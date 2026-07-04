<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\Administracion\Configuracion\CreateDescuentoRequest;
// use App\Http\Requests\Administracion\Configuracion\UpdateAsignaturaRequest;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Estudiante\Descuento;
use App\Models\app\Estudiante\PlanBenefico;

class DescuentoController extends Controller
{
    public function create()
    {
        $descuentos = Descuento::all();

        return view('administracion.configuraciones.descuentos.create',compact('descuentos'));
    }

    public function store(CreateDescuentoRequest $request)
    {
        $descuentos = Descuento::create($request->all());

        Session::flash('operp_ok','Registro guardado exitosamente');

        $view = (isset($request->view)) ? $request->view : 'administracion.configuraciones.descuentos.crud';

        $estudiant_id = (isset($request->estudiant_id)) ? $request->estudiant_id : null ;

        return redirect()->route($view,compact('estudiant_id'));
    }
    public function crud(Request $request)
    {
        $descuentos = Descuento::all();

        return view('administracion.configuraciones.descuentos.crud',compact('descuentos'));
    }
    public function destroy($id, Request $request)
    {
        $data = Descuento::findOrFail($id);
        $data->delete();
        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation
            ]);
        }
        Session::flash('operp_ok',$messenge.' -> ('.$data->estudiant->name.')');
        $descuentos = Descuento::all();
        return view('administracion.configuraciones.descuentos.crud',compact('descuentos'));
    }
}
