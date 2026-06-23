<?php

namespace App\Models\app\Profesor\Pevaluacion;

use Illuminate\Database\Eloquent\Model;

class Ecualitativa extends Model
{
    protected $fillable = [
        'estudiant_id','lapso_id','name','description','observations'
    ];

    const COLUMN_COMMENTS = [
        'estudiant_id' => 'Estudiante',
        'lapso_id' => 'Lapso',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'observations' => 'Observación',
    ];

    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }
    public function lapso()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Lapso');
    }
}
