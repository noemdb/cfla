<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Configuracion\CreateAutoridadRequest;
use App\Http\Requests\Administracion\Configuracion\UpdateAutoridadRequest;
use App\Models\app\Estudiant;
//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

//Modelos
use App\Models\app\Institucion;
use App\Models\app\Pescolar;
use App\Models\app\Institucion\Autoridad;
use App\Models\app\Institucion\Tautoridad;
use App\User;
use Illuminate\Support\Facades\DB;

class AutoridadController extends Controller
{
    public function autoridad()
    {
        $autoridads =
            Autoridad::OrderBy('created_at','DESC')
                ->where('institucion_id',session('institucion_id'))
                ->where('finicial','<=',Carbon::now())
                ->where('ffinal','>=',Carbon::now())
                // ->where('position','Administrador')
                ->get();

        $list_comment = Autoridad::COLUMN_COMMENTS;

        $tipo_list = Tautoridad::select('tautoridads.*')
            ->orderby('tautoridads.name','asc')
            ->pluck('name', 'id');

        $pescolar_list = Pescolar::select('pescolars.*')
            ->orderby('pescolars.name','asc')
            ->pluck('name', 'id');

        $institucion_list = Institucion::select('institucions.*')
            ->orderby('institucions.name','asc')
            ->pluck('name', 'id');

        $user_list = User::orderby('users.username','asc')
            ->pluck('users.username', 'users.id');

        return view('administracion.configuraciones.autoridad.index',compact('autoridads','list_comment','user_list','tipo_list','pescolar_list','institucion_list'));
    }

    public function AutoridadUpdate(UpdateAutoridadRequest $request, $id)
    {
        $autoridad = Autoridad::findOrFail($id);
        //dd($institucion);
        $autoridad->fill($request->all());
        $autoridad->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        // return redirect()->route('administracion.configuraciones.institucion.');
        return redirect()->route('administracion.configuraciones.autoridad');
        // return view('administracion.configuraciones.institucion.index',compact('institucion'));
    }

}
