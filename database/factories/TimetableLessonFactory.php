<?php

namespace Database\Factories;

use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetableShift;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimetableLessonFactory extends Factory
{
    protected $model = TimetableLesson::class;

    public function definition(): array
    {
        return [
            'calendar_id' => TimetableCalendar::factory(),
            'pevaluacion_id' => Pevaluacion::factory(),
            'shift_id' => TimetableShift::factory(),
            'weekly_blocks_t' => 3,
            'weekly_blocks_p' => 0,
            'room_type_required' => null,
            'priority' => 0,
            'locked' => false,
        ];
    }
}