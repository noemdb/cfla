<?php

namespace App\Models\app\Academy;

use Illuminate\Database\Eloquent\Model;

/**
 * PLAN-ACTIVITIES-001 §2 — Información complementaria de una actividad
 * (1:1 con Activity): texto en Markdown + imagen local (JPG).
 */
class ActivitySupplement extends Model implements \App\Contracts\Auditable
{
    protected $table = 'activity_supplements';

    protected $fillable = ['activity_id', 'text', 'image_url'];

    protected $casts = [
        'image_url' => 'string',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     */
    public function auditableAttributes(): array
    {
        return ['id', 'activity_id', 'text', 'image_url'];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }
}
