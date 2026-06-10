<?php

namespace App\Models\app\Academy\Lms;

use App\Models\User;
use App\Models\app\Academy\Activity;
use Illuminate\Database\Eloquent\Model;

class LmsActivityLink extends Model
{
    protected $table = 'lms_activity_links';

    public const TYPES = ['REFERENCE', 'VIDEO', 'TOOL', 'DOCUMENT', 'OTHER'];

    protected $fillable = [
        'activity_id', 'added_by', 'title', 'url',
        'link_type', 'description', 'sort_order', 'is_visible',
    ];

    protected $casts = ['is_visible' => 'boolean'];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
