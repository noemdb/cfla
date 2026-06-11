<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Academy\Lapso>
 */
class LapsoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->bothify('LAP-####'),
            'code_sm' => $this->faker->unique()->bothify('L##'),
            'name' => $this->faker->word(),
            'finicial' => $this->faker->date(),
            'ffinal' => $this->faker->date(),
            'academic_start_date' => $this->faker->date(),
            'date_cutnote' => $this->faker->date(),
            'date_start_census' => $this->faker->date(),
            'time_start_census' => $this->faker->time(),
            'date_end_census' => $this->faker->date(),
            'time_end_census' => $this->faker->time(),
            'date_preclosing' => $this->faker->date(),
            'time_preclosing' => $this->faker->time(),
            'status_last' => 'true',
        ];
    }
}
