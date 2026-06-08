<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\HistoricoNota\Tevaluacion;
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\RegistroTitulo;
use App\Models\app\RegistroTitulo\Titulo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RegistroTituloController extends Controller
{
    public function index(Request $request)
    {
        $pestudios = Pestudio::active('true')->get();
        $registro_titulos = RegistroTitulo::all();
        $list_comment = Titulo::COLUMN_COMMENTS;

        return view('administracion.registro_titulos.index', compact('pestudios','registro_titulos','list_comment'));
    }

    public function crud(Request $request)
    {
        $registro_titulos = RegistroTitulo::all();

        return view('administracion.registro_titulos.crud', compact('registro_titulos'));
    }

    public function create(Request $request)
    {
        $list_pestudios = Pestudio::active('true')->pluck('name', 'id');
        $list_comment = RegistroTitulo::COLUMN_COMMENTS;
        $institucion = Institucion::OrderBy('created_at','DESC')->first();

        $list_tipo = collect(['Título'=>'Título','Certificado'=>'Certificado','Reconocimiento'=>'Reconocimiento']);
        $list_tipo_evaluacion = Tevaluacion::all()->pluck('name','id');
        $list_grado = Grado::list_pestudio_grado();

        return view('administracion.registro_titulos.create',
        compact('list_pestudios','list_comment','institucion','list_tipo','list_grado','list_tipo_evaluacion'));
    }

    public function store(Request $request)
    {
        $registro_titulo = RegistroTitulo::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.registro_titulos.crud');
    }

    public function edit($id)
    {
        $registro_titulo = RegistroTitulo::findOrFail($id);
        $institucion = Institucion::OrderBy('created_at','DESC')->first();

        $list_pestudios = Pestudio::active('true')->pluck('name', 'id');
        $list_comment = RegistroTitulo::COLUMN_COMMENTS;
        $list_tipo = collect(['Título'=>'Título','Certificado'=>'Certificado','Reconocimiento'=>'Reconocimiento']);
        $list_tipo_evaluacion = Tevaluacion::all()->pluck('name','id');
        $list_grado = Grado::list_pestudio_grado();

        return view('administracion.registro_titulos.edit',compact('registro_titulo','institucion','list_comment','list_pestudios','list_tipo','list_grado','list_tipo_evaluacion'));
    }

    public function update(Request $request, $id)
    {
        $registro_titulo = RegistroTitulo::findOrFail($id);
        $registro_titulo->fill($request->all());
        $registro_titulo->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.registro_titulos.edit',$id);
    }

    public function destroy($id, Request $request)
    {
        $registro_titulo = RegistroTitulo::findOrFail($id);
        $messenge = trans('db_oper_result.destroy_not_ok');
        if ($registro_titulo->status_delete) {
            $registro_titulo->delete();
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
        return redirect()->route('administracion.registro_titulos.crud');
    }

}
