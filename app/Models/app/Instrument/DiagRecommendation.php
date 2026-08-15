<?php

namespace App\Models\app\Instrument;

use App\Models\app\Academy\Pensum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class DiagRecommendation extends Model implements \App\Contracts\Auditable
{
    protected $table = 'diag_recommendations';

    protected $fillable = [
        'report_id',
        'pensum_id',
        'type',
        'recommendation',
        'priority',
        'suggested_frequency',
        'active',
        'assigned_to',
        'started_at',
        'completed_at',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * recommendation (texto libre) excluida por volumen.
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'report_id', 'pensum_id', 'type', 'priority',
            'suggested_frequency', 'active', 'assigned_to', 'started_at', 'completed_at',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $dates = [
        'started_at',
        'completed_at',
    ];

    public function report()
    {
        return $this->belongsTo(DiagReport::class, 'report_id');
    }

    public function pensum()
    {
        return $this->belongsTo(Pensum::class, 'pensum_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
