<?php

namespace App\Models\app\Academy\Lms;

use App\Models\app\Academy\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityComment extends Model implements \App\Contracts\Auditable
{
    use SoftDeletes;

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * body (comentario del estudiante) excluido por volumen/privacy;
     * solo el flujo de aprobación y metadatos.
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'activity_id', 'user_id',
            'is_approved', 'approved_at', 'approved_by',
            'rejected_at', 'rejected_by', 'rejected_reason',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $fillable = [
        'activity_id', 'user_id', 'body',
        'is_approved', 'approved_at', 'approved_by',
        'rejected_at', 'rejected_by', 'rejected_reason',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // ─── SCOPES ────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('is_approved', false)
            ->whereNull('rejected_at')
            ->whereNull('deleted_at');
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true)
            ->whereNull('rejected_at');
    }

    public function scopeRejected($query)
    {
        return $query->whereNotNull('rejected_at');
    }

    public function scopeForActivity($query, int $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    // ─── ACTIONS ───────────────────────────────────────────────────

    public function approve(int $userId): void
    {
        $this->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);
    }

    public function reject(int $userId, ?string $reason = null): void
    {
        $this->update([
            'is_approved' => false,
            'rejected_at' => now(),
            'rejected_by' => $userId,
            'rejected_reason' => $reason,
        ]);
    }
}
