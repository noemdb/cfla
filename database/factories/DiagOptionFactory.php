<?php

namespace Database\Factories;

use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiagOptionFactory extends Factory
{
    protected $model = DiagOption::class;

    public function definition(): array
    {
        return [
            'question_id' => DiagQuestion::factory(),
            'opcion' => $this->faker->sentence(3),
            'valor' => 0,
            'orden' => 1,
        ];
    }
}