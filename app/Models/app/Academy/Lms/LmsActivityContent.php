<?php

namespace App\Models\app\Academy\Lms;

use Illuminate\Database\Eloquent\Model;

class LmsActivityContent extends Model implements \App\Contracts\Auditable
{
    protected $table = 'lms_activity_contents';

    public const TYPES = ['TEXT', 'VIDEO', 'AUDIO', 'IMAGE', 'PRESENTATION', 'HTML', 'EMBED', 'FILE_PREVIEW'];

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     * body (HTML) excluido por volumen; solo metadatos del contenido.
     */
    public function auditableAttributes(): array
    {
        return ['id', 'section_id', 'type', 'title', 'media_id', 'sort_order', 'is_required', 'is_visible'];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $fillable = [
        'section_id', 'type', 'title', 'body',
        'media_id', 'sort_order', 'is_required', 'is_visible',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(LmsActivitySection::class, 'section_id');
    }

    public function media()
    {
        return $this->belongsTo(LmsMediaLibrary::class, 'media_id');
    }

    public function isMediaBased(): bool
    {
        return in_array($this->type, ['VIDEO', 'AUDIO', 'IMAGE', 'PRESENTATION', 'FILE_PREVIEW']);
    }
}
