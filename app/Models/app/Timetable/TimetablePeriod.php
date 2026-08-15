<?php

namespace App\Models\app\Timetable;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetablePeriod extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'timetable_periods';

    protected $fillable = [
        'calendar_id', 'shift_id', 'day_of_week', 'order_in_day',
        'start_time', 'end_time', 'is_break',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'order_in_day' => 'integer',
        'is_break' => 'boolean',
    ];

    public function calendar()
    {
        return $this->belongsTo(TimetableCalendar::class, 'calendar_id');
    }

    public function shift()
    {
        return $this->belongsTo(TimetableShift::class, 'shift_id');
    }

    public function getDayLabelAttribute(): string
    {
        return [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'][$this->day_of_week] ?? '';
    }

    public function getPeriodLabelAttribute(): string
    {
        $time = $this->start_time
            ? ' · '.substr($this->start_time, 0, 5)
            : '';

        return "{$this->day_label} · Bloque {$this->order_in_day}{$time}";
    }
}
