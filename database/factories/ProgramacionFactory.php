<?php

namespace Database\Factories;

use App\Models\app\Academy\Programacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Academy\Programacion>
 */
class ProgramacionFactory extends Factory
{
    protected $model = Programacion::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
        ];
    }
}
