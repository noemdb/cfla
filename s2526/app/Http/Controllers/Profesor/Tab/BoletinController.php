<?php

namespace App\Http\Controllers\Profesor\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Lapso;
use App\Models\app\Pescolar\Asignatura;
use App\Models\app\Pescolar\Baremo;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Profesor\Pevaluacion;
use App\Http\Controllers\Profesor\Tab\Functions\Boletins\Carga;
///home/nuser/code/s2021/app/Http/Controllers/Profesor/Tab/Functions/Carga.php

class BoletinController extends Controller
{

    use Carga;
    protected $profesor;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->profesor = Profesor::where('user_id',Auth::user()->id)->first();
            return $next($request);
        });
    }

    public function planilla_notas(Request $request)
    {
        $profesor = $this->profesor;
        $seccions = $profesor->seccions;
        $pensums = $profesor->pensums;
        $pevaluacions = Pevaluacion::where('pevaluacions.profesor_id',$profesor->id)->groupBy('pensum_id','seccion_id')->get();
        $lapsos = Lapso::all();
        $baremo = new Baremo;

        // dd($pevaluacions);

        return view('profesors.boletins.planillas_notas',compact('seccions','pensums','pevaluacions','lapsos','baremo'));
    }

    public function index(Request $request)
    {
        $profesor = $this->profesor;
        $pevaluacions = Pevaluacion::select('pevaluacions.*')->where('pevaluacions.profesor_id',$profesor->id);
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $seccion_id = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $lapso_id = (!empty($request->lapso_id)) ? $request->lapso_id: null;
        $fecha = Carbon::now();

        if ($grado_id) {
            $pevaluacions = $pevaluacions
                    ->join('pensums','pensums.id','=','pevaluacions.pensum_id')
                    ->join('grados','grados.id','=','pensums.grado_id')
                    ->where('grados.id',$grado_id);
        }

        $pevaluacions = ($seccion_id) ? $pevaluacions->where('pevaluacions.seccion_id',$seccion_id) : $pevaluacions ;
        $pevaluacions = ($lapso_id) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions ;

        $pevaluacions = $pevaluacions->get();

        $list_grado = Profesor::list_grado($profesor->id);

        $list_seccion = Seccion::where('grado_id',$grado_id)->pluck('name', 'id');

        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return view('profesors.boletins.index',compact('pevaluacions','grado_id','seccion_id','lapso_id','list_grado','list_seccion','list_lapso','fecha'));
    }

    public function carga(Request $request)
    {
        $profesor = Profesor::where('user_id',Auth::user()->id)->first(); //dd($profesor);

        $pevaluacion_id = (!empty($request->pevaluacion_id)) ? $request->pevaluacion_id : null ;

        $pevaluacion = Pevaluacion::where('id',$pevaluacion_id)
            ->where('profesor_id',$profesor->id)
            ->orderby('created_at')->first();

        $list_pevaluacion = Pevaluacion::select('pevaluacions.id',
            DB::raw("CONCAT(grados.name, ' ',seccions.name, ' - ',asignaturas.name, ' || ' ,pevaluacions.description) as fullname"))
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('seccions', 'seccions.id', '=', 'pevaluacions.seccion_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->wherenull('pensums.deleted_at')
            ->wherenull('seccions.deleted_at')
            ->wherenull('grados.deleted_at')
            ->where('pevaluacions.profesor_id',$profesor->id)
            ->OrderBy('grados.name')
            ->pluck('fullname', 'id');


        if (!$pevaluacion) {
            return redirect()->back()->withErrors(['pevaluacion_id' => 'Planificación no encontrada o no autorizada.']);
        }

        $escala = $pevaluacion->escala;

        if (!$escala) {
            return redirect()->back()->withErrors(['pevaluacion_id' => 'La planificación no tiene una escala de evaluación configurada.']);
        }

        $minimo = $escala->minimo;
        $maximo = $escala->maximo;
        $list_nota[0] = 'I';
        for ($i=$minimo; $i <= $maximo ; $i++) { $list_nota[$i] = $i;}

        return view('profesors.boletins.carga', compact('list_pevaluacion','pevaluacion','list_nota'));
    }

    public function store(Request $request)
    {
        $nota_arr = (is_array($request->nota)) ? $request->nota: array();
        foreach ($nota_arr as $estudiant_id => $evaluacions) {

            foreach ($evaluacions as $evaluacion_id => $nota) {

                if ( isset($nota) && !(is_null($nota)) && $nota<>''  && $nota<>null ) {

                    $arr = [
                        'estudiant_id'=>$estudiant_id,
                        'evaluacion_id'=>$evaluacion_id,
                        'nota'=>$nota
                    ];

                    $boletin = Boletin::where('estudiant_id',$estudiant_id)->where('evaluacion_id',$evaluacion_id)->first();

                    if ($boletin) {
                        $boletin->fill($arr);
                        $boletin->save();
                    } else {
                        $create = Boletin::create($arr);
                    }

                }

            }
        }

        $messenge = trans('db_oper_result.oper_ok');
        $operation= 'create';
        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "operation"=>$operation,
            ]);
        }

        $messenge = trans('db_oper_result.update_ok');
        Session::flash('operp_ok',$messenge);

        return redirect()->route('profesors.boletins.index');
    }
}
