<?php

namespace Shelter\Kernel\Database;

use Exception;
use Ramsey\Uuid\Uuid;

/**
 * Trait ModelWithUUIDTrait
 * @package Shelter\Kernel\Database
 */
trait ModelWithUUIDTrait
{
    /**
     * @return void
     */
    public static function bootModelWithUUIDTrait(): void
    {
        self::creating(function (self $model) {
            $model->defineUUIDasID();
        });
    }

    /**
     * @return string
     * @throws Exception
     */
    public static function generateUUID(): string
    {
        $uuid = Uuid::uuid1();

        return static::decodeUUID($uuid);
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

    /**
     * @return void
     * @throws Exception
     */
    public function defineUUIDasID(): void
    {
        $keyName = $this->getKeyName();

        if ($this->{$keyName}) {
            return;
        }

        $this->{$keyName} = static::generateUUID();
    }
}
