<?php

use Illuminate\Database\Seeder;
use Shelter\Guard\Models\UserRole;

class UserRolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserRole::firstOrCreate(
            ['name' => 'guide'],
            ['title' => 'Guide']
        );

        UserRole::firstOrCreate(
            ['name' => 'tour_concierge'],
            ['title' => 'Tour concierge']
        );
    }
}
