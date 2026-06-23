<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

//Modelos
use App\Models\app\Institucion;
use App\Models\app\Pescolar\Peducativo;
use App\User;

class PeducativoController extends Controller
{
    public function __construct()
    {
    $this->middleware(['auth','is_control']);
    }

    public function index()
    {
        $peducativos =
        Peducativo::OrderBy('created_at','DESC')
            ->where('pescolar_id',session('pescolar_id'))
            ->get();
        $list_comment = Peducativo::COLUMN_COMMENTS;
        return view('administracion.configuraciones.peducativos.index',compact('peducativos','list_comment'));
    }

    public function edit($id)
    {
        $peducativo = Peducativo::findOrFail($id);
        $list_comment = Peducativo::COLUMN_COMMENTS;
        $user_list = User::select('users.*')->orderby('users.username','asc')->pluck('username', 'id');

        return view('administracion.configuraciones.peducativos.edit',compact('peducativo','list_comment','user_list'));
    }
    public function update(Request $request, $id)
    {
        $peducativo = Peducativo::findOrFail($id); //dd($request->all());
        $peducativo->fill($request->all());
        $peducativo->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.peducativos.index');
    }

    public function create()
    {
        // $list_peducativo = Peducativo::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_comment = Peducativo::COLUMN_COMMENTS;
        $user_list = User::select('users.*')->orderby('users.username','asc')->pluck('username', 'id');
        return view('administracion.configuraciones.peducativos.create',compact('list_comment','user_list'));
    }
    public function store(Request $request)
    {
        $peducativo = Peducativo::create($request->all());
        Session::flash('operp_ok','Registro guardado exitosamente');
        return redirect()->route('administracion.configuraciones.peducativos.index');
    }

    public function destroy($id, Request $request)
    {
        $peducativo = Peducativo::findOrFail($id);
        $messenge = trans('db_oper_result.destroy_not_ok');
        if ($peducativo->status_delete) {
            $peducativo->delete();
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
