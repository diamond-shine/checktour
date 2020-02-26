<?php

namespace Shelter\Kernel\Http;

trait ControllerUsesTrait
{
    /**
     * @param string $method
     * @param array|null $parameters
     * @return array|string
     */
    public static function uses(string $method, array $parameters = null)
    {
        return \makeAction(static::class, $method, $parameters);
    }
}
