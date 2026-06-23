<?php

namespace App\Models\app\Academy\Lms;

use App\Models\User;
use App\Models\app\Academy\Activity;
use Illuminate\Database\Eloquent\Model;

class LmsActivityResource extends Model
{
    protected $table = 'lms_activity_resources';

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
