<?php

namespace App\Http\Controllers\Representant\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Escolaridad;
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Estudiante\Programacion;
use App\Models\app\Estudiante\Representant;
use App\Models\app\Estudiante\Tinscripcion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Preinscripcion;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PreinscripcionController extends Controller
{
    protected $representant,$estudiants,$list_comment;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth','is_representant', function ($request, $next) {
            $this->representant = Representant::where('user_id',Auth::user()->id)->first();
            $this->list_comment = Representant::COLUMN_COMMENTS;
            $this->estudiants = ($this->representant) ? $this->representant->estudiants : null;
            return $next($request);
        }]);
    }

    public function crud(Request $request)
    {
        $representant = $this->representant;
        $preinscripcions = Preinscripcion::select('preinscripcions.*')
        ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
        ->where('estudiants.representant_id',$representant->id)
        ->get();

        /*******************list****************************/
        $list_comment = $this->list_comment; //dd($list_comment);

        return view('representants.preinscripcions.crud', compact('preinscripcions','list_comment'));
    }

    public function create()
    {
        $representant = $this->representant;

        $list_estudiants = $this->estudiants->pluck('name', 'id');

        $list_grado = Grado::list_pestudio_grado();

        $list_escolaridad = Escolaridad::select('name', 'id')
            ->orderby('name','asc')
            ->pluck('name', 'id');

        $list_programacion = Programacion::select('description', 'id')
            ->orderby('id','asc')
            ->pluck('description', 'id');

        $list_seccion = ['Seleccione un grado'=>'Seleccione un grado'];

        $list_tinscripcion = Tinscripcion::select('name', 'id')
            ->orderby('name','asc')
            ->pluck('name', 'id');

        $list_grupo_estables = GrupoEstable::select('id', 'name',DB::raw("CONCAT(name,' || ',code) as fullname"))
            ->orderby('name','asc')
            ->pluck('fullname', 'id');

        $list_comment = $this->list_comment; //dd($list_comment);

        $preinscripcions = Preinscripcion::select('preinscripcions.*')
        ->join('grados', 'grados.id', '=', 'preinscripcions.grado_id')
        ->join('estudiants', 'estudiants.id', '=', 'preinscripcions.estudiant_id')
        ->where('estudiants.representant_id',$representant->id)
        ->get();

        return view('representants.preinscripcions.create',compact('preinscripcions','list_estudiants','list_tinscripcion','list_grado','list_seccion','list_escolaridad','list_programacion','list_grupo_estables','list_comment'));
    }

    public function store(Request $request)
    {
        $preinscripcion = Preinscripcion::create($request->all());

        Session::flash('operp_ok','Registro guardado exitosamente');

        $preinscripcions = collect();

        $list_comment = $this->list_comment; //dd($list_comment);

        return redirect()->route('representants.preinscripcions.crud',compact('preinscripcions','list_comment'));
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

}
