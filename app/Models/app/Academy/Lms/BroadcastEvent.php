<?php

namespace App\Models\app\Academy\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Auditoría de eventos broadcast (Opción 10). Una fila por evento emitido
 * desde el punto central de emisión (LmsPublicationService). El flag
 * `delivered` se marca por ACK del cliente cuando el navegador recibe el evento.
 */
class BroadcastEvent extends Model implements \App\Contracts\Auditable
{
    protected $table = 'broadcast_events';

    protected $fillable = [
        'event', 'subject_type', 'subject_id', 'actor_user_id',
        'recipient_ids', 'channel_count', 'driver', 'delivered',
    ];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * El modelo ES un log de auditoría de broadcasts; su observer se registra
     * en binnacle por decisión institucional (expansión binnacle) para tener
     * un rastro unificado de la actividad del módulo LMS.
     */
    public function auditableAttributes(): array
    {
        return ['id', 'event', 'subject_type', 'subject_id', 'actor_user_id', 'channel_count', 'driver', 'delivered'];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $casts = [
        'recipient_ids' => 'array',
        'channel_count' => 'integer',
        'delivered' => 'boolean',
    ];

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function markDelivered(): bool
    {
        if ($this->delivered) {
            return true;
        }

        return $this->forceFill(['delivered' => true])->save();
    }
}
