<?php

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

$factory->define(App\Model\Car::class, function (Faker $faker) {
    static $number = 1;
    return [
        'car_number' => $number++,
        'description' => $faker->text,
        'available' => TRUE,
        'status' => TRUE,
        'car_type_id' => $faker->numberBetween($min = 1, $max = 6),
    ];
});
