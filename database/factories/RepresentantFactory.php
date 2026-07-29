<?php

namespace Database\Factories;

use App\Models\app\Learner\Representant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Learner\Representant>
 */
class RepresentantFactory extends Factory
{
    protected $model = Representant::class;

    public function definition(): array
    {
        return [
            'ci_representant' => $this->faker->unique()->numerify('########'),
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'cellphone' => $this->faker->numerify('04##-#######'),
            'email' => $this->faker->unique()->safeEmail(),
            'status_active' => 'true',
            'status_adviders' => 'false',
            'status_blacklist' => 'false',
        ];
    }
}
