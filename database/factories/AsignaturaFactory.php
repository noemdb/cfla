<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Academy\Asignatura>
 */
class AsignaturaFactory extends Factory
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
            'code' => $this->faker->unique()->bothify('SUB-####'),
            'code_sm' => $this->faker->unique()->bothify('SUB##'),
            'name' => $this->faker->word(),
            'tescala' => \App\Models\app\Academy\Escala::factory(),
            'order' => $this->faker->numberBetween(1, 20),
            'hour_t_week' => $this->faker->numberBetween(1, 6),
            'hour_p_week' => $this->faker->numberBetween(0, 4),
            'unid_credit' => $this->faker->numberBetween(1, 6),
            'approved_credit_unir' => $this->faker->numberBetween(1, 6),
            'enable_academic_index' => $this->faker->boolean(),
            'enable_lost_regulation' => $this->faker->boolean(),
            'enable_official_doc' => $this->faker->boolean(),
            'enable_repairable' => $this->faker->boolean(),
            'enable_grupo_estable' => $this->faker->boolean(),
            'observations' => $this->faker->sentence(),
            'prelacions' => $this->faker->sentence(),
        ];
    }
}
