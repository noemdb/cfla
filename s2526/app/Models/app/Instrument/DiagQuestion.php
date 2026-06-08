<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Pescolar\Pensum;

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
        'diag_main_id',
        'competency_id',
        'indicator_id',
    ];

    // 🔹 Relaciones
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
