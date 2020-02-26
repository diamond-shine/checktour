<?php

namespace App\Tools\Vue;

class Vuex
{
    /**
     * @var array
     */
    protected static $stores = [];

    /**
     * @param string $store
     * @param array $state
     */
    public static function put(string $store, array $state): void
    {
        static::$stores[$store] = $state;
    }

    /**
     * @return string
     */
    public static function toJson(): string
    {
        return app('JavaScript')->constructJavaScript([
            '__preload' => [
                'stores' => static::$stores
            ],
        ]);
    }
}
