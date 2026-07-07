<?php

namespace Database\Factories;

use App\Models\app\Academy\Escala;
use Illuminate\Database\Eloquent\Factories\Factory;

class EscalaFactory extends Factory
{
    protected $model = Escala::class;

    public function definition(): array
    {
        return [
            'tipo' => 'NUMÉRICA',
            'name' => $this->faker->word(),
            'minimo' => '1',
            'maximo' => '20',
            'aprobacion' => '10',
        ];
    }
}
