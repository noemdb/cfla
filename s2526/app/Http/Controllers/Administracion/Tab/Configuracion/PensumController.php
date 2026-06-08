<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiante\Escolaridad;
// use App\Http\Requests\Administracion\Estudiant\CreateRepresentantRequest;
// use App\Http\Requests\Administracion\Estudiant\UpdateRepresentantRequest;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Asignatura;
use App\Models\app\Pescolar\Lapso;

class PensumController extends Controller
{

    public function crud(Request $request)
    {
        $pestudios = Pestudio::active('true')->get();
        $pensums = Pensum::all();
        $lapsos = Lapso::all();
        return view('administracion.configuraciones.pensums.crud',compact('pestudios','pensums','lapsos'));
    }

    public function index(Request $request)
    {
        $pestudios = Pestudio::active('true')->get();
        $pensums = Pensum::getGradosActive();
        $list_asignaturas = Asignatura::select('id',DB::raw("CONCAT(code,' ',name) as fname"))->orderby('name','asc')->pluck('fname', 'id');
        $list_escolaridad = Escolaridad::select('name', 'id')->orderby('id','asc')->pluck('name', 'id');
        $list_pestudio = Pestudio::list_pestudio(); //dd($list_grado,$list_asignatura,$list_pestudio);
        $list_grado = Grado::list_pestudio_grado();
        $list_asignatura = Asignatura::list_asignatura();

        return view('administracion.configuraciones.pensums.index',compact('pestudios','pensums','list_asignaturas','list_escolaridad','list_pestudio','list_grado','list_asignatura'));
    }

    public function create(Request $request)
    {
        $pestudio = Pestudio::active('true')->where('id',$request->pestudio_id)->first();
        $grado = Grado::active('true')->where('id',$request->grado_id)->first();
        $list_asignaturas = Asignatura::select('id',DB::raw("CONCAT(code,' ',name) as fname"))->orderby('name','asc')->pluck('fname', 'id');
        $list_escolaridad = Escolaridad::select('name', 'id')->orderby('id','asc')->pluck('name', 'id');
        return view('administracion.configuraciones.pensums.create',compact('pestudio','grado','list_asignaturas','list_escolaridad'));
    }
    public function store(Request $request)
    {
        $arr_dat = $request->all();
        unset($arr_dat['help_asignatura']);
        $plan_benefico = Pensum::create($arr_dat);
        Session::flash('operp_ok','Registro guardado exitosamente');
        return redirect()->route('administracion.configuraciones.pensums.index');
    }

    public function edit($id)
    {
        $pensum = Pensum::findOrFail($id);
        $list_comment = Pensum::COLUMN_COMMENTS;
        $list_grado = Grado::list_pestudio_grado();
        $list_asignatura = Asignatura::list_asignatura();
        // $list_asignatura = Asignatura::all()->pluck('name','id'); //dd($list_asignatura);
        $list_pestudio = Pestudio::list_pestudio(); //dd($list_grado,$list_asignatura,$list_pestudio);
        return view('administracion.configuraciones.pensums.edit',compact('pensum','list_grado','list_asignatura','list_pestudio'));
    }

    public function update(Request $request, $id)
    {
        $pensum = Pensum::findOrFail($id);
        $pensum->fill($request->all());
        $pensum->save();
        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.pensums.index',$id);
    }
    public function destroy($id, Request $request)
    {
        $pensum = Pensum::findOrFail($id);
        $pensum->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.pensums.index');
    }
}
