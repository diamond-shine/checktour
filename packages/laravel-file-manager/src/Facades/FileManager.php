<?php

namespace Ideil\LaravelFileManager\Facades;

use Illuminate\Support\Facades\Facade;

class FileManager extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'file-manager';
    }
}
