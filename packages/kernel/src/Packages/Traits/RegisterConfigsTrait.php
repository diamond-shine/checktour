<?php

namespace Shelter\Kernel\Packages\Traits;

use RuntimeException;
use Shelter\Kernel\Packages\Package;

/**
 * Trait RegisterRoutesTrait
 * @package Shelter\Kernel\Traits\Providers
 *
 * @property Package $package
 */
trait RegisterConfigsTrait
{
    /**
     * @return void
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \ReflectionException
     */
    public function bootRegisterConfigsTrait(): void
    {
        $items = $this->package->getManifest()->configs->items;

        if (! $items) {
            return;
        }

        $configs = [];

        foreach ($items as $filename => $path) {
            $source = $this->package->path("resources/configs/{$filename}.php");

            if (! \is_file($source)) {
                throw new RuntimeException("Config file [{$filename}] not found in package [{$this->package->fullName()}]");
            }

            $this->mergeConfigFrom(
                $source,
                str_replace('/', '.', $path)
            );

            $configs[$source] = config_path(
                str_replace('.', '/', $path) . '.php'
            );
        }

        $this->publishes($configs, 'configs');
    }
}
