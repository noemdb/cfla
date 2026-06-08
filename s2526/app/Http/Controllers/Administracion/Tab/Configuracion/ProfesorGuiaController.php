<?php

namespace App\Http\Controllers\Administracion\Tab\Configuracion;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\Administracion\Configuracion\CreateProfesorGuiaRequest;
use App\Http\Requests\Administracion\Configuracion\UpdateProfesorGuiaRequest;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\ProfesorGuia;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion\Escala;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Estudiante\PlanBenefico;

class ProfesorGuiaController extends Controller
{
    public function crud(Request $request)
    {
        /*******************request****************************/
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;

        /*******************query****************************/
        $profesor_guias = ProfesorGuia::select('profesor_guias.*')->OrderBy('created_at','desc');

        /*******************if()?****************************/
        $profesor_guias = ($grado_id) ? $profesor_guias->where('profesor_guias.grado_id',$grado_id) : $profesor_guias ;
        $profesor_guias = ($seccion_id) ? $profesor_guias->where('profesor_guias.seccion_id',$seccion_id) : $profesor_guias ;
        $profesor_guias = ($lapso_id) ? $profesor_guias->where('profesor_guias.lapso_id',$lapso_id) : $profesor_guias ;

        /*******************get collections****************************/
        $profesor_guias = $profesor_guias->get();

       /*******************list****************************/
       $list_grado = Grado::list_pestudio_grado();
       $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();
       $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return view('administracion.configuraciones.profesor_guias.crud',
            compact('profesor_guias','grado_id','seccion_id','lapso_id','list_grado','list_seccion','list_lapso'));
    }

    public function index(Request $request)
    {
        $profesor_guias = ProfesorGuia::all();
        $lapsos = Lapso::all();       

        return view('administracion.configuraciones.profesor_guias.index',compact('profesor_guias','lapsos'));
    }

    public function asignacion(Request $request)
    {
        $grados = Grado::all();
        return view('administracion.configuraciones.profesor_guias.asignacion',compact('grados'));
    }

    public function create(Request $request)
    {
        $profesor_list = Profesor::all()->SortBy('fullname')->pluck('fullname','id');
        $profesor_guias = ProfesorGuia::all()->sortByDesc('create_at');

        $list_grado = Grado::list_pestudio_grado();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');
        $list_lapso['&all&'] = 'Todos';
        $lapsos = Lapso::all();

        return view('administracion.configuraciones.profesor_guias.create',
        compact('profesor_list','list_grado','list_lapso','lapsos','profesor_guias'));
    }

    public function store(CreateProfesorGuiaRequest $request)
    {        
        if ($request->lapso_id =='&all&') {
            $lapsos = Lapso::all();
            foreach ($lapsos as $lapso) {
                $arr = [
                    'profesor_id'=>$request->profesor_id,                    
                    'grado_id'=>$request->grado_id,                    
                    'seccion_id'=>$request->seccion_id,                    
                    'observations'=>$request->observations,                    
                    'lapso_id'=>$lapso->id,                    
                ];
                $profesor = ProfesorGuia::create($arr);
            }
        }
        else {
            $profesor = ProfesorGuia::create($request->all());
        }

        Session::flash('operp_ok','Registro guardado exitosamente');

        return redirect()->route('administracion.configuraciones.profesor_guias.index');
    }

    public function destroy($id, Request $request)
    {
        $profesor = ProfesorGuia::findOrFail($id);
        $profesor->delete();

        $messenge = trans('db_oper_result.delete_ok');
        $operation= 'delete';

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        Session::flash('operp_ok',$messenge);
        return redirect()->route('administracion.configuraciones.profesor_guias.index');
    }
}
