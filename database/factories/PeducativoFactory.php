<?php

namespace Database\Factories;

use App\Models\app\Academy\Peducativo;
use App\Models\app\Entity\Pescolar;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeducativoFactory extends Factory
{
    protected $model = Peducativo::class;

    public function definition(): array
    {
        return [
            'pescolar_id' => Pescolar::factory(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'status_active' => 'true',
        ];
    }
}
