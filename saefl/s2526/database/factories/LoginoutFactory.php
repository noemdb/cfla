<?php

use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Models\sys\Loginout::class, function (Faker $faker) {
    $fec_ini = Carbon::now()->year.'-01-01';
    $fec_fin = Carbon::now()->year.'-12-31';
	$arr_action = ['Registered'=>'Registered','Attempting'=>'Attempting','LogAuthenticated'=>'LogAuthenticated','LogSuccessfulLogin'=>'LogSuccessfulLogin','LogFailedLogin'=>'LogFailedLogin','LogSuccessfulLogout'=>'LogSuccessfulLogout','LogLockout'=>'LogLockout','LogPasswordReset'=>'LogPasswordReset'];
    $arr_tipo = ['primary'=>'primary','success'=>'success','info'=>'info','warning'=>'warning','danger'=>'danger','default'=>'default'];
    $created_at = $faker->dateTimeBetween($fec_ini,$fec_fin);

    return [
        'action' => array_rand($arr_action,1),
        'tipo'=>array_rand($arr_tipo,1),
        'message' => $faker->sentence(3),
        'ip' => $faker->ipv4(),
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
