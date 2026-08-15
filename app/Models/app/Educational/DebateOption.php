<?php

namespace App\Models\app\Educational;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebateOption extends Model implements \App\Contracts\Auditable
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'text',
        'observation',
        'status_option_correct',
        'status_wrong_answer',
        'attachment',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'question_id', 'text', 'observation',
            'status_option_correct', 'status_wrong_answer', 'attachment',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    const COLUMN_COMMENTS = [
        'question_id' => 'Pregunta',
        'text' => 'Texto',
        'observation' => 'Observación adicional',
        'status_option_correct' => 'Opción correcta',
        'status_wrong_answer' => 'Opción erronea seleccionada',
        'attachment' => 'Archivo adjunto',
    ];

    // Relación
    public function answers()
    {
        return $this->hasMany(DebateAnswer::class, 'option_id');
    }

    public function question()
    {
        return $this->belongsTo(DebateQuestion::class, 'question_id');
    }

    // Scope para obtener las opciones activas
    public function scopeActive($query)
    {
        return $query->whereHas('question', function ($q) {
            $q->where('status_active', true);
        });
    }

    // Scope para obtener las opciones inactivas
    public function scopeInactive($query)
    {
        return $query->whereHas('question', function ($q) {
            $q->where('status_active', false);
        });
    }

    public function getAttachmentUrlAttribute()
    {
        return ($this->attachment) ? asset('storage/educationals/'.$this->attachment) : null;
    }

    public static function option_correct($question_id)
    {
        return DebateOption::where('question_id', $question_id)->where('status_option_correct', true)->orderBy('created_at', 'desc')->first();
    }

    public static function ActiveCompetitionId($CompetitionId = null)
    {
        return DebateOption::query()
            ->select('debate_options.*')
            ->join('debate_questions', 'debate_questions.id', '=', 'debate_options.question_id')
            ->join('debates', 'debates.id', '=', 'debate_questions.debate_id')
            ->join('debate_competitions', 'debate_competitions.id', '=', 'debates.competition_id')
            ->where('debate_competitions.id', $CompetitionId)
            ->where('debate_competitions.status_active', true)
            ->where('debates.status_active', true)
            ->where('debate_questions.status_active', true)
            ->orderby('debates.created_at')
            ->get();
    }
}
