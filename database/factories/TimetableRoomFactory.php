<?php

namespace Database\Factories;

use App\Models\app\Timetable\TimetableRoom;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimetableRoomFactory extends Factory
{
    protected $model = TimetableRoom::class;

    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('A##'),
            'name' => fake()->words(2, true),
            'capacity' => 30,
            'type' => 'aula',
            'status_active' => true,
        ];
    }

    public function laboratory(): static
    {
        return $this->state(fn () => [
            'type' => 'laboratorio',
            'code' => fake()->unique()->bothify('LAB-##'),
        ]);
    }
}