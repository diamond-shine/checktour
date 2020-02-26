<?php

namespace Shelter\Kernel\Packages\Traits;

use Shelter\Kernel\Packages\Package;

/**
 * @property Package $package
 */
trait RegisterHotRelationsTrait
{
    /**
     * @return void
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \ReflectionException
     */
    public function bootRegisterHotRelationsTrait(): void
    {
        $hotRelations = $this->package->getManifest()->hotRelations;

        if ($hotRelations->isEmpty()) {
            return;
        }

        foreach ($hotRelations->items as $class) {
            $this->app['shelter.injections']->inject(new $class);
        }
    }
}
