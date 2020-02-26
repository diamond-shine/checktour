<?php

namespace Shelter\Kernel\Support;

use InvalidArgumentException;
use ReflectionException;

class Enum
{
    /**
     * @var array
     */
    protected static $keysMap = [
        'key' => 'key',
        'value' => 'value',
        'description' => 'description',
    ];

    /**
     * @return array
     * @throws ReflectionException
     */
    public static function getConstList(): array
    {
        return (new \ReflectionClass(static::class))->getConstants();
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public static function keys(): array
    {
        return \array_keys(
            static::getConstList()
        );
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public static function values(): array
    {
        return \array_values(
            static::getConstList()
        );
    }

    /**
     * @param string $value
     * @return string|null
     */
    public static function description(string $value): ?string
    {
        return static::descriptions()[$value] ?? null;
    }

    /**
     * @return array
     */
    public static function descriptions(): array
    {
        return [];
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function toSelect(): array
    {
        return \array_reduce(
            static::toArray(),
            function (array $state, array $data) {
                $state[$data['key']] = $data['description'] ?: $data['value'];

                return $state;
            },
            []
        );
    }

    /**
     * @param array $keysMap
     * @param array $except
     * @return array
     * @throws ReflectionException
     */
    public static function toArray(array $keysMap = [], array $except = ['key']): array
    {
        $result = [];
        $descriptions = static::descriptions();

        $keyKey = ($keysMap['key'] ?? static::$keysMap['key']) ?? 'key';
        $valueKey = ($keysMap['value'] ?? static::$keysMap['value']) ?? 'value';
        $descriptionKey = ($keysMap['description'] ?? static::$keysMap['description']) ?? 'description';
        $except = \array_flip($except);

        foreach (static::getConstList() as $key => $value) {
            $item = [];

            if (! isset($except['key'])) {
                $item[$keyKey] = $key;
            }

            if (! isset($except['value'])) {
                $item[$valueKey] = $value;
            }

            if (! isset($except['description'])) {
                $item[$descriptionKey] = $descriptions[$value] ?? null;
            }

            $result[] = $item;
        }

        return $result;
    }

    /**
     * @param string $key
     * @return bool
     * @throws ReflectionException
     */
    public static function hasKey(string $key): bool
    {
        return \in_array(
            $key,
            static::keys(),
            true
        );
    }

    /**
     * @param string $value
     * @return bool
     * @throws ReflectionException
     */
    public static function hasValue(string $value): bool
    {
        return \in_array(
            $value,
            static::values(),
            true
        );
    }

    /**
     * @param string $value
     * @throws ReflectionException
     */
    public static function assertValue(string $value): void
    {
        if (! self::hasValue($value)) {
            throw new InvalidArgumentException("Invalid status [{$value}]");
        }
    }
}
