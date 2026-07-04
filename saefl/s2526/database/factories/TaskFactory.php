<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Models\sys\Task::class, function (Faker $faker) {

	$arr_tipo = ['primary'=>'primary','success'=>'success','info'=>'info','warning'=>'warning','danger'=>'danger','default'=>'default'];
	$arr_estado = ['INICIADA'=>'INICIADA','REPROGRAMADA'=>'REPROGRAMADA','FINALIZADA'=>'FINALIZADA'];
    $created_at = $faker->dateTimeBetween('2019-01-01','2019-12-31');
    $ffinal = $faker->dateTimeBetween('2019-01-01', Carbon::now());
    $finicial = $faker->dateTimeBetween('2019-01-01',$ffinal);

    return [
        'codigo' => str_random(10),
        'descripcion' => $faker->sentence(10),
        'tipo' => array_rand($arr_tipo,1),
        'evento' => str_random(10),
        'estado' => array_rand($arr_estado,1),
        'finicial' => $finicial,
        'ffinal' => $ffinal,
        'created_at'=>$created_at,
        'updated_at'=>$created_at,

        'user_id' => function () {
        	return
        	DB::table('users')
				->inRandomOrder()
				->first()->id;
        }
    ];
});
