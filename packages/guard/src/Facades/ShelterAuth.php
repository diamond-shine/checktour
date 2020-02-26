<?php

namespace Shelter\Guard\Facades;

use Illuminate\Support\Facades\Facade;

class ShelterAuth extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'shelter.auth';
    }
}
