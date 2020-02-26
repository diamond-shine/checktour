<?php

namespace Shelter\Kernel\Packages\Traits;

use Shelter\Kernel\Packages\Package;

/**
 * @property Package $package
 */
trait RegisterCommandsTrait
{
    /**
     * @return void
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \ReflectionException
     */
    public function bootRegisterCommandsTrait(): void
    {
        $commands = $this->package->getManifest()->commands;

        if ($commands->isEmpty()) {
            return;
        }

        $this->commands($commands->items);
    }
}
