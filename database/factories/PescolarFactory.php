<?php

namespace Database\Factories;

use App\Models\app\Entity\Pescolar;
use App\Models\app\Entity\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class PescolarFactory extends Factory
{
    protected $model = Pescolar::class;

    public function definition(): array
    {
        return [
            'institucion_id' => Institucion::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'finicial' => $this->faker->date(),
            'ffinal' => $this->faker->date(),
        ];
    }
}
