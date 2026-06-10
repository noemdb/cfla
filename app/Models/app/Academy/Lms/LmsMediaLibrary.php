<?php

namespace App\Models\app\Academy\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class LmsMediaLibrary extends Model
{
    use SoftDeletes;

    protected $table = 'lms_media_library';

    protected $fillable = [
        'uploaded_by', 'disk', 'path', 'original_name',
        'mime_type', 'size_bytes', 'duration_secs',
        'thumbnail_path', 'provider', 'external_url', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'size_bytes' => 'integer',
    ];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function contents()
    {
        return $this->hasMany(LmsActivityContent::class, 'media_id');
    }

    public function resources()
    {
        return $this->hasMany(LmsActivityResource::class, 'media_id');
    }

    public function isLocal(): bool
    {
        return $this->provider === 'LOCAL';
    }

    public function getPublicUrlAttribute(): string
    {
        if (!$this->isLocal()) {
            return $this->external_url ?? '';
        }
        return Storage::disk($this->disk)->url($this->path);
    }

    public function getSizeForHumansAttribute(): string
    {
        $bytes = $this->size_bytes;
        if ($bytes < 1024) {
            return "{$bytes} B";
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return round($bytes / 1048576, 1) . ' MB';
    }
}
