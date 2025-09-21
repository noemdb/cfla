<?php

namespace App\Models\app\Instrument;

use App\Models\app\Academy\Pensum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagQuestion extends Model
{
    protected $table = 'diag_questions';

    protected $fillable = [
        'pensum_id',
        'pregunta',
        'tipo_pregunta',
        'orden',
        'difficulty',
        'weighing',
        'activo',
    ];

    // 🔹 Relaciones
    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function options()
    {
        return $this->hasMany(DiagOption::class, 'question_id');
    }

    public function answers()
    {
        return $this->hasMany(DiagAnswer::class, 'question_id');
    }

    public function getAsignaturaNameAttribute()
    {
        return $this->pensum && $this->pensum->asignatura
            ? $this->pensum?->asignatura->full_name
            : null;
    }
}
