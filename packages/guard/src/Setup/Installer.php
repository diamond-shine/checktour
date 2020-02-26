<?php

namespace Shelter\Guard\Setup;

use Illuminate\Console\Command;
use Shelter\Guard\Models\User;
use Shelter\Guard\Models\UserRole;

class Installer
{
    /**
     * @param \Closure $failure
     */
    public function check(\Closure $failure): void
    {
        $adminGroupSlug = config('shelter.guard.admin_group');
        $adminGroup = UserRole::whereSlug($adminGroupSlug)->first();

        if (! $adminGroup) {
            $failure('Admin group not found');
        }

        if ($adminGroup && ! $adminGroup->users()->exists()) {
            $failure('Admin user not found');
        }

        $this->checkAuthConfig($failure);
    }

    /**
     * @param \Closure $failure
     */
    protected function checkAuthConfig(\Closure $failure): void
    {
        if (config('auth.guards.control.provider') !== 'control') {
            $failure('Invalid configuration [auth.guards.control]');
        }

        if (config('auth.providers.control.model') !== User::class
            && ! is_subclass_of(
                config('auth.providers.control.model'),
                User::class
            )
        ) {
            $failure('Invalid configuration [auth.providers.control]');
        }

        if (config('auth.passwords.control.provider') !== 'control'
            || config('auth.passwords.control.table') !== 'user_password_resets'
        ) {
            $failure('Invalid configuration [auth.passwords.control]');
        }
    }

    /**
     * @param \Closure $log
     * @param Command $command
     */
    public function install(\Closure $log, Command $command): void
    {
        $adminGroup = UserRole::firstOrCreate([
            'slug' => config('shelter.guard.admin_group'),
        ], [
            'title' => 'Administrations',
            'is_active' => true,
        ]);

        if ($adminGroup->wasRecentlyCreated) {
            $log('Created admin group');
        }

        if ($adminGroup->users()->exists()) {
            return;
        }

        $log('Creating admin user');

        $data = [
            'email' => $command->ask('Email'),
            'password' => bcrypt(
                $command->secret('Password')
            ),
            'first_name' => 'Admin',
            'last_name' => '',
            'login' => 'admin',
        ];

        User::unguard();

        $adminGroup->users()->create($data);

        User::reguard();
    }
}
