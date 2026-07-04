<?php

namespace App\Models\app\Pescolar;

use App\Models\app\Estudiant;
use App\Models\app\Estudiante\Boletin;
use App\Models\app\Profesor\Pevaluacion;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// use Illuminate\Database\Eloquent\SoftDeletes;

class AreaConocimiento extends Model
{
    // use SoftDeletes;
    protected $fillable = [
        'peducativo_id','pestudio_id','leader_id','name','code','code_sm','description','observations','order','enable_academic_index'
    ];
    const COLUMN_COMMENTS = [
        'peducativo_id' => 'Plan Educativo',
        'pestudio_id' => 'Plan Estudio',
        'leader_id' => 'Jefe del Área',
        'name' => 'Nombre',
        'code' => 'Código',
        'code_sm' => 'Abreviatura',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'order' => 'Número de orden de presentación',
        'enable_academic_index' => 'Tomada en cuenta para índice o promedio académico',
    ];
    public function peducativo()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Peducativo');
    }
    public function pestudio()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Pestudio');
    }    
    public function campo_conocimientos()
    {
        return $this->hasMany('App\Models\app\Pescolar\CampoConocimiento');
    }
    public function leader()
    {
        return $this->belongsTo(User::class,'leader_id');
    }  
    /******************************************************************************************************/

    public function getProfesorsIEEsPROM($lapso_id=null)
    {
        $promedio =
            Boletin::join('evaluacions','boletins.evaluacion_id','=','evaluacions.id')
                ->join('pevaluacions','evaluacions.pevaluacion_id','=','pevaluacions.id')
                ->join('profesors','pevaluacions.profesor_id','=','profesors.id')
                ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
                ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
                ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
                ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
                ->where('area_conocimientos.id',$this->id)
                ->wherenull('pensums.deleted_at')
                ->wherenull('pevaluacions.deleted_at')
                ->groupBy('profesors.id')
                ->selectRaw('COUNT(*) as cnt')
                ;
        $promedio = (!empty($lapso_id)) ? $promedio->where('pevaluacions.lapso_id',$lapso_id) : $promedio  ;
        $promedio = $promedio->get()->avg('cnt'); //dd($promedio);
        return $promedio;
    }

    public function estudiants($lapso_id=null)
    {
        $lapso = Lapso::find($lapso_id) ; //dd($lapso);
        // $inscripcions = AreaConocimiento::select('area_conocimientos.name')
        $estudiants = Estudiant::select('estudiants.ci_estudiant')
        // ->selectRaw('count(estudiants.id) as value')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('seccions', 'seccions.id', '=', 'inscripcions.seccion_id')
            ->join('grados', 'grados.id', '=', 'seccions.grado_id')
            ->join('pensums', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
        
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')

            ->Where('area_conocimientos.id', '=', $this->id)
            
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->where('estudiants.status_active','true')

            ->wherenull('inscripcions.deleted_at')           
            ->wherenull('planpagos.deleted_at')           
            ->wherenull('pensums.deleted_at')           
            ->wherenull('asignaturas.deleted_at')           
            ->wherenull('grados.deleted_at')
            ->groupby('estudiants.id')           
            ;

        $estudiants = (!empty($lapso)) ? $estudiants->where('inscripcions.created_at','<=',$lapso->ffinal) : $estudiants  ;
        $estudiants = $estudiants->get();
        return $estudiants;
    }

    public function inscritos($lapso_id=null)
    {
        $lapso = Lapso::find($lapso_id) ; //dd($lapso);
        $inscripcions = AreaConocimiento::select('area_conocimientos.name')

            ->selectRaw('count(estudiants.id) as value')

            ->join('campo_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('pensums', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')

            ->join('seccions', 'seccions.grado_id', '=', 'grados.id')
            ->join('inscripcions', 'inscripcions.seccion_id', '=', 'seccions.id')
            ->join('estudiants', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('administrativas', 'estudiants.id', '=', 'administrativas.estudiant_id')
            ->join('planpagos', 'planpagos.id', '=', 'administrativas.planpago_id')

            ->Where('area_conocimientos.id', '=', $this->id)
            
            ->where('grados.status_active','true')
            ->where('seccions.status_active','true')
            ->where('planpagos.status_inscription_affects','true')
            ->where('planpagos.status_active','true')
            ->where('estudiants.status_active','true')

            ->wherenull('inscripcions.deleted_at')           
            ->wherenull('planpagos.deleted_at')           
            ->wherenull('pensums.deleted_at')           
            ->wherenull('asignaturas.deleted_at')           
            ->wherenull('grados.deleted_at')           
            ;

        $inscripcions = (!empty($lapso)) ? $inscripcions->where('inscripcions.created_at','<=',$lapso->ffinal) : $inscripcions  ;
        $inscripcions = $inscripcions->groupby('estudiants.id')->first();
        return ($inscripcions) ? $inscripcions : 0;
    } 

    public function getPevaluacions($lapso_id=null)
    {
        $pevaluacions =
            Pevaluacion::select('pevaluacions.*')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('grados', 'grados.id', '=', 'pensums.grado_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

            ->where('area_conocimientos.id',$this->id)
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->groupBy('asignaturas.id')
            ->orderBy('grados.code_sm');

        $pevaluacions = (!empty($lapso_id)) ? $pevaluacions->where('pevaluacions.lapso_id',$lapso_id) : $pevaluacions  ;

        $pevaluacions = $pevaluacions->get(); //dd($pevaluacions);

        return $pevaluacions;
    }

    public function getEvaluacions($lapso_id=null)
    {
        $evaluacions =
            Evaluacion::select('evaluacions.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

            ->where('area_conocimientos.id',$this->id)
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at');

        $evaluacions = (!empty($lapso_id)) ? $evaluacions->where('pevaluacions.lapso_id',$lapso_id) : $evaluacions  ;

        $evaluacions = $evaluacions->get();

        return $evaluacions;
    }

    public function getProfesorEvaluacions($lapso_id=null)
    {
        $profesors = Profesor::select('profesors.*')
            ->join('pevaluacions', 'profesors.id', '=', 'pevaluacions.profesor_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

            ->Where('area_conocimientos.id', '=', $this->id)

            ->where('profesors.status_active','true')
            ->wherenull('pevaluacions.deleted_at')
            ->groupBy('profesors.id');

            $profesors = (!empty($lapso_id)) ? $profesors->where('pevaluacions.lapso_id',$lapso_id) : $profesors  ;
            $profesors = $profesors->get();
        return $profesors;
    }

    public function getBoletins($lapso_id=null)
    {
        $boletins = Boletin::select('boletins.*')
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

            ->Where('area_conocimientos.id', '=', $this->id)

            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('asignaturas.deleted_at')
            ->groupBy('boletins.id');

        $boletins = (!empty($lapso_id)) ? $boletins->where('pevaluacions.lapso_id',$lapso_id) : $boletins  ;

        $boletins = $boletins->get();

        return $boletins;
    }


    public function getFullNameAttribute()
    {
        return  $this->name .' [' . $this->pestudio->code.']';
    }

    public function getCheckIn($asignatura_id)
    {
        $check = $this->campo_conocimientos->where('asignatura_id',$asignatura_id)->first();
        // dd($check);
        return ( empty($check) ) ? false : true ;
    }

    public function getStatusDeleteAttribute()
    {
        return ( empty($this->campo_conocimientos->count()) ) ? true : false ;
    }

    public function getPromedio($lapso_id=null)
    {
        $count =
            Boletin::select(
                DB::raw('count(boletins.id) as value'),
                DB::raw('sum(boletins.nota) as sum_nota')
            )
            ->join('evaluacions', 'evaluacions.id', '=', 'boletins.evaluacion_id')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')
            ->where('area_conocimientos.id',$this->id)
            ->wherenotnull('boletins.nota')
            ->wherenull('evaluacions.deleted_at')
            ->wherenull('pevaluacions.deleted_at')
            ->wherenull('pensums.deleted_at')
            ->groupby('area_conocimientos.id');

        $count = (!empty($lapso_id)) ? $count->where('pevaluacions.lapso_id',$lapso_id) : $count  ;

        $count = $count->first();

        return ($count) ? round(($count->sum_nota/$count->value),2) : null;
    }

    public function getGradosAttribute()
    {
        $grados =
            Grado::select('grados.*')
                ->join('pestudios', 'pestudios.id', '=', 'grados.pestudio_id')
                ->join('area_conocimientos', 'pestudios.id', '=', 'area_conocimientos.pestudio_id')

                ->where('area_conocimientos.id',$this->id)
                ->whereNull('pestudios.deleted_at')
                ->whereNull('area_conocimientos.deleted_at')

                // ->groupby('grados.id')
                ->get();

        return $grados;
    }

    public static function getEvaluacionsForLeader($leader_id,$lapso_id=null)
    {
        $evaluacions =
            Evaluacion::select('evaluacions.*')
            ->join('pevaluacions', 'pevaluacions.id', '=', 'evaluacions.pevaluacion_id')
            ->join('pensums', 'pensums.id', '=', 'pevaluacions.pensum_id')
            ->join('asignaturas', 'asignaturas.id', '=', 'pensums.asignatura_id')
            ->join('campo_conocimientos', 'asignaturas.id', '=', 'campo_conocimientos.asignatura_id')
            ->join('area_conocimientos', 'area_conocimientos.id', '=', 'campo_conocimientos.area_conocimiento_id')

            ->where('area_conocimientos.leader_id',$leader_id)
            ->wherenull('pensums.deleted_at')
            ->wherenull('pevaluacions.deleted_at');

        $evaluacions = (!empty($lapso_id)) ? $evaluacions->where('pevaluacions.lapso_id',$lapso_id) : $evaluacions  ;

        $evaluacions = $evaluacions->get();

        return $evaluacions;
    }
    
}
