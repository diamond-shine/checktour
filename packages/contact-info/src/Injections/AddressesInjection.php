<?php

namespace Shelter\ContactInfo\Injections;

use Shelter\Kernel\Database\AbstractModel;
use Shelter\Kernel\Injections\ModelRelations\InjectionInterface;

class AddressesInjection implements InjectionInterface
{
    /**
     * @return \Closure[]
     */
    public function relations(): array
    {
        return [
            'addresses' => function (AbstractModel $context, string $addressModelClass) {
                return $context->morphMany($addressModelClass, 'addressable');
            },

            'address' => function (AbstractModel $context, string $addressModelClass) {
                return $context->morphOne($addressModelClass, 'addressable')->default();
            },
        ];
    }
}
