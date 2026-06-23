<?php

namespace App\Models\app\Academy\Lms;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsHtmlEmbed extends Model
{
    protected $table = 'lms_html_embeds';

    protected $fillable = [
        'activity_id',
        'section_id',
        'added_by',
        'title',
        'html_content',
        'render_condition',
        'sort_order',
        'is_visible',
    ];

    const RENDER_CONDITIONS = ['ALWAYS'];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(\App\Models\app\Academy\Activity::class, 'activity_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(LmsActivitySection::class, 'section_id');
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
