<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pevaluacion_id', 'escala_id', 'fecha', 'objetivo', 'description', 'observations', 'status_execution',
    ];

    protected $table = 'evaluacions';

    protected $casts = [
        'fecha' => 'date',
        'status_execution' => 'boolean',
    ];

    const COLUMN_COMMENTS = [
        'pevaluacion_id' => 'Carga Académica',
        'escala_id' => 'Escala',
        'fecha' => 'Fecha',
        'objetivo' => 'Objetivo',
        'description' => 'Descripción',
        'observations' => 'Observaciones',
        'status_execution' => 'Estado de Ejecución',
    ];

    // ─── RELACIONES ──────────────────────────────────────────────

    public function pevaluacion()
    {
        return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');
    }

    public function escala()
    {
        return $this->belongsTo(Escala::class, 'escala_id');
    }

    public function boletins()
    {
        return $this->hasMany(Boletin::class, 'evaluacion_id');
    }
}
