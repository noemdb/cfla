<?php

namespace Database\Factories;

use App\Models\app\Academy\Pensum;
use App\Models\app\Instrument\DiagSession;
use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiagSessionFactory extends Factory
{
    protected $model = DiagSession::class;

    public function definition(): array
    {
        return [
            'estudiant_id' => Estudiant::factory(),
            'pensum_id' => Pensum::factory(),
            'iniciado_at' => now(),
            'total_preguntas' => 1,
            'progreso' => 0,
            'activo' => true,
        ];
    }
}