<?php

namespace App\Http\Controllers\Profesor\Tab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;
use App\Models\app\Pescolar\Lapso;

use App\Models\app\Pescolar\Pensum;
use App\Models\app\Pescolar\Profesor;
use App\Models\app\Estudiante\Representant;

class RepresentantsController extends Controller
{
    public $profesor;
    
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

        $representants = Representant::select('representants.*')
            ->join('estudiants','representants.id','=','estudiants.representant_id')
            ->join('inscripcions','estudiants.id','=','inscripcions.estudiant_id')
            ->join('seccions','seccions.id','=','inscripcions.seccion_id')
            ->join('grados','grados.id','=','seccions.grado_id')
            ->join('profesor_guias', 'seccions.id', '=', 'profesor_guias.seccion_id')
            ->join('profesors','profesors.id','=','profesor_guias.profesor_id')
            ->where('profesors.id',$profesor->id)
            ->groupBy('representants.ci_representant')
            ->OrderBy('created_at','desc');

        $representants = ($grado_id) ? $representants->where('grados.id',$grado_id) : $representants ;
        $representants = ($seccion_id) ? $representants->where('seccions.id',$seccion_id) : $representants ;
        $representants = ($lapso_id) ? $representants->where('pevaluacions.lapso_id',$lapso_id) : $representants ;

        $representants = $representants->get(); //dd($representants);

        $list_grado = Profesor::list_grado_guia($profesor->id);
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();
        $list_lapso = Lapso::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return view('profesors.representants.index',compact('representants','grado_id','list_grado','seccion_id','list_seccion','lapso_id','list_lapso'));
    }
}
