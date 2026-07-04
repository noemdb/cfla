<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administracion\Profesor\CreateEDescriptivaRequest;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion\Edescriptiva;
use Illuminate\Support\Facades\Session;

class EDescriptivaController extends Controller
{
    public function index(Request $request)
    {
        $search = (!empty($request->search)) ? $request->search : null ;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;

        $estudiants = collect();

        if (count($request->all())>0) {

            $estudiants = Estudiant::WidthInscripcion()
            // ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ;

            $estudiants = ($grado_id) ? $estudiants->where('grados.id',$grado_id) : $estudiants ;
            $estudiants = ($seccion_id) ? $estudiants->where('seccions.id',$seccion_id) : $estudiants ;

            $arr_get = [ 'search'=>$search];
            $estudiants = ($search) ? $estudiants->name($arr_get) : $estudiants ;

            $estudiants = $estudiants->get();

        }

        /*******************list****************************/
        // $pestudios = Pestudio::active('true')->with('grados')->orderBy('id', 'asc')->get();
        // foreach ($pestudios as $pestudio) {
        //     $list_grado[$pestudio->name] = $pestudio->grados->pluck('name', 'id');
        // }
        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();
        $list_lapso = Lapso::select('code', 'id')->orderby('name','asc')->pluck('code', 'id'); $list_lapso->put(null,'Final');

        $list_comment = Edescriptiva::COLUMN_COMMENTS;

        return view('administracion.edescriptivas.index', compact('estudiants','search','grado_id','list_grado','seccion_id','list_seccion','lapso_id','list_lapso','list_comment'));

    }

    public function store(CreateEDescriptivaRequest $request)
    {
        $estudiant_id = (!empty($request->estudiant_id)) ? $request->estudiant_id : null ;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id : null ;

        $edescriptiva = Edescriptiva::where('estudiant_id',$estudiant_id)->where('lapso_id',$lapso_id)->first();

        if ($edescriptiva) {
            $edescriptiva->fill($request->all());
            $edescriptiva->save();
            $operation= 'update';
            $messenge = trans('db_oper_result.oper_ok');
        }
        else {
            $operation= 'create';
            $messenge = trans('db_oper_result.oper_ok');
            $edescriptiva = Edescriptiva::create($request->all());
        }

        $count = Edescriptiva::where('estudiant_id',$estudiant_id)->count();

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
                "count"=>$count,
            ]);
        }

        Session::flash('operp_ok',$messenge);

        return redirect()->route('administracion.edescriptivas.index');
    }

    public function destroy($id, Request $request)
    {
        $edescriptiva = EDescriptiva::findOrFail($id);
        $edescriptiva->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.edescriptivas.index');
    }
}
