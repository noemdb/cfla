<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Academy\Grado>
 */
class GradoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pestudio_id' => \App\Models\app\Academy\Pestudio::factory(),
            'name' => $this->faker->word(),
            'code' => $this->faker->unique()->bothify('GR-####'),
            'code_sm' => $this->faker->unique()->bothify('GR##'),
            'description' => $this->faker->sentence(),
            'status_active' => 'true',
            'hour_social' => $this->faker->numberBetween(40, 200),
            'total_hour_social' => $this->faker->numberBetween(40, 200),
            'order' => $this->faker->numberBetween(1, 10),
        ];
    }
}
