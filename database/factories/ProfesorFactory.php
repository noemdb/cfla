<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\app\Academy\Profesor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfesorFactory extends Factory
{
    protected $model = Profesor::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'gender' => $this->faker->randomElement(['M', 'F']),
            'email' => $this->faker->unique()->safeEmail(),
            'status_active' => 'true',
        ];
    }
}