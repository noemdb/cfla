<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Modelos
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Peducativo;
use App\Models\app\Pescolar\Pestudio;
use App\User;
use Illuminate\Support\Facades\Session;

class PestudioController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_control']);
    }
    public function index()
    {
        $pestudios = Pestudio::OrderBy('created_at','DESC')->get();

        $list_comment = Pestudio::COLUMN_COMMENTS;

        $list_peducativo = Peducativo::select('name', 'id')
            ->orderby('name','asc')
            ->pluck('name', 'id');

        return view('administracion.configuraciones.pestudios.index',compact('pestudios','list_peducativo','list_comment'));
    }

    public function create()
    {
        $list_peducativo = Peducativo::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_comment = Pestudio::COLUMN_COMMENTS;
        $user_list = User::select('users.*')->orderby('users.username','asc')->pluck('username', 'id');
        return view('administracion.configuraciones.pestudios.create',compact('list_peducativo','list_comment','user_list'));
    }
    public function store(Request $request)
    {
        $pestudio = Pestudio::create($request->all());
        Session::flash('operp_ok','Registro guardado exitosamente');
        return redirect()->route('administracion.configuraciones.pestudios.index');
    }


    public function edit($id)
    {
        $pestudio = Pestudio::findOrFail($id);
        $list_comment = Pestudio::COLUMN_COMMENTS;
        $list_peducativo = Peducativo::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        $user_list = User::select('users.*')->orderby('users.username','asc')->pluck('username', 'id');

        return view('administracion.configuraciones.pestudios.edit',compact('pestudio','list_peducativo','list_comment','user_list'));
    }

    // public function update(UpdatebancoRequest $request, $id)
    public function update(Request $request, $id)
    {
        $pestudio = Pestudio::findOrFail($id);
        $pestudio->fill($request->all());
        $pestudio->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.pestudios',$id);
    }
    public function destroy($id, Request $request)
    {
        $pestudio = Pestudio::findOrFail($id);
        $messenge = trans('db_oper_result.destroy_not_ok');
        if ($pestudio->status_delete) {
            $pestudio->delete();
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
        return redirect()->route('administracion.configuraciones.pestudios.index');
    }
}
