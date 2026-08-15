<?php

namespace App\Models\app\Academy\Lms;

use App\Models\app\Academy\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LmsActivityResource extends Model implements \App\Contracts\Auditable
{
    protected $table = 'lms_activity_resources';

    /**
     * Allowlist para la bitácora (Spec BINNACLE-001, ADR-005).
     */
    public function auditableAttributes(): array
    {
        return ['id', 'activity_id', 'section_id', 'media_id', 'uploaded_by', 'display_name', 'sort_order', 'is_visible'];
    }

    public function maskedAuditFields(): array
    {
        return [];
    }

    protected $fillable = [
        'activity_id', 'section_id', 'media_id', 'uploaded_by',
        'display_name', 'description', 'sort_order', 'is_visible',
    ];

    protected $casts = ['is_visible' => 'boolean'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function section()
    {
        return $this->belongsTo(LmsActivitySection::class, 'section_id');
    }

    public function media()
    {
        return $this->belongsTo(LmsMediaLibrary::class, 'media_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function incrementDownload(): void
    {
        $this->increment('download_count');
    }
}
