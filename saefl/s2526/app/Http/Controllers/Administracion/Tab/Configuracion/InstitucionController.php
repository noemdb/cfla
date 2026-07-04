<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//validation request
use App\Http\Requests\Administracion\Configuracion\CreateInstitucionRequest;
use App\Http\Requests\Administracion\Configuracion\UpdateInstitucionRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

//Modelos
use App\Models\app\Institucion;

class InstitucionController extends Controller
{

        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth']);
        // $this->middleware(['auth','is_admin']);
    }

    public function institucion()
    {
        $institucion = Institucion::findOrFail(session('institucion_id'));
        // dd($institucion);

        return view('administracion.configuraciones.institucion.index',compact('institucion'));
    }

    public function InstitucionUpdate(UpdateInstitucionRequest $request, $id)
    {
        $institucion = Institucion::findOrFail($id);
        //dd($institucion);
        $institucion->fill($request->all());
        $institucion->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        Session::flash('class_oper','success');
        // return redirect()->route('administracion.configuraciones.institucion.');
        return redirect()->route('administracion.configuraciones.institucion');
        // return view('administracion.configuraciones.institucion.index',compact('institucion'));
    }
}
