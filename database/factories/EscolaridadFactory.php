<?php

namespace Database\Factories;

use App\Models\app\Academy\Escolaridad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Academy\Escolaridad>
 */
class EscolaridadFactory extends Factory
{
    protected $model = Escolaridad::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'code' => $this->faker->unique()->bothify('ES-####'),
        ];
    }
}
