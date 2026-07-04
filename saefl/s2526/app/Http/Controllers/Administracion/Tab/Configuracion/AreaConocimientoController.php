<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Modelos
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\AreaConocimiento;
use App\Models\app\Pescolar\Asignatura;
use App\Models\app\Pescolar\CampoConocimiento;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AreaConocimientoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_control']);
    }

    public function index()
    {
        $area_conocimientos = AreaConocimiento::all()->sortByDesc('id');
        $campo_conocimientos = CampoConocimiento::all()->sortByDesc('id');

        $list_comment_area = AreaConocimiento::COLUMN_COMMENTS;
        $list_comment_grupo = CampoConocimiento::COLUMN_COMMENTS;

        $list_area_conocimiento = AreaConocimiento::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_asignaturas = Asignatura::select('id',DB::raw("CONCAT(code,' ',name) as fname"))->orderby('name','asc')->pluck('fname', 'id');
        $list_peducativo = Peducativo::select('name', 'id')->where('status_active','true')->orderby('name','asc')->pluck('name', 'id');
        $list_pestudio = Pestudio::select('name', 'id')->where('status_active','true')->orderby('name','asc')->pluck('name', 'id');

        $peducativos = Peducativo::active('true')->get();
        $pestudios = Pestudio::active('true')->get();

        $user_list = User::select('users.*')->orderby('users.username','asc')->pluck('username', 'id');

        return view('administracion.configuraciones.area_conocimientos.index',
        compact('peducativos','pestudios','area_conocimientos','campo_conocimientos','list_peducativo','list_pestudio','list_area_conocimiento','list_asignaturas','list_comment_area','list_comment_grupo','user_list'));
    }

    public function create()
    {
        $list_comment = AreaConocimiento::COLUMN_COMMENTS;
        $user_list = User::select('users.*')->orderby('users.username','asc')->pluck('username', 'id');
        $list_grado = Grado::list_pestudio_grado();
        return view('administracion.configuraciones.area_conocimientos.create',compact('list_grado','list_comment','user_list'));
    }
    public function store(Request $request)
    {
        $AreaConocimiento = AreaConocimiento::create($request->all());
        $messenge = trans('db_oper_result.create_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.area_conocimientos.index');
    }

    public function edit($id)
    {
        $AreaConocimiento = AreaConocimiento::findOrFail($id);
        $list_comment_area = AreaConocimiento::COLUMN_COMMENTS;
        $list_comment_grupo = CampoConocimiento::COLUMN_COMMENTS;
        $list_peducativo = Peducativo::select('name', 'id')->where('status_active','true')->orderby('name','asc')->pluck('name', 'id');
        $list_pestudio = Pestudio::select('name', 'id')->where('status_active','true')->orderby('name','asc')->pluck('name', 'id');
        $user_list = User::select('users.*')->orderby('users.username','asc')->pluck('username', 'id');
        return view('administracion.configuraciones.area_conocimientos.edit',compact('area_conocimientos','list_comment_area','list_comment_area','list_peducativo','list_pestudio','user_list'));
    }
    public function update(Request $request, $id)
    {
        $AreaConocimiento = AreaConocimiento::findOrFail($id);
        $AreaConocimiento->fill($request->all());
        $AreaConocimiento->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.area_conocimientos.index');
    }

    public function destroy($id, Request $request)
    {
        $area_conocimiento = AreaConocimiento::findOrFail($id);
        $messenge = trans('db_oper_result.destroy_not_ok');
        if ($area_conocimiento->status_delete) {
            $area_conocimiento->delete();
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
        return redirect()->route('administracion.configuraciones.area_conocimientos.index');
    }
}
