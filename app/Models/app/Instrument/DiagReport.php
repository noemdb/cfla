<?php

namespace App\Models\app\Instrument;

use App\Models\app\Academy\Lapso;
use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Model;

class DiagReport extends Model implements \App\Contracts\Auditable
{
    protected $table = 'diag_reports';

    protected $fillable = [
        'estudiant_id',
        'diag_main_id',
        'referent_id',
        'lapso_id',
        'status',
        'generated_at',
        'validated_at',
        'global',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * estudiant_id referido por ID, sin datos personales del estudiante.
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'estudiant_id', 'diag_main_id', 'referent_id',
            'lapso_id', 'status', 'generated_at', 'validated_at', 'global',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $dates = [
        'generated_at',
        'validated_at',
    ];

    public function estudiant()
    {
        return $this->belongsTo(Estudiant::class, 'estudiant_id');
    }

    public function diagMain()
    {
        return $this->belongsTo(DiagMain::class, 'diag_main_id');
    }

    public function referent()
    {
        return $this->belongsTo(DiagReferent::class, 'referent_id');
    }

    public function lapso()
    {
        return $this->belongsTo(Lapso::class, 'lapso_id');
    }

    public function results()
    {
        return $this->hasOne(DiagResult::class, 'report_id');
    }

    public function pensumResults()
    {
        return $this->hasMany(DiagReportPensum::class, 'report_id');
    }

    public function indicatorResults()
    {
        return $this->hasMany(DiagReportIndicatorResult::class, 'report_id');
    }

    public function drafts()
    {
        return $this->hasMany(DiagReportAiDraft::class, 'report_id');
    }

    public function latestDraft()
    {
        return $this->hasOne(DiagReportAiDraft::class, 'report_id')->latestOfMany();
    }
}
