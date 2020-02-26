<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use libphonenumber\PhoneNumberFormat;
// use Shelter\ContactInfo\Models\Control\Telephone;
use Shelter\Guard\Models\User;

/**
 * Class UpdateRequest
 * @package Control\Packages\Users\Http\Requests\Users
 */
class UpdateRequest extends StoreRequest
{
    public function authorize(): bool
    {
        $currentUser = auth()->user();
        $user = $this->route('user');

        return $currentUser->isAdmin() || $user->id == $currentUser->id;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return \array_merge(
            parent::rules(),
            [
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email')
                        ->whereNull('deleted_at')
                        ->ignoreModel($user),
                ],
                'login' => 'nullable',
                // 'telephone.number' => [
                //     'bail',
                //     'nullable',
                //     'telephone_by_code:telephone.code',
                //     function (string $field, $value, \Closure $reject) {
                //         $exists = Telephone::telephonable(
                //             User::class,
                //             $this->route('userId')->id,
                //             true
                //         )->where(
                //             'number',
                //             phone($value, [], PhoneNumberFormat::E164)
                //         )->exists();

                //         if ($exists) {
                //             $reject('Номер вже використовується');
                //         }
                //     },
                // ],
                'password' => 'nullable|confirmed:min:6',
            ]
        );
    }
}
