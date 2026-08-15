<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model implements \App\Contracts\Auditable
{
    use HasFactory;

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     */
    public function auditableAttributes(): array
    {
        return ['id', 'activity_id', 'name', 'weighting', 'status_quantitative_weighting'];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $fillable = [
        'activity_id', 'name', 'weighting', 'status_quantitative_weighting',
    ];

    const COLUMN_COMMENTS = [
        'activity_id' => 'Actividad',
        'name' => 'Nombre del indicador',
        'weighting' => 'Ponderación',
        'status_quantitative_weighting' => 'El indicador es ponderado (cuantitativo)',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
