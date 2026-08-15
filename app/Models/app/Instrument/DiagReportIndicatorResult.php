<?php

namespace App\Models\app\Instrument;

use App\Models\app\Academy\Pensum;
use Illuminate\Database\Eloquent\Model;

class DiagReportIndicatorResult extends Model implements \App\Contracts\Auditable
{
    protected $table = 'diag_report_indicator_results';

    protected $fillable = [
        'report_id',
        'pensum_id',
        'indicator_id',
        'expected_level',
        'observed_level',
        'gap_value',
        'gap_label',
        'teacher_observation',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * teacher_observation excluida por volumen/privacy.
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'report_id', 'pensum_id', 'indicator_id',
            'expected_level', 'observed_level', 'gap_value', 'gap_label',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    public function report()
    {
        return $this->belongsTo(DiagReport::class, 'report_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function indicator()
    {
        return $this->belongsTo(DiagIndicator::class, 'indicator_id');
    }
}
