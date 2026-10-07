<?php

namespace Database\Factories;

use App\Models\app\Academy\Pensum;
use App\Models\app\Instrument\DiagMain;
use App\Models\app\Instrument\DiagQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiagQuestionFactory extends Factory
{
    protected $model = DiagQuestion::class;

    public function definition(): array
    {
        return [
            'pensum_id' => Pensum::factory(),
            'diag_main_id' => DiagMain::factory(),
            'pregunta' => $this->faker->sentence(8),
            'tipo_pregunta' => 'multiple',
            'orden' => 1,
            'difficulty' => 'medium',
            'weighing' => 1,
            'activo' => 1,
        ];
    }
}