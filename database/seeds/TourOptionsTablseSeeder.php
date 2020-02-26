<?php

use Illuminate\Database\Seeder;
use App\Models\TourOption;
use App\Models\Tour;

class TourOptionsTablseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tour = Tour::query()->first();
        TourOption::firstOrCreate(
            ['bookeo_id' => '41574LM6TPW16DBCE8A4C6_YMAUXJRY', 'tour_id' => $tour->id],
            ['name' => 'Summit', 'is_active' => true]
        );
    }
}
