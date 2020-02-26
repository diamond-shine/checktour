<?php

use Illuminate\Database\Seeder;
use Shelter\Guard\Models\UserRole;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->guidePermissions();
        $this->tourConciergePermissions();
    }

    public function guidePermissions()
    {
        $guideRole = UserRole::where('name', '=', 'guide')->first();

        $this->syncPermissions($guideRole, [
            'bookings.edit',
            // 'bookings.list',
            'bookings.process',
            'bookings.update',
            'bookings.view',

            'forecasting.list',

            'rostered.list',

            'rosters.list',
            'rosters.process',
            'rosters.update',
            'rosters.view',

            'tours.view',

            'users.edit',

            'waiting-room.list',
        ]);
    }

    public function tourConciergePermissions()
    {
        $tourConciergeRole = UserRole::where('name', '=', 'tour_concierge')->first();

        $this->syncPermissions($tourConciergeRole, [
            'bookings.create',
            'bookings.edit',
            'bookings.list',
            'bookings.process',
            'bookings.update',
            'bookings.view',

            'excursions.create',
            'excursions.delete',
            'excursions.list',

            'forecasting.list',

            'import.bookings',

            'rostered.list',

            'rosters.list',
            'rosters.permit',
            'rosters.update',
            'rosters.view',

            'tours.list',
            'tours.view',

            'tours-users.edit',
            'tours-users.view',

            'tour-options.list',
            'tour-options.manage',

            'schedules.list',
            'schedules.view',
            'schedules.create',
            'schedules.delete',

            // 'users.list',
            'users.edit',

            'waiting-room.list',
        ]);
    }

    protected function syncPermissions(UserRole $userRole, $permissions = []): void
    {
        $userRole->permissions()->delete();

        $permissions = collect($permissions)
            ->flatten()
            ->map(function (string $permission) {
                return [
                    'key' => $permission,
                ];
            })
            ->toArray();

        $userRole->permissions()->createMany($permissions);
    }
}
