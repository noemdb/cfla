<?php

namespace Database\Factories;

use App\Models\app\Learner\Estudiant;
use App\Models\app\Learner\Representant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Learner\Estudiant>
 */
class EstudiantFactory extends Factory
{
    protected $model = Estudiant::class;

    public function definition(): array
    {
        return [
            'ci_estudiant' => $this->faker->unique()->numerify('########'),
            'name' => $this->faker->firstName(),
            'lastname' => $this->faker->lastName(),
            'gender' => $this->faker->randomElement(['Masculino', 'Femenino']),
            'date_birth' => $this->faker->date(),
            'city_birth' => $this->faker->city(),
            'state_birth' => $this->faker->state(),
            'country_birth' => 'VENEZUELA',
            'representant_id' => Representant::factory(),
            'status_active' => 'true',
            'cellphone' => $this->faker->numerify('04##-#######'),
            'email' => $this->faker->unique()->safeEmail(),
            'type_ci_id' => 1,
        ];
    }
}
