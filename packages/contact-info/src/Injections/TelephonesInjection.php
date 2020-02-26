<?php

namespace Shelter\ContactInfo\Injections;

use Shelter\Kernel\Database\AbstractModel;
use Shelter\Kernel\Injections\ModelRelations\InjectionInterface;

class TelephonesInjection implements InjectionInterface
{
    /**
     * @return \Closure[]
     */
    public function relations(): array
    {
        return [
            'telephones' => function (AbstractModel $context, string $telephoneModel) {
                return $context->morphMany($telephoneModel, 'telephonable');
            },

            'telephone' => function (AbstractModel $context, string $telephoneModel) {
                return $context->morphOne($telephoneModel, 'telephonable');
            },

            'defaultTelephone' => function (AbstractModel $context, string $telephoneModel) {
                return $context->morphOne($telephoneModel, 'telephonable')->default();
            },
        ];
    }
}
