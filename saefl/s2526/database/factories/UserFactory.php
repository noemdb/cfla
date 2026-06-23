<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    static $password;
    $fakerES = \Faker\Factory::create('es_ES');

    $is_active = ['enable'=>'enable','disable'=>'disable'];
    $arr_ip = ['101.114.49.91','118.56.196.42','121.139.125.16','136.250.208.187','144.231.161.65','145.4.187.128','169.203.87.121','176.138.118.137','198.99.10.139'];

    return [
        'username' => $fakerES->unique()->userName,
        'password' => $password ?: $password = bcrypt('secret'),
        'email' => $fakerES->unique()->safeEmail,
        'is_active' => array_rand($is_active,1),
        'last_login_at' => $faker->dateTimeBetween('2019-01-01',Carbon::now()),
        'last_loginout_at' => $faker->dateTimeBetween('2019-01-01',Carbon::now()),
        'last_login_ip' => $arr_ip[array_rand($arr_ip,1)],
        'created_at' => $faker->dateTimeBetween('2019-01-01',Carbon::now()),
        'remember_token' => str_random(10),
    ];
});
