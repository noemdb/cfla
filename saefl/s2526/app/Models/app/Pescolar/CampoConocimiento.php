<?php

namespace App\Models\app\Pescolar;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class CampoConocimiento extends Model
{
    // use SoftDeletes;
    protected $fillable = [
        'area_conocimiento_id','asignatura_id','observations'
    ];
    const COLUMN_COMMENTS = [
        'area_conocimiento_id' => 'Área de Conocimiento',
        'asignatura_id' => 'Asignatura',
        'observations' => 'Observaciones',
    ];

    public function area_conocimiento()
    {
        return $this->belongsTo('App\Models\app\Pescolar\AreaConocimiento');
    }
    public function asignatura()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Asignatura');
    }

    public function areaConocimiento()
    {
        return $this->belongsTo(AreaConocimiento::class);
    }

}
