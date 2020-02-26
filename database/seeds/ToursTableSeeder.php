<?php

use Illuminate\Database\Seeder;
use App\Models\Tour;

class ToursTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tour::firstOrCreate(
            ['bookeo_id' => '41574LM6TPW16DBCE8A4C6'],
            [
                'name' => 'Eiffel Climbing Tour',
                'is_active' => 1,
                'currency' => 'EUR'
            ]
        );

        Tour::firstOrCreate(
            ['bookeo_id' => '51574LM6TPW16DBCE8A4C6'],
            [
                'name' => 'Eiffel Elevator Tour',
                'is_active' => 1,
                'currency' => 'EUR'
            ]
        );
    }
}
