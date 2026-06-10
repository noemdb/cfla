<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\app\Academy\Pevaluacion;
use App\Models\app\Academy\Pensum;
use App\Models\app\Academy\Seccion;
use App\Models\app\Academy\Lapso;
use Illuminate\Database\Eloquent\Factories\Factory;

class PevaluacionFactory extends Factory
{
    protected $model = Pevaluacion::class;

    public function definition(): array
    {
        return [
            'pensum_id'  => Pensum::factory(),
            'profesor_id' => User::factory(),
            'lapso_id'    => Lapso::factory(),
            'seccion_id'  => Seccion::factory(),
            'objetivo'    => $this->faker->sentence(5),
        ];
    }
}
