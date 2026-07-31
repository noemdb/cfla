<?php

namespace App\Models\app\Academy\Lms;

use App\Models\User;
use App\Models\app\Academy\Activity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LmsActivityPublication extends Model
{
    use HasFactory;

    protected $table = 'lms_activity_publications';

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
     * - 'hidden'  → no visible (status no activo, publish_at nulo o despublicada).
     * - 'preview' → visible solo la 1ª sección (now() < publish_at).
     * - 'full'    → visible completa (now() >= publish_at).
     */
    public function studentVisibility(): string
    {
        if (! in_array($this->status, ['PUBLISHED', 'SCHEDULED'], true)) {
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
     * Una SCHEDULED con publish_at futuro es visible como vista previa (1ª sección).
     */
    public function isVisibleToStudents(): bool
    {
        return $this->studentVisibility() !== 'hidden';
    }

    /**
     * Solo la primera sección es visible (now() < publish_at).
     */
    public function isPreviewToStudents(): bool
    {
        return $this->studentVisibility() === 'preview';
    }

    /**
     * La lección es visible completa (now() >= publish_at).
     */
    public function isFullVisibleToStudents(): bool
    {
        return $this->studentVisibility() === 'full';
    }

    public function scopeVisibleNow($query)
    {
        return $query->whereIn('status', ['PUBLISHED', 'SCHEDULED'])
            ->whereNotNull('publish_at')
            ->where(fn($q) => $q->whereNull('unpublish_at')->orWhere('unpublish_at', '>=', now()));
    }
}
