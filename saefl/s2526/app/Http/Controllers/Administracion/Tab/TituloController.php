<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\RegistroTitulo;
use App\Models\app\RegistroTitulo\Titulo;
use Illuminate\Support\Facades\Session;

class TituloController extends Controller
{
    public function aprove(Request $request)
    {
        // dd($request->all());

        $registro_titulo_id = ($request->registro_titulo_id) ? $request->registro_titulo_id : null;
        $registro_titulo = RegistroTitulo::findOrFail($registro_titulo_id);

        $aprove_arr = ($request->aprove) ? $request->aprove : array();
        $estudiant_arr = ($request->estudiant_id) ? $request->estudiant_id : array();
        $seccion_arr = ($request->seccion_id) ? $request->seccion_id : array();
        $serie_arr = ($request->serie) ? $request->serie : array();
        $observations_arr = ($request->observations) ? $request->observations : array();

        $titulo = null;

        foreach ($estudiant_arr as $k => $estudiant_id) {

            if (array_key_exists($estudiant_id, $aprove_arr)) {

                $estudiant = Estudiant::findOrFail($estudiant_id);
                $seccion = Seccion::findOrFail($seccion_arr[$estudiant_id]);
                $titulo = $estudiant->getTitulo($registro_titulo->id);

                $arr = [
                    'registro_titulo_id'=>$registro_titulo->id,
                    'estudiant_id'=>$estudiant->id,
                    'seccion_id'=>$seccion->id,
                    'serie'=>$serie_arr[$estudiant->id],
                    'observations'=>$observations_arr[$estudiant->id]
                ];

                if ($titulo) $titulo->delete();

                $titulo = Titulo::create($arr);

            }

        }

        $messenge = ($titulo) ? 'Buen trabajo! Títulos registrados exitosamente' : 'No hay datos para registrar' ;
        $operation = ($titulo) ? 'operp_ok' : 'operp_no_ok' ;

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation
            ]);
        }

        Session::flash($operation,$messenge );
        return redirect()->route('administracion.registro_titulos.index');

    }

    public function crud(Request $request)
    {
        $titulos = Titulo::all();

        return view('administracion.registro_titulos.titulos', compact('titulos'));
    }

    public function destroy($id, Request $request)
    {
        $titulo = Titulo::findOrFail($id);

        $messenge = trans('db_oper_result.destroy_not_ok');
        $titulo->delete();
        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.registro_titulos.titulos');
    }


}
