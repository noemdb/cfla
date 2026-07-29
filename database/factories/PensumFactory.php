<?php

namespace Database\Factories;

use App\Models\app\Academy\Pensum;
use Illuminate\Database\Eloquent\Factories\Factory;

class PensumFactory extends Factory
{
    protected $model = Pensum::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pestudio_id' => \App\Models\app\Academy\Pestudio::factory(),
            'grado_id' => \App\Models\app\Academy\Grado::factory(),
            'asignatura_id' => \App\Models\app\Academy\Asignatura::factory(),
            'status_component' => $this->faker->boolean(),
            'status_active' => true,
            'status_active_diagnostic' => $this->faker->boolean(),
            'observations' => $this->faker->sentence(),
        ];
    }
}
