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

    public function isVisibleToStudents(): bool
    {
        if ($this->status !== 'PUBLISHED') {
            return false;
        }
        $now = now();
        if ($this->publish_at && $now->lt($this->publish_at)) {
            return false;
        }
        if ($this->unpublish_at && $now->gt($this->unpublish_at)) {
            return false;
        }
        return true;
    }

    public function scopeVisibleNow($query)
    {
        return $query->where('status', 'PUBLISHED')
            ->where(fn($q) => $q->whereNull('publish_at')->orWhere('publish_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('unpublish_at')->orWhere('unpublish_at', '>=', now()));
    }
}
