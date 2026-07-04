<?php

namespace App\Models\app\Estudiante;

use Illuminate\Database\Eloquent\Model;

class BoletinAjuste extends Model
{
    protected $fillable = [
        'pevaluacion_id','estudiant_id','user_id','ajuste','description'
    ];

    public function pevaluacion()
    {
        return $this->belongsTo('App\Models\app\Profesor\Pevaluacion');
    }

    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
}
