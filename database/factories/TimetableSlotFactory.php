<?php

namespace Database\Factories;

use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetableLesson;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableSlot;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimetableSlotFactory extends Factory
{
    protected $model = TimetableSlot::class;

    public function definition(): array
    {
        return [
            'calendar_id' => TimetableCalendar::factory(),
            'lesson_id' => TimetableLesson::factory(),
            'period_id' => TimetablePeriod::factory(),
            'profesor_id' => 1,
            'seccion_id' => 1,
            'room_id' => null,
            'is_manual_override' => false,
            'locked' => false,
        ];
    }
}