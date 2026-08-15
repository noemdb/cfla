<?php

namespace App\Models\app\Academy\Lms;

use App\Models\app\Academy\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LmsActivityLog extends Model implements \App\Contracts\Auditable
{
    protected $table = 'lms_activity_logs';

    public $timestamps = false;

    protected $fillable = [
        'activity_id', 'user_id', 'event',
        'context_id', 'context_type', 'ip_address',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * El propio log del LMS no se audita como negocio: es una tabla de
     * telemetría; registrarla en binnacle duplicaría el rastro. Solo se
     * expone el contrato por si una integración futura decide usarlo.
     */
    public function auditableAttributes(): array
    {
        return ['id', 'activity_id', 'user_id', 'event', 'context_id', 'context_type'];
    }

    public function maskedAuditFields(): array
    {
        return ['ip_address'];
    }

    protected $casts = ['created_at' => 'datetime'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record(
        int $activityId,
        int $userId,
        string $event,
        ?int $contextId = null,
        ?string $contextType = null
    ): void {
        static::create([
            'activity_id' => $activityId,
            'user_id' => $userId,
            'event' => $event,
            'context_id' => $contextId,
            'context_type' => $contextType,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
