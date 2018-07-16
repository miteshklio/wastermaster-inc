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

$factory->define(App\Lead::class, function (Faker\Generator $faker) {
    return [
        'hauler_id' => 0,
        'msw_qty' => 0,
        'msw_yards' => 0,
        'msw_per_week' => 0,
        'rec_qty' => 0,
        'rec_yards' => 0,
        'rec_per_week' => 0,
        'msw2_qty' => 0,
        'msw2_yards' => 0,
        'msw2_per_week' => 0,
        'rec2_qty' => 0,
        'rec2_yards' => 0,
        'rec2_per_week' => 0,
        'status' => 1,
        'archived' => 0,
        'bid_count' => 0
    ];
});
