<?php

namespace App\Models\app\Timetable;

use App\Models\app\Academy\Lapso;
use App\Models\app\Academy\Pescolar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableCalendar extends Model
{
    use HasFactory;

    protected $table = 'timetable_calendars';

    protected $fillable = [
        'lapso_id', 'pescolar_id', 'name', 'status', 'period_minutes',
        'version', 'quality_score', 'preview_payload', 'active_job_id',
    ];

    protected $casts = [
        'period_minutes' => 'integer',
        'version' => 'integer',
        'quality_score' => 'decimal:2',
        'preview_payload' => 'array',
    ];

    const STATUS_DRAFT = 'draft';

    const STATUS_GENERATING = 'generating';

    const STATUS_ACTIVE = 'active';

    const STATUS_ARCHIVED = 'archived';

    public function lapso()
    {
        return $this->belongsTo(Lapso::class, 'lapso_id');
    }

    public function pescolar()
    {
        return $this->belongsTo(Pescolar::class, 'pescolar_id');
    }

    public function periods()
    {
        return $this->hasMany(TimetablePeriod::class, 'calendar_id');
    }

    public function lessons()
    {
        return $this->hasMany(TimetableLesson::class, 'calendar_id');
    }

    public function slots()
    {
        return $this->hasMany(TimetableSlot::class, 'calendar_id');
    }

    public function availabilities()
    {
        return $this->hasMany(TimetableTeacherAvailability::class, 'calendar_id');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function getIsEditableAttribute(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_ACTIVE]);
    }
}
