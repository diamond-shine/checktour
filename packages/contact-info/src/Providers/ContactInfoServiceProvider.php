<?php

namespace Shelter\ContactInfo\Providers;

use Illuminate\Validation\Validator;
use libphonenumber\{
    NumberParseException,
    PhoneNumberUtil
};
use Shelter\ContactInfo\Injections\{
    AddressesInjection,
    TelephonesInjection
};
use Shelter\ContactInfo\Models\Control\{
    Address as ControlAddress,
    Telephone as ControlTelephone
};
use Shelter\ContactInfo\Models\Front\{
    Address as FrontAddress,
    Telephone as FrontTelephone
};
use Shelter\Kernel\Support\AbstractServiceProvider;

class ContactInfoServiceProvider extends AbstractServiceProvider
{
    /**
     * @return array
     */
    public static function modelsMorphMap(): array
    {
        return [
            'address' => [
                'control' => ControlAddress::class,
                'front' => FrontAddress::class,
            ],
            'telephone' => [
                'control' => ControlTelephone::class,
                'front' => FrontTelephone::class,
            ],
        ];
    }

    /**
     * @return void
     */
    public function bootGlobal(): void
    {
        $this->injectionsManager()->inject(
            new AddressesInjection,
            new TelephonesInjection
        );

        $this->app['validator']->extend(
            'telephone_by_code',
            function (string $attribute, $value, $parameters, $validator) {
                /** @var Validator $validator */

                $codeField = $parameters[0] ?? null;

                if (! $codeField) {
                    throw new \InvalidArgumentException('Code field name no passed');
                }

                $code = $codeField === 'AUTO' ? [] : data_get(
                    $validator->getData(),
                    $codeField
                );

                try {
                    PhoneNumberUtil::getInstance()->isValidNumber(
                        phone($value, $code)->getPhoneNumberInstance()
                    );
                } catch (NumberParseException $e) {
                    return false;
                }

                return true;
            }
        );
    }
}
