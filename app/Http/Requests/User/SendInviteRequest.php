<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;
use Shelter\Kernel\Http\AbstractFormRequest;

/**
 * Class InviteRequest
 * @package Control\Packages\Users\Http\Requests\User
 *
 * @property string $email
 * @property string $login
 * @property array|null $roles
 * @property array|null $permissions
 * @property bool|null $as_admin
 */
class SendInviteRequest extends AbstractFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                (new Unique('users', 'email'))->whereNull('deleted_at'),
            ],
            'login' => [
                'required',
                'max:255',
                'alphaNum',
                (new Unique('users', 'login'))->whereNull('deleted_at'),
            ],
            'roles.*' => [
                new Exists('user_roles', 'id'),
            ],
            'permissions.*' => [
                'string',
            ],
            'as_admin' => [
                function (string $field, $value, \Closure $reject) {
                    if (! $value
                        && ! app('shelter.auth')->user()->can('users.invite.as-admin')
                    ) {
                        $reject(
                            _('Only another administrator can invite an administrator')
                        );
                    }
                },
            ],
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'email.required' => _('Enter E-mail'),
            'email.email' => _('Wrong E-mail format'),
            'email.unique' => _('E-mail already exists'),

            'login.required' => _('Enter login'),
            'login.max' => _('The number of characters should not exceed :max'),
            'login.alphaNum' => _('Login contains invalid characters'),
            'login.unique' => _('Login is already in use'),

            'roles.*.exists' => _('The role is incorrect'),

            'roles.*.string' => _('Invalid rights format'),
        ];
    }
}
