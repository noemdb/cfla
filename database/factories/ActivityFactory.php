<?php

namespace Database\Factories;

use App\Models\app\Academy\Activity;
use App\Models\app\Academy\Pevaluacion;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'pevaluacion_id' => Pevaluacion::factory(),
            'finicial'       => now(),
            'ffinal'         => now()->addDays(7),
            'topic'          => $this->faker->sentence(4),
            'status'         => false,
        ];
    }
}
