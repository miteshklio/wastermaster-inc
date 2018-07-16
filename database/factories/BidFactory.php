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

$factory->define(App\Bid::class, function (Faker\Generator $faker) {
    return [
        'hauler_id' => 0,
        'hauler_email' => $faker->companyEmail(),
        'lead_id' => 0,
        'status' => 1,
        'archived' => 0,
        'msw_price' => $faker->numberBetween(60, 200),
        'rec_price' => $faker->numberBetween(40, 100),
        'rec_offset' => 0,
        'net_monthly' => $faker->numberBetween(100,300),
        'notes' => $faker->paragraph()
    ];
});
