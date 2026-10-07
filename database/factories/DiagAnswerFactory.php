<?php

namespace Database\Factories;

use App\Models\app\Instrument\DiagAnswer;
use App\Models\app\Instrument\DiagOption;
use App\Models\app\Instrument\DiagQuestion;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiagAnswerFactory extends Factory
{
    protected $model = DiagAnswer::class;

    public function definition(): array
    {
        return [
            'estudiant_id' => Estudiant::factory(),
            'session_id' => DiagSession::factory(),
            'question_id' => DiagQuestion::factory(),
            'option_id' => DiagOption::factory(),
            'respuesta' => $this->faker->sentence(3),
            'completado_at' => now(),
        ];
    }
}