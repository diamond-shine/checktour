<?php

namespace Shelter\Guard\Commands;

use Illuminate\Validation\Rules\Unique;
use Shelter\Guard\Models\UserRole;
use Shelter\Kernel\Console\AbstractCommand;

class CreateRole extends AbstractCommand
{
    /**
     * @var string
     */
    protected $signature = 'shelter:guard:create:role';

    /**
     * @var string
     */
    protected $description = 'Create a role';

    /**
     * @return void
     */
    public function handle(): void
    {
        $role = UserRole::create([
            'title' => $this->ask('Title'),
            'name' => $this->askAndValidate(
                'name',
                'Name',
                [
                    new Unique('user_roles', 'name'),
                ]
            ),
        ]);

        $this->info("Role `{$role->name}` created");
    }
}
