<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administracion\Profesor\CreateEcualitativaRequest;
use App\Models\app\Profesor\Pevaluacion\Ecualitativa;
use Illuminate\Support\Facades\Session;

class EcualitativaController extends Controller
{
    public function store(CreateEcualitativaRequest $request)
    {
        $estudiant_id = (!empty($request->estudiant_id)) ? $request->estudiant_id : null ;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id : null ;

        $ecualitativa = Ecualitativa::where('estudiant_id',$estudiant_id)->where('lapso_id',$lapso_id)->first();

        if ($ecualitativa) {
            $ecualitativa->fill($request->all());
            $ecualitativa->save();
        }
        else {
            $ecualitativa = Ecualitativa::create($request->all());
        }

        $messenge = trans('db_oper_result.oper_ok');
        $operation= 'create';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok','Registro guardado/actualizado exitosamente');

        return redirect()->route('administracion.ecualitativa.index');
    }
}
