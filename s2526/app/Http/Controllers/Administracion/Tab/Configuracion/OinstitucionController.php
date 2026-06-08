<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\HistoricoNota\Oinstitucion;

class OinstitucionController extends Controller
{
    public function index(Request $request)
    {
        $oinstitucions = Oinstitucion::all();
        $list_comment = Oinstitucion::COLUMN_COMMENTS;
        return view('administracion.configuraciones.oinstitucions.index',compact('oinstitucions','list_comment'));
    }  

    public function create()
    {
        $oinstitucions = Oinstitucion::all()->sortByDesc('created_at');
        $list_comment = Oinstitucion::COLUMN_COMMENTS;
        return view('administracion.configuraciones.oinstitucions.create',compact('oinstitucions','list_comment'));
    }
    public function store(Request $request)
    {
        $oinstitucions = Oinstitucion::create($request->all());
        Session::flash('operp_ok','Registro guardado exitosamente');
        return redirect()->route('administracion.configuraciones.oinstitucions.index');
    }

    public function edit($id)
    {
        $oinstitucion = Oinstitucion::findOrFail($id);
        $list_comment = Oinstitucion::COLUMN_COMMENTS;
        $oinstitucions = Oinstitucion::all()->sortByDesc('created_at');
        return view('administracion.configuraciones.oinstitucions.edit',compact('oinstitucions','oinstitucion','list_comment'));
    }
    public function update(Request $request, $id)
    {
        $oinstitucion = Oinstitucion::findOrFail($id);
        $oinstitucion->fill($request->all());
        $oinstitucion->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.oinstitucions.edit',$id);
    }

    public function destroy($id, Request $request)
    {
        $oinstitucion = Oinstitucion::findOrFail($id);
        $oinstitucion->delete();
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
