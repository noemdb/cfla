<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Academy\Seccion>
 */
class SeccionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'grado_id' => \App\Models\app\Academy\Grado::factory(),
            'name' => $this->faker->unique()->bothify('SEC-####'),
            'description' => $this->faker->sentence(),
            'amount_student' => $this->faker->numberBetween(15, 45),
            'observation' => $this->faker->sentence(),
            'status_active' => 'true',
            'comment_final' => $this->faker->sentence(),
            'status_inscription_affects' => $this->faker->boolean(),
        ];
    }
}
