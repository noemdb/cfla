<?php

namespace App\Models\app\Academy;

use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscripcion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tipo_id', 'seccion_id', 'estudiant_id', 'escolaridad_id',
        'programacion_id', 'grupo_estable_id', 'observations'
    ];

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function tipo()
    {
        return $this->belongsTo(Tinscripcion::class, 'tipo_id');
    }

    public function escolaridad()
    {
        return $this->belongsTo(Escolaridad::class, 'escolaridad_id');
    }

    public function programacion()
    {
        return $this->belongsTo(Programacion::class, 'programacion_id');
    }

    public function grupoEstable()
    {
        return $this->belongsTo(GrupoEstable::class, 'grupo_estable_id');
    }
}
