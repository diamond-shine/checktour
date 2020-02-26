<?php

namespace Shelter\Kernel\Injections\ModelRelations;

use Closure;

interface InjectionInterface
{
    /**
     * @return Closure[]
     */
    public function relations(): array;
}
