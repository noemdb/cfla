<?php

namespace App\Models\app\Academy\Lms;

use Illuminate\Database\Eloquent\Model;

class LmsActivityContent extends Model
{
    protected $table = 'lms_activity_contents';

    public const TYPES = ['TEXT', 'VIDEO', 'AUDIO', 'IMAGE', 'PRESENTATION', 'HTML', 'EMBED', 'FILE_PREVIEW'];

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
