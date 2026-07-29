<?php

namespace Database\Factories;

use App\Models\app\Academy\Tinscripcion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Academy\Tinscripcion>
 */
class TinscripcionFactory extends Factory
{
    protected $model = Tinscripcion::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}
