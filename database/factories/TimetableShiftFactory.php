<?php

namespace Database\Factories;

use App\Models\app\Timetable\TimetableShift;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimetableShiftFactory extends Factory
{
    protected $model = TimetableShift::class;

    public function definition(): array
    {
        return [
            'code' => TimetableShift::CODE_MORNING,
            'name' => 'Mañana',
            'start_time' => '07:00:00',
            'end_time' => '12:15:00',
        ];
    }

    public function afternoon(): static
    {
        return $this->state(fn () => [
            'code' => TimetableShift::CODE_AFTERNOON,
            'name' => 'Tarde',
            'start_time' => '13:00:00',
            'end_time' => '18:15:00',
        ]);
    }
}