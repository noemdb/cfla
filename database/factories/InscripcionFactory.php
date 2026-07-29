<?php

namespace Database\Factories;

use App\Models\app\Academy\Escolaridad;
use App\Models\app\Academy\GrupoEstable;
use App\Models\app\Academy\Inscripcion;
use App\Models\app\Academy\Programacion;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Tinscripcion;
use App\Models\app\Learner\Estudiant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\app\Academy\Inscripcion>
 */
class InscripcionFactory extends Factory
{
    protected $model = Inscripcion::class;

    public function definition(): array
    {
        return [
            'estudiant_id'     => Estudiant::factory(),
            'seccion_id'       => Seccion::factory(),
            'tipo_id'          => Tinscripcion::factory(),
            'escolaridad_id'   => Escolaridad::factory(),
            'programacion_id'  => Programacion::factory(),
            'grupo_estable_id' => GrupoEstable::factory(),
            'observations'     => $this->faker->sentence(),
        ];
    }
}
