<?php

namespace App\Models\app\Profesor\Pevaluacion;

use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    public function pevaluacions()
    {
        return $this->hasMany('App\Models\app\Profesor\Pevaluacion');
    }
    public function evaluacions()
    {
        return $this->hasMany('App\Models\app\Profesor\Pevaluacion\Evaluacion');
    }
}
