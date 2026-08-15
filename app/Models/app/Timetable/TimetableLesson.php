<?php

namespace App\Models\app\Timetable;

use App\Models\app\Academy\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimetableLesson extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'timetable_lessons';

    protected $fillable = [
        'calendar_id', 'pevaluacion_id', 'shift_id',
        'weekly_blocks_t', 'weekly_blocks_p', 'room_type_required',
        'priority', 'locked',
    ];

    protected $casts = [
        'weekly_blocks_t' => 'integer',
        'weekly_blocks_p' => 'integer',
        'priority' => 'integer',
        'locked' => 'boolean',
    ];

    public function calendar()
    {
        return $this->belongsTo(TimetableCalendar::class, 'calendar_id');
    }

    public function pevaluacion()
    {
        return $this->belongsTo(Pevaluacion::class, 'pevaluacion_id');
    }

    public function shift()
    {
        return $this->belongsTo(TimetableShift::class, 'shift_id');
    }

    public function slots()
    {
        return $this->hasMany(TimetableSlot::class, 'lesson_id');
    }

    public function getBlocksNeededAttribute(): int
    {
        return $this->weekly_blocks_t + $this->weekly_blocks_p;
    }

    public function getFullNameAttribute(): string
    {
        $pev = $this->pevaluacion;

        return $pev
            ? "{$pev->pensum?->asignatura?->name} · {$pev->seccion?->name}"
            : "#{$this->id}";
    }
}
