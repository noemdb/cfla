<?php

namespace App\Models\app\Academy\Lms;

use App\Models\app\Academy\Activity;
use Illuminate\Database\Eloquent\Model;

class LmsActivitySection extends Model
{
    protected $table = 'lms_activity_sections';

    protected $fillable = ['activity_id', 'title', 'description', 'sort_order', 'is_visible'];

    protected $casts = ['is_visible' => 'boolean'];

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
