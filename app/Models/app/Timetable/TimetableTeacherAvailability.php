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

    protected $fillable = [
        'calendar_id', 'profesor_id', 'shift_id', 'day_of_week',
        'order_in_day', 'start_time', 'end_time', 'is_available',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'order_in_day' => 'integer',
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

    public function shift()
    {
        return $this->belongsTo(TimetableShift::class, 'shift_id');
    }

    /** Clave unívoca (turno-día-bloque) para el solver: "shift-day-order". */
    public function getBlockKeyAttribute(): string
    {
        return $this->shift_id.'-'.$this->day_of_week.'-'.$this->order_in_day;
    }
}
