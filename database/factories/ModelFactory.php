<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;

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

$factory->define(User::class, function (Faker $faker) {
    return [
        'first_name' => $faker->name,
        'last_name' => $faker->name,
        'username' => strtolower(str_replace(".","", trim(str_replace(" ", "_",$faker->name))."_".trim(str_replace(" ", "_",$faker->name)))).".".$faker->unique()->numberBetween($min = 1, $max = 99),
        'phone' => $faker->unique()->e164PhoneNumber,
        'password' => $faker->unique()->md5,
        'api_token' => $faker->unique()->md5,
        'type' => $faker->randomElement(['user', 'vip', 'vendor', 'doctor']),
        'chanel' => $faker->randomElement(['app', 'web', 'agent']),
        'verification_code' => $faker->unique()->numberBetween($min = 1000, $max = 9999),
    ];
});
