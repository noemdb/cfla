<?php

namespace App\Models\app\Academy\Lms;

use App\Models\app\Academy\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsActivityPublication extends Model implements \App\Contracts\Auditable
{
    use HasFactory;

    protected $table = 'lms_activity_publications';

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     */
    public function auditableAttributes(): array
    {
        return [
            'id', 'activity_id', 'published_by', 'status',
            'publish_at', 'unpublish_at', 'published_at',
            'allow_comments', 'allow_downloads', 'notes',
        ];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected static function newFactory()
    {
        return \Database\Factories\LmsActivityPublicationFactory::new();
    }

    protected $fillable = [
        'activity_id', 'published_by', 'status',
        'publish_at', 'unpublish_at', 'published_at',
        'allow_comments', 'allow_downloads', 'notes',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'unpublish_at' => 'datetime',
        'published_at' => 'datetime',
        'allow_comments' => 'boolean',
        'allow_downloads' => 'boolean',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Estado de la publicación desde el punto de vista del estudiante.
     *
     * Solo `PUBLISHED` es visible para los estudiantes; una `SCHEDULED`
     * (aún programada, sin publicar por un responsable) queda oculta.
     *
     * - 'hidden'  → no visible (no está publicada, publish_at nulo o expirada).
     * - 'preview' → publicada pero ahora() < publish_at → solo la 1ª sección.
     * - 'full'    → publicada y ahora() >= publish_at → visible completa.
     */
    public function studentVisibility(): string
    {
        if ($this->status !== 'PUBLISHED') {
            return 'hidden';
        }
        if ($this->publish_at === null) {
            return 'hidden';
        }
        if ($this->unpublish_at && now()->gt($this->unpublish_at)) {
            return 'hidden';
        }

        return now()->lt($this->publish_at) ? 'preview' : 'full';
    }

    /**
     * La lección es accesible para los estudiantes (en vista previa o completa).
     * Solo las PUBLISHED son visibles; una SCHEDULED queda oculta hasta que un
     * responsable (Jefe de Área, Coordinación o Planificación) la publique.
     */
    public function isVisibleToStudents(): bool
    {
        return $this->studentVisibility() !== 'hidden';
    }

    /**
     * Solo la primera sección es visible (PUBLISHED y now() < publish_at).
     */
    public function isPreviewToStudents(): bool
    {
        return $this->studentVisibility() === 'preview';
    }

    /**
     * La lección es visible completa (PUBLISHED y now() >= publish_at).
     */
    public function isFullVisibleToStudents(): bool
    {
        return $this->studentVisibility() === 'full';
    }

    public function scopeVisibleNow($query)
    {
        return $query->where('status', 'PUBLISHED')
            ->whereNotNull('publish_at')
            ->where(fn ($q) => $q->whereNull('unpublish_at')->orWhere('unpublish_at', '>=', now()));
    }
}
