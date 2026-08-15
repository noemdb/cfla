<?php

namespace App\Models\app\Academy\Lms;

use App\Models\app\Academy\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LmsActivityProgress extends Model implements \App\Contracts\Auditable
{
    protected $table = 'lms_activity_progress';

    protected $fillable = [
        'activity_id',
        'student_id',
        'status',
        'completion_pct',
        'time_spent_secs',
        'first_access_at',
        'last_access_at',
        'completed_at',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * Progreso por estudiante: metadatos de avance, sin contenido de lección.
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'activity_id', 'student_id', 'status', 'completion_pct',
            'time_spent_secs', 'first_access_at', 'last_access_at', 'completed_at',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $casts = [
        'completion_pct' => 'decimal:2',
        'time_spent_secs' => 'integer',
        'first_access_at' => 'datetime',
        'last_access_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
