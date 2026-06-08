<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\sys\Rol;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Models\sys\Rol::class, function (Faker $faker) {
    $fec_ini = Carbon::now()->year.'-01-01';
    $fec_fin = Carbon::now()->year.'-12-31';
    $arr_area = ['SISTEMA','DIRECCION','AUTORIDAD','ADMINISTRACION','CONTROL ESTUDIO','PROFESORADO','ESTUDIANTIL','REPRESENTANTE'];
    $arr_rol = ['DIRECTOR','AUTORIDAD1','AUTORIDAD2','AUTORIDAD3','AUTORIDAD4','ADMINISTRADOR','SUPERVISOR','PROFESOR','ASISTENTE','USUARIO','ESTUDIANTE','REPRESENTANTE','INIVITADO'];
    $ffinal = $faker->dateTimeBetween($fec_ini, $fec_fin);
	$finicial = $faker->dateTimeBetween($fec_ini,$ffinal);
    return [
        // 'rol' => array_rand($arr_rol,1),
        'area' => $arr_area[array_rand($arr_area)],
        'rol' => $arr_rol[array_rand($arr_rol)],        
        'descripcion' => $faker->sentence(10),
        'finicial' => $finicial,
        'ffinal' => $ffinal,
        'created_at' => $faker->dateTimeBetween($fec_ini,Carbon::now()),
        'user_id' => function () {
        	return
        	DB::table('users')
				->select('users.*','rols.id as rols_id')
				->leftJoin('rols', 'users.id', '=', 'rols.user_id')
				// ->whereNull('rols.user_id')
                ->inRandomOrder()
				->first()->id;
        }
    ];
});
