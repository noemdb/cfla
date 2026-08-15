<?php

namespace App\Models\app\Timetable;

use App\Models\app\Academy\Profesor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableTeacherAvailability extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'timetable_teacher_availability';

    protected $fillable = ['calendar_id', 'profesor_id', 'period_id', 'is_available'];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    public function calendar()
    {
        return $this->belongsTo(TimetableCalendar::class, 'calendar_id');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function period()
    {
        return $this->belongsTo(TimetablePeriod::class, 'period_id');
    }
}
