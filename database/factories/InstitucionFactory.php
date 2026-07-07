<?php

namespace Database\Factories;

use App\Models\app\Entity\Institucion;
use Illuminate\Database\Eloquent\Factories\Factory;

class InstitucionFactory extends Factory
{
    protected $model = Institucion::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'legalname' => $this->faker->company(),
            'rif_institution' => $this->faker->unique()->bothify('J-########-#'),
            'email_institution' => $this->faker->companyEmail(),
            'status_dont_allow_registration_if_insolvency' => 'false',
        ];
    }
}
