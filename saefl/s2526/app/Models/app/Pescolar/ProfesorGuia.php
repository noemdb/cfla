<?php

namespace App\Models\app\Pescolar;

use Illuminate\Database\Eloquent\Model;

class ProfesorGuia extends Model
{
    protected $fillable = [
        'profesor_id','grado_id','seccion_id','lapso_id','observations'
    ];

    public function profesor()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Profesor');
    }
    public function grado()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Grado');
    }
    public function lapso()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Lapso');
    }
    public function seccion()
    {
        return $this->belongsTo('App\Models\app\Pescolar\Seccion');
    }

    public function getFullNameAttribute()
    {
        $profesor = $this->profesor;
        return ($profesor) ? $profesor->lastname .' ' . $profesor->name : null;
    }
}
