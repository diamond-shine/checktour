<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Excursion;
use App\Models\Schedule;
use App\Models\Tour;

use Faker\Generator as Faker;

$factory->define(Schedule::class, function (Faker $faker) {
    $tour = Tour::all()->random();
    $excurions = Excursion::getForecasted($tour->excursions, 7);
    $excursionForDay = reset($excurions);
    $excursion = reset($excursionForDay);
    $user = $tour->users->random();

    dump($excurions, $excursion);
    return [
        'tour_id' => $tour->id,
        'excursion_id' => $excursion['id'],
        'user_id' => $user->id,
        'assigned_at' => $excursion['date_time']
    ];
});
