<?php

namespace App\Models\app\Instrument;

use Illuminate\Database\Eloquent\Model;

class DiagResult extends Model implements \App\Contracts\Auditable
{
    protected $table = 'diag_results';

    protected $fillable = [
        'report_id',
        'total_answered_questions',
        'precision',
        'open_ended_response_level',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     */
    public function auditableAttributes(): array
    {
        return ['id', 'report_id', 'total_answered_questions', 'precision', 'open_ended_response_level'];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    public function report()
    {
        return $this->belongsTo(DiagReport::class, 'report_id');
    }
}
