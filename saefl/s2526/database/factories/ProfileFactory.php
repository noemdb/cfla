<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\sys\Profile;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(App\Models\sys\Profile::class, function (Faker $faker) {

    $fec_ini = Carbon::now()->year.'-01-01';
    $fec_fin = Carbon::now()->year.'-12-31';
    return [
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
        // 'url_img' => 'img/profile_'.rand().'.png',
        'created_at' => $faker->dateTimeBetween($fec_ini,Carbon::now()),
        'user_id' => function () { 
        	return 
        	DB::table('users')
				->select('users.*','profiles.id as profiles_id')
				->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
				->whereNull('profiles.user_id')
                ->inRandomOrder()
				->first()->id;
        }
        // 'user_id' => function () { return factory(App\User::class)->create()->id; }
    ];
});

