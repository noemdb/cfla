<?php

namespace App\Models\app\Timetable;

use App\Models\app\Academy\Profesor;
use App\Models\app\Academy\Seccion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableSlot extends Model
{
    use HasFactory;

    protected $table = 'timetable_slots';

    protected $fillable = [
        'calendar_id', 'lesson_id', 'period_id', 'profesor_id', 'seccion_id',
        'room_id', 'is_manual_override', 'locked',
    ];

    protected $casts = [
        'is_manual_override' => 'boolean',
        'locked' => 'boolean',
    ];

    public function calendar()
    {
        return $this->belongsTo(TimetableCalendar::class, 'calendar_id');
    }

    public function lesson()
    {
        return $this->belongsTo(TimetableLesson::class, 'lesson_id');
    }

    public function period()
    {
        return $this->belongsTo(TimetablePeriod::class, 'period_id');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function room()
    {
        return $this->belongsTo(TimetableRoom::class, 'room_id');
    }
}
