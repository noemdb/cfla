<?php

namespace App\Models\app\Academy;

use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_id', 'seccion_id', 'estudiant_id', 'escolaridad_id', 'programacion_id', 'grupo_estable_id', 'observations'
    ];

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }
    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }
}
