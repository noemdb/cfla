<?php

namespace Database\Factories;

use App\Models\app\Academy\Lapso;
use App\Models\app\Timetable\TimetableCalendar;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimetableCalendarFactory extends Factory
{
    protected $model = TimetableCalendar::class;

    public function definition(): array
    {
        return [
            'lapso_id' => Lapso::factory(),
            'name' => 'Horario ' . fake()->year(),
            'status' => TimetableCalendar::STATUS_DRAFT,
            'period_minutes' => 45,
            'version' => 0,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => TimetableCalendar::STATUS_ACTIVE]);
    }
}