<?php

namespace Shelter\Kernel\Packages\Traits;

use Shelter\Kernel\Packages\Package;

/**
 * Trait RegisterSettingsTrait
 * @package Shelter\Kernel\Packages\Traits
 *
 * @property Package $package
 */
trait RegisterSettingsTrait
{
    /**
     * @return void
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \ReflectionException
     */
    public function bootRegisterSettingsTrait(): void
    {
        if (! $this->package->getManifest()->settings->status) {
            return;
        }

        $source = $this->package->path('resources/settings.php');

        if (! \file_exists($source)) {
            throw new \RuntimeException("Settings file not found in package [{$this->package->fullName()}]");
        }

        $this->package->registerSettings(require $source);
    }
}
