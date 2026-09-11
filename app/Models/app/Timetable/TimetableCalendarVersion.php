<?php

namespace App\Models\app\Timetable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableCalendarVersion extends Model
{
    use HasFactory;

    protected $table = 'timetable_calendar_versions';

    protected $fillable = [
        'calendar_id', 'version', 'status', 'published_by', 'published_at',
        'quality_score', 'summary_json',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'quality_score' => 'decimal:2',
        'summary_json' => 'array',
    ];

    public function calendar()
    {
        return $this->belongsTo(TimetableCalendar::class, 'calendar_id');
    }

    public function changes()
    {
        return $this->hasMany(TimetableChangeLog::class, 'version_id');
    }
}
