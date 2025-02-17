<?php

namespace Shelter\Kernel\Facades;

use Illuminate\Support\Facades\Facade;

class Kernel extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'shelter';
    }
}
