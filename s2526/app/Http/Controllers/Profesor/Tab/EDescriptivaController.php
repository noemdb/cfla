<?php

namespace App\Http\Controllers\Profesor\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administracion\Profesor\CreateEDescriptivaRequest;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Profesor\Pevaluacion\Edescriptiva;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EDescriptivaController extends Controller
{
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $profesor = $this->profesor;
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;

        $estudiants = collect();
        $grado = collect();
        $seccion = collect();

        if ($grado_id && $seccion_id) {

            $seccion = Seccion::select('seccions.*')
            // ->join('pevaluacions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            // ->join('profesors', 'profesors.id', '=', 'pevaluacions.profesor_id')
            // ->join('profesor_guias', 'profesors.id', '=', 'profesor_guias.profesor_id')
            ->join('profesor_guias', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->where('seccions.id',$seccion_id)
            ->where('profesor_guias.profesor_id',$profesor->id)
            ->groupBy('seccions.id')
            ->orderBy('profesor_guias.created_at','desc')
            ->first()
            ;
            if ($seccion) {

                $grado = $seccion->grado;

                $estudiants = $seccion->estudiants_in;
            }
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

        return view('profesors.edescriptivas.index', compact('estudiants','grado','grado_id','list_grado','seccion','seccion_id','list_seccion','list_lapso','lapso_id','list_comment'));

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
