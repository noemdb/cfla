<?php

namespace App\Models\app\Profesor;

use App\Models\app\Estudiant;
use App\Models\app\Profesor\Pevaluacion\Evaluacion;
use Illuminate\Database\Eloquent\Model;

class ProfesorGestable extends Model
{
    protected $fillable = ['profesor_id','pevaluacion_id','grupo_estable_id','observations'];

    const COLUMN_COMMENTS = [
        'profesor_id' => 'Profesor',
        'pevaluacion_id' => 'Plan de Estudio',
        'grupo_estable_id' => 'Grupo Estable',
        'observations' => 'Observaciones'
    ];

    public function pevaluacion()
    {
        return $this->belongsTo('App\Models\app\Profesor\Pevaluacion');
    }
    public function profesor()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Profesor');
    }
    public function grupo_estable()
    {
        return $this->belongsTo('App\Models\app\Estudiante\GrupoEstable','grupo_estable_id');
    }

    public function getEvaluacionsAttribute()
    {
        $evaluacions = Evaluacion::select('evaluacions.*')
            ->join('evaluacion_gestables', 'evaluacions.id', '=', 'evaluacion_gestables.evaluacion_id')
            ->join('profesor_gestables', 'evaluacion_gestables.id', '=', 'profesor_gestables.evaluacion_gestable_id')
            ->where('profesor_gestables.id',$this->id)
            ->get();
        return $evaluacions;
    }

    public function getStatusDeleteAttribute()
    {
        return ($this->evaluacions->isEmpty()) ? true:false;
    }

    public function getEstudiantsAttribute()
    {
        //dd($profesor_gestable_id);
        // dd($this);
        $estudiants = Estudiant::select('estudiants.*')
            ->join('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
            ->join('grupo_estables', 'grupo_estables.id', '=', 'inscripcions.grupo_estable_id')
            ->join('profesor_gestables', 'grupo_estables.id', '=', 'profesor_gestables.grupo_estable_id')
            ->wherenull('inscripcions.deleted_at')
            ->Where('profesor_gestables.id', $this->id)
            ->get();
        return $estudiants;
    }
}
