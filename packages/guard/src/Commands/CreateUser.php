<?php

namespace Shelter\Guard\Commands;

use Illuminate\Validation\Rules\Unique;
use Shelter\Guard\Models\User;
use Shelter\Kernel\Console\AbstractCommand;

class CreateUser extends AbstractCommand
{
    /**
     * @var string
     */
    protected $signature = 'shelter:guard:create:user';

    /**
     * @var string
     */
    protected $description = 'Create a permission';

    /**
     * @return void
     */
    public function handle(): void
    {
        $data = [
            'email' => $this->askAndValidate(
                'email',
                'E-mail',
                [
                    'email',
                    (new Unique('users', 'email'))
                        ->whereNull('deleted_at'),
                ]
            ),
            'password' => bcrypt(
                $this->askAndValidate(
                    'password',
                    'Password',
                    [
                        'min:6',
                    ],
                    [],
                    null,
                    'secret'
                )
            ),
            'first_name' => $this->askAndValidate(
                'first_name',
                'First name',
                [
                    'required',
                ]
            ),
            'last_name' => $this->askAndValidate(
                'last_name',
                'Last name',
                [
                    'required',
                ]
            ),
            'login' => $this->ask(
                'login',
                'Login',
                [
                    'login',
                    (new Unique('users', 'login'))
                        ->whereNull('deleted_at'),
                ]
            ),
            'is_admin' => $this->confirm('Create as admin?'),
            'is_active' => $this->confirm('Create as activated?'),
        ];

        User::unguard();

        /** @var User $user */
        if ($user = User::withTrashed()->whereEmail($data['email'])->first()) {
            if ($this->confirm('The user with this E-mail was deleted. Restore it?')) {
                $user->restore();

                $this->info('User was restored');
            }

        } else {
            User::create($data);

            $this->info('User created');
        }

        User::reguard();
    }
}
