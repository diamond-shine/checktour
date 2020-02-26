<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Excursion;
use App\Models\Tour;

use Faker\Generator as Faker;

$factory->define(Excursion::class, function (Faker $faker) {

    $tours = Tour::all();

    $minutes = (rand(1, 10) % 2) ? '00' : '30';
    $hours = rand(8, 22);

    // dd($hours . ':' . $minutes);
    return [
        'tour_id' => $tours->random()->id,
        'day' => rand(1, 7),
        'time' => $hours . ':' . $minutes
    ];
});
