<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Academy\Pestudio>
 */
class PestudioFactory extends Factory
{
    protected $model = \App\Models\app\Academy\Pestudio::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'peducativo_id' => \App\Models\app\Academy\Peducativo::factory(),
            'code' => $this->faker->unique()->bothify('PEST-####'),
            'code_oficial' => $this->faker->bothify('OF-####'),
            'manager_id' => \App\Models\User::factory(),
            'name' => $this->faker->word(),
            'order' => $this->faker->numberBetween(1, 10),
            'description' => $this->faker->sentence(),
            'description_aux' => $this->faker->sentence(),
            'mention' => $this->faker->word(),
            'status_build_promotion' => $this->faker->boolean(),
            'title' => $this->faker->word(),
            'scale' => \App\Models\app\Academy\Escala::factory(),
            'profile' => $this->faker->word(),
            'color' => $this->faker->hexColor(),
            'show_hr' => $this->faker->boolean(),
            'status_a_cualitative' => $this->faker->boolean(),
            'activities_avr' => $this->faker->randomFloat(2, 0, 100),
            'status_active' => 'true',
            'show_quantitative_indicators' => $this->faker->boolean(),
            'status_inscripcion_active' => $this->faker->boolean(),
            'status_carga_notas' => $this->faker->boolean(),
            'status_baremo' => $this->faker->boolean(),
            'status_socials' => $this->faker->boolean(),
            'planning_module' => $this->faker->boolean(),
            'paper' => $this->faker->word(),
            'remision_resumen_final' => $this->faker->boolean(),
            'fecha_prosecucion' => $this->faker->date(),
            'description_trainig_component' => $this->faker->sentence(),
            'fecha_promocion' => $this->faker->date(),
            'fecha_descriptivo' => $this->faker->date(),
            'fecha_certificacion' => $this->faker->date(),
            'fecha_informe_final' => $this->faker->date(),
        ];
    }
}
