<?php

namespace App\Models\app\Profesor;

use Illuminate\Database\Eloquent\Model;

class EvaluacionGestable extends Model
{
    protected $fillable = ['profesor_gestable_id','evaluacion_id'];

    public function profesor_gestable()
    {
        return $this->belongsTo('App\Models\app\Profesor\ProfesorGestable','profesor_gestable_id');
    }
    public function evaluacion()
    {
        return $this->belongsTo('App\Models\app\Profesor\Pevaluacion\Evaluacion');
    }
}
