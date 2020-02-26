<?php

namespace Ideil\LaravelFileManager\Traits;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

trait AutoUuidTrait
{
    /**
     * @return void
     */
    public static function bootAutoUuidTrait(): void
    {
        self::creating(function (Model $model) {
            if ($model->{$model->getKeyName()}) {
                return;
            }

            $uuid = Uuid::uuid1();

            $model->{$model->getKeyName()} = static::decodeUUID($uuid);
        });
    }

    /**
     * @param string $binaryUuid
     * @return string
     */
    public static function decodeUUID(string $binaryUuid): string
    {
        if (Uuid::isValid($binaryUuid)) {
            return $binaryUuid;
        }

        return Uuid::fromBytes($binaryUuid)->toString();
    }
}
