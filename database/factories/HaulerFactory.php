<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Hauler::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company(),
        'city_id' => 1,
        'service_area_id' => 1,
        'svc_recycle' => 1,
        'svc_waste' => 1,
        'emails' => serialize([$faker->companyEmail()]),
        'archived' => 0
    ];
});
