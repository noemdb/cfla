<?php

namespace App\Models\app\Timetable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableChangeLog extends Model
{
    use HasFactory;

    protected $table = 'timetable_change_logs';

    protected $fillable = [
        'calendar_id', 'version_id', 'user_id', 'action', 'lesson_id',
        'before_json', 'after_json', 'metadata_json',
    ];

    protected $casts = [
        'before_json' => 'array',
        'after_json' => 'array',
        'metadata_json' => 'array',
    ];

    public function calendar()
    {
        return $this->belongsTo(TimetableCalendar::class, 'calendar_id');
    }

    public function version()
    {
        return $this->belongsTo(TimetableCalendarVersion::class, 'version_id');
    }
}
