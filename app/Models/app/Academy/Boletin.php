<?php

namespace App\Models\app\Academy;

use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Boletin extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'estudiant_id', 'evaluacion_id', 'nota', 'ajuste', 'user_id', 'observations',
    ];

    protected $table = 'boletins';

    const COLUMN_COMMENTS = [
        'estudiant_id' => 'Estudiante',
        'evaluacion_id' => 'Evaluación',
        'nota' => 'Nota',
        'ajuste' => 'Ajuste',
        'user_id' => 'Usuario',
        'observations' => 'Observaciones',
    ];

    // ─── RELACIONES ──────────────────────────────────────────────

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }

    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class, 'evaluacion_id');
    }

    // ─── ACCESSORS ───────────────────────────────────────────────

    public function getPevaluacionAttribute()
    {
        return $this->evaluacion?->pevaluacion;
    }

    public function getPensumAttribute()
    {
        return $this->evaluacion?->pevaluacion?->pensum;
    }

    public function getGradoAttribute()
    {
        return $this->evaluacion?->pevaluacion?->grado;
    }

    public function getSeccionAttribute()
    {
        return $this->evaluacion?->pevaluacion?->seccion;
    }

    public function getProfesorAttribute()
    {
        return $this->evaluacion?->pevaluacion?->profesor;
    }
}
