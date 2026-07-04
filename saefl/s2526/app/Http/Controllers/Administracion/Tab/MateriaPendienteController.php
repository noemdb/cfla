<?php

namespace App\Http\Controllers\Administracion\Tab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//Helpers
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

//validation request
// use App\Http\Requests\Administracion\CreateUserRequest;
// use App\Http\Requests\Administracion\UpdateUserRequest;

use App\Models\app\Pescolar;
use App\Models\app\Estudiant;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Estudiante\Inscripcion;
use App\Models\app\Estudiante\MateriaPendiente;
use App\Models\app\Estudiante\Administrativa;
use App\Models\app\Estudiante\Escolaridad;
use App\Models\app\Estudiante\Tinscripcion;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Seccion;

class MateriaPendienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->get('search')) {

            $search = $request->get('search');
            $arr_get = [ 'search'=>$search];
            $arr_inscripcion = MateriaPendiente::select('estudiant_id')->get()->toArray();

            $estudiants = Estudiant::name($arr_get)
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->leftjoin('materia_pendientes', 'estudiants.id', '=', 'materia_pendientes.estudiant_id')
                ->OrderBy('estudiants.id', 'asc')
                ->wherenull('materia_pendientes.estudiant_id')
                ->where('inscripcions.escolaridad_id','<>',1) // escolaridad diferente a regular
                ->get();

            return view('administracion.materia_pendientes.index',compact('estudiants','search'));
        }
        else{
            $search = '';
            return view('administracion.materia_pendientes.index',compact('search'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\app\Estudiante\MateriaPendiente  $materiaPendiente
     * @return \Illuminate\Http\Response
     */
    public function show(MateriaPendiente $materiaPendiente)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\app\Estudiante\MateriaPendiente  $materiaPendiente
     * @return \Illuminate\Http\Response
     */
    public function edit(MateriaPendiente $materiaPendiente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\app\Estudiante\MateriaPendiente  $materiaPendiente
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MateriaPendiente $materiaPendiente)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\app\Estudiante\MateriaPendiente  $materiaPendiente
     * @return \Illuminate\Http\Response
     */
    public function destroy(MateriaPendiente $materiaPendiente)
    {
        //
    }
}
