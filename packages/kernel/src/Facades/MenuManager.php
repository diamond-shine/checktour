<?php

namespace Shelter\Kernel\Facades;

use Illuminate\Support\Facades\Facade;

class MenuManager extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'shelter.menu-manager';
    }
}
