<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Models\sys\Messege::class, function (Faker $faker) {

    $fec_ini = Carbon::now()->year.'-01-01';
    $fec_fin = Carbon::now()->year.'-12-31';  
    $arr_tipo = ['primary','secondary','success','info','warning','danger','default'];
    $arr_estado = ['sent','received','read'];
    $created_at = $faker->dateTimeBetween($fec_ini,'now');

    return [
        'mensaje' => $faker->sentence(10),
        'tipo' => $arr_tipo[array_rand($arr_tipo)],
        'estado' => $arr_estado[array_rand($arr_estado)],
        'created_at'=>$created_at,
        'updated_at'=>$created_at,
        'user_id' => function () { 
        	return 
        	DB::table('users')
				->inRandomOrder()
				->first()->id;
        },
        'destino_user_id' => function () { 
        	return 
        	DB::table('users')
				->inRandomOrder()
				->first()->id;
        }
    ];
});
