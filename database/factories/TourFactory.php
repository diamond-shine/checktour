<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Tour;
use Faker\Generator as Faker;

$factory->define(Tour::class, function (Faker $faker) {
    return [
        'name' => 'Tour ' . $faker->city,
        'currency'=> 'USD',
        'is_active' => true,
        'bookeo_id' => $faker->uuid
    ];
});
