<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\app\Estudiante\Inscripcion;
use Faker\Generator as Faker;

$factory->define(Inscripcion::class, function (Faker $faker) {
    return [

        'observations' => $faker->text,
        'seccion_id' => function () { 
        	return 
        	DB::table('seccions')
				->select('seccions.*','inscripcions.id as inscripcions_id')
				->leftJoin('inscripcions', 'seccions.id', '=', 'inscripcions.seccion_id')
				// ->whereNull('inscripcions.seccion_id')
                ->inRandomOrder()
				->first()->id;
        },
        'estudiant_id' => function () { 
        	return 
        	DB::table('estudiants')
				->select('estudiants.*','inscripcions.id as inscripcions_id')
				->leftJoin('inscripcions', 'estudiants.id', '=', 'inscripcions.estudiant_id')
				->whereNull('inscripcions.estudiant_id')
                ->inRandomOrder()
				->first()->id;
		},
		'tipo_id' => function () { 
        	return 
        	DB::table('tinscripcions')
				->select('tinscripcions.id')
                ->inRandomOrder()
				->first()->id;
        }

       
        
        // 'user_id' => function () { return factory(App\User::class)->create()->id; }
    ];
});