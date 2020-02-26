<?php

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\Tour;

class TicketsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tour = Tour::first();

        Ticket::firstOrCreate(
            ['bookeo_type' => 'Cadults', 'tour_id' => $tour->id],
            [
                'name' => 'Adult ticket',
                'price' => 30,
                'is_active' => true
            ]
        );

        Ticket::firstOrCreate(
            ['bookeo_type' => 'Cchildren', 'tour_id' => $tour->id],
            [
                'name' => 'Child ticket',
                'price' => 20,
                'is_active' => true
            ]
        );

        Ticket::firstOrCreate(
            ['bookeo_type' => 'Cinfants', 'tour_id' => $tour->id],
            [
                'name' => 'Baby ticket',
                'price' => 15,
                'is_active' => true
            ]
        );
    }
}
