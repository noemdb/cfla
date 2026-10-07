<?php

namespace App\Models\app\Instrument;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pensum;
use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagSession extends Model implements \App\Contracts\Auditable
{
    use HasFactory;

    protected $table = 'diag_sessions';

    protected $fillable = [
        'estudiant_id',
        'pensum_id',
        'iniciado_at',
        'completado_at',
        'progreso',
        'total_preguntas',
        'activo',
        'diag_main_id',
        'lapso_id',
        'status',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * estudiant_id referido por ID, sin datos personales del estudiante.
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'estudiant_id', 'pensum_id', 'iniciado_at', 'completado_at',
            'progreso', 'total_preguntas', 'activo', 'diag_main_id', 'lapso_id', 'status',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $dates = [
        'iniciado_at',
        'completado_at',
    ];

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function diagMain()
    {
        return $this->belongsTo(DiagMain::class, 'diag_main_id');
    }

    public function lapso()
    {
        return $this->belongsTo(Lapso::class, 'lapso_id');
    }

    public function answers()
    {
        return $this->hasMany(DiagAnswer::class, 'session_id');
    }

    /**
     * Diagnósticos de las preguntas contestadas en la sesión.
     *
     * Se usa con with() para resolver `resolvedDiagMain` en la lista paginada
     * sin una consulta por fila (el accessor cae a la columna cuando existe).
     */
    public function answeredQuestions()
    {
        return $this->hasManyThrough(
            DiagQuestion::class,
            DiagAnswer::class,
            'session_id',
            'id',
            'id',
            'question_id'
        );
    }

    /**
     * Diagnóstico resuelto de la sesión.
     *
     * `diag_main_id` no lo persiste `Diagnostic::startDiagnostic` (queda NULL),
     * pero las preguntas contestadas sí lo tienen. Si la columna está vacía se
     * deriva de ellas para no mostrar la sesión sin diagnóstico.
     */
    public function getResolvedDiagMainAttribute(): ?DiagMain
    {
        if ($this->diag_main_id && ($main = $this->diagMain)) {
            return $main;
        }

        if ($this->relationLoaded('answeredQuestions')) {
            $diagMainId = $this->answeredQuestions
                ->pluck('diag_main_id')
                ->filter()
                ->unique()
                ->first();
        } else {
            $diagMainId = $this->answers
                ->pluck('question.diag_main_id')
                ->filter()
                ->unique()
                ->first();
        }

        if (! $diagMainId) {
            // Sin respuestas cargadas: se recurre al diagnóstico de las
            // preguntas del área evaluada.
            $diagMainId = DiagQuestion::where('pensum_id', $this->pensum_id)
                ->whereNotNull('diag_main_id')
                ->value('diag_main_id');
        }

        return $diagMainId ? DiagMain::find($diagMainId) : null;
    }
}
