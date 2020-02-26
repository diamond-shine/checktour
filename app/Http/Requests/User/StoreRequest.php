<?php

namespace App\Http\Requests\User;

use Control\Packages\Users\Http\Requests\User\Mixins;
use Illuminate\Validation\Rule;
use libphonenumber\PhoneNumberFormat;
// use Shelter\ContactInfo\Models\Control\Telephone;
use Shelter\Guard\Models\User;
use Shelter\Kernel\Http\AbstractFormRequest;

/**
 * Class StoreRequest
 * @package Control\Packages\Users\Http\Requests\Users
 *
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string $email
 * @property string $login
 * @property array $telephone
 * @property string|null $password
 * @property boolean $is_active
 * @property boolean $is_banned
 * @property boolean $is_admin
 * @property array $roles
 * @property array $image
 */
class StoreRequest extends AbstractFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $result = [
            'first_name' => 'required|max:255',
            'last_name' => 'max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->whereNull('deleted_at'),
            ],
            'login' => [
                'required',
                'max:255',
                'alphaNum',
                Rule::unique('users', 'login')->whereNull('deleted_at'),
            ],
            // 'telephone.number' => [
            //     'bail',
            //     'nullable',
            //     'telephone_by_code:telephone.code',
            //     function (string $field, $value, \Closure $reject) {
            //         $exists = Telephone::telephonable(User::class)
            //             ->where(
            //                 'number',
            //                 phone($value, [], PhoneNumberFormat::E164)
            //             )
            //             ->exists();

            //         if ($exists) {
            //             $reject('Номер вже використовується');
            //         }
            //     },
            // ],
            'password' => 'required|confirmed:min:6',
            'roles.*' => [
                Rule::exists('user_roles', 'id'),
            ],
            'image.id' => [
                'nullable',
                Rule::exists('files', 'id'),
            ],
        ];

        if (auth()->user()->isAdmin()) {
            $result['is_active'] = 'required|boolean';
            $result['is_banned'] = 'required|boolean';
            $result['is_admin'] = 'required|boolean';
        }


        return $result;
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'telephone.number.telephone_by_code' => _('Невірний формат номеру телефона'),
        ];
    }
}
