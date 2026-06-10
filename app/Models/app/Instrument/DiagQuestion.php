<?php

namespace App\Models\app\Instrument;

use App\Models\app\Academy\Pensum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagQuestion extends Model
{
    use HasFactory;

    protected $table = 'diag_questions';

    protected $fillable = [
        'pensum_id',
        'pregunta',
        'tipo_pregunta',
        'orden',
        'difficulty',
        'weighing',
        'activo',
        'diag_main_id',
        'competency_id',
        'indicator_id',
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

    public function diagMain()
    {
        return $this->belongsTo(DiagMain::class, 'diag_main_id');
    }

    public function competency()
    {
        return $this->belongsTo(DiagCompetency::class, 'competency_id');
    }

    public function indicator()
    {
        return $this->belongsTo(DiagIndicator::class, 'indicator_id');
    }

    public function getAsignaturaNameAttribute()
    {
        return $this->pensum && $this->pensum->asignatura
            ? $this->pensum?->asignatura->full_name
            : null;
    }
}
