<?php

use Faker\Generator as Faker;

$factory->define(App\Models\sys\Setting::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName,
        'value' => $faker->lastName,
        'user_id' => function () { 
        	return 
        	DB::table('users')
				->select('users.*')
				->leftJoin('settings', 'users.id', '=', 'settings.user_id')
				->whereNull('settings.user_id')
                ->inRandomOrder()
				->first()->id;
        }
    ];
});
