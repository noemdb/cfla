<?php

namespace App\Models\app\Academy\Lms;

use App\Models\app\Academy\Activity;
use App\Services\Lms\LmsContentClassifier;
use Illuminate\Database\Eloquent\Model;

class LmsActivitySection extends Model implements \App\Contracts\Auditable
{
    protected $table = 'lms_activity_sections';

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     */
    public function auditableAttributes(): array
    {
        return ['id', 'activity_id', 'title', 'description', 'sort_order', 'is_visible', 'content_type'];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $fillable = [
        'activity_id',
        'title',
        'description',
        'sort_order',
        'is_visible',
        'content_type',
    ];

    protected $casts = ['is_visible' => 'boolean'];

    /** Tipos de contenido de sección (Spec "Campo content_type"). */
    public const CONTENT_TYPES = LmsContentClassifier::SECTION_TYPES;

    /** Etiquetas legibles por tipo (UI, badges). */
    public const CONTENT_TYPE_LABELS = LmsContentClassifier::SECTION_TYPE_LABELS;

    /**
     * La columna `content_type` es una caché derivada de los contenidos
     * visibles. Si está vacía (drift/legacy), se calcula en vivo.
     */
    public function getContentTypeAttribute(?string $cached): ?string
    {
        if ($cached !== null) {
            return $cached;
        }

        return app(LmsContentClassifier::class)->classifySection($this->visibleContents);
    }

    /** Etiqueta legible del tipo (o null si no hay tipo). */
    public function getContentTypeLabelAttribute(): ?string
    {
        $type = $this->content_type;

        return $type ? (self::CONTENT_TYPE_LABELS[$type] ?? ucfirst($type)) : null;
    }

    public function contents()
    {
        return $this->hasMany(LmsActivityContent::class, 'section_id')
            ->orderBy('sort_order');
    }

    public function visibleContents()
    {
        return $this->hasMany(LmsActivityContent::class, 'section_id')
            ->where('is_visible', true)
            ->orderBy('sort_order');
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
