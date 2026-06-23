<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Pescolar\CampoConocimiento;
use Illuminate\Support\Facades\Session;

class CampoConocimientoController extends Controller
{
    public function store(Request $request)
    {
        $area_conocimiento_id = ($request->area_conocimiento_id) ? $request->area_conocimiento_id : null;
        if ($area_conocimiento_id) {

            $arr_asignatura = ($request->asignatura_id) ? $request->asignatura_id : [];

            $campo_conocimientos = CampoConocimiento::Where('area_conocimiento_id',$area_conocimiento_id)->get() ;

            foreach ($campo_conocimientos as $campo_conocimiento) {

                $campo_conocimiento->delete();

            }

            foreach ($arr_asignatura as $k => $asignatura_id) {
                $arr = [
                    'area_conocimiento_id'=>$area_conocimiento_id,
                    'asignatura_id'=>$asignatura_id
                ];
                $create[] = CampoConocimiento::create($arr);
            }

            $messenge = trans('db_oper_result.create_ok');
            Session::flash('operp_ok',$messenge);
            return redirect()->route('administracion.configuraciones.area_conocimientos.index');
        }
    }
    public function update(Request $request, $id)
    {
        $campo_conocimiento = CampoConocimiento::findOrFail($id);
        $campo_conocimiento->fill($request->all());
        $campo_conocimiento->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.area_conocimientos.index');
    }
    public function destroy($id, Request $request)
    {
        $campo_conocimiento = CampoConocimiento::findOrFail($id);
        $campo_conocimiento->delete();
        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.area_conocimientos.index');
    }
}
