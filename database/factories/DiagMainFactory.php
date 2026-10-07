<?php

namespace Database\Factories;

use App\Models\app\Instrument\DiagMain;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiagMainFactory extends Factory
{
    protected $model = DiagMain::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->sentence(3),
            'description' => $this->faker->sentence(),
            'active' => true,
        ];
    }
}