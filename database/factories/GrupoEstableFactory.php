<?php

namespace Database\Factories;

use App\Models\app\Academy\GrupoEstable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Academy\GrupoEstable>
 */
class GrupoEstableFactory extends Factory
{
    protected $model = GrupoEstable::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'code' => $this->faker->unique()->bothify('GR-####'),
            'code_sm' => $this->faker->unique()->bothify('GR##'),
            'description' => $this->faker->sentence(),
            'hour_t_week' => $this->faker->numberBetween(2, 10),
            'hour_p_week' => $this->faker->numberBetween(2, 10),
            'size_max' => $this->faker->numberBetween(20, 40),
            'status_belongs_ins' => 'true',
            'status_active' => 'true',
        ];
    }
}
