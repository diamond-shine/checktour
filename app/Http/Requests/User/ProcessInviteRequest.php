<?php

namespace Control\Packages\Users\Http\Requests\User;

use Illuminate\Validation\Rules\Unique;
use Shelter\Kernel\Http\AbstractFormRequest;

/**
 * Class ProcessInviteRequest
 * @package Control\Packages\Users\Http\Requests\User
 *
 * @property string $first_name
 * @property string $last_name
 * @property string $password
 */
class ProcessInviteRequest extends AbstractFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:255',
            'last_name' => 'max:255',
            'password' => 'required|confirmed:min:6',
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'first_name.required' => _('Введіть ім’я'),
            'first_name.max' => _('Name length should not exceed :max characters'),

            'last_name.max' => _('Last name length should not exceed :max characters'),

            'password.required' => _('Enter password'),
            'password.confirmed' => _('Passwords mismatch'),
            'password.min' => _('Password length should exceed :min'),
        ];
    }
}
