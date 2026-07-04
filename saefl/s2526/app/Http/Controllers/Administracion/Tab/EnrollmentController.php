<?php

namespace App\Http\Controllers\Administracion\Tab;

use App\Http\Controllers\Controller;
use App\Models\app\Estudiant;
use Illuminate\Http\Request;
use App\Models\app\Estudiante\Enrollment;
use App\Models\app\Estudiante\GrupoEstable;
use App\Models\app\Pescolar\Grado;
use App\Models\app\Pescolar\Pestudio;
use App\Models\app\Pescolar\Seccion;
use Illuminate\Support\Facades\Session;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function token(Request $request)
    {
        $grado_id       = (!empty($request->grado_id)) ? $request->grado_id : null;
        $grado          = ($grado_id) ? Grado::find($grado_id) : null;
        $seccion_id     = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $seccion        = ($seccion_id) ? Seccion::find($seccion_id) : null;
        $estudiants     = collect([]);
        $list_comment   = Estudiant::COLUMN_COMMENTS;

        if (count($request->all())>0) {

            $estudiants = Estudiant::select('estudiants.*')
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                //->where('estudiants.status_active','true')
            ;

            $estudiants = ($grado_id) ? $estudiants->where('grados.id',$grado_id) : $estudiants ;
            $estudiants = ($seccion_id) ? $estudiants->where('seccions.id',$seccion_id) : $estudiants ;

            $estudiants = $estudiants->get();
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();

        $compact = ['estudiants','list_grado','grado_id','grado','list_seccion','grado','seccion','seccion_id','list_comment']; //dd($list_grado);
        return view('administracion.enrollments.token',compact($compact));
    }
    public function index(Request $request)
    {
        $grado_id       = (!empty($request->grado_id)) ? $request->grado_id : null;
        $grado          = ($grado_id) ? Grado::find($grado_id) : null;
        $seccion_id     = (!empty($request->seccion_id)) ? $request->seccion_id: null;
        $seccion        = ($seccion_id) ? Seccion::find($seccion_id) : null;
        $estudiants     = collect([]);
        $list_comment   = Estudiant::COLUMN_COMMENTS;

        if (count($request->all())>0) {

            $estudiants = Estudiant::select('estudiants.*')
                ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
                ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
                ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
                ->join('grados', 'grados.id', '=', 'seccions.grado_id')
                //->where('estudiants.status_active','true')
            ;

            $estudiants = ($grado_id) ? $estudiants->where('grados.id',$grado_id) : $estudiants ;
            $estudiants = ($seccion_id) ? $estudiants->where('seccions.id',$seccion_id) : $estudiants ;

            $estudiants = $estudiants->get();
        }

        $list_grado = Grado::list_pestudio_grado();
        $list_seccion = ($grado_id) ? Seccion::Where('grado_id',$grado_id)->pluck('name', 'id') : array();

        $compact = ['estudiants','list_grado','grado_id','grado','list_seccion','grado','seccion','seccion_id','list_comment']; //dd($list_grado);
        return view('administracion.enrollments.index',compact($compact));
    }

    public function crud(Request $request)
    {
        $pestudio_id = (!empty($request->pestudio_id)) ? $request->pestudio_id: null; //dd($pestudio_id);
        $grado_id = (!empty($request->grado_id)) ? $request->grado_id : null ;
        $grupo_estable_id = (!empty($request->grupo_estable_id)) ? $request->grupo_estable_id : null ;
        $ci = (!empty($request->ci)) ? $request->ci : null  ;
        $name = (!empty($request->ci)) ? $request->name : null  ;
        $enrollments = collect();

        if ($request->all()) {

            $enrollments = Enrollment::select('enrollments.*')
            ->join('grados', 'grados.id', '=', 'enrollments.grado_id')
            ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
            ;
            // $enrollments = ($pestudio_id) ? $enrollments->where('enrollments.pestudio_id',$pestudio_id) : $enrollments ;
            $enrollments = ($grado_id) ? $enrollments->where('enrollments.grado_id',$grado_id) : $enrollments ;
            $enrollments = ($pestudio_id) ? $enrollments->where('pestudios.id',$pestudio_id) : $enrollments ;

            if ($ci) {
                $enrollments = $enrollments->where(function ($query) use ($ci) {
                    $query->where('enrollments.ci_estudiant', 'like', "%".$ci."%")
                        ->orWhere('enrollments.ci_representant', 'like', "%".$ci."%");
                });
            }

            if ($name) {
                $enrollments = $enrollments->where(function ($query) use ($name) {
                    $query->where('enrollments.name', 'like', "%".$name."%")
                        ->orWhere('enrollments.lastname', 'like', "%".$name."%")
                        ->orWhere('enrollments.name_representant', 'like', "%".$name."%");
                });
            }

            $enrollments = $enrollments->get();
        }

        /*******************list****************************/
        $list_pestudio = Pestudio::list_pestudio();
        $list_grado = Grado::list_pestudio_grado();
        $list_grupo_estables = GrupoEstable::list_grupo_estable_full_inscriptions();
        $list_comment = Enrollment::COLUMN_COMMENTS;

        $compact = [
            'enrollments',
            'grado_id',
            'pestudio_id',
            'grupo_estable_id',
            'ci',
            'name',
            'list_pestudio',
            'list_grado',
            'list_grupo_estables',
            'list_comment'
        ];

        return view('administracion.enrollments.crud',
        compact($compact));
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
     * @param  \App\Models\app\Estudiante\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function show(Enrollment $enrollment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\app\Estudiante\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function edit(Enrollment $enrollment)
    {
        // dd($enrollment);
        // return redirect()->route('administracion.enrollments.edit',$enrollment);
        return view('administracion.enrollments.edit',compact('enrollment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\app\Estudiante\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        $inscripcion = Enrollment::findOrFail($enrollment->id);

        $inscripcion->fill($request->all());

        $inscripcion->save();

        $grupo_estable = ($inscripcion->grupo_estable) ? $inscripcion->grupo_estable->name : null;

        $messenge = trans('db_oper_result.update_ok');

        if($request->ajax()){
            return response()->json([
                "messenge"=>$messenge,
                "grupo_estable"=>$grupo_estable,
            ]);
        }

        Session::flash('operp_ok',$messenge);

        Session::flash('class_oper','success');

        // return redirect()->route('administracion.inscripciones.edit',$id);
        return redirect()->route('administracion.enrollments.crud');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\app\Estudiante\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Enrollment $enrollment)
    {
        //
    }
}
