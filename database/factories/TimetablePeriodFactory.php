<?php

namespace Database\Factories;

use App\Models\app\Timetable\TimetableCalendar;
use App\Models\app\Timetable\TimetablePeriod;
use App\Models\app\Timetable\TimetableShift;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimetablePeriodFactory extends Factory
{
    protected $model = TimetablePeriod::class;

    public function definition(): array
    {
        return [
            'calendar_id' => TimetableCalendar::factory(),
            'shift_id' => TimetableShift::factory(),
            'day_of_week' => 1,
            'order_in_day' => 1,
            'start_time' => '07:00:00',
            'end_time' => '07:45:00',
            'is_break' => false,
        ];
    }
}