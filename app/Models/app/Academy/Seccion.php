<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $fillable = [
        'grado_id', 'name', 'description', 'amount_student', 'observation', 'status_active', 'comment_final', 'status_inscription_affects'
    ];
    const COLUMN_COMMENTS = [
        'grado_id' => 'Grado del Plan de Estudio',
        'name' => 'Nombre',
        'description' => 'Descripción',
        'amount_student' => 'Cantidad de Estudiantes',
        'observation' => 'Observaciones',
        'status_active' => 'Estado',
        'comment_final' => 'Observaciones Resumen Final',
        'status_inscription_affects' => 'Contabiliza Inscripción'
    ];

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'grado_id');
    }
    public function inscripcions()
    {
        return $this->hasMany(Inscripcion::class, 'grado_id');
    }

    public function debateScore($debate_id)
    {
        return $this->hasMany(Inscripcion::class, 'grado_id');
    }
}
